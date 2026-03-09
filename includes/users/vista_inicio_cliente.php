<?php
namespace BistroFDI\users;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\tables\tablaProductos;

$columnas = [
    'imagen'      => 'Foto',
    'nombre'      => 'Nombre',
    'precio'      => 'Precio'
];
$tabla = new TablaProductos($columnas, Producto::listarProductos(), False);

$tituloPagina = 'Bienvenido a Bistro FDI';

$contenidoPrincipal = <<<EOS
<h1>Bistro FDI</h1>
<fieldset>
    <legend>Carta</legend>
    <?=$$tabla?>
</fieldset>
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
