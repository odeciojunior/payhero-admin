$(function () {
    var startDate = moment().subtract('days', 29).format('YYYY-MM-DD');
    var endDate = moment().format('YYYY-MM-DD');
    $('input[name="daterange"]').daterangepicker({
        startDate: moment().subtract('days', 29),
        endDate: moment(),
        opens: 'left',
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
            daysOfWeek: [
                'Dom',
                'Seg',
                'Ter',
                'Qua',
                'Qui',
                'Sex',
                'Sab'
            ],
            monthNames: [
                'Janeiro',
                'Fevereiro',
                'Março',
                'Abril',
                'Maio',
                'Junho',
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro'
            ],
            firstDay: 0,
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
        console.log(startDate, endDate);
        updateReports();

    });/* function (start, end, label) {
        endDate = end.format('YYYY-MM-DD');
        startDate = start.format('YYYY-MM-DD');
        // console.log(endDate, startDate);
        updateReports();
    */


    /*$('#date_range_requests').on('change', function (e) {
        e.preventDefault();
        updateReports();
    });*/


    $("#project").on('change', function () {
        $('#project').val($(this).val());
        updateReports();

    });

    function updateReports() {
        var date_range = $('#date_range_requests').val();
        console.log(date_range);
        $.ajax({
            url: '/reports/getValues/' + $("#project").val(),
            type: 'GET',
            data: {
                project: $("#project").val(),
                endDate: endDate,
                startDate: startDate
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                alertCustom('error', 'Erro ao tentar buscar dados');
            },
            success: function (response) {
                if (response.msg) {
                    $(".error-data").css('display', 'block').addClass('text-danger').html(response.msg);
                    $("#revenue-generated").html(0);
                    $("#qtd-aproved").html(0);
                    $("#qtd-boletos").html(0);
                    $("#qtd-recusadas").html(0);
                    $("#qtd-reembolso").html(0);
                    $("#percent-credit-card").html(0);
                    $("#percent-values-boleto").html(0);

                    $("#credit-card-value").html('R$ ' + response.totalValueCreditCard);
                    $("#boleto-value").html('R$ ' + response.totalValueBoleto);

                } else {
                    $(".error-data").css('display', 'none');
                    $("#revenue-generated").html(response.totalPaidValueAproved);
                    $("#qtd-aproved").html(response.contAproved);
                    $("#qtd-boletos").html(response.contBoleto);
                    $("#qtd-recusadas").html(response.contRecused);
                    $("#qtd-reembolso").html(response.contChargeBack);
                    $("#percent-credit-card").html(response.totalPercentCartao + ' %');
                    $("#percent-values-boleto").html(response.totalPercentPaidBoleto + ' %');

                    $("#credit-card-value").html('R$ ' + response.totalValueCreditCard);
                    $("#boleto-value").html('R$ ' + response.totalValueBoleto);
                }

            }
        })
    }

    updateReports();


});