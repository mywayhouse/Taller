<div class="factura-page">
    <div class="encabezado">
        <div class="empresa-nombre"><?= htmlspecialchars(EMPRESA_NOMBRE) ?></div>
        <div class="empresa">
            <?= htmlspecialchars(EMPRESA_DIRECCION) ?><br>
            Tel: <?= htmlspecialchars(EMPRESA_TELEFONO) ?> &nbsp;&nbsp; RTN: <?= htmlspecialchars(EMPRESA_RTN) ?>
        </div>
    </div>

    <div class="linea"></div>

    <table class="bloque-info">
        <tr>
            <td class="col-izquierda">
                <div class="label">FACTURAR A</div>
                <div class="valor"><?= htmlspecialchars($factura['cliente_nombre']) ?></div>
                <div class="detalle">RTN: <?= htmlspecialchars($factura['rnt_dni']) ?></div>
                <div class="detalle">Tel: <?= htmlspecialchars($factura['cliente_telefono'] ?? '-') ?></div>
            </td>
            <td class="col-derecha">
                <div class="label">DATOS FACTURA</div>
                <table class="datos-tabla">
                    <tr><td class="dt-label">N°:</td><td><?= htmlspecialchars($factura['numero_factura']) ?></td></tr>
                    <tr><td class="dt-label">Fecha:</td><td><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td></tr>
                    <tr><td class="dt-label"># Orden:</td><td><?= htmlspecialchars($factura['id_orden']) ?></td></tr>
                    <tr><td class="dt-label">Recepcionista:</td><td><?= htmlspecialchars($factura['recepcionista_nombre']) ?></td></tr>
                    <tr><td class="dt-label">Mecánico:</td><td><?= htmlspecialchars($factura['mecanico_nombre']) ?></td></tr>
                    <tr><td class="dt-label">Pago:</td><td><?= htmlspecialchars($factura['metodo_pago'] ?? '-') ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="linea"></div>

    <table class="items-tabla">
        <thead>
            <tr>
                <th class="th-cant">CANT</th>
                <th class="th-desc">DESCRIPCIÓN</th>
                <th class="th-precio">PRECIO U.</th>
                <th class="th-importe">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($repuestos)): ?>
                <?php foreach ($repuestos as $r): ?>
                    <tr>
                        <td class="td-cant"><?= (int)$r['cantidad'] ?></td>
                        <td><?= htmlspecialchars($r['repuesto_nombre']) ?></td>
                        <td class="td-precio">L. <?= number_format($r['precio_unitario_historico'], 2) ?></td>
                        <td class="td-importe">L. <?= number_format($r['total_linea'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr>
                <td class="td-cant">1</td>
                <td>Mano de obra</td>
                <td class="td-precio">L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></td>
                <td class="td-importe">L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="linea"></div>

    <div class="totales">
        <div class="total-item">
            <span>Subtotal Repuestos:</span>
            <span>L. <?= number_format($factura['subtotal_repuestos'], 2) ?></span>
        </div>
        <div class="total-item">
            <span>Subtotal Servicios:</span>
            <span>L. <?= number_format($factura['subtotal_mano_obra'], 2) ?></span>
        </div>
        <div class="total-item">
            <span>Subtotal:</span>
            <span>L. <?= number_format($factura['subtotal_repuestos'] + $factura['subtotal_mano_obra'], 2) ?></span>
        </div>
        <div class="total-item">
            <span>ISV 15%:</span>
            <span>L. <?= number_format($factura['isv'], 2) ?></span>
        </div>
        <div class="total-item total-final">
            <span>TOTAL:</span>
            <span>L. <?= number_format($factura['total_pagar'], 2) ?></span>
        </div>
    </div>

    <div class="linea"></div>
</div>
