<?php
require_once RAIZ_APP.'/includes/forms/formulario.php';
require_once RAIZ_APP.'/includes/users/Usuario.php';

class formularioActUsuario extends Formulario
{
    public function __construct() {
        parent::__construct('formLogin', ['urlRedireccion' => 'index.php']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        // Se reutiliza el nombre de usuario introducido previamente o se deja en blanco
        $nombreUsuario = $datos['nombreUsuario'] ?? '';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombreUsuario', 'password', 'correo'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Actualizar usuario</legend>
            <div>
                <label for="nombreUsuario">Nombre de usuario:</label>
                <input id="nombreUsuario" type="text" name="nombreUsuario" value="$nombreUsuario" />
                {$erroresCampos['nombreUsuario']}
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input id="password" type="password" name="password" />
                {$erroresCampos['password']}
            </div>
             <div>
                <label for="correo">Correo electronico:</label>
                <input id="correo" type="mail" name="correo" />
                {$erroresCampos['correo']}
            </div>
             <div>
                <label for="nombre">Nombre:</label>
                <input id="nombre" type="text" name="nombre" />
            </div>
             <div>
                <label for="apellidos">Apellidos:</label>
                <input id="apellidos" type="text" name="apellidos" />
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
                <button type="submit" name="actualizar">Ok</button>
            </div>

        </fieldset>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        $nombreUsuario = trim($datos['nombreUsuario'] ?? '');
        $nombreUsuario = filter_var($nombreUsuario, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombreUsuario || empty($nombreUsuario) ) {
            $this->errores['nombreUsuario'] = 'El nombre de usuario no puede estar vacío';
        }
        
        $password = trim($datos['password'] ?? '');
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $password || empty($password) ) {
            $this->errores['password'] = 'La contraseña no puede estar vacía.';
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $correo = trim($datos['correo'] ?? '');
        $correo = filter_var($correo, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $correo || empty($correo) ) {
            $this->errores['correo'] = 'El correo no puede estar vacío.';
        }

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
            $aux = new Usuario($nombreUsuario, $correo, $datos['nombre'], $datos['$apellidos'], $hash, $datos['rol'], $avatar);
            $usuario = Usuario::actualiza($aux);
        
            if (!$usuario) {
                $this->errores[] = "El usuario o la contraseña no coinciden";
            } else {
                $app = Aplicacion::getInstance();
                $app->loginUser($usuario);
            }
        }
    }
}
