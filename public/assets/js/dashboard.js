//REPUESTOS MAS VENDIDOS
document.addEventListener("DOMContentLoaded", function() {
    const canvasElement = document.getElementById('graficoRepuestos');
    if (canvasElement) {
        const labels = ['Pastillas de freno', 'Filtro de aceite', 'Bujías', 'Amortiguadores', 'Batería']; //AQUI AGREGA EL ARREGLO DE LOS REPUESTOS MAS VENDIDOS
        const data = {
            labels: labels,
            datasets: [
                {
                    label: 'Cantidad Vendida',
                    data: [85, 72, 60, 45, 30], // AQUI AGREGA EL ARREGLO DE DATOS DE LA CANTIDAD DEL REPUESTO TOTAL
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                }
            ]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                indexAxis: 'y',
                elements: {
                    bar: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Repuestos más vendidos'
                    }
                }
            },
        };

        new Chart(canvasElement, config);
    }
});



//INGRESOS VS MECANICO
document.addEventListener("DOMContentLoaded", function() {
    const canvasMecanicos = document.getElementById('graficoIngresosMecanico');
    
    if (canvasMecanicos) {
        // 1. Las etiquetas son los días de la semana (eje temporal)
        const labelsSemanales = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        const data = {
            labels: labelsSemanales,
            datasets: [
                {
                    label: 'Ingresos Totales por Pedido',
                    // Datos de ejemplo por día de la semana (vienen de tu BD)
                    data: [450, 600, 350, 800, 950, 1200, 300], 
                    borderColor: 'rgb(54, 162, 235)', // Azul
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    pointStyle: 'circle',
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    tension: 0.1 // Opcional: suaviza un poco las líneas
                },
                {
                    label: 'Ingresos por Mecánico / Mano de Obra',
                    // Datos de la parte correspondiente a los mecánicos por día
                    data: [150, 200, 120, 300, 350, 450, 100], 
                    borderColor: 'rgb(255, 99, 132)', // Rojo
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    pointStyle: 'circle',
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    tension: 0.1
                }
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Comparativa Semanal: Ingresos de Pedidos vs Ingresos por Mecánico'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        // Si manejas moneda, puedes agregar un formato de símbolo si lo deseas
                    }
                }
            }
        };

        new Chart(canvasMecanicos, config);
    }
});