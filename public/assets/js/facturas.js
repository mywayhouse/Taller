(function() {
    var servicioIndex = 0;

    function init() {
        var ordenSelect = document.getElementById('id_orden');
        if (!ordenSelect) return;

        var datosContainer = document.getElementById('datosOrdenContainer');
        var repuestosBody = document.getElementById('repuestosBody');
        var btnAgregar = document.getElementById('btnAgregarServicio');

        ordenSelect.addEventListener('change', function() {
            var ordenId = this.value;
            if (!ordenId) {
                datosContainer.style.display = 'none';
                return;
            }

            fetch(obtenerDatosUrl + '?id_orden=' + ordenId)
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    llenarDatosCliente(data);
                    llenarDatosServicioDisp(data);
                    llenarRepuestos(data.repuestos || []);
                    limpiarServicios();
                    if (parseFloat(data.costo_mano_obra) > 0) {
                        agregarFilaServicio('Mano de obra', data.costo_mano_obra);
                    }
                    recalcularTotales();
                    datosContainer.style.display = 'block';
                })
                .catch(function(err) {
                    console.error('Error al obtener datos de la orden:', err);
                    alert('Error al cargar los datos de la orden.');
                });
        });

        if (btnAgregar) {
            btnAgregar.addEventListener('click', function() {
                agregarFilaServicio('', 0);
            });
        }

        var form = document.getElementById('formFactura');
        if (form) {
            form.addEventListener('submit', function() {
                actualizarServiciosJson();
            });
        }
    }

    function llenarDatosCliente(data) {
        document.getElementById('displayCliente').textContent = data.cliente_nombre || '—';
        document.getElementById('displayRtn').textContent = data.rnt_dni || '—';
        document.getElementById('displayTelefono').textContent = data.cliente_telefono || '—';
    }

    function llenarDatosServicioDisp(data) {
        document.getElementById('displayOrden').textContent = '# ' + (data.id_orden || '—');
        document.getElementById('displayFechaIngreso').textContent = data.fecha_ingreso || '—';
        document.getElementById('displayPlaca').textContent = data.placa || '—';
        document.getElementById('displayVehiculo').textContent = (data.marca || '') + ' ' + (data.modelo || '');
        seleccionarSiExiste('id_recepcionista_factura', data.recepcionista);
        seleccionarSiExiste('id_mecanico_factura', data.mecanico);
    }

    function seleccionarSiExiste(selectId, nombre) {
        var sel = document.getElementById(selectId);
        if (!sel || !nombre) return;
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].text === nombre) {
                sel.selectedIndex = i;
                break;
            }
        }
    }

    function llenarRepuestos(repuestos) {
        var body = document.getElementById('repuestosBody');
        if (!repuestos || repuestos.length === 0) {
            body.innerHTML = '<tr><td colspan="4" class="text-center">Sin repuestos en esta orden.</td></tr>';
            return;
        }
        var html = '';
        for (var i = 0; i < repuestos.length; i++) {
            var r = repuestos[i];
            html += '<tr>' +
                '<td>' + (r.repuesto_nombre || '') + '</td>' +
                '<td>' + (parseInt(r.cantidad) || 0) + '</td>' +
                '<td>L. ' + formatMoney(r.precio_unitario_historico) + '</td>' +
                '<td>L. ' + formatMoney(r.total_linea) + '</td>' +
                '</tr>';
        }
        body.innerHTML = html;
    }

    function agregarFilaServicio(descripcion, precio) {
        var body = document.getElementById('serviciosBody');
        if (!body) return;
        var idx = servicioIndex++;
        var tr = document.createElement('tr');
        tr.id = 'servicioFila_' + idx;
        tr.innerHTML =
            '<td><input type="text" class="form-input servicio-desc" value="' + escHtml(descripcion) + '" placeholder="Ej: Cambio de aceite"></td>' +
            '<td><input type="number" class="form-input servicio-precio" step="0.01" min="0" value="' + formatMoney(precio) + '" onchange="recalcularTotales()" onkeyup="recalcularTotales()"></td>' +
            '<td><button type="button" class="btn btn-sm btn-delete" onclick="eliminarFilaServicio(' + idx + ')">Eliminar</button></td>';
        body.appendChild(tr);
        recalcularTotales();
    }

    window.eliminarFilaServicio = function(idx) {
        var tr = document.getElementById('servicioFila_' + idx);
        if (tr) {
            tr.remove();
            recalcularTotales();
        }
    };

    function limpiarServicios() {
        var body = document.getElementById('serviciosBody');
        if (body) body.innerHTML = '';
        servicioIndex = 0;
    }

    window.recalcularTotales = function() {
        var subRepuestos = 0;
        var filasRep = document.querySelectorAll('#repuestosBody tr');
        filasRep.forEach(function(tr) {
            var celdas = tr.querySelectorAll('td');
            if (celdas.length >= 4) {
                var txt = celdas[3].textContent.replace('L. ', '').replace(/,/g, '');
                subRepuestos += parseFloat(txt) || 0;
            }
        });

        var subServicios = 0;
        var inputsPrecio = document.querySelectorAll('.servicio-precio');
        inputsPrecio.forEach(function(inp) {
            subServicios += parseFloat(inp.value) || 0;
        });

        var subtotal = subRepuestos + subServicios;
        var isv = subtotal * 0.15;
        var total = subtotal + isv;

        document.getElementById('totalSubRepuestos').textContent = 'L. ' + formatMoney(subRepuestos);
        document.getElementById('totalSubServicios').textContent = 'L. ' + formatMoney(subServicios);
        document.getElementById('totalSubtotal').textContent = 'L. ' + formatMoney(subtotal);
        document.getElementById('totalIsv').textContent = 'L. ' + formatMoney(isv);
        document.getElementById('totalPagar').textContent = 'L. ' + formatMoney(total);
    };

    function actualizarServiciosJson() {
        var servicios = [];
        var filas = document.querySelectorAll('#serviciosBody tr');
        filas.forEach(function(tr) {
            var desc = tr.querySelector('.servicio-desc');
            var prec = tr.querySelector('.servicio-precio');
            if (desc && prec) {
                var d = desc.value.trim();
                var p = parseFloat(prec.value) || 0;
                if (d || p > 0) {
                    servicios.push({ descripcion: d, precio: p });
                }
            }
        });
        document.getElementById('servicios_json').value = JSON.stringify(servicios);
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function formatMoney(value) {
        return parseFloat(value || 0).toFixed(2);
    }

    init();
})();
