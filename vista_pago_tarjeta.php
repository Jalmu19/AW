<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__) . '/config.php';
use BistroFDI\clases\pedidos\formularioTarjeta;

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