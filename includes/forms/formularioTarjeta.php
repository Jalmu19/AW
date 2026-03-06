<?php
require_once RAIZ_APP.'/includes/forms/formulario.php';
require_once RAIZ_APP.'/includes/users/Usuario.php';

class FormularioTarjeta
{
    public function __construct() {
       
    }
    
    protected function camposFormulario()
    {
        // Se genera el HTML asociado a los campos del formulario 
        $html = <<<EOF
         <fieldset>
            <legend>Pago con tarjeta bancaria</legend>
            <div>
                <form action = "" method ="post">
                    Numero de trajeta:
                    <input type="number" name="numTarjeta" value=""/>
                    Fecha de caducidad
                    <input type="text" name="FCaducidad" value="MM/YY"/>
                    CVE
                    <input type="number" name="cve" value=""/>
                    <input type="submit" value="Enviar" />

                </form>

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
        if (!$nombreUsuario || empty($nombreUsuario) ) {
            $this->errores['nombreUsuario'] = 'El nombre de usuario no puede estar vacío';
        }
        
        $password = trim($datos['password'] ?? '');
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$password || empty($password) ) {
            $this->errores['password'] = 'La contraseña no puede estar vacía.';
        }
        
        if (count($this->errores) === 0) {
            $usuario = Usuario::login($nombreUsuario, $password);
        
            if (!$usuario) {
                $this->errores[] = "El usuario o la contraseña no coinciden";
            } else {
                $app = Aplicacion::getInstance();
                $app->loginUser($usuario);
            }
        }
    }
}
