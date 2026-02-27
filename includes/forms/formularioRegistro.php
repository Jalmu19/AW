<?php
require_once RAIZ_APP.'/includes/forms/formulario.php';
require_once RAIZ_APP.'/includes/users/Usuario.php';

class FormularioRegistro extends Formulario
{
    public function __construct() {
        parent::__construct('formRegistro', [
            'urlRedireccion' => 'index.php', 
            'enctype' => 'multipart/form-data'
        ]);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $nombreUsuario = $datos['nombreUsuario'] ?? '';
        $nombre = $datos['nombre'] ?? '';
        $apellido = $datos['apellido'] ?? '';
        $email = $datos['email'] ?? '';

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'apellido', 'email', 'avatar', 'nombreUsuario', 'password', 'password2'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Datos para el registro</legend>
            <div>
                <label for="nombre">Nombre:</label>
                <input id="nombre" type="text" name="nombre" value="$nombre" required>
                {$erroresCampos['nombre']}
            </div>
            <div>
                <label for="apellido">Apellidos:</label>
                <input id="apellido" type="text" name="apellido" value="$apellido" required>
                {$erroresCampos['apellido']}
            </div>
            <div>
                <label for="email">Email:</label>
                <input id="email" type="email" name="email" value="$email" required>
                {$erroresCampos['email']}
            </div>
            <div>
                <label for="nombreUsuario">Nombre de usuario:</label>
                <input id="nombreUsuario" type="text" name="nombreUsuario" value="$nombreUsuario" required>
                {$erroresCampos['nombreUsuario']}
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input id="password" type="password" name="password" required>
                {$erroresCampos['password']}
            </div>
            <div>
                <label for="password2">Reintroduce la contraseña:</label>
                <input id="password2" type="password" name="password2" required>
                {$erroresCampos['password2']}
            </div>
            <div>
                <input type="radio" name="tipoAvatar" value="defecto" checked> 
                Usar avatar por defecto
            </div>
            <div>
                <input type="radio" name="tipoAvatar" value="galeria"> 
                Elegir de la galería:
                <select name="avatarGaleria">
                    <option value="opcion1.png">Opcion1</option>
                    <option value="opcion2.png">Opcion2</option>
                    <option value="opcion3.png">Opcion3</option>
                </select>
            </div>
            <div>
                <input type="radio" name="tipoAvatar" value="subida"> 
                Subir mi propia foto:
                <input type="file" name="avatarArchivo" accept="image/*">
            </div>
            <div>
                <button type="submit" name="registro">Registrar</button>
            </div>
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </fieldset>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        // Validación de datos básicos
        $nombreUsuario = trim($datos['nombreUsuario'] ?? '');
        $nombreUsuario = filter_var($nombreUsuario, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$nombreUsuario || mb_strlen($nombreUsuario) < 5) {
            $this->errores['nombreUsuario'] = 'El nombre de usuario debe tener al menos 5 caracteres.';
        }

        $nombre = trim($datos['nombre'] ?? '');
        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$nombre || mb_strlen($nombre) < 3) {
            $this->errores['nombre'] = 'El nombre debe tener al menos 3 caracteres.';
        }

        $apellido = trim($datos['apellido'] ?? '');
        $apellido = filter_var($apellido, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$apellido || mb_strlen($apellido) < 3) {
            $this->errores['apellido'] = 'El apellido debe tener al menos 3 caracteres.';
        }

        $email = trim($datos['email'] ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errores['email'] = 'El email no es válido.';
        }

        $password = trim($datos['password'] ?? '');
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (mb_strlen($password) < 5) {
            $this->errores['password'] = 'La contraseña debe tener al menos 5 caracteres.';
        }

        $password2 = trim($datos['password2'] ?? '');
        $password2 = filter_var($password2, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($password !== $password2) {
            $this->errores['password2'] = 'Las contraseñas deben coincidir.';
        }

        // Lógica del Avatar
        $avatar = "user_icon.png"; //Por defecto
        $tipo = $datos['tipoAvatar'] ?? 'defecto';

        if($tipo === 'galeria') {
            $avatar = $datos['avatarGaleria'];
        } 
        elseif($tipo === 'subida') {
            if(isset($_FILES['avatarArchivo']) && $_FILES['avatarArchivo']['error'] === UPLOAD_ERR_OK) {
                $archivoTmp = $_FILES['avatarArchivo']['tmp_name'];
                //time() para garantizar nombres únicos, evita que un usuario sobrescriba la foto de otro
                $nombreArchivo = time() . "_" . $_FILES['avatarArchivo']['name'];
                
                if(move_uploaded_file($archivoTmp, RAIZ_APP . "/img/avatares/" . $nombreArchivo)) {
                    $avatar = $nombreArchivo;
                }
                else {
                    $this->errores['avatar'] = "Error al subir la imagen.";
                }
            }
        }

        if (count($this->errores) === 0) {
            $usuario = Usuario::buscaUsuario($nombreUsuario);
    
            if ($usuario) {
                $this->errores[] = "El usuario ya existe";
            } else {
                
                $usuario = Usuario::crea($nombreUsuario, $nombre, $apellido, $email, $password, $avatar, Usuario::CLIENT_ROLE);
                
                if ($usuario) {
                    $app = Aplicacion::getInstance();
                    $app->loginUser($usuario);
                } else {
                    $this->errores[] = "Error al crear el usuario.";
                }
            }
        }
    }
} 