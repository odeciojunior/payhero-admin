$(document).ready(function () {

    // COMPORTAMENTOS DA JANELA

    $("#bt_get_csv").on("click", function () {
        salesExport('csv');
    });

    $("#bt_get_xls").on("click", function () {
        salesExport('xlsx');
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

    function downloadFile(response, request) {
        let type = request.getResponseHeader("Content-Type");
        // Get file name
        let contentDisposition = request.getResponseHeader("Content-Disposition");
        let fileName = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
        fileName = fileName ? fileName[0].replace("filename=", "") : '';

        var a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);
        a.href = window.URL.createObjectURL(new Blob([response], {type: type}));
        a.setAttribute("download", fileName);
        a.click();
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }

    // FIM - COMPORTAMENTOS DA JANELA

    getFilters();

    atualizar();

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
                window.location = '/sales';
            }
        });
    });

    // Obtem o os campos dos filtros
    function getFilters() {
        loadOnAny('.page-content');
        $.ajax({
            method: "GET",
            url: "/api/projects/user-projects",
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
                if (response.data == '') {
                    $('#export-excel, .page-content').hide();
                    $('.content-error').show();
                } else {
                    $.each(response.data, function (index, project) {
                        $('#projeto').append('<option value="' + project.id + '">' + project.name + '</option>')
                    });
                }
                loadOnAny('.page-content', true);
                $('#export-excel').show();
                atualizar();
            }
        });
    }

    // Obtem lista de vendas
    function atualizar(link = null) {

        loadOnTable('#dados_tabela', '#tabela_vendas');

        if (link == null) {
            link = '/api/sales?' + 'project=' + $("#projeto option:selected ").val() + '&transaction=' + $("#transaction").val().replace('#', '') + '&payment_method=' + $("#forma option:selected").val() + '&status=' + $("#status option:selected").val() + '&client=' + $("#comprador").val() + '&date_type=' + $("#date_type").val() + '&date_range=' + $("#date_range").val();
        } else {
            link = '/api/sales' + link + '&project=' + $("#projeto option:selected ").val() + '&transaction=' + $("#transaction").val().replace('#', '') + '&payment_method=' + $("#forma option:selected").val() + '&status=' + $("#status option:selected").val() + '&client=' + $("#comprador").val() + '&date_type=' + $("#date_type").val() + '&date_range=' + $("#date_range").val();
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

                var statusArray = {
                    1: 'success',
                    6: 'primary',
                    4: 'danger',
                    3: 'danger',
                    2: 'pendente'
                };

                if (response.data == '') {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                } else {
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

                    pagination(response, 'sales', atualizar);
                }
            }
        });
    }

    // Download do relatorio
    function salesExport(fileFormat) {

        let data = {
            'select_project': $("#projeto").val(),
            'select_payment_method': $("#forma").val(),
            'sale_status': $("#status").val(),
            'client': $("#comprador").val(),
            'date_type': $("#date_type").val(),
            'date_range': $("#date_range").val(),
            'format': fileFormat
        };
        $.ajax({
            method: "POST",
            url: '/api/sales/export',
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
});
