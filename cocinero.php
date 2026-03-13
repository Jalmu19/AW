<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__.'/includes/config.php';
use BistroFDI\aplicacion;
use BistroFDI\pedidos\Pedido;
use BistroFDI\tables\tablaCocinero;

$app = Aplicacion::getInstance();
$tituloPagina = "Gestión de Cocinero";

if (!$app->isCurrentUserCook() && !$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}


// Si se acepta un pedido
if (isset($_POST['accion']) && $_POST['accion'] === 'aceptar_pedido') {
    $numPedido = (int) $_POST['num_pedido'];
    $fechaHora = $_POST['fecha_hora'];
    $cocinero = $app->getCurrentUserName();

    Pedido::aceptarPedido($numPedido, $fechaHora, $cocinero);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Si se marca un producto como preparado
if (isset($_POST['marcar_preparado'])) {
    $numPedido = (int) $_POST['num_pedido'];
    $fechaHora = $_POST['fecha_hora'];
    $nombreProducto = $_POST['nombre_producto'];
    Pedido::marcarProductoPedido($nombreProducto, $numPedido, $fechaHora);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Si se marca un pedido como listo cocina
if (isset($_POST['pedido_terminado'])) {
    $numPedido = $_POST['pedido_terminado'];
    $fechaHora = $_POST['fecha_hora'];
    Pedido::terminarCocinarPedido($numPedido, $fechaHora);
    header('Location: ' . $_SERVER['PHP_SELF']); // refresca la página
    exit();
}

$columnas = [
    'num_pedido' => 'Número del Pedido',
    'productos' => 'Productos',
    'estado' => 'Acciones'
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

