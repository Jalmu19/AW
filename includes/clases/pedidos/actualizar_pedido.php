<?php
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\clases\pedidos\formularioActPedido;
use BistroFDI\clases\aplicacion;
use BistroFDI\clases\pedidos\Pedido;


$app = Aplicacion::getInstance();

//el gerente, camarero o cocinero
if (!$app->isCurrentUserAdmin() || $app->isCurrentUserCook() || $app->isCurrentUserWaiter()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}


$num_pedido = $_GET['num_pedido'] ?? $_POST['id_pedido'] ?? null;
$fecha_hora = $_GET['fecha_hora'] ?? $_POST['fecha_hora'] ?? null;

$num_pedido = filter_var($num_pedido, FILTER_SANITIZE_NUMBER_INT);
$fecha_hora = filter_var($fecha_hora, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


$pedido = Pedido::buscarPedido($num_pedido, $fecha_hora); 
if (!$pedido) {
    header('Location:'.RUTA_APP.'/list_ped_ger.php');
    exit();
}
// Preparamos los datos para que el formulario se pinte con los valores actuales
$datos = [
    'id_pedido' => $pedido->getNumPedido(),
    'fecha_hora' => $pedido->getFechaHora(),
];


$form = new FormularioActPedido();

$htmlForm = $form->gestiona($datos);

$ruta=RUTA_APP;

$Rutaflecha = RUTA_APP."/img/volver.png";

$contenidoPrincipal = <<< EOS
    <div>
        <a href="$ruta/list_ped_ger.php" class="btn-volver" title="Volver al Inicio">
            <img src= "$Rutaflecha" alt="Volver al Inicio">
        </a>
    </div>
EOS;


$contenidoPrincipal .= <<<EOS
<div>
    <h1>Actualizar Pedido</h1>
    <p>Establece el estado del pedido a seleccionar.</p>   
    <div>
        $htmlForm
    </div>
</div>
EOS;


$tituloPagina = "Actualizar Pedido";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';