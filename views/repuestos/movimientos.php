<div class="toolbar">
    <a href="<?= APP_URL ?>/repuestos" class="btn btn-secondary">
        Volver a Repuestos
    </a>
</div>

<div class="info-card">
    <p><strong>Stock actual:</strong> <?= $repuesto['stock_actual'] ?> <?= htmlspecialchars($repuesto['unidad_medida'] ?? '') ?></p>
    <p><strong>Stock mínimo:</strong> <?= $repuesto['stock_minimo'] ?></p>
    <p><strong>Precio:</strong> L. <?= number_format($repuesto['precio_venta'], 2) ?></p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Ajustar Stock</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/repuestos/ajustarStock/<?= $repuesto['id_repuesto'] ?>" class="form-inline">
            <div class="form-group">
                <label for="nuevo_stock">Nuevo stock:</label>
                <input type="number" name="nuevo_stock" id="nuevo_stock"
                       value="<?= $repuesto['stock_actual'] ?>" required min="0" step="1"
                       style="width: 120px;">
            </div>
            <div class="form-group">
                <label for="observacion">Observación:</label>
                <input type="text" name="observacion" id="observacion"
                       placeholder="Motivo del ajuste" maxlength="255"
                       style="width: 300px;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"
                    onclick="return confirm('¿Confirmar ajuste de stock?')">
                Aplicar Ajuste
            </button>
        </form>
    </div>
</div>

<h3 style="margin-top: 24px;">Historial de Movimientos</h3>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Stock Anterior</th>
                <th>Stock Nuevo</th>
                <th>Proveedor</th>
                <th>Usuario</th>
                <th>Fecha / Hora</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($movimientos)): ?>
                <tr>
                    <td colspan="9" class="text-center">No hay movimientos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= $m['id_movimiento'] ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($m['tipo']) ?>">
                                <?= $m['tipo'] ?>
                            </span>
                        </td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= $m['stock_anterior'] ?></td>
                        <td><?= $m['stock_nuevo'] ?></td>
                        <td><?= htmlspecialchars($m['proveedor_nombre'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['usuario_nombre']) ?></td>
                        <td><?= htmlspecialchars($m['fecha_hora']) ?></td>
                        <td><?= htmlspecialchars($m['observacion'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
