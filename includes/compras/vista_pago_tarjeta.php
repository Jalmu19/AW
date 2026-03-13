<?php
require_once dirname(__DIR__) . '/config.php';
use BistroFDI\forms\formularioTarjeta;

//coger los datos enviados por el formulario desde la URL (GET)
$numPedido = $_GET['id'] ?? null;
$fechaHora = $_GET['fecha'] ?? null;

$form = new FormularioTarjeta($numPedido, $fechaHora);

$htmlFormTarjeta = $form->gestiona();

$tituloPagina = 'Pago bancario';

$contenidoPrincipal = <<<EOS
    <h1>Datos Bancarios</h1>
    $htmlFormTarjeta
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';