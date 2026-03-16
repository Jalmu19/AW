<?php
spl_autoload_register(function ($class) {
    // El prefijo de su proyecto
    $prefix = 'BistroFDI\\';

    // La base donde están sus archivos de clases (ajusten si están en una subcarpeta como 'src')
    $base_dir = __DIR__ . '/'; 

    // ¿La clase usa su namespace?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // No es una clase de nuestro proyecto
    }

    // Obtenemos el nombre relativo (ej: Clases\Usuario)
    $relative_class = substr($class, $len);

    // IMPORTANTE: Reemplazamos \ por / para que Linux entienda la ruta
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        // Esto les ayudará a ver en el log qué ruta está fallando exactamente
        error_log("Autoload error: No se encontró el archivo en " . $file);
    }
});