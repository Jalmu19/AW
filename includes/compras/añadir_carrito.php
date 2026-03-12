<?php
use BistroFDI\users\Usuarios;
use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;

require_once dirname(__DIR__).'/config.php';


$app = Aplicacion::getInstance();

if (!$app->isCurrentUserLogged()) {
    header('Location: login.php');
    exit();
}

$nombreUsuario = $app->getCurrentUserName();
$nombreProducto = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$tipoPedido = Pedido::TIPO_DOMICILIO;   //por defecto-> a domicilio
$cantidadAAñadir = filter_input(INPUT_GET, 'cantidad', FILTER_VALIDATE_INT) ?: 1;

if($nombreProducto && $tipoPedido){

    //buscar si el usuario tiene un pedido "abierto" (estado=recibido)
    //si no encuentra, crea un pedido nuevo
    [$fecha_hora, $num_pedido] = Pedido::pedidosNuevosUsuario($nombreUsuario, $tipoPedido);
    
    //añadir el producto en Pedido_Producto
    Pedido::insertarPedidoProducto($fecha_hora, $num_pedido, $nombreProducto, $cantidadAAñadir);

    //actualizar el precio
    Pedido::actualizarTotalPedido($fecha_hora, $num_pedido);  
} 

header('Location: ../../carta.php');
exit();






