<?php
// ============================================================
// FRONT CONTROLLER — Punto de entrada único de la aplicación
// ============================================================
// Todas las peticiones HTTP (excepto archivos estáticos)
// pasan por aquí gracias a las reglas de .htaccess.
// ============================================================

// ----------------------------------------------------------
// 1. CONFIGURACIÓN DE ERRORES (Entorno de desarrollo)
// ----------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/error.log');

// ----------------------------------------------------------
// 2. ZONA HORARIA Y JUEGO DE CARACTERES
// ----------------------------------------------------------
date_default_timezone_set('America/Tegucigalpa');
header('Content-Type: text/html; charset=utf-8');

// ----------------------------------------------------------
// 3. INICIO DE SESIÓN (Manejo de estado del usuario)
// ----------------------------------------------------------
// Se inicia la sesión UNA SOLA VEZ al entrar al sistema.
// Esto permite mantener al usuario autenticado entre
// páginas mediante variables $_SESSION.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------------
// 4. AUTOLOADER DE COMPOSER (PSR-4)
// ----------------------------------------------------------
// Carga automática de todas las clases del proyecto usando
// el estándar PSR-4. Mapea:
//   "App\"     -> app/
//   "Config\"  -> config/
// Elimina la necesidad de require_once manuales.
// ----------------------------------------------------------
require_once __DIR__ . '/../vendor/autoload.php';

// ----------------------------------------------------------
// 5. CONSTANTES DE RUTAS DEL SISTEMA
// ----------------------------------------------------------
// Facilita la inclusión de archivos desde cualquier parte
// del código usando rutas absolutas dinámicas.
define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/Src');
define('CONFIG', ROOT . '/config');
define('VIEWS', ROOT . '/views');
define('PUBLIC_URL', '/Taller/public');

// ----------------------------------------------------------
// 6. CARGA DE CONFIGURACIÓN GLOBAL
// ----------------------------------------------------------
require_once CONFIG . '/config.php';

// ----------------------------------------------------------
// 7. INICIO DEL ROUTER
// ----------------------------------------------------------
use App\Routes\Enrutador;
$url = isset($_GET['url']) ? $_GET['url'] : '';
$router = new Enrutador();
$router->dispatch(trim($url, '/'));
