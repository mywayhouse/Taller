(function() {
    const ordenSelect = document.getElementById('id_orden');
    if (!ordenSelect) return;

    const datosContainer = document.getElementById('datosOrdenContainer');
    const repuestosBody = document.getElementById('repuestosBody');

    ordenSelect.addEventListener('change', function() {
        const ordenId = this.value;
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
                llenarDatosServicio(data);
                llenarRepuestos(data.repuestos || []);
                llenarManoObra(data.costo_mano_obra);
                calcularTotales(data);
                datosContainer.style.display = 'block';
            })
            .catch(function(err) {
                console.error('Error al obtener datos de la orden:', err);
                alert('Error al cargar los datos de la orden.');
            });
    });

    function llenarDatosCliente(data) {
        document.getElementById('displayCliente').textContent = data.cliente_nombre || '—';
        document.getElementById('displayRtn').textContent = data.rnt_dni || '—';
        document.getElementById('displayTelefono').textContent = data.cliente_telefono || '—';
    }

    function llenarDatosServicio(data) {
        document.getElementById('displayOrden').textContent = '# ' + (data.id_orden || '—');
        document.getElementById('displayFechaIngreso').textContent = data.fecha_ingreso || '—';
        document.getElementById('displayPlaca').textContent = data.placa || '—';
        document.getElementById('displayVehiculo').textContent = (data.marca || '') + ' ' + (data.modelo || '');
        document.getElementById('displayRecepcionista').textContent = data.recepcionista || '—';
        document.getElementById('displayMecanico').textContent = data.mecanico || '—';
    }

    function llenarRepuestos(repuestos) {
        if (!repuestos || repuestos.length === 0) {
            repuestosBody.innerHTML = '<tr><td colspan="4" class="text-center">Sin repuestos en esta orden.</td></tr>';
            return;
        }
        var html = '';
        for (var i = 0; i < repuestos.length; i++) {
            var r = repuestos[i];
            var total = parseFloat(r.total_linea || 0);
            html += '<tr>' +
                '<td>' + (r.repuesto_nombre || '') + '</td>' +
                '<td>' + (parseInt(r.cantidad) || 0) + '</td>' +
                '<td>L. ' + formatMoney(r.precio_unitario_historico) + '</td>' +
                '<td>L. ' + formatMoney(total) + '</td>' +
                '</tr>';
        }
        repuestosBody.innerHTML = html;
    }

    function llenarManoObra(costo) {
        var input = document.getElementById('costo_mano_obra');
        if (input) {
            input.value = parseFloat(costo || 0).toFixed(2);
        }
    }

    function calcularTotales(data) {
        var subRepuestos = 0;
        var repuestos = data.repuestos || [];
        for (var i = 0; i < repuestos.length; i++) {
            subRepuestos += parseFloat(repuestos[i].total_linea || 0);
        }
        var subManoObra = parseFloat(data.costo_mano_obra || 0);
        var subtotal = subRepuestos + subManoObra;
        var isv = subtotal * 0.15;
        var total = subtotal + isv;

        document.getElementById('totalSubRepuestos').textContent = 'L. ' + formatMoney(subRepuestos);
        document.getElementById('totalSubManoObra').textContent = 'L. ' + formatMoney(subManoObra);
        document.getElementById('totalSubtotal').textContent = 'L. ' + formatMoney(subtotal);
        document.getElementById('totalIsv').textContent = 'L. ' + formatMoney(isv);
        document.getElementById('totalPagar').textContent = 'L. ' + formatMoney(total);
    }

    function formatMoney(value) {
        return parseFloat(value || 0).toFixed(2);
    }
})();
