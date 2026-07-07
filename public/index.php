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
// En producción cambia E_ALL a 0 para ocultar errores al usuario.
// PHP >= 8.0 permite niveles personalizados de reporte.
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
// 4. CONSTANTES DE RUTAS DEL SISTEMA
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
define('ROOT', dirname(__DIR__));                    // C:/.../taller_mecanico/
define('APP', ROOT . '/app');                        // app/
define('CONFIG', ROOT . '/config');                  // config/
define('VIEWS', ROOT . '/views');                // views/
define('PUBLIC_URL', '/Taller/public');      // URL base para assets

// ----------------------------------------------------------
// 5. CARGA DE ARCHIVOS ESENCIALES
// ----------------------------------------------------------
// Se requiere el Autoloader (PSR-4 style manual) y el archivo
// global de configuraciones (base de datos, constantes, etc.).
require_once CONFIG . '/config.php';
require_once APP . '/core/Autoloader.php';
require_once APP . '/core/Controller.php';
// ----------------------------------------------------------
// 6. INICIO DEL ROUTER (Despachador de peticiones)
// ----------------------------------------------------------
// Se captura el parámetro "url" que viene del .htaccess
// (rewrite), se sanitiza y se envía al Router para que
// determine qué Controlador y Método ejecutar.
use app\core\Router;

$url = isset($_GET['url']) ? $_GET['url'] : '';
$router = new Router();
$router->dispatch(trim($url, '/'));
