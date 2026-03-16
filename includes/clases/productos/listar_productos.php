<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__).'/config.php';
use BistroFDI\clases\tables\tablaProductos;
use BistroFDI\clases\aplicacion;
use BistroFDI\clases\productos\Producto;

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$result = Producto::listarProductos();

$contenidoPrincipal = '';

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');


// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'imagen'      => 'Foto',
    'nombre'      => 'Nombre',
    'precio'      => 'Precio',
    'categoria'   => 'Categoría',
    'descripcion' => 'Descripción'
];



$contenidoPrincipal .= <<<EOS
    <a href="../../gerente.php">← Volver al panel</a>

<div>
    <h1>Gestión de Productos</h1>
</div>

<div> 
   <form action="crear_producto.php" method="get">
        <button type="submit">
           Añadir Producto
        </button>
    </form>
</div>
EOS;

$accion = true;
$tabla = new TablaProductos($columnas, $result, $accion);
$contenidoPrincipal .= $tabla->genera();

$tituloPagina = "Administración de Productos";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';