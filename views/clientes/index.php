<!-- ============================================================
     Vista: Listado de Clientes
     ============================================================ -->
<div class="toolbar">
    <button type="button" class="btn btn-primary" onclick="abrirModalCliente()">
    + Nuevo Cliente
</button>
<!--
    <a href="<?= APP_URL ?>/clientes/crear" class="btn btn-primary">
        + Nuevo Cliente
    </a>
-->
</div>

<form method="GET" action="<?= APP_URL ?>/clientes" class="search-bar">
    <div class="search-row">
        <input type="text" name="q" placeholder="Buscar por nombre, RTN o teléfono..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="form-control search-input">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($q)): ?>
            <a href="<?= APP_URL ?>/clientes" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>RTN/DNI</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="6" class="text-center">
                        No hay clientes registrados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= $cliente['id_cliente'] ?></td>
                        <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($cliente['rnt_dni']) ?></td>
                        <td>
                            <span class="badge <?= $cliente['estado_activo'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $cliente['estado_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button type="button" class="btn btn-sm btn-edit" onclick="abrirModalCliente(<?= $cliente['id_cliente'] ?>)">
                                Editar
                            </button>
                        <!--
                            <a href="<?= APP_URL ?>/clientes/editar/<?= $cliente['id_cliente'] ?>" class="btn btn-sm btn-edit">
                                Editar
                            </a>
                        -->
                            <a href="<?= APP_URL ?>/clientes/eliminar/<?= $cliente['id_cliente'] ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Desactivar este cliente?')">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
