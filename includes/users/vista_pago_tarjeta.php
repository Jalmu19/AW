<?php
namespace BistroFDI\users;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioTarjeta;

$form = new formularioTarjeta();
$htmlFormTarjeta = $form->camposFormulario();

$tituloPagina = 'Pago bancario';

$contenidoPrincipal = <<<EOS
<h1>Datos Bancarios</h1>
$htmlFormTarjeta
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';




