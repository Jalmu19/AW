<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';
 
use BistroFDI\clases\users\formularioLogin;
use BistroFDI\clases\Aplicacion;

$app = Aplicacion::getInstance();


$form = new FormularioLogin();
$htmlFormLogin = $form->gestiona();

$tituloPagina = 'Login';

$contenidoPrincipal = <<<EOS
<h1>Acceso al sistema</h1>
$htmlFormLogin
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
