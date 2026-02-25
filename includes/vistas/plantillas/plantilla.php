<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $tituloPagina ?></title>
        <link rel="stylesheet" type="text/css" href="CSS/estilo.css" />
    </head>
    <body>
        <div id="contenedor">
            <?php
            require(RAIZ_APP.'/includes/vistas/comun/cabecera.php');
            require(RAIZ_APP.'/includes/vistas/comun/sidebarIzq.php');
            ?>
            <main>
                <article>
                    <?= $contenidoPrincipal ?>
                </article>
            </main>
            <?php
            require(RAIZ_APP.'/includes/vistas/comun/sidebarDer.php');
            require(RAIZ_APP.'/includes/vistas/comun/pie.php');
            ?>
        </div>
    </body>
</html>