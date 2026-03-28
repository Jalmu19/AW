<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\pedidos\tablaPedidos;
use BistroFDI\clases\aplicacion;use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\pedidos\formularioFinalizarPedido;


$app = Aplicacion::getInstance();


$contenidoPrincipal = '';

$nombreUsuario = $app->getCurrentUserName();
$pedidos = Pedido::getCarritoUsuario($nombreUsuario);

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$contenidoPrincipal .= <<<EOS
<div>
  <a href="carta.php" class="btn-volver">← Volver a la carta</a> 
</div>
EOS;

$contenidoPrincipal .= "<h1>Pedidos del carrito</h1>";

if ($pedidos && count($pedidos) > 0) {
    
  $columnas = [
    'num_pedido' => 'Pedido',
    'cantidad'   => 'Cantidad',
    'nombre'     => 'Producto',
    'precio'     => 'Precio'
  ];

  $tabla = new TablaPedidos($columnas, $pedidos, false);
  $contenidoPrincipal .= $tabla->genera();

  $total = isset($pedidos[0]['total']) ? $pedidos[0]['total'] : 0;
  $contenidoPrincipal .= "<h1>Total: " . $total . " €</h1>";

  $numPedido = $pedidos[0]['num_pedido'];
  $fechaHora = $pedidos[0]['fecha_hora'];

  $formFinalizar = new FormularioFinalizarPedido($numPedido, $fechaHora);
  
  $contenidoPrincipal .= $formFinalizar->gestiona();

} 
else {
  $contenidoPrincipal .= "<p>Tu carrito está actualmente vacío. ¡Añade algo de nuestra carta!</p>";
}

$tituloPagina = "Mi carrito";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';