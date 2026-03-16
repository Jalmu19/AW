<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__.'/includes/config.php';
use BistroFDI\clases\forms\formularioLogin;

$form = new FormularioLogin();
$htmlFormLogin = $form->gestiona();

$tituloPagina = 'Login';

$contenidoPrincipal = <<<EOS
<h1>Acceso al sistema</h1>
$htmlFormLogin
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
