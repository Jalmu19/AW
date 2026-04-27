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

        if ($campo == 'descuento') {
            return number_format($valor, 0) . " %";
        }

        if ($campo === 'id_oferta') {
            return "#" . htmlspecialchars($valor);
        }

        if($campo === 'detalles'){
            
            $id = $fila['id_oferta'];
            $urlComprar = "vista_oferta.php?id_Oferta=$id";
           return <<<EOS
              <a href= $urlComprar class="boton-form"> Ver detalles</a>
            EOS;
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
        
        return "";
    }
}