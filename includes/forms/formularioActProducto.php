<?php
namespace BistroFDI\forms;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\productos\Producto;
use BistroFDI\categorias\Categoria;

class formularioActProducto extends Formulario
{
    public function __construct() {
        parent::__construct('formProducto', [
            'action' => 'actualizar_producto.php', 
            'urlRedireccion' => 'listar_productos.php',
            'enctype' => 'multipart/form-data'
        ]);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $nombreProducto =$datos['nombre'] ?? '';
        $precio = $datos['precio'] ?? '';
        $categoria = $datos['categoria'] ?? '';
        $descripcion = $datos['descripcion'] ?? '';
        $imagen = $datos['imagen'] ?? '';
        $disponibilidad = $datos['disponibilidad'] ?? 0;
        $ofertado = $datos['ofertado'] ?? 0;
        $cocinable = $datos['cocinable'] ?? 0;

        //valores de los checkboxes según valor del producto anterior
        $checkedDisp = $disponibilidad ? 'checked' : '';
        $checkedOfer = $ofertado ? 'checked' : '';
        $checkedCoci = $cocinable ? 'checked' : '';


        //categorías 
        $categoriaActual = $datos['categoria'] ?? '';
        $resCategorias = Categoria::listaCategorias();
        $optionsCategorias = "<option value=''>Seleccione una categoría</option>";
        if ($resCategorias) {
            foreach ($resCategorias as $cat) {
                $selected = ($cat == $categoriaActual) ? 'selected' : ''; 
                $optionsCategorias .= "<option value='{$cat}' $selected>{$cat}</option>";
            }
        }

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['precio', 'categoria', 'descripcion', 'imagen'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Actualizar producto</legend>
            
            <input type="hidden" name="nombre" value="$nombreProducto">
            
            <div>
                <label for="precio">Precio (€):</label>
                <input id="precio" type="number" step="0.01" name="precio" value = "$precio" required>
                {$erroresCampos['precio']}
            </div>

            <div>
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    $optionsCategorias
                </select>
                {$erroresCampos['categoria']}
            </div>

            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required>$descripcion</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <div>
                <label for="imagen">Imagen del producto:</label>
                <input id="imagen" type="file" name="imagen" accept="image/*">
                {$erroresCampos['imagen']}
            </div>

            <div>
                <label><input type="checkbox" name="disponibilidad" $checkedDisp> Disponible</label>
                <label><input type="checkbox" name="ofertado" $checkedOfer> En oferta</label>
                <label><input type="checkbox" name="cocinable" $checkedCoci> Cocinable</label>

            </div>

            <div>
                <button type="submit" name="actualizar">Ok</button>
            </div>

        </fieldset>
    EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {

        $this->errores = [];

        $nombreProducto = $datos['nombre'] ?? '';

        $productoActual = Producto::buscaProducto($nombreProducto);
        if (!$productoActual) {
            $this->errores[] = "El producto no existe.";
        return;
    }
        $categoria = trim($datos['categoria'] ?? '');
        $categoria = filter_var($categoria, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $categoria || empty($categoria) ) {
            $this->errores['categoria'] = 'La categoría no puede estar vacía.';
        }
               
        $precio = filter_var(str_replace(',', '.', $datos['precio'] ?? 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if ( ! $precio || empty($precio) ) {
            $this->errores['precio'] = 'El precio no puede estar vacío.';
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $descripcion || empty($descripcion) ) {
            $this->errores['descripcion'] = 'La descripción no puede estar vacía.';
        }

        //imagen
        $nombreImagen = $productoActual->getImagen(); // Por defecto mantenemos la que ya tiene
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $_FILES['imagen']['name'];
            $rutaDestino = RAIZ_APP . '/img/productos/' . $nombreImagen;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $this->errores['imagen'] = "Error al guardar la imagen en el servidor.";
            }
        }

        $iva = $productoActual->getIva();

        $disponibilidad = isset($datos['disponibilidad']) ? 1 : 0;
        $ofertado = isset($datos['ofertado']) ? 1 : 0;
        $cocinable = isset($datos['cocinable']) ? 1 : 0;

        
        if (count($this->errores) === 0) {
            $aux = new Producto($nombreProducto, $precio, $disponibilidad, $iva, $ofertado, $descripcion, $nombreImagen, $categoria, $cocinable);
            $producto = Producto::actualiza($aux);

            if (!$producto) {
                $this->errores[] = "El producto no se ha actualizado";
            }
        }
    }
}
