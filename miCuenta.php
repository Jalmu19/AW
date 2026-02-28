<?php
require_once __DIR__.'/includes/config.php';
require_once RAIZ_APP.'/includes/perfil/pedidosHistorialUsuario.php';
require_once RAIZ_APP.'/includes/perfil/pedidosProcesoUsuario.php';


$app = Aplicacion::getInstance();
$ruta_img = RUTA_IMGS;
$ruta_pedidos = RUTA_APP."/includes/miCuenta/";

//solo usuarios logueados
if (!$app->isCurrentUserLogged()) {
    header('Location: login.php');
    exit();
}

$nombreUsuario = $app->getCurrentUserName();
$usuario = Usuario::buscaUsuario($nombreUsuario);

$tituloPagina = "Mi Perfil";

// Recuperar mensajes de éxito si viene de editar datos personales
$msg = $app->getRequestAttribute('mensaje');
$infoMsg = $msg ? "<div class='alerta-exito'>$msg</div>" : "";

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="index.php">← Volver al inicio</a>
        <div>
            <img src="{$ruta_img}{$usuario->getAvatar()}" alt="Avatar" class="avatar-grande">
            <div>
                <h1>Hola, {$usuario->getNombreUsuario()}</h1>
                <a href="editarPerfil.php">Editar mis datos</a>
            </div>
        </div>
    </div>

    <div>
        <h2>Gestión de Pedidos</h2>
        <div>
            <a href={$ruta_pedidos}."pedidosEnProceso.php">
                <h3>Pedidos en Proceso</h3>
                <p>Consulta el estado actual de tus pedidos activos</p>
            </a>
            <a href={$ruta_pedidos}."historialPedididos.php">
                <h3>Historial de Pedidos</h3>
                <p>Revisa tus pedidos anteriores y facturas</p>
            </a>
        </div>
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';