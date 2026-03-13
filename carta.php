<?php

require_once __DIR__.'/includes/config.php';
use BistroFDI\Aplicacion;
use BistroFDI\tables\TablaProductos;
use BistroFDI\productos\Producto;

$app = Aplicacion::getInstance();


//filtro
$categoriaSeleccionada = $_GET['cat'] ?? 'Todos';

$columnas = [
    'imagen' => 'Foto',
    'nombre' => 'Nombre',
    'precio' => 'Precio'
];


if ($categoriaSeleccionada == 'Todos') {
    $productos = Producto::listarProductos();
} else {
    $productos = Producto::listarPorCategoria($categoriaSeleccionada);
}


$filtrosHtml = <<<HTML
<div>
    <a href="carta.php?cat=Todos">Todos</a>
    <a href="carta.php?cat=entrante">Entrantes</a>
    <a href="carta.php?cat=primer plato">Primeros</a>
    <a href="carta.php?cat=segundo plato">Segundos</a>
    <a href="carta.php?cat=postre">Postres</a>
    <a href="carta.php?cat=bebida">Bebidas</a>
</div>
HTML;

$contenidoPrincipal = '';

if ($app->isCurrentUserAdmin() || $app->isCurrentUserCook() || $app->isCurrentUserWaiter()) {
    $contenidoPrincipal .= <<<EOS
        <a href="index.php" class="btn-volver">← Volver al inicio</a>
    EOS;
}


$tabla = new TablaProductos($columnas, $productos, true);
$htmlTabla = $tabla->genera();

$contenidoPrincipal .= <<<EOS
<h1>Bistro FDI</h1>
$filtrosHtml
<fieldset>
    <legend>Carta - $categoriaSeleccionada</legend>
    $htmlTabla
</fieldset>
EOS;



$tituloPagina = 'Bienvenido a Bistro FDI';

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';