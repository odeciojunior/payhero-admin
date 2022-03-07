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


    function  nextCard() {
        //setTimeout(function(){loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading '); }, 500);
        loadingOnAccountsHealthRemove('.sirius-account > .card  .sirius-loading ');
        $(".sirius-account .card-indicators > .active").on("click", function () {
            $('.sirius-account > .card').html('');
            loadingOnAccountsHealth('.sirius-account > .card');
            let card = $(this).data('slide-to');
            switch(card) {
                case 1:
                    updateAccountHealth();
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
                company: $('#company').val(),
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
                    //alert(data.account_score);
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
                company: $('#company').val(),
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

                            <div class="d-flex flex-row flex-nowrap" style="width: 100%;">
                                <div class="col-6 p-0 d-flex flex-row justify-content-start align-items-center align-self-start" >
                                    <span class="mr-10 ${scoreInfo[Math.floor(data.chargeback_score)].bgColor} account-health-note-circle"></span>
                                    <span class="account-tax ${scoreInfo[Math.floor(data.chargeback_score)].textColor}">${data.chargeback_score}</span>
                                </div>
                                <div class="col-6 p-0" >
                                    <div class="d-flex flex-row flex-nowrap justify-content-between">
                                        <span class="font-size-12 gray-600">Últimos 140 dias</span>
                                        <span class="font-size-12 gray-600">20 dias</span>
                                    </div>
                                    <img class="col-12 p-0" src="/modules/global/img/timeline-chargeback.svg" style="max-width: 100%;height: 15px;">
                                </div>
                            </div>

                            <div class="mt-10 d-flex flex-row flex-nowrap" style="width: 100%;">
                                <div class="col-6 p-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start" >
                                    <span class="account-health-note">${data.total_sales_approved}</span>
                                    <span class="account-health-note-description">Vendas no cartão</span>
                                </div>
                                <div class="col-6 p-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start" >
                                    <span class="account-health-note">${data.total_sales_chargeback}
                                        <!-- <span class="account-tax ${scoreInfo[Math.floor(data.chargeback_score)].textColor} ">${parseFloat( data.chargeback_rate ).toFixed(2)}%</span> -->
                                    </span>
                                    <span class="account-health-note-description">Chargebacks</span>
                                </div>
                            </div>

                            <div class="hr-horizontal mt-25 d-flex justify-content-start align-items-start align-self-start"></div>
                            <div class="mt-10 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start" >
                                <a href="./contestations" class="tips-chargeback">Acesse o painel de Contestações <i class="o-arrow-right-1 ml-10 align-items-center"></i></a>
                            </div>
                            <div class="sirius-account-loading"></div>
                        </div>
                `;

                $('.sirius-account > .card').append(item);

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
                company: $('#company').val(),
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
                company: $('#company').val(),
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
