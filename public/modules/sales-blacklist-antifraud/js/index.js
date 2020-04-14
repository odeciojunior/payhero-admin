let currentPage = null;
let updateList = null;

let statusArray = {
    1: 'success',
    2: 'pendente',
    3: 'danger',
    4: 'danger',
    6: 'primary',
    7: 'danger',
    10: 'dark',
    20: 'primary',
    21: 'primary'

};

let statusTranslated = {
    10: 'BlackList',
    21: 'Cancelado Antifraude'
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
            'date_range': $("#date_range").val(),
            'status': $('#status').val(),
            'client': $('#comprador').val(),
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
            link = '/api/blacklistantifraud?' + getFilters(true).substr(1);
        } else {
            link = '/api/blacklistantifraud' + link + getFilters(true);
        }
        $('#pagination-sales-atifraud-blacklist').hide();

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
                let showHideBlacklist = 'none';

                if (statusTranslated[$('#status').val()] == 'BlackList') {
                    $(".blacklist").show();
                    showHideBlacklist = 'block';
                } else {
                    $(".blacklist").hide();
                    showHideBlacklist = 'none';
                }

                if (isEmpty(response.data)) {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                } else {

                    let data = '';
                    $.each(response.data, function (index, value) {
                        let tableClass = '';

                        /*  const objectArray = Object.entries(value.black_list);
                          let valuesObject = ``;

                          objectArray.forEach(([key, value]) => {
                              valuesObject += `${Object.keys(value)} - ${Object.values(value)}`;
                          });
                        */
                        data += `
                            <tr class='${tableClass} text-center'>
                                <td class='display-sm-none display-m-none display-lg-none text-center'>
                                    ${value.sale_code}
                                </td>
                                <td>${value.project}</td>
                                <td>${value.product}</td>
                                <td class='display-sm-one display-m-none display-lg-none'>${value.customer}</td>
                                /*<td style='display:${showHideBlacklist}'>
                                      ${valuesObject}
                                </td>*/
                                <td class='display-sm-one display-m-one'>${value.start_date}</td>
                                <td>
                                    <a role='button' class='detalhes-black-antifraud pointer' sale='${value.sale_code}'>
                                        <i class='material-icons gradient'>remove_red_eye</i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });

                    $('#dados_tabela').append(data);

                    pagination(response, 'sales-atifraud-blacklist', updateList);
                    $('#pagination-sales-atifraud-blacklist').show();

                    $('#date').val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                    $('#date').attr('min', moment(new Date()).format("YYYY-MM-DD"));
                }

            }
        });
    }

});
