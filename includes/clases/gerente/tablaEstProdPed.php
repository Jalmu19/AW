<?php
namespace BistroFDI\clases\tables;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\tables\tabla;

class TablaPedido extends Tabla {

    public function __construct($datos) {

        $columnas = [
            'nombre' => 'Producto',
            'estado' => 'Estado'
        ];

        parent::__construct($columnas, $datos, false);
    }

}
