<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\Aplicacion;
use BistroFDI\clases\pedidos\Pedido;

$app = Aplicacion::getInstance();

//coger los datos enviados por el formulario desde la URL (GET)
$id = $_GET['id'] ?? 'Desconocido';
$fecha = $_GET['fecha'] ?? null; 

if ($id && $fecha) {
    $pedido = Pedido::buscarPedido($id, $fecha); 
    
    if ($pedido) {
        $estado = $pedido->getEstado(); 
    }
}

$tituloPagina = 'Confirmación del pedido';
$contenidoPrincipal = '';

$contenidoPrincipal .= <<<EOS
    <div>
    <a href="carta.php">← Volver a la carta</a> 
    </div>
EOS;

$contenidoPrincipal .= <<<EOS
<h1>¡Pedido confirmado!</h1>
<p>Gracias por tu compra. Tu pedido ha sido registrado correctamente.</p>

<div>
    <p><strong>Número de Pedido:</strong> $id</p>
    <p><strong>Estado actual:</strong> $estado</p>
</div>

EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';
