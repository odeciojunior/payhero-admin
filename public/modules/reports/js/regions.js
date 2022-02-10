$(function() {
    barGraph();
});

function barGraph() {
    
    const ctx = document.getElementById('regionsChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['SP', 'MG', 'RS', 'PR'],
                datasets: [
                    {
                        label: '',
                        data: [60,42,48,35],
                        color:'#636363',
                        backgroundColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                        ],
                        borderRadius: 4,
                        barThickness: 30,
                    }, 
                    {
                        label: '',
                        data: [60,42,48,35],
                        color:'#636363',
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                        ],
                        borderRadius: 4,
                        barThickness: 30,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    },
                },
                
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {
                            color: '#ECE9F1',
                            drawBorder: false,
                            display: false
                        },
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            padding: 0,
              	            stepSize: 100,
                            font: {
                                family: 'Muli',
                                size: 12,
                            },
                            color: "#747474",
                            callback: function(value){
                                return value + '% '
                            }
                        }
                    }
                },
            }
        });

}
