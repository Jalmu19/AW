<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\aplicacion;
use BistroFDI\clases\ofertas\tablaOfertas;
use BistroFDI\clases\ofertas\Oferta;


$app = Aplicacion::getInstance();

$columnas = [
    'id_oferta'=>'Número',
    'nombre' => 'Nombre',
    'productos_pack'=>'Productos incluidos',
    'cantidad'=>'Cantidad',
    'descuento' => 'Descuento',    
    'precio'=>'Precio'

];

$ofertas = Oferta::listarOfertas(true);


$Rutaflecha = RUTA_APP."/img/volver.png";
$contenidoPrincipal = '';

if ($app->isCurrentUserAdmin() || $app->isCurrentUserCook() || $app->isCurrentUserWaiter()) {
    $contenidoPrincipal .= <<<EOS
        <a href="opciones.php" class="btn-volver" title="Volver al Inicio">
            <img src= "$Rutaflecha" alt="Volver al Inicio">
        </a>
    EOS;
}

$tabla = new tablaOfertas($columnas, $ofertas, true);
$htmlTabla = $tabla->genera();

$contenidoPrincipal .= <<<EOS

<fieldset>
    <legend>Ofertas actuales</legend>
    <div>$htmlTabla</div>
</fieldset>
EOS;



$tituloPagina = 'Bienvenido a Bistro FDI';

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';