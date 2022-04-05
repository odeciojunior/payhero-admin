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

// function loadInfo() {
//     let info = JSON.parse(sessionStorage.getItem('info'));
//     $('input[name=daterange]').val(info.calendar);
//     $('#select_projects').val(info.company);
// }



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
            $("#block-pending").html(pendingBlock)
        }
    });
}

function getCashback() {
    let cashBlock = '';
    $('#card-cashback .onPreLoad *' ).remove();
    $("#block-cash").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: resumeUrl + "/cashbacks?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response, status) {
            if(response.data.total !== '0,00') {
                cashBlock = `
                    <div class="balance col-6">
                        <h6 class="grey font-size-14">
                            <span class="ico-coin">
                                <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                </svg>
                            </span>
                            Recebido
                        </h6>
                        <small>R$</small>
                        <strong class="total grey">${response.data.total}</strong>
                    </div>
                    <div class="balance col-6">
                        <h6 class="grey font-size-14 qtd">Quantidade</h6>
                        <strong class="total grey">240 vendas</strong>
                    </div>
                `;
                
            } else {
                cashBlock = `                
                    <div class="balance col-4">
                        <div class="box-ico-cash">
                            <span class="ico-cash">
                                <svg width="47" height="47" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="balance col-8">
                        <h6 class="no-cashback">Ainda sem cashback :(</h6>
                        <p class="txt-no-cashback">Suba de nível e mantenha a saúde da conta boa para receber cashback</p>
                    </div>
                `;
            }

            $("#block-cash").html(cashBlock);
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
        updateStorage({calendar: $(this).val()})
    })
    
}

function changeCompany() {
    $("#select_projects").on("change", function () {
        
        $('.onPreLoad *').remove();
        $('.onPreLoad').append(skeLoad);
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
            getCashback();
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