<?php
namespace BistroFDI\clases\ofertas;
require_once __DIR__ . '/../../../autoload.php';
use BistroFDI\clases\ofertas\Oferta;
use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\Aplicacion;

$app = Aplicacion::getInstance();
$nombreUsuario = $app->getCurrentUserName();

if ($nombreUsuario) {
    //Identificar pedido actual
    $infoPedido = Pedido::pedidosNuevosUsuario($nombreUsuario, Pedido::TIPO_DOMICILIO);
    if ($infoPedido) { 
        $fecha = $infoPedido[0];
        $num = $infoPedido[1];

        Pedido::limpiarOfertasPrevias($num, $fecha);

        
        //Cargar carrito
        $productosCarrito = Pedido::getCarritoUsuario($nombreUsuario);
        $carritoMapeado = [];
        foreach ($productosCarrito as $p) { $carritoMapeado[$p['nombre']] = $p['cantidad']; }

        //Procesar Ofertas
        $ofertasActivas = Oferta::listarOfertas(true);
        foreach ($ofertasActivas as $o) {
            $idOferta = $o['id_oferta'];
            $productosRequeridos = Oferta::buscaProductosOferta($idOferta);
            
            $vecesPosibles = PHP_INT_MAX;
            $cumpleTodaLaOferta = true;

            foreach ($productosRequeridos as $nombreProd => $cantidadNec) {
                $cantidadTengo = $carritoMapeado[$nombreProd] ?? 0;
                if ($cantidadTengo < $cantidadNec) {
                    $cumpleTodaLaOferta = false;
                    break;
                }
                $vecesPosibles = min($vecesPosibles, floor($cantidadTengo / $cantidadNec));
            }

            if ($cumpleTodaLaOferta && $vecesPosibles > 0) {
                Pedido::aplicarOferta($nombreUsuario, $idOferta, $vecesPosibles);
            }
        }

        //RECALCULAR TOTALES
        Pedido::actualizarTotalPedido($fecha, $num, true);
    }

    
}