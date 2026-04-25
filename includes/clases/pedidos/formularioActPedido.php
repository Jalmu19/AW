<?php
namespace BistroFDI\clases\pedidos;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 use BistroFDI\clases\pedidos\Pedido;
use BistroFDI\clases\formulario;


class FormularioActPedido extends Formulario
{
    public function __construct() {
        parent::__construct('formActPedido', ['action' => 'actualizar_pedido.php',
                                              'urlRedireccion' => RUTA_APP . '/list_ped_ger.php']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $idPedido = $datos['id_pedido'] ?? '';
        $fecha_hora = $datos['fecha_hora'] ?? '';

  
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['num_pedido'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Actualizar estado del pedido</legend>

           <input type="hidden" name="id_pedido" value="$idPedido">
           <input type="hidden" name="fecha_hora" value="$fecha_hora">

            <div>
                <label for="id_pedido">Id del pedido: $idPedido</label>
                {$erroresCampos['num_pedido']}
            </div>
           
            <div>
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Nuevo" class="input-en-linea"> Nuevo
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Recibido" class="input-en-linea"> Recibido
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="En preparacion" class="input-en-linea"> En preparacion
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Cocinando" class="input-en-linea"> Cocinando
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Listo cocina" class="input-en-linea"> Listo cocina
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Terminado" class="input-en-linea"> Terminado
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Entregado" class="input-en-linea"> Entregado
                </label>
                
                <label class="label-en-linea">
                    <input type="radio" name="estado" value="Cancelado" class="input-en-linea"> Cancelado
                </label>
            </div>
            
             <div>
                <button type="submit" name="actualizar" class="boton-form">Ok</button>
            </div>

        </fieldset>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos){
        $num_pedido = (int)$datos['id_pedido'];
        $fecha_hora = str_replace('T', ' ', $datos['fecha_hora'] ?? '');

        $estado = trim($datos['estado'] ?? '');
        $estado = filter_var($estado, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $estado || empty($estado) ) {
            $this->errores['estado'] = 'Debes seleccionar un estado.';
        }


        if (count($this->errores) === 0) { 
            $pedido = Pedido::actualizaEstado($num_pedido, $fecha_hora, $estado);
        
            if (!$pedido) {
                $this->errores[] = "El pedido no ha podido actualizarse correctamente";
            } 
        }
    }
}
