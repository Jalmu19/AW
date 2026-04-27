<?php
namespace BistroFDI\clases\ofertas;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\ofertas\FormularioGestionOferta;
use BistroFDI\clases\ofertas\Oferta;
use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\Aplicacion;

$app = Aplicacion::getInstance();

if (!$app->isCurrentUserLogged()) {
    header('Location: login.php');
    exit();
}

$nombreUsuario = $app->getCurrentUserName();
$idOferta = filter_input(INPUT_GET, 'id_oferta', FILTER_VALIDATE_INT) ?: null;


if($nombreUsuario && $idOferta){  
    //recorrer carrito y comprobar si la oferta es aplicable
    $productosEnLaOferta = Oferta::buscaProductosOferta($idOferta);
    $productosCarrito = Pedido::getCarritoUsuario($nombreUsuario);

    // Inicializamos con un número muy alto. 
    $vecesAplicable = !empty($productosEnLaOferta) ? PHP_INT_MAX : 0;

    foreach ($productosEnLaOferta as $nombreReq => $cantidadReq) {
        $cantidadDisponible = $productosCarrito[$nombreReq] ?? 0;
        
        // Calculamos cuántos packs completos permite este ingrediente específico
        $posiblesParaEsteProd = round(($cantidadDisponible / $cantidadReq));
        
        // El número total de packs será el mínimo de todos los componentes
        // Si un producto falta (0), el min() se convertirá automáticamente en 0
        $vecesAplicable = min($vecesAplicable, (int)$posiblesParaEsteProd);
    }

    // Solo aplicamos si el resultado final es mayor que cero.
    if ($vecesAplicable > 0) {
        Pedido::aplicarOferta($nombreUsuario, $idOferta, $vecesAplicable);
    }
} 

header('Location: ../../../carrito.php');
exit();


