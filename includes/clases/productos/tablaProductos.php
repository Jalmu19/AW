<?php
namespace BistroFDI\clases\productos;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 use BistroFDI\clases\aplicacion;use BistroFDI\clases\tabla;

class TablaProductos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'imagen') {
            $rutaImg = RUTA_IMGS . 'productos/' . $valor;
            return "<img src='$rutaImg' alt='Producto'class='producto'>";
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
        $app = Aplicacion::getInstance();
        $id = urlencode($fila['nombre']);

        // Obtenemos el nombre del archivo actual (ej: carta.php)
        $paginaActual = basename($_SERVER['PHP_SELF']);

        if ($paginaActual == 'carta.php') {
            $urlComprar = "includes/clases/pedidos/añadir_carrito.php?id=$id";
            
            return <<<EOS
                <form action="$urlComprar" method="GET">
                    <input type="hidden" name="id" value="$id">
                    <input type="number" name="cantidad" value="1" min="1" style="width: 40px;">
                    <button type="submit" class="boton-form">Comprar</button>
                </form>
            EOS;
        }

        if ($paginaActual == 'listar_productos.php') {
            $urlEditar = "actualizar_producto.php?id=$id";
            $urlBorrar = "borrar_producto.php?id=$id";
            
            return <<<EOS
                <a href="$urlEditar" class="boton-form">Editar</a>
                <a href="$urlBorrar" class="eliminar" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Borrar</a>
            EOS;
        }

        return ''; // Si no es ninguna de las anteriores, no devuelve acciones
    }
}
