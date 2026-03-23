<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 


use BistroFDI\clases\camarero\tablaCompletarPedidos;
use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\aplicacion;
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

$contenidoPrincipal = <<<EOS
    <div>
        <a href="camarero.php">← Volver al Panel</a>
    </div>
EOS;

//recuperar mensajes (éxito/error al completar los pedidos)
$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');


if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'id'        => 'ID Pedido',
    'tipo'      => 'Tipo',
    'productos' => 'Productos que faltan'
];


$contenidoPrincipal .= "<h1>Gestión de Pedidos: Bebidas y Complementos</h1>";

$result = Pedido::pedidosParaCompletar();

$tabla = new TablaCompletarPedidos($columnas, $result, true);

$contenidoPrincipal .= $tabla->genera();

$tituloPagina = "Completar Pedidos";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';