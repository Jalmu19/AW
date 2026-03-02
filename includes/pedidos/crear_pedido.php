<?php
//ES EL CARRITO DE LA COMPRA,
require_once dirname(__DIR__).'config.php'
require_once RAIZ_APP.'/includes/forms/formularioCrearPedido.php'

$app = Aplicacion::getInstance();

//si no esta logueado
if($app -> ){
    //no puede acceder
    $app->putRequestAttribute('error', 'No tienes acceso para realizar esta accion.');
    header('Location:'.RUTA_APP.'/index.php');
}

$productos_de_pedido = Carrito::listar_productos();

$tituloPagina = 'Crear un pedido';

$contenidoPrincipal = <<<EOS
<div>    
    <h1>Productos añadidos</h1>
    <div>
        $productos_de_pedido;
    </div>
</div>
EOS;

















?>