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
        $termino = trim($this->getGet('q', ''));
        if ($termino !== '') {
            $facturas = $this->facturaModel->buscar($termino);
        } else {
            $facturas = $this->facturaModel->obtenerTodos();
        }
        $data = [
            'title'       => 'Listado de Facturas',
            'pageTitle'   => 'Facturación',
            'currentPage' => 'facturas',
            'facturas'    => $facturas,
            'q'           => $termino,
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
        require_once VIEWS . '/facturas/contenido.php';
        $content = ob_get_clean();

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">';
        $html .= '<title>Factura ' . htmlspecialchars($factura['numero_factura']) . '</title>';
        $html .= '<style>
            @page { margin: 12mm; }
            body { font-family: "Helvetica", Arial, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 0; }
            .factura-page { max-width: 750px; margin: 0 auto; padding: 5px 0; }
            
            .encabezado { text-align: center; margin-bottom: 12px; }
            .empresa-nombre { font-size: 22px; font-weight: 900; color: #1a3a6b; letter-spacing: 3px; margin-bottom: 6px; text-transform: uppercase; }
            .empresa { font-size: 10px; color: #1a3a6b; line-height: 1.6; }
            
            .linea { border-top: 2px dashed #1a3a6b; margin: 10px 0; }
            
            .bloque-info { width: 100%; border-collapse: collapse; margin: 12px 0; }
            .bloque-info td { vertical-align: top; padding: 0; }
            .col-izquierda { width: 45%; }
            .col-derecha { width: 55%; }
            .label { font-weight: 700; font-size: 10px; color: #1a3a6b; margin-bottom: 5px; letter-spacing: 0.5px; }
            .valor { font-weight: 700; font-size: 12px; margin-bottom: 4px; }
            .detalle { font-size: 10px; color: #444; line-height: 1.6; }
            .datos-tabla { width: 100%; border-collapse: collapse; }
            .datos-tabla td { padding: 1px 0; font-size: 10px; }
            .dt-label { font-weight: 700; width: 100px; color: #1a1a1a; }
            
            .items-tabla { width: 100%; border-collapse: collapse; margin: 12px 0; }
            .items-tabla th { background: #1a3a6b; color: #fff; font-size: 10px; font-weight: 700; padding: 7px 8px; letter-spacing: 0.5px; }
            .items-tabla td { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #e0ddd8; }
            .th-cant, .td-cant { width: 10%; text-align: center; }
            .th-desc { width: 50%; }
            .th-precio, .td-precio { width: 20%; text-align: right; }
            .th-importe, .td-importe { width: 20%; text-align: right; }
            
            .totales { max-width: 300px; margin-left: auto; margin-top: 10px; }
            .total-item { display: flex; justify-content: space-between; padding: 2px 0; font-size: 11px; }
            .total-final { border-top: 2px solid #1a3a6b; margin-top: 4px; padding-top: 5px; font-size: 15px; font-weight: 700; color: #1a3a6b; }
        </style></head><body>';
        $html .= '<div class="factura-page">' . $content . '</div>';
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
