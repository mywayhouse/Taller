<div class="alert-info-vehiculos">Los vehículos se registran automáticamente al crear una orden de servicio.</div>

<form method="GET" action="<?= APP_URL ?>/vehiculos" class="search-bar">
    <div class="search-row">
        <input type="text" name="q" placeholder="Buscar por placa, marca, modelo o cliente..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="form-control search-input">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($q)): ?>
            <a href="<?= APP_URL ?>/vehiculos" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-vehiculos">
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
                    <td colspan="7" class="text-center">No hay vehículos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($vehiculos as $v): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($v['placa']) ?></strong></td>
                        <td><?= htmlspecialchars($v['marca']) ?></td>
                        <td><?= htmlspecialchars($v['modelo']) ?></td>
                        <td><?= $v['anio'] ?></td>
                        <td><span class="badge badge-tipo"><?= htmlspecialchars($v['tipo']) ?></span></td>
                        <td><?= htmlspecialchars($v['nombre_cliente']) ?></td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/vehiculos/editar/<?= urlencode($v['placa']) ?>" class="btn btn-sm btn-edit">Editar</a>
                        <a href="<?= APP_URL ?>/vehiculos/eliminar/<?= urlencode($v['placa']) ?>"
                        class="btn btn-sm btn-delete btn-confirmar-accion"
                        data-titulo="Eliminar Vehículo"
                        data-mensaje="¿Está seguro de eliminar este vehículo?"
                        data-detalle="Placa: <?= htmlspecialchars($v['placa'], ENT_QUOTES) ?>">
                            Eliminar
                        </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.table-vehiculos tbody tr:nth-child(even) { background: #f8fafc; }
.table-vehiculos tbody tr:hover { background: #eef2f7; }
.badge-tipo { background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-block; }
.alert-info-vehiculos { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 15px; font-size: 13px; color: #1e40af; display: flex; align-items: center; gap: 8px; }
.alert-info-vehiculos::before { content: "\2139\FE0F"; font-size: 16px; }
</style>