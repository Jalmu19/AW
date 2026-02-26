<?php

require_once (__DIR__. '/../config.php');

class Usuario {

    // Roles jerárquicos: Cliente(1) < Camarero(2) < Cocinero(3) < Gerente(4)
    public const CLIENT_ROLE = 1;
    public const WAITER_ROLE = 2;
    public const COOK_ROLE   = 3;
    public const ADMIN_ROLE  = 4;

    private $nombreUsuario;
    private $email;
    private $nombre;
    private $apellidos;
    private $password;
    private $rol;
    private $avatar;

    private function __construct($nombreUsuario, $email, $nombre, $apellidos, $hash, $rol, $avatar)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->email = $email;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->password = $hash;
        $this->rol = (int)$rol;
        $this->avatar = $avatar;
    }

    //función para login
    public static function login($nombreUsuario, $password)
    {
        $usuario = self::buscaUsuario($nombreUsuario);
        // Verificamos si el usuario existe y si la contraseña (en claro) coincide con el hash
        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario;
        }
        return false;
    }

    public static function buscaUsuario($nombreUsuario)
    {
       // $conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);       
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Usuarios WHERE nombreUsuario='%s'", 
            $conn->real_escape_string($nombreUsuario)
        );
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            $user = new Usuario($f['nombreUsuario'], $f['email'], $f['nombre'], $f['apellidos'], $f['password'], $f['rol'], $f['avatar']);
            $rs->free();
            return $user;
        }
        return false;
    }

    //función registrarse
    public static function crea($nombreUsuario, $nombre, $apellidos, $email, $password, $avatar, $rol = self::CLIENT_ROLE)
    {
        $user = new Usuario($nombreUsuario, $email, $nombre, $apellidos, password_hash($password, PASSWORD_DEFAULT), $rol, $avatar);
        return $user->guarda();
    }

    public function guarda()
    {
        // Si al buscar el nombre ya existe, actualizamos. Si no, insertamos.
        if (self::buscaUsuario($this->nombreUsuario)) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }

    private static function inserta($usuario)
    {
        // $conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO Usuarios(nombreUsuario, email, nombre, apellidos, password, rol, avatar) VALUES ('%s', '%s', '%s', '%s', '%s', %d, '%s')",
            $conn->real_escape_string($usuario->nombreUsuario),
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->nombre),
            $conn->real_escape_string($usuario->apellidos),
            $usuario->password,
            $usuario->rol,
            $conn->real_escape_string($usuario->avatar)
        );
        return $conn->query($query);
    }

    public static function actualiza($usuario)
    {
        // $conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Usuarios SET email='%s', nombre='%s', apellidos='%s', password='%s', rol=%d, avatar='%s' WHERE nombreUsuario='%s'",
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->nombre),
            $conn->real_escape_string($usuario->apellidos),
            $usuario->password,
            $usuario->rol,
            $conn->real_escape_string($usuario->avatar),
            $conn->real_escape_string($usuario->nombreUsuario)
        );
        return $conn->query($query);
    }

    //getters
    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function getNombre() { return $this->nombre; }
    public function getRol() { return $this->rol; }
    public function getAvatar() { return $this->avatar; }

}