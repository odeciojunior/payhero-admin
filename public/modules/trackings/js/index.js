let tracking_id = 'undefined';

$(() => {

    $('#tracking-product-image').on('error', function () {
        $(this).attr('src', 'https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/product-default.png')
    });

    $(document).on('click', '.copy', function () {
        var temp = $("<input>");
        $("body").append(temp);
        temp.val($(this).html()).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Código copiado!');
    });

    $(document).on('click', '.tracking-add, .tracking-edit', function () {

        let row = $(this).parent().parent();

        row.find('.input-tracking-code')
            .removeClass('fake-label')
            .prop('readonly', false)
            .focus();

        row.find('.tracking-save, .tracking-close')
            .show();

        row.find('.tracking-detail')
            .hide();

        $(this).hide();

    });

    $(document).on('click', '.tracking-close', function () {
        let row = $(this).parent().parent();

        row.find('.input-tracking-code')
            .addClass('fake-label')
            .prop('readonly', true)
            .blur();

        row.find('.tracking-add, .tracking-detail, .tracking-edit')
            .show();

        row.find('.tracking-save, .tracking-close')
            .hide();

        $(this).hide();
    });

    $('#bt_filtro').on('click', function () {
        index();
        getResume();
    });

    let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
    let endDate = moment().format('YYYY-MM-DD');
    $('#date_updated').daterangepicker({
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        opens: 'right',
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
            'Últimos 30 dias': [moment().subtract(30, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
    });

    function getFilters(urlParams = false) {
        let data = {
            'tracking_code': $('#tracking_code').val(),
            'status': $('#status').val(),
            'project': $('#project-select').val(),
            'date_updated': $('#date_updated').val(),
            'sale': $('#sale').val().replace('#', ''),
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

    /**
     * List Projects
     */
    function getProjects() {

        loadOnAny('.page-content');

        $.ajax({
            method: 'GET',
            url: '/api/projects?select=true&affiliate=false',
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
                loadOnAny('.page-content', true);
            },
            success: response => {
                $.each(response.data, function (index, project) {
                    $('#project-select').append(`<option value="${project.id}">${project.name}</option>`)
                });
                loadOnAny('.page-content', true);
                index();
                getResume();
            }
        });
    }

    function getResume() {
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
            method: 'GET',
            url: '/api/tracking/resume?' + getFilters(true).substr(1),
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
                loadOnAny('.number', true);
            },
            success: response => {
                if (isEmpty(response.data)) {
                    alertCustom('error', 'Erro ao carregar resumo dos rastreios');
                } else {
                    let {total, posted, dispatched, out_for_delivery, delivered, exception, unknown} = response.data;

                    $('#total-trackings').text(total);
                    $('#percentual-delivered').text(delivered ? delivered + ' (' + ((delivered * 100) / total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-dispatched').text(dispatched ? dispatched + ' (' + ((dispatched * 100) / total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-posted').text(posted ? posted + ' (' + ((posted * 100) / total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-out').text(out_for_delivery ? out_for_delivery + ' (' + ((out_for_delivery * 100) / total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-exception').text(exception ? exception + ' (' + ((exception * 100) / total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-unknown').text(unknown ? unknown + ' (' + ((unknown * 100) / total).toFixed(2) + '%)' : '0 (0.00%)');
                }
                loadOnAny('.number', true);
            }
        });
    }

    function getStatusBadge(status) {
        switch (status) {
            case 1:
            case 2:
            case 4:
                return 'primary';
            case 3:
                return 'success';
            case 5:
                return 'warning';
            default:
                return 'danger';
        }
    }

    function index(link = null) {

        if (link == null) {
            link = '/api/tracking?' + getFilters(true).substr(1);
        } else {
            link = '/api/tracking' + link + getFilters(true);
        }

        loadOnTable('#dados_tabela', '#tabela_trackings');
        $.ajax({
            method: 'GET',
            url: link,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                $('#dados_tabela').html('');

                let grayRow = false;
                let lastSale = '';

                if (isEmpty(response.data)) {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='5' style='height: 70px;vertical-align: middle'> Nenhum rastreamento encontrada</td></tr>");
                } else {
                    $.each(response.data, function (index, tracking) {

                        let badge = getStatusBadge(tracking.tracking_status_enum);

                        if (lastSale !== tracking.sale) {
                            grayRow = !grayRow;
                        }

                        let dados = `<tr ${grayRow ? 'class="td-odd"' : ''}>
                                         ${
                            lastSale !== tracking.sale
                                ? `<td class="detalhes_venda pointer table-title" venda="${tracking.sale}">#${tracking.sale}</td>`
                                : `<td></td>`
                        }
                                         <td>${tracking.approved_date}</td>
                                         <td>
                                             <span style="max-width: 330px; display:block; margin:0 auto;">
                                                ${tracking.product.amount}x ${tracking.product.name} ${tracking.product.description ? '(' + tracking.product.description + ')' : ''}
                                            </span>
                                        </td>
                                         <td class="td-status">
                                            <span class="badge badge-${badge}">${tracking.tracking_status}</span>
                                         </td>
                                         <td>
                                            <input maxlength="16" minlength="10" class="form-control font-weight-bold input-tracking-code fake-label" readonly placeholder="Informe o código de rastreio" value="${tracking.tracking_code}">
                                         </td>
                                         <td class="text-md-right" style="min-width: 100px;">
                                            <a class='tracking-save pointer mr-10' title="Salvar" product='${tracking.product.id}'
                                             sale='${tracking.sale}' style="display:none"><i class='material-icons gradient'>save</i></a>
                                         ${tracking.tracking_status_enum
                            ? `<a class='tracking-edit pointer mr-10' title="Editar"><i class='material-icons gradient'>edit</i></a>
                                               <a class='tracking-detail pointer' title="Visualizar" tracking='${tracking.id}'><i class='material-icons gradient'>remove_red_eye</i></a>`
                            : `<a class='tracking-add pointer' title="Adicionar"><i class='material-icons gradient'>add_circle</i></a>`
                        }
                                           <a class='tracking-close pointer' title="Fechar" style="display:none"><i class='material-icons gradient'>close</i></a>
                                        </td>
                                 </tr>`;
                        $('#dados_tabela').append(dados);

                        lastSale = tracking.sale;
                    });

                    pagination(response, 'trackings', index);
                    $('#tabela_trackings').removeClass('table-striped');
                }
            }
        });
    }

    //modal de detalhes
    $(document).on('click', '.tracking-detail', function () {
        tracking_id = $(this).attr('tracking');

        let btnDetail = $(this);

        loadOnAny('#modal-tracking-details');
        $('#modal-tracking').modal('show');

        $.ajax({
            method: 'GET',
            url: '/api/tracking/' + $(this).attr('tracking'),
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                loadOnAny('#modal-tracking-details', true);
                errorAjaxResponse(response);
            },
            success: response => {

                let tracking = response.data;

                //preenche os campos
                $('#tracking-code').text(tracking.tracking_code);
                $('#tracking-product-image').attr('src', tracking.product.photo);
                $('#tracking-product-name').text(tracking.product.name + (tracking.product.description ? '(' + tracking.product.description + ')' : ''));
                $('#tracking-product-amount').text(tracking.amount + 'x');
                $('#tracking-delivery-address').text('Endereço: ' + tracking.delivery.street + ', ' + tracking.delivery.number);
                $('#tracking-delivery-neighborhood').text('Bairro: ' + tracking.delivery.neighborhood);
                $('#tracking-delivery-zipcode').text('CEP: ' + tracking.delivery.zip_code);
                $('#tracking-delivery-city').text('Cidade: ' + tracking.delivery.city + '/' + tracking.delivery.state);
                $('#modal-tracking-details .btn-notify-trackingcode').attr('tracking', tracking.id);

                if (tracking.link) {
                    $('#link-tracking a').attr('href', tracking.link);
                    $('#link-tracking').show();
                } else {
                    $('#link-tracking').hide();
                }

                $('#table-checkpoint').html('');
                if (!isEmpty(tracking.checkpoints)) {
                    for (let checkpoint of tracking.checkpoints) {

                        $('#table-checkpoint').append(`<tr>
                                                          <td>${checkpoint.created_at}</td>
                                                          <td>
                                                              <span class="badge badge-${getStatusBadge(checkpoint.tracking_status_enum)}">${checkpoint.tracking_status}</span>
                                                          </td>
                                                          <td>${checkpoint.event}</td>
                                                      </tr>`);
                    }
                }

                let statusBadge = btnDetail.parent()
                    .parent()
                    .find('td .badge');

                if (statusBadge.html() !== tracking.tracking_status) {
                    statusBadge.removeClass('badge-success badge-warning badge-danger badge-primary')
                        .addClass('badge-' + getStatusBadge(tracking.tracking_status_enum))
                        .html(tracking.tracking_status);
                }

                loadOnAny('#modal-tracking-details', true);
            }
        });
    });

    $(document).on('click', '.input-tracking-code', function () {
        let row = $(this).parent().parent();
        $('.tracking-close').click();
        row.find('.tracking-edit, .tracking-add').click();
    });

    //salvar tracking
    $(document).on('click', '.tracking-save', function () {

        let btnSave = $(this);
        let row = btnSave.parent().parent();
        btnSave.prop('disabled', true);

        let tracking_code = btnSave.parent().parent().find('.input-tracking-code').val();
        let saleId = btnSave.attr('sale');
        let productId = btnSave.attr('product');

        $.ajax({
            method: "POST",
            url: '/api/tracking',
            data: {tracking_code: tracking_code, sale_id: saleId, product_id: productId},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                btnSave.prop('disabled', false);
                errorAjaxResponse(response);
            },
            success: (response) => {

                if (!isEmpty(response.data.tracking_status)) {

                    row.find('.tracking-close')
                        .click();

                    let tracking = response.data;

                    let statusBadge = btnSave.parent()
                        .parent()
                        .find('td .badge');

                    if (statusBadge.html() !== tracking.tracking_status) {
                        statusBadge.removeClass('badge-success badge-warning badge-danger badge-primary')
                            .addClass('badge-' + getStatusBadge(tracking.tracking_status_enum))
                            .html(tracking.tracking_status);
                    }

                    alertCustom('success', 'Código de rastreio salvo com sucesso')
                }
                btnSave.prop('disabled', false);
            }
        });
    });

    //enviar e-mail com o codigo de rastreio
    $(document).on('click', '#modal-tracking-details .btn-notify-trackingcode', function () {
        $.ajax({
            method: "POST",
            url: '/api/tracking/notify/' + tracking_id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: () => {
                alertCustom('success', 'Notificação enviada com sucesso');
            }
        });
    });

    //exportar excel
    $("#btn-export-csv").on("click", function () {
        trackingsExport('csv');
    });

    $("#btn-export-xls").on("click", function () {
        trackingsExport('xlsx');
    });

    function trackingsExport(fileFormat) {
        let data = getFilters();
        data['format'] = fileFormat;
        $.ajax({
            method: "POST",
            url: '/api/tracking/export',
            data: data,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                $('#export-email').text(response.email);
                $('#alert-export').show()
                    .shake();
            },
        });
    }

    //importar excel
    $('#btn-import-xls').on('click', function () {
        $('#input-import-xls').click();
    });

    $('#input-import-xls').on('change', function () {
        $('#btn-import-xls').prop('disabled', true);
        let form = new FormData();
        form.append('import.xlsx', this.files[0]);
        $(this).val(null);
        $.ajax({
            url: '/api/tracking/import',
            type: 'post',
            processData: false,
            contentType: false,
            cache: false,
            data: form,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                $('#btn-import-xls').prop('disabled', false);
                errorAjaxResponse(response);
            },
            success: response => {
                $('#btn-import-xls').prop('disabled', false);
                alertCustom('success', 'A importação começou! Você receberá uma notificação quando tudo estiver pronto!')
            },
        });
    });
    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            index();
            getResume();
        }
    });
});
