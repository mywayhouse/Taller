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
date_default_timezone_set('America/Tegucigalpa');   //sin importar el lugar usara zonas horarias de honduras

// Si ROOT no está definido, definirlo aquí
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));   //_DIR_ buscara la carpeta padre osea la raiz del proyecto, en este caso Taller
}

// ----------------------------------------------------------
// CONSTANTES DE CONEXIÓN A LA BASE DE DATOS
// ----------------------------------------------------------
// Si existe .env, cargar desde allí; si no, usar valores por defecto
$envFile = dirname(__DIR__) . '/.env';  //aqui se guardaran contraseñas reales fuera del codigo fuente, para que no se suban a github
if (file_exists($envFile)) {
    //si lo encuentra utilizara Dotenv para leerlo
    //luego define las constantes de conexion a la base de datos
    //?? significa usar el valor de la izquierda si existe, sino usar el de la derecha osea como si no encuentras lo que pido usa el default xd
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    define('DB_HOST', $_ENV['IP'] ?? '127.0.0.1');
    define('DB_PORT', $_ENV['PORT'] ?? '3306');
    define('DB_NAME', $_ENV['DB'] ?? 'taller_mecanico');
    define('DB_USER', $_ENV['USER'] ?? 'root');
    define('DB_PASS', $_ENV['PASSWORD'] ?? '');
    define('DB_CHARSET', 'utf8mb4');
} else {
    //si no lo encuentra asumira que es local
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
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Auto & Motos');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/Taller');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

// ----------------------------------------------------------
// DATOS DE LA EMPRESA (Facturación)
// ----------------------------------------------------------
define('EMPRESA_NOMBRE', $_ENV['EMPRESA_NOMBRE'] ?? 'Auto & Motos');
define('EMPRESA_DIRECCION', $_ENV['EMPRESA_DIRECCION'] ?? 'Col. Ejemplo, Ave. Principal, Local #1, San Pedro Sula');
define('EMPRESA_TELEFONO', $_ENV['EMPRESA_TELEFONO'] ?? '+504 9999-9999');
define('EMPRESA_RTN', $_ENV['EMPRESA_RTN'] ?? '0801-1990-00000');
define('EMPRESA_CORREO', $_ENV['EMPRESA_CORREO'] ?? 'info@auto&motos.com');

// ----------------------------------------------------------
// RUTAS PARA LOGS Y ARCHIVOS TEMPORALES
// ----------------------------------------------------------
define('LOG_PATH', ROOT . '/storage/logs');
define('UPLOAD_PATH', ROOT . '/public/uploads');
