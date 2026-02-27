<?php
require_once dirname(__DIR__).'/config.php';

//solo el gerente puede borrar usuarios
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: index.php');
    exit();
}

$nombreUsuario = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($nombreUsuario) {
    if (Usuario::borra($nombreUsuario)) {
        // Guardamos un mensaje de éxito para mostrarlo en la siguiente petición
        $app->putRequestAttribute('mensaje', "El usuario '$nombreUsuario' ha sido eliminado correctamente.");
    } 
    else {
        $app->putRequestAttribute('error', "Hubo un error al intentar eliminar al usuario '$nombreUsuario'.");
    }
}

header('Location: listar_users.php');
exit();