<?php
// ============================================================
// install.php — Script de instalación para nuevos desarrolladores
// ============================================================
// Ejecutar desde CLI: php install.php
// Crea la BD, importa los SQL y configura el .env
// ============================================================

echo "=== Instalacion del Taller Mecanico ===\n\n";

// 1. Verificar .env
if (!file_exists(__DIR__ . '/.env')) {
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
        echo "[OK] .env creado desde .env.example\n";
    }
} else {
    echo "[OK] .env ya existe\n";
}

// 2. Cargar config
require_once __DIR__ . '/vendor/autoload.php';
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$host = $_ENV['IP'] ?? '127.0.0.1';
$user = $_ENV['USER'] ?? 'root';
$pass = $_ENV['PASSWORD'] ?? '';
$db   = $_ENV['DB'] ?? 'taller_mecanico';
$port = $_ENV['PORT'] ?? '3306';

// 3. Conectar sin BD para crearla
try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[OK] Conexion a MySQL\n";
} catch (PDOException $e) {
    echo "[ERROR] No se pudo conectar a MySQL: " . $e->getMessage() . "\n";
    echo "Asegurate de que MySQL este corriendo en $host:$port\n";
    exit(1);
}

// 4. Crear BD si no existe
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$db`");
echo "[OK] Base de datos '$db' creada/verificada\n";

// 5. Importar SQLs
$sqlFiles = [__DIR__ . '/Base_taller.sql', __DIR__ . '/stored_procedures.sql'];
foreach ($sqlFiles as $file) {
    if (!file_exists($file)) {
        echo "[WARN] $file no encontrado, se omite\n";
        continue;
    }
    $sql = file_get_contents($file);
    if (empty(trim($sql))) continue;

    try {
        $pdo->exec("USE `$db`");
        $pdo->exec($sql);
        echo "[OK] " . basename($file) . " importado\n";
    } catch (PDOException $e) {
        echo "[WARN] " . basename($file) . ": " . $e->getMessage() . "\n";
    }
}

// 6. Resetear passwords a admin123
try {
    $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->exec("UPDATE `$db`.`usuarios` SET contrasenia = '$hash'");
    $count = $pdo->exec("UPDATE `$db`.`usuarios` SET estado_activo = 1");
    echo "[OK] $count usuarios activados con contrasena 'admin123'\n";
} catch (PDOException $e) {
    echo "[WARN] No se pudieron resetear passwords: " . $e->getMessage() . "\n";
}

// 7. Verificar tablas
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "[OK] Tablas creadas: " . implode(', ', $tables) . "\n";

echo "\n=== Instalacion completada ===\n";
echo "Usuario: admin@taller.com / admin123\n";
echo "Usuario: ana@taller.com / admin123\n";
echo "Usuario: juan@taller.com / admin123\n";
