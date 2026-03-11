<?php
namespace BistroFDI\pedidos;
use BistroFDI\Aplicacion;

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
    public const TIPO_DOMICILIO = 'A domicilio';
    public const TIPO_LOCAL = 'En local';

    private $num_pedido; 
    private $fecha_hora;
    private $cliente;
    private $total;
    private $estado;
    private $tipo;
    private $productos; // Array de productos: [['nombre' => 'Agua', 'cantidad' => 2, 'preparado' => 0], ...] de la tabla Pedido_Producto.

    private function __construct($num_pedido, $fecha_hora, $cliente, $total, $estado, $tipo, $productos = []) {
        $this->num_pedido = $num_pedido;
        $this->fecha_hora = $fecha_hora;
        $this->cliente = $cliente;
        $this->total = $total;
        $this->estado = $estado;
        $this->tipo = $tipo;
        $this->productos = $productos;
    }

    //crea un pedido
    public static function crea($fecha_hora, $num_pedido, $tipo, $total, $estado, $cliente, $productosArr) {
        $pedido = new Pedido($num_pedido, $fecha_hora, $cliente, $total, $estado, $tipo, $productosArr);
        return self::inserta($pedido);
    }

    //insertar un pedido
    private static function inserta($pedido) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $query = sprintf("INSERT INTO Pedido (fecha_hora, num_pedido, tipo, total, estado, cliente) 
            VALUES ('%s', %d, '%s', %f, '%s', '%s')",
            $conn->real_escape_string($pedido->fecha_hora),
            $pedido->num_pedido,
            $conn->real_escape_string($pedido->tipo),
            $pedido->total,
            $conn->real_escape_string($pedido->estado),
            $conn->real_escape_string($pedido->cliente)
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
    public function pedidosProcesoUsuario($nombreUsuario){
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

    //historial de pedidos de un usuario concreto
    public function historialPedidoUsuario($nombreUsuario){
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
    private static function actualizaEstado($num_pedido, $fecha_hora, $nuevoEstado) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Pedido SET estado='%s' WHERE num_pedido=%d AND fecha_hora='%s'",
            $conn->real_escape_string($nuevoEstado), $num_pedido, $conn->real_escape_string($fecha_hora));
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

    //(completar pedido camarero): id pedido, fecha, tipo y productos no cocinables de los pedidos
    public static function pedidosParaCompletar() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT p.num_pedido as id, p.fecha_hora, p.tipo,
                        GROUP_CONCAT(CONCAT(pp.cantidad, ' x ', prod.nombre) SEPARATOR '<br>') as productos
                FROM Pedido p
                JOIN Pedido_Producto pp ON p.num_pedido = pp.num_pedido AND p.fecha_hora = pp.fecha_hora
                JOIN Producto prod ON pp.nombre = prod.nombre
                WHERE p.estado = '" . self::ESTADO_LISTO_COCINA . "' AND prod.cocinable = 0
                GROUP BY p.num_pedido, p.fecha_hora";
        
        $rs = $conn->query($query);
        $lista = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) { $lista[] = $f; }
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

    //(entregar pedido camarero): id pedido(tipo = en local), cliente, productos
    public static function getPedidosParaEntregarLocal() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $query = "SELECT p.num_pedido as id, p.fecha_hora, p.cliente,
                        GROUP_CONCAT(CONCAT(pp.cantidad, ' x ', prod.nombre) SEPARATOR '<br>') as productos
                FROM Pedido p
                JOIN Pedido_Producto pp ON p.num_pedido = pp.num_pedido AND p.fecha_hora = pp.fecha_hora
                JOIN Producto prod ON pp.nombre = prod.nombre
                WHERE p.estado = '" . self::ESTADO_TERMINADO . "' 
                AND p.tipo = '" . self::TIPO_LOCAL . "'
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
        $sql = "SELECT num_pedido, fecha_hora FROM Pedido 
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
                    $pedidosCocinero[] = ['num_pedido' => $f['num_pedido'], 'fecha_hora' => $f['fecha_hora'], 'productos' => $prods];
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

        $sql1 = sprintf(
            "UPDATE Pedido_Producto 
            SET preparado = 1 
            WHERE nombre='%s' AND num_pedido=%d AND fecha_hora='%s'",
            $conn->real_escape_string($nombre_producto),
            $num_pedido,
            $conn->real_escape_string($fecha_hora)
        );
        $conn->query($sql1);

        $sql2 = sprintf(
            "UPDATE Pedido 
            SET estado='%s' 
            WHERE num_pedido=%d AND fecha_hora='%s'",
            Pedido::ESTADO_COCINANDO,
            $num_pedido,
            $conn->real_escape_string($fecha_hora)
        );
        $conn->query($sql2);

    }

    
    public static function aceptarPedido($num_pedido, $fecha_hora, $cocinero)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();

        $sql = sprintf(
            "UPDATE Pedido 
            SET estado='%s', cocinero='%s'
            WHERE num_pedido=%d AND fecha_hora='%s'",
            self::ESTADO_COCINANDO,
            $conn->real_escape_string($cocinero),
            $num_pedido,
            $conn->real_escape_string($fecha_hora)
        );

        return $conn->query($sql);
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


    // Getters
    public function getNumPedido() { return $this->num_pedido; }
    public function getFechaHora() { return $this->fecha_hora; }
    public function getEstado() { return $this->estado; }
    public function getTotal() { return $this->total; }
    
}