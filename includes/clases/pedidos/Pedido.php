<?php
namespace BistroFDI\clases\pedidos;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\clases\aplicacion;
class Pedido {

    // Estados
    public const ESTADO_NUEVO = 'Nuevo';
    public const ESTADO_RECIBIDO = 'Recibido';
    public const ESTADO_PREPARACION = 'En preparacion';
    public const ESTADO_COCINANDO = 'Cocinando';
    public const ESTADO_LISTO_COCINA ='Listo cocina';
    public const ESTADO_TERMINADO = 'Terminado';
    public const ESTADO_ENTREGADO = 'Entregado';
    public const ESTADO_CANCELADO = 'Cancelado';

    // Tipos
    public const TIPO_DOMICILIO = 'a domicilio';
    public const TIPO_LOCAL = 'local';

    private $num_pedido; 
    private $fecha_hora;
    private $cliente;
    private $cocinero;
    private $camarero;
    private $total;
    private $estado;
    private $tipo;
    private $productos; // Array de productos: [['nombre' => 'Agua', 'cantidad' => 2, 'preparado' => 0], ...] de la tabla Pedido_Producto.

    private function __construct($num_pedido, $fecha_hora, $cliente, $total, $estado, $tipo, $productos = [], $cocinero="NULL", $camarero="NULL") {
        $this->num_pedido = $num_pedido;
        $this->fecha_hora = $fecha_hora;
        $this->cliente = $cliente;
        $this->total = $total;
        $this->estado = $estado;
        $this->tipo = $tipo;
        $this->productos = $productos;
        $this->cocinero = $cocinero;
        $this->camarero = $camarero;
    }

    //crea un pedido
    public static function crea($fecha_hora, $num_pedido, $tipo, $total, $estado, $cliente, $productosArr, $cocinero, $camarero) {
        $pedido = new Pedido($num_pedido, $fecha_hora, $cliente, $total, $estado, $tipo, $productosArr, $cocinero, $camarero);
        return self::inserta($pedido);
    }

    //insertar un pedido
    private static function inserta($pedido) {
        $conn = Aplicacion::getInstance()->getConexionBd();


        $camarero = ($pedido->camarero && $pedido->camarero !== "NULL") ? "'" . $conn->real_escape_string($pedido->camarero) . "'" : "NULL";                
        $cocinero = ($pedido->cocinero && $pedido->cocinero !== "NULL")? "'" . $conn->real_escape_string($pedido->cocinero) . "'" : "NULL";
        
        $query = sprintf("INSERT INTO Pedido (fecha_hora, num_pedido, tipo, total, estado, cliente, camarero, cocinero) 
            VALUES ('%s', %d, '%s', %f, '%s', '%s',%s, %s)",
            $conn->real_escape_string($pedido->fecha_hora),
            $pedido->num_pedido,
            $conn->real_escape_string($pedido->tipo),
            $pedido->total,
            $conn->real_escape_string($pedido->estado),
            $conn->real_escape_string($pedido->cliente),
            $camarero,
            $cocinero       
        );

        if (!$conn->query($query)) return false;

        // Insertar productos en Pedido_Producto
        foreach ($pedido->productos as $p) {
            //si producto no cocinable-> preparado = 1
            //si producto cocinable-> preparado = 0 
            $queryProd = sprintf("INSERT INTO Pedido_Producto (nombre, fecha_hora, num_pedido, cantidad, preparado) 
                SELECT '%s', '%s', %d, %d, NOT cocinable 
                FROM Producto 
                WHERE nombre = '%s'",
                $conn->real_escape_string($p['nombre']),
                $conn->real_escape_string($pedido->fecha_hora),
                $pedido->num_pedido,
                $p['cantidad'],
                $conn->real_escape_string($p['nombre'])
            );
            $conn->query($queryProd);
        }
        return true;
    }

    //buscar pedido
    public static function buscarPedido($num_pedido, $fecha_hora) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Pedido WHERE num_pedido=%d AND fecha_hora='%s'", 
            $num_pedido, $conn->real_escape_string($fecha_hora));
        
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            
            // Cargar productos y sus cantidades
            $productos = [];
            $queryProd = sprintf("SELECT * FROM Pedido_Producto WHERE num_pedido=%d AND fecha_hora='%s'", 
                $num_pedido, $conn->real_escape_string($fecha_hora));
            $rsProd = $conn->query($queryProd);
            while($p = $rsProd->fetch_assoc()) {
                $productos[] = $p;
            }
            
            $pedido = new Pedido($f['num_pedido'], $f['fecha_hora'], $f['cliente'], $f['total'], $f['estado'], $f['tipo'], $productos);
            $rs->free();
            return $pedido;
        }
        return false;
    }

    //pedidos en proceso de un usuario concreto
    public static function pedidosProcesoUsuario($nombreUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = "SELECT num_pedido, fecha_hora, estado 
                FROM Pedido 
                WHERE cliente = ? 
                AND estado NOT IN ('" . self::ESTADO_ENTREGADO . "', '" . self::ESTADO_CANCELADO . "')
                ORDER BY fecha_hora DESC";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("s", $nombreUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $lista = [];
        while ($fila = $result->fetch_assoc()) {
            $lista[] = $fila;
        }
        
        $stmt->close();
        return $lista;
    }


    //último pedido en estado nuevo
    public static function pedidosNuevosUsuario($nombreUsuario, $tipo){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = sprintf("SELECT num_pedido, fecha_hora, estado 
                FROM Pedido 
                WHERE cliente = '%s' AND estado = '%s'
                ORDER BY fecha_hora DESC LIMIT 1", $nombreUsuario, self::ESTADO_NUEVO);

        $resultado = $conn->query($query);

        //si existe un pedido nuevo
        if($resultado && $resultado->num_rows > 0){
            $pedidoActual = $resultado->fetch_assoc();
            $fecha_hora = $pedidoActual['fecha_hora'];
            $num_pedido = $pedidoActual['num_pedido'];
        }
        else{ //si no, creamos uno

            //saber el ultimo pedido
            $queryUltimoPedido = sprintf("SELECT MAX(num_pedido) as ultimo FROM Pedido"); //devuelve el útlimo num usado
            $resQueyUltimoPedido = $conn->query($queryUltimoPedido);
            $filaMax = $resQueyUltimoPedido->fetch_assoc();
            $num_pedido = ($filaMax['ultimo'] !== null) ? $filaMax['ultimo'] + 1 : 1;


            $fecha_hora = date('Y-m-d H:i:s');
            self::crea($fecha_hora,$num_pedido, $tipo, 0.0, self::ESTADO_NUEVO , $nombreUsuario, [], NULL, NULL);
        }

        return [$fecha_hora, $num_pedido];
    }


    public static function insertarPedidoProducto($fecha_hora, $num_pedido, $nombreProducto, $cantidad){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = sprintf("SELECT cantidad FROM Pedido_Producto 
                        WHERE nombre='%s' AND fecha_hora='%s' AND num_pedido=%d",
                        $nombreProducto, $fecha_hora, $num_pedido);

        $res = $conn->query($query);
        //si ya existe el producto, aumentamos la cantidad
        if($res && $res->num_rows > 0){
            $query2 = sprintf("UPDATE Pedido_Producto SET cantidad = cantidad + %d
                               WHERE nombre='%s' AND fecha_hora='%s' AND num_pedido=%d",
                               $cantidad, $nombreProducto, $fecha_hora, $num_pedido);
            $conn->query($query2);
        }
        else{
            $query3 = sprintf("INSERT INTO Pedido_Producto (nombre, cantidad, fecha_hora, num_pedido, preparado) 
                                VALUES ('%s', %d, '%s', %d, 0)",
                                $nombreProducto, $cantidad, $fecha_hora, $num_pedido);
            $conn->query($query3);
        } 
    }

    public static function actualizarTotalPedido($fecha_hora, $num_pedido){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = sprintf("SELECT SUM(p.precio * pp.cantidad) as total_calculado
                        FROM Pedido_Producto pp
                                JOIN Producto p ON pp.nombre = p.nombre
                        WHERE pp.fecha_hora = '%s' AND pp.num_pedido = %d",
                        $fecha_hora, $num_pedido);

        $result = $conn->query($query);
        if ($result) {
            $fila = $result->fetch_assoc();
            $nuevoTotal = $fila['total_calculado'] ?? 0.0;

            // Actualizamos el campo 'total' en la tabla Pedido
            $queryUpdate = sprintf("UPDATE Pedido SET total = %f 
                                    WHERE fecha_hora = '%s' AND num_pedido = %d",
                                    $nuevoTotal, $fecha_hora, $num_pedido);
            $conn->query($queryUpdate);
        }
    }



    //historial de pedidos de un usuario concreto
    public static function historialPedidoUsuario($nombreUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = "SELECT p.num_pedido as id, p.fecha_hora, p.total as precio_total, p.estado, p.tipo,
                         GROUP_CONCAT(CONCAT(pp.cantidad, ' x ', pp.nombre) SEPARATOR '<br>') as productos
                FROM Pedido p
                JOIN Pedido_Producto pp ON p.num_pedido = pp.num_pedido AND p.fecha_hora = pp.fecha_hora
                WHERE p.cliente = ? AND p.estado = '" . self::ESTADO_ENTREGADO . "'
                GROUP BY p.num_pedido, p.fecha_hora
                ORDER BY p.fecha_hora DESC";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("s", $nombreUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $lista = [];
        while ($fila = $result->fetch_assoc()) {
            $lista[] = $fila;
        }

        $stmt->close();
        return $lista;
    }

    //actualizar estado
    public static function actualizaEstado($num_pedido, $fecha_hora, $nuevoEstado) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Pedido SET estado='%s' WHERE num_pedido=%d AND fecha_hora='%s'",
            $conn->real_escape_string($nuevoEstado), $num_pedido, $conn->real_escape_string($fecha_hora));
        return $conn->query($query);
    }

    //actualizar tipo
    public static function actualizaTipo($num_pedido, $fecha_hora, $tipo) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Pedido SET tipo='%s' WHERE num_pedido=%d AND fecha_hora='%s'",
            $conn->real_escape_string($tipo), $num_pedido, $conn->real_escape_string($fecha_hora));
        return $conn->query($query);
    }

    //borrar pedido
    public static function borra($num_pedido, $fecha_hora) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        // Primero borrar productos por integridad (claves foráneas)
        $query1 = sprintf("DELETE FROM Pedido_Producto WHERE num_pedido=%d AND fecha_hora='%s'", 
            $num_pedido, $conn->real_escape_string($fecha_hora));
        $conn->query($query1);

        // Luego borrar el pedido
        $query2 = sprintf("DELETE FROM Pedido WHERE num_pedido=%d AND fecha_hora='%s'", 
            $num_pedido, $conn->real_escape_string($fecha_hora));
        return $conn->query($query2);
    }

    // (completar pedido camarero): devuelve pedidos en LISTO_COCINA, 
    // mostrando productos no cocinables (si los hay) y el tipo de pedido.
    public static function pedidosParaCompletar() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        // Usamos LEFT JOIN para no descartar pedidos que no tengan productos no cocinables
        $query = "SELECT p.num_pedido as id, p.fecha_hora, p.tipo,
                        GROUP_CONCAT(CONCAT(pp.cantidad, ' x ', prod.nombre) SEPARATOR '<br>') as productos
                FROM Pedido p
                LEFT JOIN Pedido_Producto pp ON p.num_pedido = pp.num_pedido AND p.fecha_hora = pp.fecha_hora
                LEFT JOIN Producto prod ON pp.nombre = prod.nombre AND prod.cocinable = 0
                WHERE p.estado = '" . self::ESTADO_LISTO_COCINA . "' 
                GROUP BY p.num_pedido, p.fecha_hora, p.tipo
                ORDER BY p.fecha_hora ASC";
        
        $rs = $conn->query($query);
        $lista = [];
        
        if ($rs) {
            while ($f = $rs->fetch_assoc()) { 
                // Si el pedido no tiene productos no cocinables, productos será NULL.
                if (is_null($f['productos'])) {
                    $f['productos'] = "Solo productos de cocina (Ya listos)";
                }
                $lista[] = $f; 
            }
            $rs->free();
        }
        
        return $lista;
    }

    //(cobrar pedido camarero): pedidos pendientes de cobro, incluyendo el total y la lista de productos.
    public static function getPedidosParaCobrar() {
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = "SELECT p.num_pedido as id, p.fecha_hora, p.total,
                        GROUP_CONCAT(CONCAT(pp.cantidad, ' x ', prod.nombre) SEPARATOR '<br>') as productos
                FROM Pedido p
                JOIN Pedido_Producto pp ON p.num_pedido = pp.num_pedido AND p.fecha_hora = pp.fecha_hora
                JOIN Producto prod ON pp.nombre = prod.nombre
                WHERE p.estado = '" . self::ESTADO_RECIBIDO . "'
                GROUP BY p.num_pedido, p.fecha_hora";
        
        $rs = $conn->query($query);
        $lista = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) { 
                $lista[] = $f; 
            }
            $rs->free();
        }
        return $lista;
    }

    //(entregar pedido camarero): id pedido, cliente, productos
    public static function getPedidosParaEntregarLocal() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $query = "SELECT p.num_pedido as id, p.fecha_hora, p.cliente,
                        GROUP_CONCAT(CONCAT(pp.cantidad, ' x ', prod.nombre) SEPARATOR '<br>') as productos
                FROM Pedido p
                JOIN Pedido_Producto pp ON p.num_pedido = pp.num_pedido AND p.fecha_hora = pp.fecha_hora
                JOIN Producto prod ON pp.nombre = prod.nombre
                WHERE p.estado = '" . self::ESTADO_TERMINADO . "' 
                GROUP BY p.num_pedido, p.fecha_hora";
        
        $rs = $conn->query($query);
        $lista = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) { 
                $lista[] = $f; 
            }
            $rs->free();
        }
        return $lista;
    }

    //(cocinero): productos para cocinar de los pedidos
    public static function getPedidosCocinero() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $sql = "SELECT num_pedido, fecha_hora, estado, cocinero FROM Pedido 
                WHERE estado IN ('".self::ESTADO_PREPARACION."', '".self::ESTADO_COCINANDO."')";
        $rs = $conn->query($sql);

        $pedidosCocinero = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                $sqlProd = sprintf("SELECT pp.nombre, pp.cantidad, pp.preparado FROM Pedido_Producto pp
                    JOIN Producto prod ON pp.nombre = prod.nombre
                    WHERE pp.num_pedido = %d AND pp.fecha_hora = '%s' AND prod.cocinable = 1", 
                    $f['num_pedido'], $conn->real_escape_string($f['fecha_hora']));
                
                $rsP = $conn->query($sqlProd);
                $prods = [];
                while ($p = $rsP->fetch_assoc()) { $prods[] = $p; }

                if (!empty($prods)) {
                    $pedidosCocinero[] = ['num_pedido' => $f['num_pedido'], 'fecha_hora' => $f['fecha_hora'], 'estado' => $f['estado'], 'productos' => $prods, 'cocinero' => $f['cocinero']];
                }
            }
        }
        return $pedidosCocinero;
    }

    public static function getTodosLosPedidos(){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $sql = "SELECT * FROM Pedido";
        $rs = $conn->query($sql);

        $pedidos = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                $sqlProd = sprintf("SELECT Pedido_Producto.nombre, Pedido_Producto.cantidad, Pedido_Producto.preparado FROM Pedido_Producto
                    JOIN Producto ON Pedido_Producto.nombre = Producto.nombre
                    WHERE Pedido_Producto.num_pedido = %d AND Pedido_Producto.fecha_hora = '%s' AND Producto.cocinable = 1", 
                    $f['num_pedido'], $conn->real_escape_string($f['fecha_hora']));
                
                $rsP = $conn->query($sqlProd);
                $prods = [];
                while ($p = $rsP->fetch_assoc()) { $prods[] = $p; }

                if (!empty($prods)) {
                    $pedidos[] = ['num_pedido' => $f['num_pedido'], 'fecha_hora' => $f['fecha_hora'], 
                                    'tipo' => $f['tipo'], 'total' => $f['total'], 'estado' => $f['estado'], 
                                    'cliente' => $f['cliente'], 'cocinero' => $f['cocinero'], 'productos' => $prods];
                }
            }
        }
        return $pedidos;
    }


    public static function marcarProductoPedido($nombre_producto, $num_pedido, $fecha_hora){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $sql = sprintf(
            "UPDATE Pedido_Producto 
            SET preparado = 1 
            WHERE nombre='%s' AND num_pedido=%d AND fecha_hora='%s'",
            $conn->real_escape_string($nombre_producto),
            $num_pedido,
            $conn->real_escape_string($fecha_hora)
        );
        $conn->query($sql);

    }
    
    public static function aceptarPedido($num_pedido, $fecha_hora, $cocinero)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();

        $sql = sprintf(
            "UPDATE Pedido 
            SET estado='%s', cocinero='%s'
            WHERE num_pedido=%d AND fecha_hora='%s' AND estado='%s'",
            self::ESTADO_COCINANDO,
            $conn->real_escape_string($cocinero),
            $num_pedido,
            $conn->real_escape_string($fecha_hora),
            self::ESTADO_PREPARACION // Solo se puede aceptar si estaba en preparación
        );

        return $conn->query($sql);
    }

    public static function getEstadoProductosPedido(int $num_pedido, string $fecha_hora): array
    {
        $conn = Aplicacion::getInstance()->getConexionBd();

        $sql = sprintf(
            "SELECT 
                pp.nombre,
                prod.cocinable,
                pp.preparado
            FROM Pedido_Producto pp
            JOIN Producto prod ON pp.nombre = prod.nombre
            WHERE pp.num_pedido = %d
            AND pp.fecha_hora = '%s'
            ORDER BY pp.nombre ASC",
            $num_pedido,
            $conn->real_escape_string($fecha_hora)
        );

        $rs = $conn->query($sql);
        $productos = [];

        if ($rs) {
            while ($f = $rs->fetch_assoc()) {

                $estado = '';

                if ((int)$f['cocinable'] === 1) {
                    // Es cocinable → su estado depende de 'preparado'
                    $estado = ((int)$f['preparado'] === 1) ? 'Preparado' : 'Pendiente';
                } else {
                    // No cocinable → no aplica preparado
                    $estado = 'No cocinable';
                }

                $productos[] = [
                    'nombre' => $f['nombre'],
                    'estado' => $estado
                ];
            }
            $rs->free();
        }

        return $productos;
    }


    public static function getPedidosGerente(): array
    {
        $conn = Aplicacion::getInstance()->getConexionBd();

        // Estados que el Gerente debe visualizar
        $pendientes = [
            self::ESTADO_RECIBIDO,
            self::ESTADO_PREPARACION,
            self::ESTADO_COCINANDO,
            self::ESTADO_LISTO_COCINA,
            self::ESTADO_TERMINADO
        ];
        $in = "'" . implode("','", $pendientes) . "'";

        $sql = "
            SELECT 
                p.num_pedido,
                p.fecha_hora,
                p.estado,
                p.cocinero,
                u.avatar AS avatar_cocinero
            FROM Pedido p
            LEFT JOIN Usuarios u
                ON p.cocinero = u.nombreUsuario
            WHERE p.estado IN ($in)
            ORDER BY p.fecha_hora DESC
        ";

        $rs = $conn->query($sql);
        $out = [];

        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                $out[] = [
                    'num_pedido'      => (int)$f['num_pedido'],
                    'fecha_hora'      => $f['fecha_hora'],
                    'estado'          => $f['estado'],
                    'cocinero'        => $f['cocinero'],        
                    'avatar_cocinero' => $f['avatar_cocinero'], 
                ];
            }
            $rs->free();
        }

        return $out;
    }


    public static function getCarritoUsuario($nombreUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = sprintf("SELECT Pedido.num_pedido, Pedido.fecha_hora, cantidad, Producto.nombre, total, precio FROM Pedido
	                            JOIN Pedido_Producto ON Pedido.fecha_hora=Pedido_Producto.fecha_hora AND Pedido.num_pedido=Pedido_Producto.num_pedido
                                JOIN Producto ON Pedido_Producto.nombre = Producto.nombre
                            WHERE cliente='%s' AND estado='Nuevo'", $nombreUsuario);

        $res = $conn->query($query);

        $productos = [];
        if ($res) {
            while ($fila = $res->fetch_assoc()) {
                $productos[] = $fila;
            }
            $res->free();
        }
        return $productos;     
    }

    //Terminar cocinar pedido (cocinero): Cocinando->ListoCocina
    public static function terminarCocinarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_LISTO_COCINA);
    }

    //Completar pedido (camarero): ListoCocina->Terminado
    public static function completarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_TERMINADO);
    }

    //Cobrar pedido (camarero): Recibido-> En preparación
    public static function cobrarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_PREPARACION);
    }
    
    //Entregar pedido (camarero): Terminado-> Entregado
    public static function entregarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_ENTREGADO);
    }

    //Confirmar pedido (cliente): Nuevo-> Recibido
    public static function confirmarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_RECIBIDO);
    }


    // Getters
    public function getNumPedido() { return $this->num_pedido; }
    public function getFechaHora() { return $this->fecha_hora; }
    public function getEstado() { return $this->estado; }
    public function getTotal() { return $this->total; }

}
