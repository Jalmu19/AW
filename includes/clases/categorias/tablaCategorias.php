<?php
namespace BistroFDI\clases\categorias;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

 use BistroFDI\clases\tabla;

class TablaCategorias extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        // No hace falta formatear nada de forma concreta ya que los atributos de las categorías
        // son texto plano
        return parent::formateaContenido($campo, $valor, $fila);
    }
    
    protected function generaAcciones($fila) {
        $id = urlencode($fila['nombre']);
        
        $urlEditar = "crear_y_actualizar_categoria.php?id=$id";
        $urlBorrar = "borrar_categoria.php?id=$id";
        
        return <<<EOS
            <a href="$urlEditar">Editar</a>
            <a href="$urlBorrar" onclick="return confirm('¿Seguro que deseas eliminar esta categoria?')">Borrar</a>
        EOS;
    }
}
