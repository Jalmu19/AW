<?php
function perfil() {
    $rutaApp = RAIZ_APP;
    $html = '';

    //Si el usuario ha iniciado sesión
    if (isset($_SESSION["login"]) && ($_SESSION["login"] === true)) {
        //Determinar la imagen (si no tiene, una por defecto)
        $avatar = $_SESSION["avatar"] ?? "user_icon.png";
        $imagenAvatar = RUTA_IMGS."/avatares/{$avatar}"; //???

        $html = <<<EOS
            <div>
                <a href="<?= $rutaApp ?>/carrito.php">
                    <img src="<?= $rutaApp ?>/img/cart_icon.png" alt="Carrito" width= "30">
                </a>

                <a href = "{$rutaApp}/perfil.php">
                    <img src="{$imagenAvatar}" alt="Avatar" width="40" height="40">
                </a>
            </div>
        EOS;
    } 
    //Si no ha iniciado sesión
    else {
        $html = <<<EOS
            <div>
                <a href="{$rutaApp}/login.php">Iniciar sesión</a> | <a href="{$rutaApp}/registro.php">Registrarse</a>
            </div>
        EOS;
    }

    return $html;
}
?>

<header>
    <div class="menu-hamburguesa">
        <img src="<?= $rutaApp ?>/img/menu_icon.png" alt="Menú" width= "30">
    </div>

    <div class="logo-central">
        <a href="<?= $rutaApp ?>/index.php">
            <img src="<?= $rutaApp ?>/img/logo_bistro.png" alt="Bistro FDI Logo" width= "80">
        </a>
    </div>
        
    <div class="perfil">
        <?= perfil() ?>
    </div>

</header>