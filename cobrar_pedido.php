<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\aplicacion;use BistroFDI\clases\camarero\tablaCobrarPedidos;
use BistroFDI\clases\pedidos\Pedido;

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



$contenidoPrincipal = <<<EOS
    <div>
        <a href="camarero.php" class="btn-volver">← Volver al Panel</a>
    </div>

    <h1>Pedidos Pendientes de Cobro</h1>
EOS;



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

$tituloPagina = "Cobrar Pedidos";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';