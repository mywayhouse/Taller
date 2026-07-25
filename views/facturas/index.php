<div class="toolbar">
    <a href="<?= APP_URL ?>/facturas/crear" class="btn btn-primary">+ Nueva Factura</a>
</div>

<form method="GET" action="<?= APP_URL ?>/facturas" class="search-bar">
    <div class="search-row">
        <input type="text" name="q" placeholder="Buscar por N° factura, cliente o placa..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="form-control search-input">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($q)): ?>
            <a href="<?= APP_URL ?>/facturas" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

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
                        <td><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
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
                            class="btn btn-sm btn-delete btn-confirmar-accion"
                            data-titulo="Anular Factura"
                            data-mensaje="¿Anular esta factura?"
                            data-btn-texto="Sí, anular"
                            data-detalle="Factura #<?= $factura['id_factura'] ?>">
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
