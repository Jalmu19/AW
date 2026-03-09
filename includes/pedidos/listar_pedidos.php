<?php
namespace BistroFDI\pedidos;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\tables\tablaPedidos;
use BistroFDI\Aplicacion;

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$pedidos = Pedidos::getTodosLosPedidos();

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$contenidoPrincipal = "<h1>Visualización de Pedidos</h1>";

// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'num_pedido'  => 'Num de pedido',
    'fecha_hora' => 'Fecha y hora',
    'tipo'    => 'Tipo',
    'total'   => 'Precio total',
    'estado' => 'Estado',
    'cliente' => 'Cliente',
    'cocinero' => 'Cocinero'
];

$accion = false;

$num_pedidos = $pedidos.sizeof();

$tabla = new TablaProductos($columnas, $result, $accion);
$contenidoPrincipal .=  <<<EOS 
    <a href="index.php">← Volver al inicio</a> 
EOS;
$contenidoPrincipal .= <<<EOS
    Total: $num_pedidos pedidos         
EOS;
$contenidoPrincipal .= $tabla->genera();

$tituloPagina = "Visualización de Pedidos";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';