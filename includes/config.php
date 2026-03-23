<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
use BistroFDI\clases\aplicacion;
/*use BistroFDI\clases\users\Usuario;*/
/**
 * Parámetros de conexión a la BD
 */
//define('BD_HOST', 'vm002.db.swarm.test');// sevidor de la base de datos, no localhost
define('BD_HOST', 'localhost');
define('BD_NAME', 'awp2');
define('BD_USER', 'awp2');
define('BD_PASS', 'awpass');

/**
 * Parámetros de configuración utilizados para generar las URLs y las rutas a ficheros en la aplicación
 */
define('RAIZ_APP', dirname(__DIR__));
define('RUTA_APP', '/AW/AW');
define('RUTA_IMGS', RUTA_APP.'/img/');
define('RUTA_CSS', RUTA_APP.'/css/');
define('RUTA_JS', RUTA_APP.'/js/');

/**
 * Configuración del soporte de UTF-8, localización (idioma y país) y zona horaria
 */
ini_set('default_charset', 'UTF-8');
setLocale(LC_ALL, 'es_ES.UTF.8');
date_default_timezone_set('Europe/Madrid');

// Carga de clases principales

require_once __DIR__ . '/clases/aplicacion.php';
/*require_once __DIR__ . '/users/Usuario.php';*/

//Función para autocargar clases PHP.
spl_autoload_register(function ($class) {
    // Prefijo del namespace del proyecto
    $prefix = 'BistroFDI\\';

    // Directorio base para el namespace raíz
    $base_dir = __DIR__ . "/";

    // Verificar si la clase usa nuestro namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtener la parte relativa de la clase
    $relative_class = substr($class, $len);

    // Convertir namespace en ruta de archivos
    $file = str_replace('\\', '/', $base_dir) . str_replace("\\", '/', $relative_class) . '.php';

    // Cargar el archivo si existe
    if (file_exists($file)) {
        require_once $file;
    }
});

// Inicialización de la aplicación
$app = Aplicacion::getInstance();
$app->init(['host' => BD_HOST, 'bd'   => BD_NAME, 'user' => BD_USER, 'pass' => BD_PASS]);

// Cierre automático de conexión al finalizar el script

register_shutdown_function([$app, 'shutdown']);
