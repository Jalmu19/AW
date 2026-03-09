<?php


require_once dirname(__DIR__).'/config.php';
use BistroFDI\tables\tablaCategorias;
use BistroFDI\Aplicacion;


$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$conn = $app->getConexionBd();
$result = $conn->query("SELECT nombre, descripcion FROM Categoria");

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$contenidoPrincipal = "<h1>Gestión de Categorías</h1>";

// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$columnas = [
    'nombre'      => 'Nombre',
    'descripcion' => 'Descripción'
];

$accion = true;

$tabla = new TablaProductos($columnas, $result, $accion);
$contenidoPrincipal .=  <<<EOS 
    <a href="index.php">← Volver al inicio</a> 
EOS;
$contenidoPrincipal .= <<<EOS
    <form action="crear_categoria.php" method="get">
        <button type="submit">
           Añadir Categoría
        </button>
    </form>
EOS;
$contenidoPrincipal .= $tabla->genera();

$tituloPagina = "Administración de Categorías";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';