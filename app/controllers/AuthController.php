<?php
// ============================================================
// AuthController.php — Autenticación de usuarios
// ============================================================
// Maneja el inicio y cierre de sesión, así como el registro
// de logs de acceso mediante el SP sp_registrar_log.
// ============================================================

namespace app\controllers;

use Controller;
use app\models\Usuario;

class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function login(): void
    {
        // Si ya hay sesión, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }

        $data = [
            'title' => 'Iniciar Sesión',
            'error' => $_SESSION['error_login'] ?? null,
            'oldCorreo' => $_SESSION['old_correo'] ?? '',
        ];

        unset($_SESSION['error_login'], $_SESSION['old_correo']);

        // El login usa un layout especial (sin sidebar ni header)
        $this->render('auth/login', $data);
    }

    /**
     * Procesa el formulario de login.
     */
    public function authenticate(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
        }

        $correo    = trim($this->getPost('correo', ''));
        $password  = $this->getPost('contrasenia', '');

        // Validar campos vacíos
        if (empty($correo) || empty($password)) {
            $_SESSION['error_login'] = 'Todos los campos son obligatorios.';
            $_SESSION['old_correo'] = $correo;
            $this->redirect('auth/login');
        }

        // Buscar usuario por correo (SP: sp_obtener_usuario_por_correo)
        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);

        // --------------------------------------------------
        // Verificar contraseña
        // --------------------------------------------------
        // Las contraseñas en Base_taller.sql están en texto
        // plano. Se compara directamente. En producción,
        // deben almacenarse con password_hash(bcrypt) y
        // usar password_verify(). Ejecute el script
        // helpers/hash_passwords.php para migrar.
        // --------------------------------------------------
        $hash = $usuario['contrasenia'];
        $passwordValida = password_verify($password, $hash)
                       || ($password === $hash); // fallback texto plano

        if (!$usuario || !$passwordValida) {
            $_SESSION['error_login'] = 'Usuario o contraseña incorrectos.';
            $_SESSION['old_correo'] = $correo;
            $this->redirect('auth/login');
        }

        // Verificar si el usuario está activo
        if ((int) $usuario['estado_activo'] !== 1) {
            $_SESSION['error_login'] = 'Cuenta desactivada. Contacte al administrador.';
            $this->redirect('auth/login');
        }

        // --- Inicio de sesión exitoso ---
        $_SESSION['usuario_id']   = $usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol']    = $usuario['rol'];

        // Registrar log del acceso usando AuditHelper
        $this->audit('Inicio de sesión');

        $this->redirect('dashboard');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(): void
    {
        // Registrar log antes de destruir la sesión
        $this->audit('Cierre de sesión');

        // Limpiar sesión
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
