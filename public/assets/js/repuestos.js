document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-repuesto');
    const form = document.getElementById('form-repuesto');
    const modalTitulo = document.getElementById('modal-titulo');
    const btnGuardar = document.getElementById('btn-guardar');
    
    const btnNuevo = document.getElementById('btn-nuevo-repuesto');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar-modal');
    const botonesEditar = document.querySelectorAll('.btn-editar-repuesto');

    const appUrl = '<?= APP_URL ?>';

    // Abrir Modal
    const abrirModal = () => {
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    };

    // Cerrar Modal
    const cerrarModal = () => {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    };

    if (btnNuevo) {
        btnNuevo.addEventListener('click', () => {
            form.reset();
            form.action = `${appUrl}/repuestos/guardar`;
            modalTitulo.textContent = 'Nuevo Repuesto';
            btnGuardar.textContent = 'Guardar Repuesto';
            abrirModal();
        });
    }

    botonesEditar.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            
            document.getElementById('nombre').value = btn.dataset.nombre;
            document.getElementById('stock_actual').value = btn.dataset.stockActual;
            document.getElementById('stock_minimo').value = btn.dataset.stockMinimo;
            document.getElementById('unidad_medida').value = btn.dataset.unidad;
            document.getElementById('precio_venta').value = btn.dataset.precio;

            form.action = `${appUrl}/repuestos/actualizar/${id}`;
            modalTitulo.textContent = 'Editar Repuesto';
            btnGuardar.textContent = 'Actualizar Repuesto';
            
            abrirModal();
        });
    });

    if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
    if (btnCancelar) btnCancelar.addEventListener('click', cerrarModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) cerrarModal();
    });
});