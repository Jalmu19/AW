<?php
require_once __DIR__.'/includes/config.php';

$app = Aplicacion::getInstance();
$tituloPagina = "Gestión de Camarero";

//solo camareros, cocineros o gerentes
if (!$app->isCurrentUserLogged() || $app->isCurrentUserClient()) {
    $app->putRequestAttribute('error', 'No tienes permisos para acceder.');
    header('Location: ' . RUTA_APP . '/index.php');
    exit();
}

$contenidoPrincipal = <<<EOS
<div>
    <div>
        <a href="index.php" class="btn-volver">← Volver al Inicio</a>
    </div>

    <h1>Panel - Camarero</h1>
    <p>Seleccione la acción que desea realizar sobre los pedidos:</p>

    <div>
        
        <form action="completar_pedido.php" method="get">
            <button type="submit">
                Completar Pedidos
            </button>
        </form>

        <form action="entregar_pedido.php" method="get">
            <button type="submit">
                Entregar Pedidos
            </button>
        </form>

        <form action="cobrar_pedido.php" method="get">
            <button type="submit">
                Cobrar Cuenta
            </button>
        </form>

    </div>
</div>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';