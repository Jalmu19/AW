<?php
namespace BistroFDI\tables;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\productos\Producto;
use BistroFDI\Aplicacion;


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
            $app = Aplicacion::getInstance();

            $id = urlencode($fila['nombre']);

            if($app->isCurrentUserClient()){
                $urlComprar = "añadir_carrito.php?id=$id";

                return <<<EOS
                    <a href="$urlComprar">Comprar</a>
                EOS;
            }

            if($app->isCurrentUserAdmin()){         
                $urlEditar = "actualizar_producto.php?id=$id";
                $urlBorrar = "borrar_producto.php?id=$id";
                
                return <<<EOS
                    <a href="$urlEditar">Editar</a>
                    <a href="$urlBorrar" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Borrar</a>
                EOS;
        }
        }
}