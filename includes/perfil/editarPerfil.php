<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioActUsuario;

$form = new formularioActUsuario();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Actualiza Usuario';

$contenidoPrincipal = <<<EOS
<h1>Actualizacion de usuario</h1>
$htmlFormRegistro
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';