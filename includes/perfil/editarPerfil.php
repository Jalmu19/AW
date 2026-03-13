<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioActUsuario;
use BistroFDI\users\Usuario;

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
   <a href="../../miCuenta.php">← Volver a mi cuenta</a>
</div>

<h1>Actualizacion de usuario</h1>
$htmlFormRegistro
EOS;


require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';