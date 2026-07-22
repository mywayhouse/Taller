<div class="toolbar">
    <a href="<?= APP_URL ?>/proveedores/crear" class="btn btn-primary">
        + Nuevo Proveedor
    </a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($proveedores)): ?>
                <tr>
                    <td colspan="7" class="text-center">No hay proveedores registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($proveedores as $p): ?>
                    <tr>
                        <td><?= $p['id_proveedor'] ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['contacto'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['direccion'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $p['estado_activo'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $p['estado_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/proveedores/editar/<?= $p['id_proveedor'] ?>" class="btn btn-sm btn-edit">Editar</a>
                            <a href="<?= APP_URL ?>/proveedores/eliminar/<?= $p['id_proveedor'] ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Eliminar este proveedor?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
