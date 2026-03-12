<?php
namespace BistroFDI\tables;

class TablaPedido extends Tabla {

    public function __construct($datos) {

        $columnas = [
            'nombre' => 'Producto',
            'estado' => 'Estado'
        ];

        parent::__construct($columnas, $datos, false);
    }

}