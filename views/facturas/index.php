<div class="toolbar">
    <a href="<?= APP_URL ?>/facturas/crear" class="btn btn-primary">
        + Nueva Factura
    </a>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>N° Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Placa</th>
                <th>Total</th>
                <th>Método Pago</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($facturas)): ?>
                <tr>
                    <td colspan="8" class="text-center">
                        No hay facturas registradas.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($facturas as $factura): ?>
                    <tr>
                        <td><?= htmlspecialchars($factura['numero_factura']) ?></td>
                        <td><?= htmlspecialchars($factura['fecha_emision']) ?></td>
                        <td><?= htmlspecialchars($factura['nombre_cliente']) ?></td>
                        <td><?= htmlspecialchars($factura['placa']) ?></td>
                        <td>L. <?= number_format($factura['total_pagar'], 2) ?></td>
                        <td><?= htmlspecialchars($factura['metodo_pago'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= ($factura['estado_activo'] ?? 1) ? 'badge-active' : 'badge-inactive' ?>">
                                <?= ($factura['estado_activo'] ?? 1) ? 'Activa' : 'Anulada' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= APP_URL ?>/facturas/ver/<?= $factura['id_factura'] ?>" class="btn btn-sm btn-edit">
                                Ver
                            </a>
                            <?php if ($factura['estado_activo'] ?? 1): ?>
                                <a href="<?= APP_URL ?>/facturas/anular/<?= $factura['id_factura'] ?>"
                                   class="btn btn-sm btn-delete"
                                   onclick="return confirm('¿Anular esta factura?')">
                                    Anular
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
