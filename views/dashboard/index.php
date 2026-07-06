<!-- ============================================================
     Vista: Dashboard (Panel Principal)
     ============================================================ -->
<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Órdenes Pendientes</h3>
        <p class="stat-number">0</p>
    </div>
    <div class="stat-card">
        <h3>Clientes Registrados</h3>
        <p class="stat-number">
        <?= isset($totalClientesActivos) ? $totalClientesActivos : 'Error' ?>
        </p>
    </div>
    <div class="stat-card">
        <h3>Vehículos en Taller</h3>
        <p class="stat-number">0</p>
    </div>
    <div class="stat-card">
        <h3>Repuestos Stock Bajo</h3>
        <p class="stat-number">0</p>
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
                <tr>
                    <td colspan="4" class="text-center">No hay órdenes recientes.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
