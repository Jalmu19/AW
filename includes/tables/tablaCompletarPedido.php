<?php
namespace BistroFDI\tables;

class TablaCompletarPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'id') {
            return "#" . htmlspecialchars($valor);
        }
        if ($campo === 'productos') {
            return $valor; 
        }
        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $idPedido = $fila['id'];

        $html = <<<EOS
            <form action="completar_pedido.php" method="POST">
                <input type="hidden" name="idPedido" value="$idPedido"> 
                <button type="submit">
                    Completar
                </button>
            </form>
        EOS;
        return $html;
    }
}