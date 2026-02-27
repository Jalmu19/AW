<?php

class Producto {
    private $nombre;
    private $precio;
    private $disponibilidad;
    private $iva;
    private $ofertado;
    private $descripcion;
    private $imagen;
    private $categoria;

    private function __construct($nombre, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $imagen, $categoria)
    {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->disponibilidad = $disponibilidad;
        $this->iva = $iva;
        $this->ofertado = $ofertado;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
        $this->categoria = $categoria;
    }


    public static function buscaProducto($nombreUsuario)
    {
       // $conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);       
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Producto WHERE nombre='%s'", 
            $conn->real_escape_string($nombre)
        );
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            $producto = new Producto($f['nombre'], $f['precio'], $f['disponibilidad'], $f['iva'], $f['ofertado'], $f['descripcion'], $f['imagen'], $f['categoria']);
            $rs->free();
            return $producto;
        }
        return false;
    }

    //función crear
    public static function crea($nombre, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $imagen, $categoria)
    {
        $producto = new Producto($nombre, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $imagen, $categoria);
        return $producto->guarda();
    }

    public function guarda()
    {
        // Si al buscar el nombre ya existe, actualizamos. Si no, insertamos.
        if (self::buscaProducto($this->nombre)) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }

    private static function inserta($producto)
    {
        // $conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO Producto(nombre, precio, disponibilidad, iva, ofertado, descripcion, imagen, categoria) VALUES ('%s', %f, %s, %f, %s, '%s', '%s', '%s')",
            $conn->real_escape_string($producto->nombre),
            $conn->real_escape_string($producto->precio),
            $conn->real_escape_string($producto->disponibilidad),
            $conn->real_escape_string($producto->iva),
            $conn->real_escape_string($producto->ofertado),
            $conn->real_escape_string($producto->descripcion),
            $conn->real_escape_string($producto->imagen),
            $conn->real_escape_string($producto->categoria)
        );
        return $conn->query($query);
    }

    public static function actualiza($producto)
    {
        // $conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Producto SET precio=%f, disponibilidad=%s, iva=%f, ofertado=%s, descripcion='%s', imagen='%s', categoria='%s' WHERE nombre='%s'",
            $conn->real_escape_string($producto->precio),
            $conn->real_escape_string($producto->disponibilidad),
            $conn->real_escape_string($producto->iva),
            $conn->real_escape_string($producto->ofertado),
            $conn->real_escape_string($producto->descripcion),
            $conn->real_escape_string($producto->imagen),
            $conn->real_escape_string($producto->categoria),
            $conn->real_escape_string($producto->nombre),
        );
        return $conn->query($query);
    }

    public static function borra($nombre)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = "DELETE FROM Producto WHERE nombre = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            error_log("Error en la preparación: " . $conn->error);
            return false;
        }

        $stmt->bind_param("s", $nombre); //para que sepa que es un string

        if ($stmt->execute()) {
            $result = true;
        }

        $stmt->close();
        return $result;
    }


    //getters
    public function getNombre() { return $this->nombre; }
    public function getPrecio() { return $this->precio; }
    public function getDisponibilidad() { return $this->disponibilidad; }
    public function getIva() { return $this->iva; }
    public function getOfertado() { return $this->ofertado; }
    public function getDescripcion() { return $this->descripcion; }
    public function getImagen() { return $this->imagen; }
    public function getCategoria() { return $this->categoria; }

}

