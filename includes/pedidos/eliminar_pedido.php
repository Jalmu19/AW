<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\pedidos\Pedido;


require_once dirname(__DIR__).'/config.php';

//solo el gerente puede borrar productos
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$nombre = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($nombre) {
    if (Pedido::borra($nombre)) {
        // Guardamos un mensaje de éxito para mostrarlo en la siguiente petición
        $app->putRequestAttribute('mensaje', "El pedido '$nombre' ha sido eliminado correctamente.");
    } 
    else {
        $app->putRequestAttribute('error', "Hubo un error al intentar eliminar el pedido '$nombre'.");
    }
}

header('Location: listar_pedidos.php');
exit();