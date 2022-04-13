$(function() {
    loadingOnScreen();
    exportReports();
    
    updateReports();    
    
    changeCompany();
    changeCalendar();
    
    if(!sessionStorage.info) {
        return;
    }
    let info = JSON.parse(sessionStorage.getItem('info'));
    $('input[name=daterange]').val(info.calendar); 
    
});

let resumeUrl = '/api/reports/resume';
let financesResumeUrl = '/api/reports/finances';

function distribution() {
    let distributionHtml = '';   
    $('#card-distribution .onPreLoad *').remove();
    $("#block-distribution").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/distribuitions?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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
                    <div class="distribution-area">
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
             $(".box-graph-dist").prepend('<div class="distribution-graph"></div>')
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
        url: financesResumeUrl + "/withdrawals?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {

            infoWithdraw = `
                <div class="no-draws">
                    <footer class="d-flex footer-withdrawals">
                        <div>
                            <svg width="122" height="151" viewBox="0 0 122 151" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M60.994 144.494C94.68 144.494 121.988 117.186 121.988 83.5C121.988 49.8139 94.68 22.506 60.994 22.506C27.3079 22.506 0 49.8139 0 83.5C0 117.186 27.3079 144.494 60.994 144.494Z" fill="url(#paint0_linear_1185_948)"/>
                                <path d="M95.9638 57.4758H26.024C23.7783 57.4758 21.9578 59.2964 21.9578 61.5421V146.934C21.9578 149.179 23.7783 151 26.024 151H95.9638C98.2095 151 100.03 149.179 100.03 146.934V61.5421C100.03 59.2964 98.2095 57.4758 95.9638 57.4758Z" fill="white"/>
                                <path d="M52.8614 69.6746H31.7169C30.3694 69.6746 29.2771 70.7669 29.2771 72.1143C29.2771 73.4618 30.3694 74.5541 31.7169 74.5541H52.8614C54.2089 74.5541 55.3012 73.4618 55.3012 72.1143C55.3012 70.7669 54.2089 69.6746 52.8614 69.6746Z" fill="#B4DAFF"/>
                                <path d="M67.5 80.2467H31.7169C30.3694 80.2467 29.2771 81.339 29.2771 82.6865C29.2771 84.0339 30.3694 85.1262 31.7169 85.1262H67.5C68.8474 85.1262 69.9398 84.0339 69.9398 82.6865C69.9398 81.339 68.8474 80.2467 67.5 80.2467Z" fill="#DFEAFB"/>
                                <path d="M52.8614 91.6324H31.7169C30.3694 91.6324 29.2771 92.7248 29.2771 94.0722C29.2771 95.4196 30.3694 96.512 31.7169 96.512H52.8614C54.2089 96.512 55.3012 95.4196 55.3012 94.0722C55.3012 92.7248 54.2089 91.6324 52.8614 91.6324Z" fill="#B4DAFF"/>
                                <path d="M67.5 102.205H31.7169C30.3694 102.205 29.2771 103.297 29.2771 104.644C29.2771 105.992 30.3694 107.084 31.7169 107.084H67.5C68.8474 107.084 69.9398 105.992 69.9398 104.644C69.9398 103.297 68.8474 102.205 67.5 102.205Z" fill="#DFEAFB"/>
                                <path d="M52.8614 113.59H31.7169C30.3694 113.59 29.2771 114.683 29.2771 116.03C29.2771 117.378 30.3694 118.47 31.7169 118.47H52.8614C54.2089 118.47 55.3012 117.378 55.3012 116.03C55.3012 114.683 54.2089 113.59 52.8614 113.59Z" fill="#B4DAFF"/>
                                <path d="M67.5 124.163H31.7169C30.3694 124.163 29.2771 125.255 29.2771 126.602C29.2771 127.95 30.3694 129.042 31.7169 129.042H67.5C68.8474 129.042 69.9398 127.95 69.9398 126.602C69.9398 125.255 68.8474 124.163 67.5 124.163Z" fill="#DFEAFB"/>
                                <g filter="url(#filter0_d_1185_948)">
                                    <path d="M95.9638 16H26.024C23.7783 16 21.9578 17.8205 21.9578 20.0663V44.4639C21.9578 46.7096 23.7783 48.5301 26.024 48.5301H95.9638C98.2095 48.5301 100.03 46.7096 100.03 44.4639V20.0663C100.03 17.8205 98.2095 16 95.9638 16Z" fill="#1485FD"/>
                                </g>
                                <path d="M52.8614 24.9458H31.7169C30.3694 24.9458 29.2771 26.0381 29.2771 27.3856C29.2771 28.733 30.3694 29.8253 31.7169 29.8253H52.8614C54.2089 29.8253 55.3012 28.733 55.3012 27.3856C55.3012 26.0381 54.2089 24.9458 52.8614 24.9458Z" fill="#B4DAFF"/>
                                <path d="M67.5 35.5181H31.7169C30.3694 35.5181 29.2771 36.6104 29.2771 37.9578C29.2771 39.3053 30.3694 40.3976 31.7169 40.3976H67.5C68.8474 40.3976 69.9398 39.3053 69.9398 37.9578C69.9398 36.6104 68.8474 35.5181 67.5 35.5181Z" fill="white"/>
                                <defs>
                                    <filter id="filter0_d_1185_948" x="1.95776" y="0" width="118.072" height="72.5302" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dy="4"/>
                                    <feGaussianBlur stdDeviation="10"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.180392 0 0 0 0 0.521569 0 0 0 0 0.92549 0 0 0 0.17 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1185_948"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1185_948" result="shape"/>
                                    </filter>
                                    <linearGradient id="paint0_linear_1185_948" x1="60.994" y1="22.506" x2="60.994" y2="144.494" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#E3ECFA"/>
                                        <stop offset="1" stop-color="#DAE7FF"/>
                                    </linearGradient>
                                </defs>
                            </svg>
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


            if(response.data.chart) {
                $("#draw").html(graphDraw);
                $('#block-withdraw').html('<canvas id="financesChart" height="285"></canvas>');
                let labels = [...response.data.chart.labels];
                let withdraw = [...response.data.chart.withdrawal.values];
                let series = [...response.data.chart.income.values];
                barGraph(series, labels, withdraw);
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
        url: financesResumeUrl + "/blockeds?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
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
    let ticket, commission, chargebacks, transactions = '';   
    $('#finance-card .onPreLoad *').remove();
    
    $("#finance-commission,#finance-ticket,#finance-chargebacks,#finance-transactions").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: financesResumeUrl + "/resume?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            
            transactions = `
                <span class="title">N de transações</span>
                <div class="d-flex">
                    <strong class="number">
                        <span>${response.data.transactions == undefined ? 0 : response.data.transactions}</span>
                    </strong>
                </div>
            `;
            
            ticket = `
                <span class="title">Ticket Médio</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${response.data.average_ticket == undefined ? '0,00' : removeMoneyCurrency(response.data.average_ticket)}</strong>
                </div>
            `;
            commission = `
                <span class="title">Comissão total</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${response.data.comission == undefined ? '0,00' : removeMoneyCurrency(response.data.comission)}</strong>
                </div>
            `;

            chargebacks = `
                <span class="title">Total em Chargebacks</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number"><span class="bold">${response.data.chargeback == undefined ? '0,00' : removeMoneyCurrency(response.data.chargeback)}</span></strong>
                </div>
            `;
            
            $("#finance-commission").html(commission);
            $("#finance-ticket").html(ticket);
            $("#finance-chargebacks").html(chargebacks);
            $("#finance-transactions").html(transactions);
        }
    });
    
}

function onCommission() {
    let infoComission = '';   
    $('#info-commission .onPreLoad *').remove();
    $("#info-commission").prepend(skeLoad);

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
                            <strong>${removeMoneyCurrency(response.data.total)}</strong>
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
                        <div class="${response.data.total !== 'R$ 0,00' ?'new-finance-graph' : ''  }"></div>
                    </div>
                </section>
            `;           
            $("#info-commission").html(infoComission);

            if( response.data.total !== 'R$ 0,00' ) {
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
        url: financesResumeUrl + "/pendings?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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
        url: financesResumeUrl + "/cashbacks?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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
    // $('input[name="daterange"]').daterangepicker(
    //     {
    //         startDate: moment().subtract(30, "days"),
    //         endDate: moment(),
    //         opens: "left",
    //         maxDate: moment().endOf("day"),
    //         alwaysShowCalendar: true,
    //         showCustomRangeLabel: "Customizado",
    //         autoUpdateInput: true,
    //         locale: {
    //             locale: "pt-br",
    //             format: "DD/MM/YYYY",
    //             applyLabel: "Aplicar",
    //             cancelLabel: "Limpar",
    //             fromLabel: "De",
    //             toLabel: "Até",
    //             customRangeLabel: "Customizado",
    //             weekLabel: "W",
    //             daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
    //             monthNames: [
    //                 "Janeiro",
    //                 "Fevereiro",
    //                 "Março",
    //                 "Abril",
    //                 "Maio",
    //                 "Junho",
    //                 "Julho",
    //                 "Agosto",
    //                 "Setembro",
    //                 "Outubro",
    //                 "Novembro",
    //                 "Dezembro",
    //             ],
    //             firstDay: 0,
    //         },
    //         ranges: {
    //             Hoje: [moment(), moment()],
    //             Ontem: [
    //                 moment().subtract(1, "days"),
    //                 moment().subtract(1, "days"),
    //             ],
    //             "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
    //             "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
    //             "Este mês": [
    //                 moment().startOf("month"),
    //                 moment().endOf("month"),
    //             ],
    //             "Mês passado": [
    //                 moment().subtract(1, "month").startOf("month"),
    //                 moment().subtract(1, "month").endOf("month"),
    //             ],
    //         },
    //     },
    //     function (start, end) {
    //         startDate = start.format("YYYY-MM-DD");
    //         endDate = end.format("YYYY-MM-DD");
            
            
    //         $('.onPreLoad *').remove();
    //         $('.onPreLoad').html(skeLoad);
            
    //         updateReports();
    //     }
    // );
    
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
                console.log('a', normalize);
            } else {
                $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
                $('input[name="daterange"]').val(`${startDate}-${endDate}`);
                console.log('b');
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
        updateStorage({company: $(this).val()})
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
                if(!sessionStorage.info) {
                    return;
                }                
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
                        barThickness: 30,
                    }, 
                    {
                        label: 'Saques',
                        data: withdraw,
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
                        // beginAtZero: true,
                        // min: 0,
                        // max: 100000,
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