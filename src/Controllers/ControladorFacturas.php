<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Usuario;
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

        $usuarioModel = new Usuario();
        $recepcionistas = $usuarioModel->obtenerPorRol('RECEPCIONISTA');
        $mecanicos = $usuarioModel->obtenerPorRol('MECANICO');

        $data = [
            'title'              => 'Nueva Factura',
            'pageTitle'          => 'Generar Factura',
            'currentPage'        => 'facturas',
            'ordenesDisponibles' => $ordenesDisponibles,
            'numeroFactura'      => $numeroFactura,
            'recepcionistas'     => $recepcionistas,
            'mecanicos'          => $mecanicos,
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
        $serviciosJson = trim($this->getPost('servicios_json', '[]'));
        $idRecepcionista = $this->getPost('id_recepcionista_factura', '') !== '' ? (int) $this->getPost('id_recepcionista_factura', '') : null;
        $idMecanico = $this->getPost('id_mecanico_factura', '') !== '' ? (int) $this->getPost('id_mecanico_factura', '') : null;

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

        $servicios = json_decode($serviciosJson, true) ?? [];
        $subtotalServicios = 0;
        foreach ($servicios as $s) {
            $subtotalServicios += (float) ($s['precio'] ?? 0);
        }

        $repuestos = $this->facturaModel->obtenerRepuestosPorOrden($idOrden);
        $subtotalRepuestos = 0;
        foreach ($repuestos as $r) {
            $subtotalRepuestos += (float) ($r['total_linea'] ?? 0);
        }

        $subtotal = $subtotalRepuestos + $subtotalServicios;
        $isv = round($subtotal * 0.15, 2);
        $totalPagar = round($subtotal + $isv, 2);

        $this->facturaModel->insertar(
            $numeroFactura,
            $subtotalServicios,
            $subtotalRepuestos,
            $isv,
            $totalPagar,
            $idOrden,
            $metodoPago,
            $serviciosJson,
            $idRecepcionista,
            $idMecanico
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

    public function eliminar(int $id): void
    {
        $this->requireAccess('facturas');
        $this->requireWriteAccess('facturas');

        $factura = $this->facturaModel->obtenerPorId($id);
        if (!$factura) {
            $this->showError(404, 'Factura no encontrada.');
            return;
        }

        $this->facturaModel->eliminar($id);
        $this->audit("Eliminó factura {$factura['numero_factura']}");
        $_SESSION['mensaje'] = "Factura {$factura['numero_factura']} eliminada permanentemente.";
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

        $servicios = json_decode($factura['servicios_json'] ?? '[]', true) ?? [];

        $azul = '#1a3a6b';

        $recepcionista = $factura['factura_recepcionista_nombre'] ?? $factura['recepcionista_nombre'] ?? '—';
        $mecanico = $factura['factura_mecanico_nombre'] ?? $factura['mecanico_nombre'] ?? '—';

        $itemsHtml = '';
        foreach ($repuestos as $r) {
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td style="text-align:center;">' . (int)$r['cantidad'] . '</td>';
            $itemsHtml .= '<td>' . htmlspecialchars($r['repuesto_nombre']) . '</td>';
            $itemsHtml .= '<td style="text-align:right;">L. ' . number_format($r['precio_unitario_historico'], 2) . '</td>';
            $itemsHtml .= '<td style="text-align:right;">L. ' . number_format($r['total_linea'], 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        foreach ($servicios as $s) {
            $precio = (float)($s['precio'] ?? 0);
            $desc = htmlspecialchars($s['descripcion'] ?? 'Servicio');
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td style="text-align:center;">1</td>';
            $itemsHtml .= '<td>' . $desc . '</td>';
            $itemsHtml .= '<td style="text-align:right;">L. ' . number_format($precio, 2) . '</td>';
            $itemsHtml .= '<td style="text-align:right;">L. ' . number_format($precio, 2) . '</td>';
            $itemsHtml .= '</tr>';
        }
        if (empty($repuestos) && empty($servicios)) {
            $itemsHtml = '<tr><td colspan="4" style="text-align:center;padding:10px;">Sin ítems</td></tr>';
        }

        $subtotal = $factura['subtotal_repuestos'] + $factura['subtotal_mano_obra'];
        $estadoTexto = ($factura['estado_activo'] ?? 1) ? 'ACTIVA' : 'ANULADA';
        $clienteNombre = htmlspecialchars($factura['cliente_nombre'] ?? '—');
        $clienteRtn = htmlspecialchars($factura['rnt_dni'] ?? '—');
        $clienteTel = htmlspecialchars($factura['cliente_telefono'] ?? '—');
        $numFactura = htmlspecialchars($factura['numero_factura']);
        $fecha = htmlspecialchars($factura['fecha_emision'] ?? '');
        $fechaIngreso = htmlspecialchars($factura['fecha_ingreso'] ?? '');
        $ordenId = htmlspecialchars($factura['id_orden']);
        $metodoPago = htmlspecialchars($factura['metodo_pago'] ?? '—');
        $empresa = htmlspecialchars(EMPRESA_NOMBRE);
        $direccion = htmlspecialchars(EMPRESA_DIRECCION);
        $tel = htmlspecialchars(EMPRESA_TELEFONO);
        $rtn = htmlspecialchars(EMPRESA_RTN);

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">';
        $html .= '<title>Factura ' . $numFactura . '</title>';
        $html .= '<style>
            @page { margin: 15px; }
            body { font-family: "Segoe UI", Arial, Helvetica, sans-serif; font-size: 11px; color: #333; margin: 0; }
            .page { padding: 5px 12px; }
            .center { text-align: center; }
            .dashed { border: none; border-top: 2px dashed ' . $azul . '; margin: 10px 0; }
            .dashed-thin { border: none; border-top: 1px dashed ' . $azul . '; margin: 6px 0; }

            .company-name { font-size: 16px; font-weight: 700; color: ' . $azul . '; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
            .company-info { font-size: 10px; color: #555; margin: 1px 0; }
            .info-section { width: 100%; margin: 8px 0; }
            .info-section td { vertical-align: top; padding: 0 10px; width: 50%; }
            .info-title { font-size: 10px; font-weight: 700; color: ' . $azul . '; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 4px; }
            .info-line { font-size: 11px; color: #333; margin: 2px 0; }
            .table-items { width: 100%; border-collapse: collapse; margin: 6px 0; }
            .table-items th { background: ' . $azul . '; color: #fff; font-size: 10px; font-weight: 600; padding: 5px 8px; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; }
            .table-items th.left { text-align: left; }
            .table-items td { padding: 4px 8px; font-size: 10px; border-bottom: 1px solid #ddd; }
            .totals { width: 280px; margin-left: auto; margin-top: 6px; }
            .totals td { padding: 3px 8px; font-size: 11px; }
            .totals .label { text-align: right; }
            .totals .value { text-align: right; font-weight: 600; }
            .totals .total-label { text-align: right; font-size: 15px; font-weight: 900; color: ' . $azul . '; }
            .totals .total-value { text-align: right; font-size: 16px; font-weight: 900; color: ' . $azul . '; }
            .totals hr { border: none; border-top: 2px solid ' . $azul . '; margin: 2px 0; }
        </style></head><body>';
        $html .= '<div class="page">';

        // -- HEADER --
        $html .= '<div class="center">';
        $html .= '<div class="company-name">' . $empresa . '</div>';
        $html .= '<div class="company-info">' . $direccion . '</div>';
        $html .= '<div class="company-info">Tel: ' . $tel . ' &nbsp;|&nbsp; RTN: ' . $rtn . '</div>';
        $html .= '</div>';

        $html .= '<hr class="dashed">';

        // -- INFO BLOCK --
        $html .= '<table class="info-section">';
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '<div class="info-title">FACTURAR A</div>';
        $html .= '<div class="info-line"><strong>' . $clienteNombre . '</strong></div>';
        $html .= '<div class="info-line">RTN: ' . $clienteRtn . '</div>';
        $html .= '<div class="info-line">Tel: ' . $clienteTel . '</div>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<div class="info-title">DATOS FACTURA</div>';
        $html .= '<div class="info-line"><strong>N°:</strong> ' . $numFactura . '</div>';
        $html .= '<div class="info-line"><strong>Fecha:</strong> ' . $fecha . '</div>';
        $html .= '<div class="info-line"><strong># Orden:</strong> ' . $ordenId . '</div>';
        $html .= '<div class="info-line"><strong>Recepcionista:</strong> ' . $recepcionista . '</div>';
        $html .= '<div class="info-line"><strong>Mecánico:</strong> ' . $mecanico . '</div>';
        $html .= '<div class="info-line"><strong>Pago:</strong> ' . $metodoPago . '</div>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<hr class="dashed">';

        // -- TABLE --
        $html .= '<table class="table-items">';
        $html .= '<tr><th style="width:8%;">CANT</th><th class="left" style="width:52%;">DESCRIPCIÓN</th><th style="width:20%;">PRECIO U.</th><th style="width:20%;">IMPORTE</th></tr>';
        $html .= $itemsHtml;
        $html .= '</table>';

        $html .= '<hr class="dashed-thin">';

        // -- TOTALS --
        $totalRep = $factura['subtotal_repuestos'];
        $totalServ = $factura['subtotal_mano_obra'];
        $isv = $factura['isv'];
        $totalPagar = $factura['total_pagar'];
        $html .= '<table class="totals">';
        $html .= '<tr><td class="label">Subtotal Repuestos:</td><td class="value">L. ' . number_format($totalRep, 2) . '</td></tr>';
        $html .= '<tr><td class="label">Subtotal Servicios:</td><td class="value">L. ' . number_format($totalServ, 2) . '</td></tr>';
        $html .= '<tr><td class="label">Subtotal:</td><td class="value">L. ' . number_format($subtotal, 2) . '</td></tr>';
        $html .= '<tr><td class="label">ISV (15%):</td><td class="value">L. ' . number_format($isv, 2) . '</td></tr>';
        $html .= '<tr><td colspan="2"><hr></td></tr>';
        $html .= '<tr><td class="total-label">TOTAL:</td><td class="total-value">L. ' . number_format($totalPagar, 2) . '</td></tr>';
        $html .= '</table>';

        $html .= '<hr class="dashed">';

        $html .= '</div>'; // .page
        $html .= '</body></html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("factura_{$numFactura}.pdf", [
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
