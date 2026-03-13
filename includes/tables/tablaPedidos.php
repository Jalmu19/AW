<?php
namespace BistroFDI\tables;

require_once dirname(__DIR__).'/config.php';

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