<?php
// ============================================================
// Autoloader.php — Carga automática de clases (PSR-4 style)
// ============================================================
// Elimina la necesidad de escribir "require_once" en cada
// archivo. Se registra con spl_autoload_register() y se
// ejecuta cada vez que se usa una clase no definida aún.
// ============================================================

/**
 * Autoloader principal.
 * Convierte el nombre de la clase (con su namespace) en una
 * ruta de archivo y lo incluye si existe.
 *
 * Ejemplo:
 *   "app\controllers\ClientesController"
 *   -> busca en: /app/controllers/ClientesController.php
 */
spl_autoload_register(function (string $className): void {
    // ------------------------------------------------------
    // 1. Convertir namespace a ruta relativa
    //    "app\controllers\ClientesController"
    //    -> "app/controllers/ClientesController.php"
    // ------------------------------------------------------
    $file = ROOT . DIRECTORY_SEPARATOR
          . str_replace('\\', DIRECTORY_SEPARATOR, $className)
          . '.php';
    
    $coreFile = APP . '/core/' . $className . '.php';      
    // ------------------------------------------------------
    // 2. Incluir el archivo si existe
    // ------------------------------------------------------
    if (file_exists($file)) {
        require_once $file;
    } elseif (file_exists($coreFile)){
        require_once $coreFile;
    }
});
