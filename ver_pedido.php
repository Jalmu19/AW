<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';

use BistroFDI\clases\aplicacion;use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\gerente\tablaEstProdPed;

$app = Aplicacion::getInstance();

if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$num = isset($_GET['num_pedido']) ? (int)$_GET['num_pedido'] : 0;
$fh  = isset($_GET['fecha_hora']) ? (string)$_GET['fecha_hora'] : '';

$contenidoPrincipal="";

if ($num <= 0 || $fh === '') {
    $contenidoPrincipal = "Parámetros inválidos.";
} else {

    $productos = Pedido::getEstadoProductosPedido($num, $fh); 
    $tabla = new TablaEstProdPed($productos);

    $contenidoPrincipal .= <<<EOS
    <div>
        <a href="list_ped_ger.php" class="btn-volver"> ← Volver al listado de pedidos</a> 
    </div>
    <div>
        <h2> Pedido numero $num<h2>
    </div>
    EOS;

    $contenidoPrincipal .= $tabla->genera();
}

$tituloPagina = "Detalle del pedido";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';