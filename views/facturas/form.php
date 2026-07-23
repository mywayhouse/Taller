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
        </div>
    </div>

    <form action="<?= APP_URL ?>/facturas/guardar" method="POST" class="form" id="formFactura">
        <input type="hidden" name="numero_factura" id="numero_factura" value="<?= htmlspecialchars($numeroFactura) ?>">

        <div class="factura-body">
            <div class="form-group">
                <label for="id_orden">Orden de Servicio *</label>
                <select name="id_orden" id="id_orden" required>
                    <option value="">-- Seleccione una orden --</option>
                    <?php foreach ($ordenesDisponibles as $orden): ?>
                        <option value="<?= $orden['id_orden'] ?>">
                            #<?= $orden['id_orden'] ?> — <?= htmlspecialchars($orden['nombre_cliente']) ?>
                            (<?= htmlspecialchars($orden['placa']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="datosOrdenContainer" style="display:none;">
                <div class="factura-seccion">
                    <h3>Datos del Cliente</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Cliente</label>
                            <p class="factura-dato" id="displayCliente">—</p>
                        </div>
                        <div class="form-group">
                            <label>RTN/DNI</label>
                            <p class="factura-dato" id="displayRtn">—</p>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <p class="factura-dato" id="displayTelefono">—</p>
                        </div>
                    </div>
                </div>

                <div class="factura-seccion">
                    <h3>Datos del Servicio</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>N° Orden</label>
                            <p class="factura-dato" id="displayOrden">—</p>
                        </div>
                        <div class="form-group">
                            <label>Fecha Ingreso</label>
                            <p class="factura-dato" id="displayFechaIngreso">—</p>
                        </div>
                        <div class="form-group">
                            <label>Placa</label>
                            <p class="factura-dato" id="displayPlaca">—</p>
                        </div>
                        <div class="form-group">
                            <label>Vehículo</label>
                            <p class="factura-dato" id="displayVehiculo">—</p>
                        </div>
                        <div class="form-group">
                            <label>Recepcionista</label>
                            <p class="factura-dato" id="displayRecepcionista">—</p>
                        </div>
                        <div class="form-group">
                            <label>Mecánico</label>
                            <p class="factura-dato" id="displayMecanico">—</p>
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
                    <h3>Mano de Obra</h3>
                    <div class="form-group">
                        <label for="costo_mano_obra">Costo Mano de Obra (L.)</label>
                        <input type="number" name="costo_mano_obra" id="costo_mano_obra"
                               step="0.01" min="0" value="0" readonly>
                    </div>
                </div>

                <div class="factura-totales">
                    <div class="total-row">
                        <span>Subtotal Repuestos:</span>
                        <span id="totalSubRepuestos">L. 0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Subtotal Mano de Obra:</span>
                        <span id="totalSubManoObra">L. 0.00</span>
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

<script>
    const obtenerDatosUrl = '<?= APP_URL ?>/facturas/obtenerDatosOrdenAjax';
</script>
