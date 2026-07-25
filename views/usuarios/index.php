<div class="toolbar">
    <button type="button" id="btn-nuevo-usuario" class="btn btn-primary">
        + Nuevo Usuario
    </button>
</div>

<form method="GET" action="<?= APP_URL ?>/usuarios" class="search-bar">
    <div class="search-row">
        <input type="text" name="q" placeholder="Buscar por nombre o correo..."
               value="<?= htmlspecialchars($q ?? '') ?>" class="form-control search-input">
        <select name="rol" class="form-control search-select">
            <option value="">Todos los roles</option>
            <option value="ADMINISTRADOR" <?= ($rolFiltro ?? '') === 'ADMINISTRADOR' ? 'selected' : '' ?>>Administrador</option>
            <option value="RECEPCIONISTA" <?= ($rolFiltro ?? '') === 'RECEPCIONISTA' ? 'selected' : '' ?>>Recepcionista</option>
            <option value="MECANICO" <?= ($rolFiltro ?? '') === 'MECANICO' ? 'selected' : '' ?>>Mecánico</option>
        </select>
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($q) || !empty($rolFiltro)): ?>
            <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['correo']) ?></td>
                        <td><?= htmlspecialchars($u['rol']) ?></td>
                        <td>
                            <span class="badge <?= $u['estado_activo'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $u['estado_activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <!-- Botón Editar adaptado a modal con data-attributes -->
                            <button type="button"
                                    class="btn btn-sm btn-edit btn-editar-usuario"
                                    data-id="<?= $u['id_usuario'] ?>"
                                    data-nombre="<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>"
                                    data-correo="<?= htmlspecialchars($u['correo'], ENT_QUOTES) ?>"
                                    data-rol="<?= htmlspecialchars($u['rol'], ENT_QUOTES) ?>">
                                Editar
                            </button>

                            <?php if ((int)$u['id_usuario'] !== (int)($_SESSION['usuario_id'] ?? 0)): ?>
                            <form method="POST" action="<?= APP_URL ?>/usuarios/eliminar/<?= $u['id_usuario'] ?>" style="display:inline;" class="form-eliminar-usuario">
                                <button type="button" class="btn btn-sm btn-delete"
                                        onclick="mostrarModalEliminar(this, '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>')">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Eliminar Usuario -->
<div id="modal-eliminar-usuario" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3>Desactivar Usuario</h3>
        <p>¿Desactivar este usuario?</p>
        <p class="modal-detalle"><strong id="modal-nombre-usuario"></strong></p>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalEliminar()">No, volver</button>
            <button type="button" class="btn btn-delete" onclick="confirmarEliminar()">Sí, desactivar</button>
        </div>
    </div>
</div>

<!-- Incluimos el formulario modal dentro del mismo index -->
<?php require VIEWS . '/usuarios/form.php'; ?>

<script>
var formEliminarActual = null;

function mostrarModalEliminar(btn, nombre) {
    formEliminarActual = btn.closest('form');
    document.getElementById('modal-nombre-usuario').textContent = nombre;
    document.getElementById('modal-eliminar-usuario').style.display = 'flex';
}

function cerrarModalEliminar() {
    document.getElementById('modal-eliminar-usuario').style.display = 'none';
    formEliminarActual = null;
}

function confirmarEliminar() {
    if (formEliminarActual) {
        formEliminarActual.submit();
    }
}

document.getElementById('modal-eliminar-usuario').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalEliminar();
});
</script>

<style>
.modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 9999;
    display: flex; align-items: center; justify-content: center;
}
.modal-box {
    background: #fff; border-radius: 8px; padding: 30px;
    max-width: 420px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    text-align: center;
}
.modal-box h3 { color: #c62828; margin: 0 0 10px; font-size: 18px; }
.modal-box p { margin: 5px 0; font-size: 14px; color: #333; }
.modal-detalle { font-size: 12px; color: #666; margin-bottom: 15px !important; }
.modal-actions { display: flex; justify-content: center; gap: 10px; margin-top: 15px; }
</style>