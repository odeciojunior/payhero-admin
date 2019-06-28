$(document).ready(function () {

    /// calendario ///////////
    var startDate = moment().subtract(29, 'days');
    var endDate = moment();

    /*  function cb(start, end) {
          $('#reportrange span').html(start.format('D  MMMM , YYYY') + ' - ' + end.format('D  MMMM , YYYY'));
      }*/

    updateReports();

    $('#reportrange').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        lang: 'pt-br',
        "timePicker24Hour": true,
        "autoApply": true,
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Último 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este Mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            "format": "DD/MM/YYYY",
            "separator": "-",
            "customRangeLabel": 'Personalizado',
            "daysOfWeek": [
                'Dom',
                'Seg',
                'Ter',
                'Qua',
                'Qui',
                'Sex',
                'Sab',
            ],
            "monthNames": [
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
                'Dezembro',
            ],
            "monthNamesShort": [
                'Jan',
                'Fev',
                'Mar',
                'Abr',
                'Mai',
                'Jun',
                'Jul',
                'Ago',
                'Set',
                'Out',
                'Nov',
                'Dez',
            ],
            firstDay: 1
        },
        "alwaysShowCalendar": true,
    }, function (start, end) {
        startDate = start;
        endDate = end;
        alert(startDate);
    });

    // cb(start, end);
    /// calendario Fim ///////////

    $("#project").on('change', function () {
        $('#project').val($(this).val());
        updateReports();
    });

    function updateReports() {
        $.ajax({
            url: '/reports/getValues/' + $("#project").val(),
            type: 'GET',
            data: {project: $("#project").val()},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                alertCustom('error', 'Erro ao tentar buscar dados');
            },
            success: function (response) {
                console.log(response);
                $("#revenue-generated").html(response.totalPaidValue);
                $("#qtd-aproved").html(response.contAproved);
                $("#qtd-boletos").html(response.contBoleto);
                $("#qtd-recusadas").html(response.contRecused);
                $("#qtd-reembolso").html(response.contChargeBack);
                $("#percent-credit-card").html(response.totalPercentCartao + ' %');
                $("#percent-values-boleto").html(response.totalPercentPaidBoleto + ' %');

                $("#credit-card-value").html('R$ ' + response.totalValueCreditCard);
                $("#boleto-value").html('R$ ' + response.totalValueBoleto);
            }
        })
    }

});
