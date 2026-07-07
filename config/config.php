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

// ----------------------------------------------------------
// CONSTANTES DE CONEXIÓN A LA BASE DE DATOS
// ----------------------------------------------------------
// Valores por defecto para entorno local (Laragon).
// En producción, leer de variables de entorno.
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'taller_mecanico');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ----------------------------------------------------------
// CONSTANTES DE LA APLICACIÓN
// ----------------------------------------------------------
define('APP_NAME', 'Sistema de Gestión - Taller Mecánico');
define('APP_URL', 'http://localhost/Taller');
define('APP_DEBUG', true);                  // Cambiar a false en producción

// ----------------------------------------------------------
// RUTAS PARA LOGS Y ARCHIVOS TEMPORALES
// ----------------------------------------------------------
define('LOG_PATH', ROOT . '/storage/logs');
define('UPLOAD_PATH', ROOT . '/public/uploads');
