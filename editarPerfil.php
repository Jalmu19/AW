<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';

use BistroFDI\clases\users\formularioActUsuario;
use BistroFDI\clases\users\Usuario;
use BistroFDI\clases\Aplicacion;


$app = Aplicacion::getInstance();


$nombreUsuario = $app->getCurrentUserName();
$usuario = Usuario::buscaUsuario($nombreUsuario);

$datos = [
   'nombre' => $usuario->getNombre(),
   'apellidos' => $usuario->getApellidos(),
   'correo' => $usuario->getEmail(),
   'password' => $usuario->getPassword(),
   'avatar' => $usuario->getAvatar(),
];



$form = new formularioActUsuario();

$htmlFormRegistro = $form->gestiona($datos);

$tituloPagina = 'Actualiza Usuario';

$contenidoPrincipal = <<<EOS
<div>
   <a href="miCuenta.php">← Volver a mi cuenta</a>
</div>

<h1>Actualizacion de usuario</h1>
$htmlFormRegistro
EOS;


require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';