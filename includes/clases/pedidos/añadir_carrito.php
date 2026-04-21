<?php
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\aplicacion;
use BistroFDI\clases\pedidos\Pedido;

 

$app = Aplicacion::getInstance();

if (!$app->isCurrentUserLogged()) {
    header('Location: login.php');
    exit();
}

$nombreUsuario = $app->getCurrentUserName();
$nombreProducto = urldecode(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$tipoPedido = Pedido::TIPO_DOMICILIO;   //por defecto-> a domicilio
$cantidadAAñadir = filter_input(INPUT_GET, 'cantidad', FILTER_VALIDATE_INT) ?: 1;

// Si viene el parámetro 'origen=carta', sumamos. Si no, es de la actualización de la cantidad en el carrito
$origen = filter_input(INPUT_GET, 'origen', FILTER_SANITIZE_STRING);
$carta = ($origen === 'carta');


if($nombreProducto && $tipoPedido){

    //buscar si el usuario tiene un pedido "abierto" (estado=recibido)
    //si no encuentra, crea un pedido nuevo
    [$fecha_hora, $num_pedido] = Pedido::pedidosNuevosUsuario($nombreUsuario, $tipoPedido);
    
    //añadir el producto en Pedido_Producto
    Pedido::insertarPedidoProducto($fecha_hora, $num_pedido, $nombreProducto, $cantidadAAñadir, $carta);

    //actualizar el precio
    Pedido::actualizarTotalPedido($fecha_hora, $num_pedido);  
} 

header('Location: ../../../carta.php');
exit();






