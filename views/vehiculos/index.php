<!-- Listado de vehículos -->
<div class="toolbar">
    <a href="<?= APP_URL ?>/vehiculos/crear" class="btn btn-primary">
        + Nuevo Vehículo
    </a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Tipo</th>
                <th>Cilindraje</th>
                <th>Cliente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vehiculos)): ?>
                <tr>
                    <td colspan="8" class="text-center">No hay vehículos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($vehiculos as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['placa']) ?></td>
                        <td><?= htmlspecialchars($v['marca']) ?></td>
                        <td><?= htmlspecialchars($v['modelo']) ?></td>
                        <td><?= $v['anio'] ?></td>
                        <td><?= htmlspecialchars($v['tipo']) ?></td>
                        <td><?= $v['cilindraje'] ? htmlspecialchars($v['cilindraje']) . ' CC' : '-' ?></td>
                        <td><?= htmlspecialchars($v['nombre_cliente']) ?></td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/vehiculos/editar/<?= urlencode($v['placa']) ?>" class="btn btn-sm btn-edit">Editar</a>
                            <a href="<?= APP_URL ?>/vehiculos/eliminar/<?= urlencode($v['placa']) ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Está seguro de eliminar este vehículo?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>