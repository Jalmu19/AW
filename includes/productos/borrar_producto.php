<?php
require_once dirname(__DIR__).'/config.php';

//solo el gerente puede borrar productos
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: index.php');
    exit();
}

$nombre = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($nombre) {
    if (Producto::borra($nombre)) {
        // Guardamos un mensaje de éxito para mostrarlo en la siguiente petición
        $app->putRequestAttribute('mensaje', "El producto '$nombre' ha sido eliminado correctamente.");
    } 
    else {
        $app->putRequestAttribute('error', "Hubo un error al intentar eliminar el producto '$nombre'.");
    }
}

header('Location: listar_productos.php');
exit();