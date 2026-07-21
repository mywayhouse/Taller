
//vehiculos MÁS VENDIDOS
document.addEventListener("DOMContentLoaded", function() {
    const canvasElement = document.getElementById('graficoModelosVehiculos');
    if (canvasElement) {
        const rawLabels = window.chartVehiculosLabels;
        const rawData = window.chartVehiculosData;

        const labels = (rawLabels && rawLabels.length > 0) ? rawLabels : ['Sin datos'];
        const datosValores = (rawData && rawData.length > 0) ? rawData : [0];

        const data = {
            labels: labels,
            datasets: [{
                label: 'Visitas al Taller',
                data: datosValores,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
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
                    legend: { display: false },
                    title: { display: true, text: 'Modelos de Vehículos más Frecuentes' }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        };

        new Chart(canvasElement, config);
    }
});

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
                    legend: { display: false},
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

//MECANICOS POR MES (GRÁFICO DE LÍNEAS DINÁMICO)
window.addEventListener('DOMContentLoaded', () => {
    const rawData = window.ordenesMecanicosRaw || [];

    // Meses base en español
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'];
    
    const traduccionMeses = {
        'January': 'Enero', 'February': 'Febrero', 'March': 'Marzo',
        'April': 'Abril', 'May': 'Mayo', 'June': 'Junio',
        'July': 'Julio', 'August': 'Agosto', 'September': 'Septiembre',
        'October': 'Octubre', 'November': 'Noviembre', 'December': 'Diciembre'
    };

    // Extraer mecánicos únicos
    const mecanicos = [...new Set(rawData.map(item => item.mecanico))];

    //lineas
    const colores = [
        'rgb(54, 162, 235)', 'rgb(255, 99, 132)', 'rgb(75, 192, 192)',
        'rgb(255, 206, 86)', 'rgb(153, 102, 255)', 'rgb(255, 159, 64)',
        'rgb(46, 204, 113)', 'rgb(231, 76, 60)', 'rgb(52, 152, 219)', 'rgb(155, 89, 182)'
    ];

    //datasets
    const datasets = mecanicos.map((mecanico, index) => {
        const dataPorMes = meses.map(mesEspanol => {
            const encontrado = rawData.find(item => {
                const mesBD = traduccionMeses[item.mes_nombre] || item.mes_nombre;
                return item.mecanico === mecanico && mesBD === mesEspanol;
            });
            return encontrado ? parseInt(encontrado.total_ordenes) : 0;
        });

        const colorLinea = colores[index % colores.length];

        return {
            label: mecanico,
            data: dataPorMes,
            borderColor: colorLinea,
            backgroundColor: colorLinea.replace('rgb', 'rgba').replace(')', ', 0.5)'),
            borderWidth: 3,
            tension: 0.3,
            fill: false
        };
    });

    const ctxMecanicos = document.getElementById('graficoOrdenesMecanicoMes');
    if (ctxMecanicos) {
        new Chart(ctxMecanicos, {
            type: 'line',
            data: {
                labels: meses,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11 } } 
                    },
                    title: { 
                        display: true, 
                        text: 'Tendencia de Órdenes por Mecánico y por Mes' 
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true 
                    }
                }
            }
        });
    }
});