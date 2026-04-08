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
        $fotoLogout = RUTA_APP . '/img/logout.png';
        $rutaPerfil = RUTA_APP . '/miCuenta.php';
        $rutaLogout = RUTA_APP . '/logout.php';
        $rutaCarrito = RUTA_APP. '/carrito.php';

        $html = "<div class='perfil_carrito_salir'>
                  
            <a href='$rutaPerfil' class = 'perfil_cabecera'> 
                <img src='$rutaImg' class = 'perfil'>   $nombre 
            </a>             
                        
            <a href='$rutaCarrito' class = 'carrito_cabecera'> 
                <img src='$fotoCarrito' class = 'carrito'>
                Mi carrito 
            </a>       
            
            <a href='$rutaLogout' class = 'salir_cabecera'>
                <img src='$fotoLogout' class = 'logout'>
                Salir
            </a>

        </div>";
    } else {
        $rutaRegistro = RUTA_APP . '/registro.php';
        $rutaLogin = RUTA_APP . '/login.php';

        $html = "<div>
            <a href='$rutaLogin'>Login</a> | <a href='$rutaRegistro'>Registro</a>
        </div>";
    }
    return $html;
}
?>

<header>
    <div>
        <a href="<?= RUTA_APP ?>/index.php">
            <img src="<?= RUTA_APP ?>/img/logo_bistro.png" alt="Bistro FDI Logo" class="logo-central"/>
        </a>
    </div>    
    
    <?= perfil() ?> 

</header>