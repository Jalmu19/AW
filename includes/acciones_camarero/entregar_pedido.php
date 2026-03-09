<?php
require_once dirname(__DIR__).'/config.php';

use BistroFDI\tables\TablaEntregarPedidos;
use BistroFDI\pedidos\Pedido;
use BistroFDI\Aplicacion;

$app = Aplicacion::getInstance();

//solo personal autorizado
if (!$app->isCurrentUserLogged() || $app->isCurrentUserClient()) {
    $app->putRequestAttribute('error', 'No tienes permisos para entregar pedidos.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

//procesar la entrega
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPedido'])) {
    $id = $_POST['idPedido'];
    $fecha = $_POST['fechaHora'];

    if (Pedido::entregarPedido($id, $fecha)) {
        $app->putRequestAttribute('mensaje', "Pedido #$id entregado con éxito.");
    } else {
        $app->putRequestAttribute('error', "Error al entregar el pedido #$id.");
    }

    header('Location: ' . $_SERVER['PHP_SELF']); //refrescar página
    exit();
}

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$tituloPagina = "Entregar Pedidos";
$contenidoPrincipal = "<h1>Pedidos Listos para Entregar (En local)</h1>";

if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'id'        => 'ID Pedido',
    'cliente'   => 'Cliente',
    'productos' => 'Contenido'
];

$pedidos = Pedido::getPedidosParaEntregarLocal();
$tabla = new TablaEntregarPedidos($columnas, $pedidos, true);

$contenidoPrincipal .= $tabla->genera();
$contenidoPrincipal .= '<p><a href="camarero.php">← Volver al Panel</a></p>';

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';