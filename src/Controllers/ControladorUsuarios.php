<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Usuario;

class ControladorUsuarios extends Controlador
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void
    {
        $this->requireAccess('usuarios');
        $termino = trim($this->getGet('q', ''));
        $rol = trim($this->getGet('rol', ''));
        if ($termino !== '' || $rol !== '') {
            $usuarios = $this->usuarioModel->buscar($termino, $rol);
        } else {
            $usuarios = $this->usuarioModel->obtenerTodos();
        }
        $data = [
            'title'       => 'Listado de Usuarios',
            'pageTitle'   => 'Usuarios',
            'currentPage' => 'usuarios',
            'usuarios'    => $usuarios,
            'q'           => $termino,
            'rolFiltro'   => $rol,
        ];
        $this->renderWithLayout('usuarios/index', $data);
    }

    public function crear(): void
    {
        $this->requireAccess('usuarios');
        $this->requireWriteAccess('usuarios');
        $data = [
            'title'       => 'Nuevo Usuario',
            'pageTitle'   => 'Registrar Usuario',
            'currentPage' => 'usuarios',
            'usuario'     => ['id_usuario' => '', 'nombre' => '', 'correo' => '', 'rol' => ''],
            'errores'     => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('usuarios/form', $data);
    }

    public function guardar(): void
    {
        $this->requireAccess('usuarios');
        $this->requireWriteAccess('usuarios');
        if (!$this->isPost()) {
            $this->redirect('usuarios');
        }
        $nombre      = trim($this->getPost('nombre', ''));
        $correo      = trim($this->getPost('correo', ''));
        $contrasenia = $this->getPost('contrasenia', '');
        $rol         = trim($this->getPost('rol', ''));
        $errores = [];
        if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
        if (empty($correo)) $errores[] = 'El correo es obligatorio.';
        if (strlen($contrasenia) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        if (!preg_match('/[A-Z]/', $contrasenia)) $errores[] = 'La contraseña debe contener al menos una mayúscula.';
        if (!preg_match('/[0-9]/', $contrasenia)) $errores[] = 'La contraseña debe contener al menos un número.';
        if (empty($rol)) $errores[] = 'El rol es obligatorio.';
        if (!in_array($rol, ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'])) $errores[] = 'Rol inválido.';
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('usuarios/crear');
        }
        $existente = $this->usuarioModel->obtenerPorCorreo($correo);
        if ($existente) {
            $_SESSION['errores'] = ['El correo ya está registrado.'];
            $this->redirect('usuarios/crear');
        }
        $hash = password_hash($contrasenia, PASSWORD_DEFAULT);
        $this->usuarioModel->insertar($nombre, $correo, $hash, $rol);
        $this->audit("Creo usuario: {$nombre}");
        $_SESSION['mensaje'] = 'Usuario creado exitosamente.';
        $this->redirect('usuarios');
    }

    public function editar(int $id): void
    {
        $this->requireAccess('usuarios');
        $this->requireWriteAccess('usuarios');
        $usuario = $this->usuarioModel->obtenerPorId($id);
        if (!$usuario) {
            $this->showError(404, 'Usuario no encontrado.');
            return;
        }
        $data = [
            'title'       => 'Editar Usuario',
            'pageTitle'   => 'Editar Usuario',
            'currentPage' => 'usuarios',
            'usuario'     => $usuario,
            'errores'     => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('usuarios/form', $data);
    }

    public function actualizar(int $id): void
    {
        $this->requireAccess('usuarios');
        $this->requireWriteAccess('usuarios');
        if (!$this->isPost()) {
            $this->redirect('usuarios');
        }
        $usuario = $this->usuarioModel->obtenerPorId($id);
        if (!$usuario) {
            $this->showError(404, 'Usuario no encontrado.');
            return;
        }
        $nombre = trim($this->getPost('nombre', ''));
        $correo = trim($this->getPost('correo', ''));
        $rol    = trim($this->getPost('rol', ''));
        $errores = [];
        if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
        if (empty($correo)) $errores[] = 'El correo es obligatorio.';
        if (empty($rol)) $errores[] = 'El rol es obligatorio.';
        if (!in_array($rol, ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'])) $errores[] = 'Rol inválido.';
        $existente = $this->usuarioModel->obtenerPorCorreo($correo);
        if ($existente && $existente['id_usuario'] != $id) {
            $errores[] = 'El correo ya está en uso por otro usuario.';
        }
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('usuarios/editar/' . $id);
        }
        $this->usuarioModel->actualizar($id, $nombre, $correo, $rol);
        $contrasenia = $this->getPost('contrasenia', '');
        if (!empty($contrasenia)) {
            if (strlen($contrasenia) < 6) {
                $_SESSION['errores'] = ['La contraseña debe tener al menos 6 caracteres.'];
                $this->redirect('usuarios/editar/' . $id);
            }
            if (!preg_match('/[A-Z]/', $contrasenia)) {
                $_SESSION['errores'] = ['La contraseña debe contener al menos una mayúscula.'];
                $this->redirect('usuarios/editar/' . $id);
            }
            if (!preg_match('/[0-9]/', $contrasenia)) {
                $_SESSION['errores'] = ['La contraseña debe contener al menos un número.'];
                $this->redirect('usuarios/editar/' . $id);
            }
            $hash = password_hash($contrasenia, PASSWORD_DEFAULT);
            $this->usuarioModel->actualizarContrasenia($id, $hash);
        }
        $this->audit("Actualizo usuario #{$id}");
        $_SESSION['mensaje'] = 'Usuario actualizado exitosamente.';
        $this->redirect('usuarios');
    }

    public function eliminar(int $id): void
    {
        $this->requireAccess('usuarios');
        $this->requireWriteAccess('usuarios');
        if (!$this->isPost()) {
            $this->redirect('usuarios');
        }
        $usuario = $this->usuarioModel->obtenerPorId($id);
        if (!$usuario) {
            $this->showError(404, 'Usuario no encontrado.');
            return;
        }
        $idSesion = (int)($_SESSION['usuario_id'] ?? 0);
        if ($id === $idSesion) {
            $_SESSION['errores'] = ['No puedes desactivarte a ti mismo.'];
            $this->redirect('usuarios');
        }
        $this->usuarioModel->eliminar($id);
        $this->audit("Desactivo usuario #{$id}");
        $_SESSION['mensaje'] = 'Usuario desactivado exitosamente.';
        $this->redirect('usuarios');
    }
}
