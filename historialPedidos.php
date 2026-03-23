<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

$result = Pedido::historialPedidoUsuario($app->getCurrentUserName());

$columnas = [
    'id'           => 'Nº Pedido',
    'fecha_hora'        => 'Fecha',
    'tipo'         => 'Tipo',
    'productos'    => 'Detalle',
    'precio_total' => 'Importe',
    'estado'       => 'Estado'
];

//NO queremos columna de acciones (false)
$tabla = new TablaPedidos($columnas, $result, false);
$htmlTabla = $tabla->genera();

$contenidoPrincipal = <<<EOS
    <a href="{$ruta}/miCuenta.php">← Volver a mi cuenta</a>

    <h1>Historial de Pedidos</h1>
    <div>
        $htmlTabla
    </div>
EOS;


$tituloPagina = "Mi Historial de Pedidos";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';