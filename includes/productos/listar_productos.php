<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\tables\tablaProductos;
use BistroFDI\Aplicacion;

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$conn = $app->getConexionBd();
$result = $conn->query("SELECT nombre, precio, descripcion, imagen, categoria FROM Producto");

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$contenidoPrincipal = "<h1>Gestión de Productos</h1>";

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


$tituloPagina = "Administración de Productos";

$contenidoPrincipal .=  <<<EOS 
    <a href="index.php">← Volver al inicio</a> 
EOS;
 $contenidoPrincipal .= <<<EOS
   <form action="crear_producto.php" method="get">
        <button type="submit">
           Añadir Producto
        </button>
    </form>
EOS;

$accion = true;
$tabla = new TablaProductos($columnas, $result, $accion);
$contenidoPrincipal .= $tabla->genera();


require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';