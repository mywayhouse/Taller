<!-- ============================================================
     Vista: Listado de Vehículos
     ============================================================ -->
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
                <th>Cliente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vehiculos)): ?>
                <tr>
                    <td colspan="7" class="text-center">
                        No hay vehículos registrados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($vehiculos as $vehiculo): ?>
                    <tr>
                        <td><?= htmlspecialchars($vehiculo['placa']) ?></td>
                        <td><?= htmlspecialchars($vehiculo['marca']) ?></td>
                        <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
                        <td><?= $vehiculo['anio'] ?></td>
                        <td><?= htmlspecialchars($vehiculo['tipo']) ?></td>
                        <td><?= htmlspecialchars($vehiculo['nombre_cliente']) ?></td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/vehiculos/editar/<?= $vehiculo['placa'] ?>" class="btn btn-sm btn-edit">
                                Editar
                            </a>
                            <a href="<?= APP_URL ?>/vehiculos/eliminar/<?= $vehiculo['placa'] ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Eliminar este vehículo?')">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>