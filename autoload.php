<?php
spl_autoload_register(function ($class) {
    // El prefijo de su proyecto
    $prefix = 'BistroFDI\\';


    // La base donde están sus archivos de clases
    $base_dir = __DIR__ . '/includes/';


    // ¿La clase usa su namespace?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // No es una clase de nuestro proyecto
    }

    $relative_class = substr($class, $len); // ej: clases\Aplicacion

    // IMPORTANTE: Reemplazamos \ por / para que Linux entienda la ruta
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php'; // → includes/clases/Aplicacion.php

    if (file_exists($file)) {
        require $file;
    } else {
        // Esto les ayudará a ver en el log qué ruta está fallando exactamente
        error_log("Autoload error: No se encontró el archivo en " . $file);
    }
});