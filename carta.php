<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\aplicacion;use BistroFDI\clases\productos\tablaProductos;
use BistroFDI\clases\productos\Producto;


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
<div class="panel-carta-categorias">
    <a href="carta.php?cat=Todos">Todos</a>
    <a href="carta.php?cat=entrante">Entrantes</a>
    <a href="carta.php?cat=primer plato">Primeros</a>
    <a href="carta.php?cat=segundo plato">Segundos</a>
    <a href="carta.php?cat=postre">Postres</a>
    <a href="carta.php?cat=bebida">Bebidas</a>
</div>
HTML;

$Rutaflecha = RUTA_APP."/img/volver.png";
$contenidoPrincipal = '';

if ($app->isCurrentUserAdmin() || $app->isCurrentUserCook() || $app->isCurrentUserWaiter()) {
    $contenidoPrincipal .= <<<EOS
        <a href="index.php" class="btn-volver" title="Volver al Inicio">
            <img src= "$Rutaflecha" alt="Volver al Inicio">
        </a>
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