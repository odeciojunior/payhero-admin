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

    const ctx = document.getElementById('financesChart').getContext('2d');
        const myChart = new Chart(ctx, {
            plugins: [legendMargin],
            type: 'bar',
            data: {
                labels: ['SET', 'AGO', 'JUL', 'JUN', 'MAY', 'ABR'],
                datasets: [
                    {
                        label: 'Receitas',
                        data: [35000,81650,30],
                        color:'#636363',
                        backgroundColor: "rgba(69, 208, 126, 1)",
                        borderRadius: 4,
                        barThickness: 30,
                    }, 
                    {
                        label: 'Saques',
                        data: [50000,76650, 1150],
                        color:'#636363',
                        backgroundColor: "rgba(216, 245, 228, 1)",
                        borderRadius: 4,
                        barThickness: 30,
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        align: 'center',
                        labels: {
                            boxWidth: 16,
                            color: '#636363',
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            font: {
                                size: '12',
                                family: "'Muli'"
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Últimos 6 meses',
                        align: 'end',
                        color: '#9E9E9E',
                        fullSize: false,
                        padding: {
                            top: 0,
                            bottom: -23,
                        }
                    },
                },
                
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 15,
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
                            drawBorder: false
                        },
                        beginAtZero: true,
                        min: 0,
                        max: 100000,
                        ticks: {
                            padding: 0,
              	            stepSize: 20000,
                            font: {
                                family: 'Muli',
                                size: 12,
                            },
                            color: "#747474",
                            callback: function(value){
                                return (value / 1000) + 'K '
                            }
                        }
                    }
                },
                interaction: {
                    mode: "index",
                    // axis: 'y',
                    borderRadius: 4,
                    usePointStyle: true,
                    yAlign: 'bottom',
                    padding: 15,
                    titleSpacing: 10,
                    callbacks: {
                        title: titleTooltip,
                        label: function (tooltipItem) {
                            return Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(tooltipItem.raw);
                            //return tooltipItem.raw.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
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
