<?php

require_once __DIR__.'/includes/config.php';
require_once RAIZ_APP.'/includes/forms/formularioTarjeta.php';

$form = new formularioTarjeta();
$htmlFormTarjeta = $form->camposFormulario();

$tituloPagina = 'Pago bancario';

$contenidoPrincipal = <<<EOS
<h1>Datos Bancarios</h1>
$htmlFormRegistro
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';




