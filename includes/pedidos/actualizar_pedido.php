<?php


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

$tituloPagina = "Actualizar Pedido";


$contenidoPrincipal = <<<EOS
<h1>Modificar precio del producto</h1>
   <div>
    <div>
        <a href="listar_pedidos.php">← Volver al listado</a>
    </div>

    <h1>Actualizar Pedido</h1>
    <p>Establece el estado del pedido a seleccionar.</p>   
    <div>
        $htmlForm
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';