<div class="toolbar">
    <button type="button" class="btn btn-primary" onclick="abrirModalOrden()">+ Nueva orden</button>
</div>

<!--
<div class="toolbar">
    <a href="<?= APP_URL ?>/ordenes/crear" class="btn btn-primary">
        + Nueva Orden
    </a>
</div>
-->
<form method="GET" action="<?= APP_URL ?>/ordenes" class="search-bar">
    <div class="search-row">
        <input type="text" name="q" placeholder="Buscar por número de orden, placa, cliente, marca..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="form-control search-input">
        <select name="estado" class="form-control search-select">
            <option value="">Todos los estados</option>
            <option value="RECIBIDO" <?= ($estadoFiltro ?? '') === 'RECIBIDO' ? 'selected' : '' ?>>Recibido</option>
            <option value="EN PROCESO" <?= ($estadoFiltro ?? '') === 'EN PROCESO' ? 'selected' : '' ?>>En Proceso</option>
            <option value="PENDIENTE" <?= ($estadoFiltro ?? '') === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
            <option value="LISTO" <?= ($estadoFiltro ?? '') === 'LISTO' ? 'selected' : '' ?>>Listo</option>
            <option value="ENTREGADO" <?= ($estadoFiltro ?? '') === 'ENTREGADO' ? 'selected' : '' ?>>Entregado</option>
            <option value="CANCELADO" <?= ($estadoFiltro ?? '') === 'CANCELADO' ? 'selected' : '' ?>>Cancelado</option>
        </select>
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($q) || !empty($estadoFiltro)): ?>
            <a href="<?= APP_URL ?>/ordenes" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Tipo</th>
                <th>Diagnóstico</th>
                <th>Estado</th>
                <th>Mecánico</th>
                <th>Ingreso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ordenes)): ?>
                <tr>
                    <td colspan="9" class="text-center">No hay órdenes de servicio registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($ordenes as $o): ?>
                    <tr>
                        <td><?= $o['id_orden'] ?></td>
                        <td><?= htmlspecialchars($o['nombre_cliente'] ?? '') ?></td>
                        <td><?= htmlspecialchars(($o['marca'] ?? '') . ' ' . ($o['modelo'] ?? '') . ' - ' . ($o['placa_vehiculo'] ?? '')) ?></td>
                        <td><?php $t = $o['tipo_servicio'] ?? 'REVISION'; echo $t === 'REVISION' ? 'Revisión' : ($t === 'CLIENTE_TRAE_REPUESTO' ? 'Trae repuesto' : 'Rep. tienda'); ?></td>
                        <td><?= htmlspecialchars(mb_substr($o['diagnostico_preliminar'] ?? '', 0, 50)) ?></td>
                        <td>
                            <span class="badge <?= match($o['estado']) {
                                'RECIBIDO' => 'badge-warning',
                                'EN PROCESO' => 'badge-info',
                                'PENDIENTE' => 'badge-secondary',
                                'LISTO' => 'badge-success',
                                'ENTREGADO' => 'badge-active',
                                'CANCELADO' => 'badge-inactive',
                                default => 'badge-secondary'
                            } ?>">
                                <?= $o['estado'] ?>
                            </span>
                        </td>
                    
                        <td><?= htmlspecialchars($o['mecanico'] ?? '') ?></td>
                        <td><?= date('d/m/Y', strtotime($o['fecha_ingreso'])) ?></td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/ordenes/ver/<?= $o['id_orden'] ?>" class="btn btn-sm btn-info">Ver</a>
                            <!--
                            <a href="<?= APP_URL ?>/ordenes/ver/<?= $o['id_orden'] ?>" class="btn btn-sm btn-info">Ver</a>
                            -->
                            <button type="button" class="btn btn-sm btn-edit" onclick="abrirModalOrden(<?= $o['id_orden'] ?>)">
                                Editar
                            </button>
                            <!--
                            <a href="<?= APP_URL ?>/ordenes/editar/<?= $o['id_orden'] ?>" class="btn btn-sm btn-edit">Editar</a>
                            -->
                            <a href="<?= APP_URL ?>/ordenes/eliminar/<?= $o['id_orden'] ?>"
                               class="btn btn-sm btn-delete"
                               onclick="return confirm('¿Está seguro de eliminar esta orden?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
