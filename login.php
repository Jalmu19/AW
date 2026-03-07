<?php

require_once __DIR__.'/includes/config.php';
use BistroFDI\forms\FormularioLogin;

$form = new FormularioLogin();
$htmlFormLogin = $form->gestiona();

$tituloPagina = 'Login';

$contenidoPrincipal = <<<EOS
<h1>Acceso al sistema</h1>
$htmlFormLogin
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
