<?php
// ============================================================
// hash_passwords.php
// ============================================================
// Script ÚNICO de migración: convierte contraseñas en texto
// plano de la tabla `usuarios` a hashes bcrypt seguros.
//
// USO (CLI):
//   php helpers/hash_passwords.php
//
// REQUISITO: tener la BD configurada en config/config.php
// ============================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Autoloader.php';

use config\Database;

try {
    $pdo = Database::getConnection();

    echo "=== Migrando contraseñas a bcrypt ===\n\n";

    $stmt = $pdo->query("SELECT id_usuario, contrasenia FROM usuarios");
    $count = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id_usuario'];
        $plain = $row['contrasenia'];

        // Si ya parece un hash bcrypt ($2y$...), lo saltamos
        if (str_starts_with($plain, '$2y$')) {
            echo "  [SKIP] Usuario ID {$id}: ya está hasheado\n";
            continue;
        }

        $hash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

        $update = $pdo->prepare("UPDATE usuarios SET contrasenia = :hash WHERE id_usuario = :id");
        $update->execute([':hash' => $hash, ':id' => $id]);

        echo "  [OK]   Usuario ID {$id}: contraseña hasheada\n";
        $count++;
    }

    echo "\n=== Migración completada. {$count} contraseñas actualizadas. ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
