<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\aplicacion;
$app = Aplicacion::getInstance();


$tituloPagina = "opciones";

$ofertasActivas = RUTA_APP .'/ver_ofertas.php';
$verCartaProductos = RUTA_APP .'/carta.php';


$Rutaflecha = RUTA_APP."/img/volver.png";
$contenidoPrincipal = <<<EOS
<div>
    <a href="index.php" class="btn-volver" title="Volver al Inicio">
        <img src= "$Rutaflecha" alt="Volver al Inicio">
    </a> 

    <h1>¿Qué le apetece hacer hoy?</h1>
    <p>Seleccione la acción que desea realizar:</p>

    <div class="panel-botones">
        
        <form action= "$ofertasActivas" method="get">
            <button type="submit" class="boton">
                Ofertas activas
            </button>
        </form>

        <form action= "$verCartaProductos" method="get">
            <button type="submit" class="boton">
                Accede a nuestra carta
            </button>
        </form>

    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';