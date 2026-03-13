<?php
namespace BistroFDI\users;
use BistroFDI\Aplicacion;

class Usuario {

    // Roles jerárquicos
    public const CLIENT_ROLE = 1;
    public const WAITER_ROLE = 2;
    public const COOK_ROLE = 3;
    public const ADMIN_ROLE = 4;

    private $nombreUsuario;
    private $email;
    private $nombre;
    private $apellidos;
    private $password;
    private $rol;
    private $avatar;

    // CONSTRUCTOR PRIVADO (Mantenido como pediste)
    private function __construct($nombreUsuario, $email, $nombre, $apellidos, $hash, $rol, $avatar)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->email = $email;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->password = $hash;
        $this->rol = $rol;
        $this->avatar = $avatar;
    }

    public static function login($nombreUsuario, $password)
    {
        $usuario = self::buscaUsuario($nombreUsuario);
        // Usamos el getter getPassword() para la verificación
        if ($usuario && password_verify($password, $usuario->getPassword())) {
            return $usuario;
        }
        return false;
    }

    public static function buscaUsuario($nombreUsuario)
    {
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

    public static function crea($nombreUsuario, $nombre, $apellidos, $email, $password, $avatar, $rol = self::CLIENT_ROLE)
    {
        // El orden coincide con el constructor: user, email, nombre, apellidos...
        $user = new Usuario($nombreUsuario, $email, $nombre, $apellidos, password_hash($password, PASSWORD_DEFAULT), $rol, $avatar);
        return $user->guarda();
    }

    public function guarda()
    {
        if (self::buscaUsuario($this->nombreUsuario)) {
            return self::actualiza($this->nombreUsuario, $this->email, $this->nombre, $this->apellidos, $this->password, $this->avatar);
        }
        return self::inserta($this);
    }

    private static function inserta($usuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO Usuarios(nombreUsuario, email, nombre, apellidos, password, rol, avatar) VALUES ('%s', '%s', '%s', '%s', '%s', %d, '%s')",
            $conn->real_escape_string($usuario->getNombreUsuario()),
            $conn->real_escape_string($usuario->getEmail()),
            $conn->real_escape_string($usuario->getNombre()),
            $conn->real_escape_string($usuario->getApellidos()),
            $usuario->getPassword(),
            $usuario->getRol(),
            $conn->real_escape_string($usuario->getAvatar())
        );
        return $conn->query($query);
    }

    public static function borra($nombreUsuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "DELETE FROM Usuarios WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public static function getTodosUsuarios() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT avatar, nombreUsuario, nombre, apellidos, rol FROM Usuarios";
        $rs = $conn->query($query);
        $usuarios = [];
        if ($rs) {
            while ($fila = $rs->fetch_assoc()) {
                $usuarios[] = $fila;
            }
            $rs->free();
        }
        return $usuarios;
    }

    //MÉTODOS PARA ACTUALIZAR USUARIO
    public static function actualizaEmail($nombreUsuario, $email) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "UPDATE Usuarios SET email = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $email, $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public static function actualizaNombreReal($nombreUsuario, $nombre) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "UPDATE Usuarios SET nombre = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $nombre, $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public static function actualizaApellidos($nombreUsuario, $apellidos) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "UPDATE Usuarios SET apellidos = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $apellidos, $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }


    public static function actualizaPassword($nombreUsuario, $hash) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "UPDATE Usuarios SET password = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $hash, $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public static function actualizaAvatar($nombreUsuario, $avatar) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "UPDATE Usuarios SET avatar = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $avatar, $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public static function cambiaRol($nombreUsuario, $nuevoRol)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "UPDATE Usuarios SET rol = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("is", $nuevoRol, $nombreUsuario);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // GETTERS (Todos necesarios)
    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function getEmail() { return $this->email; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getPassword() { return $this->password; }
    public function getRol() { return $this->rol; }
    public function getAvatar() { return $this->avatar; }
}