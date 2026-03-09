<?php
require_once __DIR__.'/includes/config.php';
use BistroFDI\Aplicacion;

$app = Aplicacion::getInstance();
$tituloPagina = "Gestión de Gerente";
/*
$GestionarProducto = RUTA_APP .'AW/includes/productos/listar_productos.php';
$GestionarUsuarios = RUTA_APP .'/includes/users/listar_usuario.php';
$GestionarCategorias = RUTA_APP .'/includes/categorias/listar_categorias.php';
$VisualizarPedidos = RUTA_APP .'/includes/pedidos/listar_pedidos.php';
*/

$GestionarProducto = RUTA_APP . '/AW/includes/productos/listar_productos.php';
$GestionarUsuarios = RUTA_APP . '/AW/includes/users/listar_usuario.php';
$GestionarCategorias = RUTA_APP . '/AW/includes/categorias/listar_categorias.php';
$VisualizarPedidos = RUTA_APP . '/AW/includes/pedidos/listar_pedidos.php';

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