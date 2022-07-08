$(document).ready(function () {
    let scoreInfo = {
        0: {
            textColor: 'text-color-red',
            bgColor: 'bg-color-red',
            description: '<span class="text-color-red">Alerta:</span> regularize sua situação para evitar bloqueios.'
        },
        1: {
            textColor: 'text-color-red',
            bgColor: 'bg-color-red',
            description: '<span class="text-color-red">Alerta:</span> regularize sua situação para evitar bloqueios.'
        },
        2: {
            textColor: 'text-color-orange',
            bgColor: 'bg-color-orange',
            description: '<span class="text-color-orange">Atenção:</span> a saúde da sua conta está muito baixa.'
        },
        3: {
            textColor: 'text-color-orange',
            bgColor: 'bg-color-orange',
            description: '<span class="text-color-orange">Atenção:</span> a saúde da sua conta está muito baixa.'
        },
        4: {
            textColor: 'text-color-yellow',
            bgColor: 'bg-color-yellow',
            description: 'Seu desempenho é <span class="text-color-yellow">regular</span>. Fique de olho!'
        },
        5: {
            textColor: 'text-color-yellow',
            bgColor: 'bg-color-yellow',
            description: 'Seu desempenho é <span class="text-color-yellow">regular</span>. Fique de olho!'
        },
        6: {
            textColor: 'text-color-green',
            bgColor: 'bg-color-green',
            description: 'Continue assim. Seu desempenho é <span class="text-color-green">bom</span>!'
        },
        7: {
            textColor: 'text-color-green',
            bgColor: 'bg-color-green',
            description: 'Continue assim. Seu desempenho é <span class="text-color-green">bom</span>!'
        },
        8: {
            textColor: 'text-color-dark-green',
            bgColor: 'bg-color-dark-green',
            description: 'A saúde da sua conta está <span class="text-color-dark-green">excelente</span>. Parabéns!'
        },
        9: {
            textColor: 'text-color-dark-green',
            bgColor: 'bg-color-dark-green',
            description: 'A saúde da sua conta está <span class="text-color-dark-green">excelente</span>. Parabéns!'
        },
        10: {
            textColor: 'text-color-dark-green',
            bgColor: 'bg-color-dark-green',
            description: 'A saúde da sua conta está <span class="text-color-dark-green">excelente</span>. Parabéns!'
        },
    }

    function indexColor(value) {
        if(value <= 1.5) {
            return 'color: #1BCE68;';
            console.log('green');
        }else {
            return 'color: #FF3006;';
            console.log('red');
        }
        return '';
    }


    function  nextCard() {
        //setTimeout(function(){loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading '); }, 500);
        loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading ');
        $(".sirius-account .card-indicators > .active").on("click", function () {
            $('.sirius-account > .card').html('');
            loadingOnAccountsHealth('.sirius-account > .card');
            let card = $(this).data('slide-to');
            switch(card) {
                case 1:
                    window.updateAccountHealth();
                    break;
                case 2:
                    updateChargeback();
                    break;
                case 3:
                    updateAttendance();
                    break;
                case 4:
                    updateTracking();
                    break;
                default:
            }
        });
    }

    window.updateAccountHealth = function () {
        loadingOnAccountsHealth('.sirius-account > .card');

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-account-health`,
            dataType: "json",
            data: {
                company: $('#company-navbar').val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading ');
                errorAjaxResponse(response);
            },
            success: function success(data) {
                if (!data.account_score) {
                    updateEmptyScore();
                }
                else {
                    let item = `
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-health">
                                <div class="font-size-14 gray-600 mr-auto">
                                    <span class="ml-0">Saúde da Conta</span>
                                </div>
                                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                                        <li class="active" data-slide-to="1"></li>
                                        <li class="" data-slide-to="2"></li>
                                        <li class="" data-slide-to="3"></li>
                                        <li class="" data-slide-to="4"></li>
                                        <i class="o-angle-down-1 control-prev active" data-slide-to="4"></i>
                                        <i class="o-angle-down-1 control-next active" data-slide-to="2"></i>
                                    </ol>
                            </div>
                            <div class="card-body pt-0 d-flex flex-column justify-content-start align-items-start account-health">
                                <div id="" class="d-flex flex-row justify-content-start align-items-start align-self-start" >
                                    <canvas id="account-health-chart-gauge" class="mr-15"></canvas>
                                    <div class="mt-15 d-flex flex-column justify-content-center align-self-center">
                                        <span id="account-health-note"><span class="${scoreInfo[Math.floor(data.account_score)].textColor}">${data.account_score >= 1 ? data.account_score : 0}</span>/10</span>
                                        <p id="account-health-description" class="account-health-description">${scoreInfo[Math.floor(data.account_score)].description}</p>
                                    </div>
                                </div>
                                <div class="hr-horizontal mt-5 d-flex justify-content-center align-items-center align-self-center"></div>
                                <div id="card-notes" class="mt-10 d-flex flex-row flex-nowrap justify-content-around align-items-stretch align-self-stretch" >
                                    <div class="d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch">
                                        <div id="account-health-note-chargebacks" class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                            <span class="mr-10 ${scoreInfo[Math.floor(data.chargeback_score)].bgColor} account-health-note-circle"></span>
                                            <span class="account-health-note">${data.chargeback_score ? data.chargeback_score:0}</span>
                                        </div>
                                        <span class="account-health-note-description">Chargebacks</span>
                                    </div>
                                    <div class="hr-vertical d-flex justify-content-center align-items-center align-self-center"></div>
                                    <div class="d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch">
                                        <div id="account-health-note-attendance" class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                            <span class="mr-10 ${scoreInfo[Math.floor(data.attendance_score)].bgColor} account-health-note-circle"></span>
                                            <span class="account-health-note">${data.attendance_score ? data.attendance_score:0}</span>
                                        </div>
                                        <span class="account-health-note-description">Atendimento</span>
                                    </div>
                                    <div class="hr-vertical d-flex justify-content-center align-items-center align-self-center"></div>
                                    <div class="d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch">
                                        <div id="account-health-note-tracking" class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                            <span class="mr-10 ${scoreInfo[Math.floor(data.tracking_score)].bgColor} account-health-note-circle"></span>
                                            <span class="account-health-note">${data.tracking_score ? data.tracking_score:0}</span>
                                        </div>
                                        <span class="account-health-note-description">Cod. Rastreio</span>
                                    </div>
                                </div>
                                <div class="sirius-account-loading"></div>
                            </div>
                    `;

                    $('.sirius-account > .card').append(item);

                    nextCard();
                    updateGauge(data.account_score);
                }

                $(".page.dashboard .sirius-account .sirius-account-health").css({'height': ' 225px'});
            }
        });
    }

    function updateEmptyScore() {
        setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading'); }, 500);
        let item = `
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-health">
                            </div>
                            <div class="card-body pt-0 d-flex flex-column justify-content-start align-items-start empty-score">
                                <div class="d-flex flex-column flex-nowrap justify-content-center align-items-center align-self-center">
                                    <span class="bg-color-blue "></span>
                                    <h4>EM BREVE</h4>
                                    <p>Faça +100 vendas para ativar este recurso.</p>
                                </div>
                                <div class="d-flex flex-row flex-nowrap justify-content-around align-items-stretch align-self-stretch" >
                                    <div class="d-flex flex-column justify-content-between align-items-center align-self-center">
                                        <div class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                            <span class="bg-color-gray empty-cilce"></span>
                                            <span></span>
                                        </div>
                                        <span class="account-health-note-description">Chargebacks</span>
                                    </div>
                                    <div class="hr-vertical d-flex justify-content-center align-items-center align-self-center"></div>
                                    <div class="d-flex flex-column justify-content-between align-items-center align-self-center">
                                        <div class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                            <span class="bg-color-gray empty-cilce"></span>
                                            <span></span>
                                        </div>
                                        <span class="account-health-note-description">Atendimento</span>
                                    </div>
                                    <div class="hr-vertical d-flex justify-content-center align-items-center align-self-center"></div>
                                    <div class="d-flex flex-column justify-content-between align-items-center align-self-center">
                                        <div class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                            <span class="bg-color-gray empty-cilce"></span>
                                            <span></span>
                                        </div>
                                        <span class="account-health-note-description">Cod. Rastreio</span>
                                    </div>
                                </div>
                                <div class="sirius-account-loading"></div>
                            </div>
                    `;

        $('.sirius-account > .card').append(item);
    }

    function  updateGauge(account_score) {
        var opts = {
            angle: 0, // A extensão do arco do medidor
            lineWidth: 0.30, // A espessura da linha
            radiusScale: 0.80, // Raio relativo
            pointer: {
                length: 0.47, // Em relação ao raio de medição
                strokeWidth: 0.035, // A espessura
                color: '#000000' // Cor de preenchimento
            },
            limitMax: true,     // Se for falso, o valor máximo aumenta automaticamente se o valor> maxValue
            limitMin: true,     // If true, the min value of the gauge will be fixed
            colorStart: '#6FADCF',   // Colors
            colorStop: '#FE330A',    // just experiment with them
            strokeColor: '#8FC0DA',  // to see which ones work best for you
            generateGradient: true,
            highDpiSupport: false,     // Suporte de alta resolução
            // renderTicks é opcional
            renderTicks: {
                divisions: 0,
                divWidth: 0.1,
                divLength: 0,
                divColor: '#333333',
                subDivisions: 0,
                subLength: 0,
                subWidth: 10,
                subColor: '#666666'
            },
            staticZones: [
                {strokeStyle: "#FF3006", min: 0, max: 2, height: 1.3},
                {strokeStyle: "#FFAF00", min: 2, max: 4, height: 1.3},
                {strokeStyle: "#F2CC11", min: 4, max: 6, height: 1.3},
                {strokeStyle: "#1BCE68", min: 6, max: 8, height: 1.3},
                {strokeStyle: "#04A74A", min: 8, max: 10, height: 1.3}
            ],

        };
        var target = document.getElementById('account-health-chart-gauge'); // your canvas element
        var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
        gauge.maxValue = 10; // set max gauge value
        gauge.setMinValue(0);  // Prefer setter over gauge.minValue = 0
        gauge.animationSpeed = 64; // set animation speed (32 is default value)
        gauge.set(account_score); // set actual value
    }

    function updateChargeback() {
        //loadingOnAccountsHealth('.sirius-account > .card');

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-account-chargeback`,
            dataType: "json",
            data: {
                company: $('#company-navbar').val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading ');
                errorAjaxResponse(response);
            },
            success: function success(data) {

                let item = `
                        <div
                            class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-chargeback">
                            <div class="font-size-14 gray-600 mr-auto">
                                <span class="ml-0">Chargebacks</span>
                            </div>
                                <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                                    <li class="" data-slide-to="1"></li>
                                    <li class="active" data-slide-to="2"></li>
                                    <li class=""  data-slide-to="3"></li>
                                    <li class=""  data-slide-to="4"></li>
                                    <i class="o-angle-down-1 control-prev active" data-slide-to="1"></i>
                                    <i class="o-angle-down-1 control-next active" data-slide-to="3"></i>
                                </ol>
                        </div>
                        <div class="card-body pt-0 mt-20 d-flex flex-column justify-content-start align-items-start account-chargeback">

                            <div class="d-flex flex-row flex-nowrap justify-content-between" style="width: 100%;">
                                <div class="col-4 p-0 d-flex flex-row justify-content-start align-items-center align-self-start" >
                                    <span class="mr-10 ${scoreInfo[Math.floor(data.chargeback_score)].bgColor} account-health-note-circle"></span>
                                    <span class="account-tax ${scoreInfo[Math.floor(data.chargeback_score)].textColor}">${data.chargeback_score}</span>
                                </div>
                                <div class="col-6 p-0 d-flex flex-row justify-content-end align-items-center" >
                                    <span class="font-size-12 gray-600">Como analisamos</span>

                                    <div class="custom-tooltip" >
                                        <div id="chargeback-custom-tooltip-icon" data-target="chargeback-custom-tooltip-container" style="margin-left: 3px;">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8 0C12.4183 0 16 3.58172 16 8C16 12.4183 12.4183 16 8 16C3.58172 16 0 12.4183 0 8C0 3.58172 3.58172 0 8 0ZM8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1ZM8 11.5C8.41421 11.5 8.75 11.8358 8.75 12.25C8.75 12.6642 8.41421 13 8 13C7.58579 13 7.25 12.6642 7.25 12.25C7.25 11.8358 7.58579 11.5 8 11.5ZM8 3.5C9.38071 3.5 10.5 4.61929 10.5 6C10.5 6.72959 10.1848 7.40774 9.6513 7.8771L9.49667 8.00243L9.27817 8.16553L9.19065 8.23718C9.1348 8.28509 9.08354 8.33373 9.03456 8.38592C8.69627 8.74641 8.5 9.24223 8.5 10C8.5 10.2761 8.27614 10.5 8 10.5C7.72386 10.5 7.5 10.2761 7.5 10C7.5 8.98796 7.79312 8.24747 8.30535 7.70162C8.41649 7.5832 8.53202 7.47988 8.66094 7.37874L8.90761 7.19439L9.02561 7.09468C9.325 6.81435 9.5 6.42206 9.5 6C9.5 5.17157 8.82843 4.5 8 4.5C7.17157 4.5 6.5 5.17157 6.5 6C6.5 6.27614 6.27614 6.5 6 6.5C5.72386 6.5 5.5 6.27614 5.5 6C5.5 4.61929 6.61929 3.5 8 3.5Z" fill="#78838E"/>
                                            </svg>
                                        </div>

                                        <div id="chargeback-custom-tooltip-container" class="custom-tooltip-container mx-2" style="display: none; margin-top: -430px;">
                                            <div class="custom-tooltip-content" style="margin-right: 150px">
                                                <p>
                                                O chargeback acontece quando uma cobrança é contestada pelo titular do cartão de crédito.
                                                </p>

                                                <p>
                                                Analisamos uma janela de 150 dias (5 meses), excluindo os últimos 20 dias* de vendas, para calcular o índice e atribuir uma nota que vai de 0 a 10.
                                                </p>

                                                <p>
                                                O índice exigido pelas regras internacionais das bandeiras é de não exceder 1,5%.
                                                Dentre 1,5% e 3% é considerado alto, podendo sofrer multas por cada chargeback.
                                                Acima de 3%, além das multas, a sua conta poderá ser bloqueada definitivamente.
                                                </p>

                                                <p>
                                                *A exclusão dos últimos 20 dias é para tornar o cálculo mais preciso, uma vez que os chargebacks geralmente aparecem após este período.
                                                </p>

                                                <div class="border border-1 rounded" style="padding: 5px 20px;">
                                                    <div class="d-flex flex-row flex-nowrap justify-content-between">
                                                        <span class="font-size-12 gray-600 font-weight-bold">Últimos 150 dias</span>
                                                        <span class="font-size-12 gray-600 font-weight-bold">20 dias</span>
                                                    </div>
                                                    <img class="col-12 p-0" src="/build/global/img/timeline-chargeback.svg" style="max-width: 100%;height: 15px;">
                                                </div>

                                            </div>
                                            <div class="custom-tooltip-arrow"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mt-10 d-flex flex-row flex-nowrap justify-content-between" style="width: 100%;">
                                <div class="col-4 p-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start" >
                                    <span class="account-health-note">${data.total_sales_approved}</span>
                                    <span class="account-health-note-description">Vendas no cartão</span>
                                </div>
                                <div class="col-3 p-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start" >
                                    <span class="account-health-note">${data.total_sales_chargeback}
                                        <!-- <span class="account-tax ${scoreInfo[Math.floor(data.chargeback_score)].textColor} ">${parseFloat( data.chargeback_rate ).toFixed(2)}%</span> -->
                                    </span>
                                    <span class="account-health-note-description">Chargebacks</span>
                                </div>
                                <div class="col-2 p-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start" >
                                    <span class="account-health-note" style="font-size: 20px; ${indexColor(data.chargeback_rate)}">${parseFloat( data.chargeback_rate ).toFixed(1)}%</span>
                                    <span class="account-health-note-description">Índice</span>
                                </div>
                            </div>

                            <div class="hr-horizontal mt-25 d-flex justify-content-start align-items-start align-self-start"></div>
                            <div class="d-flex flex-row flex-nowrap justify-content-start align-items-center align-self-start" style="height: 100%;" >
                                <a href="./contestations" class="tips-chargeback">Acesse o painel de Contestações <i class="o-arrow-right-1 ml-10 align-items-center"></i></a>
                            </div>
                            <div class="sirius-account-loading"></div>
                        </div>
                `;

                $('.sirius-account > .card').append(item);

                $('#chargeback-custom-tooltip-icon').on({
                    mouseenter: function () {
                        $( "#chargeback-custom-tooltip-container" ).fadeIn();
                    },
                    mouseleave: function () {
                        $( "#chargeback-custom-tooltip-container" ).fadeOut();
                    }
                });

                nextCard();
            }
        });
    }

    function updateAttendance() {
        loadingOnAccountsHealth('.sirius-account > .card');

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-account-attendance`,
            dataType: "json",
            data: {
                company: $('#company-navbar').val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading ');
                errorAjaxResponse(response);
            },
            success: function success(data) {

                let item = `
                        <div
                            class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-attendance">
                            <div class="font-size-14 gray-600 mr-auto">
                                <span class="ml-0">Atendimento</span>
                            </div>
                                <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                                    <li class="" data-slide-to="1"></li>
                                    <li class="" data-slide-to="2"></li>
                                    <li class="active" data-slide-to="3"></li>
                                    <li class=""  data-slide-to="4"></li>
                                    <i class="o-angle-down-1 control-prev active" data-slide-to="2"></i>
                                    <i class="o-angle-down-1 control-next active" data-slide-to="4"></i>
                                </ol>
                        </div>
                        <div class="card-body pt-0 mt-20 d-flex flex-column justify-content-start align-items-start account-attendance">
                            <div class="d-flex flex-row flex-nowrap" style="width: 100%;">
                                <div class="col-6 p-0 d-flex flex-row justify-content-start align-items-center align-self-start" >
                                    <span class="mr-10 ${scoreInfo[Math.floor(data.attendance_score)].bgColor} account-health-note-circle"></span>
                                    <span class="account-tax ${scoreInfo[Math.floor(data.attendance_score)].textColor}">${data.attendance_score}</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-row flex-nowrap justify-content-start align-items-center align-self-center font-size-12 gray-600">
                                    <span class="o-clock-1 mr-5" data-toggle="tooltip" data-original-title="Tempo médio de resposta" style="font-size: 18px;line-height: 18px;-webkit-text-stroke: 1.45px rgba(0, 0, 0, 0.1);"></span> ${data.attendance_average_response_time || 0} hora${data.attendance_average_response_time === 1 ? '' : 's'}
                                </div>
                            </div>
                            <div class="mt-10 p-0 d-flex flex-row flex-wrap" style="height: 100%; width: 100%;">
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.open || 0}</span>
                                    <span class="account-health-note-description">Abertos</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.closed || 0}</span>
                                    <span class="account-health-note-description">Resolvidos</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.mediation || 0}</span>
                                    <span class="account-health-note-description">Mediação</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.total}</span>
                                    <span class="account-health-note-description">Total</span>
                                </div>
                            </div>
                            <div class="sirius-account-loading"></div>
                        </div>
                `;

                $('.sirius-account > .card').append(item);
                nextCard();

                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                });
            }
        });
    }

    function updateTracking() {
        loadingOnAccountsHealth('.sirius-account > .card');

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-account-tracking`,
            dataType: "json",
            data: {
                company: $('#company-navbar').val(), //Storage.getItem('company_default'),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading ');
                errorAjaxResponse(response);
            },
            success: function success(data) {

                let item = `
                        <div
                            class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-tracking">
                            <div class="font-size-14 gray-600 mr-auto">
                                <span class="ml-0">Códigos de Rastreio</span>
                            </div>
                                <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                                    <li class="" data-slide-to="1"></li>
                                    <li class="" data-slide-to="2"></li>
                                    <li class="" data-slide-to="3"></li>
                                    <li class="active"  data-slide-to="4"></li>
                                    <i class="o-angle-down-1 control-prev active" data-slide-to="3"></i>
                                    <i class="o-angle-down-1 control-next active" data-slide-to="1"></i>
                                </ol>
                        </div>
                        <div class="card-body pt-0 mt-20 d-flex flex-column justify-content-start align-items-start account-tracking">
                            <div id="" class="d-flex flex-row justify-content-start align-items-center align-self-start">
                                <span class="mr-10 ${scoreInfo[Math.floor(data.tracking_score)].bgColor} account-health-note-circle"></span>
                                <span class="account-tax ${scoreInfo[Math.floor(data.tracking_score)].textColor}">${data.tracking_score}</span>
                            </div>
                            <div class="mt-10 p-0 d-flex flex-row flex-wrap" style="height: 100%; width: 100%;">
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.average_post_time} dia${data.average_post_time === 1 ? '' : 's'}</span>
                                    <span class="account-health-note-description-tracking">Tempo médio de postagem</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.problem} <small class="account-health-note-description font-size-14">(${data.problem_percentage}%)</small></span>
                                    <span class="account-health-note-description-tracking">Códigos com problema</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.unknown} <small class="account-health-note-description font-size-14">(${data.problem_percentage}%)</small></span>
                                    <span class="account-health-note-description-tracking">Códigos não informados</span>
                                </div>
                                <div class="col-6 pr-0 pl-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                    <span class="account-health-note">${data.tracking_today}</span>
                                    <span class="account-health-note-description-tracking">Códigos informados hoje</span>
                                </div>
                            </div>
                            <div class="sirius-account-loading"></div>
                        </div>
                `;

                $('.sirius-account > .card').append(item);
                nextCard();
            }
        });
    }
});
