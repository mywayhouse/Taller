<?php
$esEdicion = !empty($repuesto['id_repuesto']);
$actionUrl = $esEdicion
    ? APP_URL . '/repuestos/actualizar/' . $repuesto['id_repuesto']
    : APP_URL . '/repuestos/guardar';
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

<form action="<?= $actionUrl ?>" method="POST" class="form">
    <div class="form-group">
        <label for="nombre">Nombre del repuesto *</label>
        <input type="text" name="nombre" id="nombre"
               value="<?= htmlspecialchars($repuesto['nombre'] ?? '') ?>"
               required maxlength="100" placeholder="Ej: Filtro de aceite">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="stock_actual">Stock actual *</label>
            <input type="number" name="stock_actual" id="stock_actual"
                   value="<?= htmlspecialchars($repuesto['stock_actual'] ?? '0') ?>"
                   required min="0" step="1">
        </div>

        <div class="form-group">
            <label for="stock_minimo">Stock mínimo *</label>
            <input type="number" name="stock_minimo" id="stock_minimo"
                   value="<?= htmlspecialchars($repuesto['stock_minimo'] ?? '0') ?>"
                   required min="0" step="1">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="unidad_medida">Unidad de medida *</label>
            <select name="unidad_medida" id="unidad_medida" required>
                <option value="">Seleccione...</option>
                <?php $um = htmlspecialchars($repuesto['unidad_medida'] ?? ''); ?>
                <option value="UNIDAD"  <?= $um === 'UNIDAD' ? 'selected' : '' ?>>Unidad</option>
                <option value="LITRO"   <?= $um === 'LITRO' ? 'selected' : '' ?>>Litro</option>
                <option value="GALON"   <?= $um === 'GALON' ? 'selected' : '' ?>>Galón</option>
                <option value="KILO"    <?= $um === 'KILO' ? 'selected' : '' ?>>Kilogramo</option>
                <option value="LIBRA"   <?= $um === 'LIBRA' ? 'selected' : '' ?>>Libra</option>
                <option value="CAJA"    <?= $um === 'CAJA' ? 'selected' : '' ?>>Caja</option>
                <option value="PAQUETE" <?= $um === 'PAQUETE' ? 'selected' : '' ?>>Paquete</option>
                <option value="METRO"   <?= $um === 'METRO' ? 'selected' : '' ?>>Metro</option>
            </select>
        </div>

        <div class="form-group">
            <label for="precio_venta">Precio de venta (L.) *</label>
            <input type="number" name="precio_venta" id="precio_venta"
                   value="<?= htmlspecialchars($repuesto['precio_venta'] ?? '') ?>"
                   required min="0.01" step="0.01" placeholder="0.00">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $esEdicion ? 'Actualizar Repuesto' : 'Guardar Repuesto' ?>
        </button>
        <a href="<?= APP_URL ?>/repuestos" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
