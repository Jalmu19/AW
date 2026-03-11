<?php
require_once dirname(__DIR__).'/config.php';

use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;
use BistroFDI\tables\TablaPedidosGerente;

$app = Aplicacion::getInstance();

// solo gerentes
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$pedidos = Pedido::getPedidosGerente();

$columnas = [
    'num_pedido' => 'Pedido',
    'estado'     => 'Estado',
];

$tabla = new TablaPedidosGerente($columnas, $pedidos, true);

$tituloPagina = "Visualización de Pedidos";
$contenidoPrincipal  = "<h1>Visualización de Pedidos</h1>";
$contenidoPrincipal .= $tabla->genera();

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';