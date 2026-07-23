<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Usuario;

class ControladorAuth extends Controlador
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function login(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }
        $data = [
            'title' => 'Iniciar Sesion',
            'error' => $_SESSION['error_login'] ?? null,
            'oldCorreo' => $_SESSION['old_correo'] ?? '',
        ];
        unset($_SESSION['error_login'], $_SESSION['old_correo']);
        $this->render('auth/login', $data);
    }

    public function authenticate(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
        }
        $correo    = trim($this->getPost('correo', ''));
        $password  = $this->getPost('contrasenia', '');

        if (empty($correo) || empty($password)) {
            $_SESSION['error_login'] = 'Todos los campos son obligatorios.';
            $_SESSION['old_correo'] = $correo;
            $this->redirect('auth/login');
        }

        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
//Para depurar
/*

// 🔍 BLOQUE DE DEPURACIÓN MEJORADO
echo "<pre style='background:#1a1a1a; color:#00ff00; padding:20px; margin:20px; font-size:14px;'>";
echo "══════════════════════════════════════════════\n";
echo "  🔍 DEPURACIÓN COMPLETA DE CONTRASEÑAS\n";
echo "══════════════════════════════════════════════\n\n";

if (!$usuario) {
    echo "❌ USUARIO NO ENCONTRADO\n";
    echo "Correo buscado: " . $correo . "\n";
} else {
    echo "✅ USUARIO ENCONTRADO:\n\n";
    echo "ID:              " . $usuario['id_usuario'] . "\n";
    echo "Nombre:          " . $usuario['nombre'] . "\n";
    echo "Correo:          " . $usuario['correo'] . "\n";
    echo "Rol:             " . $usuario['rol'] . "\n";
    echo "Estado:          " . ($usuario['estado_activo'] == 1 ? 'Activo' : 'Inactivo') . "\n\n";
    
    echo "──────────────────────────────────────────────\n";
    echo "  🔐 HASH ALMACENADO EN LA BASE DE DATOS\n";
    echo "──────────────────────────────────────────────\n\n";
    echo "Hash completo:\n";
    echo $usuario['contrasenia'] . "\n\n";
    
    // Analizar el hash almacenado
    echo "Análisis del hash almacenado:\n";
    $infoHashBD = password_get_info($usuario['contrasenia']);
    echo "  - Algoritmo:     " . $infoHashBD['algoName'] . "\n";
    echo "  - Costo:         " . ($infoHashBD['options']['cost'] ?? 'N/A') . "\n";
    
    // Extraer partes del hash bcrypt
    // Formato: $2y$12$[salt de 22 caracteres][hash de 31 caracteres]
    $partesHash = explode('$', $usuario['contrasenia']);
    echo "  - Versión:       $" . ($partesHash[1] ?? '?') . "$\n";
    echo "  - Costo:         " . ($partesHash[2] ?? '?') . "\n";
    if (isset($partesHash[3])) {
        $salt = substr($partesHash[3], 0, 22);
        $hashReal = substr($partesHash[3], 22);
        echo "  - Salt (22 car): " . $salt . "\n";
        echo "  - Hash (31 car): " . $hashReal . "\n";
    }
    
    echo "\n──────────────────────────────────────────────\n";
    echo "  🔑 CONTRASEÑA INGRESADA\n";
    echo "──────────────────────────────────────────────\n\n";
    echo "Texto plano:     " . $password . "\n";
    echo "Longitud:        " . strlen($password) . " caracteres\n";
    echo "Hexadecimal:     " . bin2hex($password) . "\n";
    echo "¿Tiene espacios?: " . (strlen($password) !== strlen(trim($password)) ? 'SÍ ⚠️' : 'No') . "\n";
    echo "¿Caracteres especiales?: " . (preg_match('/[^\w]/', $password) ? 'SÍ' : 'No') . "\n\n";
    
    echo "──────────────────────────────────────────────\n";
    echo "  🧪 GENERAR HASH DE LA CONTRASEÑA INGRESADA\n";
    echo "──────────────────────────────────────────────\n\n";
    
    // Generar hash con diferentes costos
    $costs = [10, 11, 12];
    foreach ($costs as $cost) {
        $hashGenerado = password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
        echo "Costo {$cost}: {$hashGenerado}\n";
    }
    
    echo "\n──────────────────────────────────────────────\n";
    echo "  🔄 COMPARACIÓN CON password_verify()\n";
    echo "──────────────────────────────────────────────\n\n";
    
    $resultado = password_verify($password, $usuario['contrasenia']);
    
    if ($resultado) {
        echo "✅ RESULTADO: Las contraseñas COINCIDEN\n";
    } else {
        echo "❌ RESULTADO: Las contraseñas NO COINCIDEN\n\n";
        
        // Posibles causas
        echo "POSIBLES CAUSAS:\n\n";
        
        // 1. Verificar si la contraseña original era diferente
        echo "1. ¿La contraseña original era diferente?\n";
        $passwordsComunes = ['admin123', 'Admin123', 'admin', '123456', 'password', 'taller', 'admin@taller2.com'];
        echo "   Probando contraseñas comunes:\n";
        foreach ($passwordsComunes as $pass) {
            $test = password_verify($pass, $usuario['contrasenia']);
            echo "   - '" . $pass . "': " . ($test ? '✅ COINCIDE' : '❌ No') . "\n";
        }
        
        // 2. Verificar si hubo error de codificación
        echo "\n2. ¿Problema de codificación UTF-8?\n";
        echo "   Hash BD (hex): " . bin2hex($usuario['contrasenia']) . "\n";
        echo "   Password (hex): " . bin2hex($password) . "\n";
        
        // 3. Verificar si el hash está truncado
        echo "\n3. ¿Hash completo? (debe tener 60 caracteres)\n";
        echo "   Longitud del hash: " . strlen($usuario['contrasenia']) . " caracteres\n";
        echo "   Formato esperado:  \$2y\$12\$[22 caracteres salt][31 caracteres hash] = 60 caracteres\n";
        
        // 4. Generar un hash nuevo y verificar
        echo "\n4. ¿Funciona password_verify() correctamente?\n";
        $hashNuevo = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $verificacionNueva = password_verify($password, $hashNuevo);
        echo "   Hash nuevo generado: {$hashNuevo}\n";
        echo "   Verificación: " . ($verificacionNueva ? '✅ Funciona' : '❌ No funciona') . "\n";
        
        // 5. Comparar caracter por caracter
        echo "\n5. Comparación visual del hash:\n";
        echo "   BD:  " . $usuario['contrasenia'] . "\n";
        echo "   Nuevo: " . $hashNuevo . "\n";
        
        $hashBD = $usuario['contrasenia'];
        if (strlen($hashBD) === strlen($hashNuevo)) {
            $diferencias = 0;
            for ($i = 0; $i < strlen($hashBD); $i++) {
                if ($hashBD[$i] !== $hashNuevo[$i]) {
                    $diferencias++;
                    if ($diferencias <= 5) { // Mostrar primeras 5 diferencias
                        echo "   Diferencia en posición {$i}: BD='{$hashBD[$i]}' vs Nuevo='{$hashNuevo[$i]}'\n";
                    }
                }
            }
            echo "   Total diferencias: {$diferencias} de " . strlen($hashBD) . " caracteres\n";
        }
    }
}

echo "\n══════════════════════════════════════════════\n";
echo "</pre>";
exit;
*/
        if (!$usuario) {
            $_SESSION['error_login'] = 'Usuario o contrasena incorrectos.';
            $_SESSION['old_correo'] = $correo;
            $this->redirect('auth/login');
        }

        $passwordValida = password_verify($password, $usuario['contrasenia']);

        if (!$passwordValida) {
            $_SESSION['error_login'] = 'Usuario o contrasena incorrectos.';
            $_SESSION['old_correo'] = $correo;
            $this->redirect('auth/login');
        }

        if ((int) $usuario['estado_activo'] !== 1) {
            $_SESSION['error_login'] = 'Cuenta desactivada. Contacte al administrador.';
            $this->redirect('auth/login');
        }

        $_SESSION['usuario_id']   = $usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol']    = $usuario['rol'];

        $this->redirect('panel');
    }

    public function logout(): void
    {
        $this->audit('Cierre de sesion');
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $this->redirect('auth/login');
    }
}
