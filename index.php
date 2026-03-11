<?php
require_once __DIR__.'/includes/config.php';
use BistroFDI\Aplicacion;

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
    $contenidoPrincipal .= <<<EOS
        <form action="carta.php" method="get">
            <button type="submit">
                Acceder como Cliente
            </button>
        </form>

        <form action="camarero.php" method="get">
            <button type="submit">
                Acceder como Camarero
            </button>
        </form>
    EOS;
}

//cocineros
if ($app->isCurrentUserCook() || $app->isCurrentUserAdmin()) {
    $contenidoPrincipal .= <<<EOS
        <form action="cocinero.php" method="get">
            <button type="submit">
                Acceder como Cocinero
            </button>
        </form>
    EOS;
}

//gerente
if ($app->isCurrentUserAdmin()) {
    $contenidoPrincipal .= <<<EOS
        <form action="gerente.php" method="get">
            <button type="submit">
                Acceder como Gerente
            </button>
        </form>
    EOS;
}


require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';