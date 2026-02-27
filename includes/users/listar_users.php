<?php
require_once dirname(__DIR__).'/config.php';

$app = Aplicacion::getInstance();

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'No tienes permisos para realizar esta acción.');
    header('Location: index.php');
    exit();
}

$conn = $app->getConexionBd();
$result = $conn->query("SELECT nombreUsuario, nombre, apellidos, rol FROM Usuarios");

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$contenidoPrincipal = "<h1>Gestión de Usuarios</h1>";

// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";

$rolesTexto = [1 => 'Cliente', 2 => 'Camarero', 3 => 'Cocinero', 4 => 'Gerente'];
$nombreRol = $rolesTexto[$fila['rol']] ?? 'Desconocido';

$contenidoPrincipal .= "<table>";
while ($fila = $result->fetch_assoc()) {
    $contenidoPrincipal .= "<tr>
        <td>{$fila['nombreUsuario']}</td>
        <td>{$nombreRol}</td>
        <td>
            <a href='actualizar_users.php?id={$fila['nombreUsuario']}'>Editar</a>
            <a href='borrar_users.php?id={$fila['nombreUsuario']}' onclick='return confirm(\"¿Seguro?\")'>Borrar</a>
        </td>
    </tr>";
}
$contenidoPrincipal .= "</table>";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';