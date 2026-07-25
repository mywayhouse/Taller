document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-usuario');
    const form = document.getElementById('form-usuario');
    const modalTitulo = document.getElementById('modal-titulo');
    const btnGuardar = document.getElementById('btn-guardar');
    const labelContrasenia = document.getElementById('label-contrasenia');
    const inputContrasenia = document.getElementById('contrasenia');
    
    const btnNuevo = document.getElementById('btn-nuevo-usuario');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar-modal');
    const botonesEditar = document.querySelectorAll('.btn-editar-usuario');

    // Asume la presencia global de la constante APP_URL o la recupera según tu estructura
    const appUrl = (typeof APP_URL !== 'undefined') ? APP_URL : window.location.origin;

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

    // Crear Usuario
    if (btnNuevo) {
        btnNuevo.addEventListener('click', () => {
            form.reset();
            form.action = `${appUrl}/usuarios/guardar`;
            modalTitulo.textContent = 'Nuevo Usuario';
            btnGuardar.textContent = 'Crear Usuario';
            
            // Requerir contraseña para la creación
            labelContrasenia.textContent = 'Contraseña *';
            inputContrasenia.required = true;

            abrirModal();
        });
    }

    // Editar Usuario
    botonesEditar.forEach(btn => {
        btn.addEventListener('click', () => {
            form.reset();
            const id = btn.dataset.id;
            
            document.getElementById('nombre').value = btn.dataset.nombre || '';
            document.getElementById('correo').value = btn.dataset.correo || '';
            document.getElementById('rol').value = btn.dataset.rol || '';
            
            // La contraseña no es obligatoria al editar
            inputContrasenia.value = '';
            inputContrasenia.required = false;
            labelContrasenia.textContent = 'Nueva contraseña (dejar vacío para mantener)';

            form.action = `${appUrl}/usuarios/actualizar/${id}`;
            modalTitulo.textContent = 'Editar Usuario';
            btnGuardar.textContent = 'Actualizar Usuario';
            
            abrirModal();
        });
    });

    if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
    if (btnCancelar) btnCancelar.addEventListener('click', cerrarModal);
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) cerrarModal();
    });
});