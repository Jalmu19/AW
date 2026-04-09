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
                return $texto;

            case 'cocinero':
                $cocinero = htmlspecialchars($fila['cocinero'] ?? '');
                $avatar   = $fila['avatar_cocinero'] ?? '';
                $rutaAvatar = RUTA_APP . "/img/avatares/$avatar";

                if (!empty($cocinero)) {
                    if (!empty($avatar)) {
                        $text = "<img src=$rutaAvatar class='usuario'>" .  $cocinero;
                    } else {
                        $text = $cocinero;
                    }
                } else {
                    $text = "Sin asignar";
                }
                
                return $text;


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
            <a href="$urlVer" class="boton-form"> Ver detalles </a>
            <a href="$urlEditar" class="boton-form">Editar</a>
            <a href="$urlBorrar" class="eliminar" onclick="return confirm('¿Borrar $num_pedido?')">Borrar</a>
            
        EOS;
    }
}

