let currentPage = null;
let updateList = null;

let statusArray = {
    1: 'success',
    6: 'primary',
    7: 'danger',
    4: 'danger',
    3: 'danger',
    2: 'pendente',
    20: 'antifraude',
    99: 'blacklist'
};

$(document).ready(function () {

    $("#filtros").on('click', function () {
        if ($("#div_filtros").is(':visible')) {
            $("#div").slideUp();
        } else {
            $("#div").slideDown();
        }
    });

    $("#bt_filtro").on('click', function (event) {
        event.preventDefault();
        updateList();
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
            'project': $('#projeto').val(),
            'payment_method': $('#forma').val(),
            'status': $('#status').val(),
            'client': $('#comprador').val(),
            'date_type': $('#date_type').val(),
            'date_range': $('#date_range').val(),
            'transaction': $('#transaction').val().replace('#', ''),
        };

        if (urlParams) {
            let params = '';
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
            method: 'GET',
            url: 'api/projects?select=true',
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            }, success: function success(response) {
                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, project) {
                        $('#projeto').append(`<option value='${project.id}'>${project.name}</option>`);
                    });
                    loadOnAny('.page-content', true);
                    updateList();
                }
            }
        });
    }

    // lista
    updateList = function (link = null) {
        currentPage = link;

        loadOnTable('#dados_tabela', '#tabela_vendas');

        if (link == null) {
            link = '/api/salesblacklistantifraud?' + getFilters(true).substr(1);
        } else {
            link = '/api/salesblacklistantifraud' + link + getFilters(true);
        }

        $.ajax({
            method: 'GET',
            url: link,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $('#dados_tabela').html('');
                $('#tabela_vendas').addClass('table-striped');

                if (!isEmpty(response.data)) {
                    console.log(response.data);
                    let data = '';
                    $.each(response.data, function (index, value) {
                        let tableClass = '';
                        data += `
                        <tr class='${tableClass}'>
                            <td class='display-sm-none display-m-none display-lg-none text-center'>
                                ${valeu.sale_code}
                                ${valeu.upsell ? '<span class="text-muted font-size-10">(Upsell)</span>' : ''}
                            </td>
                            <td>${value.project}</td>
                            <td>${value.product}${value.affiliate != null && value.user_sale_type == 'producer' ? `<br><small>(Afiliado: ${valeu.affiliate}</small>` : ''}</td>
                            <td class='display-sm-one display-m-none display-lg-none'>${value.client}</td>
                            <td>
                                <img src='/modules/global/img/cartoes/${value.brand}.png' style='width: 45px'>
                            </td>
                            <td>
                                <span class="badge badge-${statusArray[value.status]} ${value.status_translate === 'Pendente' ? 'boleto-pending' : ''}" ${value.status_translate === 'Pendente' ? 'status="' + value.status_translate + '" sale="' + value.id_default + '"' : ''}>7
                                    ${value.status_translate}
                                </span>
                            </td>
                            <td class='display-sm-one display-m-one'>${value.start_date}</td>
                            <td class='display-sm-one'>${value.end_date}</td>
                            <td style='white-space: nowrap'><b>${value.total_paid}</b></td>
                            <td>
                                <a role='button' class='detalhes_venda pointer' venda='${value.id}'>
                                    <i class='material-icons gradient'>remove_red_eye</i>
                                </a>
                            </td>
                        </tr>`;
                    });

                    $('#dados_tabela').append(data);

                    $('#date').val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                    $('#date').attr('min', moment(new Date()).format("YYYY-MM-DD"));
                } else {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }

                pagination(response, 'sales-atifraud-blacklist', updateList);
            }
        });
    }

});
