<?php

/**
 * Clase que mantiene el estado global de la aplicación.
 */
class Aplicacion
{
    // Constantes
    /** 
     * @var string Clave para almacenar los atributos de la petición en la sesión
     */
    const REQUEST_ATTRIBUTES = 'requestAtts';

    // Patrón Singleton
    /**
     * @var Aplicacion Instancia de la aplicación
     */
    private static $instancia;
    
	/**
	 * Devuele una instancia de {@see Aplicacion}.
	 * 
	 * @return Applicacion Obtiene la única instancia de la <code>Aplicacion</code>
	 */    
    public static function getInstance() 
    {
        if (!self::$instancia instanceof self ) 
        {
            self::$instancia = new static();
        }
        
        return self::$instancia;
    }

	/**
	 * @var array Almacena los datos de configuración de la BD
	 */
	private $bdDatosConexion;
	
	/**
	 * Almacena si la Aplicacion ya ha sido inicializada.
	 * 
	 * @var boolean
	 */
	private $inicializada = false;
	
	/**
	 * @var \mysqli Conexión de BD.
	 */
	private $conn;
	
	/**
	 * Evita que se pueda instanciar la clase directamente.
	 */
	private function __construct()
	{
	}
	
    
	/**
	 * @var array Tabla asociativa con los atributos pendientes de la petición. Es decir, almacena los atributos de la petición
	 */    
    private $requestAttributes;
    
	/**
	 * Inicializa la aplicación.
     *
     * Opciones de conexión a la BD:
     * <table>
     *   <thead>
     *     <tr>
     *       <th>Opción</th>
     *       <th>Descripción</th>
     *     </tr>
     *   </thead>
     *   <tbody>
     *     <tr>
     *       <td>host</td>
     *       <td>IP / dominio donde se encuentra el servidor de BD.</td>
     *     </tr>
     *     <tr>
     *       <td>bd</td>
     *       <td>Nombre de la BD que queremos utilizar.</td>
     *     </tr>
     *     <tr>
     *       <td>user</td>
     *       <td>Nombre de usuario con el que nos conectamos a la BD.</td>
     *     </tr>
     *     <tr>
     *       <td>pass</td>
     *       <td>Contraseña para el usuario de la BD.</td>
     *     </tr>
     *   </tbody>
     * </table>
	 * 
	 * @param array $dbConnectionData datos de configuración de la BD
	 */
    public function init($bdDatosConexion)
    {
        if ( ! $this->inicializada ) {
    	    $this->bdDatosConexion = $bdDatosConexion;
    		$this->inicializada = true;
    		session_start();
        

			/** 
            * Se inicializa los atributos asociados a la petición en base a la sesión y se eliminan para que
			* no estén disponibles después de la gestión de esta petición.
			*/
            $this->requestAttributes = $_SESSION[self::REQUEST_ATTRIBUTES] ?? [];
            unset($_SESSION[self::REQUEST_ATTRIBUTES]);
        }
    }
    
	/**
	 * Cierre de la aplicación (se cierra la conexión a la BD).
	 */
	public function shutdown()
	{
	    $this->compruebaInstanciaInicializada();
	    if ($this->conn !== null && ! $this->conn->connect_errno) {
	        $this->conn->close();
	    }
	}
    
	/**
	 * Comprueba si la aplicación está inicializada. Si no lo está muestra un mensaje y termina la ejecución.
	 */
	private function compruebaInstanciaInicializada()
	{
	    if (! $this->inicializada ) {
	        echo "Aplicacion no inicializa";
	        exit();
	    }
	}
    
	/**
	 * Devuelve una conexión a la BD. Se encarga de que exista como mucho una conexión a la BD por petición.
	 * 
	 * @return \mysqli Conexión a MySQL.
	 */
    public function getConexionBd()
	{
	    $this->compruebaInstanciaInicializada();
		if (! $this->conn ) {
			$bdHost = $this->bdDatosConexion['host'];
			$bdUser = $this->bdDatosConexion['user'];
			$bdPass = $this->bdDatosConexion['pass'];
			$bd = $this->bdDatosConexion['bd'];
			
			$conn = new mysqli($bdHost, $bdUser, $bdPass, $bd);
			if ( $conn->connect_errno ) {
				echo "Error de conexión a la BD ({$conn->connect_errno}):  {$conn->connect_error}";
				exit();
			}
			if ( ! $conn->set_charset("utf8mb4")) {
				echo "Error al configurar la BD ({$conn->errno}):  {$conn->error}";
				exit();
			}
			$this->conn = $conn;
		}
		return $this->conn;
	}

	/**
	 * Añade un atributo <code>$value</code> para que esté disponible en la siguiente petición bajo la clave <code>$key</code>.
	 * 
	 * @param string $key Clave bajo la que almacenar el atributo.
	 * @param any    $value Valor a almacenar como atributo de la petición.
	 * 
	 */
    public function putRequestAttribute($key, $value)
    {
        $atts = null;
        
        if (isset($_SESSION[self::REQUEST_ATTRIBUTES])) 
        {
            $atts = &$_SESSION[self::REQUEST_ATTRIBUTES];
        } 
        else 
        {
            $atts = array();
            $_SESSION[self::REQUEST_ATTRIBUTES] = &$atts;
        }

        $atts[$key] = $value;
    }

	/**
	 * Devuelve un atributo establecido en la petición actual o en la petición justamente anterior.
	 * 
	 * @param string $key Clave sobre la que buscar el atributo.
	 * 
	 * @return any Atributo asociado a la sesión bajo la clave <code>$key</code> o <code>null</code> si no existe.
	 */
    public function getRequestAttribute($key)
    {
        $result = $this->requestAttributes[$key] ?? null;
        
        if(is_null($result) && isset($_SESSION[self::REQUEST_ATTRIBUTES])) 
        {
            $result = $_SESSION[self::REQUEST_ATTRIBUTES][$key] ?? null;
        }
        
        return $result;
    }

    /**
    * funciones login y logout
    **/
    public function loginUser($usuario)
    {
        $_SESSION['login'] = true;
        $_SESSION['nombreUsuario'] = $usuario->getNombreUsuario();
        $_SESSION['nombre'] = $usuario->getNombre();
        $_SESSION['rol'] = $usuario->getRol();
        $_SESSION['avatar'] = $usuario->getAvatar();
    }

    public function logoutUser()
    {
        unset($_SESSION['login']);
        unset($_SESSION['nombreUsuario']);
        unset($_SESSION['nombre']);
        unset($_SESSION['rol']);
        unset($_SESSION['avatar']);
        session_destroy();
    }

    /**
     * Verifica si el usuario actual está logueado.
     * 
     * @return bool Verdadero si el usuario está logueado, falso en caso contrario.
     */
    public function isCurrentUserLogged()
    {
        return isset($_SESSION['login']);
    }
    
    /**
     * Verifica el rol del usuario actual.
     * 
     * @return bool Verdadero si el usuario es x, falso en caso contrario.
     */
    public function isCurrentUserAdmin()
    {
       return $this->isCurrentUserLogged() && $_SESSION['rol'] == Usuario::ADMIN_ROLE;
    }

    public function isCurrentUserClient()
    {
        return $this->isCurrentUserLogged() && $_SESSION['rol'] == Usuario::CLIENT_ROLE;
    }

    public function isCurrentUserWaiter()
    {
        return $this->isCurrentUserLogged() && $_SESSION['rol'] == Usuario::WAITER_ROLE;
    }

    public function isCurrentUserCook()
    {
        return $this->isCurrentUserLogged() && $_SESSION['rol'] == Usuario::COOK_ROLE;
    }    

    /**
     * getters
     *
     */
    public function getCurrentUserName() {
        return $_SESSION['nombreUsuario'] ?? null;
    }

    public function getCurrentUserRealName() {
        return $_SESSION['nombre'] ?? null;
    }

    public function getCurrentUserRole() {
        return $_SESSION['rol'] ?? null;
    }

    public function getCurrentUserAvatar() {
        return $_SESSION['avatar'] ?? 'user_icon.png';
    }
}