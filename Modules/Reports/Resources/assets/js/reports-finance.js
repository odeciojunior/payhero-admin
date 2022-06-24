$(function() {
    loadingOnScreen();
    exportReports();

    updateReports();

    changeCompany();
    changeCalendar();

    if(sessionStorage.info) {
        let info = JSON.parse(sessionStorage.getItem('info'));
        $('input[name=daterange]').val(info.calendar);
    }

});

let resumeUrl = '/api/reports/resume';
let financesResumeUrl = '/api/reports/finances';

function distribution() {
    let distributionHtml = '';
    $('#card-distribution .onPreLoad *').remove();
    $("#block-distribution").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/distribuitions?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
             let { available, blocked, pending, total } = response.data;
             let series = [available.percentage, pending.percentage, blocked.percentage];

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
                <div class="d-flex box-distribution">
                    <div class="distribution-area">
                        <header class="grey font-size-14">
                            <span class="${available.color} cube"></span>
                            Disponível
                        </header>
                        <footer class="footer-distribution">
                            <small>R$</small>
                            <strong>${removeMoneyCurrency(available.value)}</strong>
                        </footer>
                    </div>
                    <div class="distribution-area">
                        <header class="grey font-size-14">
                            <span class="cube ${pending.color}">
                                <i></i>
                            </span>
                            Pendente
                        </header>
                        <footer class="footer-distribution">
                            <small>R$</small>
                            <strong class="value-pending">${removeMoneyCurrency(pending.value)}</strong>
                        </footer>
                    </div>
                    <div class="distribution-area" style="${removeMoneyCurrency(blocked.value) == "0,00" ? 'display: none': ''}">
                        <header class="grey font-size-14">
                            <span class="cube ${blocked.color}">
                                <i></i>
                            </span>
                            Bloqueado
                        </header>
                        <footer class="footer-distribution">
                            <small>R$</small>
                            <strong>${removeMoneyCurrency(blocked.value)}</strong>
                        </footer>
                    </div>
                </div>
             `;
             $("#block-distribution").html(distributionHtml);
             $(".box-graph-dist").prepend('<div class="distribution-graph"></div>');
             distributionGraph(series);
        }
    });

}

function withdrawals() {

    let infoWithdraw, graphDraw = '';
    $('#card-draw .onPreLoad *').remove();
    $("#draw").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/withdrawals?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { chart } = response.data;
            let { withdrawal, income } = chart;
            let incomeTotal = String(removeMoneyCurrency(income.total).replace(',','.'));
            let withdrawalTotal = String(removeMoneyCurrency(withdrawal.total).replace(',','.'));
            const numbers = [incomeTotal, withdrawalTotal].map(Number).reduce((prev, value) => prev + value,0);           

            infoWithdraw = `
                <div class="no-draws">
                    <footer class="d-flex footer-withdrawals">
                        <div>                            
                            ${noWithdrawal}
                        </div>

                        <div class="data-withdrawals">
                            <h6>Sem dados, por enquanto...</h6>
                            <p>
                            Ainda faltam dados suficientes para a comparação, continue rodando!
                            </p>
                        </div>
                    </footer>
                </div>

            `;

            graphDraw = `<div id="block-withdraw"></div>`;

            if(numbers !== 0) {
                $("#draw").html(graphDraw);
                $('#block-withdraw').html('<canvas height="260" id="financesChart"></canvas>');
                let label = [...chart.labels];
                let withdraw = [...chart.withdrawal.values];
                let series = [...chart.income.values];
                barGraph(series, label, withdraw);
            } else {
                $('#card-draw .onPreLoad *').remove();
                $("#card-draw").find('.graph').after(infoWithdraw);
            }
        }
    });
}

function blockeds() {
    let blockedsHtml = '';
    $('#card-blockeds .onPreLoad *' ).remove();
    $("#block-blockeds").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/blockeds?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        beforeSend: function( jqXHR ) {
            jqXHR.then(dados => {
                let { value } = dados.data;
                if( removeMoneyCurrency(value) == "0,00" ) {
                    $('#card-blockeds').hide();
                }
            })
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
             let {amount, value} = response.data;
             
             blockedsHtml = `
                <div class="d-flex">
                    <div class="balance col-3">
                        <h6 class="grey font-size-14">Total</h6>
                        <strong class="grey total">${kFormatter(amount)}</strong>
                    </div>
                    <div class="balance col-9">
                        <h6 class="font-size-14">Saldo</h6>
                        <small>R$</small>
                        <strong class="total red ">${removeMoneyCurrency(value)}</strong>
                    </div>
                </div>
             `;
             $("#block-blockeds").html(blockedsHtml);
        }
    });
}

function onResume() {
    let ticket, commission, chargebacks, trans = '';
    $('#finance-card .onPreLoad *').remove();

    $("#finance-commission,#finance-ticket,#finance-chargebacks,#finance-transactions").html(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/resume?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { transactions, average_ticket, comission, chargeback } = response.data;

            trans = `
                <span class="title">N de transações</span>
                <div class="d-flex">
                    <strong class="number">
                        <span>${transactions == undefined ? 0 : transactions}</span>
                    </strong>
                </div>
            `;

            ticket = `
                <span class="title">Ticket Médio</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${average_ticket == undefined ? '0,00' : removeMoneyCurrency(average_ticket)}</strong>
                </div>
            `;
            commission = `
                <span class="title">Comissão total</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${comission == undefined ? '0,00' : removeMoneyCurrency(comission)}</strong>
                </div>
            `;

            chargebacks = `
                <span class="title">Total em Chargebacks</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number"><span class="bold">${chargeback == undefined ? '0,00' : removeMoneyCurrency(chargeback)}</span></strong>
                </div>
            `;

            $("#finance-commission").html(commission);
            $("#finance-ticket").html(ticket);
            $("#finance-chargebacks").html(chargebacks);
            $("#finance-transactions").html(trans);
        }
    });

}

function onCommission() {
    let infoComission = '';
    $('#card-commission .onPreLoad *').remove();
    $("#block-commission").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: resumeUrl + "/commissions?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { chart, total, variation } = response.data;

            infoComission = `
                <div class="d-flex justify-content-between box-finances-values">
                    <div class="finances-values">
                        <span>R$</span>
                        <strong>${removeMoneyCurrency(total)}</strong>
                    </div>
                    <div class="finances-values">
                        <svg class="${variation.color}" width="18" height="14" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.1237 0L16.9451 0.00216293L17.1065 0.023901L17.2763 0.0736642L17.4287 0.145306L17.4865 0.18052L17.5596 0.23218L17.6737 0.332676L17.8001 0.484464L17.8876 0.634047L17.9499 0.792176L17.9845 0.938213L18 1.125V7.88084C18 8.50216 17.4964 9.00583 16.8751 9.00583C16.3057 9.00583 15.835 8.58261 15.7606 8.03349L15.7503 7.88084L15.7495 3.8415L9.41947 10.1762C9.01995 10.5759 8.39457 10.6121 7.95414 10.2849L7.82797 10.1758L5.62211 7.96668L1.92041 11.6703C1.48121 12.1098 0.768994 12.1099 0.329622 11.6707C-0.069807 11.2713 -0.106236 10.6463 0.220416 10.2059L0.329304 10.0797L4.82693 5.57966C5.22645 5.17994 5.85182 5.14374 6.29225 5.47097L6.41841 5.58004L8.62427 7.78914L14.1597 2.25H10.1237C9.55424 2.25 9.08361 1.82677 9.00912 1.27766L8.99885 1.125C8.99885 0.50368 9.50247 0 10.1237 0Z" fill="#1BE4A8"/>
                        </svg>
                        <em class="${variation.color}">${variation.value}</em>
                    </div>
                </div>
                <section style="margin-left: -7px;">
                    <div class="graph-reports">
                        <div class="${removeMoneyCurrency(total) !== '0,00' ? 'new-finance-graph' : ''  }"></div>
                    </div>
                </section>
            `;
            $("#block-commission").html(infoComission);

            if( removeMoneyCurrency(total) !== '0,00' ) {
                $('.new-finance-graph').html('<canvas id=comission-graph-finance></canvas>');
                let labels = [...chart.labels];
                let series = [...chart.values];
                graphComission(series, labels, variation.value);
            } else {
                infoComission = `
                    <div class="finances-values">
                        <span>R$</span>
                        <strong>0</strong>
                    </div>
                    <div class="row d-flex empty-graph">
                        <div class="info-graph no-info-graph">
                            <div class="no-sell">
                                ${bigGraph}
                                <footer class="footer-no-info">
                                    <p>Sem dados para gerar o gráfico</p>
                                </footer>
                            </div>
                        </div>
                    </div>
                `;
                $("#block-commission").html(infoComission);
            }
        }
    });
}

function getPending() {
    let pendingBlock = '';
    $('#card-pending .onPreLoad *' ).remove();
    $("#block-pending").html(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/pendings?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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

            pendingBlock = `
                <footer class="">
                    <div class="d-flex">
                        <div class="balance col-3">
                            <h6 class="grey font-size-14">Total</h6>
                            <strong class="grey total">${kFormatter(amount)}</strong>
                        </div>
                        <div class="balance col-9">
                            <h6 class="font-size-14">Saldo</h6>
                            <small>R$</small>
                            <strong class="total orange">${removeMoneyCurrency(value)}</strong>
                        </div>
                    </div>
                </footer>
            `;
            $("#block-pending").html(pendingBlock);
        }
    });
}

function getCashback() {
    let cashBlock = '';
    $('#card-cashback .onPreLoad *' ).remove();
    $("#block-cash").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/cashbacks?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },

        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { quantity, value } = response.data;
            if(response.data !== undefined) {
                if(removeMoneyCurrency(value) !== '0,00') {
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
                            <strong class="total grey">${removeMoneyCurrency(value)}</strong>
                        </div>
                        <div class="balance col-6">
                            <h6 class="grey font-size-14 qtd">Quantidade</h6>
                            <strong class="total grey">${quantity} vendas</strong>
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
        $.ajaxQ.abortAll();
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
        $.ajaxQ.abortAll();
        updateStorage({company: $(this).val(), companyName: $(this).find('option:selected').text()});
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
                    removeDuplcateItem("#select_projects option");
                });
                if(sessionStorage.info) {
                    $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
                    $("#select_projects").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
                }
            } else {
                $("#export-excel").hide();
                $("#project-not-empty").hide();
                $("#project-empty").show();
            }

            loadingOnScreenRemove();
            $('.onPreLoad *').remove();
            blockeds();
            onResume();
            onCommission();
            getPending();
            getCashback();
            withdrawals();
            distribution();
        },
    });
}


function updateStorage(v){
    var existing = sessionStorage.getItem('info');
    existing = existing ? JSON.parse(existing) : {};
    Object.keys(v).forEach(function(val, key){
        existing[val] = v[val];
   })
    sessionStorage.setItem('info', JSON.stringify(existing));
}

function graphComission(series, labels, variant) {
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
    const ctx = document.getElementById('comission-graph-finance').getContext('2d');
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
                            return 'R$ ' + (value / 100000) + 'K '
                        }
                    }

                },
            },
            pointBackgroundColor:"#1BE4A8",
            radius: (variant != '0%') ? 3 : 0,
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

function barGraph(series, labels, withdraw) {
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
                labels,
                datasets: [
                    {
                        label: 'Receitas',
                        data: series,
                        color:'#636363',
                        backgroundColor: "rgba(69, 208, 126, 1)",
                        borderRadius: 4,
                        barPercentage: 0.5
                    },
                    {
                        label: 'Saques',
                        data: withdraw,
                        color:'#636363',
                        backgroundColor: "rgba(216, 245, 228, 1)",
                        borderRadius: 4,
                        barPercentage: 0.7
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        align: 'center',
                        labels: {
                            boxWidth: 10,
                            color: '#636363',
                            usePointStyle: true,
                            pointStyle: 'circle',
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

                responsive: false,
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
                        ticks: {
                            padding: 0,
                            font: {
                                family: 'Muli',
                                size: 12,
                            },
                            color: "#747474",
                            callback: function(value){
                                return (value / 100000) + 'K '
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

function distributionGraph(series) {
    new Chartist.Pie('.distribution-graph', {
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

function kFormatter(num) {
    return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'k' : Math.sign(num)*Math.abs(num);
}

const formatCash = n => {
    if (n < 1e3) return n;
    if (n >= 1e3 && n < 1e6) return +(n / 1e3).toFixed(1) + "K";
    if (n >= 1e6 && n < 1e9) return +(n / 1e6).toFixed(1) + "M";
    if (n >= 1e9 && n < 1e12) return +(n / 1e9).toFixed(1) + "B";
    if (n >= 1e12) return +(n / 1e12).toFixed(1) + "T";
};

function removeDuplcateItem(item) {
    for (i = 0; i < $(item).length; i++) {
        text = $(item).get(i);
        for (j = i + 1; j < $(item).length; j++) {
          text_to_compare = $(item).get(j);
          if (text.innerHTML == text_to_compare.innerHTML) {
            $(text_to_compare).remove();
            j--;
            maxlength = $(item).length;
          }
        }
    }
}

// abort all ajax
$.ajaxQ = (function(){
    var id = 0, Q = {};
  
    $(document).ajaxSend(function(e, jqx){
      jqx._id = ++id;
      Q[jqx._id] = jqx;
    });
    $(document).ajaxComplete(function(e, jqx){
      delete Q[jqx._id];
    });
  
    return {
      abortAll: function(){
        var r = [];
        $.each(Q, function(i, jqx){
          r.push(jqx._id);
          jqx.abort();
        });
        return r;
      }
    };
  
  })();

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
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let bigGraph = `
<svg width="863" height="242" viewBox="0 0 863 242" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M748.537 160.747C793.828 168.612 825.042 215.402 863 223.075V242H6.10352e-05V127.825C42.2571 127.825 47.8961 162.696 67.7818 153.097C87.6674 143.497 108.556 77.4964 131.756 80.9871C154.956 84.4778 167.688 -8.14635 216.574 0.580398C265.459 9.30715 301.438 108.98 340.381 66.2193C379.324 23.4582 387.601 46.0209 429.03 37.2942C470.458 28.5674 488.607 133.109 527.846 129.769C567.086 126.428 577.561 213.379 617.605 205.314C666.13 195.541 661.906 190.067 683.483 177.391C705.059 164.714 724.398 156.555 748.537 160.747Z" fill="url(#paint0_linear_2642_647)"/>
<defs>
<linearGradient id="paint0_linear_2642_647" x1="431.5" y1="-3.8329e-05" x2="431.5" y2="229.5" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let noWithdrawal = `
<svg width="111" height="138" viewBox="0 0 111 138" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M55.5 132C86.1518 132 111 107.152 111 76.5C111 45.8482 86.1518 21 55.5 21C24.8482 21 0 45.8482 0 76.5C0 107.152 24.8482 132 55.5 132Z" fill="#FAFAFA"/>
<path d="M87.32 52.8199H23.68C21.6365 52.8199 19.98 54.4765 19.98 56.5199V134.22C19.98 136.263 21.6365 137.92 23.68 137.92H87.32C89.3634 137.92 91.02 136.263 91.02 134.22V56.5199C91.02 54.4765 89.3634 52.8199 87.32 52.8199Z" fill="white"/>
<path d="M48.0999 63.9199H28.8599C27.6338 63.9199 26.6399 64.9138 26.6399 66.1399C26.6399 67.3659 27.6338 68.3599 28.8599 68.3599H48.0999C49.326 68.3599 50.3199 67.3659 50.3199 66.1399C50.3199 64.9138 49.326 63.9199 48.0999 63.9199Z" fill="#B4DAFF"/>
<path d="M61.4199 73.5397H28.8599C27.6338 73.5397 26.6399 74.5336 26.6399 75.7597C26.6399 76.9857 27.6338 77.9797 28.8599 77.9797H61.4199C62.646 77.9797 63.6399 76.9857 63.6399 75.7597C63.6399 74.5336 62.646 73.5397 61.4199 73.5397Z" fill="#DFEAFB"/>
<path d="M48.0999 83.8999H28.8599C27.6338 83.8999 26.6399 84.8938 26.6399 86.1199C26.6399 87.346 27.6338 88.3399 28.8599 88.3399H48.0999C49.326 88.3399 50.3199 87.346 50.3199 86.1199C50.3199 84.8938 49.326 83.8999 48.0999 83.8999Z" fill="#B4DAFF"/>
<path d="M61.4199 93.5199H28.8599C27.6338 93.5199 26.6399 94.5138 26.6399 95.7399C26.6399 96.966 27.6338 97.9599 28.8599 97.9599H61.4199C62.646 97.9599 63.6399 96.966 63.6399 95.7399C63.6399 94.5138 62.646 93.5199 61.4199 93.5199Z" fill="#DFEAFB"/>
<path d="M48.0999 103.88H28.8599C27.6338 103.88 26.6399 104.874 26.6399 106.1C26.6399 107.326 27.6338 108.32 28.8599 108.32H48.0999C49.326 108.32 50.3199 107.326 50.3199 106.1C50.3199 104.874 49.326 103.88 48.0999 103.88Z" fill="#B4DAFF"/>
<path d="M61.4199 113.5H28.8599C27.6338 113.5 26.6399 114.494 26.6399 115.72C26.6399 116.946 27.6338 117.94 28.8599 117.94H61.4199C62.646 117.94 63.6399 116.946 63.6399 115.72C63.6399 114.494 62.646 113.5 61.4199 113.5Z" fill="#DFEAFB"/>
<g filter="url(#filter0_d_1640_468)">
<path d="M87.32 15.08H23.68C21.6365 15.08 19.98 16.7365 19.98 18.78V40.98C19.98 43.0235 21.6365 44.68 23.68 44.68H87.32C89.3634 44.68 91.02 43.0235 91.02 40.98V18.78C91.02 16.7365 89.3634 15.08 87.32 15.08Z" fill="#1485FD"/>
</g>
<path d="M48.0999 23.2201H28.8599C27.6338 23.2201 26.6399 24.214 26.6399 25.4401C26.6399 26.6661 27.6338 27.6601 28.8599 27.6601H48.0999C49.326 27.6601 50.3199 26.6661 50.3199 25.4401C50.3199 24.214 49.326 23.2201 48.0999 23.2201Z" fill="#B4DAFF"/>
<path d="M61.4199 32.8401H28.8599C27.6338 32.8401 26.6399 33.834 26.6399 35.0601C26.6399 36.2862 27.6338 37.2801 28.8599 37.2801H61.4199C62.646 37.2801 63.6399 36.2862 63.6399 35.0601C63.6399 33.834 62.646 32.8401 61.4199 32.8401Z" fill="white"/>
<defs>
<filter id="filter0_d_1640_468" x="1.78146" y="0.521187" width="107.437" height="65.997" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
<feFlood flood-opacity="0" result="BackgroundImageFix"/>
<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
<feOffset dy="3.6397"/>
<feGaussianBlur stdDeviation="9.09926"/>
<feComposite in2="hardAlpha" operator="out"/>
<feColorMatrix type="matrix" values="0 0 0 0 0.180392 0 0 0 0 0.521569 0 0 0 0 0.92549 0 0 0 0.17 0"/>
<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1640_468"/>
<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1640_468" result="shape"/>
</filter>
</defs>
</svg>
`;