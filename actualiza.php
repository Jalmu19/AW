<?php

require_once __DIR__.'/includes/config.php';
require_once RAIZ_APP.'/includes/forms/formularioActUsuario.php';

$form = new formularioActUsuario();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Actualiza Usuario';

$contenidoPrincipal = <<<EOS
<h1>Actualizacion de usuario</h1>
$htmlFormRegistro
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';