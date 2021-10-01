$(window).on("load", function(){

    window.gatewayCode = window.location.href.split('/')[4];

    if(window.gatewayCode == 'NzJqoR32egVj5D6') {
        $(".page-title").text('Finanças - Asaas');
    }
    else if(window.gatewayCode == 'w7YL9jZD6gp4qmv') {
        $(".page-title").text('Finanças - Getnet');
    }
    else if(window.gatewayCode == 'oXlqv13043xbj4y') {
        $(".page-title").text('Finanças - Gerencianet');
    }
    else if(window.gatewayCode == 'pM521rZJrZeaXoQ') {
        $(".page-title").text('Finanças - Cielo');
    }

    $('#date_range').daterangepicker({
        startDate: moment().startOf('week'),
        endDate: moment(),
        opens: 'center',
        maxDate: moment().endOf("day"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: 'Customizado',
        autoUpdateInput: true,
        locale: {
            locale: 'pt-br',
            format: 'DD/MM/YYYY',
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: 'De',
            toLabel: 'Até',
            customRangeLabel: 'Customizado',
            weekLabel: 'W',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 0
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Vitalício': [moment('2018-01-01 00:00:00'), moment()]
        }
    });

    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {

                if (isEmpty(response.data)) {
                    $('.page-content').hide();
                    $('.content-error').show();
                    loadingOnScreenRemove();
                    return;
                }

                $('.page-content').show();
                $('.content-error').hide();

                $(response.data).each(function (index, value) {
                    let data = `<option country="${value.country}" value="${value.id}">${value.name}</option>`;
                    $("#transfers_company_select").append(data);
                    $("#extract_company_select").append(data);
                });

                checkBlockedWithdrawal();
                updateBalances();
                updateTransfersTable();
                $("#nav-statement").css('display', '');
                $("#nav-statement").css('display', '');
                $("#nav-statement-tab").on('click', function () {
                    $("#nav-statement").css('display', '');
                });

                loadingOnScreenRemove();
            }
        });
    }

    getCompanies();

    $('#transaction').on('change paste keyup select', function () {
        let val = $(this).val();

        if (val === '') {
            $('#date_type').attr('disabled', false).removeClass('disableFields');
            $('#date_range').attr('disabled', false).removeClass('disableFields');
        } else {
            $('#date_type').attr('disabled', true).addClass('disableFields');
            $('#date_range').attr('disabled', true).addClass('disableFields');
        }
    });

    $('.withdrawal-value').maskMoney({thousands: '.', decimal: ',', allowZero: true});

    //Verifica se o saque está liberado
    function checkBlockedWithdrawal() {
        $.ajax({
            url: "/api/withdrawals/checkallowed",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
                $('#bt-withdrawal').prop('disabled', true).addClass('disabled');
            },
            success: response => {
                if (response.allowed && verifyAccountFrozen() == false) {
                    $('#bt-withdrawal').prop('disabled', false).removeClass('disabled');
                    $('#blocked-withdrawal').hide();
                } else {
                    $('#bt-withdrawal').prop('disabled', true).addClass('disabled');
                    $('#blocked-withdrawal').show();
                }
            }
        });
    }

});
