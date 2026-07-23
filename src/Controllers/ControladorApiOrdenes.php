<?php
namespace App\Controllers;

use App\Models\Orden;
use App\Models\Repuesto;

class ControladorApiOrdenes extends ApiControlador
{
    private Orden $ordenModel;
    private Repuesto $repuestoModel;

    public function __construct()
    {
        parent::__construct();
        $this->ordenModel = new Orden();
        $this->repuestoModel = new Repuesto();
    }

    public function listar(): void
    {
        $this->requireApiAccess('ordenes');
        $termino = $this->getParam('q', '');
        $estado  = $this->getParam('estado', '');
        $ordenes = ($termino !== '' || $estado !== '')
            ? $this->ordenModel->buscar($termino, $estado)
            : $this->ordenModel->obtenerTodos();
        $this->jsonSuccess($ordenes);
    }

    public function obtener(int $id): void
    {
        $this->requireApiAccess('ordenes');
        $orden = $this->ordenModel->obtenerPorId($id);
        if (!$orden) {
            $this->jsonError('Orden no encontrada.', 404);
        }
        $detalles = $this->ordenModel->listarDetalles($id);
        $orden['detalles'] = $detalles;
        $this->jsonSuccess($orden);
    }

    public function guardar(): void
    {
        $this->requireApiWriteAccess('ordenes');
        $data = $this->getJsonInput();
        $errors = $this->validateRequired($data, [
            'diagnostico_preliminar' => 'Diagnóstico',
            'fecha_ingreso'         => 'Fecha de ingreso',
            'id_recepcionista'      => 'Recepcionista',
            'id_mecanico'           => 'Mecánico',
            'placa_vehiculo'        => 'Placa',
        ]);
        $numericErrors = $this->validateNumeric($data, [
            'id_recepcionista' => 'Recepcionista',
            'id_mecanico'      => 'Mecánico',
        ]);
        $errors = array_merge($errors, $numericErrors);
        if (!empty($errors)) {
            $this->jsonError('Errores de validación', 422, $errors);
        }
        $id = $this->ordenModel->insertar(
            $data['diagnostico_preliminar'], $data['fecha_ingreso'],
            (int) $data['id_recepcionista'], (int) $data['id_mecanico'],
            $data['placa_vehiculo']
        );
        $this->audit("API: Creo orden #{$id}");
        $this->jsonCreated(['id_orden' => $id]);
    }

    public function cambiarEstado(int $id): void
    {
        $this->requireApiWriteAccess('ordenes');
        $data = $this->getJsonInput();
        $estado = $data['estado'] ?? '';
        $estadosValidos = ['RECIBIDO', 'EN PROCESO', 'LISTO', 'ENTREGADO'];
        if (!in_array($estado, $estadosValidos)) {
            $this->jsonError('Estado no válido. Use: ' . implode(', ', $estadosValidos), 422);
        }
        $this->ordenModel->actualizarEstado($id, $estado);
        $this->audit("API: Cambio estado orden #{$id} a {$estado}");
        $this->jsonSuccess(null, "Estado actualizado a {$estado}.");
    }

    public function agregarRepuesto(int $id): void
    {
        $this->requireApiWriteAccess('ordenes');
        $data = $this->getJsonInput();
        $errors = $this->validateRequired($data, ['id_repuesto' => 'Repuesto', 'cantidad' => 'Cantidad', 'precio' => 'Precio']);
        $numericErrors = $this->validateNumeric($data, ['id_repuesto' => 'Repuesto', 'cantidad' => 'Cantidad', 'precio' => 'Precio']);
        $errors = array_merge($errors, $numericErrors);
        if (!empty($errors)) {
            $this->jsonError('Errores de validación', 422, $errors);
        }
        $this->ordenModel->insertarDetalle($id, (int) $data['id_repuesto'], (int) $data['cantidad'], (float) $data['precio']);
        $this->audit("API: Agrego repuesto a orden #{$id}");
        $this->jsonCreated(null, 'Repuesto agregado a la orden.');
    }

    public function quitarRepuesto(int $idDetalle): void
    {
        $this->requireApiWriteAccess('ordenes');
        $this->ordenModel->eliminarDetalle($idDetalle);
        $this->audit("API: Quito detalle #{$idDetalle}");
        $this->jsonSuccess(null, 'Repuesto quitado.');
    }

    public function listarRepuestos(): void
    {
        $this->requireApiAccess('ordenes');
        $repuestos = $this->repuestoModel->obtenerTodos();
        $this->jsonSuccess($repuestos);
    }

    public function listarMecanicos(): void
    {
        $this->requireApiAccess('ordenes');
        $usuarios = $this->ordenModel->listarMecanicos();
        $mecanicos = array_values(array_filter($usuarios, fn($u) => $u['rol'] === 'MECANICO'));
        $this->jsonSuccess($mecanicos);
    }
}
