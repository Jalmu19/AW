<?php
namespace BistroFDI\categorias;
use BistroFDI\Aplicacion;

class Categoria {

    /** NUESTRAS CATEGORIAS:
     * Entrante
     * Primero
     * Segundo
     * Postre
     */

    private $nombre;
    private $descripcion;

    private function __construct($nombre, $descripcion)
    {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    public static function buscaCategoria($nombre)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Categoria WHERE nombre='%s'", 
            $conn->real_escape_string($nombre)
        );
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            $categoria = new Categoria($f['nombre'], $f['descripcion']);
            $rs->free();
            return $categoria;
        }
        return false;
    }

    // función crear
    public static function crea($nombre, $descripcion)
    {
        $categoria = new Categoria($nombre, $descripcion);
        return $categoria->guarda();
    }

    public function guarda()
    {
        // Si al buscar el nombre ya existe, actualizamos. Si no, insertamos.
        if (self::buscaCategoria($this->nombre)) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }

    private static function inserta($categoria)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO Categoria(nombre, descripcion) VALUES ('%s', '%s')",
            $conn->real_escape_string($categoria->nombre),
            $conn->real_escape_string($categoria->descripcion)
        );
        return $conn->query($query);
    }

    public static function actualiza($categoria)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Categoria SET descripcion='%s' WHERE nombre='%s'",
            $conn->real_escape_string($categoria->descripcion),
            $conn->real_escape_string($categoria->nombre)
        );
        return $conn->query($query);
    }

    public static function borra($nombre)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = "DELETE FROM Categoria WHERE nombre = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            error_log("Error en la preparación: " . $conn->error);
            return false;
        }

        $stmt->bind_param("s", $nombre);

        if ($stmt->execute()) {
            $result = true;
        }

        $stmt->close();
        return $result;
    }

    public static function listaCategorias(){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT nombre FROM Categoria");

        $rs = $conn->query($query);
        $categoria = [];
        if ($rs) {
            while($fila = $rs->fetch_assoc()){
                $categoria[] = $fila['nombre'];
            }
            $rs->free();
            return $categoria;
        }
        return false;
    }

    // getters
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }

}