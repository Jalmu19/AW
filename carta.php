<?php

use BistroFDI\tables\tablaProductos;
use BistroFDI\productos\Producto;
use BistroFDI\users\Usuario;

require_once __DIR__.'/includes/config.php';

$columnas = [
    'imagen'      => 'Foto',
    'nombre'      => 'Nombre',
    'descripcion' => 'Descripcion',
    'precio'      => 'Precio'
];


$tituloPagina = 'Bienvenido a Bistro FDI';

$tabla = new TablaProductos($columnas, Producto::listarProductos(), false);
$tabla_generada = $tabla->genera();

$contenidoPrincipal = <<<EOS
<h1>Bistro FDI</h1>
<fieldset>
    <legend>Carta</legend>
    $tabla_generada
</fieldset>
EOS;




require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
