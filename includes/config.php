<?php

/**
 * Parámetros de conexión a la BD
 */
define('BD_HOST', 'localhost');
define('BD_NAME', 'awp2');
define('BD_USER', 'awp2');
define('BD_PASS', 'awpass');

/**
 * Parámetros de configuración utilizados para generar las URLs y las rutas a ficheros en la aplicación
 */
define('RAIZ_APP', dirname(__DIR__));
define('RUTA_APP', '/practica2');
define('RUTA_IMGS', RUTA_APP.'img/');
define('RUTA_CSS', RUTA_APP.'css/');
define('RUTA_JS', RUTA_APP.'js/');

/**
 * Configuración del soporte de UTF-8, localización (idioma y país) y zona horaria
 */
ini_set('default_charset', 'UTF-8');
setLocale(LC_ALL, 'es_ES.UTF.8');
date_default_timezone_set('Europe/Madrid');

// Carga de clases principales
require_once __DIR__ . '/Aplicacion.php';
require_once __DIR__ . '/users/Usuario.php';

// Inicialización de la aplicación
$app = Aplicacion::getInstance();
$app->init(['host' => BD_HOST, 'bd'   => BD_NAME, 'user' => BD_USER, 'pass' => BD_PASS]);

// Cierre automático de conexión al finalizar el script
register_shutdown_function([$app, 'shutdown']);