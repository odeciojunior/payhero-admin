$(document).ready(function () {

    let levelInfo = {
        1: {
            name: 'Aventureiro',
            title: 'Pronto para começar?',
            icon: '/modules/global/adminremark/assets/images/nivel-1.png',
            message: '1Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação',
            billedStart: '0',
            messageStart: '0K',
            billedStop: '100000',
            messageStop: '100K',
        },
        2: {
            name: 'Viajante Espacial',
            title: 'Nível 2',
            icon: '/modules/global/adminremark/assets/images/nivel-2.png',
            message: '2Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação',
            billedStart: '100000',
            messageStart: '100K',
            billedStop: '1000000',
            messageStop: '1M',
        },
        3: {
            name: 'Conquistador',
            title: 'Nível 3',
            icon: '/modules/global/adminremark/assets/images/nivel-3.png',
            message: '3Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação',
            billedStart: '1000000',
            messageStart: '1M',
            billedStop: '10000000',
            messageStop: '10M',
        },
        4: {
            name: 'Colonizador',
            title: 'Nível 4',
            icon: '/modules/global/adminremark/assets/images/nivel-4.png',
            message: '4Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação',
            billedStart: '10000000',
            messageStart: '10M',
            billedStop: '50000000',
            messageStop: '50M',
        },
        5: {
            name: 'Capitão Galáctico',
            title: 'Nível 5',
            icon: '/modules/global/adminremark/assets/images/nivel-5.png',
            message: '5Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação',
            billedStart: '50000000',
            messageStart: '50M',
            billedStop: '100000000',
            messageStop: '100M',
        },
        6: {
            name: 'Sirius Major',
            title: 'Nível 6',
            icon: '/modules/global/adminremark/assets/images/nivel-6.png',
            message: '6Você acaba de chegar na Central de Operações Sirius. A partir daqui, você será treinado e deverá provar que merece um lugar no foguete que nos levará para uma longa viagem espacial, que tem como objetivo final atingir a constelação',
            billedStart: '100000000',
            messageStart: '100M',
            billedStop: '500000000',
            messageStop: '500M',
        },
    };

    function  nextPerformance() {
        setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-loading'); }, 1000);
        $(".sirius-performance .card-indicators > .active").on("click", function () {
            setTimeout(function(){ loadingOnAccountsHealthRemove('.sirius-loading'); }, 500);
            //$('.sirius-account > .card').html('');

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

        $('#achievements .achievements-item').addClass('opacity-3');
        $('.task .task-icon').removeClass('o-checkmark-1');
        $('.task .task-icon').removeClass('.task-icon-checked');

        $.ajax({
            method: "POST",
            url: "/api/dashboard/get-performance",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {company: $('#company').val()},
            error: function error(response) {;

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

        //if ( tasks.len data.level ===  1 || data.level ===2){
        updateTasks(data.level, data.tasks);
        //}

        if ( data.level > 1){
            updateCashback(data.money_cashback)
            //$('#cashback-container #cashback-container-money').text(`${data.money_cashback}`);
            //$("#cashback").show();
        }

        //updateProgressBar(data.progress);
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
        $("#progress-bar-1").css({'width': '0%'}); //, 'padding-right': '0px'});
        $("#progress-bar-2").css({'width': '100%'}); //, 'padding-left': '0px'});  //, 'border-radius': '10px 10px 10px 10px'});

        $('#progress-1').text(`${currentLevel.messageStart}`);
        $('.progress-billed').text(`Total faturado ${(billed/100).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})}`)
        $('#progress-2').text(`${currentLevel.messageStop}`);

        //alert(progress_money + "  -  " + money);
        var percentage = billed /currentLevel.billedStop;

        if (percentage > 0) {
            percentage = percentage > 10 ? parseFloat(percentage).toFixed(0) : parseFloat(percentage).toFixed(1);
            $("#progress-bar-1").css({'width': `${percentage}%` });
            $("#progress-bar-2").css({'width': `${100 - percentage}%`} );

            if ( $('#progress-bar-1').width() > 40) {
                $('#progress-bar-1').text(`${parseFloat(percentage).toFixed(0) }%`)
                $("#progress-bar-1").css({'padding-right': '8px' });
                $("#progress-bar-2").css({'padding-left': '0px' });
            } else {
                $('#progress-bar-2').text(`${percentage > 1 ? parseFloat(percentage).toFixed(0) : parseFloat(percentage).toFixed(1)  }%`);
                $("#progress-bar-2").css({'padding-left': '8px' });
                $("#progress-bar-1").css({'padding-right': '0px' });
            }

        }
    }



    function updatePerformanceCard2(data) {
        let currentLevel = levelInfo[data.level];
        //data.billed = data.billed/100;
        //$("#level-icon").html('').html(`<img src="${currentLevel.icon}" alt="">`);
        $("#level-full").text('').text(`${currentLevel.name}`);
        $("#level-message").text('').text(currentLevel.message);
        //$("#billed").text(`R$${data.billed.toLocaleString('pt-br', {minimumFractionDigits: 2})}`);
        $("#billed-message").text(`R$${currentLevel.messageStart} - R$${currentLevel.messageStop}`);



        $.each(levelInfo, function (index, value) {

            //<div id="level-item-1" className="level-item"
              //   style="background-image: url(https://pm1.narvii.com/7191/1ccee66facee377777d3e3f943ccb0ae2a8bedd6r1-200-141v2_hq.jpg)">
            //     <img src="">
            // </div>
            // <span
            //     className="benefit-button ${value.status === 1 ? 'benefit-button-checked' : ''} d-flex justify-content-around align-items-center">${value.card}</span>
            // <p className="m-0">${value.benefit}</p>

            //alert(index);
            if (data.level == index) {
                $("#level-current").show();
                //alert(data.level);
            }

            let item = ` <div id="level-item-${index}" class="level-item ${ data.level == index ? 'active' : '' }" data-level="${index}" data-level-current="${data.level}">
                             <img src="${value.icon}">

                         </div>`;
            $('#levels').append(item);


        });

        $(".level-item").click(function () {

            let level = $(this).data('level');
            //alert(level);
            let currentLevel = levelInfo[level];
            $(this).data('level-current') ===  level ? $("#level-current").show() : $("#level-current").hide();
            $(".level-item").removeClass("active");
            $(this).addClass("active");
            //$(`#level-item-${index}`).removeClass("active");


            $("#level-full").text('').text(`${currentLevel.name}`);
            $("#level-message").text('').text(currentLevel.message);
            $("#billed-message").text(`R$${currentLevel.messageStart} - R$${currentLevel.messageStop}`);

            //$(this).removeClass("active");
        });

        updateBenefits(data.level, data.benefits);


        //--updateAchievements(data.achievements);

        //if ( tasks.len data.level ===  1 || data.level ===2){
        //--updateTasks(data.level, data.tasks);
        //}

        if ( data.level > 1){
            //--updateCashback(data.money_cashback)
            //$('#cashback-container #cashback-container-money').text(`${data.money_cashback}`);
            //$("#cashback").show();
        }

        //updateProgressBar(data.progress);
        //updateProgressBar(data.progress_money, currentLevel);
        //--updateBenefits(data.level, data.benefits);

    }

    function updateBenefits(level, benefits) {
        $('#benefits-active-container').html('');
        $('#benefits-container').html('');

        if (!isEmpty(benefits.active)) {

            $.each(benefits.active, function (index, value) {

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

        } else {

            //d-flex flex-row justify-content-start align-items-start align-self-start
            //<div class="d-flex flex-row justify-content-start align-items-start align-self-start">

            //</div>

            $('#benefits-active-container').html(`
                                                    <div class="d-flex justify-content-start align-items-center align-self-start benefit">
                                                        <div class=" text-center px-0 d-flex justify-content-center mr-20">
                                                            <div id="benefits-empty">
                                                                <img src="/modules/global/adminremark/assets/images/benefits-empty.png" alt="">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center align-self-center">
                                                            <div class="level mb-1">
                                                                Você ainda não tem nenhum benefício ativo em sua conta.
                                                            </div>
                                                            <div class="level-description">
                                                                Suba de nível para mudar isso :)
                                                            </div>
                                                        </div>
                                                    </div>
                                                    `);
        }

        if (!isEmpty(benefits.next)) {

            $.each(benefits.next, function (index, value) {

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

    // function updateBenefits(level, data) {
    //     $('#benefits-container').html('');
    //
    //     if (!isEmpty(data)) {
    //
    //         $.each(data, function (index, value) {
    //
    //             let item = `<div class="mb-10 d-flex justify-content-start align-items-center align-self-start benefit">
    //                              <span class="benefit-button ${value.status === 1 ? 'benefit-button-checked' : ''} d-flex justify-content-around align-items-center">${value.card}</span>
    //                              <p class="m-0">${value.benefit}</p>
    //                         </div>`;
    //             $('#benefits-container').append(item);
    //         });
    //
    //         if ( level === 2){
    //             $("#benefits-container").height() > 83 ? $("#benefits-container").css({'max-height': '80px', 'overflow-y': 'scroll'}) : $("#benefits-container").css({'max-height': '80px', 'overflow-y': 'hidden'});
    //         }
    //         else {
    //             $("#benefits-container").height() > 122 ? $("#benefits-container").css({'max-height': '120px', 'overflow-y': 'scroll'}) : $("#benefits-container").css({'max-height': '120px', 'overflow-y': 'hidden'});
    //         }
    //
    //     }
    }

});
