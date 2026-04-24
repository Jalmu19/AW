<?php
namespace BistroFDI\clases\ofertas;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\ofertas\FormularioGestionOferta;
use BistroFDI\clases\ofertas\Oferta; // Necesario para buscar la oferta
use BistroFDI\clases\Aplicacion;

$app = Aplicacion::getInstance();

// Solo gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

// Buscamos el ID tanto si viene por la URL (GET) como si viene del formulario oculto (POST)
$id = $_GET['id'] ?? $_POST['id_oferta'] ?? null;
$oferta = null;
$titulo = "Crear una nueva oferta";
$subtitulo = "Rellena todos los campos para dar de alta una oferta.";
$tituloPagina = 'Añadir Oferta';

if ($id) {
    $oferta = Oferta::buscaOferta($id);
    if ($oferta) {
        $titulo = "Actualizar oferta: " . $oferta->getNombre();
        $subtitulo = "Modifica los campos necesarios para actualizar la promoción.";
        $tituloPagina = 'Actualizar Oferta';
    }
}

// Pasamos el objeto $oferta (que será null si es creación) al constructor
$form = new FormularioGestionOferta($oferta);
$htmlForm = $form->gestiona();

$Rutaflecha = RUTA_APP."/img/volver.png";

$contenidoPrincipal = <<< EOS
    <div>
        <a href="listar_ofertas.php" class="btn-volver" title="Volver al Listado">
            <img src= "$Rutaflecha" alt="Volver">
        </a>
    </div>
EOS;

$contenidoPrincipal .= <<<EOS
<div>
    <h1>$titulo</h1>
    <p>$subtitulo</p>
    
    <div>
        $htmlForm
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';