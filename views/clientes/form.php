<!-- ============================================================
     Vista: Formulario de Cliente (Crear / Editar)
     ============================================================ -->
<?php
// Determinar la acción del formulario según si estamos
// creando o editando un cliente.
$esEdicion = !empty($cliente['id_cliente']);
$actionUrl = $esEdicion
    ? APP_URL . '/clientes/actualizar/' . $cliente['id_cliente']
    : APP_URL . '/clientes/guardar';
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
        <label for="nombre">Nombre completo *</label>
        <input
            type="text"
            name="nombre"
            id="nombre"
            value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>"
            required
            maxlength="100"
            placeholder="Ej: Juan Pérez"
        >
    </div>

    <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input
            type="text"
            name="telefono"
            id="telefono"
            value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
            maxlength="20"
            placeholder="Ej: 9999-1234"
        >
    </div>

    <div class="form-group">
        <label for="rnt_dni">RTN / DNI *</label>
        <input
            type="text"
            name="rnt_dni"
            id="rnt_dni"
            value="<?= htmlspecialchars($cliente['rnt_dni'] ?? '') ?>"
            required
            maxlength="20"
            placeholder="Ej: 0801-1990-12345"
        >
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?= $esEdicion ? 'Actualizar Cliente' : 'Guardar Cliente' ?>
        </button>
        <a href="<?= APP_URL ?>/clientes" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
