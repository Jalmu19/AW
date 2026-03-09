<?php
require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP.'/includes/tables/tabla.php';

class TablaCocinero extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {

        // Columna de lista de productos con checkbox por producto
        if ($campo === 'productos') {

            $html = "";

            foreach ($valor as $producto) {
                $html .= '
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="num_pedido" value="'.$fila['num_pedido'].'">
                        <input type="hidden" name="fecha_hora" value="'.$fila['fecha_hora'].'">
                    
                        <label>
                        <input type="checkbox" name="producto_preparado"
                               value="'.$producto['nombre'].'"
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

        // Añadimos un botón para terminar el pedido una vez que el cocinero vea que ha
        // marcado todos los productos que tenía por cocinar
        return '
            <form method="POST">
                <input type="hidden" name="pedido_terminado" value="'.$numPedido.'">
                <input type="hidden" name="fecha_hora" value="'.$fechaHora.'">
                <button type="submit">Terminar pedido</button>
            </form>

        ';
    }
}