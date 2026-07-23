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
                <p><strong>Fecha:</strong> <?= htmlspecialchars($factura['fecha_emision']) ?></p>
                <p>
                    <span class="badge <?= ($factura['estado_activo'] ?? 1) ? 'badge-active' : 'badge-inactive' ?>">
                        <?= ($factura['estado_activo'] ?? 1) ? 'ACTIVA' : 'ANULADA' ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="factura-seccion">
            <h3>Datos del Cliente</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Cliente</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['cliente_nombre']) ?></p>
                </div>
                <div class="form-group">
                    <label>RTN/DNI</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['rnt_dni']) ?></p>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['cliente_telefono'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <div class="factura-seccion">
            <h3>Datos del Servicio</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>N° Orden</label>
                    <p class="factura-dato">#<?= htmlspecialchars($factura['id_orden']) ?></p>
                </div>
                <div class="form-group">
                    <label>Fecha Ingreso</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['fecha_ingreso']) ?></p>
                </div>
                <div class="form-group">
                    <label>Placa</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['placa']) ?></p>
                </div>
                <div class="form-group">
                    <label>Vehículo</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['marca']) ?> <?= htmlspecialchars($factura['modelo']) ?></p>
                </div>
                <div class="form-group">
                    <label>Recepcionista</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['recepcionista_nombre']) ?></p>
                </div>
                <div class="form-group">
                    <label>Mecánico</label>
                    <p class="factura-dato"><?= htmlspecialchars($factura['mecanico_nombre']) ?></p>
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

        <div class="factura-seccion">
            <h3>Mano de Obra</h3>
            <p class="factura-dato">L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></p>
        </div>

        <div class="factura-totales">
            <div class="total-row">
                <span>Subtotal Repuestos:</span>
                <span>L. <?= number_format($factura['subtotal_repuestos'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Subtotal Mano de Obra:</span>
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

        <div class="form-actions">
            <a href="<?= APP_URL ?>/facturas/pdf/<?= $factura['id_factura'] ?>" class="btn btn-primary" target="_blank">
                Descargar PDF
            </a>
            <a href="<?= APP_URL ?>/facturas" class="btn btn-secondary">Volver al listado</a>
        </div>
    </div>
</div>
