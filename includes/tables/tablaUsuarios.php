<?php
namespace BistroFDI\tables;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\users\Usuario;

class TablaUsuarios extends Tabla {
    
    protected function formateaContenido($campo, $valor, $fila) {
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
        $urlEditar = RUTA_APP."/users/actualizar_usuario.php?id=$id";
        $urlBorrar = RUTA_APP."/users/borrar_usuario.php?id=$id";
        
        return <<<EOS
            <a href="$urlEditar">Editar</a>
            <a href="$urlBorrar" onclick="return confirm('¿Borrar a $id?')">Borrar</a>
        EOS;
    }
}