var currentPage  = null;
var atualizar    = null;

$(document).ready(function () {

    //checkbox
    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
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
            'payment_method': $("#forma").val(),
            'status': $("#status").val(),
            'client': $("#comprador").val(),
            'customer_document': $("#customer_document").val(),
            'date_type': $("#date_type").val(),
            'date_range': $("#date_range").val(),
            'transaction': $("#transaction").val().replace('#', ''),
            'plan': $('#plan').val(),
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

    // FIM - COMPORTAMENTOS DA JANELA

    getProjects();

    // Obtem o os campos dos filtros
    function getProjects() {
        loadOnAny('.page');
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
                    $("#project-empty").hide();
                    $("#project-not-empty").show();

                    $.each(response.data, function (index, project) {
                        $('#projeto').append('<option value="' + project.id + '">' + project.name + '</option>')
                    });
                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }

                loadOnAny('.page', true);
            }
        });
    }

    // Obtem lista de vendas
    atualizar = function (link = null) {

        currentPage = link;
        let updateResume = true;
        loadOnTable('#dados_tabela', '#tabela_vendas');

        if (link == null) {
            link = '/api/reports/blockedbalance?' + getFilters(true).substr(1);
        } else {
            link = '/api/reports/blockedbalance' + link + getFilters(true);
            updateResume = false;
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
                $('#dados_tabela').html('');
                $('#tabela_vendas').addClass('table-striped');

                let statusArray = {
                    1: 'success',
                    6: 'primary',
                    7: 'danger',
                    8: 'warning',
                    4: 'danger',
                    3: 'danger',
                    2: 'pendente',
                    20: 'antifraude',
                    12: 'success',
                    20: 'antifraude',
                    22: 'danger',
                    23: 'warning',
                    24: 'antifraude',
                };

                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, value) {

                        dados = `  <tr>
                                    <td class='display-sm-none display-m-none display-lg-none text-center'>
                                        ${value.sale_code}
                                        ${value.upsell ? '<span class="text-muted font-size-10">(Upsell)</span>' : ''}
                                    </td>
                                    <td>${value.project}</td>
                                    <td>${value.product}${value.affiliate != null && value.user_sale_type == 'producer' ? `<br><small>(Afiliado: ${value.affiliate})</small>` : ''}</td>
                                    <td class='display-sm-none display-m-none display-lg-none'>${value.client}</td>
                                    <td>
                                        <img src='/modules/global/img/cartoes/${value.brand}.png'  style='width: 45px'>
                                    </td>
                                    <td>
                                       <div class="d-flex align-items-center">
                                            <span class="badge badge-${statusArray[value.status]} ${value.status_translate === 'Pendente' ? 'boleto-pending' : ''}" ${value.status_translate === 'Pendente' ? 'status="' + value.status_translate + '" sale="' + value.id_default + '"' : ''}>${value.status_translate}</span>
                                               ${value.is_chargeback_recovered && value.status_translate === 'Aprovado' ? `
                                                <img class="orange-gradient ml-10" width="20px" src="/modules/global/img/svg/chargeback.svg" title="Chargeback recuperado">`
                                                : ''}
                                        </div>
                                    </td>
                                    <td class='display-sm-none display-m-none'>${value.start_date}</td>
                                    <td class='display-sm-none'>${value.end_date}</td>
                                    <td style='white-space: nowrap'><b>${value.total_paid}</b></td>
                                    <td>
                                        ${value.reason_blocked}
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
            }
        });

        if (updateResume) {
            blockedResume();
        }

    }

    $("#projeto").on('change', function () {
        let value = $(this).val();
        $("#plan").val(null).trigger('change');
    });
    //Search plan
    $('#plan').select2({
        placeholder: 'Nome do plano',
        // multiple: true,
        allowClear: true,
        language: {
            noResults: function () {
                return 'Nenhum plano encontrado';
            },
            searching: function () {
                return 'Procurando...';
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'plan',
                    search: params.term,
                    project_id: $("#projeto").val(),
                };
            },
            method: "GET",
            url: "/api/sales/user-plans",
            delay: 300,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name + (obj.description ? ' - ' + obj.description : '')};
                    })
                };
            },
        }
    });
    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });

    function blockedResume() {

        loadOnAny('.number', false, {
            styles: {
                container: {
                    minHeight: '32px',
                    height: 'auto'
                },
                loader: {
                    width: '20px',
                    height: '20px',
                    borderWidth: '4px'
                },
            }
        });

        $.ajax({
            method: "GET",
            url: '/api/reports/blockedresume',
            data: getFilters(),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.number', true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny('.number', true);
                $('#total_sales').text('0');
                $('#commission_blocked, #total').text('R$ 0,00');
                if (response.total_sales) {
                    $('#total_sales, #commission_blocked, #total').text('');
                    $('#total_sales').text(response.total_sales);
                    $('#commission_blocked').text(`R$ ${response.commission}`);
                    $('#total').text(`R$ ${response.total}`);
                }
            }
        });
    }
});
