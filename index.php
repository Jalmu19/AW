<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
require_once __DIR__ . '/autoload.php';
use BistroFDI\clases\aplicacion;
$app = Aplicacion::getInstance();


$tituloPagina = "Panel Principal";
$contenidoPrincipal = "<h1>Bienvenido a la Gestión del Restaurante</h1>";

//usuarios no logged in
if(!$app->isCurrentUserLogged()){
    header('Location: login.php');
}

//clientes
if($app->isCurrentUserClient()){
    header('Location: carta.php');
}

//camareros
if ($app->isCurrentUserWaiter() || $app->isCurrentUserCook() || $app->isCurrentUserAdmin()) {
    $contenidoPrincipal .= '<div class="panel-botones">';
    
    $contenidoPrincipal .= <<<EOS
        <form action="carta.php" method="get">
            <button type="submit" class="boton">Acceder como Cliente</button>
        </form>   

        <form action="camarero.php" method="get">
            <button type="submit" class="boton">Acceder como Camarero</button>
        </form>
    EOS;
}

//cocineros
if ($app->isCurrentUserCook() || $app->isCurrentUserAdmin()) {
    $contenidoPrincipal .= <<<EOS
        <form action="cocinero.php" method="get">
            <button type="submit" class="boton">Acceder como Cocinero</button>
        </form>
    EOS;
}

//gerente
if ($app->isCurrentUserAdmin()) {
    $contenidoPrincipal .= <<<EOS
        <form action="gerente.php" method="get">
            <button type="submit" class="boton">Acceder como Gerente</button>
        </form>
    EOS;
}

$contenidoPrincipal .= '</div>';

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';
