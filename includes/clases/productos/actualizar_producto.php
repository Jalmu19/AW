<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\productos\formularioActProducto;
use BistroFDI\clases\productos\Producto;
use BistroFDI\clases\aplicacion;
$app=Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}

//Intenta obtener el nombre de la URL (GET), y si no está, del formulario (POST)
$nombre = $_GET['id'] ?? $_POST['nombre'] ?? null;

// Si no hay ID en la URL y no es un envío de formulario, redirigimos
if (!$nombre) {
    header('Location:'.RUTA_APP.'/includes/clases/productos/listar_productos.php');
    exit();
}

$producto = Producto::buscaProducto($nombre); //
if (!$producto) {
    header('Location:'.RUTA_APP.'/includes/clases/productos/listar_productos.php');
    exit();
}

// Preparamos los datos para que el formulario se pinte con los valores actuales
$datos = [
    'nombre' => $producto->getNombre(),
    'precio' => $producto->getPrecio(),
    'categoria' => $producto->getCategoria(),
    'descripcion' => $producto->getDescripcion(),
    'imagen' => $producto->getImagen(),
    'disponibilidad' => $producto->getDisponibilidad(),
    'ofertado' => $producto->getOfertado(),
    'cocinable' => $producto->getCocinable()
];


$form = new FormularioActProducto();

$htmlForm = $form->gestiona($datos);    

$contenidoPrincipal = <<< EOS
    <div>
        <a href="listar_productos.php">← Volver al listado</a>
    </div>
EOS;

$contenidoPrincipal .= <<<EOS
<div>
    <h2>Gestión de Inventario: Actualizar $nombre </h2>
    <p>Rellena todos los campos para actualizar el producto en la carta.</p>

    <div>
        $htmlForm
    </div>
</div>
EOS;

$tituloPagina = "Actualizar producto";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';

 



