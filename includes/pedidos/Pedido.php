<?php

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
    private $productos; // array con producto y cantidad
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
            $pedido = new Pedido($f['nombreUsuario'], $f['productos'], $f['precio_total'], $f['estado'], $f['fecha'], $f['tipo']);
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

    private function insertar_pedido(){}
    private function crear_pedido(){}
    private function actualizar_pedido(){}
    private function borrar_pedido(){}

   
    //getters
    public function getId() { return $this->id; }
    public function getFecha() { return $this->fecha; }
    public function getEstado() { return $this->estado; }
    public function getNombreUsuario(){return $this->nombreUsuario; }
    public function getPrecioTotal(){return $this->precio_total; }
    public function getProductos(){return $this->productos; }

}