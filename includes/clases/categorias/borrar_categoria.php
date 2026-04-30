<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\categorias\Categoria;
use BistroFDI\clases\aplicacion;

$app = Aplicacion::getInstance(); 
 
//solo el gerente puede borrar categorias
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$nombreCategoria = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($nombreCategoria) {
    if (Categoria::tieneProductos($nombreCategoria)) {
        $app->putRequestAttribute('error', "No puedes borrar la categoría '$nombreCategoria' porque tiene productos asociados. Primero cambia la categoría de esos productos.");
    }
    else if (Categoria::borra($nombreCategoria)) {
        // Guardamos un mensaje de éxito para mostrarlo en la siguiente petición
        $app->putRequestAttribute('mensaje', "La categoría '$nombreCategoria' ha sido eliminada correctamente.");
    } 
    else {
        $app->putRequestAttribute('error', "Hubo un error al intentar eliminar la categoría '$nombreCategoria'.");
    }
}

header('Location: listar_categorias.php');
exit();