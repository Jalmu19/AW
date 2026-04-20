<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $tituloPagina ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="<?= RUTA_APP ?>/css/estilo.css" />
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    </head>
    <body>
        <div id="contenedor">
            <?php
            require(RAIZ_APP.'/includes/vistas/comun/cabecera.php');
           # require(RAIZ_APP.'/includes/vistas/comun/sidebarIzq.php');
            ?>
            <main>
                <article>
                    <?= $contenidoPrincipal ?>
                </article>
            </main>
            <?php
            #require(RAIZ_APP.'/includes/vistas/comun/sidebarDer.php');
            require(RAIZ_APP.'/includes/vistas/comun/pie.php');
            ?>
        </div>
        <script type="text/javascript" src="<?= RUTA_APP ?>/js/main.js"></script>
    </body>
</html>

