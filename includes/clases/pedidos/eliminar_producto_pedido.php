<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\aplicacion;


$app=Aplicacion::getInstance();
//solo el gerente puede borrar productos
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$nombreProd = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


if ($nombreProd) {
    if (Pedido::borra_producto_carrito($nombreProd)) {
        // Guardamos un mensaje de éxito para mostrarlo en la siguiente petición
        $app->putRequestAttribute('mensaje', "El producto '$nombreProd' ha sido eliminado correctamente.");
    } 
    else {
        $app->putRequestAttribute('error', "Hubo un error al intentar eliminar el producto '$nombreProd' del carrito.");
    }
}

header('Location: ../../../carrito.php');
exit();