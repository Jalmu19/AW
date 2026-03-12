<?php

use BistroFDI\tables\TablaPedidos;
use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;

require_once dirname(__DIR__).'/config.php';


$app = Aplicacion::getInstance();

$nombreUsuario = $app->getCurrentUserName();
$pedidos = Pedido::getCarritoUsuario($nombreUsuario);

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');



// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'num_pedido'  => 'Pedido',
    'cantidad' => 'Cantidad',
    'nombre'    => 'Producto',
    'precio'   => 'Precio'
];


$contenidoPrincipal .=  <<<EOS
<div>
  <a href="../../index.php">← Volver al inicio</a> 
</div>
EOS;

$contenidoPrincipal .= "<h1>Pedidos del carrito</h1>";

$accion = false;

$tabla = new TablaPedidos($columnas, $pedidos, $accion);
$contenidoPrincipal .= $tabla->genera();


$contenidoPrincipal .= "<h1>Total: " . $pedidos[0]['total'] . " €</h1>";

$tituloPagina = "Mi carrito";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';