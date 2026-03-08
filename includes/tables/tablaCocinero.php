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
                    <label>
                        <input type="checkbox" name="producto[]" value="'.$producto['id'].'">
                        '.htmlspecialchars($producto['nombre']).'
                    </label><br>
                ';
            }

            return $html;
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {

        $idPedido = $fila['pedido'];

        // Añadimos un botón para terminar el pedido una vez que el cocinero vea que ha
        // marcado todos los productos que tenía por cocinar
        return '
            <form method="POST">
                <input type="hidden" name="pedido_terminado" value="'.$idPedido.'">
                <button type="submit">Terminar pedido</button>
            </form>
        ';
    }
}