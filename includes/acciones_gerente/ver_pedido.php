<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__) . '/config.php';

use BistroFDI\aplicacion;
use BistroFDI\pedidos\Pedido;
use BistroFDI\tables\tablaEstProdPed;

$app = Aplicacion::getInstance();

if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$num = isset($_GET['num_pedido']) ? (int)$_GET['num_pedido'] : 0;
$fh  = isset($_GET['fecha_hora']) ? (string)$_GET['fecha_hora'] : '';

if ($num <= 0 || $fh === '') {
    $contenidoPrincipal = "Parámetros inválidos.";
} else {

    $productos = Pedido::getEstadoProductosPedido($num, $fh); 
    $tabla = new TablaEstProdPed($productos);
    $contenidoPrincipal = $tabla->genera();
}

$tituloPagina = "Detalle del pedido";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';