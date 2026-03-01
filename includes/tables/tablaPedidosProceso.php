<?php
require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP.'/includes/tables/tabla.php';

class TablaPedidosProceso extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'precio_total') {
            return number_format($valor, 2, ',', '.') . " €";
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }
}