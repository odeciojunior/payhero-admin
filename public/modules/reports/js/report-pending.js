var currentPage = null;
var atualizar = null;

$(document).ready(function () {

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
    });

    let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
    let endDate = moment().format('YYYY-MM-DD');
    $('#date_range').daterangepicker({
        startDate: moment().subtract(30, 'days'),
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
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
    });

    function getFilters(urlParams = false) {
        let data = {
            'project': $("#projeto").val(),
            'sale_code': $("#sale_code").val(),
            'date_range': $("#date_range").val(),
        };

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += '&' + param + '=' + data[param];
            }
            return encodeURI(params);
        } else {
            return data;
        }
    }

    getProjects();

    function getProjects() {
        loadOnAny('.page-content');
        $.ajax({
            method: "GET",
            url: '/api/projects?select=true',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, project) {
                        $('#projeto').append('<option value="' + project.id + '">' + project.name + '</option>')
                    });
                }
                loadOnAny('.page-content', true);
                atualizar();
            }
        });
    }

    atualizar = function (link = null) {

        currentPage = link;

        loadOnTable('#body-table-pending', '.table-pending');

        if (link == null) {
            link = '/api/reports/pending-balance?' + getFilters(true).substr(1);
        } else {
            link = '/api/reports/pending-balance' + link + getFilters(true);
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#body-table-pending').html('');
                $('.table-pending').addClass('table-striped');
                    console.log(response)

                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, value) {

                        var dados = `  <tr>
                                    <td>${value.sale_code}</td>
                                    <td>${value.project}</td>
                                    <td>
                                        <img src='/modules/global/img/cartoes/${value.brand}.png' alt="${value.brand}"  style='width: 45px'>
                                    </td>
                                    <td>${value.start_date}</td>
                                    <td>${value.end_date}</td>
                                    <td>${value.value}</td>
                                    <td>
                                        <a role='button' class='detalhes_venda pointer' venda='${value.id}'><i class='material-icons gradient'>remove_red_eye</i></button></a>
                                    </td>
                                </tr>`;

                        $("#body-table-pending").append(dados);
                    });

                    $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                    $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));
                } else {
                    $('#body-table-pending').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda efetuada encontrada</td></tr>");
                }
                pagination(response, 'pending', atualizar);
            }
        });
    }

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });
});
