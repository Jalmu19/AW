<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$productos = Pedido::getEstadoProductosPedido($num, $fh);

foreach ($productos as $p) {
    echo $p['nombre'] . " — " . $p['estado'] . "<br>";
}

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';