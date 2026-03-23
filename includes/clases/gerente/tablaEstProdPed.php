<?php
namespace BistroFDI\clases\gerente;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\tabla;

class TablaEstProdPed extends Tabla {

    public function __construct($datos) {

        $columnas = [
            'nombre' => 'Producto',
            'estado' => 'Estado'
        ];

        parent::__construct($columnas, $datos, false);
    }

}
