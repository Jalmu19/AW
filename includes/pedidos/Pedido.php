<?php
namespace BistroFDI\pedidos;
// INTERACTUA CON LA BBDD


class Pedido {

    //estados
    public const ESTADO_NUEVO = 'Nuevo';
    public const ESTADO_RECIBIDO = 'Recibido';
    public const ESTADO_PREPARACION = 'En preparacion';
    public const ESTADO_COCINANDO = 'Cocinando';
    public const ESTADO_LISTO_COCINA ='Listo cocina';
    public const ESTADO_TERMINADO = 'Terminado';
    public const ESTADO_ENTREGADO = 'Entregado';
    public const ESTADO_CANCELADO = 'Cancelado';

    //tipos
    public const TIPO_DOMICILIO = 'A domicilio';
    public const TIPO_LOCAL = 'En local';

    private $id; //numero pedido
    private $nombreUsuario;
    /**
     * @var array $productos 
     * Estructura: Array asociativo ["NombreProducto" => Cantidad]
     * Ejemplo: ["Agua" => 2, "Hamburguesa" => 1]
     * En la base de datos (columna 'productos') se guarda como un string JSON 
     * mediante json_encode() y se recupera con json_decode($datos, true).
     */
    private $productos;
    private $precio_total;
    private $estado;
    private $fecha;
    private $tipo;
    //clave primaria (id, fecha)

    private function __construct($id, $nombreUsuario, $productos, $precio_total, $estado, $fecha, $tipo) {
        $this->id = $id;
        $this->nombreUsuario = $nombreUsuario;
        $this->productos = $productos;
        $this->precio_total = $precio_total;
        $this->estado = $estado;
        $this->fecha = $fecha;
        $this->tipo = $tipo;
    }

    private function buscarPedido($id){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Pedidos WHERE id='%s'", $id);
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            
            // CONVERSIÓN: De JSON (texto) a Array Asociativo
            $productosArray = json_decode($f['productos'], true); 
            
            // Creamos el objeto con el array ya convertido
            $pedido = new Pedido($f['id'], $f['nombreUsuario'], $productosArray, $f['precio_total'], $f['estado'], $f['fecha'], $f['tipo']);
            $rs->free();
            return $pedido;
        }
        return false;
    }

    private function buscarPedidoPorEstado($estado){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Pedidos WHERE estadod='%s'", $estado);
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            $pedido = new Pedido($f['nombreUsuario'], $f['productos'], $f['precio_total'], $f['estado'], $f['fecha'], $f['tipo']);
            $rs->free();
            return $pedido;
        }
        return false;
    }

    //private function insertar_pedido(){}
     private function crear_pedido($pedido){
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        // CONVERSIÓN: De Array a JSON (texto)
        $productosJson = json_encode($pedido->productos);

        $query = sprintf("INSERT INTO Pedidos(nombreUsuario,productos,precio_total,fecha,tipo) 
                        VALUES ('%s', '%s', %f, '%s', '%s')",
                        $conn->real_escape_string($pedido->nombreUsuario),
                        $conn->real_escape_string($productosJson),
                        $pedido->precio_total,
                        $conn->real_escape_string($pedido->fecha),
                        $conn->real_escape_string($pedido->tipo));
        
        return $conn->query($query) ? "Nuevo pedido creado con éxito" : "No ha sido posible crear el pedido";
    }


    private function actualizar_pedido($id, $pedidoAct){

        $conn = Aplicacion::getInstance()->getConexionBd();
        $pedido = self->buscarPedido($id);
        if($pedido === FALSE){
            return "No se ha podido actualizar el pedido";
            //return true
        }
        $query = sprintf("UPDATE Pedido SET productos=%s, precio_total=%f, fecha=%s, tipo='%s'  WHERE id = $id",
                        $conn->real_escape_string($pedidoAct->productos),
                        $conn->real_escape_string($pedidoAct->precio_total),
                        $conn->real_escape_string($pedidoAct->fecha),
                        $conn->real_escape_string($pedidoAct->tipo));

        return $conn->query($query);
    }

    private function borrar_pedido($id){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $pedido = self->buscarPedido($id);
        if($pedido === FALSE){
            return "No se ha podido actualizar el pedido";
            //return true
        }
        $query = sprintf("DELETE FROM Pedido WHERE id = $id");

        return $conn->query($query);
    }

    //Terminar cocinar pedido (cocinero): Cocinando->ListoCocina
    public static function terminarCocinarPedido($idPedido) {
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = sprintf(
            "UPDATE Pedidos SET estado='%s' WHERE id='%s'",
            $conn->real_escape_string(self::ESTADO_LISTO_COCINA),
            $conn->real_escape_string($idPedido)
        );
    }

    //Completar pedido (camarero): ListoCocina->Terminado
    public static function completarPedido($id) {
        $conn = \Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Pedidos SET estado='%s' WHERE id=%d",
            self::ESTADO_TERMINADO,
            $id
        );
        return $conn->query($query);
    }
   
    //getters
    public function getId() { return $this->id; }
    public function getFecha() { return $this->fecha; }
    public function getEstado() { return $this->estado; }
    public function getNombreUsuario(){return $this->nombreUsuario; }
    public function getPrecioTotal(){return $this->precio_total; }
    public function getProductos(){return $this->productos; }

    public static function getPedidosCocinero() {
        $conn = Aplicacion::getInstance()->getConexionBd();

        // Obtenemos todos los pedidos que no estén terminados
        $sql = "SELECT * FROM Pedido WHERE estado != '".self::ESTADO_TERMINADO."'";
        $rs = $conn->query($sql);

        $pedidosCocinero = [];

        if ($rs) {
            while ($pedido = $rs->fetch_assoc()) {
                $numPedido = $pedido['num_pedido'];
                $fecha = $pedido['fecha_hora'];

                // Obtenemos los productos cocinables de este pedido
                $sqlProd = "
                    SELECT p.nombre, p.nombre as id
                    FROM Producto p
                    JOIN Pedido_Producto pp
                    ON pp.nombre = p.nombre
                    AND pp.num_pedido = '$numPedido'
                    AND pp.fecha_hora = '$fecha'
                    WHERE p.cocinable = 1
                ";
                $rsProd = $conn->query($sqlProd);

                $productos = [];
                if ($rsProd) {
                    while ($prod = $rsProd->fetch_assoc()) {
                        $productos[] = $prod;
                    }
                }

                // Solo añadimos pedidos que tengan productos cocinables
                if (count($productos) > 0) {
                    $pedidosCocinero[] = [
                        'pedido' => $numPedido,
                        'productos' => $productos
                    ];
                }
            }
            $rs->free();
        }
        
        return $pedidosCocinero;
    }

    //Pedidos que tiene que completar el camarero y los productos que son
    public static function pedidosParaCompletar() {
        $conn = \Aplicacion::getInstance()->getConexionBd();
        
        //Pedidos estado listoCocina
        $query = "SELECT id, productos FROM Pedidos WHERE estado = '" . self::ESTADO_LISTO_COCINA . "'";
        $rs = $conn->query($query);
        
        $listaFinal = [];
        
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                //Guardamos el id del pedido
                $idPedido = $f['id']; 
                
                //Decodificamos los productos (que están en formato JSON)
                $productosArr = json_decode($f['productos'], true);
                $textoProductos = "";
                
                if ($productosArr) {
                    foreach ($productosArr as $nombre => $cantidad) {
                        //Productos no cocinables
                        $queryProd = sprintf("SELECT cocinable FROM Producto WHERE nombre='%s'", 
                                            $conn->real_escape_string($nombre));
                        $resProd = $conn->query($queryProd);
                        $datosProd = $resProd->fetch_assoc();
                        
                        //Si el producto es no cocinable lo añadimos a la lista
                        if ($datosProd && $datosProd['cocinable'] == 0) {
                            $textoProductos .= "$cantidad x $nombre<br>";
                        }
                    }
                }
                
                $listaFinal[] = [
                    'id'        => $idPedido, //id pedido
                    'productos' => !empty($textoProductos) ? rtrim($textoProductos, '<br>') : ""
                ];
            }
            $rs->free();
        }
        return $listaFinal;
    }

}