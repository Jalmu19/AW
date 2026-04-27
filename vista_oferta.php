
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php'; 

use BistroFDI\clases\aplicacion;
use BistroFDI\clases\ofertas\Oferta;
use BistroFDI\clases\productos\Producto;
use BistroFDI\clases\productos\TablaProductos;

$app = Aplicacion::getInstance();

$idOferta = $_GET['id_Oferta'] ?? '';
$productos = Oferta::buscaProductosOferta($idOferta);
$filasTabla = [];
foreach($productos as $p => $cant){
    
    $producto = Producto::buscaProducto($p);
    $filasTabla[] = [
        'nombre' => $p,
        'cantidad' => $cant,
        'precio' => $producto ? $producto->getPrecio() : 0
    ];
    
}

$columnas = [
    'nombre'   => 'Producto',
    'cantidad' => 'Cantidad',
    'precio'   => 'Precio Unit.'
];

$tablaObj = new TablaProductos($columnas, $filasTabla, false);

$contenidoPrincipal = "";

$Rutaflecha = RUTA_APP."/img/volver.png";

$contenidoPrincipal .= <<<EOS
<div id = "">
    <a href="ver_ofertas.php" class="btn-volver" title="Volver al Inicio">
        <img src= "$Rutaflecha" alt="Volver al Inicio">
    </a>  
</div>

EOS;

$contenidoPrincipal .= $tablaObj->genera();

$contenidoPrincipal .= <<<EOS
<div>
    <h3>Total Pack (Sin descuento): <span id = "total_sin" >0.00</span>€</h3> 
    <h2>Con descuento: <span id="total_con">0.00</span>€</h2>   
    
</div>
<script> document.addEventListener('DOMContentLoaded', () => {calculoPrecioTotal(); calculoPrecioFinal();}); </script>
EOS;
//<div>
//       <label>Precio Final de la Oferta (€):</label>
//     <input id="precio_reducido" type="number" min="0" name="precio_reducido" step="0.01" placeholder="Ej: 9.99">
//</div>

//

$tituloPagina = "Detalles de oferta";
require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';
