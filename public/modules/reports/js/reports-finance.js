$(function() {
    loadingOnScreen();
    exportReports();
    
    barGraph();
    updateReports();    
    
    changeCompany();
    changeCalendar();
    
    let info = JSON.parse(sessionStorage.getItem('info'));
    $('input[name=daterange]').val(info.calendar);
    
    
    
    
});

let resumeUrl = '/api/reports/resume';

function loadInfo() {
    let info = JSON.parse(sessionStorage.getItem('info'));
    $('input[name=daterange]').val(info.calendar);
    $('#select_projects').val(info.company);
}



function onCommission() {
    let data, infoComission = '';   
    $('.onPreLoad *').remove();
    $("#finance-commission, #info-commission").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: resumeUrl + "/commissions?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            data = `
                <span class="title">Comissão total</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${response.data.total}</strong>
                </div>
            `;
            infoComission = `
                <section class="container">
                    <header class="d-flex title-graph">
                        <h5 class="grey font-size-16">
                            <strong>Comissão</strong>
                        </h5>
                    </header>

                    <div class="d-flex justify-content-between box-finances-values">
                        <div class="finances-values">
                            <span>R$</span>
                            <strong>${response.data.total}</strong>
                        </div>
                        <div class="finances-values">
                            <svg class="${response.data.variation.color}" width="18" height="14" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.1237 0L16.9451 0.00216293L17.1065 0.023901L17.2763 0.0736642L17.4287 0.145306L17.4865 0.18052L17.5596 0.23218L17.6737 0.332676L17.8001 0.484464L17.8876 0.634047L17.9499 0.792176L17.9845 0.938213L18 1.125V7.88084C18 8.50216 17.4964 9.00583 16.8751 9.00583C16.3057 9.00583 15.835 8.58261 15.7606 8.03349L15.7503 7.88084L15.7495 3.8415L9.41947 10.1762C9.01995 10.5759 8.39457 10.6121 7.95414 10.2849L7.82797 10.1758L5.62211 7.96668L1.92041 11.6703C1.48121 12.1098 0.768994 12.1099 0.329622 11.6707C-0.069807 11.2713 -0.106236 10.6463 0.220416 10.2059L0.329304 10.0797L4.82693 5.57966C5.22645 5.17994 5.85182 5.14374 6.29225 5.47097L6.41841 5.58004L8.62427 7.78914L14.1597 2.25H10.1237C9.55424 2.25 9.08361 1.82677 9.00912 1.27766L8.99885 1.125C8.99885 0.50368 9.50247 0 10.1237 0Z" fill="#1BE4A8"/>
                            </svg>
                            <em class="${response.data.variation.color}">${response.data.variation.value}</em>
                        </div>
                    </div>
                </section>
                <section class="container">
                    <div class="graph-reports">
                        <div class="${response.data.total !== '0,00' ?'new-finance-graph' : ''  }"></div>
                    </div>
                </section>
            `;           
           

            $("#finance-commission, #info-commission").find('.ske-load').remove();
            $("#info-commission").html(infoComission);
            $("#finance-commission").html(data);

            if( response.data.total !== '0,00' ) {
                $('.new-finance-graph').html('<canvas id=comission-graph></canvas>');
                let labels = [...response.data.chart.labels];
                let series = [...response.data.chart.values];
                graphComission(series, labels);
            }

        }
    });    
    
}

function getPending() {
    let pendingBlock = '';
    $('#card-pending .onPreLoad *' ).remove();
    $("#block-pending").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: resumeUrl+ "/pendings?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
            
        },
        success: function success(response, status) {
            $(".value-pending").text(response.data.total);

            pendingBlock = `
            <footer class="">
                <div class="d-flex">
                    <div class="balance col-3">
                        <h6 class="grey font-size-14">Total</h6>
                        <strong class="grey total">1.2K</strong>
                    </div>
                    <div class="balance col-9">
                        <h6 class="font-size-14">Saldo</h6>
                        <small>R$</small>
                        <strong class="total orange">${response.data.total}</strong>
                    </div>
                </div>
            </footer>
            `;
            $("#block-pending").html(pendingBlock).removeClass('mini-block');
        }
    });
}

function changeCalendar() {
    $('.onPreLoad *').remove();
    
    $('input[name="daterange"]').daterangepicker(
        {
            startDate: moment().subtract(30, "days"),
            endDate: moment(),
            opens: "left",
            maxDate: moment().endOf("day"),
            alwaysShowCalendar: true,
            showCustomRangeLabel: "Customizado",
            autoUpdateInput: true,
            locale: {
                locale: "pt-br",
                format: "DD/MM/YYYY",
                applyLabel: "Aplicar",
                cancelLabel: "Limpar",
                fromLabel: "De",
                toLabel: "Até",
                customRangeLabel: "Customizado",
                weekLabel: "W",
                daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
                monthNames: [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro",
                ],
                firstDay: 0,
            },
            ranges: {
                Hoje: [moment(), moment()],
                Ontem: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este mês": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Mês passado": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
        },
        function (start, end) {
            startDate = start.format("YYYY-MM-DD");
            endDate = end.format("YYYY-MM-DD");
            
            
            $('.onPreLoad *').remove();
            $('.onPreLoad').append(skeLoad);
            
            updateReports();
        }
    );
    
    $('input[name="daterange"]').change(function() {
        $("#block-pending").addClass('mini-block');
        updateStorage({calendar: $(this).val()})
    })
    
}

function changeCompany() {
    $("#select_projects").on("change", function () {
        
        $('.onPreLoad *').remove();
        $('.onPreLoad').append(skeLoad);
        
        $("#block-pending").addClass('mini-block');
        updateStorage({company: $(this).val()})
        updateReports();
    });
}


function updateReports() {
    $('.onPreLoad *').remove();
    $('.onPreLoad').append(skeLoad);

    $.ajax({
        method: "GET",
        url: "/api/projects?select=true",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            loadingOnScreenRemove();
            $("#modal-content").hide();
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (!isEmpty(response.data)) {

                $("#project-empty").hide();
                $("#project-not-empty").show();
                $("#export-excel").show();

                $.each(response.data, function (i, project) {
                    $("#select_projects").append(
                        $("<option>", {
                            value: project.id,
                            text: project.name,
                        })
                    );
                });
                $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
            } else {
                $("#export-excel").hide();
                $("#project-not-empty").hide();
                $("#project-empty").show();
            }

            loadingOnScreenRemove();
        },
    });

    var date_range = $("#date_range_requests").val();
    var startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    var endDate = moment().format("YYYY-MM-DD");
    
    $.ajax({
        url: "/api/reports",
        type: "GET",
        data: {
            project: $("#select_projects").val(),
            endDate: endDate,
            startDate: startDate,
        },
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $('.onPreLoad *').remove();
            onCommission();
            getPending();
        },
    });
}


function updateStorage(value){
    let prevData = JSON.parse(sessionStorage.getItem('info'));
    Object.keys(value).forEach(function(val, key){
         prevData[val] = value[val];
    })
    sessionStorage.setItem('info', JSON.stringify(prevData));
}

function graphComission(series, labels) {
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
    const ctx = document.getElementById('comission-graph').getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 450);
    gradient.addColorStop(0, 'rgba(54,216,119, 0.23)');
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
    
    const myChart = new Chart(ctx, {
        plugins: [legendMargin],
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Legenda',
                    data: series,
                    color:'#636363',
                    backgroundColor: gradient,
                    borderColor: "#1BE4A8",
                    borderWidth: 4,
                    fill: true,
                    borderRadius: 4,
                    barThickness: 30,
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {display: false},
                title: {display: false},
            },
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Muli',
                            size: 12,
                        },
                        color: "#A2A3A5",
                    }
                },
                y: {
                    grid: {
                        color: '#ECE9F1',
                        drawBorder: false
                    },
                    
                    ticks: {
                        padding: 15,
                        font: {
                            family: 'Muli',
                            size: 12,
                        },
                        color: "#A2A3A5",
                        callback: function(value){
                            return (value / 100000) + 'K '
                        }
                    }
                    
                },
            },
            pointBackgroundColor:"#1BE4A8",
            radius: 3,
            interaction: {
              intersect: false,
              mode: "index",
              borderRadius: 4,
              usePointStyle: true,
              yAlign: 'bottom',
              padding: 10,
              titleSpacing: 10,
              callbacks: {
                  label: function (tooltipItem) {
                      return convertToReal(tooltipItem);
                  },
                  labelPointStyle: function (context) {
                      return {
                          pointStyle: 'rect',
                          borderRadius: 4,
                          rotatio: 0,
                      }
                  }
              }
            }
          },
    });
}

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

 function convertToReal(tooltipItem) {
    let tooltipValue = tooltipItem.raw;
        tooltipValue = tooltipValue + '';
        tooltipValue = parseInt(tooltipValue.replace(/[\D]+/g, ''));
        tooltipValue = tooltipValue + '';
        tooltipValue = tooltipValue.replace(/([0-9]{2})$/g, ",$1");

        if (tooltipValue.length > 6) {
            tooltipValue = tooltipValue.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        }
        
        return 'R$ ' + tooltipValue;
}

function exportReports() {
    // show/hide modal de exportar relatórios
    $(".lk-export").on('click', function(e) {
        e.preventDefault();
        $('.inner-reports').addClass('focus');
        $('.line-reports').addClass('d-flex');
    });

    $('.reports-remove').on('click', function (e) {
        e.preventDefault();
        $('.inner-reports').removeClass('focus');
        $('.line-reports').removeClass('d-flex');
    });

}

let skeLoad = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text"></div>
        </div>
    </div>
`;