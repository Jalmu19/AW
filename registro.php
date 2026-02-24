<?php

require_once __DIR__.'/includes/config.php';
require_once RAIZ_APP.'/forms/formularioRegistro.php';

$form = new FormularioRegistro();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Registro';

$contenidoPrincipal = <<<EOS
<h1>Registro de usuario</h1>
$htmlFormRegistro
EOS;

require RAIZ_APP.'/vistas/plantillas/plantilla.php';