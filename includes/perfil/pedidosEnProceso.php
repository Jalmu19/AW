<?php
require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP.'/includes/tables/TablaPedidosProceso.php';

$app = Aplicacion::getInstance();
$ruta = RUTA_APP;

//solo usuarios logueados
if (!$app->isCurrentUserLogged()) {
    header('Location: login.php');
    exit();
}

$conn = $app->getConexionBd();
$nombreUsuario = $app->getCurrentUserName();

//consulta de pedidos activos
$query = "SELECT id, productos, precio_total, estado 
          FROM Pedidos 
          WHERE nombreUsuario = ? AND estado NOT IN ('Nuevo', 'Recibido', 'Entregado', 'Cancelado')
          ORDER BY id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$result = $stmt->get_result();

$columnas = [
    'id'           => 'ID Pedido',
    'productos'    => 'Productos',
    'precio_total' => 'Total',
    'estado'       => 'Estado Actual'
];

//NO queremos columna de acciones (false)
$tabla = new TablaPedidosProceso($columnas, $result, false);
$htmlTabla = $tabla->genera();

$tituloPagina = "Pedidos en Curso";
$contenidoPrincipal = <<<EOS
    <h1>Estado de mis Pedidos</h1>
    <a href={$ruta}."/miCuenta.php">← Volver a mi cuenta</a>
    <div>
        $htmlTabla
    </div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';