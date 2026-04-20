<?php
namespace BistroFDI\clases\pedidos;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
use BistroFDI\clases\tabla;
use BistroFDI\clases\Aplicacion;

class TablaPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'estado') {
            return "<span>" . htmlspecialchars($valor) . "</span>";
        }
        
        if ($campo === 'precio_total') {
            // Formateamos el número como moneda
            return number_format($valor, 2, ',', '.') . " €";
        }

        if ($campo == 'productos') {
            return htmlspecialchars_decode($valor);
        }
        if ($campo == 'cantidad') {
            $idProd = $fila['nombre']; //ID del producto
            $precio = $fila['precio']; //precio del producto
            return <<<EOS
                    <button type="button" class="btn_disminuir_producto"> - </button>
                    <span class="cantidad_producto">$valor</span>
                    <input type="hidden" name="cantidades[$idProd]" class="input_cantidad" value="$valor">
                    <input type="hidden" class="precio_unidad" value="$precio">
                    <button type="button" class="btn_aumentar_producto"> + </button>                
                EOS;
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $id = urlencode($fila['nombre']);

        $urlBorrar = RUTA_APP . "/includes/clases/pedidos/eliminar_producto_pedido.php?id=$id";
        return <<<EOS
            <a href="$urlBorrar" class="borrar_prod_carrito">Borrar</a>
        EOS;
    }

}
