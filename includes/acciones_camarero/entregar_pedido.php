<?php
require_once dirname(__DIR__).'/config.php';

use BistroFDI\tables\tablaEntregarPedidos;
use BistroFDI\pedidos\Pedido;
use BistroFDI\aplicacion;

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




$contenidoPrincipal = <<<EOS
    <div>
        <a href="../../camarero.php">← Volver al Panel</a>
    </div>
    <h1>Pedidos Listos para Entregar</h1>
EOS;



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

$tituloPagina = "Entregar Pedidos";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';