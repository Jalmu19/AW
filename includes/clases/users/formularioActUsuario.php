<?php
namespace BistroFDI\clases\users;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\clases\users\Usuario;
use BistroFDI\clases\aplicacion;use BistroFDI\clases\formulario;

class formularioActUsuario extends Formulario
{
    public function __construct() {
        parent::__construct('formEditarPerfil', [
            'action' => RUTA_APP . '/editarPerfil.php', 
            'enctype' => 'multipart/form-data'
        ]);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $nombre = $datos['nombre'] ?? '';
        $apellidos = $datos['apellidos'] ?? '';
        $correo = $datos['correo'] ?? '';
        $password = $datos['password'] ?? '';
        $imagen = $datos['avatar'] ?? '';

        return <<<EOF
        <fieldset>
            <legend>Actualizar perfil</legend>
            <div>
                <label>Nombre:</label>
                <input type="text" name="nombreUsuario" value="$nombre" />
            </div>
            <div>
                <label>Apellidos:</label>
                <input type="text" name="apellidos" value="$apellidos" />
            </div>
            <div>
                <label>Correo:</label>
                <input type="email" name="correo" value="$correo" />
            </div>
            <div>
                <label>Nueva Contraseña (dejar en blanco para mantener):</label>
                <input type="password" name="password" />
            </div>
            <div class="caja-avatar">
                <label>Avatar actual:</label><br>
                <input type="radio" name="tipoAvatar" value="nada" checked> Mantener actual<br>
                <input type="radio" name="tipoAvatar" value="borrar"> Borrar y poner por defecto<br>
                <input type="radio" name="tipoAvatar" value="subida"> Subir nuevo: 
                <input type="file" name="avatarArchivo" />
            </div>
            <button type="submit" name="actualizar" class="boton-form">Guardar cambios</button>
        </fieldset>
        EOF;
    }

    protected function procesaFormulario(&$datos)
    {
        $app = Aplicacion::getInstance();
        $nombreUsuario = $datos['nombreUsuario'] ?? $app->getCurrentUserName();
        $usuarioOriginal = Usuario::buscaUsuario($nombreUsuario);

        if (!$usuarioOriginal) {
            return ["Error: No se ha encontrado el usuario."];
        }

        $nuevoNombre = trim($datos['nombre'] ?? '');
        if (!empty($nuevoNombre) && $nuevoNombre !== $usuarioOriginal->getNombre()) {
            Usuario::actualizaNombreReal($nombreUsuario, $nuevoNombre);
        }

        $nuevosApellidos = trim($datos['apellidos'] ?? '');
        if (!empty($nuevosApellidos) && $nuevosApellidos !== $usuarioOriginal->getApellidos()) {
            Usuario::actualizaApellidos($nombreUsuario, $nuevosApellidos);
        }

        $nuevoCorreo = trim($datos['correo'] ?? '');
        if (!empty($nuevoCorreo) && $nuevoCorreo !== $usuarioOriginal->getEmail()) {
            Usuario::actualizaEmail($nombreUsuario, $nuevoCorreo);
        }

        $password = trim($datos['password'] ?? '');
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            Usuario::actualizaPassword($nombreUsuario, $hash);
        }

   
        $tipo = $datos['avatar'] ?? 'nada';
        if ($tipo === 'borrar') {
            Usuario::actualizaAvatar($nombreUsuario, "avatar1.png");
        } elseif ($tipo === 'subida' && isset($_FILES['avatarArchivo']) && $_FILES['avatarArchivo']['error'] === UPLOAD_ERR_OK) {
            $nombreF = time() . "_" . $_FILES['avatarArchivo']['name'];
            if (move_uploaded_file($_FILES['avatarArchivo']['tmp_name'], RAIZ_APP . "/img/avatares/" . $nombreF)) {
                Usuario::actualizaAvatar($nombreUsuario, $nombreF);
            } else {
                $this->errores[] = "Error al subir el archivo.";
            }
        }

        if (count($this->errores) === 0) {
            $usuarioModificado = Usuario::buscaUsuario($nombreUsuario);
            $app->loginUser($usuarioModificado);
            
            $this->urlRedireccion = RUTA_APP . '/miCuenta.php';
        }
    }
}
