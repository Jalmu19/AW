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
                <input type="radio" id="nuevo" name="estado" value="Nuevo"> Nuevo
                <input type="radio"name="estado" value="Recibido"> Recibido
                <input type="radio" name="estado" value="En preparacion">En preparacion
                <input type="radio" name="estado" value="Cocinando">Cocinando
                <input type="radio" name="estado" value="Listo cocina">Listo cocina
                <input type="radio" name="estado" value="Terminado">Terminado
                <input type="radio" name="estado" value="Entregado">Entregado
                <input type="radio" name="estado" value="Cancelado">Cancelado
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
