<?php

namespace BistroFDI\clases\cocinero;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 use BistroFDI\clases\tabla;

class TablaCocinero extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {

        // Columna de lista de productos con checkbox por producto
        if ($campo === 'productos') {

            $html = "";

            foreach ($valor as $producto) {
                $checked = ($producto['preparado'] == 1) ? 'checked' : '';
                $disabled = ($producto['preparado'] == 1) ? 'disabled' : '';

                $html .= '
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="num_pedido" value="'.$fila['num_pedido'].'">
                        <input type="hidden" name="fecha_hora" value="'.$fila['fecha_hora'].'">
                        <input type="hidden" name="nombre_producto" value="'.htmlspecialchars($producto['nombre']).'">
                    
                        <label>
                        <input type="checkbox" name="marcar_preparado"
                            value="1" 
                            '.$checked.' '.$disabled.'
                            onclick="this.form.submit();">
                        '.htmlspecialchars($producto['nombre']).'
                        </label><br>
                    </form>
                ';
            }

            return $html;
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $numPedido = $fila['num_pedido'];
        $fechaHora = $fila['fecha_hora'];
        $estado = $fila['estado'];
        $cocineroAsignado = $fila['cocinero'] ?? null; // Columna cocinero en tabla Pedido

        $html = "";

        // Si el pedido no tiene cocinero asignado (es NULL en la BD) y está en preparación
        if ($cocineroAsignado == null && $estado === 'En preparacion') {
            $html .= "
            <form method='POST'>
                <input type='hidden' name='num_pedido' value='$numPedido'>
                <input type='hidden' name='fecha_hora' value='$fechaHora'>
                <button type='submit' name='accion' value='aceptar_pedido'>Aceptar pedido</button>
            </form>
            ";
        } else {
            // Si ya tiene cocinero, solo mostramos el botón de Terminar
            $html .= "
            <form method='POST'>
                <input type='hidden' name='pedido_terminado' value='$numPedido'>
                <input type='hidden' name='fecha_hora' value='$fechaHora'>
                <button type='submit'>Terminar pedido</button>
            </form>
            ";
        }

        return $html;
    }
}
