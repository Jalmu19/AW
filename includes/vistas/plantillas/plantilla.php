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
            require(RAIZ_APP.'/comun/cabecera.php');
            require(RAIZ_APP.'/comun/sidebarIzq.php');
            ?>
            <main>
                <article>
                    <?= $contenidoPrincipal ?>
                </article>
            </main>
            <?php
            require(RAIZ_APP.'/comun/sidebarDer.php');
            require(RAIZ_APP.'/comun/pie.php');
            ?>
        </div>
    </body>
</html>