<div class="logs-container">
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
