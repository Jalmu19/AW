<?php

require_once __DIR__.'/includes/config.php';
use BistroFDI\Aplicacion;
use BistroFDI\tables\TablaProductos;
use BistroFDI\productos\Producto;

$app = Aplicacion::getInstance();

require_once __DIR__.'/includes/config.php';

$columnas = [
    'imagen' => 'Foto',
    'nombre' => 'Nombre',
    'precio' => 'Precio'
];

$tabla = new TablaProductos($columnas, Producto::listarProductos(), false);

$htmlTabla = $tabla->genera();

$tituloPagina = 'Bienvenido a Bistro FDI';

$tabla = new TablaProductos($columnas, Producto::listarProductos(), false);
$tabla_generada = $tabla->genera();

$contenidoPrincipal = <<<EOS
<h1>Bistro FDI</h1>
<fieldset>
    <legend>Carta</legend>
    $htmlTabla
</fieldset>
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
