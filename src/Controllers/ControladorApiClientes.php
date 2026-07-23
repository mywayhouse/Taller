<?php
namespace App\Controllers;

use App\Models\Cliente;

class ControladorApiClientes extends ApiControlador
{
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    public function index(): void
    {
        $this->requireApiAccess('clientes');
        $clientes = $this->clienteModel->obtenerTodos();
        $this->jsonSuccess($clientes, count($clientes) > 0 ? '' : 'No hay clientes registrados');
    }

    public function mostrar(string $id): void
    {
        $this->requireApiAccess('clientes');
        $id = (int) $id;
        if ($id <= 0) {
            $this->jsonError('ID de cliente inválido.', 400);
        }
        $cliente = $this->clienteModel->obtenerPorId($id);
        if (!$cliente) {
            $this->jsonError('Cliente no encontrado.', 404);
        }
        $this->jsonSuccess($cliente);
    }

    public function crear(): void
    {
        $this->requireApiWriteAccess('clientes');
        $data = $this->getJsonInput();

        $errors = $this->validateRequired($data, [
            'nombre'  => 'El nombre',
            'rnt_dni' => 'El RTN/DNI',
        ]);

        if (!empty($errors)) {
            $this->jsonError('Errores de validación.', 422, $errors);
        }

        $nombre   = trim($data['nombre']);
        $telefono = trim($data['telefono'] ?? '');
        $rntDni   = trim($data['rnt_dni']);

        if (strlen($nombre) < 3) {
            $this->jsonError('Errores de validación.', 422, [
                'nombre' => 'El nombre debe tener al menos 3 caracteres.'
            ]);
        }
        if (!empty($telefono) && !preg_match('/^[\d\s\-+()]{6,20}$/', $telefono)) {
            $this->jsonError('Errores de validación.', 422, [
                'telefono' => 'El teléfono no tiene un formato válido (6-20 dígitos).'
            ]);
        }

        $id = $this->clienteModel->insertar($nombre, $telefono, $rntDni);
        $this->audit("API: Creo cliente ID {$id} - {$nombre}");
        $this->jsonCreated([
            'id_cliente' => $id,
            'nombre'     => $nombre,
            'telefono'   => $telefono,
            'rnt_dni'    => $rntDni,
        ], 'Cliente creado exitosamente.');
    }

    public function actualizar(string $id): void
    {
        $this->requireApiWriteAccess('clientes');
        $id = (int) $id;
        if ($id <= 0) {
            $this->jsonError('ID de cliente inválido.', 400);
        }

        $existente = $this->clienteModel->obtenerPorId($id);
        if (!$existente) {
            $this->jsonError('Cliente no encontrado.', 404);
        }

        $data = $this->getJsonInput();

        $errors = $this->validateRequired($data, [
            'nombre'  => 'El nombre',
            'rnt_dni' => 'El RTN/DNI',
        ]);

        if (!empty($errors)) {
            $this->jsonError('Errores de validación.', 422, $errors);
        }

        $nombre   = trim($data['nombre']);
        $telefono = trim($data['telefono'] ?? '');
        $rntDni   = trim($data['rnt_dni']);

        if (strlen($nombre) < 3) {
            $this->jsonError('Errores de validación.', 422, [
                'nombre' => 'El nombre debe tener al menos 3 caracteres.'
            ]);
        }

        $this->clienteModel->actualizar($id, $nombre, $telefono, $rntDni);
        $this->audit("API: Actualizo cliente ID {$id} - {$nombre}");
        $this->jsonSuccess([
            'id_cliente' => $id,
            'nombre'     => $nombre,
            'telefono'   => $telefono,
            'rnt_dni'    => $rntDni,
        ], 'Cliente actualizado exitosamente.');
    }

    public function eliminar(string $id): void
    {
        $this->requireApiWriteAccess('clientes');
        $id = (int) $id;
        if ($id <= 0) {
            $this->jsonError('ID de cliente inválido.', 400);
        }

        $cliente = $this->clienteModel->obtenerPorId($id);
        if (!$cliente) {
            $this->jsonError('Cliente no encontrado.', 404);
        }

        $this->clienteModel->eliminar($id);
        $this->audit("API: Desactivo cliente ID {$id} - {$cliente['nombre']}");
        $this->jsonSuccess(null, 'Cliente desactivado exitosamente.');
    }
}
