<?php
namespace BistroFDI\clases\ofertas;
require_once __DIR__ . '/../../../autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use BistroFDI\clases\productos\Producto;
use BistroFDI\clases\ofertas\Oferta;
use BistroFDI\clases\formulario;

class FormularioCrearOferta extends Formulario {

    public function __construct() {
        parent::__construct('formOferta', [
            'action' => 'crear_oferta.php', 
            'urlRedireccion' => 'listar_ofertas.php'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {

        // Recuperar datos si hay errores de validación
        $nombre = $datos['nombre'] ?? '';
        $descripcion = $datos['descripcion'] ?? '';
        $fecha_ini = $datos['fecha_ini'] ?? date('Y-m-d\TH:i');
        $fecha_fin = $datos['fecha_fin'] ?? '';
        $descuento = $datos['descuento'] ?? '0.00';

        // Gestión de errores
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion', 'fecha_ini', 'fecha_fin', 'descuento'], $this->errores, 'span', array('class' => 'error'));

        // Cargar productos para el selector
        $listaProductos = Producto::listarProductos();
        $optionsProductos = "<option value='' data-precio='0'>Selecciona un producto...</option>";
        if ($listaProductos) {
            foreach ($listaProductos as $prod) {
                $optionsProductos .= "<option value='{$prod['nombre']}' data-precio='{$prod['precio']}'>{$prod['nombre']} ({$prod['precio']}€)</option>";
            }
        }

        // Tabla para ir viendo los productos añadidos al pack de la oferta
        $columnas = [
            'nombre'   => 'Producto',
            'cantidad' => 'Cantidad',
            'precio'   => 'Precio Unit.'
        ];

        $tablaObj = new TablaOfertas($columnas, [], true);
        $tablaHtml = $tablaObj->genera();

        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Configuración de la nueva oferta</legend>
            
            <div>
                <label for="nombre">Nombre de la oferta:</label>
                <input id="nombre" type="text" name="nombre" value="$nombre" required>
                {$erroresCampos['nombre']}
            </div>

            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required>$descripcion</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div>
                <div>
                    <label for="fecha_ini">Fecha Inicio:</label>
                    <input id="fecha_ini" type="datetime-local" name="fecha_ini" value="$fecha_ini" required>
                    {$erroresCampos['fecha_ini']}
                </div>
                <div>
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input id="fecha_fin" type="datetime-local" name="fecha_fin" value="$fecha_fin" required>
                    {$erroresCampos['fecha_fin']}
                </div>
            </div>

            <hr>
            
            <h3>Productos incluidos en el Pack</h3>
            <div>
                <select id="select-prod-aux">
                    $optionsProductos
                </select>
                <input type="number" id="cant-prod-aux" value="1" min="1" placeholder="Cant.">
                <button type="button" onclick="addProductoOferta()" class="boton-form">Añadir</button>
            </div>

            $tablaHtml

            <hr>

            <div>
                <p>Total Pack (Sin descuento): <span id="precio-original-display">0.00</span>€</p>
                
                <div>
                    <label for="precio_final">Precio Final de la Oferta (€):</label>
                    <input id="precio_final" type="number" step="0.01" placeholder="Ej: 9.99">
                </div>

                <div>
                    <label for="descuento-input">Descuento calculado (%):</label>
                    <input id="descuento-input" type="text" name="descuento" value="$descuento" readonly>
                    {$erroresCampos['descuento']}
                </div>
            </div>


            <button type="submit" name="enviar" class="boton-form">Guardar Oferta</button>

        </fieldset>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        //validaciones básicas
        $nombre = trim($datos['nombre'] ?? '');
        if (empty($nombre)) $this->errores['nombre'] = "El nombre es obligatorio.";

        $descripcion = trim($datos['descripcion'] ?? '');
        if (empty($descripcion)) {
            $this->errores['descripcion'] = "Debes introducir una descripción.";
        }

        $fecha_ini = $datos['fecha_ini'] ?? '';
        $fecha_fin = $datos['fecha_fin'] ?? '';
        if (strtotime($fecha_fin) <= strtotime($fecha_ini)) {
            $this->errores['fecha_fin'] = "La fecha de fin debe ser posterior a la de inicio.";
        }

        //procesar arrays de productos (enviados por el JS)
        $nombresProd = $datos['prod_nombres'] ?? [];
        $cantsProd = $datos['prod_cants'] ?? [];

        if (empty($nombresProd)) {
            $this->errores[] = "Debes añadir al menos un producto al pack de la oferta.";
        }

        //si no hay errores, guardamos
        if (count($this->errores) === 0) {
            $productos_pack = [];
            for ($i = 0; $i < count($nombresProd); $i++) {
                $productos_pack[$nombresProd[$i]] = (int)$cantsProd[$i];
            }

            $descuento = (float)$datos['descuento'];
            
            $exito = Oferta::crea(
                $nombre, 
                $descripcion, 
                $fecha_ini, 
                $fecha_fin, 
                $descuento, 
                $productos_pack
            );

            if (!$exito) {
                $this->errores[] = "Hubo un error al guardar la oferta en la base de datos.";
            }
        }
    }
}