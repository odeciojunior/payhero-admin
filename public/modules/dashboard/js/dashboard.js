$(document).ready(function () {

    let levelInfo = {
        1: {
            name: 'Aventureiro',
            title: 'Pronto para começar?',
            icon: '/modules/global/adminremark/assets/images/nivel-1.png',
            message: 'Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação Canis Major, onde brilha a maior estrela do universo.',
            billedStart: '0',
            messageStart: '0',
            billedStop: '100000',
            messageStop: '100K',
        },
        2: {
            name: 'Viajante Espacial',
            title: 'Nível 2',
            icon: '/modules/global/adminremark/assets/images/nivel-2.png',
            message: 'Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação Canis Major, onde brilha a maior estrela do universo.',
            billedStart: '100000',
            messageStart: '100K',
            billedStop: '1000000',
            messageStop: '1M',
        },
        3: {
            name: 'Conquistador',
            title: 'Nível 3',
            icon: '/modules/global/adminremark/assets/images/nivel-3.png',
            message: 'Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação Canis Major, onde brilha a maior estrela do universo.',
            billedStart: '1000000',
            messageStart: '1M',
            billedStop: '10000000',
            messageStop: '10M',
        },
        4: {
            name: 'Colonizador',
            title: 'Nível 4',
            icon: '/modules/global/adminremark/assets/images/nivel-4.png',
            message: 'Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação Canis Major, onde brilha a maior estrela do universo.',
            billedStart: '10000000',
            unityStart: '10M',
            billedStop: '50000000',
            unityStop: '50M',
        },
        5: {
            name: 'Capitão Galáctico',
            title: 'Nível 5',
            icon: '/modules/global/adminremark/assets/images/nivel-5.png',
            message: 'Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação Canis Major, onde brilha a maior estrela do universo.',
            billedStart: '50000000',
            unityStart: '50M',
            billedStop: '100000000',
            unityStop: '100M',
        },
        6: {
            name: 'Sirius Major',
            title: 'Nível 6',
            icon: '/modules/global/adminremark/assets/images/nivel-6.png',
            message: 'Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação Canis Major, onde brilha a maior estrela do universo.',
            billedStart: '100000000',
            unityStart: '100M',
            billedStop: '500000000',
            unityStop: '500M',
        },
    };

    let scoreInfo = {
        0: {
            textColor: 'text-color-red',
            bgColor: 'bg-color-red',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-red">mediano0</span>.'
        },
        1: {
            textColor: 'text-color-red',
            bgColor: 'bg-color-red',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-red">mediano1</span>.'
        },
        2: {
            textColor: 'text-color-orange',
            bgColor: 'bg-color-orange',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-orange">mediano2</span>.'
        },
        3: {
            textColor: 'text-color-orange',
            bgColor: 'bg-color-orange',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-orange">mediano3</span>.'
        },
        4: {
            textColor: 'text-color-yellow',
            bgColor: 'bg-color-yellow',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-yellow">mediano4</span>.'
        },
        5: {
            textColor: 'text-color-yellow',
            bgColor: 'bg-color-yellow',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-yellow">mediano5</span>.'
        },
        6: {
            textColor: 'text-color-green',
            bgColor: 'bg-color-green',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-green">mediano6</span>.'
        },
        7: {
            textColor: 'text-color-green',
            bgColor: 'bg-color-green',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-green">mediano7</span>.'
        },
        8: {
            textColor: 'text-color-dark-green',
            bgColor: 'bg-color-dark-green',
            description: 'Sua conta precisa de atenção. <br>Seu desempenho é <span class="text-color-dark-green">mediano8</span>.'
        },
        9: {
            textColor: 'text-color-dark-green',
            bgColor: 'bg-color-dark-green',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-dark-green">mediano9</span>.'
        },
        10: {
            textColor: 'text-color-dark-green',
            bgColor: 'bg-color-dark-green',
            description: 'Sua conta precisa de atenção. <br> Seu desempenho é <span class="text-color-dark-green">mediano10</span>.'
        },
    }

    getProjects();

    function updateChart() {
        $('#scoreLineToMonth').html('')
        loadingOnChart('#chart-loading');

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-chart-data`,
            dataType: "json",
            data: {
                company: $('#company').val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnChartRemove('#chart-loading');
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                getChart(response)
                loadingOnChartRemove('#chart-loading');
            }
        });
    }

    function getChart(chartData) {

        let haveData = 0;

        chartData.value_data.forEach(function(elem, index){
            if(elem) haveData += parseInt(elem)
        });

        if (haveData > 0) {
            var scoreChart = function scoreChart(id, labelList, series1List) {
                    var scoreChart = new Chartist.Line("#" + id, {
                        labels: labelList,
                        series: [series1List],
                    }, {
                        lineSmooth: Chartist.Interpolation.simple({
                            divisor: 2
                        }),
                        showPoint: false,
                        showLine: false,
                        showArea: true,
                        fullWidth: true,
                        chartPadding: {
                            right: 50,
                            left: 20,
                            top: 30,
                            button: 20
                        },
                        axisX: {
                            showGrid: false,
                            labelInterpolationFnc: function (value) {
                                return value;
                            }
                        },
                        axisY: {
                            labelInterpolationFnc: function labelInterpolationFnc(value) {
                               let str = parseInt(value)

                                if (str > 0) {
                                    str = str / 1e3 + "K"
                                } else {
                                    str = "0.00"
                                }

                                return str;
                            },
                            scaleMinSpace: 40,
                        },
                        low: 0,
                        height: 260,
                    });
                    scoreChart.on("created", function (data) {
                        var defs = data.svg.querySelector("defs") || data.svg.elem("defs"),
                            filter = (data.svg.width(), data.svg.height(), defs.elem("filter", {
                                x: 0, y: "-10%", id: "shadow" + id
                            }, "", !0));
                        return filter.elem("feGaussianBlur", {
                            in: "SourceAlpha", stdDeviation: "800", result: "offsetBlur"
                        }), filter.elem("feOffset", {
                            dx: "0", dy: "800"
                        }), filter.elem("feBlend", {
                            in: "SourceGraphic", mode: "multiply"
                        }), defs;
                    }).on("draw", function (data) {
                        "line" === data.type ? data.element.attr({
                            filter: "url(#shadow" + id + ")"
                        }) : "point" === data.type && new Chartist.Svg(data.element._node.parentNode).elem("line", {
                            x1: data.x, y1: data.y, x2: data.x + .01, y2: data.y, class: "ct-point-content"
                        }), "line" !== data.type && "area" != data.type || data.element.animate({
                            d: {
                                begin: 1e3 * data.index,
                                dur: 1e3,
                                from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                                to: data.path.clone().stringify(),
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    });

                    $('#not-empty-sale').show()
                },
                labelList = chartData.label_list,
                totalSalesData = {value: chartData.value_data}
            createChart = function createChart() {
                scoreChart("scoreLineToMonth", labelList, totalSalesData);
            };

            $('#empty-sale').fadeOut()
            createChart();
        } else {
            $('#empty-sale').fadeIn()
            $('#scoreLineToMonth').html('')
        }

    }

    $("#company").on("change", function () {
        updateValues();
        updateChart();
    });
    let userAccepted = true;

    function getDataDashboard() {
        $.ajax({
            method: "GET",
            url: `/api/dashboard${window.location.search}`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'appliation/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                if (!isEmpty(data.companies)) {
                    for (let i = 0; i < data.companies.length; i++) {
                        if (data.companies[i].company_type == '1') {
                            $('#company').append('<option value="' + data.companies[i].id_code + '">Pessoa física</option>')
                        } else {
                            $('#company').append('<option value="' + data.companies[i].id_code + '">' + data.companies[i].fantasy_name + '</option>')
                        }
                    }


                    $(".content-error").hide();
                    $('#company-select').show();

                    updateValues();
                    updateChart();
                    updatePerformace();
                    updateAccountHealth();
                } else {
                    $(".content-error").show();
                    $('#company-select, .page-content').hide();
                    loadingOnScreenRemove();
                }
            }
        });
    }

    function updateValues() {

        loadOnAnyEllipsis('.text-money, .update-text, .text-circle', false, {
            styles: {
                container: {
                    minHeight: '30px',
                    width: '30px',
                    height: 'auto',
                    margin: 'auto'
                },
                loader: {
                    width: '30px',
                    height: '30px',
                    borderWidth: '6px'
                },

            }
        });

        loadingOnChart('#chart-loading');

        $('.circle strong').addClass('loaded')

        $.ajax({
            method: "POST",
            url: "/api/dashboard/getvalues",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {company: $('#company').val()},
            error: function error(response) {
                loadOnAnyEllipsis('.text-money, .update-text, .text-circle', true)
                loadingOnScreenRemove();

                errorAjaxResponse(response);
            },
            success: function success(data) {

                $(".moeda").html(data.currency);
                $("#pending_money").html(data.pending_balance);
                $("#available_money").html(data.available_balance);
                $("#total_money").html(data.total_balance);
                $("#today_money").html(data.today_balance);

                $('#total_sales_approved').text(data.total_sales_approved);
                $('#total_sales_chargeback').text(data.total_sales_chargeback);

                let title = "Valor incluindo o saldo bloqueado de R$ " + data.blocked_balance_total;
                // if(data.blocked_balance_invite !== "0,00"){
                //     title += "\ne saldo bloqueado referente à convites de R$ " + data.blocked_balance_invite;
                // }

                $('#info-total-balance').attr('title', title).tooltip({placement: 'bottom'});

                //--updateTrackings(data.trackings);
                //--updateChargeback(data.chargeback_tax);
                //--updateTickets(data.tickets);

                loadOnAnyEllipsis('.text-money, .update-text, .text-circle', true)
                //loadingOnScreenRemove();
            }
        });
    }

    function  nextPerformace() {
        //alert('nextPerformace');;
        setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-loading'); }, 5000);
        $(".sirius-performace .card-indicators > .active").on("click", function () {
            //$('.sirius-account > .card').html('');
            //loadingOnAccountsHealth('.sirius-account > .card');
            alert('nextPerformace');
            loadingOnAccountsHealth('.sirius-performace > .card');
            //$('.sirius-performace > .card').toggle();
            let card = $(this).data('slide-to');
            switch(card) {
                case 1:
                    updatePerformace();
                    break;
                case 2:
                    //alert('nextPerformace2');
                    //updatePerformace();
                    break;
                default:
            }
        });
    }

    function updatePerformace() {

        loadOnAnyEllipsis('.load', false, {
            styles: {
                container: {
                    minHeight: '30px',
                    width: '30px',
                    height: 'auto',
                    margin: 'auto'
                },
                loader: {
                    width: '30px',
                    height: '30px',
                    borderWidth: '6px'
                },

            }
        });

        //--loadingOnChart('#chart-loading');

        //$('.circle strong').addClass('loaded');
        $('#achievements .achievements-item').addClass('opacity-3');
        $('.task .task-icon').removeClass('o-checkmark-1');
        $('.task .task-icon').removeClass('.task-icon-checked');

        $.ajax({
            method: "POST",
            url: "/api/dashboard/get-performace",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {company: $('#company').val()},
            error: function error(response) {
                loadOnAnyEllipsis('.load', true)
                //loadingOnScreenRemove();

                errorAjaxResponse(response);
            },
            success: function success(data) {

                let currentLevel = levelInfo[data.level];
                $("#level-icon").html('').html(`<img src="${currentLevel.icon}" alt="">`);
                $("#level-description").html('').html(`<span class="mb-1">${currentLevel.name}</span>`);
                $("#level").html('').html(`<span>${currentLevel.title}</span>`);

                updateAchievements(data.achievements);

                if ( data.level ===  1 || data.level ===2){
                    updateTasks(data.level, data.tasks);
                }

                if ( data.level > 1){
                    $('#cashback-container #cashback-container-money').text(`${data.money_cashbac}`);
                    $("#cashback").show();
                }

                //updateProgressBar(data.progress);
                updateProgressBar(data.progress_money, currentLevel);
                updateBenefits(data.level, data.benefits);


                $(".moeda").html(data.currency);
                //--$("#pending_money").html(data.pending_balance);
                //--$("#available_money").html(data.available_balance);
                //--$("#total_money").html(data.total_balance);
                //--$("#today_money").html(data.today_balance);

                //--$('#total_sales_approved').text(data.total_sales_approved);
                //--$('#total_sales_chargeback').text(data.total_sales_chargeback);

                //--let title = "Valor incluindo o saldo bloqueado de R$ " + data.blocked_balance_total;
                // if(data.blocked_balance_invite !== "0,00"){
                //     title += "\ne saldo bloqueado referente à convites de R$ " + data.blocked_balance_invite;
                // }

                //--$('#info-total-balance').attr('title', title).tooltip({placement: 'bottom'});

                //--updateTrackings(data.trackings);
                // updateChargeback(data.chargeback_tax);
                //--updateTickets(data.tickets);

                loadOnAnyEllipsis('.load', true);
                nextPerformace();
                //alert("depois do next");
                //loadingOnScreenRemove();
            }
        });
    }

    function updateAchievements(data) {

        if (!isEmpty(data)) {
            $.each(data, function (index, value) {
                //console.log(index);
                var i = index + 1;
                $(`#achievements #achievements-item-${i}`).removeClass('opacity-3');
            });

            $('#achievements').show();
        } else {
            $('#achievements .achievements-item').addClass('opacity-3');
        }
    }

    function updateTasks(level, data) {
        $('#tasks').html('');

        if (!isEmpty(data)) {

            $.each(data, function (index, value) {

                let item = `<div class="d-flex justify-content-start align-items-center align-self-start task">
                                 <span class="task-icon ${value.status === 1 ? 'o-checkmark-1 task-icon-checked' : ''} d-flex justify-content-around align-items-center"></span>
                                 <p class="m-0 ${value.status === 1 ? 'task-description-checked' : ''} ">${value.task}</p>
                            </div>`;
                $('#tasks').append(item);
            });

            if ( level === 1){
                $("#tasks").height() > 140 ? $("#tasks").css({'max-height': '130px', 'overflow-y': 'scroll'}) : $("#tasks").css({'max-height': '130px', 'overflow-y': 'hidden'});
            }
            else {
                $("#tasks").height() > 70 ? $("#tasks").css({'max-height': '65px', 'overflow-y': 'scroll'}) : $("#tasks").css({'max-height': '65px', 'overflow-y': 'hidden'});
            }

            $('#tasks').show();
        } else {
            $('#tasks').hide();
        }
    }

    function updateCashback(money_cashback) {
        $('#cashback-container #cashback-container-money').text(`${money_cashback}`);
        $("#cashback").show();
    }

    function updateProgressBar(progress_money, currentLevel) {
        $("#progess-bar-1").css({'width': '0%', 'padding-left': '0px'});
        $("#progess-bar-2").css({'width': '100%', 'border-radius': '10px 10px 10px 10px'});

        $('#progess-1').text(`${currentLevel.messageStart}`);
        $('#progess-2').text(`${currentLevel.messageStop}`);

        var money = parseFloat(progress_money.replace('.','').replace(',','.'));

        //alert(progress_money + "  -  " + money);
        let percentage = (money * 100)/currentLevel.billedStop;
        //alert(percentage);

        if (percentage > 0) {
            $('#progess-bar-1').text(`R$ ${progress_money}`);
            $("#progess-bar-1").css({'width': `${percentage}%`, 'padding-right': '8px' });
            $("#progess-bar-2").css({'width': `${100 - percentage}%`, 'border-radius': '0px 10px 10px 0px'});
        }
    }

    function updateBenefits(level, data) {
        $('#benefits-container').html('');

        if (!isEmpty(data)) {

            $.each(data, function (index, value) {

                let item = `<div class="mb-10 d-flex justify-content-start align-items-center align-self-start benefit">
                                 <span class="benefit-button ${value.status === 1 ? 'benefit-button-checked' : ''} d-flex justify-content-around align-items-center">${value.card}</span>
                                 <p class="m-0">${value.benefit}</p>
                            </div>`;
                $('#benefits-container').append(item);
            });

            if ( level === 2){
                $("#benefits-container").height() > 83 ? $("#benefits-container").css({'max-height': '80px', 'overflow-y': 'scroll'}) : $("#benefits-container").css({'max-height': '80px', 'overflow-y': 'hidden'});
            }
            else {
                $("#benefits-container").height() > 122 ? $("#benefits-container").css({'max-height': '120px', 'overflow-y': 'scroll'}) : $("#benefits-container").css({'max-height': '120px', 'overflow-y': 'hidden'});
            }

        }
    }

    function  nextCard() {
        setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-loading'); }, 500);
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

    function updateAccountHealth() {
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
                //loadingOnScreenRemove();
                loadingOnAccountsHealthRemove('.sirius-loading');
                errorAjaxResponse(response);
            },
            success: function success(data) {

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
                                    <i class="o-angle-down-1 control-prev"></i>
                                    <i class="o-angle-down-1 control-next active" data-slide-to="2"></i>
                                </ol>
                        </div>
                        <div class="card-body pt-0 d-flex flex-column justify-content-start align-items-start account-health">
                            <div id="" class="d-flex flex-row justify-content-start align-items-start align-self-start" >
                                <canvas id="account-health-chart-gauge" class="mr-15"></canvas>
                                <div class="mt-15 d-flex flex-column justify-content-center align-self-center">
                                    <span id="account-health-note"><span class="${scoreInfo[Math.floor(data.account_score)].textColor}">${data.account_score}</span>/10</span>
                                    <p id="account-health-description">${scoreInfo[Math.floor(data.account_score)].description}</p>
                                </div>
                            </div>
                            <div class="hr-horizontal mt-5 d-flex justify-content-center align-items-center align-self-center"></div>
                            <div id="card-notes" class="mt-10 d-flex flex-row flex-nowrap justify-content-around align-items-stretch align-self-stretch" >

                                <div class="d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch">
                                    <div id="account-health-note-chargebacks" class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                        <span class="mr-10 ${scoreInfo[Math.floor(data.chargeback_score)].bgColor} account-health-note-circle"></span>
                                        <span class="account-health-note">${data.chargeback_score}</span>
                                    </div>
                                    <span class="account-health-note-description">Chargebacks</span>
                                </div>

                                <div class="hr-vertical d-flex justify-content-center align-items-center align-self-center"></div>

                                <div class="d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch">
                                    <div id="account-health-note-attendance" class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                        <span class="mr-10 ${scoreInfo[Math.floor(data.attendance_score)].bgColor} account-health-note-circle"></span>
                                        <span class="account-health-note">${data.attendance_score}</span>
                                    </div>
                                    <span class="account-health-note-description">Atendimento</span>
                                </div>

                                <div class="hr-vertical d-flex justify-content-center align-items-center align-self-center"></div>

                                <div class="d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch">
                                    <div id="account-health-note-tracking" class="d-flex flex-row flex-nowrap justify-content-center align-items-center align-self-center">
                                        <span class="mr-10 ${scoreInfo[Math.floor(data.tracking_score)].bgColor} account-health-note-circle"></span>
                                        <span class="account-health-note">${data.tracking_score}</span>
                                    </div>
                                    <span class="account-health-note-description">Cod. Rastreio</span>
                                </div>
                            </div>
                            <div class="sirius-account-loading"></div>
                        </div>
                `;

                //<span id="account-health-chart-gauge" class="mr-15"><img src="/modules/global/adminremark/assets/images/temp.png"></span>
                $('.sirius-account > .card').append(item);

                //loadingOnAccountsHealthRemove('.sirius-loading');

                nextCard();
                updateGauge(data.account_score);
            }
        });
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
                {strokeStyle: "#FE330A", min: 0, max: 2, height: 1.3},
                {strokeStyle: "#FDAD00", min: 2, max: 4, height: 1.3},
                {strokeStyle: "#F2CB0A", min: 4, max: 6, height: 1.3},
                {strokeStyle: "#9FCC00", min: 6, max: 8, height: 1.3},
                {strokeStyle: "#177401", min: 8, max: 10, height: 1.3}
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
        loadingOnAccountsHealth('.sirius-account > .card');

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
                //loadingOnScreenRemove();
                loadingOnAccountsHealthRemove('.sirius-loading');
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
                            <div id="" class="d-flex flex-row justify-content-start align-items-center align-self-start">
                                <span class="mr-10 ${scoreInfo[Math.floor(data.chargeback_score)].bgColor} account-health-note-circle"></span>
                                <span class="account-chargeback-tax ${scoreInfo[Math.floor(data.chargeback_score)].textColor} ">${parseFloat(data.chargeback_rate).toFixed(2)}%</span>
                            </div>
                            <div id="card-notes" class="mt-10 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start" >
                                <div class="d-flex mr-60 flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.total_sales_approved}</span>
                                        <span class="account-health-note-description">Vendas no cartão</span>
                                </div>
                                <div class="d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.total_sales_chargeback}</span>
                                        <span class="account-health-note-description">Chargebacks</span>
                                </div>
                            </div>
                            <div class="hr-horizontal mt-30 d-flex justify-content-start align-items-start align-self-start"></div>
                            <div class="mt-15 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start" >

                                <a href="" class="tips-chargeback">Dicas para reduzir a taxa de chargebacks <i class="o-arrow-right-1 ml-10 align-items-center"></i></a>
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
                //loadingOnScreenRemove();
                loadingOnAccountsHealthRemove('.sirius-loading');
                errorAjaxResponse(response);
            },
            success: function success(data) {

                let item = `
                        <div
                            class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-chargeback">
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
                        <div class="card-body pt-0 mt-20 d-flex flex-column justify-content-start align-items-start account-chargeback">
                            <div id="" class="d-flex flex-row justify-content-start align-items-center align-self-start">
                                <span class="mr-10 ${scoreInfo[Math.floor(data.attendance_score)].bgColor} account-health-note-circle"></span>
                                <span class="account-chargeback-tax ${scoreInfo[Math.floor(data.attendance_score)].textColor}">${data.attendance_score}</span>
                            </div>
                            <div id="card-notes" class="mt-10 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start" >
                                <div class="d-flex mr-20 flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.open || 0}</span>
                                        <span class="account-health-note-description">Abertos</span>
                                </div>
                                <div class="d-flex mr-20 flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.closed || 0}</span>
                                        <span class="account-health-note-description">Resolvidos</span>
                                </div>

                                <div class="d-flex mr-20 flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.mediation || 0}</span>
                                        <span class="account-health-note-description">Mediação</span>
                                </div>

                                <div class="d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.total}</span>
                                        <span class="account-health-note-description">Total</span>
                                </div>

                            </div>
                            <div class="hr-horizontal mt-30 d-flex justify-content-start align-items-start align-self-start"></div>
                            <div class="mt-15 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start" >

                                <a href="" class="tips-chargeback">Dicas para reduzir a taxa de chargebacks <i class="o-arrow-right-1 ml-10 align-items-center"></i></a>
                            </div>
                            <div class="sirius-account-loading"></div>
                        </div>
                `;

                $('.sirius-account > .card').append(item);
                nextCard();
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
                //loadingOnScreenRemove();
                loadingOnAccountsHealthRemove('.sirius-loading');
                errorAjaxResponse(response);
            },
            success: function success(data) {

                let item = `
                        <div
                            class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 account-chargeback">
                            <div class="font-size-14 gray-600 mr-auto">
                                <span class="ml-0">Códigos de Rastreio</span>
                            </div>
                                <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                                    <li class="" data-slide-to="1"></li>
                                    <li class="" data-slide-to="2"></li>
                                    <li class="" data-slide-to="3"></li>
                                    <li class="active"  data-slide-to="4"></li>
                                    <i class="o-angle-down-1 control-prev active" data-slide-to="3"></i>
                                    <i class="o-angle-down-1 control-next" ></i>
                                </ol>
                        </div>
                        <div class="card-body pt-0 mt-20 d-flex flex-column justify-content-start align-items-start account-chargeback">
                            <div id="" class="d-flex flex-row justify-content-start align-items-center align-self-start">
                                <span class="mr-10 ${scoreInfo[Math.floor(data.tracking_score)].bgColor} account-health-note-circle"></span>
                                <span class="account-chargeback-tax ${scoreInfo[Math.floor(data.tracking_score)].textColor}">${data.tracking_score}</span>
                            </div>
                            <div id="card-notes" class="mt-10 col-12 px-0 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start">
                                <div class="col-6 px-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.average_post_time} dia${data.average_post_time === 1 ? '' : 's'}</span>
                                        <span class="account-health-note-description font-size-12">Tempo médio de postagem</span>
                                </div>
                                <div class="col-6 px-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.oldest_sale} dia${data.oldest_sale === 1 ? '' : 's'}</span>
                                        <span class="account-health-note-description font-size-12">Venda mais antiga sem código</span>
                                </div>

                            </div>


                            <div id="card-notes" class="mt-10 col-12 px-0 d-flex flex-row flex-nowrap justify-content-start align-items-start align-self-start">
                                <div class="col-6 px-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.problem} <small class="account-health-note-description font-size-14">(${data.problem_percentage}%)</small></span>
                                        <span class="account-health-note-description font-size-12">Códigos com problema</span>
                                </div>
                                <div class="col-6 px-0 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-start">
                                        <span class="account-health-note">${data.unknown} <small class="account-health-note-description font-size-14">(${data.problem_percentage}%)</small></span>
                                        <span class="account-health-note-description font-size-12">Códigos não informados</span>
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

    // function updateChargeback(value) {
    //     $('.circle').circleProgress({
    //         size: 125,
    //         startAngle: -Math.PI / 2,
    //         value: value / 100,
    //         fill: {
    //             gradient: ["#F76B1C", "#FA6161"]
    //         }
    //     });
    //
    //     $('.circle strong').addClass('loaded')
    //         .text(parseFloat(value).toFixed(2) + '%');
    // }

    function updateTrackings(trackings) {
        $('#average_post_time').html(trackings.average_post_time + ' dia' + (trackings.average_post_time === 1 ? '' : 's'));
        $('#oldest_sale').html(trackings.oldest_sale + ' dia' + (trackings.oldest_sale === 1 ? '' : 's'));
        $('#problem').html(trackings.problem + ' <small>(' + trackings.problem_percentage + '%)</small>');
        $('#unknown').html(trackings.unknown + ' <small>(' + trackings.unknown_percentage + '%)</small>');
    }

    function updateTickets(data) {
        $('#open-tickets').text(data.open || 0);
        $('#closed-tickets').text(data.closed || 0);
        $('#mediation-tickets').text(data.mediation || 0);
        $('#total-tickets').text(data.total);
    }

    function updateNews(data) {

        $('#carouselNews .carousel-inner').html('');
        $('#carouselNews .carousel-indicators').html('');

        if (!isEmpty(data)) {

            for (let i = 0; i < data.length; i++) {

                let active = i === 0 ? 'active' : '';

                let slide = `<div class="carousel-item ${active}">
                                 <div class="card shadow news-background">
                                     <div class="card-body p-md-60 d-flex flex-column justify-content-center" style="height: 354px">
                                         <h1 class="news-title">${data[i].title}</h1>
                                         <div class="news-content">${data[i].content}</div>
                                     </div>
                                 </div>
                             </div>`;

                let indicator = `<li data-target="#carouselNews" data-slide-to="${i}" class="${active}"></li>`;

                $('#carouselNews .carousel-inner').append(slide);
                $('#carouselNews .carousel-indicators').append(indicator);
            }

            if (data.length === 1) {
                $('#carouselNews .carousel-indicators').hide();
                $('#carouselNews .carousel-control-prev, #carouselNews .carousel-control-next').hide();
            } else {
                $('#carouselNews .carousel-indicators').show();
                $('#carouselNews .carousel-control-prev, #carouselNews .carousel-control-next').show();
            }

            $('#news-col').show();
        } else {
            $('#news-col').hide();
        }
    }

    function updateReleases(data) {
        $('#releases-div').html('');

        if (!isEmpty(data)) {
            $.each(data, function (index, value) {
                let item = `<div class="d-flex align-items-center my-15">
                                <div class="release-progress" id="${index}">
                                    <strong>${value.progress}%</strong>
                                </div>
                                <span class="ml-2">${value.release}</span>
                            </div>`;
                $('#releases-div').append(item);

                updateReleasesProgress(index, value.progress);
            });

            $('#releases-col').show();
        } else {
            $('#releases-col').hide();
        }
    }

    // function verifyPendingData() {
    //     $.ajax({
    //         method: "GET",
    //         url: "/api/dashboard/verifypendingdata",
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         data: {company: $('#company').val()},
    //         error: function error(response) {
    //             errorAjaxResponse(response);
    //         },
    //         success: function success(response) {
    //             let companies = response.companies;
    //             if ((!isEmpty(companies)) || response.pending_user_data) {
    //                 if (response.pending_user_data) {
    //                     $('.tr-pending-profile').show();
    //                 } else {
    //                     $('.tr-pending-profile').hide();
    //                 }
    //                 for (let company of companies) {
    //                     $('.table-pending-data-body').append(`
    //                             <tr>
    //                                 <td style='width:2px;' class='text-center'>
    //                                 <span class="status status-lg status-away"></span>
    //                                 </td>
    //                                 <td class='text-left'>
    //                                     Empresas > ${company.fantasy_name}
    //                                 </td>
    //                                 <td class='text-center'>
    //                                     <a class='btn' style='color:darkorange;' href='/companies/${company.id_code}/edit?type=${company.type}' target='_blank'><b><i class="fa fa-pencil-square-o mr-2" aria-hidden="true"></i>Atualizar</b></a>
    //                                 </td>
    //                             </tr>
    //                         `);
    //                 }
    //                 if (!userAccepted) {
    //                     $('#modal-peding-data').modal('hide');
    //                 } else {
    //                     $('#modal-peding-data').modal('show');
    //                 }
    //             }
    //         }
    //     });
    // }
    function updateReleasesProgress(id, value) {

        let circle = $('#' + id);

        let color = '';
        switch (true) {
            case value <= 33:
                color = '#FFA040';
                break;
            case value > 33 && value <= 66:
                color = '#FF6F00';
                break;
            default:
                color = '#C43E00';
                break;
        }

        circle.circleProgress({
            size: 55,
            startAngle: -Math.PI / 2,
            thickness: 6,
            value: value / 100,
            fill: {
                color: color,
            }
        });
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });

    function getProjects() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: '/api/projects?select=true',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();

                    getDataDashboard();

                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }

                loadingOnScreenRemove();
            }
        });
    }
});
