<?php
namespace BistroFDI\clases\camarero;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\tabla;

class TablaEntregarPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'id') {
            return "#" . htmlspecialchars($valor);
        }
        if ($campo === 'productos') {
            return $valor; 
        }
        if ($campo === 'cliente') {
            return htmlspecialchars($valor);
        }
        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $id = $fila['id'];
        $fecha = $fila['fecha_hora'];
        $ruta = RUTA_APP;

        return <<<EOS
            <form action="$ruta/entregar_pedido.php" method="POST">
                <input type="hidden" name="idPedido" value="$id"> 
                <input type="hidden" name="fechaHora" value="$fecha"> 
                <button type="submit">Entregar</button>
            </form>
        EOS;
    }
}
