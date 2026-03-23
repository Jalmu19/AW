<?php
namespace BistroFDI\clases\users;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 use BistroFDI\clases\users\Usuario;
use BistroFDI\clases\tabla;


class TablaUsuarios extends Tabla {
    
    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'avatar') {
            $rutaImg = RUTA_IMGS . 'avatares/' . $valor;
            return "<img src='$rutaImg' alt='Usuario' style='width: 50px; height: auto;'>";
        }

        if ($campo === 'rol') {
            $roles = [
                Usuario::CLIENT_ROLE => 'Cliente',
                Usuario::WAITER_ROLE => 'Camarero',
                Usuario::COOK_ROLE   => 'Cocinero',
                Usuario::ADMIN_ROLE  => 'Gerente'
            ];
            return $roles[$valor] ?? 'Desconocido';
        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $id = $fila['nombreUsuario'];
        $urlEditar = "actualizar_usuario.php?id=$id";
        $urlBorrar = "borrar_usuario.php?id=$id";
        
        return <<<EOS
            <a href="$urlEditar">Editar</a>
            <a href="$urlBorrar" onclick="return confirm('¿Borrar a $id?')">Borrar</a>
        EOS;
    }
}
