<h1><?= isset($vehiculo['placa']) ? 'Editar Vehículo' : 'Nuevo Vehículo' ?></h1>

<!-- Mostrar errores acumulados -->
<?php if (!empty($errores)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= isset($vehiculo['placa']) ? APP_URL . '/vehiculos/actualizar/' . $vehiculo['placa'] : APP_URL . '/vehiculos/guardar' ?>" method="POST">
    <div class="mb-3">
        <label for="placa" class="form-label">Placa *</label>
        <input type="text" name="placa" id="placa" class="form-control"
               value="<?= htmlspecialchars($vehiculo['placa'] ?? '') ?>"
               required maxlength="15">
    </div>

    <div class="mb-3">
        <label for="marca" class="form-label">Marca *</label>
        <input type="text" name="marca" id="marca" class="form-control"
               value="<?= htmlspecialchars($vehiculo['marca'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label for="modelo" class="form-label">Modelo *</label>
        <input type="text" name="modelo" id="modelo" class="form-control"
               value="<?= htmlspecialchars($vehiculo['modelo'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label for="anio" class="form-label">Año</label>
        <input type="number" name="anio" id="anio" class="form-control"
               value="<?= htmlspecialchars($vehiculo['anio'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>
        <input type="text" name="tipo" id="tipo" class="form-control"
               value="<?= htmlspecialchars($vehiculo['tipo'] ?? '') ?>">
    </div>

    <!-- Selector dinámico de cliente -->
    <div class="mb-3">
        <label for="cliente_search" class="form-label">Buscar Cliente (RTN/DNI o nombre)</label>
        <input type="text" id="cliente_search" class="form-control"
               placeholder="Escriba RTN/DNI o nombre..."
               value="<?= htmlspecialchars($vehiculo['rnt_dni'] ?? '') ?>">
        <input type="hidden" name="id_cliente" id="id_cliente"
               value="<?= htmlspecialchars($vehiculo['id_cliente'] ?? '') ?>">
        <div id="cliente_suggestions" class="list-group mt-1"></div>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="<?= APP_URL ?>/vehiculos" class="btn btn-secondary">Cancelar</a>
</form>

<script>
// Definir APP_URL para JS
const APP_URL = "<?= APP_URL ?>";

document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('cliente_search');
    const suggestions = document.getElementById('cliente_suggestions');
    const idCliente = document.getElementById('id_cliente');

    search.addEventListener('keyup', function() {
        const term = this.value.trim();
        if (term.length < 2) {
            suggestions.innerHTML = '';
            return;
        }
        fetch(`${APP_URL}/vehiculos/buscarClientes?term=${encodeURIComponent(term)}`)
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(c => {
                    html += `<a href="#" class="list-group-item list-group-item-action"
                              data-id="${c.id_cliente}" data-rnt="${c.rnt_dni}">
                              ${c.nombre} (${c.rnt_dni})</a>`;
                });
                suggestions.innerHTML = html;
            })
            .catch(err => console.error(err));
    });

    suggestions.addEventListener('click', e => {
        if (e.target.matches('.list-group-item-action')) {
            e.preventDefault();
            const id = e.target.dataset.id;
            const rnt = e.target.dataset.rnt;
            idCliente.value = id;
            search.value = rnt + ' - ' + e.target.textContent.trim();
            suggestions.innerHTML = '';
        }
    });
});
</script>