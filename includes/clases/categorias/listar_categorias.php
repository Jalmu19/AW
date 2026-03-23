<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

 use BistroFDI\clases\categorias\tablaCategorias;
use BistroFDI\clases\Aplicacion;


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

$columnas = [
    'nombre'      => 'Nombre',
    'descripcion' => 'Descripción'
];

$contenidoPrincipal = <<<EOS
    <a href="../../gerente.php">← Volver al panel</a> 
    
<div>
    <h1>Gestión de Categorías</h1>
</div>
EOS;

// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";



$contenidoPrincipal .= <<<EOS
    <form action="crear_y_actualizar_categoria.php" method="get">
        <button type="submit">
           Añadir Categoría
        </button>
    </form>
EOS;


$accion = true;
$tabla = new TablaCategorias($columnas, $result, $accion);
$contenidoPrincipal .= $tabla->genera();

$tituloPagina = "Administración de Categorías";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';