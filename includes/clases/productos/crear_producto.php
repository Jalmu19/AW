<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

 use BistroFDI\clases\productos\formularioCrearProducto;
use BistroFDI\clases\aplicacion;
$app = Aplicacion::getInstance();

//solo gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}


$form = new FormularioCrearProducto();

$htmlForm = $form->gestiona();

$Rutaflecha = RUTA_APP."/img/volver.png";
$contenidoPrincipal = "";

$contenidoPrincipal = <<< EOS
    <div>
        <a href="listar_productos.php" class="btn-volver" title="Volver al Inicio">
            <img src= "$Rutaflecha" alt="Volver al Inicio">
        </a>
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