<?php
namespace BistroFDI\tables;

class TablaPedidosGerente extends Tabla
{
    protected function formateaContenido($campo, $valor, $fila)
    {
        switch ($campo) {

            case 'num_pedido':
                $num = htmlspecialchars($fila['num_pedido']);
                $fh  = htmlspecialchars($fila['fecha_hora'] ?? '');
                return $num . " (" . $fh . ")";

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
        $url = RUTA_APP . "/includes/acciones_gerente/ver_pedido.php"
             . "?num_pedido=" . urlencode($fila['num_pedido'])
             . "&fecha_hora=" . urlencode($fila['fecha_hora']);

        return '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '">Ver detalle</a>';
    }
}

