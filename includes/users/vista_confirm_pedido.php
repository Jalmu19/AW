<?php

//TODO: Como obtener el id y el estado de un pedido concreto
require_once __DIR__.'/includes/config.php';
//require_once RAIZ_APP.'/includes/forms/formularioTarjeta.php';

$tituloPagina = 'Confirmacion del pedido';

$contenidoPrincipal = <<<EOS
<h1>Pedido confirmado</h1>
<p>Identificador: <?= $identificador?> </p>
<p>Estado: <?=$estado?></p>
<a href= "<?=RAIZ_APP.'/index.php'?>">
    <button type='button'>Volver al inicio</button>
</a>
EOS;

require RAIZ_APP.'/includes/vistas/plantillas/plantilla.php';
