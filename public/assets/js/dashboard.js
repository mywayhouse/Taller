
//REPUESTOS MÁS VENDIDOS
document.addEventListener("DOMContentLoaded", function() {
    const canvasElement = document.getElementById('graficoRepuestos');
    if (canvasElement) {
        const rawLabels = window.chartRepuestosLabels;
        const rawData = window.chartRepuestosData;

        const labels = (rawLabels && rawLabels.length > 0) ? rawLabels : ['Sin datos'];
        const datosValores = (rawData && rawData.length > 0) ? rawData : [0];

        const data = {
            labels: labels,
            datasets: [{
                label: 'Cantidad Vendida',
                data: datosValores,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderWidth: 2
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    title: { display: true, text: 'Repuestos más vendidos' }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        };

        new Chart(canvasElement, config);
    }
});



//INGRESOS SEMANALES
document.addEventListener("DOMContentLoaded", function() {
    const canvasMecanicos = document.getElementById('graficoIngresosMecanico');
    
    if (canvasMecanicos) {
        const diasEspanol = {
            'Monday': 'Lunes',
            'Tuesday': 'Martes',
            'Wednesday': 'Miércoles',
            'Thursday': 'Jueves',
            'Friday': 'Viernes',
            'Saturday': 'Sábado',
            'Sunday': 'Domingo'
        };

        const diasOrdenados = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        
        let datosTotalesFinales = [0, 0, 0, 0, 0, 0, 0];
        let datosManoObraFinales = [0, 0, 0, 0, 0, 0, 0];

        const registrosDB = window.ingresosSemanalesRaw || [];

        registrosDB.forEach(item => {
            let diaEsp = diasEspanol[item.dia_semana] || item.dia_semana;
            let index = diasOrdenados.indexOf(diaEsp);
            if (index !== -1) {
                datosTotalesFinales[index] = parseFloat(item.ingresos_totales);
                datosManoObraFinales[index] = parseFloat(item.ingresos_mano_obra);
            }
        });

        const data = {
            labels: diasOrdenados,
            datasets: [
                {
                    label: 'Ingresos Totales por Pedido',
                    data: datosTotalesFinales, 
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    pointRadius: 6,
                    tension: 0.1
                },
                {
                    label: 'Ingresos por Mecánico / Mano de Obra',
                    data: datosManoObraFinales, 
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    pointRadius: 6,
                    tension: 0.1
                }
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Comparativa Semanal: Ingresos de Pedidos vs Ingresos por Mecánico'
                    },
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        };

        new Chart(canvasMecanicos, config);
    }
});