<?php
namespace App\Controllers;

use App\Models\Vehiculo;

class ControladorApiVehiculos extends ApiControlador
{
    private Vehiculo $vehiculoModel;

    public function __construct()
    {
        $this->vehiculoModel = new Vehiculo();
    }

    public function index(): void
    {
        $this->requireApiAccess('vehiculos');
        $vehiculos = $this->vehiculoModel->obtenerTodos();
        $this->jsonSuccess($vehiculos, count($vehiculos) > 0 ? '' : 'No hay vehículos registrados');
    }

    public function mostrar(string $placa): void
    {
        $this->requireApiAccess('vehiculos');
        if (empty(trim($placa))) {
            $this->jsonError('Placa del vehículo inválida.', 400);
        }
        $vehiculo = $this->vehiculoModel->obtenerPorPlaca(strtoupper($placa));
        if (!$vehiculo) {
            $this->jsonError('Vehículo no encontrado.', 404);
        }
        $this->jsonSuccess($vehiculo);
    }

    public function crear(): void
    {
        $this->requireApiWriteAccess('vehiculos');
        $data = $this->getJsonInput();

        $errors = $this->validateRequired($data, [
            'placa'      => 'La placa',
            'marca'      => 'La marca',
            'modelo'     => 'El modelo',
            'tipo'       => 'El tipo',
            'id_cliente' => 'El ID del cliente',
        ]);

        $errorsNumeric = $this->validateNumeric($data, [
            'anio'       => 'El año',
            'id_cliente' => 'El ID del cliente',
        ]);

        $allErrors = array_merge($errors, $errorsNumeric);
        if (!empty($allErrors)) {
            $this->jsonError('Errores de validación.', 422, $allErrors);
        }

        $placa     = strtoupper(trim($data['placa']));
        $marca     = trim($data['marca']);
        $modelo    = trim($data['modelo']);
        $anio      = (int) $data['anio'];
        $tipo      = trim($data['tipo']);
        $idCliente = (int) $data['id_cliente'];
        $cilindraje = $tipo === 'Motocicleta' ? (int) ($data['cilindraje'] ?? 0) : null;
        $tipoMoto   = $tipo === 'Motocicleta' ? trim($data['tipo_moto'] ?? '') : null;

        if (!preg_match('/^[A-Z0-9\-]{3,15}$/', $placa)) {
            $this->jsonError('Errores de validación.', 422, [
                'placa' => 'La placa debe tener entre 3 y 15 caracteres alfanuméricos.'
            ]);
        }
        if ($anio < 1950 || $anio > ((int) date('Y')) + 1) {
            $this->jsonError('Errores de validación.', 422, [
                'anio' => "El año debe estar entre 1950 y " . (date('Y') + 1) . "."
            ]);
        }
        if ($tipo === 'Motocicleta') {
            if (empty($cilindraje) || $cilindraje < 50)
                $allErrors['cilindraje'] = 'El cilindraje debe ser al menos 50 CC.';
            if (empty($tipoMoto))
                $allErrors['tipo_moto'] = 'El tipo de motocicleta es obligatorio.';
            if (!empty($allErrors))
                $this->jsonError('Errores de validación.', 422, $allErrors);
        }

        $existente = $this->vehiculoModel->obtenerPorPlaca($placa);
        if ($existente) {
            $this->jsonError('Errores de validación.', 422, [
                'placa' => 'Ya existe un vehículo registrado con esa placa.'
            ]);
        }

        $this->vehiculoModel->insertar($placa, $marca, $modelo, $anio, $tipo, $idCliente, $cilindraje, $tipoMoto);
        $this->audit("API: Creo vehículo placa {$placa}");
        $this->jsonCreated([
            'placa'       => $placa,
            'marca'       => $marca,
            'modelo'      => $modelo,
            'anio'        => $anio,
            'tipo'        => $tipo,
            'id_cliente'  => $idCliente,
            'cilindraje'  => $cilindraje,
            'tipo_moto'   => $tipoMoto,
        ], 'Vehículo creado exitosamente.');
    }

    public function actualizar(string $placa): void
    {
        $this->requireApiWriteAccess('vehiculos');
        $placa = strtoupper(trim($placa));
        if (empty($placa)) {
            $this->jsonError('Placa del vehículo inválida.', 400);
        }

        $existente = $this->vehiculoModel->obtenerPorPlaca($placa);
        if (!$existente) {
            $this->jsonError('Vehículo no encontrado.', 404);
        }

        $data = $this->getJsonInput();

        $errors = $this->validateRequired($data, [
            'marca'      => 'La marca',
            'modelo'     => 'El modelo',
            'tipo'       => 'El tipo',
            'id_cliente' => 'El ID del cliente',
        ]);

        $errorsNumeric = $this->validateNumeric($data, [
            'anio'       => 'El año',
            'id_cliente' => 'El ID del cliente',
        ]);

        $allErrors = array_merge($errors, $errorsNumeric);
        if (!empty($allErrors)) {
            $this->jsonError('Errores de validación.', 422, $allErrors);
        }

        $marca     = trim($data['marca']);
        $modelo    = trim($data['modelo']);
        $anio      = (int) $data['anio'];
        $tipo      = trim($data['tipo']);
        $idCliente = (int) $data['id_cliente'];
        $cilindraje = $tipo === 'Motocicleta' ? (int) ($data['cilindraje'] ?? 0) : null;
        $tipoMoto   = $tipo === 'Motocicleta' ? trim($data['tipo_moto'] ?? '') : null;

        if ($anio < 1950 || $anio > ((int) date('Y')) + 1) {
            $this->jsonError('Errores de validación.', 422, [
                'anio' => "El año debe estar entre 1950 y " . (date('Y') + 1) . "."
            ]);
        }
        if ($tipo === 'Motocicleta') {
            $motoErrors = [];
            if (empty($cilindraje) || $cilindraje < 50)
                $motoErrors['cilindraje'] = 'El cilindraje debe ser al menos 50 CC.';
            if (empty($tipoMoto))
                $motoErrors['tipo_moto'] = 'El tipo de motocicleta es obligatorio.';
            if (!empty($motoErrors))
                $this->jsonError('Errores de validación.', 422, $motoErrors);
        }

        $this->vehiculoModel->actualizar($placa, $marca, $modelo, $anio, $tipo, $idCliente, $cilindraje, $tipoMoto);
        $this->audit("API: Actualizo vehículo placa {$placa}");
        $this->jsonSuccess([
            'placa'       => $placa,
            'marca'       => $marca,
            'modelo'      => $modelo,
            'anio'        => $anio,
            'tipo'        => $tipo,
            'id_cliente'  => $idCliente,
            'cilindraje'  => $cilindraje,
            'tipo_moto'   => $tipoMoto,
        ], 'Vehículo actualizado exitosamente.');
    }

    public function eliminar(string $placa): void
    {
        $this->requireApiWriteAccess('vehiculos');
        $placa = strtoupper(trim($placa));
        if (empty($placa)) {
            $this->jsonError('Placa del vehículo inválida.', 400);
        }

        $vehiculo = $this->vehiculoModel->obtenerPorPlaca($placa);
        if (!$vehiculo) {
            $this->jsonError('Vehículo no encontrado.', 404);
        }

        $this->vehiculoModel->eliminar($placa);
        $this->audit("API: Eliminó vehículo placa {$placa}");
        $this->jsonSuccess(null, 'Vehículo eliminado exitosamente.');
    }
}
