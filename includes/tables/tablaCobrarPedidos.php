<?php
namespace BistroFDI\tables;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\tables\tabla;
class TablaCobrarPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'id') {
            return "#" . htmlspecialchars($valor);
        }
        if ($campo === 'total') {
            return number_format($valor, 2) . " €"; 
        }
        if ($campo === 'productos') {
            return $valor;
        }
        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $idPedido = $fila['id'];
        $fecha = $fila['fecha_hora'];

        //El botón envía la orden de cobro al controlador
        return <<<EOS
            <form action="cobrar_pedido.php" method="POST">
                <input type="hidden" name="idPedido" value="$idPedido"> 
                <input type="hidden" name="fechaHora" value="$fecha"> 
                <button type="submit">
                    Confirmar Pago
                </button>
            </form>
        EOS;
    }
}