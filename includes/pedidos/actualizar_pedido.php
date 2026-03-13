<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioActPedido;

//el gerente, camarero o cocinero
if (!$app->isCurrentUserAdmin() || $app->isCurrentUserCocinero() || $app->isCurrentUserCamarero()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$form = new FormularioActPedido();

$htmlForm = $form->gestiona();


$contenidoPrincipal = <<< EOS
    <div>
        <a href="listar_pedidos.php">← Volver al listado</a>
    </div>
EOS;


$contenidoPrincipal = <<<EOS
<h1>Modificar precio del producto</h1>
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