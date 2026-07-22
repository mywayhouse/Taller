<?php
/** @var array $ordenesDisponibles */
/** @var string $numeroFactura */
/** @var array $recepcionistas */
/** @var array $mecanicos */
/** @var array $errores */
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

<div class="factura-wrapper">
    <div class="factura-print">
        <div class="factura-header">
            <div class="factura-empresa">
                <h2><?= htmlspecialchars(EMPRESA_NOMBRE) ?></h2>
                <p><?= htmlspecialchars(EMPRESA_DIRECCION) ?></p>
                <p>Tel: <?= htmlspecialchars(EMPRESA_TELEFONO) ?></p>
                <p>RTN: <?= htmlspecialchars(EMPRESA_RTN) ?></p>
            </div>
            <div class="factura-titulo">
                <h1>FACTURA</h1>
                <p><strong>N°:</strong> <span id="lblNumeroFactura"><?= htmlspecialchars($numeroFactura) ?></span></p>
                <p><strong># Orden:</strong> <span id="displayOrden">—</span></p>
                <p><strong>Fecha Ingreso:</strong> <span id="displayFechaIngreso">—</span></p>
            </div>
        </div>

        <form action="<?= APP_URL ?>/facturas/guardar" method="POST" class="form" id="formFactura">
            <input type="hidden" name="numero_factura" id="numero_factura" value="<?= htmlspecialchars($numeroFactura) ?>">

            <div class="factura-body">
                <div class="factura-seccion">
                    <h3>Seleccionar Orden de Servicio</h3>
                    <div class="form-group">
                        <select name="id_orden" id="id_orden" required>
                            <option value="">-- Seleccione una orden --</option>
                            <?php foreach ($ordenesDisponibles as $orden): ?>
                                <option value="<?= htmlspecialchars($orden['id_orden']) ?>">
                                    #<?= $orden['id_orden'] ?> — <?= htmlspecialchars($orden['nombre_cliente']) ?>
                                    (<?= htmlspecialchars($orden['placa']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="datosOrdenContainer" style="display:none;">
                    <div class="factura-seccion">
                        <h3>Información del Cliente y Personal</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Cliente</span>
                                <span class="info-value" id="displayCliente">—</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">RTN/DNI</span>
                                <span class="info-value" id="displayRtn">—</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Teléfono</span>
                                <span class="info-value" id="displayTelefono">—</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Placa</span>
                                <span class="info-value" id="displayPlaca">—</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Vehículo</span>
                                <span class="info-value" id="displayVehiculo">—</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Recepcionista</span>
                                <select name="id_recepcionista_factura" id="id_recepcionista_factura" class="form-input">
                                    <option value="">-- Seleccione --</option>
                                    <?php foreach ($recepcionistas as $r): ?>
                                        <option value="<?= $r['id_usuario'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Mecánico</span>
                                <select name="id_mecanico_factura" id="id_mecanico_factura" class="form-input">
                                    <option value="">-- Seleccione --</option>
                                    <?php foreach ($mecanicos as $m): ?>
                                        <option value="<?= $m['id_usuario'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="factura-seccion">
                        <h3>Detalle de Repuestos</h3>
                        <div class="table-responsive">
                            <table class="table" id="tablaRepuestos">
                                <thead>
                                    <tr>
                                        <th>Repuesto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="repuestosBody">
                                    <tr>
                                        <td colspan="4" class="text-center">Seleccione una orden para cargar los repuestos.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="factura-seccion">
                        <h3>Servicios</h3>
                        <p class="text-muted" style="margin-bottom:10px;font-size:13px;">Agregue los servicios realizados (mano de obra, diagnóstico, etc.)</p>
                        <div class="table-responsive">
                            <table class="table" id="tablaServicios">
                                <thead>
                                    <tr>
                                        <th style="width:55%;">Descripción</th>
                                        <th style="width:25%;">Precio (L.)</th>
                                        <th style="width:20%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="serviciosBody">
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="btnAgregarServicio" class="btn btn-sm btn-secondary">+ Agregar Servicio</button>
                        <input type="hidden" name="servicios_json" id="servicios_json" value="">
                    </div>

                    <div class="factura-totales">
                        <div class="total-row">
                            <span>Subtotal Repuestos:</span>
                            <span id="totalSubRepuestos">L. 0.00</span>
                        </div>
                        <div class="total-row">
                            <span>Subtotal Servicios:</span>
                            <span id="totalSubServicios">L. 0.00</span>
                        </div>
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span id="totalSubtotal">L. 0.00</span>
                        </div>
                        <div class="total-row">
                            <span>ISV (15%):</span>
                            <span id="totalIsv">L. 0.00</span>
                        </div>
                        <div class="total-row total-final">
                            <span>Total a Pagar:</span>
                            <span id="totalPagar">L. 0.00</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="metodo_pago">Método de Pago *</label>
                        <select name="metodo_pago" id="metodo_pago" required>
                            <option value="">-- Seleccione --</option>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Generar Factura</button>
                        <a href="<?= APP_URL ?>/facturas" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const obtenerDatosUrl = '<?= APP_URL ?>/facturas/obtenerDatosOrdenAjax';
</script>
