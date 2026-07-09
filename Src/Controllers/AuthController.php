<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller
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

        $this->redirect('dashboard');
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
