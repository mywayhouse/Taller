<div class="toolbar">
    <?php if (\App\Helpers\AyudaAcceso::hasWriteAccess('repuestos')): ?>
        <!-- Reemplazamos el <a> por un <button> con id -->
        <button type="button" id="btn-nuevo-repuesto" class="btn btn-primary">
            + Nuevo Repuesto
        </button>
    <?php endif; ?>
</div>

<div class="search-bar">
    <form method="GET" action="<?= APP_URL ?>/repuestos" class="search-form">
        <input type="text" name="q" placeholder="Buscar repuesto..." value="<?= htmlspecialchars($q ?? '') ?>" class="search-input">
        <label class="checkbox-label">
            <input type="checkbox" name="stock_bajo" value="1" <?= ($stockBajoChecked ?? '0') === '1' ? 'checked' : '' ?>>
            Stock bajo
        </label>
        <button type="submit" class="btn btn-primary btn-sm" style="border:0;box-sizing:border-box;">Filtrar</button>
        <a href="<?= APP_URL ?>/repuestos" class="btn btn-secondary btn-sm" style="box-sizing:border-box;">Limpiar</a>
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
                            <!-- Botón Editar con data-attributes -->
                            <button type="button" 
                                    class="btn btn-sm btn-edit btn-editar-repuesto"
                                    data-id="<?= $r['id_repuesto'] ?>"
                                    data-nombre="<?= htmlspecialchars($r['nombre']) ?>"
                                    data-stock-actual="<?= $r['stock_actual'] ?>"
                                    data-stock-minimo="<?= $r['stock_minimo'] ?>"
                                    data-unidad="<?= htmlspecialchars($r['unidad_medida'] ?? '') ?>"
                                    data-precio="<?= $r['precio_venta'] ?>">
                                Editar
                            </button>

                            <a href="<?= APP_URL ?>/repuestos/movimientos/<?= $r['id_repuesto'] ?>" class="btn btn-sm btn-info">Mov.</a>
                            <a href="<?= APP_URL ?>/repuestos/eliminar/<?= $r['id_repuesto'] ?>"
                            class="btn btn-sm btn-delete btn-confirmar-accion"
                            data-titulo="Desactivar Repuesto"
                            data-mensaje="¿Desactivar este repuesto?"
                            data-detalle="<?= htmlspecialchars($r['nombre'], ENT_QUOTES) ?>">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Incluimos el formulario modal dentro del mismo index -->
<?php require VIEWS . '/repuestos/form.php'; ?>