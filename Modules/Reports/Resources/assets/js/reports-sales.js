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

let salesUrl = '/api/reports/sales';
let mktUrl = '/api/reports/marketing';

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
        url: salesUrl + "/resume?project_id=" + project_id + "&date_range=" + date_range,
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
        url: salesUrl + "/distribuitions?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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
            else {
                distributionHtml = `
                <div class="d-flex box-graph-dist">
                    <div class="info-graph">
                        <h6 class="font-size-14 grey">Saldo Total</h6>
                        <em>
                            <small class="font-size-14">R$</small>
                            <strong class="grey">0,00</strong>
                        </em>
                    </div>
                </div>
                `;
                $("#block-distribution").html(distributionHtml);
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

function loadDevices() {
    let deviceBlock = '';
    $('#card-devices .onPreLoad *' ).remove();
    $("#block-devices").html(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/devices?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let {
                total, 
                percentage_desktop, 
                percentage_mobile, 
                count_desktop, 
                count_mobile,
                value_desktop,
                value_mobile
            } = response.data;
            
            deviceBlock = `
                 <div class="row container-payment">
                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#636363"/></svg>
                                    </div>Smartphones
                                </div>

                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span>379</span>
                                            /<small>436</small>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-16">${percentage_mobile}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 2.83333C0 1.26853 1.26853 0 2.83333 0H14.1667C15.7315 0 17 1.26853 17 2.83333V11.3333C17 12.8981 15.7315 14.1667 14.1667 14.1667H11.3333V14.875C11.3333 15.2662 11.6505 15.5833 12.0417 15.5833H12.75C13.1412 15.5833 13.4583 15.9005 13.4583 16.2917C13.4583 16.6829 13.1412 17 12.75 17H4.25C3.8588 17 3.54167 16.6829 3.54167 16.2917C3.54167 15.9005 3.8588 15.5833 4.25 15.5833H4.95833C5.34953 15.5833 5.66667 15.2662 5.66667 14.875V14.1667H2.83333C1.26853 14.1667 0 12.8981 0 11.3333V2.83333ZM10.0376 15.5833C9.95928 15.3618 9.91667 15.1234 9.91667 14.875V14.1667H7.08333V14.875C7.08333 15.1234 7.04072 15.3618 6.96242 15.5833H10.0376ZM14.1667 12.75C14.9491 12.75 15.5833 12.1157 15.5833 11.3333H1.41667C1.41667 12.1157 2.05093 12.75 2.83333 12.75H14.1667ZM15.5833 2.83333C15.5833 2.05093 14.9491 1.41667 14.1667 1.41667H2.83333C2.05093 1.41667 1.41667 2.05093 1.41667 2.83333V9.91667H15.5833V2.83333Z" fill="#636363"/></svg>
                                    </div> Desktop
                                </div>
                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span>105</span>
                                            /<small>211</small>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-16">${percentage_desktop}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                 </div>
            `;
            $("#block-devices").html(deviceBlock);
        }
    }); 
}

function typePayments() {
    let url = "/api/reports/resume/type-payments?date_range=" + $("input[name='daterange']").val();
    let paymentsHtml = '';

    $('#card-payments .onPreLoad *' ).remove();
    $("#block-payments").html(skeLoad);

    return $.ajax({
        method: "GET",
        url,
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { credit_card, pix, boleto, total } = response.data;

            paymentsHtml = `
                <div class="row container-payment">
                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"></path>
                                        </svg>
                                    </div>Cartão
                                </div>

                                <div class="box-payment-option option">
                                    <div class="col-payment grey" id='percent-credit-card'>
                                        ${credit_card.percentage}
                                    </div>
                                    <div class="col-payment col-graph">
                                        <div class="bar blue-1" style="width:${credit_card.percentage};">-</div>
                                    </div>
                                    <div class="col-payment">
                                        <span class="money-td green bold grey font-size-14" id='credit-card-value'>R$${credit_card.value}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="38.867"
                                            height="40.868"
                                            viewBox="0 0 38.867 40.868"
                                            style="width: 24px;"
                                        >
                                            <g id="Grupo_61" data-name="Grupo 61" transform="translate(-2948.5 213.743)">
                                                <g id="g992" transform="translate(2956.673 -190.882)">
                                                    <path id="path994" d="M-73.541-25.595a5.528,5.528,0,0,1-3.933-1.629l-5.68-5.68a1.079,1.079,0,0,0-1.492,0l-5.7,5.7a5.529,5.529,0,0,1-3.934,1.628H-95.4l7.193,7.194a5.753,5.753,0,0,0,8.136,0l7.214-7.214Z" transform="translate(95.4 34.202)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                </g>
                                                <g id="g996" transform="translate(2956.673 -212.243)">
                                                    <path id="path998" d="M-3.765-29.869A5.528,5.528,0,0,1,.169-28.24l5.7,5.7a1.056,1.056,0,0,0,1.493,0l5.68-5.68a5.529,5.529,0,0,1,3.934-1.629h.684l-7.214-7.214a5.753,5.753,0,0,0-8.136,0l-7.193,7.193Z" transform="translate(4.884 37.747)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                </g>
                                                <g id="g1000" transform="translate(2949 -201.753)">
                                                    <path id="path1002" d="M-121.731-14.725l-4.36-4.359a.83.83,0,0,1-.31.063h-1.982a3.917,3.917,0,0,0-2.752,1.14l-5.68,5.68a2.718,2.718,0,0,1-1.927.8,2.719,2.719,0,0,1-1.928-.8l-5.7-5.7a3.917,3.917,0,0,0-2.752-1.14h-2.437a.827.827,0,0,1-.293-.059l-4.377,4.377a5.753,5.753,0,0,0,0,8.136l4.377,4.377a.828.828,0,0,1,.293-.059h2.437a3.917,3.917,0,0,0,2.752-1.14l5.7-5.7a2.792,2.792,0,0,1,3.856,0l5.68,5.679a3.917,3.917,0,0,0,2.752,1.14h1.982a.83.83,0,0,1,.31.062l4.359-4.359a5.753,5.753,0,0,0,0-8.136" transform="translate(157.913 19.102)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                </g>
                                            </g>
                                        </svg>
                                    </div> Pix
                                </div>
                                <div class="box-payment-option option">
                                    <div class="col-payment grey" id='percent-values-pix'>
                                        ${pix.percentage}
                                    </div>
                                    <div class="col-payment col-graph">
                                        <div class="bar blue-2" style="width:${pix.percentage};">-</div>
                                    </div>
                                    <div class="col-payment">
                                        <span class="money-td green grey bold font-size-14" id='pix-value'>R$${pix.value}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <span class="">
                                            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_386_407)">
                                                    <rect x="-161.098" y="-1313.01" width="646" height="1962" rx="12" fill="white"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4016 2.27981H2.40165C2.07013 2.27981 1.75218 2.41555 1.51776 2.65717C1.28333 2.89878 1.15163 3.22648 1.15163 3.56817V13.875C1.15172 14.2167 1.28346 14.5443 1.51787 14.7858C1.75229 15.0274 2.07019 15.1631 2.40165 15.1631H17.4019C17.7334 15.1631 18.0514 15.0273 18.2858 14.7857C18.5202 14.5441 18.6519 14.2164 18.6519 13.8747V3.56817C18.6519 3.39895 18.6196 3.23139 18.5567 3.07506C18.4939 2.91873 18.4018 2.77668 18.2857 2.65704C18.1696 2.5374 18.0317 2.44251 17.88 2.37779C17.7283 2.31306 17.5658 2.27977 17.4016 2.27981ZM2.40165 0.991455C1.7386 0.991455 1.10271 1.26293 0.633857 1.74616C0.165008 2.22939 -0.0983887 2.88479 -0.0983887 3.56817L-0.0983887 13.875C-0.0983887 14.5584 0.165008 15.2138 0.633857 15.6971C1.10271 16.1803 1.7386 16.4518 2.40165 16.4518H17.4019C17.7302 16.4518 18.0553 16.3851 18.3586 16.2556C18.6619 16.1261 18.9376 15.9363 19.1697 15.6971C19.4019 15.4578 19.586 15.1737 19.7116 14.8611C19.8373 14.5485 19.9019 14.2134 19.9019 13.875V3.56817C19.9019 3.22979 19.8373 2.89473 19.7116 2.58211C19.586 2.26948 19.4019 1.98543 19.1697 1.74616C18.9376 1.50689 18.6619 1.31709 18.3586 1.1876C18.0553 1.0581 17.7302 0.991455 17.4019 0.991455H2.40165Z" fill="#636363"/>
                                                    <path d="M4.34595 4.99976H6.27182V12.9399H4.34595V4.99976ZM7.23492 4.99976H8.19803V12.9399H7.23492V4.99976ZM14.9387 4.99976H15.9018V12.9399H14.9387V4.99976ZM11.087 4.99976H13.977V12.9399H11.087V4.99976ZM9.16113 4.99976H10.1242V12.9399H9.16113V4.99976Z" fill="#636363"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_386_407">
                                                        <rect width="20.082" height="15.46" fill="white" transform="translate(0 0.991486)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div> Boleto
                                </div>
                                <div class="box-payment-option option">
                                    <div class="col-payment grey" id='percent-values-boleto'>${boleto.percentage}</div>
                                    <div class="col-payment col-graph">
                                        <div class="bar blue" style="width:${boleto.percentage};">-</div>
                                    </div>
                                    <div class="col-payment">
                                        <span class="money-td green bold grey font-size-14" id='boleto-value'>R$${boleto.value}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $("#block-payments").html(paymentsHtml);
        }
    });
}

function loadFrequenteSales() {
    let salesBlock = '';
    $('#card-most-sales .onPreLoad *' ).remove();
    $("#block-sales").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/most-frequent-sales?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $.each(response.data, function (i, item) {
                let value = removeMoneyCurrency(item.value);
                let newV = value.replace(/[\D]+/g,'');
                salesBlock = `
                    <div class="box-payment-option pad-0">
                        <div class="d-flex align-items list-sales">
                            <div class="d-flex align-items">
                                <div>
                                    <figure class="box-ico">
                                        <img class="photo" width="34px" height="34px" src="${item.photo}" alt="${item.description}">
                                    </figure>
                                </div>
                                <div>
                                    <span class="desc-product">${item.name}</span>
                                </div>
                            </div>
                            <div class="grey font-size-14">${item.sales_amount}</div>
                            <div class="grey font-size-14 value"><strong>${kFormatter(newV)}</strong></div>
                        </div>
                    </div>
                `;
                $("#block-sales").append(salesBlock);
                $('.photo').on('error', function() {
                    $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg');
                });
            });
            $('#block-sales .ske-load' ).remove();
        }
    });   
}

function kFormatter(num) {
    return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'k' : Math.sign(num)*Math.abs(num);
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
            loadDevices();
            typePayments();
            loadFrequenteSales();
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