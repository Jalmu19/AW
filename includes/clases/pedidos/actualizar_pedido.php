<?php
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\clases\pedidos\formularioActPedido;
use BistroFDI\clases\aplicacion;


$app = Aplicacion::getInstance();

//el gerente, camarero o cocinero
if (!$app->isCurrentUserAdmin() || $app->isCurrentUserCook() || $app->isCurrentUserWaiter()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$form = new FormularioActPedido();

$htmlForm = $form->gestiona();

$ruta=RUTA_APP;

$contenidoPrincipal = <<< EOS
    <div>
        <a href="$ruta/list_ped_ger.php" class="btn-volver">← Volver al listado</a>
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