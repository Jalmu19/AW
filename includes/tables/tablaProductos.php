<?php
require_once RAIZ_APP.'/includes/tables/tabla.php';
require_once RAIZ_APP.'/includes/productos/Producto.php';

class TablaProductos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'imagen') {
            $rutaImg = RUTA_IMGS . 'productos/' . $valor;
            return "<img src='$rutaImg' alt='Producto' style='width: 50px; height: auto;'>";
        }
        
        if ($campo === 'precio') {
            /**parametros de number_format()
                $valor: número
                2: número de decimales
                ',': separador de los decimales
                '.': separador de miles
            **/
            return number_format($valor, 2, ',', '.') . " €";
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $id = urlencode($fila['nombre']);
        
        $urlEditar = "actualizar_producto.php?id=$id";
        $urlBorrar = "borrar_producto.php?id=$id";
        
        return <<<EOS
            <a href="$urlEditar">Editar</a>
            <a href="$urlBorrar" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Borrar</a>
        EOS;
    }
}