<?php
$esEdicion = !empty($vehiculo['placa']);
$actionUrl = $esEdicion
    ? APP_URL . '/vehiculos/actualizar/' . urlencode($vehiculo['placa'])
    : APP_URL . '/vehiculos/guardar';
?>

<?php if (!empty($errores)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= $actionUrl ?>" method="POST" class="form" id="formVehiculo">
    <div class="form-group">
        <label for="placa">Placa *</label>
        <input
            type="text"
            name="placa"
            id="placa"
            value="<?= htmlspecialchars($vehiculo['placa'] ?? '') ?>"
            required
            maxlength="15"
            placeholder="Ej: P123ABC"
            <?= $esEdicion ? 'readonly' : '' ?>
        >
    </div>

    <!-- Selector dinámico de cliente por RTN/DNI -->
    <div class="form-group">
        <label for="rtn_dni">RTN/DNI del cliente *</label>
        <div class="input-group">
            <input
                type="text"
                name="rnt_dni"
                id="rtn_dni"
                placeholder="Ingrese RTN/DNI y presione Buscar"
                maxlength="20"
                value="<?= htmlspecialchars($vehiculo['rnt_dni'] ?? '') ?>"
            >
            <button type="button" id="btnBuscarCliente" class="btn btn-secondary">Buscar</button>
        </div>
        <div id="clienteInfo" style="margin-top:5px; font-weight:bold;">
            <?php if ($esEdicion && !empty($vehiculo['nombre_cliente'])): ?>
                Cliente: <?= htmlspecialchars($vehiculo['nombre_cliente']) ?>
            <?php endif; ?>
        </div>
        <input type="hidden" name="id_cliente" id="id_cliente" value="<?= htmlspecialchars($vehiculo['id_cliente'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="marca">Marca *</label>
        <input
            type="text"
            name="marca"
            id="marca"
            value="<?= htmlspecialchars($vehiculo['marca'] ?? '') ?>"
            required
            maxlength="50"
            placeholder="Ej: Toyota"
        >
    </div>

    <div class="form-group">
        <label for="modelo">Modelo *</label>
        <input
            type="text"
            name="modelo"
            id="modelo"
            value="<?= htmlspecialchars($vehiculo['modelo'] ?? '') ?>"
            required
            maxlength="50"
            placeholder="Ej: Corolla"
        >
    </div>

    <div class="form-group">
        <label for="anio">Año *</label>
        <input
            type="number"
            name="anio"
            id="anio"
            value="<?= htmlspecialchars($vehiculo['anio'] ?? date('Y')) ?>"
            required
            min="1900"
            max="<?= date('Y') + 1 ?>"
        >
    </div>

    <div class="form-group">
        <label for="tipo">Tipo *</label>
        <select name="tipo" id="tipo" required>
            <option value="">Seleccione tipo</option>
            <option value="Sedán" <?= ($vehiculo['tipo'] ?? '') === 'Sedán' ? 'selected' : '' ?>>Sedán</option>
            <option value="SUV" <?= ($vehiculo['tipo'] ?? '') === 'SUV' ? 'selected' : '' ?>>SUV</option>
            <option value="Pickup" <?= ($vehiculo['tipo'] ?? '') === 'Pickup' ? 'selected' : '' ?>>Pickup</option>
            <option value="Camión" <?= ($vehiculo['tipo'] ?? '') === 'Camión' ? 'selected' : '' ?>>Camión</option>
            <option value="Motocicleta" <?= ($vehiculo['tipo'] ?? '') === 'Motocicleta' ? 'selected' : '' ?>>Motocicleta</option>
        </select>
    </div>

    <div id="camposMoto" class="moto-fields" style="display: <?= ($vehiculo['tipo'] ?? '') === 'Motocicleta' ? 'block' : 'none' ?>;">
        <div class="form-group">
            <label for="cilindraje">Cilindraje (CC) *</label>
            <input
                type="number"
                name="cilindraje"
                id="cilindraje"
                value="<?= htmlspecialchars($vehiculo['cilindraje'] ?? '') ?>"
                min="50"
                max="9999"
                placeholder="Ej: 150"
            >
        </div>
        <div class="form-group">
            <label for="tipo_moto">Tipo de Motocicleta *</label>
            <select name="tipo_moto" id="tipo_moto">
                <option value="">Seleccione tipo</option>
                <?php $tiposMoto = ['Deportiva', 'Cruiser', 'Naked', 'Enduro', 'Scooter', 'Touring', 'Doble Propósito']; ?>
                <?php foreach ($tiposMoto as $tm): ?>
                    <option value="<?= $tm ?>" <?= ($vehiculo['tipo_moto'] ?? '') === $tm ? 'selected' : '' ?>><?= $tm ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $esEdicion ? 'Actualizar Vehículo' : 'Guardar Vehículo' ?>
        </button>
        <a href="<?= APP_URL ?>/vehiculos" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
    const buscarClienteUrl = '<?= APP_URL ?>/vehiculos/buscarClienteAjax';

    document.getElementById('tipo').addEventListener('change', function() {
        const camposMoto = document.getElementById('camposMoto');
        const cilindraje = document.getElementById('cilindraje');
        const tipoMoto = document.getElementById('tipo_moto');
        if (this.value === 'Motocicleta') {
            camposMoto.style.display = 'block';
            cilindraje.required = true;
            tipoMoto.required = true;
        } else {
            camposMoto.style.display = 'none';
            cilindraje.required = false;
            tipoMoto.required = false;
        }
    });
</script>