<?php

require_once __DIR__.'/includes/config.php';
require_once RAIZ_APP.'/includes/productos/Producto.php';


$listaProductos = Producto::listarProductos();

$tituloPagina = 'Bienvenido a Bistro FDI';

$contenidoPrincipal = <<<EOS
<h1>Bistro FDI</h1>
<fieldset>
    <legend>Carta</legend>
    <?=$listaProductos?>
</fieldset>
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
