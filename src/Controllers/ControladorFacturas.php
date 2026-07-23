<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Dompdf\Dompdf;

class ControladorFacturas extends Controlador
{
    private Factura $facturaModel;

    public function __construct()
    {
        parent::__construct();
        $this->facturaModel = new Factura();
    }

    public function index(): void
    {
        $this->requireAccess('facturas');
        $facturas = $this->facturaModel->obtenerTodos();
        $data = [
            'title'       => 'Listado de Facturas',
            'pageTitle'   => 'Facturación',
            'currentPage' => 'facturas',
            'facturas'    => $facturas,
        ];
        $this->renderWithLayout('facturas/index', $data);
    }

    public function crear(): void
    {
        $this->requireAccess('facturas');
        $this->requireWriteAccess('facturas');

        $ordenesDisponibles = $this->facturaModel->obtenerOrdenesDisponibles();
        $numeroFactura = $this->facturaModel->generarNumeroFactura();

        $data = [
            'title'              => 'Nueva Factura',
            'pageTitle'          => 'Generar Factura',
            'currentPage'        => 'facturas',
            'ordenesDisponibles' => $ordenesDisponibles,
            'numeroFactura'      => $numeroFactura,
            'errores'            => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('facturas/form', $data);
    }

    public function guardar(): void
    {
        $this->requireAccess('facturas');
        $this->requireWriteAccess('facturas');

        if (!$this->isPost()) {
            $this->redirect('facturas');
        }

        $idOrden = (int) $this->getPost('id_orden', 0);
        $metodoPago = trim($this->getPost('metodo_pago', ''));
        $numeroFactura = trim($this->getPost('numero_factura', ''));
        $costoManoObra = (float) $this->getPost('costo_mano_obra', 0);

        $errores = [];
        if ($idOrden <= 0) {
            $errores[] = 'Debe seleccionar una orden.';
        }
        if (empty($metodoPago)) {
            $errores[] = 'Debe seleccionar un método de pago.';
        }
        if (empty($numeroFactura)) {
            $errores[] = 'Error al generar el número de factura.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('facturas/crear');
        }

        $repuestos = $this->facturaModel->obtenerRepuestosPorOrden($idOrden);
        $subtotalRepuestos = 0;
        foreach ($repuestos as $r) {
            $subtotalRepuestos += (float) ($r['total_linea'] ?? 0);
        }

        $subtotal = $subtotalRepuestos + $costoManoObra;
        $isv = round($subtotal * 0.15, 2);
        $totalPagar = round($subtotal + $isv, 2);

        $this->facturaModel->insertar(
            $numeroFactura,
            $costoManoObra,
            $subtotalRepuestos,
            $isv,
            $totalPagar,
            $idOrden,
            $metodoPago
        );

        $this->audit("Generó factura {$numeroFactura} para orden #{$idOrden}");

        $_SESSION['mensaje'] = "Factura {$numeroFactura} generada exitosamente.";
        $this->redirect('facturas');
    }

    public function ver(int $id): void
    {
        $this->requireAccess('facturas');
        $factura = $this->facturaModel->obtenerPorId($id);

        if (!$factura) {
            $this->showError(404, 'Factura no encontrada.');
            return;
        }

        $repuestos = $this->facturaModel->obtenerRepuestosPorOrden($factura['id_orden']);

        $data = [
            'title'       => 'Factura ' . ($factura['numero_factura'] ?? ''),
            'pageTitle'   => 'Factura #' . ($factura['numero_factura'] ?? ''),
            'currentPage' => 'facturas',
            'factura'     => $factura,
            'repuestos'   => $repuestos,
        ];
        $this->renderWithLayout('facturas/ver', $data);
    }

    public function anular(int $id): void
    {
        $this->requireAccess('facturas');
        $this->requireWriteAccess('facturas');

        $factura = $this->facturaModel->obtenerPorId($id);
        if (!$factura) {
            $this->showError(404, 'Factura no encontrada.');
            return;
        }

        $this->facturaModel->anular($id);
        $this->audit("Anuló factura {$factura['numero_factura']}");
        $_SESSION['mensaje'] = "Factura {$factura['numero_factura']} anulada.";
        $this->redirect('facturas');
    }

    public function obtenerDatosOrdenAjax(): void
    {
        $this->requireAccess('facturas');
        $idOrden = (int) ($this->getGet('id_orden', 0));

        if ($idOrden <= 0) {
            $this->jsonResponse(['error' => 'ID de orden inválido'], 400);
        }

        $ordenModel = new \App\Models\Orden();
        $orden = $ordenModel->obtenerPorId($idOrden);

        if (!$orden) {
            $this->jsonResponse(['error' => 'Orden no encontrada'], 404);
        }

        $repuestos = $this->facturaModel->obtenerRepuestosPorOrden($idOrden);

        $this->jsonResponse([
            'id_orden'           => $orden['id_orden'],
            'fecha_ingreso'      => $orden['fecha_ingreso'],
            'diagnostico'        => $orden['diagnostico_preliminar'],
            'costo_mano_obra'    => (float) ($orden['costo_mano_obra'] ?? 0),
            'cliente_nombre'     => $orden['nombre_cliente'] ?? '',
            'cliente_telefono'   => $orden['cliente_telefono'] ?? '',
            'rnt_dni'            => $orden['rnt_dni'] ?? '',
            'placa'              => $orden['placa'] ?? '',
            'marca'              => $orden['marca'] ?? '',
            'modelo'             => $orden['modelo'] ?? '',
            'recepcionista'      => $orden['recepcionista'] ?? '',
            'mecanico'           => $orden['mecanico'] ?? '',
            'repuestos'          => $repuestos,
        ]);
    }

    public function pdf(int $id): void
    {
        $this->requireAccess('facturas');
        $factura = $this->facturaModel->obtenerPorId($id);

        if (!$factura) {
            $this->showError(404, 'Factura no encontrada.');
            return;
        }

        $repuestos = $this->facturaModel->obtenerRepuestosPorOrden($factura['id_orden']);

        ob_start();
        require_once VIEWS . '/facturas/ver.php';
        $content = ob_get_clean();

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">';
        $html .= '<title>Factura ' . htmlspecialchars($factura['numero_factura']) . '</title>';
        $html .= '<style>
            body { font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; color: #333; margin: 20px; }
            .factura-print { max-width: 800px; margin: 0 auto; padding: 20px; }
            .factura-header { display: flex; justify-content: space-between; padding-bottom: 15px; margin-bottom: 15px; border-bottom: 2px solid #3b82f6; }
            .factura-empresa h2 { color: #3b82f6; margin: 0 0 5px; }
            .factura-empresa p { color: #666; font-size: 12px; margin: 2px 0; }
            .factura-titulo { text-align: right; }
            .factura-titulo h1 { color: #3b82f6; font-size: 24px; margin: 0 0 5px; }
            .badge-active { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 11px; }
            .badge-inactive { background: #fce4ec; color: #c62828; padding: 2px 8px; border-radius: 4px; font-size: 11px; }
            .factura-seccion { border: 1px solid #ddd; border-radius: 4px; padding: 12px; margin-bottom: 12px; }
            .factura-seccion h3 { font-size: 14px; color: #3b82f6; margin: 0 0 8px; padding-bottom: 4px; border-bottom: 1px solid #ddd; }
            table { width: 100%; border-collapse: collapse; margin: 8px 0; }
            th { background: #f1f5f9; text-align: left; padding: 6px 8px; font-size: 12px; border-bottom: 2px solid #ddd; }
            td { padding: 6px 8px; border-bottom: 1px solid #eee; }
            .text-center { text-align: center; }
            .factura-totales { margin-top: 15px; max-width: 350px; margin-left: auto; }
            .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
            .total-final { border-top: 2px solid #3b82f6; padding-top: 8px; font-size: 16px; font-weight: bold; color: #3b82f6; }
        </style></head><body>';
        $html .= '<div class="factura-print">' . $content . '</div>';
        $html .= '</body></html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("factura_{$factura['numero_factura']}.pdf", [
            'Attachment' => true,
        ]);
        exit;
    }

    private function showError(int $code, string $message): void
    {
        http_response_code($code);
        $errorView = VIEWS . "/errors/{$code}.php";
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "<h1>Error {$code}</h1><p>{$message}</p>";
        }
        exit;
    }
}
