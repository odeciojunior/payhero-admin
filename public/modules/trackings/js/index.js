let tracking_id = 'undefined';

$(() => {

    $('#tracking-product-image').on('error', function () {
        $(this).attr('src', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/product-default.png')
    });

    $(document).on('click', '.copy', function () {
        let temp = $("<input>");
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
        getBlockedBalance();
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
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Vitalício': [moment('2018-01-01 00:00:00'), moment()]
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
            'transaction_status': $("#status_commission").val(),
            'problem': $('#tracking_problem').prop('checked') ? 1 : 0
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

        loadingOnScreen();

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
                loadingOnScreenRemove();
            },
            success: response => {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel").show()

                    $.each(response.data, function (i, project) {
                        $("#project-select").append($('<option>', {
                            value: project.id,
                            text: project.name
                        }));
                    });

                    index();
                    getResume();
                    getBlockedBalance();
                } else {
                    $("#export-excel").hide()
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }

                loadingOnScreenRemove();
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

    function getBlockedBalance() {

        $('#alert-blockedbalance').hide();
        //TODO: descomentar quando liberar
        /*$.ajax({
            method: 'GET',
            url: '/api/tracking/blockedbalance',
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                if(response.sales) {
                    $('#blocked-balance').html(response.total);
                    $('#blocked-balance-sales').html(response.sales);
                    $('#alert-blockedbalance').show()
                        .shake();
                }
            }
        });*/
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

    function getSystemStatus(status) {
        switch (status) {
            case 2:
                return `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="O código foi reconhecido pela transportadora mas, ainda não teve nenhuma movimentação. Essa informação pode ser atualizada nos próximos dias">report_problem</i>`;
            case 3:
                return `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="O código não foi reconhecido por nenhuma transportadora">report_problem</i>`;
            case 4:
                return `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="A data de postagem da remessa é anterior a data da venda">report_problem</i>`;
            case 5:
                return `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="Já existe uma venda com esse código de rastreio cadastrado">report_problem</i>`;
            default :
                return '';
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
                $('#tabela_trackings').addClass('table-striped');

                let grayRow = false;
                let lastSale = '';

                if (isEmpty(response.data)) {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='6' style='height: 70px;vertical-align: middle'> Nenhum rastreamento encontrado</td></tr>");
                    $('#pagination-trackings').html("");
                } else {
                    $.each(response.data, function (index, tracking) {

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
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-${getStatusBadge(tracking.tracking_status_enum)}">${tracking.tracking_status}</span>
                                                ${getSystemStatus(tracking.system_status_enum)}
                                                ${
                                                    tracking.is_chargeback_recovered
                                                    ? '<img class="orange-gradient ml-10" width="20px" src="/modules/global/img/svg/chargeback.svg" title="Chargeback recuperado">'
                                                    : ''
                                                }
                                            </div>
                                         </td>
                                         <td>
                                            <input maxlength="18" minlength="10" class="form-control font-weight-bold input-tracking-code fake-label" readonly placeholder="Informe o código de rastreio" value="${tracking.tracking_code}">
                                         </td>
                                         <td class="text-center">
                                            <a class='tracking-save pointer mr-10' title="Salvar" pps='${tracking.pps_id}'
                                             style="display:none"><i class='material-icons gradient'>save</i></a>
                                             ${
                                                 tracking.tracking_status_enum
                                                     ? `<a class='tracking-edit pointer mr-10' title="Editar"><span class="o-edit-1"></span></a>
                                                        <a class='tracking-detail pointer' title="Visualizar" tracking='${tracking.id}'><span class="o-eye-1"></span></a>`
                                                     : `<a class='tracking-add pointer' title="Adicionar"><span class='o-add-1'></span></a>`
                                             }
                                            <a class='tracking-close pointer' title="Fechar" style="display:none"><i class='material-icons gradient'>close</i></a>
                                        </td>
                                 </tr>`;
                        $('#dados_tabela').append(dados);

                        lastSale = tracking.sale;
                    });

                    pagination(response, 'trackings', index);
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

                /*let statusBadge = btnDetail.parent()
                    .parent()
                    .find('td .badge');

                if (statusBadge.html() !== tracking.tracking_status) {
                    statusBadge.removeClass('badge-success badge-warning badge-danger badge-primary')
                        .addClass('badge-' + getStatusBadge(tracking.tracking_status_enum))
                        .html(tracking.tracking_status);
                }*/

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
        btnSave.prop('disabled', true);

        let tracking_code = btnSave.parent().parent().find('.input-tracking-code').val();
        let ppsId = btnSave.attr('pps');

        $.ajax({
            method: "POST",
            url: '/api/tracking',
            data: {tracking_code: tracking_code, product_plan_sale_id: ppsId},
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

                    let tracking = response.data;

                    let td = btnSave.parent();

                    td.find('.tracking-add, .tracking-edit, .tracking-detail')
                        .remove();

                    td.find('.tracking-close')
                        .click();

                    let buttons = `<a class='tracking-edit pointer mr-10' title="Editar"><span class="o-edit-1"></span></a>
                                   <a class='tracking-detail pointer' title="Visualizar" tracking='${tracking.id}'><span class="o-eye-1"></span></a>`;

                    td.append(buttons);

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
                btnSave.prop('disabled', false)
                    .hide();
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
            getBlockedBalance();
        }
    });
});
