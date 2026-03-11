<?php
require_once dirname(__DIR__).'/config.php';

use BistroFDI\tables\TablaCompletarPedidos;
use BistroFDI\pedidos\Pedido;
use BistroFDI\Aplicacion;

$app = Aplicacion::getInstance();

//solo personal autorizado (Camarero, Cocinero o Gerente)
if (!$app->isCurrentUserLogged() || $app->isCurrentUserClient()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

//procesar la acción de "Completar" enviada desde la tabla 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPedido']) && isset($_POST['fechaHora'])) {
    $id = $_POST['idPedido'];
    $fecha = $_POST['fechaHora'];

    if (Pedido::completarPedido($id, $fecha)) { 
        $app->putRequestAttribute('mensaje', "Pedido #$id completado con éxito.");
    } else {
        $app->putRequestAttribute('error', "Error al completar el pedido #$id.");
    }

    header('Location: ' . $_SERVER['PHP_SELF']); //refrescar página
    exit();
}

//recuperar mensajes (éxito/error al completar los pedidos)
$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$tituloPagina = "Completar Pedidos";
$contenidoPrincipal = "<h1>Gestión de Pedidos: Bebidas y Complementos</h1>";

if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'id'        => 'ID Pedido',
    'tipo'      => 'Tipo',
    'productos' => 'Productos que faltan'
];


$contenidoPrincipal .= <<<EOS
    <div>
        <a href="../../camarero.php">← Volver al Panel</a>
    </div>
EOS;

$result = Pedido::pedidosParaCompletar();

$tabla = new TablaCompletarPedidos($columnas, $result, true);

$contenidoPrincipal .= $tabla->genera();

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';