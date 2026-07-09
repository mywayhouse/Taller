<?php
// ============================================================
// config.php — Configuración global de la aplicación
// ============================================================
// Aquí se definen constantes generales del sistema:
// zona horaria, datos de conexión BD, URLs base, etc.
// ============================================================

// ----------------------------------------------------------
// ZONA HORARIA (Ajustar según ubicación del servidor)
// ----------------------------------------------------------
date_default_timezone_set('America/Tegucigalpa');

// Si ROOT no está definido, definirlo aquí
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

// ----------------------------------------------------------
// CONSTANTES DE CONEXIÓN A LA BASE DE DATOS
// ----------------------------------------------------------
// Si existe .env, cargar desde allí; si no, usar valores por defecto
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    define('DB_HOST', $_ENV['IP'] ?? '127.0.0.1');
    define('DB_PORT', $_ENV['PORT'] ?? '3306');
    define('DB_NAME', $_ENV['DB'] ?? 'taller_mecanico');
    define('DB_USER', $_ENV['USER'] ?? 'root');
    define('DB_PASS', $_ENV['PASSWORD'] ?? '');
    define('DB_CHARSET', 'utf8mb4');
} else {
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_NAME', 'taller_mecanico');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
}

// ----------------------------------------------------------
// CONSTANTES DE LA APLICACIÓN
// ----------------------------------------------------------
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistema de Gesti\u00f3n - Taller Mec\u00e1nico');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/Taller');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

// ----------------------------------------------------------
// RUTAS PARA LOGS Y ARCHIVOS TEMPORALES
// ----------------------------------------------------------
define('LOG_PATH', ROOT . '/storage/logs');
define('UPLOAD_PATH', ROOT . '/public/uploads');
