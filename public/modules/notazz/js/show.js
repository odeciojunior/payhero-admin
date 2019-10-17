$(document).ready(function () {

    // COMPORTAMENTOS DA JANELA

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar(getFilters(true));
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

    $(document).on({
            mouseenter: function () {
                $(this).css('cursor', 'pointer').text('Regerar');
                $(this).css("background", "#545B62");
            },
            mouseleave: function () {
                var status = $(this).attr('status');
                $(this).removeAttr("style");
                $(this).text(status);
            }
        }, '.boleto-pending'
    );

    function getFilters(urlParams = false) {
        let data = {
            'status': $("#status").val(),
            'client': $("#comprador").val(),
            'date_range': $("#date_range").val(),
            'transaction': $("#transaction").val(),
        };

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += '&' + param + '=' + data[param];
            }
            return params;
        } else {
            return data;
        }
    }

    // FIM - COMPORTAMENTOS DA JANELA

    loadOnAny('.page-content', true);
    atualizar();

    function atualizar() {

        loadOnTable('#dados_tabela', '#tabela_vendas');

        // if (data == null) {
        //     link = '/api/apps/notazz/report/' + extractIdFromPathName();
        // } else {
        //     link = '/api/apps/notazz/report/' + extractIdFromPathName() + '/';
        // }

        // let data = {
        //     'status': $("#status").val(),
        //     'client': $("#comprador").val(),
        //     'date_range': $("#date_range").val(),
        //     'transaction': $("#transaction").val(),
        // }

        $.ajax({
            method: "GET",
            url: '/api/apps/notazz/report/' + extractIdFromPathName() + '/?' + getFilters(true),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#dados_tabela').html('');
                $('#tabela_vendas').addClass('table-striped');

                var statusArray = {
                    1: 'success',
                    6: 'primary',
                    4: 'danger',
                    3: 'danger',
                    2: 'pendente'
                };

                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, value) {
                        dados = `<tr>
                                    <td class='display-sm-none display-m-none display-lg-none'>${value.sale_code}</td>
                                    <td>${value.project}</td>
                                    <td>${value.product}</td>
                                    <td class='display-sm-none display-m-none display-lg-none'>${value.client}</td>
                                    <td>
                                        <img src='/modules/global/img/cartoes/${value.brand}.png'  style='width: 60px'>
                                    </td>
                                    <td>
                                        <span class="badge badge-${statusArray[value.status]} ${value.status_translate === 'Pendente' ? 'boleto-pending' : ''}" ${value.status_translate === 'Pendente' ? 'status="' + value.status_translate + '" sale="' + value.id_default + '"' : ''}>${value.status_translate}</span>
                                    </td>
                                    <td class='display-sm-none display-m-none'>${value.start_date}</td>
                                    <td class='display-sm-none'>${value.end_date}</td>
                                    <td style='white-space: nowrap'><b>${value.total_paid}</b></td>
                                    <td>
                                        <a role='button' class='detalhes_venda pointer' venda='${value.id}'><i class='material-icons gradient'>remove_red_eye</i></button></a>
                                    </td>
                                </tr>`;

                        $("#dados_tabela").append(dados);
                    });

                    $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                    $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));
                } else {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }
                pagination(response, 'sales', atualizar);
                $('#export-excel').show();
            }
        });

    }

});
