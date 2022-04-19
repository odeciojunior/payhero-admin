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
        $('#select_projects').val(JSON.parse(sessionStorage.info).company);
    }

    $('.box-export').on('click', function() { 
            $.ajax({
                method: "GET",
                url: salesUrl + "/recurrence?project_id=" + $("#select_projects option:selected").val(),
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                }
            });
    });
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
                        <div class="no-sell">
                            <svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M55.5 111C86.1518 111 111 86.1518 111 55.5C111 24.8482 86.1518 0 55.5 0C24.8482 0 0 24.8482 0 55.5C0 86.1518 24.8482 111 55.5 111Z" fill="#F6F8FE"/>
                                <path d="M88.7999 111H22.2V39.22C25.339 39.2165 28.3485 37.9679 30.5682 35.7483C32.7879 33.5286 34.0364 30.5191 34.04 27.38H76.96C76.9566 28.935 77.2617 30.4753 77.8576 31.9116C78.4534 33.3479 79.3282 34.6519 80.4313 35.7479C81.5273 36.8513 82.8313 37.7264 84.2678 38.3224C85.7043 38.9184 87.2447 39.2235 88.7999 39.22V111Z" fill="white"/>
                                <path d="M55.5 75.48C65.3086 75.48 73.26 67.5286 73.26 57.72C73.26 47.9114 65.3086 39.96 55.5 39.96C45.6914 39.96 37.74 47.9114 37.74 57.72C37.74 67.5286 45.6914 75.48 55.5 75.48Z" fill="#2E85EC"/>
                                <path d="M61.7791 66.0922L55.5 59.8131L49.2209 66.0922L47.1279 63.9992L53.407 57.7201L47.1279 51.441L49.2209 49.348L55.5 55.6271L61.7791 49.348L63.8721 51.441L57.593 57.7201L63.8721 63.9992L61.7791 66.0922Z" fill="white"/>
                                <path d="M65.1199 79.92H45.8799C44.6538 79.92 43.6599 80.9139 43.6599 82.14C43.6599 83.3661 44.6538 84.36 45.8799 84.36H65.1199C66.346 84.36 67.3399 83.3661 67.3399 82.14C67.3399 80.9139 66.346 79.92 65.1199 79.92Z" fill="#DFEAFB"/>
                                <path d="M71.78 88.8H39.22C37.9939 88.8 37 89.7939 37 91.02C37 92.2461 37.9939 93.24 39.22 93.24H71.78C73.0061 93.24 74 92.2461 74 91.02C74 89.7939 73.0061 88.8 71.78 88.8Z" fill="#DFEAFB"/>
                            </svg>
                            <footer>
                                <h4>Nada por aqui...</h4>
                                <p>
                                    Não há dados suficientes
                                    para gerar este relatório.
                                </p>   
                            </footer>
                        </div>
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
            let { credit_card, pix, boleto } = response.data;

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
            if(response.data.length > 0) {
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
                    $('#block-sales .ske-load' ).remove();
                    $('#block-sales').addClass('scroll-212');
                    $("#block-sales").append(salesBlock);
                    $('.photo').on('error', function() {
                        $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg');
                    });
                });
            } else {
                salesBlock = `
                    <div class="box-payment-option pad-0">
                        <div class="d-flex align-items list-sales">
                            <div class="d-flex align-items">
                                <div class="no-sell">
                                    <svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M55.5 111C86.1518 111 111 86.1518 111 55.5C111 24.8482 86.1518 0 55.5 0C24.8482 0 0 24.8482 0 55.5C0 86.1518 24.8482 111 55.5 111Z" fill="#F6F8FE"/>
                                        <path d="M88.7999 111H22.2V39.22C25.339 39.2165 28.3485 37.9679 30.5682 35.7483C32.7879 33.5286 34.0364 30.5191 34.04 27.38H76.96C76.9566 28.935 77.2617 30.4753 77.8576 31.9116C78.4534 33.3479 79.3282 34.6519 80.4313 35.7479C81.5273 36.8513 82.8313 37.7264 84.2678 38.3224C85.7043 38.9184 87.2447 39.2235 88.7999 39.22V111Z" fill="white"/>
                                        <path d="M55.5 75.48C65.3086 75.48 73.26 67.5286 73.26 57.72C73.26 47.9114 65.3086 39.96 55.5 39.96C45.6914 39.96 37.74 47.9114 37.74 57.72C37.74 67.5286 45.6914 75.48 55.5 75.48Z" fill="#2E85EC"/>
                                        <path d="M61.7791 66.0922L55.5 59.8131L49.2209 66.0922L47.1279 63.9992L53.407 57.7201L47.1279 51.441L49.2209 49.348L55.5 55.6271L61.7791 49.348L63.8721 51.441L57.593 57.7201L63.8721 63.9992L61.7791 66.0922Z" fill="white"/>
                                        <path d="M65.1199 79.92H45.8799C44.6538 79.92 43.6599 80.9139 43.6599 82.14C43.6599 83.3661 44.6538 84.36 45.8799 84.36H65.1199C66.346 84.36 67.3399 83.3661 67.3399 82.14C67.3399 80.9139 66.346 79.92 65.1199 79.92Z" fill="#DFEAFB"/>
                                        <path d="M71.78 88.8H39.22C37.9939 88.8 37 89.7939 37 91.02C37 92.2461 37.9939 93.24 39.22 93.24H71.78C73.0061 93.24 74 92.2461 74 91.02C74 89.7939 73.0061 88.8 71.78 88.8Z" fill="#DFEAFB"/>
                                    </svg>
                                    <footer>
                                        <h4>Nada por aqui...</h4>
                                        <p>
                                            Não há dados suficientes
                                            para gerar este relatório.
                                        </p>   
                                    </footer>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#block-sales .ske-load' ).remove();
                $('#block-sales').removeClass('scroll-212');
                $("#block-sales").html(salesBlock);
            }
        }
    });   
}

function abandonedCarts() {
    let abandonedBlock = '';
    $("#card-abandoned .onPreLoad *" ).remove();
    $("#block-abandoned").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: salesUrl + "/abandoned-carts?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
             let { percentage, value } = response.data;

             abandonedBlock = `
                <div class="row container-payment height-auto">
                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.11982 0.436309C0.64062 0.432235 0.255174 0.841681 0.251702 1.34176C0.248229 1.84184 0.64062 2.24314 1.11982 2.24722C1.7605 2.25265 2.27269 2.69985 2.66595 3.5488C3.03404 4.34243 3.14167 4.99554 3.1547 5.07676L4.18517 12.0091C4.50116 13.66 6.00126 14.9236 7.57691 14.9236H13.7614C15.3371 14.9236 16.8441 13.6483 17.1532 12.0374L18.2114 4.68062C18.4571 3.40049 17.5508 2.24722 16.2581 2.24722H3.96814C3.31531 1.12545 2.32044 0.446451 1.11982 0.436309ZM4.72774 4.05812H16.2581C16.4552 4.05812 16.5403 4.16705 16.5021 4.36942L15.4438 11.7262C15.3041 12.4479 14.5097 13.1127 13.7614 13.1127H7.57691C6.82945 13.1127 6.04121 12.4624 5.8945 11.6979L4.89094 4.7938C4.86056 4.60121 4.79025 4.29598 4.72774 4.05812ZM6.76262 15.829C6.04381 15.829 5.46043 16.4371 5.46043 17.1872C5.46043 17.9373 6.04381 18.5454 6.76262 18.5454C7.48142 18.5454 8.0648 17.9373 8.0648 17.1872C8.0648 16.4371 7.48142 15.829 6.76262 15.829ZM14.5757 15.829C13.8569 15.829 13.2735 16.4371 13.2735 17.1872C13.2735 17.9373 13.856 18.5454 14.5757 18.5454C15.2945 18.5454 15.8779 17.9373 15.8779 17.1872C15.8779 16.4371 15.2954 15.829 14.5757 15.829Z" fill="#636363"/></svg>
                                    </div>Carrinhos
                                </div>

                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span>${percentage}</span>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-16">${value}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             `;

             $("#block-abandoned").html(abandonedBlock);
        }
    });
}

function orderbump() {
    let orderbumpBlock = '';
    $("#card-orderbump .onPreLoad *" ).remove();
    $("#block-orderbump").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: salesUrl + "/orderbump?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { amount, value } = response.data;

            if(value !== null) {
                orderbumpBlock = `
                   <div class="d-flex align-items">
                       <div class="balance col-6">
                           <h6 class="grey font-size-14">
                               <span class="ico-coin">
                                   <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                       <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                   </svg>
                               </span>
                               Ganhos
                           </h6>
                           <small>R$</small>
                           <strong class="total grey">${removeMoneyCurrency(value)}</strong>
                       </div>
                       <div class="balance col-6">
                           <h6 class="grey font-size-14 qtd">Conversões</h6>
                           <strong class="total grey">${amount} vendas</strong>
                       </div>
                   </div>
                `;
            } else {
                orderbumpBlock = `
                   <div class="d-flex align-items">
                       <div class="balance col-4">
                           <div class="box-ico-cash">
                               <span class="ico-cash">
                                   <svg width="55" height="55" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M28.4968 19.0015L36.0525 19.0029L36.1734 19.0168L36.2611 19.0364L36.365 19.0708L36.4541 19.1112L36.5179 19.1468L36.5805 19.1883L36.6445 19.2382L36.7076 19.2965L36.802 19.4062L36.8736 19.5174L36.9271 19.6302L36.9624 19.7355L36.9781 19.8007L36.9873 19.853L36.9983 20.0015V27.5054C36.9983 28.0576 36.5506 28.5054 35.9983 28.5054C35.4854 28.5054 35.0628 28.1193 35.005 27.622L34.9983 27.5054L34.998 22.4155L20.7061 36.7071C20.3456 37.0676 19.7784 37.0953 19.3861 36.7903L19.2919 36.7071C18.9314 36.3466 18.9037 35.7794 19.2087 35.3871L19.2919 35.2929L33.583 21.0015H28.4968C27.9839 21.0015 27.5612 20.6154 27.5035 20.1181L27.4968 20.0015C27.4968 19.4492 27.9445 19.0015 28.4968 19.0015Z" fill="#2E85EC"/>
                                       <circle cx="27.5" cy="27.5" r="26.5" stroke="#2E85EC" stroke-width="2"/>
                                   </svg>
                               </span>
                           </div>
                       </div>
                       <div class="balance col-8">
                           <h6 class="no-orderbump">Sem vendas por orderbump</h6>
                           <p class="txt-no-orderbump">Ofereça mais um produto no checkout e aumente sua conversão</p>
                       </div>
                   </div>
                `;
            }
            
            $("#block-orderbump").html(orderbumpBlock);
        }
    });
}

function upsell() {
    let upsellBlock = '';
    $("#card-upsell .onPreLoad *" ).remove();
    $("#block-upsell").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: salesUrl + "/upsell?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { value, amount } = response.data;
            
            if( value !== null ) {
                upsellBlock = `
                    <div class="d-flex align-items">
                        <div class="balance col-6">
                            <h6 class="grey font-size-14">
                                <span class="ico-coin">
                                    <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                    </svg>
                                </span>
                                Ganhos
                            </h6>
                            <small>R$</small>
                            <strong class="total grey">${removeMoneyCurrency(value)}</strong>
                        </div>
                        <div class="balance col-6">
                            <h6 class="grey font-size-14 qtd">Conversões</h6>
                            <strong class="total grey">${amount} vendas</strong>
                        </div>
                    </div>
                `;
            } else {
                upsellBlock = `
                    <div class="d-flex align-items">
                        <div class="balance col-4">
                            <div class="box-ico-cash">
                                <span class="ico-cash">
                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="55px" height="55px" viewBox="0 0 55 55" enable-background="new 0 0 55 55" xml:space="preserve">  <image id="image0" width="55" height="55" x="0" y="0"
                                        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADcAAAA3CAYAAACo29JGAAAABGdBTUEAALGPC/xhBQAAACBjSFJN
                                    AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QA/wD/AP+gvaeTAAAA
                                    CXBIWXMAAA7DAAAOwwHHb6hkAAAAB3RJTUUH5gIQDSkIZFXSSgAADaVJREFUaN7NmmtwVdd1gL+9
                                    zzn36l69ESAJSWDeiIcx+IkNJhg9gDRO7NT+lWknbqfu40czndjGxnZnmkxTY5s4mXbaZurU40w7
                                    yaSOYzvYGIQgftTGPMxTEg+noBcIgd5X995zz9m7P/a9QqArdPWI2/UD/lzts7691l57rbWX8DxP
                                    MwUiBQgBSkF/XHO5X9Heq+jo13RFFAOuxldgS8gOCqaFJSV5gtI8ycxcSW5QICVoDWpKNAJ7sgtY
                                    EjwF7b2Kkxd9jrR4NF3yaetV9EYVcd8Aj9gMCUEb8rMkZQWSymKL1RU2y2dZFOdKLAm+Gr8+w0VM
                                    1HKWhGgCTrR57G5K8On5BC3dioQPOUHBjBzBrHxJca6kMCzIDoghhSOupmtQ09GnuNinuDygiMQh
                                    YEFFoWTNXIfqJQ7LZ9mEnIlDjhtOCkj4cLjF45efx/nkfzx6o5qibMHyUos75zismGVRXiDJDwmC
                                    thhy2ZRoDb4G19P0RjUt3YoTF30OXkhwst2na1CTHxLcO9fmkVVBVlfYONb43XVccFLA+auKnx2M
                                    8V5Dgr6YZm6RpHpJgAcWOcyfLgkHDIXSBmJMBYRZF2DQ1ZzrVOw941LXlOB8lyI/JNiyNMC37gwy
                                    Z5ocF2BGcEKA50PdaZd//TjO2cs+ZQWSh24N8AfLA5QXSiTGGpMVS5iNaelRvHPC5dfHXdp7FYuL
                                    LR6/L4uNix0smeHGjQUnhYl+r30a5z8OxnF9zcbFDo+tyWJJsYVg6qLbjd/VGho7fF79JEb9mQRZ
                                    tuBbdwb547uD5ATFmN+9KZwUcCWieWVflHdOuBSGBX96bxYPrwwQDoy9+FSIJWDA1bxx1OXVT2L0
                                    RDXfWBHgrzeEmBa+uQ6jwkkBVwY0P9gzyPuNCeYVSZ6oCrF2ngMiM7eYKhEC0PDBFwlerItyoUvx
                                    1eUBnqy6OaA92mJ9Mc2OfVHeb0ywpNjiuU1hbiuzzLn6EsHg2kauX+CQlyX43q4oO0+6OBKerA6R
                                    ExRpN1uOAMOE+n/77xi/OemyYLrk+c1hbiu3piRgTEaUhtXlNs9vCjG3SPL2CZeffhLH89P/fiSc
                                    gHdPufz8cJwZOYInq8OsLLMmnS1MlfgaVpXbPFEVYlq24D8PxdnV6A5dJ6PCSQGnL/v85OMYvobH
                                    14ZYM9eeErDhl7gQE18nBbh2vsOf3ZeF52t+8nGMc53+CMAhOAHEEvDap3EudCm2LAvw4IrAlJwv
                                    KeByv2JHfZRX9ke5MqDT7vS4RMM3bg1QuzTA764oXjsQJ+5d/5OhgCIlfPy7BHtPu8wtknz77iBZ
                                    9uTvMCmgo1/x97uj1J9OIAS09yierglTlD3x60QDIUfw2D1BjrZ67GlKULU4wYZFzpCnSTBW64tp
                                    fnEkTsyDR1cHmTfdmhKwzgHN9roo+84khtxxT1OC7XVRrkYmZ0GlYcEMi0dWBRlMGP37Y5rUkjJl
                                    tYMXPI60eFSWWGxeGpi0N6YSgBf2DFLXlEAKqK10qFrsALCrweWlvVG6BicHqIEtyxwWz7Q41Oxx
                                    uMVDymFwcc98LObB5qUBZuSISV3Sw8H2NCUA2LQ0wLbaMNtqw2xc7KCBdxtcXqybHKDWUJwr2bzU
                                    IZqAXQ0J3OTZk1LAhS6fQ80es/Il6xc4U2ax3Y0GrKbS4bsbQxSEBEXZgq01IQOopwYQ4CsLHUry
                                    JAebPVq6TeSUQsDhZo/OAc0ds20qCsdXVtwIdvUGsOolDk9Vh5meDB5Kw8wcydbqEA8sSgKeMi7a
                                    PUFApWF2ocXtFRYdfYojrb4ppeKeKTyFgLvnmKJwMmDb66Kjgg1XpjhX8nRNEhDYecrlpfooPdGJ
                                    AQZsuGuO8brDzR6uD/JqRHG2UzEtLKgssSZ01qSA7kHNy/VRdjW4Q2Bbq8PMyEkf7kcAath50uWH
                                    +6L0TgBQa1hWalEYFpy57NMVUcjWbsXlfkVZgaQkT44bTgroiWpeqo+y85SLBqoWJy2Wc/N7bDjg
                                    VxY6KA1vHXd5ZV+Uvtj4ADVQkieZlS/p6Fe09SjkhW7FoKupKLQIB8S4roAU2Mv1JkvXGjYudtha
                                    ExrVYqMBPlMTYt18A/jmcWPB8QBqbRpTFYWSSFzT3K2Q7b0KX8GsPIktM1voOrC9ppBV2kSsp6pD
                                    zMhJH5QsaYrPdIAleZJttaZe9DW8eWz8gLaE0jw51GqUVwZMrjItW2Sc0F4HdtKA3b/A4ZmaECW5
                                    I8FSrYjDzR5HWj2Uhhs/pTTMyk8B2kOAKRfNRDchYFq2sdDViEJGXPOH2YHMyEQSbEf99WDbakOU
                                    5I0O9sbRON95I8J33ojw9glzNtMBlhVInq0Ns3aebVz0mMuP9kcZiGsy0TDFMRAH6fnmI5m6pFLw
                                    009jvHXcgK1PgpXeBOy/jsb58f4Y3YOarojZmDePuagxAO+bZ+MpeOOoy+sH4hnFA0smu3VKIy1p
                                    Ik2mVbav4VKvuuaKGYL1xTSl+ZLiPElPVPPDfVF+fcxNq3AKcFsS0FfQ3qcyDlA6aSw7O9l/iLiZ
                                    0TkS/nJdiHULHO6aY1M82hnDuOKP98fojWnmFEqe3RTGV5rvvx+ltUfxyr4oUsDXbw2kVbK8QPK3
                                    W8IcavZYWWZn1K8cdA1dOCCwi8LGMboHdUZ3nAZuKZLMnR4YSqfS/eZXR11+9NvrwdbMNeXjs7UM
                                    Ae7YF0VK+Nry9IAleZIHVwQyev3RGroiJkAWZUtkab5ESriYvBIyEaXN48Rom/HWcRMEeqMG7Lkk
                                    WGoz1s53eLY2RHmBpHvQnMGdp9xRFfZVZkWzp+Bin8aSJvLKOdMswo6gpUcxmMgsIt1M3j7hsmOf
                                    yRFnF0q2bQpzTxJs+OasnW+ujrJ8ydWIuVbea3An/H2BccnWHp9wwFzmsqJAMiNH0Nrtc7lfTap5
                                    885Jlx31JrsvTwaEe28AGw54/wKHp2tMQLoS0bxYF+XdCQKKZDujrUcxM1dSXiCR03Mk82dYXI1o
                                    mjr8CcO9c8Ll5WRlXZZv0qn75tlj5pbrFxrAkkkCCgGNl8zz18IZ0py5LAdWV5iM4LML3qgNzrEs
                                    9nK9AZuVL3mmNsT9C5yMzonWsGGRw9Zqk92kAHc1jg8w4cOB8yb7WV1hE7STbYY7ZtsUZQsOXvBo
                                    71PjysZ3njKuOARWE2J9hmDDAVMJd8qC2+ui7G5KZORJUkBbj+Jwi8eMHMHtFTZokL6CuUUWq8pt
                                    WnsUH32R2YICeL/RuOLVyDWwVOkyXkkBPl0dojhXJrtmprk0lj4C+PCLBG09itUVNnOmmda/BAg5
                                    UFsZwJbGElcjN09UDZhpz12JaErzrq/JJipawwOLjYsW5wo6+jX/sOdavzOtLgI6I5qdp1wCNmyq
                                    DJCVbANJMAd7zVyblWU2J9t99p5OMFqqKQTsPZNge90gnQOakmSxuWGSYMMBq5Y4PFFlqviOfsUP
                                    9kT57dn0gALY0+TScNFnVbnN3bfYQ9MTMrVgYVjwh6sCWBJ+fjhOS0/6s+d6JlNv71UU5wqeqg6x
                                    YdHUgA0HrF3i8ERViOnZ5pp667g7IthJAc3dil8ccXEseGRVkPzQtYJ7qJ2ulCk21813qGtK8LPP
                                    4nx3YwjrBhM6FmyqdBDAw7cF2JDsf0y1aIyLWULw9gmXmkoHW157uhCA68PrB+Kcu+yzaanDugX2
                                    dTMv172sSgHH23z+5s0I/THN85tDfHVZIK1VEr7pOP2+X1iFMN5yY1dOCpMNfX/XIIVhyY6Hs1lW
                                    ev0TwHV2URpWlFk8dk+QhK/5xw9iHG3zRlgvZcEv4+lY65FgloQjLR7/9KF5antsTZClpSPfNkaq
                                    reGhlQEeWhmgtVvxwu4oZy/7aQH/L8SS0NTh88KeKO09im+uDPL1W4Npn9pGqJx6GvqLdSZQHG/3
                                    +d6uKGf+HwCmwP7uvUFOXfKpWuLw5+uyyLLTPyOmVVdpmJ4teKoqxNr5NoeaPZ77zSBHWjwz6vQl
                                    QwnMGTt0wehxrM3n/gXOmNMMY86htPUottdFqT+ToKxA8lfrsqhdGiAwgVmsiYhMBpR3G1z++cMY
                                    7b2K6uQ1ka69kTFcavGrEc2/fBTjV8fiWBIeXBHkj+4KMrtQovn9BBaR9JDzXYrXD8R4J9n0/eZt
                                    QR5fmzXmgE1GcCnAWALeOhHn1U/itPUoFs6UPLoqSM2SANNzrg2zTVZSiUPngOb9Rpdffh7nXKei
                                    olDyJ2uy+NqKAMEMr6CMp/ZE8p+Giz7//mmM/WcTJHyoLLHYsizA/fMdygvl0BWhdWazAikLpe6z
                                    1h7FB+cSvNfg0nDJJ2jDhoUO374niyUlFmS47rjghu/sYELz0Rdm3vLzFo+4Z3oWt1fY3HWLTWWx
                                    RUmeJDsosGX60QytIaEgEtdc6lM0XvI5kHy6bu9VZNmwqsLm0dVB1s6zCTnjHw6Y0KSswLyj90U1
                                    n13w2NXocrjFo7PfVBMFIUFZgSn1S/Ik08KS7IB5xlVKE3Gha1BxsVfR2mNmobujxiQzcwV3zHao
                                    rXS4c45NXpZAqYlNjEx4DHg4ZNyD5i4z33ykxedsp09HvyLi6ptW9rZl2t/FuZJFM82M8+oKi4pC
                                    i6DNhKGmBG64pEZ9Xc9Ypb1X0dJt/u+KaAZcjac0thTkBAVFYUFpvqSiUFKWLykMy6FcdaqumP8F
                                    CHotCRcObH4AAAAldEVYdGRhdGU6Y3JlYXRlADIwMjItMDItMTZUMTY6NDI6MTIrMDM6MDBCCShp
                                    AAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIyLTAyLTE2VDE2OjQyOjEyKzAzOjAwM1SQ1QAAAABJRU5E
                                    rkJggg==" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="balance col-8">
                            <h6 class="no-orderbump">Sem vendas por upsell</h6>
                            <p class="txt-no-orderbump">Ofereça mais um produto no checkout e aumente sua conversão</p>
                        </div>
                    </div>
                `;
            }
            $("#block-upsell").html(upsellBlock);
        }
    });

}

function conversion() {
    let conversionBlock = '';
    $("#card-conversion .onPreLoad *" ).remove();
    $("#block-conversion").prepend(skeLoad);
   return $.ajax({
        method: "GET",
        url: salesUrl + "/conversion?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { credit_card, pix, boleto } = response.data;

            conversionBlock = `
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
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span>${credit_card.approved}</span>
                                            /<small>${credit_card.total}</small>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-16">${credit_card.percentage}</strong>
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
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span>${pix.approved}</span>
                                            /<small>${pix.total}</small>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-16">${pix.percentage}</strong>
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
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span>${boleto.approved}</span>
                                            /<small>${boleto.total}</small>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-16">${boleto.percentage}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $("#block-conversion").html(conversionBlock);
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
        updateStorage({calendar: $(this).val()});
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
            abandonedCarts();
            //orderbump();
            //upsell();
            conversion();
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

function updateStorage(v){
    var existing = sessionStorage.getItem('info');
    existing = existing ? JSON.parse(existing) : {};
    Object.keys(v).forEach(function(val, key){
        existing[val] = v[val];
   })
    sessionStorage.setItem('info', JSON.stringify(existing));
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