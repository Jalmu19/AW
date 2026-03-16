<?php
namespace BistroFDI\clases\forms;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__DIR__).'/config.php';
use BistroFDI\clases\categorias\Categoria;
use BistroFDI\clases\forms\formulario;

class FormularioCategoria extends Formulario {

    private $categoria;

    public function __construct($categoria = null) { 
        $this->categoria = $categoria;

        parent::__construct('formCategoria', [
        'action' => 'crear_y_actualizar_categoria.php' . ($categoria ? '?id='.$categoria->getNombre() : ''),
        // 'urlRedireccion' => 'listar_categorias.php',  <-- COMENTA ESTO PARA DEPURA
    ]);
    }

    protected function generaCamposFormulario(&$datos) {

         /*Si hay errores, usa $datos
        Si atualizamos, usa la categoría existente (para valores por defecto)
        Si creamos, vacío*/
        $nombre = $datos['nombre'] ?? ($this->categoria ? $this->categoria->getNombre() : '');
        $descripcion = $datos['descripcion'] ?? ($this->categoria ? $this->categoria->getDescripcion() : '');
       
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion'], $this->errores, 'span', array('class' => 'error'));

        $modoEdicion = $this->categoria !== null;
        $titulo = $modoEdicion ? "Editar Categoría" : "Añadir Categoría";
        $textoBoton = $modoEdicion ? "Actualizar" : "Crear";

       // Si es edición, mostramos el nombre pero lo metemos en un 'hidden' para que llegue a procesaFormulario
        $campoNombre = $modoEdicion ? 
            <<<EOS
                <p><strong>Categoría:</strong> $nombre</p>
                <input type="hidden" name="nombre" value="$nombre"> 
            EOS
            : <<<EOS
                <label for="nombre">Nombre:</label>
                <input id="nombre" type="text" name="nombre" value="$nombre" required>
            EOS;

        //html
        $html = <<<EOF
            $htmlErroresGlobales
            <fieldset>
                <legend>$titulo</legend>

                <div> $campoNombre </div>

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
        }
        else if (!$this->categoria) {
            if(Categoria::buscaCategoria($nombre)){
                $this->errores['nombre'] = "Ya existe una categoría con ese nombre.";
            }
        }
        

        $descripcion = trim($datos['descripcion'] ?? '');
        if (empty($descripcion)) {
            $this->errores['descripcion'] = "Debes introducir una descripción.";
        }
   

        //creacion
       if (count($this->errores) === 0) {
            if ($this->categoria) {
                $exito = Categoria::actualiza($nombre, $descripcion);
            } else {
                $exito = Categoria::crea($nombre, $descripcion);
            }

           if ($exito) {
                header('Location: listar_categorias.php');
                exit();
               
            } else {
                $this->errores[] = "La base de datos no ha realizado ningún cambio.";       
            }
        }
    }
}
