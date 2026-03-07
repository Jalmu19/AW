<?php
require_once __DIR__.'/includes/config.php';

$app = Aplicacion::getInstance();
$tituloPagina = "Gestión de Cocinero";

if (!$app->isCurrentUserCook() && !$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

// Si se marca un pedido como terminado
if (isset($_POST['pedido_terminado'])) {
    $idPedido = $_POST['pedido_terminado'];
    Pedido::terminar_pedido($idPedido);
    header('Location: ' . $_SERVER['PHP_SELF']); // refresca la página
    exit();
}

require_once RAIZ_APP . '/includes/tables/tablaCocinero.php';

$columnas = [
    'num_pedido' => 'Número de Pedido',
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
<h1>Pedidos</h1>
$tabla
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';

