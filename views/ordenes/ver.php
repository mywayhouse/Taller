<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Detalle general de la orden</h2>
    <a href="<?= APP_URL ?>/ordenes" class="btn btn-secondary">← Volver al listado</a>
</div>

<div class="row" style="display:grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Columna Izquierda: Información y Repuestos -->
    <div>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-body">
                <div class="info-row">
                    <strong>Estado:</strong>
                    <span class="badge <?= match($orden['estado'] ?? '') {
                        'RECIBIDO' => 'badge-warning',
                        'EN PROCESO' => 'badge-info',
                        'PENDIENTE' => 'badge-secondary',
                        'LISTO' => 'badge-success',
                        'ENTREGADO' => 'badge-active',
                        'CANCELADO' => 'badge-inactive',
                        default => 'badge-secondary'
                    } ?>">
                        <?= htmlspecialchars($orden['estado'] ?? '') ?>
                    </span>
                </div>
                <div class="info-row">
                    <strong>Cliente:</strong> <?= htmlspecialchars($orden['nombre_cliente'] ?? '') ?>
                </div>
                <div class="info-row">
                    <strong>Vehículo:</strong> <?= htmlspecialchars(($orden['marca'] ?? '') . ' ' . ($orden['modelo'] ?? '') . ' - ' . ($orden['placa_vehiculo'] ?? '')) ?>
                </div>
                <div class="info-row">
                    <strong>Fecha Ingreso:</strong> <?= !empty($orden['fecha_ingreso']) ? date('d/m/Y H:i', strtotime($orden['fecha_ingreso'])) : '' ?>
                </div>
                <?php if (!empty($orden['fecha_entrega'])): ?>
                    <div class="info-row">
                        <strong>Fecha Entrega:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_entrega'])) ?>
                    </div>
                <?php endif; ?>
                <div class="info-row">
                    <strong>Recepcionista:</strong> <?= htmlspecialchars($orden['recepcionista'] ?? '') ?>
                </div>
                <div class="info-row">
                    <strong>Mecánico:</strong> <?= htmlspecialchars($orden['mecanico'] ?? '') ?>
                </div>
                <div class="info-row">
                    <strong>Tipo de Servicio:</strong>
                    <?php 
                        $tipos = [
                            'REVISION' => 'Solo revisión / diagnóstico', 
                            'CLIENTE_TRAE_REPUESTO' => 'El cliente trae su repuesto', 
                            'REPUESTO_TIENDA' => 'Requiere repuesto de la tienda'
                        ];
                        $tipoKey = $orden['tipo_servicio'] ?? '';
                    ?>
                    <?= $tipos[$tipoKey] ?? htmlspecialchars($tipoKey) ?>
                </div>
                <div class="info-row">
                    <strong>Costo Mano de Obra:</strong> L. <?= number_format((float) ($orden['costo_mano_obra'] ?? 0), 2) ?>
                </div>
                <div class="info-row">
                    <strong>Diagnóstico:</strong>
                    <p><?= nl2br(htmlspecialchars($orden['diagnostico_preliminar'] ?? '')) ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($detalles)): ?>
            <div class="card">
                <div class="card-body">
                    <h3>Repuestos Utilizados</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Repuesto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $d): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($d['repuesto_nombre'] ?? '') ?></td>
                                        <td><?= (int) ($d['cantidad'] ?? 0) ?> <?= htmlspecialchars($d['unidad_medida'] ?? '') ?></td>
                                        <td>L. <?= number_format((float) ($d['precio_unitario_historico'] ?? 0), 2) ?></td>
                                        <td>L. <?= number_format((float) ($d['total_linea'] ?? (($d['cantidad'] ?? 0) * ($d['precio_unitario_historico'] ?? 0))), 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Acciones rápidas -->
    <div>
        <div class="card" style="margin-bottom: 20px; position: sticky; top: 20px;">
            <div class="card-body">
                <form action="<?= APP_URL ?>/ordenes/cambiarEstado/<?= $orden['id_orden'] ?>" method="POST" class="form">
                    <h3 style="margin-bottom: 15px; font-size: 16px;">Cambiar Estado</h3>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <select name="estado" class="form-control" required>
                            <option value="RECIBIDO" <?= ($orden['estado'] ?? '') === 'RECIBIDO' ? 'selected' : '' ?>>Recibido (R)</option>
                            <option value="EN PROCESO" <?= ($orden['estado'] ?? '') === 'EN PROCESO' ? 'selected' : '' ?>>En Proceso (P)</option>
                            <option value="PENDIENTE" <?= ($orden['estado'] ?? '') === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente (P)</option>
                            <option value="LISTO" <?= ($orden['estado'] ?? '') === 'LISTO' ? 'selected' : '' ?>>Listo (L)</option>
                            <option value="ENTREGADO" <?= ($orden['estado'] ?? '') === 'ENTREGADO' ? 'selected' : '' ?>>Entregado</option>
                            <option value="CANCELADO" <?= ($orden['estado'] ?? '') === 'CANCELADO' ? 'selected' : '' ?>>Cancelado (C)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Actualizar Estado</button>
                </form>

                <hr style="margin: 20px 0; border:0; border-top:1px solid #eee;">

                <div style="display:flex; flex-direction:column; gap: 10px;">
                    <a href="<?= APP_URL ?>/ordenes/editar/<?= $orden['id_orden'] ?>" class="btn btn-secondary" style="text-align:center;">Editar Orden completa</a>
                    <a href="<?= APP_URL ?>/ordenes/eliminar/<?= $orden['id_orden'] ?>" class="btn btn-delete" style="text-align:center;" onclick="return confirm('¿Está seguro de eliminar esta orden?')">Eliminar Orden</a>
                </div>
            </div>
        </div>
    </div>
</div>