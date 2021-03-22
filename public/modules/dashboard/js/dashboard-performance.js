$(document).ready(function () {

    let levelInfo = {
        1: {
            name: 'Aventureiro',
            description: 'Pronto para começar?',
            icon: '/modules/global/adminremark/assets/images/nivel-1.png',
            storytelling: 'Nossa jornada está apenas começando. Você já pode começar a olhar o céu noturno e se imaginar navegando na imensidão do desconhecido, é hora de mirar as estrelas e se preparar para a maior aventura de sua vida empreendedora.',
            billedStart: '0',
            messageStart: '0K',
            billedStop: '100000',
            messageStop: '100K',
        },
        2: {
            name: 'Viajante Espacial',
            description: 'Nível 2',
            icon: '/modules/global/adminremark/assets/images/nivel-2.png',
            storytelling: 'Nosso foguete está saindo da Terra, este momento de fortes emoções foi experimentado por poucos! Quem diria, de tanto olhar para o céu estrelado, hoje você está navegando por ele, rumo à nossa primeira parada: a lua!',
            billedStart: '100000',
            messageStart: '100K',
            billedStop: '1000000',
            messageStop: '1M',
        },
        3: {
            name: 'Conquistador',
            description: 'Nível 3',
            icon: '/modules/global/adminremark/assets/images/nivel-3.png',
            storytelling: 'Nível 3? Você está avançando bem, daqui da lua você já consegue enxergar que a Terra é pequena demais para você. Aproveite a vista, faça pequenos reparos porque ainda temos bastante aventura pela frente e a próxima parada é Marte!',
            billedStart: '1000000',
            messageStart: '1M',
            billedStop: '10000000',
            messageStop: '10M',
        },
        4: {
            name: 'Colonizador',
            description: 'Nível 4',
            icon: '/modules/global/adminremark/assets/images/nivel-4.png',
            storytelling: 'Elon Musk ficaria orgulhoso, pisar em Marte é para poucos, seja na vida real ou até mesmo no nosso game. 10 milhões de faturamento te coloca na mais alta patente, com os mais destemidos empreendedores da galáxia!',
            billedStart: '10000000',
            messageStart: '10M',
            billedStop: '50000000',
            messageStop: '50M',
        },
        5: {
            name: 'Capitão Galáctico',
            description: 'Nível 5',
            icon: '/modules/global/adminremark/assets/images/nivel-5.png',
            storytelling: 'Existe vida fora da Terra e agora você é capaz de provar. Apesar de estarmos bem longe, nossa viagem deve continuar, mas se fosse para ficar... os nativos ficariam orgulhosos com sua história, de onde você veio e para onde está indo!',
            billedStart: '50000000',
            messageStart: '50M',
            billedStop: '100000000',
            messageStop: '100M',
        },
        6: {
            name: 'Sirius Major',
            description: 'Nível 6',
            icon: '/modules/global/adminremark/assets/images/nivel-6.png',
            storytelling: 'Parabéns! Você atingiu os confins do universo e a expressiva marca de 100M de faturamento, um verdadeiro explorador do espaço e dos negócios. Você acaba de chegar na Canis Major e conhecer de perto a Sírius, a estrela mais brilhante!',
            billedStart: '100000000',
            messageStart: '100M',
            billedStop: '500000000',
            messageStop: '500M',
        },
    };

    function nextPerformance(data) {
        setTimeout(function () {
            loadingOnAccountsHealthRemove('.sirius-performance > .card .sirius-loading');
        }, 500);
        $(".sirius-performance .card-indicators > .active").on("click", function () {
            $('.sirius-performance > .card').html('');
            //setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-performance > .card .sirius-loading'); }, 500);
            loadingOnAccountsHealth('.sirius-performance > .card');
            let card = $(this).data('slide-to');
            switch (card) {
                case 1:
                    updatePerformanceCard1(data);
                    break;
                case 2:
                    updatePerformanceCard2(data);
                    break;
                case 3:
                    updatePerformanceCard3(data);
                    break;
                default:
            }
        });
    }


    window.updatePerformance = function () {

        loadingOnAccountsHealth('.sirius-performance > .card');
        //$('#achievements .achievements-item').addClass('opacity-3');

        $.ajax({
            method: "GET",
            url: "/api/dashboard/get-performance",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                company: $('#company').val(),
            },
            error: function error(response) {
                loadingOnAccountsHealthRemove('.sirius-performance > .card .sirius-loading');
                errorAjaxResponse(response);
            },
            success: function success(data) {

                updatePerformanceCard1(data);
                if ((data.level > 1) && (data.money_cashback !== '0,00')) {
                    updateCashback(data.money_cashback)
                }
                //nextPerformance(data);
                //updatePerformanceCard2(data);

            }
        });
    }

    function updatePerformanceCard1(data) {
        let currentLevel = levelInfo[data.level];
        //$("#level-icon").html('').html(`<img src="${currentLevel.icon}" alt="">`);
        //$("#level").text('').text(currentLevel.name);
        //$("#level-description").text('').text(currentLevel.description);


        let item = `
                <div class="card-header mt-10 pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="font-size-14 gray-600 mr-auto">
                        <span class="ml-0">Seu desempenho</span>
                    </div>
                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                        <li class="active" data-slide-to="1"></li>
                        <li class="" data-slide-to="2"></li>
                        <li class="" data-slide-to="3"></li>
                        <i class="o-angle-down-1 control-prev active" data-slide-to="3"></i>
                        <i class="o-angle-down-1 control-next active" data-slide-to="2"></i>
                    </ol>
                </div>
                <div class="card-body pb-5 pt-0 mt-15 d-flex flex-column justify-content-start ">
                    <div class="d-flex flex-row justify-content-start align-items-start align-self-start">
                        <div class=" text-center px-0 d-flex justify-content-center mr-20">
                            <div id="level-icon">
                                <img src="${currentLevel.icon}" alt="${currentLevel.name}">
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-self-center">
                            <div id="level" class="level mb-1">${currentLevel.name}</div>
                            <div id="level-description" class="level-description">${currentLevel.description}</div>
                        </div>
                    </div>
                    <div id="achievements" class="mt-10 d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch ">
                        <div id="achievements-item-1" class=" mb-10 d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">
                        </div>
                        <div id="achievements-item-2" class="d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">
                        </div>
                    </div>
                    <div id="tasks">
                        <div class="d-flex flex-column justify-content-start align-items-start">
                        </div>
                    </div>
                    <div id="progress" class="mt-15 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">
                        <div class="d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">
                            <span id="progress-message-1"></span>
                            <span id="progress-message-2"></span>
                        </div>
                        <div id="progress-bar"
                             class="mt-10 d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch"
                             data-toggle="tooltip"
                        >
                                <div></div>
                                <span></span>
                        </div>
                    </div>
                </div>

        `;

        $('.sirius-performance > .card').append(item);

        updateAchievements(data.achievements);

        updateTasks(data.level, data.tasks);

        updateProgressBar(data.billed, currentLevel);

        nextPerformance(data);
    }

    function updateAchievements(achievements) {

        if (!isEmpty(achievements)) {
            let achievementsLength = achievements.length / 2;

            $.each(achievements, function (index, value) {

                let item = ` <div  class="achievements-item ${value.active ? '' : 'not-active'} " >
                             <img src="${value.icon}">

                         </div>`;
                if (index < achievementsLength) {
                    $('#achievements > #achievements-item-1').append(item);
                } else {
                    $('#achievements > #achievements-item-2').append(item);
                }
            });
        }
    }

    function updateTasks(level, tasks) {
        const elementTask =  $('#tasks');
        elementTask.first('div').html('');

        if (!isEmpty(tasks)) {

            $.each(tasks, function (index, value) {

                let item = `<div class="d-flex justify-content-start align-items-center align-self-start task">
                                 <span class="task-icon ${value.status === 1 ? 'o-checkmark-1 task-icon-checked' : ''} d-flex justify-content-around align-items-center"></span>
                                 <p class="m-0 ${value.status === 1 ? 'task-description-checked' : ''} ">${value.name}</p>
                            </div>`;
                elementTask.first('div').append(item);
            });

            elementTask.css({'margin-top': '20px'}).show();

            if (tasks.length > 3) {
                elementTask.css({'margin-top': '0'}).asScrollable();
                $('#achievements').css({'margin-bottom': '20px'});
            }

        } else {
            elementTask.hide();
        }
    }

    function updateProgressBar(billed, currentLevel) {

        $('#progress-message-1').text(`${currentLevel.messageStart}`);
        $('#progress-bar').attr('data-original-title', `Total faturado ${(billed / 100).toLocaleString('pt-br', {
            style: 'currency',
            currency: 'BRL'
        })}`);
        $('#progress-message-2').text(`${currentLevel.messageStop}`);

        var percentage = billed / currentLevel.billedStop;
        percentage = percentage > 100 ? 99 : percentage;

        percentage = percentage > 10 ? percentage : parseFloat(percentage).toFixed(1);
        $("#progress-bar > div").css({'width': `${percentage > 1 ? percentage : 1}%`});
        if (percentage > 13) {
            $('#progress-bar > span').text(`${Math.trunc(percentage)}%`);
            $('#progress-bar > span').css({'left': `${parseFloat(percentage) - 9}%`, 'color': '#FFFFFF'});
        } else {
            $('#progress-bar > span').text(`${percentage > 1 ? Math.trunc(percentage) : parseFloat(percentage).toFixed(1)}%`);
            $('#progress-bar > span').css({'left': `${parseFloat(percentage) + 3}%`, 'color': '#2E85EC'});
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    }

    function updateCashback(money) {
        $('#cashback-container #cashback-container-money').text(`${money}`);
        $(".sirius-cashback > .card").removeClass('d-none');
    }

    function updatePerformanceCard2(data) {
        loadingOnAccountsHealth('.sirius-performance > .card');
        let currentLevel = levelInfo[data.level];

        //$("#level-icon").html('').html(`<img src="${currentLevel.icon}" alt="">`);
        //$("#level").text('').text(currentLevel.name);
        //$("#level-description").text('').text(currentLevel.description);


        let item = `
                <div class="card-header mt-10 pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="mr-auto">
                        <span class="ml-0 title-performance">Seu desempenho</span>
                    </div>
                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                        <li class="" data-slide-to="1"></li>
                        <li class="active" data-slide-to="2"></li>
                        <li class="" data-slide-to="3"></li>
                        <i class="o-angle-down-1 control-prev active" data-slide-to="1"></i>
                        <i class="o-angle-down-1 control-next active" data-slide-to="3"></i>
                    </ol>
                </div>
                <div class="card-body pb-5 pt-0 mt-15 d-flex flex-column justify-content-start">
                    <div id="card-level-description" >
                        <div class="p-15 d-flex flex-column flex-nowrap justify-content-start align-items-stretch align-self-stretch ">
                            <div class="d-flex flex-row justify-content-between align-items-center">
                                <div class="">
                                    <span id="level-full" class="level mr-5">${currentLevel.name}</span>
                                    <span id="level-current">ATUAL</span>
                                </div>

                                <div class="">
                                    <span id="billed-message" class="ml-0">R$${currentLevel.messageStart} - R$${currentLevel.messageStop}</span>
                                </div>
                            </div>
                            <p id="level-message" class="level-description">${currentLevel.storytelling}</p>
                        </div>
                    </div>

                    <div id="levels" class="mt-15 d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">
                    </div>

                    <div class="benefits mt-10 d-flex flex-column flex-nowrap justify-content-start ">
                        <span class="mb-10 title-performance">Benefícios ativos</span>
                        <div id="benefits-active-container">
                            <div class="d-flex flex-column flex-nowrap justify-content-start ">
                            </div>
                        </div>
                    </div>

                    <div class="benefits mt-10 d-flex flex-column flex-nowrap justify-content-start ">
                        <span class="mb-10 title-performance">Seus próximos benefícios</span>
                        <div id="benefits-container">
                            <div class="d-flex flex-column flex-nowrap justify-content-start ">
                            </div>
                        </div>
                    </div>

                </div>

        `;

        $('.sirius-performance > .card').append(item);
        // $("#level-full").text('').text(`${currentLevel.name}`);
        // $("#level-message").text('').text(currentLevel.storytelling);
        // $("#billed-message").text(`R$${currentLevel.messageStart} - R$${currentLevel.messageStop}`);

        $.each(levelInfo, function (index, value) {
            if (data.level == index) {
                $("#level-current").show();
            }

            let item = ` <div id="level-item-${index}" class="level-item ${data.level == index ? 'active' : ''}" data-level="${index}" data-level-current="${data.level}">
                             <img src="${value.icon}">

                         </div>`;
            $('#levels').append(item);
        });

        $(".level-item").click(function () {

            let level = $(this).data('level');
            let currentLevel = levelInfo[level];
            $(this).data('level-current') === level ? $("#level-current").show() : $("#level-current").hide();
            $(".level-item").removeClass("active");
            $(this).addClass("active");

            $("#level-full").text('').text(`${currentLevel.name}`);
            $("#level-message").text('').text(currentLevel.storytelling);
            $("#billed-message").text(`R$${currentLevel.messageStart} - R$${currentLevel.messageStop}`);

        });

        updateBenefits(data.level, data.benefits);

        nextPerformance(data);
    }

    function updateBenefits(level, benefits) {
        $('#benefits-active-container > div').html('');

        //const elementBenefitsActiveContainer = $('#benefits-active-container');
        //elementBenefitsActiveContainer.html('');



        $('#benefits-container').html('');

        if (!isEmpty(benefits.active)) {

            $.each(benefits.active, function (index, value) {

                let item = `<div class=" d-flex justify-content-start align-items-center align-self-start benefit">
                                 <span class="benefits-button ${value.enabled ? 'benefits-button-checked' : 'benefits-button-blocked'} d-flex justify-content-around align-items-center">${value.disabled ? 'Inativo' : 'Ativo'}</span>
                                 <p class="m-0">${value.name}</p>
                            </div>`;
                $('#benefits-active-container').append(item);
            });

            if (benefits.active.length > 2) {
                $('#benefits-active-container').asScrollable();
            }

        } else {

            $('#benefits-active-container').html(`
                                                        <div class="d-flex justify-content-start align-items-center align-self-start">
                                                            <div id="benefits-empty-image" class=" text-center px-0 d-flex justify-content-center mr-20">
                                                                <img src="/modules/global/adminremark/assets/images/empty-benefits.png" alt="">
                                                            </div>
                                                            <div class="d-flex flex-column justify-content-center align-self-center">
                                                                <div class="benefits-name mb-1">
                                                                    Você ainda não tem nenhum benefício ativo em sua conta.
                                                                </div>
                                                                <div class="benefits-description">
                                                                    Suba de nível para mudar isso :)
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `);
        }

        if (!isEmpty(benefits.next)) {

            $.each(benefits.next, function (index, value) {

                let item = `<div class="d-flex justify-content-start align-items-center align-self-start">
                                 <span class="benefits-button d-flex justify-content-around align-items-center">NÍVEL ${value.level}</span>
                                 <p class="m-0">${value.name}</p>
                            </div>`;
                $('#benefits-container').append(item);
            });

            if (benefits.next.length > 2) {
                $('#benefits-container').asScrollable();
            }
        }

    }

    function updatePerformanceCard3(data) {
        loadingOnAccountsHealth('.sirius-performance > .card');
        let currentLevel = levelInfo[data.level];

        let item = `
                <div class="card-header mt-10 mb-20 pb-0 d-flex justify-content-between align-items-center bg-white">
                    <div class="mr-auto">
                        <span class="ml-0 title-performance">Suas conquistas</span>
                    </div>
                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                        <li class="" data-slide-to="1"></li>
                        <li class="" data-slide-to="2"></li>
                        <li class="active" data-slide-to="3"></li>
                        <i class="o-angle-down-1 control-prev active" data-slide-to="2"></i>
                        <i class="o-angle-down-1 control-next active" data-slide-to="1"></i>
                    </ol>
                </div>

                <div id="card-achievements" class="card-body pb-0 pt-0 ">
                    <div class="d-flex flex-column justify-content-start align-items-start">
                    </div>
                </div>
                <div class="list-linear-gradient"></div>
        `;

        $('.sirius-performance > .card').append(item);

        updateAchievementsCard(data.achievements);

        nextPerformance(data);
    }

    function updateAchievementsCard(achievements) {
        if (!isEmpty(achievements)) {
            const element = $('#card-achievements > div');
            let item = "";

            const isActiveAchievements = achievements => achievements.active;
            const achievementsActive = achievements.filter(isActiveAchievements);
            item += achievementsList(achievementsActive);

            item += `<div class="title-performance mt-15 mb-10 d-flex justify-content-start align-items-start align-self-start">Você ainda não conquistou:</div>`;

            const isNotActiveAchievements = achievements => !achievements.active;
            const achievementsNotActive = achievements.filter(isNotActiveAchievements);
            item += achievementsList(achievementsNotActive);

            element.append(item);

            $('#card-achievements').asScrollable();

        }
    }

    function achievementsList(achievements) {

        const achievementsLength = achievements.length;
        let item = '';

        $.each(achievements, function (index, value) {

            item += ` <div class="achievements-list d-flex flex-row justify-content-start align-items-start align-self-start">
                            <div class="achievements-list-icon mr-20 ${value.active ? '' : 'not-active'} d-flex justify-content-center ">
                                <img src="${value.icon}" alt="${value.name}">
                            </div>
                            <div class="d-flex flex-column justify-content-center align-self-center">
                                <div class="achievements-list-name level mb-1">${value.name}</div>
                                <div class="achievements-list-description level-description">${value.description}</div>
                            </div>
                        </div>`;

            if (index < (achievementsLength - 1)) {
                item += `<div class="hr-horizontal mt-10 mb-10 d-flex justify-content-center align-items-center align-self-center"></div>`;
            }
        });

        return item;
    }
});
