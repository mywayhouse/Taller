<div class="search-bar">
    <form method="GET" action="<?= APP_URL ?>/auditoria" class="search-form">
        <input type="text" name="q" placeholder="Buscar por acción o usuario..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="search-input">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="fecha_desde" style="display: inline; margin-right: 4px;">Desde:</label>
            <input type="date" name="fecha_desde" id="fecha_desde"
                   value="<?= htmlspecialchars($fechaDesde ?? '') ?>"
                   style="width: auto; display: inline; padding: 6px 10px;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label for="fecha_hasta" style="display: inline; margin-right: 4px;">Hasta:</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta"
                   value="<?= htmlspecialchars($fechaHasta ?? '') ?>"
                   style="width: auto; display: inline; padding: 6px 10px;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        <a href="<?= APP_URL ?>/auditoria" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acción</th>
                <th>Fecha / Hora</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6" class="text-center">No hay registros de auditoría.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['id_log']) ?></td>
                        <td><?= htmlspecialchars($log['usuario_nombre']) ?></td>
                        <td><?= htmlspecialchars($log['usuario_rol']) ?></td>
                        <td><?= htmlspecialchars($log['accion']) ?></td>
                        <td><?= htmlspecialchars($log['fecha_hora']) ?></td>
                        <td><?= htmlspecialchars($log['ip_direccion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
