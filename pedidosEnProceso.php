<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\pedidos\tablaPedidos;
use BistroFDI\clases\aplicacion;

$app = Aplicacion::getInstance();
$ruta = RUTA_APP;

//solo usuarios logueados
if (!$app->isCurrentUserLogged()) {
    header('Location:' .RUTA_APP.'/login.php');
    exit();
}

$conn = $app->getConexionBd();

$result = Pedido::pedidosProcesoUsuario($app->getCurrentUserName());

$columnas = [
    'num_pedido'   => 'ID Pedido',
    'estado'       => 'Estado Actual'
];

//NO queremos columna de acciones (false)
$tabla = new TablaPedidos($columnas, $result, false);
$htmlTabla = $tabla->genera();

$tituloPagina = "Pedidos en Curso";
$contenidoPrincipal = <<<EOS
    <a href="{$ruta}/miCuenta.php">← Volver a mi cuenta</a>

    <h1>Estado de mis Pedidos</h1>
    <div>
        $htmlTabla
    </div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';