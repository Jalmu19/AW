<?php
namespace BistroFDI\forms;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__).'/config.php';
use BistroFDI\productos\Producto;
use BistroFDI\categorias\Categoria;

class FormularioCrearProducto extends Formulario {

    public function __construct() {
        parent::__construct('formProducto', [
            'action' => 'crear_producto.php', 
            'urlRedireccion' => 'listar_productos.php',
            'enctype' => 'multipart/form-data'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {

        $nombre = $datos['nombre'] ?? '';
        $precio = $datos['precio'] ?? '';
        $descripcion = $datos['descripcion'] ?? '';
        $cat_seleccionada = $datos['categoria'] ?? '';

        //errores dinámicos
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'precio', 'categoria', 'descripcion', 'imagen'], $this->errores, 'span', array('class' => 'error'));

        //categorías para el select
        $resCategorias = Categoria::listaCategorias();
        $optionsCategorias = "<option value=''>Seleccione una categoría</option>";
        if ($resCategorias) {
            foreach ($resCategorias as $cat) {
                $optionsCategorias .= "<option value='{$cat}'>{$cat}</option>";
            }
        }

        //html
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Nuevo Producto</legend>
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
                <label><input type="checkbox" name="cocinable"> Cocinable </label>
            </div>

            <button type="submit" name="registro" class="btn-primario">Crear Producto</button>
        </fieldset>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        //validacion
        $nombre = trim($datos['nombre'] ?? '');
        if (empty($nombre) || mb_strlen($nombre) < 3) {
            $this->errores['nombre'] = "El nombre es obligatorio y debe tener 3 caracteres mín.";
        } else {
            if (Producto::buscaProducto($nombre)) {
                $this->errores['nombre'] = "Ya existe un producto con ese nombre.";
            }
        }

        $precio = filter_var($datos['precio'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($precio === false || $precio <= 0) {
            $this->errores['precio'] = "Introduce un precio válido mayor que 0.";
        }

        $categoria = $datos['categoria'] ?? '';
        if (empty($categoria)) {
            $this->errores['categoria'] = "Debes elegir una categoría.";
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        if (empty($descripcion)) {
            $this->errores['descripcion'] = "Debes introducir una descripción.";
        }

        $disponibilidad = isset($datos['disponibilidad']) ? 1 : 0;
        $ofertado = isset($datos['ofertado']) ? 1 : 0;
        $cocinable = isset($datos['cocinable']) ? 1 : 0;

        //imagen
        $nombreImagen = 'producto_default.png'; 
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $_FILES['imagen']['name'];
            $rutaDestino = RAIZ_APP . '/img/productos/' . $nombreImagen;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $this->errores['imagen'] = "Error al guardar la imagen en el servidor.";
            }
        }

        //creacion
        if (count($this->errores) === 0) {
            $exito = Producto::crea($nombre, $precio, $disponibilidad, 10.0, $ofertado, $descripcion, $nombreImagen , $categoria, $cocinable);
            if (!$exito) {
                $this->errores[] = "Error de base de datos: No se pudo insertar el producto.";
            }
        }
    }
}