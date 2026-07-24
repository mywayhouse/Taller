<div class="toolbar">
    <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary">← Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($errores)): ?>
            <div class="alert alert-error">
                <?php foreach ($errores as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/usuarios/<?= $usuario['id_usuario'] ? 'actualizar/' . $usuario['id_usuario'] : 'guardar' ?>"
              method="POST" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" class="form-control"
                           value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="rol" class="form-label">Rol</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        <option value="ADMINISTRADOR" <?= ($usuario['rol'] ?? '') === 'ADMINISTRADOR' ? 'selected' : '' ?>>Administrador</option>
                        <option value="RECEPCIONISTA" <?= ($usuario['rol'] ?? '') === 'RECEPCIONISTA' ? 'selected' : '' ?>>Recepcionista</option>
                        <option value="MECANICO" <?= ($usuario['rol'] ?? '') === 'MECANICO' ? 'selected' : '' ?>>Mecánico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contrasenia" class="form-label">
                        <?= $usuario['id_usuario'] ? 'Nueva contraseña (dejar vacío para mantener)' : 'Contraseña' ?>
                    </label>
                    <input type="password" id="contrasenia" name="contrasenia" class="form-control"
                           <?= !$usuario['id_usuario'] ? 'required' : '' ?>
                           minlength="6">
                    <small class="form-text">Mínimo 6 caracteres, al menos una mayúscula y un número.</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $usuario['id_usuario'] ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                </button>
                <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
