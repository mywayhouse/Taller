<div class="toolbar">
    <a href="<?= APP_URL ?>/proveedores/crear" class="btn btn-primary">
        + Nuevo Proveedor
    </a>
</div>

<div class="search-bar">
    <form method="GET" action="<?= APP_URL ?>/proveedores" class="search-form">
        <input type="text" name="q" placeholder="Buscar proveedor (nombre, contacto, teléfono, correo, RTN)..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="search-input">
        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
        <a href="<?= APP_URL ?>/proveedores" class="btn btn-secondary btn-sm">Limpiar</a>
    </form>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>RTN</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($proveedores)): ?>
                <tr>
                    <td colspan="8" class="text-center">No hay proveedores registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($proveedores as $p): ?>
                    <tr>
                        <td><?= $p['id_proveedor'] ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['contacto'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['correo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['rtn'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $p['estado_activo'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $p['estado_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/proveedores/editar/<?= $p['id_proveedor'] ?>" class="btn btn-sm btn-edit">Editar</a>
                            <a href="<?= APP_URL ?>/proveedores/eliminar/<?= $p['id_proveedor'] ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Desactivar este proveedor?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
