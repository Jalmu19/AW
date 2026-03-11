<?php

require_once dirname(__DIR__) . '/config.php';

use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;
use BistroFDI\tables\Tabla;

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
    $columnas = [
        'nombre' => 'Producto',
        'estado' => 'Estado'
    ];
    $tabla = new Tabla($columnas, $productos, false);
    $contenidoPrincipal = $tabla->genera();
}

$tituloPagina = "Detalle del pedido";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';