<?php

require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP.'/includes/tables/TablaHistorialPedidos.php';

$app = Aplicacion::getInstance();
$ruta = RUTA_APP;

//solo usuarios logueados
if (!$app->isCurrentUserLogged()) {
    header('Location: login.php');
    exit();
}

$conn = $app->getConexionBd();
$nombreUsuario = $app->getCurrentUserName();

//consulta de pedidos terminados
$query = "SELECT id, productos, precio_total, estado, fecha, tipo 
          FROM Pedidos 
          WHERE nombreUsuario = ? AND estado = 'Entregado'
          ORDER BY fecha DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$result = $stmt->get_result();

$columnas = [
    'id'           => 'Nº Pedido',
    'fecha'        => 'Fecha',
    'tipo'         => 'Tipo',
    'productos'    => 'Detalle',
    'precio_total' => 'Importe',
    'estado'       => 'Estado'
];

//NO queremos columna de acciones (false)
$tabla = new TablaHistorialPedidos($columnas, $result, false);
$htmlTabla = $tabla->genera();

$tituloPagina = "Mi Historial de Pedidos";
$contenidoPrincipal = <<<EOS
    <h1>Historial de Pedidos</h1>
    <a href={$ruta}."/miCuenta.php">← Volver a mi cuenta</a>
    <div>
        $htmlTabla
    </div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';