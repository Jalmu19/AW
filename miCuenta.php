<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\users\Usuario;
use BistroFDI\clases\aplicacion;
$app = Aplicacion::getInstance();


$ruta_img = RUTA_IMGS;
$ruta = RUTA_APP."/";

//solo usuarios logueados
if (!$app->isCurrentUserLogged()) {
    $app->putRequestAttribute('error', 'Debes iniciar sesión.');
    header('Location: login.php');
    exit();
}

$nombreUsuario = $app->getCurrentUserName();
$usuario = Usuario::buscaUsuario($nombreUsuario);

$tituloPagina = "Mi Perfil";

// Recuperar mensajes de éxito si viene de editar datos personales
$msg = $app->getRequestAttribute('mensaje');
$infoMsg = $msg ? "<div>$msg</div>" : "";

$Rutaflecha = RUTA_APP."/img/volver.png";

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="index.php" class="btn-volver" title="Volver al Inicio">
            <img src= "$Rutaflecha" alt="Volver al Inicio">
        </a>
        <div>
            <img src="{$ruta_img}avatares/{$usuario->getAvatar()}" alt="Avatar">
            <div>
                <h1>Hola, {$usuario->getNombreUsuario()}</h1>
                <a href="{$ruta}editarPerfil.php" class="boton">Editar mis datos</a>
            </div>
        </div>
    </div>

    <div class="panel-perfil">
        <h2>Gestión de Pedidos</h2>
        <div>
            <a href="{$ruta}pedidosEnProceso.php" class="boton-form">Pedidos en Proceso</a>
            <p>Consulta el estado actual de tus pedidos activos</p>

            <a href="{$ruta}historialPedidos.php" class="boton-form">
                Historial de Pedidos
            </a>
            <p>Revisa tus pedidos anteriores y facturas</p>
        </div>
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';

