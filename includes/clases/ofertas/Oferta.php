<?php
namespace BistroFDI\clases\ofertas;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\Aplicacion;

class Oferta {

    private $id_oferta;
    private $nombre;
    private $descripcion;
    private $fecha_ini;
    private $fecha_fin;
    private $descuento;
    private $productos_pack; // diccionario con ['nombre_producto' => cantidad]

    private function __construct($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $id_oferta, $productos_pack)
    {
        $this->id_oferta = $id_oferta;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
        $this->descuento = $descuento;
        $this->productos_pack = $productos_pack;
    }

    public static function buscaOferta($id_oferta)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Oferta WHERE id_oferta=%d", $id_oferta);
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows === 1) {
            $f = $rs->fetch_assoc();
            $productos = self::buscaProductosOferta($id_oferta);
            $oferta = new Oferta($f['nombre'], $f['descripcion'], $f['fecha_ini'], $f['fecha_fin'], $f['descuento'], $f['id_oferta'], $productos);
            $rs->free();
            return $oferta;
        }
        return false;
    }

    private static function buscaProductosOferta($id_oferta)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT nombre_producto, cantidad FROM Oferta_Producto WHERE id_oferta=%d", $id_oferta);
        $rs = $conn->query($query);
        $productos = [];
        if ($rs) {
            while ($fila = $rs->fetch_assoc()) {
                $productos[$fila['nombre_producto']] = $fila['cantidad'];
            }
            $rs->free();
        }
        return $productos;
    }
    
    public static function borra($id_oferta)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        //borramos primero la relación por integridad
        $queryRelacion = sprintf("DELETE FROM Oferta_Producto WHERE id_oferta=%d", $id_oferta);
        $conn->query($queryRelacion);

        $query = sprintf("DELETE FROM Oferta WHERE id_oferta=%d", $id_oferta);
        return $conn->query($query);
    }

    public static function listarOfertas($soloActivas = false)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        //usamos GROUP_CONCAT para traer todos los productos y cantidades en una sola celda
        //tablas-> Oferta (o), Oferta_Producto (op)
        $query = "SELECT o.*, 
                  GROUP_CONCAT(CONCAT(op.nombre_producto, 'x', op.cantidad) SEPARATOR '<br>') as productos_pack
                  FROM Oferta o
                  LEFT JOIN Oferta_Producto op ON o.id_oferta = op.id_oferta";
        
        //para mostrar las que están activas al cliente
        if ($soloActivas) {
            $fechaActual = date('Y-m-d H:i:s');
            $query .= sprintf(" WHERE o.fecha_ini <= '%s' AND o.fecha_fin >= '%s'", 
                $conn->real_escape_string($fechaActual),
                $conn->real_escape_string($fechaActual)
            );
        }

        $query .= " GROUP BY o.id_oferta ORDER BY o.fecha_ini DESC";

        $rs = $conn->query($query);
        $ofertas = [];
        
        if ($rs) {
            while ($fila = $rs->fetch_assoc()) {
                $ofertas[] = $fila;
            }
            $rs->free();
            return $ofertas;
        }
        
        return false;
    }

    public static function crea($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $productos_pack, $id_oferta)
    {
        $oferta = new Oferta($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $id_oferta, $productos_pack);
        return $oferta->guarda();
    }

   public function guarda()
    {
        // Si el id no es nulo, es que viene de un formulario de edición -> ACTUALIZAMOS
        if ($this->id_oferta !== null && $this->id_oferta !== '') {
            return self::actualiza($this);
        }

        return self::inserta($this);
    }

    private static function inserta($oferta)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $query = sprintf("INSERT INTO Oferta (nombre, descripcion, fecha_ini, fecha_fin, descuento) VALUES ('%s', '%s', '%s', '%s', %f)",
            $conn->real_escape_string($oferta->nombre),
            $conn->real_escape_string($oferta->descripcion),
            $conn->real_escape_string($oferta->fecha_ini),
            $conn->real_escape_string($oferta->fecha_fin),
            $oferta->descuento
        );

        if ($conn->query($query)) {

            $oferta->id_oferta = $conn->insert_id;
            return self::insertaProductosPack($oferta->id_oferta, $oferta->productos_pack);
        }
        return false;
    }

    private static function actualiza($oferta)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $query = sprintf("UPDATE Oferta SET nombre='%s', descripcion='%s', fecha_ini='%s', fecha_fin='%s', descuento=%f WHERE id_oferta=%d",
            $conn->real_escape_string($oferta->nombre),
            $conn->real_escape_string($oferta->descripcion),
            $conn->real_escape_string($oferta->fecha_ini),
            $conn->real_escape_string($oferta->fecha_fin),
            $oferta->descuento,
            $oferta->id_oferta
        );

        if ($conn->query($query)) {
            // Borramos relación antigua y metemos la nueva
            $queryBorrar = sprintf("DELETE FROM Oferta_Producto WHERE id_oferta=%d", $oferta->id_oferta);
            $conn->query($queryBorrar);
            
            return self::insertaProductosPack($oferta->id_oferta, $oferta->productos_pack);
        }
        return false;
    }

    private static function insertaProductosPack($id_oferta, $productos)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        foreach ($productos as $nombreProd => $cantidad) {
            $query = sprintf("INSERT INTO Oferta_Producto (id_oferta, nombre_producto, cantidad) VALUES (%d, '%s', %d)",
                $id_oferta,
                $conn->real_escape_string($nombreProd),
                $cantidad
            );
            if (!$conn->query($query)) {
                return false;
            }
        }
        return true;
    }


    // Getters
    public function getIdOferta() { return $this->id_oferta; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFechaIni() { return $this->fecha_ini; }
    public function getFechaFin() { return $this->fecha_fin; }
    public function getDescuento() { return $this->descuento; }
    public function getProductosPack() { return $this->productos_pack; }
}