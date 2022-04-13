$(function() {
    loadingOnScreen();
    exportReports();
    barGraph();

    updateReports();

    changeCompany();
    changeCalendar();
    
    if(sessionStorage.info) {
        let info = JSON.parse(sessionStorage.getItem('info'));
        $('input[name=daterange]').val(info.calendar); 
    }
});

let salesResumeUrl = '/api/reports/sales';

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

function salesResume() {
    let salesTransactions, salesAverageTicket, salesComission, salesNumberChargeback = '';
    
    var project_id = $("#select_projects option:selected").val();
    var date_range = $("input[name='daterange']").val();    

    $('#reports-content .onPreLoad *').remove();
    $("#sales-transactions,#sales-average-ticket,#sales-comission,#sales-number-chargeback").html(skeLoad);

    $.ajax({
        method: "GET",
        url: salesResumeUrl + "/resume?project_id=" + project_id + "&date_range=" + date_range,
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { average_ticket, chargeback, comission, transactions } = response.data;

            salesTransactions = `
                <span class="title">N de transações</span>
                <div class="d-flex">
                    <strong class="number">${transactions == undefined ? 0: transactions}</strong>
                </div>
            `;
            
            salesAverageTicket = `
                <span class="title">Ticket Médio</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${average_ticket == undefined ? '0,00': removeMoneyCurrency(average_ticket)}</strong>
                </div>
            `;

            salesComission = `
                <span class="title">Comissão total</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${comission == undefined ? '0,00': removeMoneyCurrency(comission)}</strong>
                </div>
            `;

            salesNumberChargeback = `
                <span class="title">Total em Chargebacks</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${chargeback == undefined ? '0,00': removeMoneyCurrency(chargeback)}</strong>
                </div>
            `;            

            $("#sales-number-chargeback").html(salesNumberChargeback);
            $("#sales-comission").html(salesComission);
            $("#sales-average-ticket").html(salesAverageTicket);
            $("#sales-transactions").html(salesTransactions);
        }
    });
}

function distribution() {
    let distributionHtml = '';   
    $('#card-distribution .onPreLoad *').remove();
    $("#block-distribution").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: salesResumeUrl + "/distribuitions?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if(response.data.exception !== null) {
                let { approved, canceled, chargeback, other, pending, refunded, refused, total } = response.data;
                let series = [
                    approved.percentage,
                    pending.percentage,
                    canceled.percentage, 
                    refused.percentage,
                    refunded.percentage,
                    chargeback.percentage,
                    other.percentage,
                ];
                
                distributionHtml = `     
                <div class="d-flex box-graph-dist">
                    <div class="info-graph">
                        <h6 class="font-size-14 grey">Saldo Total</h6>
                        <em>
                            <small class="font-size-14">R$</small>
                            <strong class="grey">${removeMoneyCurrency(total)}</strong>
                        </em>
                    </div>
                </div>
                <div class="d-flex box-distribution secondary">
                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#1BE4A8" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Aprovadas</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${approved.value}</strong>
                        </div>
                        <div class="item right"><small class="grey font-size-14">${approved.percentage}%</small></div>
                    </div>

                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FFBA06" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Pendentes</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${pending.value}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${pending.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#665FE8" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Canceladas</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${canceled.value}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${canceled.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FF2F2F" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Recusadas</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${refused.value}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${refused.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#00C2FF" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Reembolsos</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${refunded.value}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${refunded.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#D10000" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Chargebacks</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${chargeback.value}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${chargeback.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#767676" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Outros</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${other.value}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${other.percentage}%</small>
                        </div>
                    </div>
                </div>
                `;
                    
                $("#block-distribution").html(distributionHtml);
                $(".box-graph-dist").prepend('<div class="distribution-graph-seller"></div>');
                distributionGraph(series);
            }
             
            
        }
    });
    
}

function distributionGraph(series) {
    new Chartist.Pie('.distribution-graph-seller', {
        series
      }, {
        donut: true,
        donutWidth: 30,
        donutSolid: true,
        startAngle: 270,
        showLabel: false,
        chartPadding: 0,
        labelOffset: 0,
        height: 123
      });
}

function changeCalendar() {
    $('.onPreLoad *').remove();
    
    var startDate = moment().subtract(30, "days").format("DD/MM/YYYY");
    var endDate = moment().format("DD/MM/YYYY");

    $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
    $('input[name="daterange"]').dateRangePicker({
        setValue: function (s) {
            if (s) {
                let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                $(this).html(s).data('value', normalize);
                $('input[name="daterange"]').attr('value', normalize);
                $('input[name="daterange"]').val(normalize);
            } else {
                $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
                $('input[name="daterange"]').val(`${startDate}-${endDate}`);
            }
        }
    })
    .on('datepicker-change', function () {
        updateReports();
    })
    .on('datepicker-open', function () {
        $('.filter-badge-input').removeClass('show');
    })
    .on('datepicker-close', function () {
        $(this).removeClass('focused');
        if ($(this).data('value')) {
            $(this).addClass('active');
        }
    });

    $('input[name="daterange"]').change(function() {
        updateStorage({calendar: $(this).val()})
    })
    
}

function changeCompany() {
    $("#select_projects").on("change", function () {
        $('.onPreLoad *').remove();
        $('.onPreLoad').html(skeLoad);
        updateStorage({company: $(this).val()});
        updateReports();
    });
}

function updateReports() {
    $('.onPreLoad *').remove();
    $('.onPreLoad').html(skeLoad);

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
                if(sessionStorage.info) {
                    $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
                }                
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
            salesResume();
            distribution();
        },
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

function updateStorage(value){
    let prevData;
    if(sessionStorage.info) JSON.parse(sessionStorage.getItem('info'));

    Object.keys(value).forEach(function(val, key){
         prevData[val] = value[val];
    })
    sessionStorage.setItem('info', JSON.stringify(prevData));
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