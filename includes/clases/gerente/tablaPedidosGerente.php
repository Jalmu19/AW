<?php
namespace BistroFDI\clases\gerente;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\tabla;
class TablaPedidosGerente extends Tabla
{
    protected function formateaContenido($campo, $valor, $fila)
    {
        switch ($campo) {

            case 'num_pedido':
                $num = htmlspecialchars($fila['num_pedido']);
                return $num;

            case 'estado':
                $estado = htmlspecialchars($valor);
                $texto = $estado;

                if ($valor === 'En preparacion') {
                    $cocinero = htmlspecialchars($fila['cocinero'] ?? '');
                    $avatar   = $fila['avatar_cocinero'] ?? '';

                    if (!empty($cocinero)) {
                        if (!empty($avatar)) {
                            $texto .= " | " . "<img src='" . htmlspecialchars($avatar, ENT_QUOTES) . "' width='25' height='25'>" . " " . $cocinero;
                        } else {
                            $texto .= " | " . $cocinero;
                        }
                    } else {
                        $texto .= " | Sin asignar";
                    }
                }

                return $texto;

            default:
                return parent::formateaContenido($campo, $valor, $fila);
        }
    }

    protected function generaAcciones($fila)
    {
        $fecha_hora = urlencode($fila['fecha_hora']);
        $num_pedido =  urlencode($fila['num_pedido']);

        $urlVer = RUTA_APP . "/ver_pedido.php"
             . "?num_pedido=" .$num_pedido
             . "&fecha_hora=" . $fecha_hora;
        
        $urlEditar = RUTA_APP . "/includes/clases/pedidos/actualizar_pedido.php"
                    .   "?num_pedido=" . $num_pedido
                    . "&fecha_hora=" . $fecha_hora;
        
        $urlBorrar = RUTA_APP . "/includes/clases/pedidos/eliminar_pedido.php"
                    ."?num_pedido=" . $num_pedido
                    . "&fecha_hora=" . $fecha_hora;
        
        return <<<EOS
            <a href="$urlEditar">Editar</a>
            <a href="$urlBorrar" onclick="return confirm('¿Borrar $num_pedido?')">Borrar</a>
            <a href="$urlVer"> Ver detalles </a>
        EOS;
    }
}

