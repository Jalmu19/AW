<?php
require_once __DIR__ . '/../../../autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 use BistroFDI\clases\categorias\formularioCategoria;
use BistroFDI\clases\Aplicacion;
use BistroFDI\clases\categorias\Categoria;

$app = Aplicacion::getInstance(); 

//solo gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$nombreCategoria = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$categoria = $nombreCategoria ? Categoria::buscaCategoria($nombreCategoria) : null;

$datosIniciales = [];
if ($categoria) {
    $datosIniciales = [
        'nombre' => $categoria->getNombre(),
        'descripcion' => $categoria->getDescripcion()
    ];
}


// Si categoria es null, se crea una nueva. Si no, se actualiza la existente
$form = new FormularioCategoria($categoria);  

$htmlForm = $form->gestiona($datosIniciales);

$modoEdicion = $categoria !== null;
$tituloPagina = $modoEdicion ? 'Actualizar Categoría' : 'Añadir Categoría';
$subtitulo = $modoEdicion 
    ? 'Modifica los campos que quieras actualizar de la categoría.' 
    : 'Rellena todos los campos para dar de alta una nueva categoría.';

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="listar_categorias.php">← Volver al listado</a>
    </div>

    <h1>Gestión de Inventario: $tituloPagina</h1>
    <p>$subtitulo</p>
    
    <div>
        $htmlForm
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';