<?php
namespace BistroFDI\clases\ofertas;

require_once __DIR__ . '/../../../autoload.php';

use BistroFDI\clases\productos\Producto;
use BistroFDI\clases\ofertas\Oferta;
use BistroFDI\clases\formulario;

class FormularioGestionOferta extends Formulario {

    private $oferta; // Objeto oferta para edición

    public function __construct($oferta = null) {
        $this->oferta = $oferta;
        $idForm = $oferta ? 'formEditarOferta' : 'formCrearOferta';
        $action = 'crear_actualizar_oferta.php';

        parent::__construct($idForm, [
            'action' => $action, 
            'urlRedireccion' => 'listar_ofertas.php'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {

        // Si venimos de un error de validación, usamos $datos. 
        // Si no, y tenemos oferta, usamos los valores de la oferta.
        $id_oferta = $this->oferta ? $this->oferta->getIdOferta() : ($datos['id_oferta'] ?? '');
        $nombre = $datos['nombre'] ?? ($this->oferta ? $this->oferta->getNombre() : '');
        $descripcion = $datos['descripcion'] ?? ($this->oferta ? $this->oferta->getDescripcion() : '');
        $fecha_ini = $datos['fecha_ini'] ?? ($this->oferta ? date('Y-m-d\TH:i', strtotime($this->oferta->getFechaIni())) : date('Y-m-d\TH:i'));
        $fecha_fin = $datos['fecha_fin'] ?? ($this->oferta ? date('Y-m-d\TH:i', strtotime($this->oferta->getFechaFin())) : '');
        $descuento = $datos['descuento'] ?? ($this->oferta ? $this->oferta->getDescuento() : '0.00');

        // Productos existentes para la tabla si estamos editando
        $productosExistentes = $this->oferta ? $this->oferta->getProductosPack() : [];
        
        // Convertimos el diccionario ['nombre' => cant] en el formato que espera la TablaOfertas
        $filasTabla = [];
        foreach ($productosExistentes as $nom => $cant) {
            $p = Producto::buscaProducto($nom);
            $filasTabla[] = [
                'nombre' => $nom,
                'cantidad' => $cant,
                'precio' => $p ? $p->getPrecio() : 0
            ];
        }

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion', 'fecha_ini', 'fecha_fin', 'descuento'], $this->errores, 'span', array('class' => 'error'));

        $listaProductos = Producto::listarProductos();
        $optionsProductos = "<option value='' data-precio='0'>Selecciona un producto...</option>";
        if ($listaProductos) {
            foreach ($listaProductos as $prod) {
                $optionsProductos .= "<option value='{$prod['nombre']}' data-precio='{$prod['precio']}'>{$prod['nombre']} ({$prod['precio']}€)</option>";
            }
        }

        $columnas = [
            'nombre'   => 'Producto',
            'cantidad' => 'Cantidad',
            'precio'   => 'Precio Unit.'
        ];

        $tablaObj = new TablaOfertas($columnas, $filasTabla, true);
        $tablaHtml = $tablaObj->genera();

        $legend = $this->oferta ? "Editar Oferta: " . $nombre : "Configuración de la nueva oferta";
        $botonTexto = $this->oferta ? "Actualizar Oferta" : "Guardar Oferta";

        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>$legend</legend>
            
            <input type="hidden" name="id_oferta" value="$id_oferta">

            <div>
                <label>Nombre de la oferta:</label>
                <input id="nombre" type="text" name="nombre" value="$nombre" required>
                {$erroresCampos['nombre']}
            </div>

            <div>
                <label>Descripción:</label>
                <textarea id="descripcion" name="descripcion" required>$descripcion</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div>
                <div>
                    <label>Fecha Inicio:</label>
                    <input id="fecha_ini" type="datetime-local" name="fecha_ini" value="$fecha_ini" required>
                    {$erroresCampos['fecha_ini']}
                </div>
                <div>
                    <label>Fecha Fin:</label>
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
                <input type="number" id="cant-prod-aux" value="1" min="1">
                <button type="button" onclick="addProductoOferta()" class="boton-form">Añadir</button>
            </div>

            $tablaHtml

            <hr>

            <div>
                <p>Total Pack (Sin descuento): <span id="precio-original-display">0.00</span>€</p>
                
                <div>
                    <label>Precio Final de la Oferta (€):</label>
                    <input id="precio_final" type="number" step="0.01" placeholder="Ej: 9.99">
                </div>

                <div>
                    <label>Descuento calculado (%):</label>
                    <input id="descuento-input" type="text" name="descuento" value="$descuento" readonly>
                    {$erroresCampos['descuento']}
                </div>
            </div>

            <button type="submit" name="enviar" class="boton-form">$botonTexto</button>

        </fieldset>
        <script> document.addEventListener('DOMContentLoaded', () => { if(typeof calcularTotales === 'function') calcularTotales(); }); </script>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        $id_oferta = $datos['id_oferta'] ?? null;
        $nombre = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');
        $fecha_ini = $datos['fecha_ini'] ?? '';
        $fecha_fin = $datos['fecha_fin'] ?? '';
        $descuento = (float)($datos['descuento'] ?? 0);

        //validaciones
        if (empty($nombre)) $this->errores['nombre'] = "El nombre es obligatorio.";

        if (empty($descripcion)) {
            $this->errores['descripcion'] = "Debes introducir una descripción.";
        }

        if (strtotime($fecha_fin) <= strtotime($fecha_ini)) {
            $this->errores['fecha_fin'] = "La fecha de fin debe ser posterior a la de inicio.";
        }

        $nombresProd = $datos['prod_nombres'] ?? [];
        $cantsProd = $datos['prod_cants'] ?? [];
        if (empty($nombresProd)) {
            $this->errores[] = "Debes añadir al menos un producto al pack.";
        }

        if (count($this->errores) === 0) {
            $productos_pack = [];
            for ($i = 0; $i < count($nombresProd); $i++) {
                $productos_pack[$nombresProd[$i]] = (int)$cantsProd[$i];
            }

            // Si hay ID, actualizamos; si no, creamos.
            if ($id_oferta) {
                $ofertaObj = new Oferta($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $id_oferta, $productos_pack);
                $exito = $ofertaObj->guarda();
            } else {
                $exito = Oferta::crea($nombre, $descripcion, $fecha_ini, $fecha_fin, $descuento, $productos_pack);
            }

            if (!$exito) {
                $this->errores[] = "Hubo un error al procesar la oferta en la base de datos.";
            }
        }
    }
}