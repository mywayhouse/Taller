<!-- ============================================================
     Vista: Dashboard (Panel Principal)
     ============================================================ -->
<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Órdenes Pendientes</h3>
        <p class="stat-number"><?= $ordenes_pendientes ?? 0 ?></p>
    </div>
    <div class="stat-card">
        <h3>Clientes Registrados</h3>
        <p class="stat-number"><?= $clientes_activos ?? 0 ?></p>
    </div>
    <div class="stat-card">
        <h3>Vehículos en Taller</h3>
        <p class="stat-number"><?= $vehiculos_en_taller ?? 0 ?></p>
    </div>
    <div class="stat-card">
        <h3>Repuestos Stock Bajo</h3>
        <p class="stat-number"><?= $repuestos_stock_bajo ?? 0 ?></p>
    </div>
    <div class="stat-card">
        <h3>Tiempo promedio de pedidos</h3>
        <p class="stat-number"><?= $tiempo_promedio_pedidos ?? 0 ?></p> <!-- LEVOM: AQIO ESTA ÁRA QIE LLAMES EL PROCEDIMIENTO DEL PROMEDIO DE TIEMPO DE FINALIZACION DE PEDIDOS, OKEY?-->
    </div>
</div>
<!--CHARTS-->
<div class="content-chart">

    <div class="chart-rep">
        <canvas id="graficoRepuestos"></canvas>
    </div>

    <div class="chart-Ingreso">
        <canvas id="graficoIngresosMecanico"></canvas>
    </div>
</div>



<div class="dashboard-panels">
    <div class="panel">
        <h3>Últimas Órdenes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th># Orden</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ultimasOrdenes)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay órdenes recientes.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ultimasOrdenes as $orden): ?>
                        <tr>
                            <td><?= $orden['id_orden'] ?></td>
                            <td><?= htmlspecialchars($orden['nombre_cliente'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($orden['marca'] ?? '') . ' ' . htmlspecialchars($orden['modelo'] ?? '') ?></td>
                            <td><span class="badge badge-<?= strtolower(str_replace(' ', '-', $orden['estado'] ?? '')) ?>"><?= $orden['estado'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>