<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__).'/config.php';
use BistroFDI\clases\aplicacion;
use BistroFDI\clases\users\tablaUsuarios;
use BistroFDI\clases\users\Usuario;

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');



// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$result = Usuario::getTodosUsuarios();

$columnas = [
    'avatar'        => 'Avatar',
    'nombreUsuario' => 'Usuario',
    'nombre'        => 'Nombre',
    'rol'           => 'Rango'
];

$accion = true;

$tabla = new TablaUsuarios($columnas, $result, $accion);
$htmlTabla = $tabla->genera();

$tituloPagina = "Administración de Usuarios";
$contenidoPrincipal = <<<EOS
<div>
    <a href="../../gerente.php">← Volver al panel</a> 
</div>

<div>
    <h1>Gestión de Usuarios</h1>
</div>
EOS;

$contenidoPrincipal .= $htmlTabla;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';