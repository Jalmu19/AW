<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\aplicacion;
$app = Aplicacion::getInstance();


$tituloPagina = "Gestión de Gerente";

$GestionarProducto = RUTA_APP .'/includes/clases/productos/listar_productos.php';
$GestionarUsuarios = RUTA_APP .'/includes/clases/users/listar_usuario.php';
$GestionarCategorias = RUTA_APP .'/includes/clases/categorias/listar_categorias.php';
$GestionarOfertas = RUTA_APP .'/includes/clases/ofertas/listar_ofertas.php';
$VisualizarPedidos = RUTA_APP .'/list_ped_ger.php';


//solo gerentes
if (!$app->isCurrentUserAdmin()) {
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

    <h1>Panel - Gerente</h1>
    <p>Seleccione la acción que desea realizar:</p>

    <div class="panel-botones">
        
        <form action= "$GestionarProducto" method="get">
            <button type="submit" class="boton">
                Gestionar los productos
            </button>
        </form>

        <form action= "$GestionarUsuarios" method="get">
            <button type="submit" class="boton">
                Gestionar los usuarios
            </button>
        </form>

        <form action= "$GestionarCategorias" method="get">
            <button type="submit" class="boton">
                Gestionar las categorías
            </button>
        </form>

         <form action= "$GestionarOfertas" method="get">
            <button type="submit" class="boton">
               Gestionar las ofertas
            </button>
        </form>

        <form action= "$VisualizarPedidos" method="get">
            <button type="submit" class="boton">
               Visualizar los pedidos
            </button>
        </form>
    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';