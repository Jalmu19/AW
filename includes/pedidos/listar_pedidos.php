<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\tables\tablaPedidos;
use BistroFDI\aplicacion;
use BistroFDI\pedidos\Pedido;

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$pedidos = Pedido::getTodosLosPedidos();

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$contenidoPrincipal =  <<<EOS
    <div>
    <a href="../../gerente.php">← Volver al panel</a> 
    </div>

    <h1>Visualización de Pedidos</h1>
EOS;


// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'num_pedido'  => 'Pedido',
    'fecha_hora' => 'Fecha y hora',
    'tipo'    => 'Tipo',
    'total'   => 'Precio total',
    'estado' => 'Estado',
    'cliente' => 'Cliente',
    'cocinero' => 'Cocinero'
];

$accion = false;

$tabla = new TablaPedidos($columnas, $pedidos, $accion);
$contenidoPrincipal .= $tabla->genera();

$tituloPagina = "Visualización de Pedidos";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';