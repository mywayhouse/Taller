
function abrirModalCliente(id_cliente = null) {
    const titulo = id_cliente ? 'Editar Cliente' : 'Nuevo Cliente';
    
    abrirModalGlobal(titulo);
    document.getElementById('globalModalBody').innerHTML = '<p class="text-center">Cargando formulario...</p>';

    // Aquí usamos la variable global APP_URL que definimos en el main.php
    let url = window.APP_URL + '/clientes/form'; 
    if (id_cliente) {
        url += '/' + id_cliente;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('globalModalBody').innerHTML = html;
        })
        .catch(error => {
            console.error('Error al cargar el formulario:', error);
            document.getElementById('globalModalBody').innerHTML = '<p class="text-error">Error al cargar el formulario.</p>';
        });
}