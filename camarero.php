<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\aplicacion;
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

$Rutaflecha = RUTA_APP."/img/volver.png";
$contenidoPrincipal = <<<EOS
    <div>
        <a href="index.php" class="btn-volver" title="Volver al Inicio">
            <img src= "$Rutaflecha" alt="Volver al Inicio">
        </a>
    </div>

    <h1>Panel - Camarero</h1>
    <p>Seleccione la acción que desea realizar sobre los pedidos:</p>

    <div class="panel-botones">
    
        <form action= "$Cobrar" method="get">
            <button type="submit" class="boton">
                Cobrar Cuenta
            </button>
        </form>
        
        <form action= "$Completar" method="get">
            <button type="submit" class="boton">
                Completar Pedidos
            </button>
        </form>

        <form action= "$Entregar" method="get">
            <button type="submit" class="boton">
                Entregar Pedidos
            </button>
        </form>

    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';