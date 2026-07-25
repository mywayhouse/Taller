<?php
$esEdicion = !empty($usuario['id_usuario']);
$actionUrl = $esEdicion
    ? APP_URL . '/usuarios/actualizar/' . $usuario['id_usuario']
    : APP_URL . '/usuarios/guardar';
?>

<div id="modal-usuario" class="modal <?= !empty($errores) ? 'show' : '' ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titulo"><?= $esEdicion ? 'Editar Usuario' : 'Nuevo Usuario' ?></h3>
            <button type="button" class="close-btn" id="btn-cerrar-modal">&times;</button>
        </div>

        <form action="<?= $actionUrl ?>" method="POST" id="form-usuario" class="form">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errores as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre completo *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo" class="form-label">Correo electrónico *</label>
                    <input type="email" id="correo" name="correo" class="form-control"
                           value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="rol" class="form-label">Rol *</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        <?php $rSel = $usuario['rol'] ?? ''; ?>
                        <option value="ADMINISTRADOR" <?= $rSel === 'ADMINISTRADOR' ? 'selected' : '' ?>>Administrador</option>
                        <option value="RECEPCIONISTA" <?= $rSel === 'RECEPCIONISTA' ? 'selected' : '' ?>>Recepcionista</option>
                        <option value="MECANICO" <?= $rSel === 'MECANICO' ? 'selected' : '' ?>>Mecánico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contrasenia" class="form-label" id="label-contrasenia">
                        <?= $esEdicion ? 'Nueva contraseña (dejar vacío para mantener)' : 'Contraseña *' ?>
                    </label>
                    <input type="password" id="contrasenia" name="contrasenia" class="form-control"
                           <?= !$esEdicion ? 'required' : '' ?> minlength="6">
                    <small class="form-text">Mínimo 6 caracteres, al menos una mayúscula y un número.</small>
                </div>
            </div>

            <div class="modal-footer form-actions">
                <button type="submit" class="btn btn-primary" id="btn-guardar">
                    <?= $esEdicion ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                </button>
                <button type="button" class="btn btn-secondary" id="btn-cancelar-modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>