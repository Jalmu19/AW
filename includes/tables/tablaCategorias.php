<?php
require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP.'/includes/tables/tabla.php';
require_once RAIZ_APP.'/includes/categorias/Categoria.php';

class TablaCategorias extends Tabla {

     protected function formateaContenido($campo, $valor, $fila) {
         // No hace falta formatear nada de forma concreta ya que los atributos de las categorías
         // son texto plano
        return parent::formateaContenido($campo, $valor, $fila);
     }
    
     protected function generaAcciones($fila) {
        $id = urlencode($fila['nombre']);
        
        $urlEditar = "actualizar_categoria.php?id=$id";
        $urlBorrar = "borrar_categoria.php?id=$id";
        
        return <<<EOS
            <a href="$urlEditar">Editar</a>
            <a href="$urlBorrar" onclick="return confirm('¿Seguro que deseas eliminar esta categoria?')">Borrar</a>
        EOS;
    }
}