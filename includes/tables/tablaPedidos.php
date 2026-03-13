<?php
namespace BistroFDI\tables;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__).'/config.php';

use BistroFDI\tables\tabla;

class TablaPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'estado') {
            return "<span>" . htmlspecialchars($valor) . "</span>";
        }
        
        if ($campo === 'precio_total') {
            // Formateamos el número como moneda
            return number_format($valor, 2, ',', '.') . " €";
        }

        if ($campo == 'productos') {
            return htmlspecialchars_decode($valor);
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

}