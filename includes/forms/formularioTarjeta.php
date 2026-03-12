<?php
namespace BistroFDI\forms;

require_once dirname(__DIR__).'/config.php';

class FormularioTarjeta extends Formulario
{
     public function __construct() {
        parent::__construct('formEditarPerfil', ['action' => 'pagar.php', 
                                                'urlRedireccion' => 'vista_confirm_pedido.php']);
    }
    
    protected function camposFormulario()
    {
        // Se genera el HTML asociado a los campos del formulario 
        $html = <<<EOF
         <fieldset>
            <legend>Pago con tarjeta bancaria</legend>
            <div>
                <form action = "" method ="post">
                    Numero de trajeta:
                    <input type="number" name="numTarjeta" value=""/>
                    Fecha de caducidad
                    <input type="text" name="FCaducidad" value="MM/YY"/>
                    CVE
                    <input type="number" name="cve" value=""/>
                    <input type="submit" value="Enviar" />

                </form>

            </div>

        </fieldset>
        EOF;
        return $html;
    }



    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        $tarjeta = (string) $datos['numTarjeta'];
        $tarjeta = filter_var($tarjeta, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //errores en el numero de tarjeta
        if (strlen($tarjeta) < 12 || strlen($tarjeta) > 12) {
            $this->errores['numTarjeta'] = 'No coincide el numero de dígitos con los que se piden';
        }
        
        $fecha = explode('/',  $datos['fCaducidad']);//separo en mes y año
       
        if ( 1 < sizeof($fecha) || sizeof($fecha) > 2 ) {
            $this->errores['fCaducidad'] = 'Error en la fecha de caducidad';
        }
        else{
            //comprobaciones en las fechas
            $mes = $fecha[0];
            $mes = filter_var($mes, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $anyo = $fecha[1];
            $anyo = filter_var($anyo, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if(!(in_array($mes, [01,02,03,04,05,06,07,08,09,10,11,12]) && (strlen($anyo)<2 ||strlen($anyo)>2))){
                $this->errores['fCaducidad'] = 'Error en la fecha de caducidad';
            }
        }

        $cve = to_string($datos['cve']);//cve
        $cve = filter_var($cve, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ( !in_array(sizeof($cve), [3])) {
                $this->errores['cve'] = 'CVE incorrecto';
            }
        } 
    }
}
