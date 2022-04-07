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
                        data: [60,22,48,35],
                        color:'#636363',
                        backgroundColor: [
                            'rgba(46, 133, 236, 1)',
                            'rgba(102, 95, 232, 1)',
                            'rgba(244, 63, 94, 1)',
                            'rgba(255, 121, 0, 1)',
                        ],
                        borderRadius: 4,
                        barThickness: 30,
                    }, 
                    {
                        label: '',
                        data: [100,42,58,45],
                        color:'#636363',
                        backgroundColor: [
                            'rgba(46, 133, 236, .2)',
                            'rgba(102, 95, 232, .2)',
                            'rgba(244, 63, 94, .2)',
                            'rgba(255, 121, 0, .2)',
                        ],
                        borderRadius: 4,
                        barThickness: 30,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {display: false},
                    title: {display: false},
                },
                
                responsive: false,
                scales: {
                    x: {
                        display: false,
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
                            color: "#636363",
                            callback: function(value, index){
                                return this.getLabelForValue(value);
                            }
                        }
                    }
                },
            }
        });

}