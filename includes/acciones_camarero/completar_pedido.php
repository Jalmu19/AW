<?php
require_once dirname(__DIR__).'/config.php';

use BistroFDI\tables\TablaCompletarPedidos;
use BistroFDI\pedidos\Pedido;

$app = Aplicacion::getInstance();

//Solo personal autorizado
if (!$app->isCurrentUserLogged() || $app->isCurrentUserClient()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

//Procesar la acción de "Completar" de la tabla 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPedido'])) {
    $id = $_POST['idPedido'];
    if (Pedido::completarPedido($id)) { //actualizamos el estado del pedido
        $app->putRequestAttribute('mensaje', "Pedido #$id completado con éxito.");
    } else {
        $app->putRequestAttribute('error', "Error al completar el pedido #$id.");
    }
}

//Recuperar mensajes del objeto Aplicacion 
//(si ha tenido éxito o no la acción de completar el pedido)
$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$tituloPagina = "Completar Pedidos";
$contenidoPrincipal = "<h1>Gestión de Pedidos: Bebidas y Complementos</h1>";

if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'id'        => 'ID Pedido',
    'productos' => 'Productos que faltan'
];

$result = Pedido::pedidosParaCompletar();

$tabla = new TablaCompletarPedidos($columnas, $result, true);

$contenidoPrincipal .= <<<EOS
    <div>
        <a href="camarero.php">← Volver al Panel</a>
    </div>
EOS;

$contenidoPrincipal .= $tabla->genera();

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';