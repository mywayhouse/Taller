<div class="toolbar">
    <a href="<?= APP_URL ?>/repuestos" class="btn btn-secondary">
        Volver a Repuestos
    </a>
</div>

<div class="info-card">
    <p><strong>Repuesto:</strong> <?= htmlspecialchars($repuesto['nombre']) ?></p>
    <p><strong>Stock actual:</strong> <?= $repuesto['stock_actual'] ?> <?= htmlspecialchars($repuesto['unidad_medida'] ?? '') ?></p>
    <p><strong>Stock mínimo:</strong> <?= $repuesto['stock_minimo'] ?></p>
</div>

<?php if (!empty($errores)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Registrar Entrada de Stock</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= APP_URL ?>/repuestos/guardarEntrada/<?= $repuesto['id_repuesto'] ?>" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="cantidad">Cantidad a ingresar *</label>
                    <input type="number" name="cantidad" id="cantidad"
                           required min="1" step="1" placeholder="Ej: 10"
                           style="max-width: 200px;">
                </div>

                <div class="form-group">
                    <label for="id_proveedor">Proveedor</label>
                    <select name="id_proveedor" id="id_proveedor" style="max-width: 300px;">
                        <option value="">-- Seleccione un proveedor --</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?= $p['id_proveedor'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="observacion">Observación</label>
                <input type="text" name="observacion" id="observacion"
                       placeholder="Motivo de la entrada (opcional)" maxlength="255"
                       style="max-width: 400px;">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Entrada</button>
                <a href="<?= APP_URL ?>/repuestos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
