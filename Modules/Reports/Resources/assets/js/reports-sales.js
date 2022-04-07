$(function() {
    barGraph();
});

function barGraph() {
    const titleTooltip = (tooltipItems) => {
        return '';
    }   

    const legendMargin = {
        id: 'legendMargin',
        beforeInit(chart, legend, options) {
            const fitValue = chart.legend.fit;
            chart.legend.fit = function () {  
                fitValue.bind(chart.legend)();
                return this.height += 20;
            }
        }
    };

    const ctx = document.getElementById('salesChart').getContext('2d');
        const myChart = new Chart(ctx, {
            plugins: [legendMargin],
            type: 'bar',
            data: {
                labels: ['','','','', '', ''],
                datasets: [
                    {
                        axis: 'x',
                        label: '',
                        data: [12, 15,20, 25,30,40],
                        color:'#2E85EC',
                        backgroundColor: ['rgba(46, 133, 236, 1)'],
                        borderRadius: 4,
                        barThickness: 24,
                        fill: false
                    },
                ]
            },
            options: {
                indexAxis: 'x',
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    },
                    subtitle: {
                        display: true,
                        align: 'start',
                        text: '32 clientes recorrentes',
                        color: '#2E85EC',
                        font: {
                            size: '14',
                            family: "'Muli'",
                            weight: 'normal'
                        },
                        padding: {
                            top: 0,
                            bottom: 15
                        }
                    }
                },
                
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 0,
                            color: "#747474",
                            align: 'center',
                            font: {
                                family: 'Muli',
                                size: 12,
                            },
                        },
                    },
                    y: {
                        grid: {
                            color: '#ECE9F1',
                            drawBorder: true
                        },
                        min: 10,
                        max: 40,
                        ticks: {
                            padding: 0,
              	            stepSize: 10,
                            font: {
                                family: 'Muli',
                                size: 14,
                            },
                            color: "#A2A3A5"
                        }
                    }
                },
                interaction: {
                    mode: "index",
                    borderRadius: 4,
                    usePointStyle: true,
                    yAlign: 'bottom',
                    padding: 15,
                    titleSpacing: 10,
                    callbacks: {
                        title: titleTooltip,
                        label: function (tooltipItem) {
                            return Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(tooltipItem.raw);
                        },
                        labelPointStyle: function (context) {
                            return {
                                pointStyle: 'rect',
                                borderRadius: 4,
                                rotatio: 0,
                            }
                        }
                    }
                },
            }
        });

}