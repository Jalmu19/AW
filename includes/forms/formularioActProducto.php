<?php
namespace BistroFDI\forms;

require_once dirname(__DIR__).'/config.php';
use BistroFDI\productos\Producto;
use BistroFDI\categorias\Categoria;

class formularioAcProducto extends Formulario
{
    public function __construct() {
        parent::__construct('formEditarProducto', ['action' => 'actualizar_producto.php',
                                                    'urlRedireccion' => 'listar_productos.php',
                                                    'enctype' => 'multipart/form-data']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        // Se reutiliza el nombre de producto introducido previamente o se deja en blanco
        $nombreProducto = $datos['nombre'] ?? '';


        //categorías 
        $resCategorias = Categoria::listaCategorias();
        $optionsCategorias = "<option value=''>Seleccione una categoría</option>";
        if ($resCategorias) {
            while ($cat = $resCategorias->fetch_assoc()) {
                $sel = ($cat['nombre'] === $cat_seleccionada) ? 'selected' : '';
                $optionsCategorias .= "<option value='{$cat['nombre']}' $sel>{$cat['nombre']}</option>";
            }
        }

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'precio', 'categoria', 'descripcion', 'imagen'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Actualizar producto</legend>
            <div>
                <label for="nombre">Nombre:</label>
                <input id="nombre" type="text" name="nombre" value="$nombre" required>
                {$erroresCampos['nombre']}
            </div>

            <div>
                <label for="precio">Precio (€):</label>
                <input id="precio" type="number" step="0.01" name="precio" value="$precio" required>
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
                <label><input type="checkbox" name="disponibilidad" checked> Disponible</label>
                <label><input type="checkbox" name="ofertado"> En oferta</label>
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

        $nombreProducto = trim($datos['nombre'] ?? '');
        $nombreProducto = filter_var($nombreProducto, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombreProducto || empty($nombreProducto) ) {
            $this->errores['nombre'] = 'El nombre del producto no puede estar vacío';
        }
        
        $categoria = trim($datos['categoria'] ?? '');
        $categoria = filter_var($categoria, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $categoria || empty($categoria) ) {
            $this->errores['categoria'] = 'La categoría no puede estar vacía.';
        }
               
        $precio = filter_var(str_replace(',', '.', $datos['precio'] ?? 0), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if ( ! $categoria || empty($categoria) ) {
            $this->errores['categoria'] = 'El precio.';
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $descripcion || empty($descripcion) ) {
            $this->errores['descripcion'] = 'La descripción no puede estar vacía.';
        }

        //imagen
        $nombreImagen = 'producto_default.png'; 
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $_FILES['imagen']['name'];
            $rutaDestino = RAIZ_APP . '/img/productos/' . $nombreImagen;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $this->errores['imagen'] = "Error al guardar la imagen en el servidor.";
            }
        }
        
        if (count($this->errores) === 0) {
            $aux = new Producto($nombre, $precio, $disponibilidad, 10.0, $ofertado, $descripcion, $nombreImagen, $categoria);
            $producto = Producto::actualiza($aux);
        
            if (!$producto) {
                $this->errores[] = "El producto no se ha actualizado";
            }
        }
    }
}
