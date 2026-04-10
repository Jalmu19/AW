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
                                              'urlRedireccion' => 'listar_pedidos.php',
                                               'enctype' => 'multipart/form-data']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $idPedido = $datos['num_pedido'] ?? '';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['id_pedido'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Actualizar estado del pedido</legend>
            <div>
                <label for="id_pedido">Id del pedido:</label>
                <input id="id_pedido" type="text" name="id_pedido" value="$idPedido" />
                {$erroresCampos['id_pedido']}
            </div>
           
            <div>
                <label class="label-en-linea">
                    <input type="radio" id="nuevo" name="estado" value="Nuevo" class="input-en-linea"> Nuevo
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
        
        if (count($this->errores) === 0) { 
            $pedido = Pedido::actualizaEstado($datos['num_pedido'], $datos['fecha_hora'], $datos['estado']);
        
            if (!$pedido) {
                $this->errores[] = "El pedido no ha podido actualizarse correctamente";
            } 
        }
    }
}
