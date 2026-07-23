<?php
namespace App\Controllers;

use App\Models\Factura;
use App\Models\Orden;

class ControladorApiFacturas extends ApiControlador
{
    private Factura $facturaModel;

    public function __construct()
    {
        parent::__construct();
        $this->facturaModel = new Factura();
    }

    public function listar(): void
    {
        $this->requireApiAccess('facturas');
        $termino  = $this->getParam('q', '');
        $facturas = $termino !== ''
            ? $this->facturaModel->buscar($termino)
            : $this->facturaModel->obtenerTodos();
        $this->jsonSuccess($facturas);
    }

    public function obtener(int $id): void
    {
        $this->requireApiAccess('facturas');
        $factura = $this->facturaModel->obtenerPorId($id);
        if (!$factura) {
            $this->jsonError('Factura no encontrada.', 404);
        }
        $this->jsonSuccess($factura);
    }

    public function guardar(): void
    {
        $this->requireApiWriteAccess('facturas');
        $data = $this->getJsonInput();
        $errors = $this->validateRequired($data, [
            'numero_factura'     => 'Número de factura',
            'subtotal_mano_obra' => 'Subtotal mano de obra',
            'subtotal_repuestos' => 'Subtotal repuestos',
            'isv'                => 'ISV',
            'total_pagar'        => 'Total a pagar',
            'id_orden'           => 'Orden',
        ]);
        $numericErrors = $this->validateNumeric($data, [
            'subtotal_mano_obra' => 'Subtotal mano de obra',
            'subtotal_repuestos' => 'Subtotal repuestos',
            'isv'                => 'ISV',
            'total_pagar'        => 'Total a pagar',
            'id_orden'           => 'Orden',
        ]);
        $errors = array_merge($errors, $numericErrors);
        if (!empty($errors)) {
            $this->jsonError('Errores de validación', 422, $errors);
        }
        $id = $this->facturaModel->insertar(
            $data['numero_factura'], (float) $data['subtotal_mano_obra'],
            (float) $data['subtotal_repuestos'], (float) $data['isv'],
            (float) $data['total_pagar'], (int) $data['id_orden']
        );
        $this->audit("API: Genero factura {$data['numero_factura']}");
        $this->jsonCreated(['id_factura' => $id]);
    }

    public function generarNumero(): void
    {
        $this->requireApiAccess('facturas');
        $numero = $this->facturaModel->generarNumeroFactura();
        $this->jsonSuccess(['numero_factura' => $numero]);
    }

    public function ordenesListas(): void
    {
        $this->requireApiAccess('facturas');
        $ordenModel = new Orden();
        $todas = $ordenModel->obtenerTodos();
        $listas = array_values(array_filter($todas, fn($o) => $o['estado'] === 'LISTO'));
        $this->jsonSuccess($listas);
    }
}
