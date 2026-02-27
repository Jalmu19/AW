<?php
function perfil() {
    $app = Aplicacion::getInstance();
    $html = '';

    if ($app->isCurrentUserLogged()) {
        $avatar = $app->getCurrentUserAvatar();
        $nombre = $app->getCurrentUserRealName();
        $rutaImg = RUTA_APP . '/img/avatares/' . $avatar;

        $html = "<div>
            Hola, $nombre 
            <a href='perfil.php'><img src='$rutaImg' width='40'></a>
            <a href='logout.php'>Salir</a>
        </div>";
    } else {
        $html = "<div>
            <a href='login.php'>Login</a> | <a href='registro.php'>Registro</a>
        </div>";
    }
    return $html;
}
?>

<header>
    <div class="menu-hamburguesa">
        <img src="<?= RUTA_IMGS ?>menu_icon.png" alt="Menú" width= "30">
    </div>

    <div class="logo-central">
        <a href="<?=__DIR__?>../../registro.php">
            <img src= "<?= RUTA_IMGS ?>logo_bistro.png" alt="Bistro FDI Logo" width= "80"/>
        </a>
    </div>
        
    <div class="perfil">
        <?= perfil() ?>
    </div>

</header>