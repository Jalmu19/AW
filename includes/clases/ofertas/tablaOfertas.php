<?php
namespace BistroFDI\clases\ofertas;

require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\Aplicacion;
use BistroFDI\clases\tabla;

class TablaOfertas extends Tabla {

   protected function formateaContenido($campo, $valor, $fila) {

        if ($campo == 'nombre') {
            return '<span class="nom-prod">' . htmlspecialchars($valor) . '</span>';
        }

        if ($campo == 'precio') {
            return '<span class="precio-prod">' . number_format((float)$valor, 2) . '</span>€';
        }

        if ($campo == 'cantidad') {
            return '<span class="cant-prod">' . htmlspecialchars($valor) . '</span>';
        }

        if ($campo == 'descuento') {
            return number_format($valor, 0) . " %";
        }

        if ($campo === 'id_oferta') {
            return "#" . htmlspecialchars($valor);
        }

        if ($campo === 'productos_pack') {
            return $valor;
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $paginaActual = basename($_SERVER['PHP_SELF']);

        if ($paginaActual == 'listar_ofertas.php') {
            $id = urlencode($fila['id_oferta']); 
            $urlEditar = "crear_actualizar_oferta.php?id=$id";
            $urlBorrar = "borrar_oferta.php?id=$id";
            
            return <<<EOS
                <a href="$urlEditar" class="boton-form">Editar</a>
                <a href="$urlBorrar" class="eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta oferta?')">Borrar</a>
            EOS;
        }

        if ($paginaActual == 'crear_actualizar_oferta.php') {
            return <<<EOS
                <button type="button" class="borrar_prod_pack">Borrar</button>
            EOS;
        }

        if($paginaActual == 'ver_ofertas.php'){

           $id = urlencode($fila['id_oferta']);

           $urlComprar = "includes/clases/pedidos/anyadir_carrito.php?id=$id&origen=ofertas";

           return <<<EOS
                <form action="$urlComprar" method="GET">
                    <input type="hidden" name="origen" value="ofertas">
                    <input type="hidden" name="id" value="$id">
                    <input type="number" class="cantidad_oferta" name="cant_ofert" value="1" min="1">
                    <button type="submit" class="boton-form">Comprar</button>
                </form>
            EOS;
        }
        
        return "";
    }
}