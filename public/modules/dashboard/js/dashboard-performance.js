$(document).ready(function () {

    let levelInfo = {
        1: {
            name: 'Aventureiro',
            title: 'Pronto para começar?',
            icon: '/modules/global/adminremark/assets/images/nivel-1.png',
            message: 'Nossa jornada está apenas começando. Você já pode começar a olhar o céu noturno e se imaginar navegando na imensidão do desconhecido, é hora de mirar as estrelas e se preparar para a maior aventura de sua vida empreendedora.',
            billedStart: '0',
            messageStart: '0K',
            billedStop: '100000',
            messageStop: '100K',
        },
        2: {
            name: 'Viajante Espacial',
            title: 'Nível 2',
            icon: '/modules/global/adminremark/assets/images/nivel-2.png',
            message: 'Nosso foguete está saindo da Terra, este momento de fortes emoções foi experimentado por poucos! Quem diria, de tanto olhar para o céu estrelado, hoje você está navegando por ele, rumo à nossa primeira parada: a lua!',
            billedStart: '100000',
            messageStart: '100K',
            billedStop: '1000000',
            messageStop: '1M',
        },
        3: {
            name: 'Conquistador',
            title: 'Nível 3',
            icon: '/modules/global/adminremark/assets/images/nivel-3.png',
            message: 'Nível 3? Você está avançando bem, daqui da lua você já consegue enxergar que a Terra é pequena demais para você. Aproveite a vista, faça pequenos reparos porque ainda temos bastante aventura pela frente e a próxima parada é Marte!',
            billedStart: '1000000',
            messageStart: '1M',
            billedStop: '10000000',
            messageStop: '10M',
        },
        4: {
            name: 'Colonizador',
            title: 'Nível 4',
            icon: '/modules/global/adminremark/assets/images/nivel-4.png',
            message: 'Elon Musk ficaria orgulhoso, pisar em Marte é para poucos, seja na vida real ou até mesmo no nosso game. 10 milhões de faturamento te coloca na mais alta patente, com os mais destemidos empreendedores da galáxia!',
            billedStart: '10000000',
            messageStart: '10M',
            billedStop: '50000000',
            messageStop: '50M',
        },
        5: {
            name: 'Capitão Galáctico',
            title: 'Nível 5',
            icon: '/modules/global/adminremark/assets/images/nivel-5.png',
            message: 'Existe vida fora da Terra e agora você é capaz de provar. Apesar de estarmos bem longe, nossa viagem deve continuar, mas se fosse para ficar... os nativos ficariam orgulhosos com sua história, de onde você veio e para onde está indo!',
            billedStart: '50000000',
            messageStart: '50M',
            billedStop: '100000000',
            messageStop: '100M',
        },
        6: {
            name: 'Sirius Major',
            title: 'Nível 6',
            icon: '/modules/global/adminremark/assets/images/nivel-6.png',
            message: 'Parabéns! Você atingiu os confins do universo e a expressiva marca de 100M de faturamento, um verdadeiro explorador do espaço e dos negócios. Você acaba de chegar na Canis Major e conhecer de perto a Sírius, a estrela mais brilhante!',
            billedStart: '100000000',
            messageStart: '100M',
            billedStop: '500000000',
            messageStop: '500M',
        },
    };

    function  nextPerformance() {
        setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-performance > .card .sirius-loading'); }, 500);
        $(".sirius-performance .card-indicators > .active").on("click", function () {
            setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-performance > .card .sirius-loading'); }, 500);
            loadingOnAccountsHealth('.sirius-performance > .card');

            let card = $(this).data('slide-to');
            switch(card) {
                case 1:
                    $('#performance-card-2').hide();
                    $('#performance-card-1').show();
                    break;
                case 2:
                    $('#performance-card-1').hide();
                    $('#performance-card-2').show();
                    break;
                default:
            }
        });
    }

    window.updatePerformance = function () {

        loadingOnAccountsHealth('.sirius-performance > .card');
        $('#achievements .achievements-item').addClass('opacity-3');

        $.ajax({
            method: "POST",
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
                nextPerformance();
                updatePerformanceCard2(data);

            }
        });
    }

    function updatePerformanceCard1(data) {
        let currentLevel = levelInfo[data.level];
        $("#level-icon").html('').html(`<img src="${currentLevel.icon}" alt="">`);
        $("#level").text('').text(currentLevel.name);
        $("#level-description").text('').text(currentLevel.title);


        updateAchievements(data.achievements);

        updateTasks(data.level, data.tasks);

        if ( data.level > 1){
            updateCashback(data.money_cashback)
        }

        updateProgressBar(data.billed, currentLevel);
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
                                 <p class="m-0 ${value.status === 1 ? 'task-description-checked' : ''} ">${value.name}</p>
                            </div>`;
                $('#tasks').append(item);
            });

            if (data.length > 3) {
                $("#tasks").hover(
                    function () {
                        console.log('Entrei scroll');
                        $("#tasks").css({'overflow-y': 'scroll'});
                    }, function () {
                        console.log('Sai hidden');
                        $("#tasks").css({'overflow-y': 'hidden'});
                    }
                );
            }

            $("#tasks").css({'max-height': '95px', 'overflow-y': 'hidden'});
            $('#tasks').show();

        } else {
            $('#tasks').hide();
        }
    }

    function updateCashback(money_cashback) {
        var money = money_cashback/100;
        $('#cashback-container #cashback-container-money').text(`${money.toLocaleString('pt-br',{minimumFractionDigits: 2}) }`);
        $("#cashback").show();
    }

    function updateProgressBar(billed, currentLevel) {

        $('#progress-message-1').text(`${currentLevel.messageStart}`);
        $('#progress-bar').attr('data-original-title', `Total faturado ${(billed/100).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})}`);
        $('#progress-message-2').text(`${currentLevel.messageStop}`);

        var percentage = billed /currentLevel.billedStop;

        percentage = percentage > 10 ? percentage : parseFloat(percentage).toFixed(1);
        $("#progress-bar > div").css({'width': `${percentage > 1 ? percentage : 1}%`});
        if (percentage > 13) {
            $('#progress-bar > span').text(`${Math.trunc(percentage) }%`);
            $('#progress-bar > span').css({'left': `${parseFloat(percentage) - 9 }%`, 'color': '#FFFFFF'});
        }
        else {
            $('#progress-bar > span').text(`${percentage > 1 ? Math.trunc(percentage) : parseFloat(percentage).toFixed(1)  }%`);
            $('#progress-bar > span').css({'left': `${parseFloat(percentage) + 3 }%`, 'color': '#2E85EC' });
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

    }

    function updatePerformanceCard2(data) {
        let currentLevel = levelInfo[data.level];

        $("#level-full").text('').text(`${currentLevel.name}`);
        $("#level-message").text('').text(currentLevel.message);
        $("#billed-message").text(`R$${currentLevel.messageStart} - R$${currentLevel.messageStop}`);



        $.each(levelInfo, function (index, value) {
            if (data.level == index) {
                $("#level-current").show();
            }

            let item = ` <div id="level-item-${index}" class="level-item ${ data.level == index ? 'active' : '' }" data-level="${index}" data-level-current="${data.level}">
                             <img src="${value.icon}">

                         </div>`;
            $('#levels').append(item);
        });

        $(".level-item").click(function () {

            let level = $(this).data('level');
            let currentLevel = levelInfo[level];
            $(this).data('level-current') ===  level ? $("#level-current").show() : $("#level-current").hide();
            $(".level-item").removeClass("active");
            $(this).addClass("active");

            $("#level-full").text('').text(`${currentLevel.name}`);
            $("#level-message").text('').text(currentLevel.message);
            $("#billed-message").text(`R$${currentLevel.messageStart} - R$${currentLevel.messageStop}`);

        });

        updateBenefits(data.level, data.benefits);

        $('#performance-card-2').hide();
    }

    function updateBenefits(level, benefits) {
        $('#benefits-active-container').html('');
        $('#benefits-container').html('');

        if (!isEmpty(benefits.active)) {

            $.each(benefits.active, function (index, value) {

                let item = `<div class=" d-flex justify-content-start align-items-center align-self-start benefit">
                                 <span class="benefits-button ${value.status ? 'benefits-button-checked' : 'benefits-button-blocked'} d-flex justify-content-around align-items-center">${value.status ? 'Ativo' : 'Inativo'}</span>
                                 <p class="m-0">${value.name}</p>
                            </div>`;
                $('#benefits-active-container').append(item);
            });

            //if ($("#benefits-active-container").height() > 92) {
            if (benefits.active.length > 2) {
                $("#benefits-active-container").hover(
                    function () {
                        $("#benefits-active-container").css({'overflow-y': 'scroll'});
                    }, function () {
                        $("#benefits-active-container").css({'overflow-y': 'hidden'});
                    }
                );
            }
            $("#benefits-active-container").css({'max-height': '60px', 'overflow-y': 'hidden'});

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

            //if ($("#benefits-container").height() > 92) {
            if (benefits.next.length > 2) {
                $("#benefits-container").hover(
                    function () {
                        $("#benefits-container").css({'overflow-y': 'scroll'});
                    }, function () {
                        $("#benefits-container").css({'overflow-y': 'hidden'});
                    }
                );
            }
            $("#benefits-container").css({'max-height': '60px', 'overflow-y': 'hidden'});

        }

    }

});
