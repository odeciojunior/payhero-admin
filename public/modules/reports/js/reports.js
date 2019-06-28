$(document).ready(function () {

    var project = $(".project").val();

    /// calendario ///////////
    var start = moment().subtract(29, 'days');
    var end = moment();
    updateReports(project, start, end);

    function cb(start, end) {
        $('#reportrange span').html(start.format('D  MMMM , YYYY') + ' - ' + end.format('D  MMMM , YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
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
            "fromLabel": "From",
            "toLabel": "To",
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
    }, cb);

    cb(start, end);
    /// calendario Fim ///////////

    $(".project").change(function () {
        project = $(".project option:selected").val();
        updateReports();
    });

    function updateReports(project,start, end) {
        $.ajax({
            method: 'GET',
            url: '/reports',
            data: {
                project: project,
                dateStart: start,
                dateEnd: end,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                alertCustom('error', 'Erro ao tentar buscar dados');
            },
            success: function (data) {

            }
        })
    }

});
