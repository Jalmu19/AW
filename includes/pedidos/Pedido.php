<?php
namespace BistroFDI\pedidos;

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

        //insertar productos en Pedido_Producto
        foreach ($pedido->productos as $p) {
            $queryProd = sprintf("INSERT INTO Pedido_Producto (nombre, fecha_hora, num_pedido, cantidad, preparado) 
                VALUES ('%s', '%s', %d, %d, %d)",
                $conn->real_escape_string($p['nombre']),
                $conn->real_escape_string($pedido->fecha_hora),
                $pedido->num_pedido,
                $p['cantidad'],
                $p['preparado'] ?? 0
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

    //actualizar estado
    public static function actualizaEstado($num_pedido, $fecha_hora, $nuevoEstado) {
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

    //(completar pedido camarero): productos no cocinables de los pedidos
    public static function pedidosParaCompletar() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        //usamos GROUP_CONCAT para dar forma a la lista (ej: 2x Agua)
        $query = "SELECT p.num_pedido as id, p.fecha_hora,
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
                    $pedidosCocinero[] = ['id' => $f['num_pedido'], 'productos' => $prods];
                }
            }
        }
        return $pedidosCocinero;
    }

    //Terminar cocinar pedido (cocinero): Cocinando->ListoCocina
    public static function terminarCocinarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_LISTO_COCINA);
    }

    //Completar pedido (camarero): ListoCocina->Terminado
    public static function completarPedido($num_pedido, $fecha_hora) {
        return self::actualizaEstado($num_pedido, $fecha_hora, self::ESTADO_TERMINADO);
    }

    // Getters
    public function getNumPedido() { return $this->num_pedido; }
    public function getFechaHora() { return $this->fecha_hora; }
    public function getEstado() { return $this->estado; }
    public function getTotal() { return $this->total; }
}