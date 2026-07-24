<div class="card">
    <div class="card-body">
        <div class="info-row">
            <strong>Orden #:</strong> <?= $orden['id_orden'] ?>
        </div>
        <div class="info-row">
            <strong>Estado:</strong>
            <span class="badge <?= match($orden['estado']) {
                'RECIBIDO' => 'badge-warning',
                'EN PROCESO' => 'badge-info',
                'PENDIENTE' => 'badge-secondary',
                'LISTO' => 'badge-success',
                'ENTREGADO' => 'badge-active',
                'CANCELADO' => 'badge-inactive',
                default => 'badge-secondary'
            } ?>">
                <?= $orden['estado'] ?>
            </span>
        </div>
        <div class="info-row">
            <strong>Cliente:</strong> <?= htmlspecialchars($orden['nombre_cliente'] ?? '') ?>
        </div>
        <div class="info-row">
            <strong>Vehículo:</strong> <?= htmlspecialchars(($orden['marca'] ?? '') . ' ' . ($orden['modelo'] ?? '') . ' - ' . ($orden['placa_vehiculo'] ?? '')) ?>
        </div>
        <div class="info-row">
            <strong>Fecha Ingreso:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_ingreso'])) ?>
        </div>
        <?php if (!empty($orden['fecha_entrega'])): ?>
            <div class="info-row">
                <strong>Fecha Entrega:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_entrega'])) ?>
            </div>
        <?php endif; ?>
        <div class="info-row">
            <strong>Recepcionista:</strong> <?= htmlspecialchars($orden['recepcionista'] ?? '') ?>
        </div>
        <div class="info-row">
            <strong>Mecánico:</strong> <?= htmlspecialchars($orden['mecanico'] ?? '') ?>
        </div>
        <div class="info-row">
            <strong>Tipo de Servicio:</strong>
            <?php $tipos = ['REVISION' => 'Solo revisión / diagnóstico', 'CLIENTE_TRAE_REPUESTO' => 'El cliente trae su repuesto', 'REPUESTO_TIENDA' => 'Requiere repuesto de la tienda']; ?>
            <?= $tipos[$orden['tipo_servicio']] ?? $orden['tipo_servicio'] ?>
        </div>
        <div class="info-row">
            <strong>Costo Mano de Obra:</strong> L. <?= number_format((float) ($orden['costo_mano_obra'] ?? 0), 2) ?>
        </div>
        <div class="info-row">
            <strong>Diagnóstico:</strong>
            <p><?= nl2br(htmlspecialchars($orden['diagnostico_preliminar'] ?? '')) ?></p>
        </div>
    </div>
</div>

<?php if (!empty($detalles)): ?>
    <h3>Repuestos Utilizados</h3>
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
                <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['repuesto_nombre'] ?? '') ?></td>
                        <td><?= (int) ($d['cantidad'] ?? 0) ?> <?= htmlspecialchars($d['unidad_medida'] ?? '') ?></td>
                        <td>L. <?= number_format((float) ($d['precio_unitario_historico'] ?? 0), 2) ?></td>
                        <td>L. <?= number_format((float) ($d['total_linea'] ?? (($d['cantidad'] ?? 0) * ($d['precio_unitario_historico'] ?? 0))), 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<form action="<?= APP_URL ?>/ordenes/cambiarEstado/<?= $orden['id_orden'] ?>" method="POST" class="form" style="margin-top:20px; max-width:400px;">
    <h3>Cambiar Estado</h3>
    <div class="form-row">
        <div class="form-group">
            <select name="estado" class="form-control" required>
                <option value="RECIBIDO" <?= $orden['estado'] === 'RECIBIDO' ? 'selected' : '' ?>>Recibido (R)</option>
                <option value="EN PROCESO" <?= $orden['estado'] === 'EN PROCESO' ? 'selected' : '' ?>>En Proceso (P)</option>
                <option value="PENDIENTE" <?= $orden['estado'] === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente (P)</option>
                <option value="LISTO" <?= $orden['estado'] === 'LISTO' ? 'selected' : '' ?>>Listo (L)</option>
                <option value="ENTREGADO" <?= $orden['estado'] === 'ENTREGADO' ? 'selected' : '' ?>>Entregado</option>
                <option value="CANCELADO" <?= $orden['estado'] === 'CANCELADO' ? 'selected' : '' ?>>Cancelado (C)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
    </div>
</form>

<div class="toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;">
    <a href="<?= APP_URL ?>/ordenes/editar/<?= $orden['id_orden'] ?>" class="btn btn-primary">Editar</a>
    <div>
        <a href="<?= APP_URL ?>/ordenes" class="btn btn-secondary">← Volver</a>
        <a href="<?= APP_URL ?>/ordenes/eliminar/<?= $orden['id_orden'] ?>"
           class="btn btn-delete"
           onclick="return confirm('¿Está seguro de eliminar esta orden?')">Eliminar</a>
    </div>
</div>
