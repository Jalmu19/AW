<?php

require_once dirname(__DIR__).'/config.php';
use BistroFDI\forms\formularioActProducto;
use BistroFDI\productos\Producto;

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permiso para realizar esta acción.');
    header('Location:'.RUTA_APP.'/index.php');
    exit();
}


$nombre = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if(! $nombre){
    header('Location:'.RUTA_APP.'/includes/productos/listar_productos.php');
    exit();
}

$producto = Producto::buscaProducto($nombre);
if(!$producto){
    header('Location:'.RUTA_APP.'/includes/productos/listar_productos.php');
    exit();
}

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

 



