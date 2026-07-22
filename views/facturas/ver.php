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
                <p><strong>N°:</strong> <?= htmlspecialchars($factura['numero_factura']) ?></p>
                <p><strong># Orden:</strong> <?= htmlspecialchars($factura['id_orden']) ?></p>
                <p><strong>Fecha Ingreso:</strong> <?= htmlspecialchars($factura['fecha_ingreso']) ?></p>
                <p><strong>Fecha Emisión:</strong> <?= htmlspecialchars($factura['fecha_emision']) ?></p>
                <p>
                    <span class="badge <?= ($factura['estado_activo'] ?? 1) ? 'badge-active' : 'badge-inactive' ?>">
                        <?= ($factura['estado_activo'] ?? 1) ? 'ACTIVA' : 'ANULADA' ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="factura-seccion">
            <h3>Información del Cliente y Personal</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Cliente</span>
                    <span class="info-value"><?= htmlspecialchars($factura['cliente_nombre']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">RTN/DNI</span>
                    <span class="info-value"><?= htmlspecialchars($factura['rnt_dni']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono</span>
                    <span class="info-value"><?= htmlspecialchars($factura['cliente_telefono'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Placa</span>
                    <span class="info-value"><?= htmlspecialchars($factura['placa']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vehículo</span>
                    <span class="info-value"><?= htmlspecialchars($factura['marca']) ?> <?= htmlspecialchars($factura['modelo']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Recepcionista</span>
                    <span class="info-value"><?= htmlspecialchars($factura['factura_recepcionista_nombre'] ?? $factura['recepcionista_nombre']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mecánico</span>
                    <span class="info-value"><?= htmlspecialchars($factura['factura_mecanico_nombre'] ?? $factura['mecanico_nombre']) ?></span>
                </div>
            </div>
        </div>

        <div class="factura-seccion">
            <h3>Detalle de Repuestos</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Repuesto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($repuestos)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Sin repuestos.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($repuestos as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['repuesto_nombre']) ?></td>
                                    <td><?= (int)$r['cantidad'] ?></td>
                                    <td>L. <?= number_format($r['precio_unitario_historico'], 2) ?></td>
                                    <td>L. <?= number_format($r['total_linea'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php $serviciosList = json_decode($factura['servicios_json'] ?? '[]', true) ?? []; ?>
        <?php if (!empty($serviciosList)): ?>
        <div class="factura-seccion">
            <h3>Servicios Realizados</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($serviciosList as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['descripcion'] ?? '-') ?></td>
                                <td>L. <?= number_format((float)($s['precio'] ?? 0), 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="factura-seccion">
            <h3>Servicios</h3>
            <p class="factura-dato">L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></p>
        </div>
        <?php endif; ?>

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

        <?php if (empty($GLOBALS['_pdf_context'])): ?>
        <div class="form-actions" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <a href="<?= APP_URL ?>/facturas/pdf/<?= $factura['id_factura'] ?>" class="btn btn-primary" target="_blank">
                    Descargar PDF
                </a>
            </div>
            <div style="display:flex;gap:8px;">
                <?php if ($factura['estado_activo'] ?? 1): ?>
                    <button type="button" class="btn btn-delete" id="btnCancelarFactura">
                        Cancelar
                    </button>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/facturas" class="btn btn-secondary">Volver al listado</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalAnular" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-icon">&#9888;</div>
        <h3 class="modal-title">Anular Factura</h3>
        <p class="modal-text">¿Está seguro de anular esta factura?<br>Esta acción no se puede deshacer.</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="modalCancelarBtn">Cancelar</button>
            <a href="<?= APP_URL ?>/facturas/anular/<?= $factura['id_factura'] ?>" class="btn btn-delete" id="modalConfirmarBtn">Confirmar</a>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-box {
    background: #fff;
    border-radius: 12px;
    padding: 30px 35px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    text-align: center;
}
.modal-icon {
    font-size: 48px;
    color: #ef4444;
    margin-bottom: 10px;
}
.modal-title {
    font-size: 18px;
    color: #1e293b;
    margin: 0 0 8px;
    font-weight: 700;
}
.modal-text {
    font-size: 14px;
    color: #64748b;
    margin: 0 0 24px;
    line-height: 1.5;
}
.modal-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
.modal-actions .btn {
    min-width: 110px;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
}
</style>

<script>
(function() {
    var btn = document.getElementById('btnCancelarFactura');
    var modal = document.getElementById('modalAnular');
    var cancelBtn = document.getElementById('modalCancelarBtn');
    if (btn && modal) {
        btn.addEventListener('click', function() { modal.style.display = 'flex'; });
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() { modal.style.display = 'none'; });
        }
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.style.display = 'none';
        });
    }
})();
</script>
