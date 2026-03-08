<?php

require_once __DIR__.'/includes/config.php';
use BistroFDI\forms\FormularioRegistro;

$form = new FormularioRegistro();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Registro';

$contenidoPrincipal = <<<EOS
<h1>Registro de usuario</h1>
$htmlFormRegistro
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';