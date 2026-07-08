/**
 * vehiculos.js – Búsqueda de cliente por RTN/DNI
 */
document.addEventListener('DOMContentLoaded', function () {
    const btnBuscar = document.getElementById('btnBuscarCliente');
    const inputRtn  = document.getElementById('rtn_dni');
    const idCliente = document.getElementById('id_cliente');
    const infoDiv   = document.getElementById('clienteInfo');

    if (!btnBuscar || !inputRtn || !idCliente) return;

    btnBuscar.addEventListener('click', function () {
        const rtn = inputRtn.value.trim();
        if (!rtn) {
            infoDiv.innerHTML = '<span style="color:red;">Ingrese un RTN/DNI.</span>';
            idCliente.value = '';
            return;
        }

        fetch(buscarClienteUrl + '?rtn=' + encodeURIComponent(rtn))
            .then(response => response.json())
            .then(data => {
                if (data.exito) {
                    infoDiv.innerHTML = 'Cliente: ' + data.cliente.nombre;
                    idCliente.value = data.cliente.id;
                } else {
                    infoDiv.innerHTML = '<span style="color:red;">' + data.mensaje + '</span>';
                    idCliente.value = '';
                }
            })
            .catch(err => {
                console.error(err);
                infoDiv.innerHTML = '<span style="color:red;">Error de conexión.</span>';
                idCliente.value = '';
            });
    });

    // Si se carga en edición con un valor previo de RTN/DNI, se puede precargar
    // (opcional: buscar automáticamente al cargar la página si ya existe el campo)
    if (inputRtn.value && idCliente.value) {
        // ya se mostró la info desde PHP, no es necesaria otra búsqueda
    }
});