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

    public static function crea($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $productos_pack)
    {
        //obtener el siguiente id disponible
        $queryUltimoID = sprintf("SELECT MAX(id_oferta) as ultimo FROM Oferta");
        $resQueryUltimoID = $conn->query($queryUltimoID);
        $filaMax = $resQueryUltimoID->fetch_assoc();
        
        // Si no hay ofertas (null)->empezamos en 1
        // Si hay -> +1
        $id_oferta = ($filaMax['ultimo'] !== null) ? $filaMax['ultimo'] + 1 : 1;

        $oferta = new Oferta($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $id_oferta, $productos_pack);

        return $oferta->guarda();
    }

    public function guarda()
    {
        if ($this->id_oferta !== null) {
            return self::actualiza($this);
        }
        return self::inserta($this);
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

    // Getters
    public function getIdOferta() { return $this->id_oferta; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFechaIni() { return $this->fecha_ini; }
    public function getFechaFin() { return $this->fecha_fin; }
    public function getDescuento() { return $this->descuento; }
    public function getProductosPack() { return $this->productos_pack; }
}