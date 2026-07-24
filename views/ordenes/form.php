<?php $isEdit = !empty($orden['id_orden']); ?>
<form action="<?= APP_URL ?>/ordenes/<?= $isEdit ? 'actualizar/' . $orden['id_orden'] : 'guardar' ?>" method="POST" class="form" id="formOrden">
    <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errores as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if ($isEdit): ?>
        <div class="form-group">
            <label for="placa_vehiculo">Vehículo *</label>
            <select name="placa_vehiculo" id="placa_vehiculo" class="form-control" required>
                <option value="">-- Seleccione un vehículo --</option>
                <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= htmlspecialchars($v['placa']) ?>" <?= ($orden['placa_vehiculo'] ?? '') === $v['placa'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['placa'] . ' — ' . $v['marca'] . ' ' . $v['modelo'] . ' (' . ($v['nombre_cliente'] ?? '') . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php else: ?>
        <div class="form-group">
            <label for="placa_vehiculo">Placa del Vehículo *</label>
            <input type="text" id="placa_vehiculo" name="placa_vehiculo" class="form-control"
                   value="<?= htmlspecialchars($orden['placa_vehiculo'] ?? '') ?>"
                   placeholder="Ej: ABC1234" required maxlength="15">
            <small class="form-text">Si la placa ya existe, los datos del vehículo se cargarán automáticamente.</small>
        </div>

        <div id="vehiculoExistente" style="display:none;" class="alert alert-success">
            Vehículo encontrado: <strong id="vehiculoInfo"></strong>
            <input type="hidden" id="vehiculo_existe" name="vehiculo_existe" value="0">
        </div>

        <fieldset id="datosVehiculoNuevo" style="border:1px solid #e2e8f0; border-radius:6px; padding:16px; margin-bottom:20px;">
            <legend style="font-weight:600; font-size:14px; color:var(--text-primary);">Datos del nuevo vehículo</legend>

            <div class="form-group">
                <label for="rtn_dni">RTN/DNI del Cliente *</label>
                <div class="input-group">
                    <input type="text" id="rtn_dni" name="rnt_dni" class="form-control"
                           placeholder="Ingrese RTN/DNI" maxlength="20"
                           value="<?= htmlspecialchars($orden['rnt_dni'] ?? '') ?>">
                    <button type="button" id="btnBuscarCliente" class="btn btn-secondary">Buscar</button>
                </div>
                <div id="clienteInfo" style="margin-top:5px; font-weight:bold;"></div>
                <input type="hidden" name="id_cliente" id="id_cliente" value="">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="marca">Marca *</label>
                    <input type="text" id="marca" name="marca" class="form-control"
                           placeholder="Ej: Toyota" required maxlength="50">
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo *</label>
                    <input type="text" id="modelo" name="modelo" class="form-control"
                           placeholder="Ej: Corolla" required maxlength="50">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="anio">Año *</label>
                    <input type="number" id="anio" name="anio" class="form-control"
                           value="<?= date('Y') ?>" required min="1900" max="<?= date('Y') + 1 ?>">
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo *</label>
                    <select name="tipo" id="tipo" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="Sedán">Sedán</option>
                        <option value="SUV">SUV</option>
                        <option value="Pickup">Pickup</option>
                        <option value="Camión">Camión</option>
                        <option value="Motocicleta">Motocicleta</option>
                    </select>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>

    <div class="form-group">
        <label for="fecha_ingreso">Fecha de Ingreso *</label>
        <input type="date" id="fecha_ingreso" name="fecha_ingreso"
               value="<?= $isEdit ? date('Y-m-d', strtotime($orden['fecha_ingreso'])) : ($orden['fecha_ingreso'] ?? date('Y-m-d')) ?>"
               class="form-control" required>
    </div>

    <?php if ($isEdit): ?>
        <div class="form-group">
            <label for="estado">Estado *</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="RECIBIDO" <?= $orden['estado'] === 'RECIBIDO' ? 'selected' : '' ?>>Recibido (R)</option>
                <option value="EN PROCESO" <?= $orden['estado'] === 'EN PROCESO' ? 'selected' : '' ?>>En Proceso (P)</option>
                <option value="PENDIENTE" <?= $orden['estado'] === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente (P)</option>
                <option value="LISTO" <?= $orden['estado'] === 'LISTO' ? 'selected' : '' ?>>Listo (L)</option>
                <option value="ENTREGADO" <?= $orden['estado'] === 'ENTREGADO' ? 'selected' : '' ?>>Entregado</option>
                <option value="CANCELADO" <?= $orden['estado'] === 'CANCELADO' ? 'selected' : '' ?>>Cancelado (C)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Cliente</label>
            <p class="form-control-static"><?= htmlspecialchars($orden['nombre_cliente'] ?? '') ?></p>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="id_recepcionista">Recepcionista *</label>
        <select name="id_recepcionista" id="id_recepcionista" class="form-control" required <?= $isEdit ? 'disabled' : '' ?>>
            <option value="">-- Seleccione --</option>
            <?php foreach ($usuarios as $u): ?>
                <?php if ($u['rol'] === 'RECEPCIONISTA'): ?>
                    <option value="<?= $u['id_usuario'] ?>" <?= ($orden['id_recepcionista'] ?? $_SESSION['usuario_id']) == $u['id_usuario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="id_mecanico">Mecánico Asignado *</label>
        <select name="id_mecanico" id="id_mecanico" class="form-control" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($usuarios as $u): ?>
                <?php if ($u['rol'] === 'MECANICO'): ?>
                    <option value="<?= $u['id_usuario'] ?>" <?= ($orden['id_mecanico'] ?? '') == $u['id_usuario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="tipo_servicio">Tipo de Servicio *</label>
        <select name="tipo_servicio" id="tipo_servicio" class="form-control" required>
            <option value="REVISION" <?= ($orden['tipo_servicio'] ?? 'REVISION') === 'REVISION' ? 'selected' : '' ?>>Solo revisión / diagnóstico</option>
            <option value="CLIENTE_TRAE_REPUESTO" <?= ($orden['tipo_servicio'] ?? '') === 'CLIENTE_TRAE_REPUESTO' ? 'selected' : '' ?>>El cliente trae su repuesto</option>
            <option value="REPUESTO_TIENDA" <?= ($orden['tipo_servicio'] ?? '') === 'REPUESTO_TIENDA' ? 'selected' : '' ?>>Requiere repuesto de la tienda</option>
        </select>
    </div>

    <div class="form-group">
        <label for="diagnostico_preliminar">Diagnóstico / Problema *</label>
        <textarea id="diagnostico_preliminar" name="diagnostico_preliminar"
                  class="form-control" rows="4" required><?= htmlspecialchars($orden['diagnostico_preliminar'] ?? '') ?></textarea>
    </div>

    <?php if ($isEdit): ?>
        <div class="form-group">
            <label for="costo_mano_obra">Costo Mano de Obra (L.)</label>
            <input type="number" id="costo_mano_obra" name="costo_mano_obra"
                   value="<?= htmlspecialchars($orden['costo_mano_obra'] ?? 0) ?>"
                   class="form-control" step="0.01" min="0">
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Actualizar Orden' : 'Guardar Orden' ?></button>
        <a href="<?= APP_URL ?>/ordenes" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php if (!$isEdit): ?>
<script>
var buscarClienteUrl = '<?= APP_URL ?>/vehiculos/buscarClienteAjax';
var inputPlaca = document.getElementById('placa_vehiculo');
var fieldsetNuevo = document.getElementById('datosVehiculoNuevo');
var divExistente = document.getElementById('vehiculoExistente');
var spanInfo = document.getElementById('vehiculoInfo');
var hiddenExiste = document.getElementById('vehiculo_existe');

function verificarPlaca() {
    var placa = inputPlaca.value.trim().toUpperCase();
    inputPlaca.value = placa;
    if (!placa) { fieldsetNuevo.style.display = 'block'; divExistente.style.display = 'none'; hiddenExiste.value = '0'; return; }
    fetch('<?= APP_URL ?>/ordenes/verificarVehiculoAjax?placa=' + encodeURIComponent(placa))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.existe) {
                fieldsetNuevo.style.display = 'none';
                divExistente.style.display = 'block';
                spanInfo.textContent = data.info;
                hiddenExiste.value = '1';
            } else {
                fieldsetNuevo.style.display = 'block';
                divExistente.style.display = 'none';
                hiddenExiste.value = '0';
            }
        });
}

inputPlaca.addEventListener('blur', verificarPlaca);

// Client search
var btnBuscar = document.getElementById('btnBuscarCliente');
var inputRtn = document.getElementById('rtn_dni');
var idCliente = document.getElementById('id_cliente');
var infoDiv = document.getElementById('clienteInfo');

function buscarCliente() {
    var rtn = inputRtn.value.trim();
    if (!rtn) { infoDiv.innerHTML = '<span style="color:red;">Ingrese un RTN/DNI.</span>'; idCliente.value = ''; return; }
    infoDiv.innerHTML = '<span style="color:#666;">Buscando...</span>';
    fetch(buscarClienteUrl + '?rtn=' + encodeURIComponent(rtn))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.exito) { infoDiv.innerHTML = 'Cliente: ' + data.cliente.nombre; idCliente.value = data.cliente.id; }
            else { infoDiv.innerHTML = '<span style="color:red;">' + data.mensaje + '</span>'; idCliente.value = ''; }
        })
        .catch(function() { infoDiv.innerHTML = '<span style="color:red;">Error de conexión.</span>'; idCliente.value = ''; });
}

btnBuscar.addEventListener('click', buscarCliente);
var timer = null;
inputRtn.addEventListener('input', function() { if (timer) clearTimeout(timer); timer = setTimeout(buscarCliente, 500); });

document.getElementById('formOrden').addEventListener('submit', function(e) {
    if (hiddenExiste.value === '0') {
        if (!idCliente.value) { e.preventDefault(); infoDiv.innerHTML = '<span style="color:red;">Busque un cliente válido con el RTN/DNI.</span>'; return; }
        var reqs = ['marca', 'modelo', 'anio', 'tipo'];
        for (var i = 0; i < reqs.length; i++) {
            var el = document.getElementById(reqs[i]);
            if (el && !el.value.trim()) { e.preventDefault(); alert('Complete todos los datos del vehículo.'); el.focus(); return; }
        }
    }
});
</script>
<?php endif; ?>
