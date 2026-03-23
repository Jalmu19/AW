<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\aplicacion;


$app = Aplicacion::getInstance();

//Doble seguridad: unset + destroy
unset($_SESSION['login']);
unset($_SESSION['esAdmin']);
unset($_SESSION['nombre']);

session_destroy();


$tituloPagina = 'Logout';

$contenidoPrincipal = <<< EOS
	<h1>Hasta pronto!</h1>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';