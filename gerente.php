<?php
require_once __DIR__.'/includes/config.php';

$app = Aplicacion::getInstance();
$tituloPagina = "Gestión de Gerente";

$rutaAcciones = RUTA_APP .'/includes/tables/';
$GestionarProducto = $rutaAcciones . "tablaProductos.php";
$GestionarUsuarios  = $rutaAcciones . "tablaUsuarios.php";
$GestionarCategorias    = $rutaAcciones . "tablaCategorias.php";
$VisualizarPedidos    = $rutaAcciones . "tablaHistorialPedidos.php";

//solo gerentes
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="index.php" class="btn-volver">← Volver al Inicio</a>
    </div>

    <h1>Panel - Gerente</h1>
    <p>Seleccione la acción que desea realizar:</p>

    <div>
        
        <form action= "$GestionarProducto" method="get">
            <button type="submit">
                Gestionar los productos
            </button>
        </form>

        <form action= "$GestionarUsuarios" method="get">
            <button type="submit">
                Gestionar los usuarios
            </button>
        </form>

        <form action= "$GestionarCategorias" method="get">
            <button type="submit">
                Gestionar las categoráas
            </button>
        </form>

        <form action= "$VisualizarPedidos" method="get">
            <button type="submit">
               Visualizar los pedidos
            </button>
        </form>

    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';