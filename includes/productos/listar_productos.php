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
$result = $conn->query("SELECT nombre, precio, descripcion, imagen, categoria FROM Producto");

$msg = $app->getRequestAttribute('mensaje');
$err = $app->getRequestAttribute('error');

$contenidoPrincipal = "<h1>Gestión de Productos</h1>";

// Concatenamos los mensajes a la variable principal para que salgan en el cuerpo de la página
if ($msg) $contenidoPrincipal .= "<div class='alerta-exito'>$msg</div>";
if ($err) $contenidoPrincipal .= "<div class='alerta-error'>$err</div>";


 $contenidoPrincipal .= "<img src="$fila['imagen']" alt="foto_producto">" 
$contenidoPrincipal .= "<table>";
while ($fila = $result->fetch_assoc()) {
    $contenidoPrincipal .= "<tr>
        <td>{$fila['nombre']}</td>
        <td>{$fila['precio']}</td>
        <td>{$fila['descripcion']}</td>
        <td>{$fila['imagen']}</td>
        <td>{$fila['categoria']}</td>
        <td>
            <a href='actualizar_producto.php?id={$fila['nombre']}'>Editar</a>
            <a href='borrar_producto.php?id={$fila['nombre']}' onclick='return confirm(\"¿Seguro?\")'>Borrar</a>
        </td>
    </tr>";
}
$contenidoPrincipal .= "</table>";

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';