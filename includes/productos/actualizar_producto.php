<?php
require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP.'/includes/forms/formularioActProducto.php';

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$form = new FormularioProducto();

$htmlForm = $form->gestiona();

$tituloPagina = "Actualizar Producto";


$contenidoPrincipal = <<<EOS
<h1>Modificar precio del producto</h1>
   <div>
    <div>
        <a href="listar_productos.php">← Volver al listado</a>
    </div>

    <h1>Gestión de Inventario: Actualizar Producto</h1>
    <p>Rellena todos los campos para actualizar el producto en la carta.</p>
    
    <div>
        $htmlForm
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';