<div class="toolbar">
    <a href="<?= APP_URL ?>/repuestos/crear" class="btn btn-primary">
        + Nuevo Repuesto
    </a>
</div>

<div class="search-bar">
    <form method="GET" action="<?= APP_URL ?>/repuestos" class="search-form">
        <input type="text" name="q" placeholder="Buscar repuesto..." value="<?= htmlspecialchars($q ?? '') ?>"
               class="search-input">
        <label class="checkbox-label">
            <input type="checkbox" name="stock_bajo" value="1" <?= ($stockBajoChecked ?? '0') === '1' ? 'checked' : '' ?>>
            Stock bajo
        </label>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        <a href="<?= APP_URL ?>/repuestos" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Unidad</th>
                <th>Precio Venta</th>
                <th>Alerta</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($repuestos)): ?>
                <tr>
                    <td colspan="9" class="text-center">No hay repuestos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($repuestos as $r): ?>
                    <?php $alerta = ($r['stock_actual'] <= $r['stock_minimo']); ?>
                    <tr class="<?= $alerta ? 'row-warning' : '' ?>">
                        <td><?= $r['id_repuesto'] ?></td>
                        <td><?= htmlspecialchars($r['nombre']) ?></td>
                        <td class="<?= $alerta ? 'text-danger' : '' ?>">
                            <strong><?= $r['stock_actual'] ?></strong>
                        </td>
                        <td><?= $r['stock_minimo'] ?></td>
                        <td><?= htmlspecialchars($r['unidad_medida'] ?? '-') ?></td>
                        <td>L. <?= number_format($r['precio_venta'], 2) ?></td>
                        <td>
                            <?php if ($alerta): ?>
                                <span class="badge badge-alert">Stock Bajo</span>
                            <?php else: ?>
                                <span class="badge badge-ok">Ok</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $r['estado_activo'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $r['estado_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/repuestos/editar/<?= $r['id_repuesto'] ?>" class="btn btn-sm btn-edit">Editar</a>
                            <a href="<?= APP_URL ?>/repuestos/movimientos/<?= $r['id_repuesto'] ?>" class="btn btn-sm btn-info">Mov.</a>
                            <a href="<?= APP_URL ?>/repuestos/eliminar/<?= $r['id_repuesto'] ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Desactivar este repuesto?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
