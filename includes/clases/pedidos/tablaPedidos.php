<?php
namespace BistroFDI\clases\pedidos;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
use BistroFDI\clases\tabla;
use BistroFDI\clases\Aplicacion;

class TablaPedidos extends Tabla {

    protected function formateaContenido($campo, $valor, $fila) {
        if ($campo === 'estado') {
            return "<span>" . htmlspecialchars($valor) . "</span>";
        }
        
        if ($campo === 'precio_total') {
            // Formateamos el número como moneda
            return number_format($valor, 2, ',', '.') . " €";
        }

        if ($campo == 'productos') {
            return htmlspecialchars_decode($valor);
        }
        if ($campo == 'cantidad') {
            return <<<EOS
                    <button type="submit" onclick = "disminuir_cantidad"> - </button>
                     $valor
                    <button type="submit" onclick = "aumentar_cantidad"> + </button>
                    
                EOS;

        }

        return parent::formateaContenido($campo, $valor, $fila);
    }

    protected function generaAcciones($fila) {
        $app = Aplicacion::getInstance();
        $id = urlencode($fila['nombre']);

        // Obtenemos el nombre del archivo actual (ej: carta.php)
        $paginaActual = basename($_SERVER['PHP_SELF']);

        $urlBorrar = '';
        
        return <<<EOS
            <a href="$urlBorrar" class="eliminar" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Borrar</a>
        EOS;
    }

}
