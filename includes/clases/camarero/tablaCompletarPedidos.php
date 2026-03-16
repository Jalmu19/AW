<?php
namespace BistroFDI\clases\tables;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\tables\tabla;

class TablaCompletarPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'id') {
            return "#" . htmlspecialchars($valor);
        }
        if ($campo === 'productos') {
            // El valor ya viene con <br> desde el GROUP_CONCAT de Pedido.php
            return $valor; 
        }
        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $idPedido = $fila['id'];
        $fecha = $fila['fecha_hora'] ?? ''; 

        $html = <<<EOS
            <form action="completar_pedido.php" method="POST">
                <input type="hidden" name="idPedido" value="$idPedido"> 
                <input type="hidden" name="fechaHora" value="$fecha"> 
                <button type="submit">
                    Completar
                </button>
            </form>
        EOS;
        return $html;
    }
}
