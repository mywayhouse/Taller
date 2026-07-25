<link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/facturas.css">
<div class="toolbar">
    <a href="<?= APP_URL ?>/facturas" class="btn btn-secondary">← Volver al historial de facturas</a>
</div>
<div class="factura-wrapper">
    <div class="factura-print">
        <div class="factura-header">
            <div class="factura-empresa">
                <h2><?= htmlspecialchars(EMPRESA_NOMBRE) ?></h2>
                <p><?= htmlspecialchars(EMPRESA_DIRECCION) ?></p>
                <p>Tel: <?= htmlspecialchars(EMPRESA_TELEFONO) ?></p>
                <p>RTN: <?= htmlspecialchars(EMPRESA_RTN) ?></p>
            </div>
            <div class="factura-titulo">
                <h1>FACTURA</h1>
                <p><strong>No.</strong> <?= htmlspecialchars($factura['numero_factura']) ?></p>
                <p><strong>Orden N°:</strong> <?= htmlspecialchars($factura['id_orden']) ?></p>
                <p><strong>Fecha Ingreso:</strong> <?= date('d/m/Y', strtotime($factura['fecha_ingreso'])) ?></p>
                <p><strong>Fecha Emisión:</strong> <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></p>
                <span class="badge <?= ($factura['estado_activo'] ?? 1) ? 'badge-active' : 'badge-inactive' ?>">
                    <?= ($factura['estado_activo'] ?? 1) ? 'ACTIVA' : 'ANULADA' ?>
                </span>
            </div>
        </div>

        <div class="factura-body">
            <div class="factura-seccion">
                <h3>Información del Cliente y Personal</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                    <div><div style="font-size:11px;color:var(--text-secondary);">CLIENTE</div><div style="font-weight:700;"><?= htmlspecialchars($factura['cliente_nombre']) ?></div></div>
                    <div><div style="font-size:11px;color:var(--text-secondary);">RTN/DNI</div><div style="font-weight:700;"><?= htmlspecialchars($factura['rnt_dni']) ?></div></div>
                    <div><div style="font-size:11px;color:var(--text-secondary);">TELÉFONO</div><div style="font-weight:700;"><?= htmlspecialchars($factura['cliente_telefono'] ?? '-') ?></div></div>
                    <div><div style="font-size:11px;color:var(--text-secondary);">VEHÍCULO</div><div style="font-weight:700;"><?= htmlspecialchars($factura['marca'] . ' ' . $factura['modelo']) ?></div></div>
                    <div><div style="font-size:11px;color:var(--text-secondary);">PLACA</div><div style="font-weight:700;"><?= htmlspecialchars($factura['placa']) ?></div></div>
                    <div><div style="font-size:11px;color:var(--text-secondary);">MECÁNICO</div><div style="font-weight:700;"><?= htmlspecialchars($factura['mecanico_nombre']) ?></div></div>
                    <div><div style="font-size:11px;color:var(--text-secondary);">RECEPCIONISTA</div><div style="font-weight:700;"><?= htmlspecialchars($factura['recepcionista_nombre']) ?></div></div>
                </div>
            </div>
        </div>

        <div class="factura-seccion">
            <h3>Detalle de Repuestos</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cant.</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($repuestos)): ?>
                        <?php foreach ($repuestos as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['repuesto_nombre']) ?></td>
                                <td><?= (int)$r['cantidad'] ?></td>
                                <td>L. <?= number_format($r['precio_unitario_historico'], 2) ?></td>
                                <td>L. <?= number_format($r['total_linea'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Sin repuestos</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="factura-seccion">
            <h3>Servicios Realizados</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= nl2br(htmlspecialchars($factura['diagnostico_preliminar'] ?? 'Mano de obra')) ?></td>
                        <td>L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="factura-totales">
            <div class="total-row">
                <span>Subtotal Repuestos:</span>
                <span>L. <?= number_format($factura['subtotal_repuestos'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Subtotal Servicios:</span>
                <span>L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Subtotal:</span>
                <span>L. <?= number_format($factura['subtotal_repuestos'] + $factura['subtotal_mano_obra'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>ISV (15%):</span>
                <span>L. <?= number_format($factura['isv'], 2) ?></span>
            </div>
            <div class="total-row total-final">
                <span>Total a Pagar:</span>
                <span>L. <?= number_format($factura['total_pagar'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Método de Pago:</span>
                <span><?= htmlspecialchars($factura['metodo_pago'] ?? '-') ?></span>
            </div>
        </div>
    </div>
</div>

<div class="toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-top:15px;">
    <a href="<?= APP_URL ?>/facturas/pdf/<?= $factura['id_factura'] ?>" class="btn btn-primary" target="_blank">Descargar PDF</a>
    <div>
        
        <?php if ($factura['estado_activo'] ?? 1): ?>
            <a href="#" class="btn btn-delete" onclick="mostrarModalAnular(event)">Cancelar Factura</a>
        <?php endif; ?>
    </div>
</div>

<div id="modal-anular" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3>Cancelar Factura</h3>
        <p>¿Está seguro de anular esta factura?</p>
        <p class="modal-detalle">Factura No. <?= htmlspecialchars($factura['numero_factura']) ?></p>
        <div class="modal-actions">
            <a href="<?= APP_URL ?>/facturas" class="btn btn-secondary" onclick="cerrarModalAnular(event)">No, volver</a>
            <a href="<?= APP_URL ?>/facturas/anular/<?= $factura['id_factura'] ?>" class="btn btn-delete">Sí, anular</a>
        </div>
    </div>
</div>

<script>
function mostrarModalAnular(e) {
    e.preventDefault();
    document.getElementById('modal-anular').style.display = 'flex';
}
function cerrarModalAnular(e) {
    e.preventDefault();
    document.getElementById('modal-anular').style.display = 'none';
}
document.getElementById('modal-anular').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAnular(e);
});
</script>

<style>
.modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 9999;
    display: flex; align-items: center; justify-content: center;
}
.modal-box {
    background: #fff; border-radius: 8px; padding: 30px;
    max-width: 420px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    text-align: center;
}
.modal-box h3 { color: #c62828; margin: 0 0 10px; font-size: 18px; }
.modal-box p { margin: 5px 0; font-size: 14px; color: #333; }
.modal-detalle { font-size: 12px; color: #666; margin-bottom: 15px !important; }
.modal-actions { display: flex; justify-content: center; gap: 10px; margin-top: 15px; }
</style>
