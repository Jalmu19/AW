<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';

use BistroFDI\clases\aplicacion;use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\gerente\tablaPedidosGerente;

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


$tituloPagina = "Visualización de Pedidos";

$contenidoPrincipal  = <<<EOS
    <a href="gerente.php">← Volver al panel</a> 
    <h1>Visualización de Pedidos</h1>
EOS;

$tabla = new TablaPedidosGerente($columnas, $pedidos, true);
$contenidoPrincipal .= $tabla->genera();

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';