<?php
namespace App\Controllers;

use App\Models\Usuario;

class ControladorApiUsuarios extends ApiControlador
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    public function listar(): void
    {
        $this->requireApiAccess('usuarios');
        $termino = $this->getParam('q', '');
        $usuarios = $termino !== ''
            ? $this->usuarioModel->buscar($termino)
            : $this->usuarioModel->obtenerTodos();
        $this->jsonSuccess($usuarios);
    }

    public function obtener(int $id): void
    {
        $this->requireApiAccess('usuarios');
        $usuario = $this->usuarioModel->obtenerPorId($id);
        if (!$usuario) {
            $this->jsonError('Usuario no encontrado.', 404);
        }
        $this->jsonSuccess($usuario);
    }

    public function guardar(): void
    {
        $this->requireApiWriteAccess('usuarios');
        $data = $this->getJsonInput();
        $errors = $this->validateRequired($data, ['nombre' => 'Nombre', 'correo' => 'Correo', 'contrasenia' => 'Contraseña', 'rol' => 'Rol']);
        if (!empty($errors)) {
            $this->jsonError('Errores de validación', 422, $errors);
        }
        $emailError = $this->validateEmail($data, 'correo', 'Correo');
        if ($emailError) {
            $this->jsonError($emailError, 422);
        }
        $id = $this->usuarioModel->insertar($data['nombre'], $data['correo'], password_hash($data['contrasenia'], PASSWORD_DEFAULT), $data['rol']);
        $this->audit("API: Creo usuario {$data['nombre']}");
        $this->jsonCreated(['id_usuario' => $id]);
    }

    public function actualizar(int $id): void
    {
        $this->requireApiWriteAccess('usuarios');
        $data = $this->getJsonInput();
        $contrasenia = !empty($data['contrasenia']) ? password_hash($data['contrasenia'], PASSWORD_DEFAULT) : '';
        $this->usuarioModel->actualizar($id, $data['nombre'] ?? '', $data['correo'] ?? '', $contrasenia, $data['rol'] ?? '');
        $this->audit("API: Actualizo usuario #{$id}");
        $this->jsonSuccess(null, 'Usuario actualizado.');
    }

    public function eliminar(int $id): void
    {
        $this->requireApiWriteAccess('usuarios');
        $this->usuarioModel->eliminar($id);
        $this->audit("API: Desactivo usuario #{$id}");
        $this->jsonSuccess(null, 'Usuario desactivado.');
    }
}
