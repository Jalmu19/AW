<?php
namespace BistroFDI\forms;

use BistroFDI\Aplicacion;
use BistroFDI\pedidos\Pedido;

class FormularioFinalizarPedido extends Formulario
{
    private $numPedido;
    private $fechaHora;

    public function __construct($numPedido, $fechaHora) {
        // Solo necesitamos pasar el ID del formulario y la acción.
        // La redirección se decide dinámicamente en procesaFormulario.
        parent::__construct('formFinalizar', ['action' => 'carrito.php']);
        $this->numPedido = $numPedido;
        $this->fechaHora = $fechaHora;
    }

    protected function generaCamposFormulario(&$datos)
    {
        return <<<EOF
        <input type="hidden" name="num_pedido" value="{$this->numPedido}">
        <input type="hidden" name="fecha_hora" value="{$this->fechaHora}">
        
        <fieldset>
            <legend>Finalizar Compra</legend>
            
            <h3>Opciones de entrega</h3>
            <label><input type="radio" name="tipo_entrega" value="local" checked> Para tomar aquí</label><br>
            <label><input type="radio" name="tipo_entrega" value="llevar"> Para llevar</label>

            <h3>Método de pago</h3>
            <label><input type="radio" name="metodo_pago" value="efectivo" checked> Efectivo</label><br>
            <label><input type="radio" name="metodo_pago" value="tarjeta"> Tarjeta de crédito</label>

            <div>
                <button type="submit">Confirmar y Finalizar Pedido</button>
            </div>
        </fieldset>
        EOF;
    }

    protected function procesaFormulario(&$datos)
    {
        $numPedido = $datos['num_pedido'] ?? null;
        $fechaHora = $datos['fecha_hora'] ?? null; 
        $tipoEntrega = $datos['tipo_entrega'] ?? 'local';
        $metodoPago = $datos['metodo_pago'] ?? 'efectivo';

        if (!$numPedido || !$fechaHora) {
            return ["Error: Datos del pedido no encontrados."];
        }

        $Estado = Pedido::confirmarPedido($numPedido, $fechaHora); 
        $Tipo = Pedido::actualizaTipo($numPedido, $fechaHora, $tipoEntrega);

        if ($Estado && $Tipo) {
            // USAR $this->urlRedireccion (propiedad de la clase base Formulario)
            // Y usar urlencode para la fecha porque tiene espacios y símbolos
            $fechaEncoded = urlencode($fechaHora);
            
            if ($metodoPago === 'tarjeta') {
                $this->urlRedireccion = RUTA_APP . "/includes/users/vista_pago_tarjeta.php?id=$numPedido&fecha=$fechaEncoded";
            } else {
                $this->urlRedireccion = RUTA_APP . "/includes/users/vista_confirm_pedido.php?id=$numPedido&fecha=$fechaEncoded";
            }
        } else {
            return ["Error al procesar el pedido en la base de datos. Inténtelo de nuevo."];
        }
    }
}