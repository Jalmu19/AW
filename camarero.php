<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\Aplicacion;

$app = Aplicacion::getInstance();


$tituloPagina = "Gestión de Camarero";


$Completar = "completar_pedido.php";
$Entregar  =  "entregar_pedido.php";
$Cobrar    = "cobrar_pedido.php";

//solo camareros, cocineros o gerentes
if (!$app->isCurrentUserLogged() || $app->isCurrentUserClient()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="index.php" class="btn-volver">← Volver al Inicio</a>
    </div>

    <h1>Panel - Camarero</h1>
    <p>Seleccione la acción que desea realizar sobre los pedidos:</p>

    <div>
        
        <form action= "$Completar" method="get">
            <button type="submit">
                Completar Pedidos
            </button>
        </form>

        <form action= "$Entregar" method="get">
            <button type="submit">
                Entregar Pedidos
            </button>
        </form>

        <form action= "$Cobrar" method="get">
            <button type="submit">
                Cobrar Cuenta
            </button>
        </form>

    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';