// inventario/js/inventario.js
// Lógica de control e interconexión asíncrona para S.A.V. Corporativo

document.addEventListener("DOMContentLoaded", function () {
    const formNuevo = document.getElementById("form-nuevo-repuesto");

    // 1. CONTROL DE REGISTRO CON ENVÍO REAL AL BACKEND
    if (formNuevo) {
        formNuevo.addEventListener("submit", function (e) {
            e.preventDefault(); // Detener recarga automática de la página

            const nombre = document.getElementById("txt-nombre").value.trim();
            const stock = document.getElementById("txt-stock").value.trim();
            const stockMin = document.getElementById("txt-stock-minimo").value.trim();
            const unidad = document.getElementById("txt-unidad").value;
            const precio = document.getElementById("txt-precio").value.trim();

            // Validar campos vacíos
            if (nombre === "" || stock === "" || stockMin === "" || unidad === "" || precio === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Incompletos',
                    text: 'Por favor, rellene todos los campos requeridos del formulario.',
                    confirmButtonColor: '#0284c7'
                });
                return;
            }

            // Validar números negativos
            if (parseFloat(stock) < 0 || parseFloat(stockMin) < 0 || parseFloat(precio) < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Valores Inválidos',
                    text: 'Las cantidades de stock y precios no pueden ser números negativos.',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            // Preparar los datos estructurados para mandarlos por POST
            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('stock', stock);
            formData.append('stock_minimo', stockMin);
            formData.append('unidad', unidad);
            formData.append('precio', precio);

            // Mostrar animación de carga mientras procesa
            Swal.fire({
                title: 'Procesando registro...',
                text: 'Guardando datos de forma segura en el servidor.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Enviar datos mediante AJAX (Fetch API) a guardar_repuesto.php
            fetch('guardar_repuesto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro Exitoso!',
                        text: data.message,
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'Ver Almacén'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'inventario_general.php'; // Redirige al catálogo
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Servidor',
                        text: data.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Fallo de Comunicación',
                    text: 'No se pudo conectar con el controlador de almacenamiento.',
                    confirmButtonColor: '#ef4444'
                });
                console.error("Error en Fetch: ", error);
            });
        });
    }

    // 2. BUSCADOR EN TIEMPO REAL
    const txtBuscar = document.getElementById("txt-buscar");
    if (txtBuscar) {
        txtBuscar.addEventListener("keyup", function () {
            const valor = this.value.toLowerCase();
            const filas = document.querySelectorAll(".table-custom tbody tr");

            filas.forEach(fila => {
                const textoColumna = fila.cells[1].textContent.toLowerCase();
                if (textoColumna.includes(valor)) {
                    fila.style.display = "";
                } else {
                    fila.style.display = "none";
                }
            });
        });
    }
});

// 3. CONTROL DE ELIMINACIÓN DE REGISTROS
function confirmarBorrado(id) {
    Swal.fire({
        title: '¿Está seguro de eliminar?',
        text: "Esta acción removerá el repuesto ID #" + id + " de forma permanente del inventario.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'El registro ha sido removido del sistema.',
                icon: 'success',
                confirmButtonColor: '#0284c7'
            });
        }
    });
}