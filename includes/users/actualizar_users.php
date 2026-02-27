<?php
require_once dirname(__DIR__).'/config.php';

//solo el gerente
if (!$app->isCurrentUserAdmin()) {
    $app->putRequestAttribute('error', 'Acceso denegado: se requieren permisos de Gerente.');
    header('Location: index.php');
    exit();
}

//coger el id del usuario de la URL
$nombreUsuario = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$usuario = Usuario::buscaUsuario($nombreUsuario);

if (!$usuario) {
    $app->putRequestAttribute('error', 'El usuario no existe.');
    header('Location: listar_users.php');
    exit();
}

//procesar el post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validamos que el rol sea un entero
    $nuevoRol = filter_input(INPUT_POST, 'rol', FILTER_VALIDATE_INT);

    if ($nuevoRol) {
        if (Usuario::cambiaRol($nombreUsuario, $nuevoRol)) {
            $app->putRequestAttribute('mensaje', "Rol de '$nombreUsuario' actualizado con éxito.");
            header('Location: listar_users.php');
            exit();
        } 
        else {
            $error = "Error al actualizar el rol en la base de datos.";
        }
    } 
    else {
        $error = "Por favor, selecciona un rol válido de la lista.";
    }
}

$tituloPagina = "Editar Rol";

// Definimos los nombres de los roles para el select
$roles = [
    Usuario::CLIENT_ROLE => 'Cliente',
    Usuario::WAITER_ROLE => 'Camarero',
    Usuario::COOK_ROLE   => 'Cocinero',
    Usuario::ADMIN_ROLE  => 'Gerente'
];

// Construimos las opciones empezando por una neutra
$options = "<option value='' disabled selected>Selecciona un nuevo rol</option>";
foreach ($roles as $val => $nombre) {
    $options .= "<option value='$val'>$nombre</option>";
}

$contenidoPrincipal = <<<EOS
<h1>Modificar Rol del Usuario: $nombreUsuario</h1>
<form method="POST">
    <fieldset>
        <div>
            <label for="rol">Asignar nuevo rol:</label>
            <select name="rol" id="rol" required>
                $options
            </select>
        </div>
        <br>
        <button type="submit">Guardar Cambios</button>
        <a href="listar_users.php">Cancelar</a>
    </fieldset>
</form>
EOS;

require RAIZ_APP . '/includes/vistas/plantillas/plantilla.php';