<?php

use BistroFDI\clases\aplicacion;
function perfil() {
    $app = Aplicacion::getInstance();
    $html = '';

    if ($app->isCurrentUserLogged()) {
        $avatar = $app->getCurrentUserAvatar();
        $nombre = $app->getCurrentUserRealName();

        $rutaImg = RUTA_APP . '/img/avatares/' . $avatar;
        $fotoCarrito = RUTA_APP . '/img/carrito.png';
        $rutaPerfil = RUTA_APP . '/miCuenta.php';
        $rutaLogout = RUTA_APP . '/logout.php';
        $rutaCarrito = RUTA_APP. '/carrito.php';

        $html = "<div>
            Hola, 
            <a href='$rutaPerfil'>$nombre<img src='$rutaImg' class = 'cabecera'></a>
            <a href='$rutaCarrito'>Mi carrito<img src='$fotoCarrito' class = 'cabecera'></a>
            <a href='$rutaLogout'>Salir</a>
        </div>";
    } else {
        $rutaRegistro = RUTA_APP . '/registro.php';
        $rutaLogin = RUTA_APP . '/login.php';

        $html = "<div class=login-registro>
            <a href='$rutaLogin'>Login</a> | <a href='$rutaRegistro'>Registro</a>
        </div>";
    }
    return $html;
}
?>

<header>
    <div class="logo-central">
        <a href="<?= RUTA_APP ?>/index.php">
            <img src="<?= RUTA_APP ?>/img/logo_bistro.png" alt="Bistro FDI Logo" width="80"/>
        </a>
    </div>
        
    <div class="perfil">
        <?= perfil() ?>
    </div>

</header>