<?php
require_once __DIR__.'/includes/config.php';
use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;
use BistroFDI\tables\TablaCocinero;

$app = Aplicacion::getInstance();
$tituloPagina = "Gestión de Cocinero";

if (!$app->isCurrentUserCook() && !$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

// Si se marca un pedido como terminado
if (isset($_POST['pedido_terminado'])) {
    $numPedido = $_POST['pedido_terminado'];
    $fechaHora = $_POST['fecha_hora'];
    Pedido::terminarCocinarPedido($numPedido, $fechaHora);
    header('Location: ' . $_SERVER['PHP_SELF']); // refresca la página
    exit();
}

// Si se marca un producto como preparado
if (isset($_POST['producto_preparado'])) {
    $numPedido = (int) $_POST['num_pedido'];
    $fechaHora = $_POST['fecha_hora'];
    $nombreProducto = $_POST['producto_preparado'];
    Pedido::marcarProductoPedido($nombreProducto, $numPedido, $fechaHora);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}


$columnas = [
    'num_pedido' => 'Número del Pedido',
    'productos' => 'Productos'
];

// $datos puede venir de la base de datos usando tu clase Pedido, por ejemplo:
$datos = Pedido::getPedidosCocinero();  // Retorna array de pedidos

// Creamos la tabla
$tablaCocinero = new TablaCocinero($columnas, $datos, true);

// Generamos el HTML
$tabla = $tablaCocinero->genera();

// Mostramos en la plantilla
$contenidoPrincipal = <<<EOS
<div>
    <a href="index.php" class="btn-volver">← Volver al Inicio</a>
</div>

<div>
    <h1>Pedidos</h1>
    $tabla
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';

