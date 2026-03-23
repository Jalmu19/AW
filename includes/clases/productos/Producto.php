<?php
namespace BistroFDI\clases\productos;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\clases\aplicacion;
class Producto {
    // Categorias de producto: Entrante(1), Primer plato(2), Segundo plato(3), Postre(4)
    public const ENTRANTE = 1;
    public const PRIMER_PLATO = 2;
    public const SEGUNDO_PLATO = 3;
    public const POSTRE = 4;


    private $nombre;
    private $precio;
    private $disponibilidad;
    private $iva;
    private $ofertado;
    private $descripcion;
    private $imagen;
    private $categoria;
    private $cocinable;

    private function __construct($nombre, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $imagen, $categoria, $cocinable)
    {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->disponibilidad = $disponibilidad;
        $this->iva = $iva;
        $this->ofertado = $ofertado;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
        $this->categoria = $categoria;
        $this->cocinable = $cocinable;
    }


    public static function buscaProducto($nombre)
    {
             
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Producto WHERE nombre='%s'", 
            $conn->real_escape_string($nombre)
        );
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            $producto = new Producto($f['nombre'], $f['precio'], $f['disponibilidad'], $f['iva'], $f['ofertado'], $f['descripcion'], $f['imagen'], $f['categoria'], $f['cocinable']);
            $rs->free();
            return $producto;
        }
        return false;
    }

    public static function listarProductos()
    {
             
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT nombre, precio, categoria, descripcion, imagen FROM Producto WHERE ofertado=true");
        $rs = $conn->query($query);
        $productos = [];
        if ($rs ) {
            while($fila = $rs->fetch_assoc()){
                $productos[] = $fila;
            }
            $rs->free();
            return $productos;
        }
        return false;
    }

    public static function listarPorCategoria($categoria)
    {
        $app = Aplicacion::getInstance();
        $conn = $app->getConexionBd();
        
        $query = sprintf("SELECT * FROM Producto WHERE categoria = '%s' AND disponibilidad = 1", $conn->real_escape_string($categoria));
        
        $rs = $conn->query($query);
        $result = [];
        if ($rs) {
            while ($fila = $rs->fetch_assoc()) {
                $productos[] = $fila;
            }
            $rs->free();
        }
        return $productos;
    }

    //función crear
    public static function crea($nombre, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $imagen, $categoria, $cocinable)
    {
        $producto = new Producto($nombre, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $imagen, $categoria, $cocinable);
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
        // $conn =  (BD_HOST, BD_USER, BD_PASS, BD_NAME);
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO Producto(nombre, precio, disponibilidad, iva, ofertado, descripcion, imagen, categoria, cocinable) VALUES ('%s', %f, %d, %f, %d, '%s', '%s', '%s' ,'%d')",
            $conn->real_escape_string($producto->nombre),
            $producto->precio,       
            $producto->disponibilidad, 
            $producto->iva,            
            $producto->ofertado,      
            $conn->real_escape_string($producto->descripcion),
            $conn->real_escape_string($producto->imagen),
            $conn->real_escape_string($producto->categoria),
            $producto->cocinable
            
        );
        return $conn->query($query);
    }


    public static function actualiza($producto)
    {
        
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("UPDATE Producto SET precio=%f, descripcion='%s', imagen='%s', categoria='%s', disponibilidad=%d, ofertado=%d, cocinable=%d WHERE nombre='%s'",
            $producto->precio, 
            $conn->real_escape_string($producto->descripcion),
            $conn->real_escape_string($producto->imagen),
            $conn->real_escape_string($producto->categoria),
            $producto->disponibilidad, 
            $producto->ofertado,      
            $producto->cocinable,   
            $conn->real_escape_string($producto->nombre)
        );
        error_log("UPDATE ejecutado: $query");
        return $conn->query($query);
    }



    public static function borra($nombre)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();

        $query = "UPDATE Producto SET ofertado = false WHERE nombre = ?";
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
    public function getCocinable() { return $this->cocinable; }

}

