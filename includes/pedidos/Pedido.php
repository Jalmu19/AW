<?php

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

   
    //getters
    public function getId() { return $this->id; }
    public function getFecha() { return $this->fecha; }
    public function getEstado() { return $this->estado; }
}