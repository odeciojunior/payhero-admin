$(document).ready(function () {

    var currentPage;
    // COMPORTAMENTOS DA JANELA

    $("#bt_get_csv").on("click", function () {
        salesExport('csv');
    });

    $("#bt_get_xls").on("click", function () {
        salesExport('xls');
    });

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
            'project': $("#projeto").val(),
            'payment_method': $("#forma").val(),
            'status': $("#status").val(),
            'client': $("#comprador").val(),
            'date_type': $("#date_type").val(),
            'date_range': $("#date_range").val(),
            'transaction': $("#transaction").val().replace('#', ''),
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

    //Carrega o modal para regerar boleto
    $(document).on('click', '.boleto-pending', function () {

        let saleId = $(this).attr('sale');
        $('#modal_regerar_boleto #bt_send').attr('sale', saleId);

        $('#modal_regerar_boleto').modal('show');
    });

    //Salvar boleto regerado
    $('#bt_send').on('click', function () {
        loadingOnScreen();
        let saleId = $(this).attr('sale');
        $.ajax({
            method: "POST",
            url: "/api/recovery/regenerateboleto",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                saleId: saleId,
                date: $('#date').val(),
                discountType: $("#discount_type").val(),
                discountValue: $("#discount_value").val()
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                $("#modal_regerar_boleto").modal('hide');
                atualizar(currentPage);
            }
        });
    });

    // Obtem o os campos dos filtros
    function getProjects() {
        loadOnAny('.page-content');
        $.ajax({
            method: "GET",
            url: '/api/projects',

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

    // Obtem lista de vendas
    function atualizar(link = null) {

        currentPage = link;

        let updateResume = true;
        loadOnTable('#dados_tabela', '#tabela_vendas');

        if (link == null) {
            link = '/api/sales?' + getFilters(true).substr(1);
        } else {
            link = '/api/sales' + link + getFilters(true);
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
                    4: 'danger',
                    3: 'danger',
                    2: 'pendente',
                    20: 'pendente'
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

        if (updateResume) {
            salesResume();
        }
    }

    // Download do relatorio
    function salesExport(fileFormat) {

        let data = getFilters();
        data['format'] = fileFormat;
        $.ajax({
            method: "POST",
            url: '/api/sales/export',
            xhrFields: {
                responseType: 'blob'
            },
            data: data,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, textStatus, request) {
                downloadFile(response, request);
            }
        });
    }

    // Resumo
    function salesResume() {

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
            url: '/api/sales/resume',
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
                $('#total-sales').text('0');
                $('#comission, #total').text('R$ 0,00');
                if (response.total_sales) {
                    $('#total-sales, #comission, #total').text('');
                    $('#total-sales').text(response.total_sales);
                    if (!isEmpty(response.real)) {
                        $('#comission').append(`<div>R$ ${response.real.comission}</div>`);
                        $('#total').append(`<div>R$ ${response.real.total}</div>`);
                    }
                    if (!isEmpty(response.dolar)) {
                        $('#comission').append(`<div>$ ${response.dolar.comission}</div>`);
                        $('#total').append(`<div>$ ${response.dolar.total}</div>`);
                    }
                }

            }
        });
    }
});
