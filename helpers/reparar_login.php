<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use App\bd\BaseDatos;

try {
    $pdo = BaseDatos::getConnection();
    echo "✅ Conexión a BD exitosa.\n\n";

    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    echo "Hash generado para '{$password}': {$hash}\n\n";

    // 1. Actualizar hash a los 3 usuarios que conservamos
    $stmt = $pdo->prepare("UPDATE usuarios SET contrasenia = :hash WHERE id_usuario IN (1,2,3)");
    $stmt->execute([':hash' => $hash]);
    echo "✅ Hash actualizado para usuarios ID 1, 2, 3\n";

    // 2. Activar el admin (id=1) que estaba desactivado
    $stmt = $pdo->prepare("UPDATE usuarios SET estado_activo = 1 WHERE id_usuario = 1");
    $stmt->execute();
    echo "✅ Admin (id=1) activado (estado_activo = 1)\n";

    // 3. Eliminar los usuarios sobrantes (id 4,5,6,7,8)
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario IN (4,5,6,7,8)");
    $stmt->execute();
    $eliminados = $stmt->rowCount();
    echo "✅ {$eliminados} usuarios sobrantes eliminados (ids 4,5,6,7,8)\n";

    // 4. Verificar usuarios restantes
    echo "\n--- Usuarios activos ---\n";
    $result = $pdo->query("SELECT id_usuario, nombre, correo, rol, estado_activo FROM usuarios ORDER BY id_usuario");
    foreach ($result as $row) {
        $estado = $row['estado_activo'] ? 'Activo' : 'Inactivo';
        echo "  [{$row['id_usuario']}] {$row['nombre']} - {$row['correo']} - {$row['rol']} - {$estado}\n";
    }

    // 5. Verificar que password_verify funciona
    $test = $pdo->query("SELECT contrasenia FROM usuarios WHERE id_usuario = 1")->fetch();
    $verifica = password_verify($password, $test['contrasenia']);
    echo "\n🔑 Verificación password_verify('admin123', hash): " . ($verifica ? '✅ OK' : '❌ FALLA') . "\n";

    echo "\n🎉 Reparación completada. Usa:\n";
    echo "   admin@taller.com / admin123  (ADMINISTRADOR)\n";
    echo "   ana@taller.com    / admin123  (RECEPCIONISTA)\n";
    echo "   juan@taller.com   / admin123  (MECANICO)\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}