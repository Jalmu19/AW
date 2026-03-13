<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioCrearProducto;
use BistroFDI\aplicacion;

$app = Aplicacion::getInstance();

//solo gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}


$form = new FormularioCrearProducto();

$htmlForm = $form->gestiona();


$contenidoPrincipal = <<< EOS
    <div>
        <a href="listar_productos.php">← Volver al listado</a>
    </div>
EOS;

$contenidoPrincipal .= <<<EOS
<div>
    <h1>Gestión de Inventario: Nuevo Producto</h1>
    <p>Rellena todos los campos para dar de alta un producto en la carta.</p>
    
    <div>
        $htmlForm
    </div>
</div>
EOS;

$tituloPagina = 'Añadir Producto';
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';