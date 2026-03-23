<?php
namespace BistroFDI\clases\pedidos;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
use BistroFDI\clases\tabla;

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
