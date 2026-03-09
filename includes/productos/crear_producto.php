<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioCrearProducto;
use BistroFDI\Aplicacion;

$app = Aplicacion::getInstance();

//solo gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}


$form = new FormularioProducto();

$htmlForm = $form->gestiona();

$tituloPagina = 'Añadir Producto';

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="listar_productos.php">← Volver al listado</a>
    </div>

    <h1>Gestión de Inventario: Nuevo Producto</h1>
    <p>Rellena todos los campos para dar de alta un producto en la carta.</p>
    
    <div>
        $htmlForm
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';