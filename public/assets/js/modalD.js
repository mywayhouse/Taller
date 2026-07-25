document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-confirmar-accion');
    if (!btn) return;

    e.preventDefault();

    const modal = document.getElementById('modal-confirmar-accion');
    if (!modal) {
        console.error('El HTML del modal "#modal-confirmar-accion" no existe en la vista.');
        return;
    }

    const txtTitulo = document.getElementById('modal-confirm-titulo');
    const txtMensaje = document.getElementById('modal-confirm-mensaje');
    const txtDetalle = document.getElementById('modal-confirm-detalle');
    const wrapperDetalle = txtDetalle?.closest('.modal-detalle-wrapper');
    const btnAceptar = document.getElementById('btn-aceptar-confirmacion');
    const btnCancelar = document.getElementById('btn-cancelar-confirmacion');

    const url = btn.getAttribute('href');
    const form = btn.closest('form');

    // Cargar textos dinámicos
    txtTitulo.textContent = btn.dataset.titulo || 'Confirmación';
    txtMensaje.textContent = btn.dataset.mensaje || '¿Desea realizar esta acción?';
    
    // Si hay detalle lo muestra; si no, oculta el contenedor
    if (btn.dataset.detalle) {
        txtDetalle.textContent = btn.dataset.detalle;
        if (wrapperDetalle) wrapperDetalle.style.display = 'inline-block';
    } else {
        txtDetalle.textContent = '';
        if (wrapperDetalle) wrapperDetalle.style.display = 'none';
    }

    btnAceptar.textContent = btn.dataset.btnTexto || 'Sí, continuar';

    // Función auxiliar para cerrar
    const cerrarModal = () => {
        modal.style.display = 'none';
    };

    // Mostrar modal
    modal.style.display = 'flex';

    // Asignar eventos de los botones
    btnAceptar.onclick = () => {
        cerrarModal();
        if (url && url !== '#') {
            window.location.href = url;
        } else if (form) {
            form.submit();
        }
    };

    btnCancelar.onclick = () => {
        cerrarModal();
    };

    modal.onclick = (event) => {
        if (event.target === modal) {
            cerrarModal();
        }
    };
});