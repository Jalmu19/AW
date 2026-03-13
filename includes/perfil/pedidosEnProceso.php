<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\pedidos\Pedido;
use BistroFDI\tables\TablaPedidos;
use BistroFDI\Aplicacion;

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