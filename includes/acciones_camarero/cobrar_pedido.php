<?php
require_once dirname(__DIR__).'/config.php';

use BistroFDI\tables\TablaCobrarPedidos;
use BistroFDI\pedidos\Pedido;

$app = Aplicacion::getInstance();

//solo personal autorizado
if (!$app->isCurrentUserLogged() || $app->isCurrentUserClient()) {
    $app->putRequestAttribute('error', 'No tienes permisos para cobrar pedidos.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

//procesar el cobro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPedido'])) {
    $id = $_POST['idPedido'];
    $fecha = $_POST['fechaHora'];

    // Al cobrar, el pedido pasa a 'En preparación' para que aparezca al cocinero 
    if (Pedido::cobrarPedido($id, $fecha)) {
        $app->putRequestAttribute('mensaje', "Pago del pedido #$id confirmado correctamente.");
    } else {
        $app->putRequestAttribute('error', "Error al procesar el pago del pedido #$id.");
    }

    header('Location: ' . $_SERVER['PHP_SELF']); //refrescar página
    exit();
}

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$tituloPagina = "Cobrar Pedidos";
$contenidoPrincipal = "<h1>Pedidos Pendientes de Cobro</h1>";

if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'id'        => 'ID Pedido',
    'total'     => 'Precio Total',
    'productos' => 'Productos'
];

$pedidos = Pedido::getPedidosParaCobrar();
$tabla = new TablaCobrarPedidos($columnas, $pedidos, true);

$contenidoPrincipal .= $tabla->genera();
$contenidoPrincipal .= '<p><a href="camarero.php">Volver al Panel</a></p>';

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';