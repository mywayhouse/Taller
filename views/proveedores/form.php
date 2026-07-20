<?php
$esEdicion = !empty($proveedor['id_proveedor']);
$actionUrl = $esEdicion
    ? APP_URL . '/proveedores/actualizar/' . $proveedor['id_proveedor']
    : APP_URL . '/proveedores/guardar';
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
        <label for="nombre">Nombre del proveedor *</label>
        <input type="text" name="nombre" id="nombre"
               value="<?= htmlspecialchars($proveedor['nombre'] ?? '') ?>"
               required maxlength="150" placeholder="Ej: Repuestos García S.A.">
    </div>

    <div class="form-group">
        <label for="contacto">Nombre del contacto</label>
        <input type="text" name="contacto" id="contacto"
               value="<?= htmlspecialchars($proveedor['contacto'] ?? '') ?>"
               maxlength="100" placeholder="Ej: Juan Pérez">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono"
                   value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>"
                   maxlength="20" placeholder="Ej: 9999-1234">
        </div>

        <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input type="email" name="correo" id="correo"
                   value="<?= htmlspecialchars($proveedor['correo'] ?? '') ?>"
                   maxlength="100" placeholder="Ej: contacto@proveedor.com">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="rtn">RTN</label>
            <input type="text" name="rtn" id="rtn"
                   value="<?= htmlspecialchars($proveedor['rtn'] ?? '') ?>"
                   maxlength="20" placeholder="Ej: 08019001234567">
        </div>
    </div>

    <div class="form-group">
        <label for="direccion">Dirección</label>
        <textarea name="direccion" id="direccion" rows="3"
                  maxlength="500" placeholder="Dirección del proveedor"><?= htmlspecialchars($proveedor['direccion'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $esEdicion ? 'Actualizar Proveedor' : 'Guardar Proveedor' ?>
        </button>
        <a href="<?= APP_URL ?>/proveedores" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
