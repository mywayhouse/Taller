'use strict';

function abrirModalOrden(id_orden = null) {
    const titulo = id_orden ? 'Editar Orden de Servicio' : 'Nueva Orden de Servicio';
    
    abrirModalGlobal(titulo);
    document.getElementById('globalModalBody').innerHTML = '<p class="text-center">Cargando formulario...</p>';

    let url = window.APP_URL + '/ordenes/form'; 
    if (id_orden) {
        url += '/' + id_orden;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('globalModalBody').innerHTML = html;
        })
        .catch(error => {
            console.error('Error al cargar el formulario de órdenes:', error);
            document.getElementById('globalModalBody').innerHTML = '<p class="text-error">Error al cargar el formulario.</p>';
        });
}