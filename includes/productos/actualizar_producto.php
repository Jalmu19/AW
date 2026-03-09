<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioActProducto;

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$form = new FormularioActProducto();

$htmlForm = $form->gestiona();

$tituloPagina = "Actualizar Producto";

$contenidoPrincipal .= <<<EOS
    <a href="listar_productos.php">← Volver al listado</a> 
EOS;

$contenidoPrincipal .= <<<EOS

    <h2>Gestión de Inventario: Actualizar Producto</h2>
    <p>Rellena todos los campos para actualizar el producto en la carta.</p>
    
    <div>
        $htmlForm
    </div>

EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';