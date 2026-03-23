<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\aplicacion;use BistroFDI\clases\users\formularioRegistro;


$app = Aplicacion::getInstance();


$form = new FormularioRegistro();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Registro';

$contenidoPrincipal = <<<EOS
<h1>Registro de usuario</h1>
$htmlFormRegistro
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';