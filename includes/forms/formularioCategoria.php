<?php
require_once dirname(__DIR__).'/config.php';
require_once RAIZ_APP . '/includes/forms/formulario.php';
require_once RAIZ_APP . '/includes/categorias/Categoria.php';

class FormularioCategoria extends Formulario {

    private $categoria;

    public function __construct($categoria = null) {

        $this->categoria = $categoria;

        $accion = 'crear_y_actualizar_categoria.php';

        parent::__construct('formCategoria', [
            'action' =>  $accion, 
            'urlRedireccion' => 'listar_categorias.php',
            'enctype' => 'multipart/form-data'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {

         /*Si hay errores, usa $datos
        Si atualiamos, usa la categoría existente (para valores por defecto)
        Si creamos, vacío*/
        $nombre = $datos['nombre'] ?? ($this->categoria ? $this->categoria->getNombre() : '');
        $descripcion = $datos['descripcion'] ?? ($this->categoria ? $this->categoria->getDescripcion() : '');
       
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion'], $this->errores, 'span', array('class' => 'error'));

        $modoEdicion = $this->categoria !== null;
        $titulo = $modoEdicion ? "Editar Categoría" : "Nueva Categoría";
        $textoBoton = $modoEdicion ? "Actualizar Categoría" : "Crear Categoría";

        //html
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>$titulo</legend>

            <div>
                <label for="nombre">Nombre:</label>
                <input id="nombre" type="text" name="nombre" value="$nombre" required>
                {$erroresCampos['nombre']}
            </div>

            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required>$descripcion</textarea>
                {$erroresCampos['descripcion']}
            </div>

            <button type="submit" name="registro" class="btn-primario">$textoBoton</button>
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
            if (!$this->categoria && Categoria::buscaCategoria($nombre)) {
                $this->errores['nombre'] = "Ya existe una categoría con ese nombre.";
            }
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        if (empty($descripcion)) {
            $this->errores['descripcion'] = "Debes introducir una descripción.";
        }

        //creacion
        if (count($this->errores) === 0) {
            $categoria = new Categoria($nombre, $descripcion);
            $exito = $categoria->guarda(); // guarda() decide si inserta o actualiza
            if (!$exito) {
                $this->errores[] = "Error de base de datos: No se pudo gestionar la categoría.";
            }
        }
    }
}