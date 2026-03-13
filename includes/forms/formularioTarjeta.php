<?php
namespace BistroFDI\forms;

use BistroFDI\pedidos\Pedido;

class FormularioTarjeta extends Formulario
{
    private $numPedido;
    private $fechaHora;

    public function __construct($numPedido, $fechaHora) {
        parent::__construct('formTarjeta', ['action' => "vista_pago_tarjeta.php?id=$numPedido&fecha=" . urlencode($fechaHora)]);
        
        $this->numPedido = $numPedido;
        $this->fechaHora = $fechaHora;
    }
    
    protected function generaCamposFormulario(&$datos)
    {

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['numTarjeta', 'fCaducidad', 'cve'], $this->errores, 'span', array('class' => 'error'));

        // Recuperamos valores si el usuario ya los escribió y hubo un error de validación
        $numTarjeta = $datos['numTarjeta'] ?? '';
        $fCaducidad = $datos['fCaducidad'] ?? '';
        $cve = $datos['cve'] ?? '';

        return <<<EOF
        <input type="hidden" name="num_pedido" value="{$this->numPedido}">
        <input type="hidden" name="fecha_hora" value="{$this->fechaHora}">
        <fieldset>
            <legend>Pago con tarjeta bancaria</legend>
            <div>
                <p>Número de tarjeta: <input type="text" name="numTarjeta" value="$numTarjeta" maxlength="12" required /></p>
                {$erroresCampos['numTarjeta']}

                <p>Fecha caducidad (MM/YY): <input type="text" name="fCaducidad" value="$fCaducidad" placeholder="MM/YY" required /></p>
                 {$erroresCampos['fCaducidad']}

                <p>CVE: <input type="password" name="cve" value="$cve" maxlength="3";" required /></p>
                 {$erroresCampos['cve']}
                <button type="submit">Finalizar Pago</button>
            </div>
        </fieldset>
        EOF;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $numPedido = $datos['num_pedido'] ?? null;
        $fechaHora = $datos['fecha_hora'] ?? null;
        $tarjeta = trim($datos['numTarjeta'] ?? '');
        $fCaducidad = trim($datos['fCaducidad'] ?? '');

        //Validar tarjeta
        if (strlen($tarjeta) !== 12 || !is_numeric($tarjeta)) {
            $this->errores['numTarjeta'] = 'El número de tarjeta debe tener 12 dígitos.';
        } 

        if (count($this->errores) === 0) {
  
            $estado = Pedido::cobrarPedido($numPedido, $fechaHora);

            if ($estado) {
                $this->urlRedireccion = RUTA_APP . "/includes/compras/vista_confirm_pedido.php?id=$numPedido&fecha=" . urlencode($fechaHora);
            } else {
                $this->errores[] = "Error crítico: No se pudo actualizar el pedido en la base de datos.";
            }
        }
    }
}