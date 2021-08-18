// CORES DO GRAFICO
const chartDefaultColorsLabel = [
    'rgba(51, 99, 143, 1)', // POSTADO (DARK-BLUE)
    'rgba(255, 205, 27, 1)', // EM TRÂNSITO (YELLOW)
    'rgba(0, 177, 255, 1)', // SAIU PARA ENTREGA (LIGHT-BLUE)
    'rgba(255, 47, 47, 1)', // PROBLEMA NA ENTREGA (RED)
    'rgba(185, 185, 185, 1)', // NAO INFORMADO (GRAY)
    'rgba(27, 228, 168, 1)', // ENTREGUES (LIGHT GREEN)
];

//CORES DA LEGENDA DO GRAFICO
let tracking_id = 'undefined';
const statusEnum = {
    1: 'statusPosted',
    2: 'statusInTransit',
    3: 'statusDelivered',
    4: 'statusOnDelivery',
    5: 'statusProblem',
    '': 'statusWithoutInfo',
};

//ICONES DO STATUS
const systemStatus = {
    1: '',
    2: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="O código foi reconhecido pela transportadora mas, ainda não teve nenhuma movimentação. Essa informação pode ser atualizada nos próximos dias">report_problem</i>`,
    3: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="O código não foi reconhecido por nenhuma transportadora">report_problem</i>`,
    4: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="A data de postagem da remessa é anterior a data da venda">report_problem</i>`,
    5: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" title="Já existe uma venda com esse código de rastreio cadastrado">report_problem</i>`,
    '': '',
}

$(() => {

    $('#tracking-product-image').on('error', function () {
        $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg')
    });

    $('#sale').on('change paste keyup select', function () {
        let val = $(this).val();

        if (val === '') {
            $('#date_updated').attr('disabled', false).removeClass('disableFields');
        } else {
            $('#date_updated').attr('disabled', true).addClass('disableFields');
        }
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
            .prop('readonly', true)
            .blur();

        if($(this).data('code').length < 1){
            row.find('.input-tracking-code').addClass('fake-label');
        }

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
                    $("#project-not-empty").removeClass("d-none");
                    $("#export-excel").show()

                    $.each(response.data, function (i, project) {
                        $("#project-select").append($('<option>', {
                            value: project.id,
                            text: project.name
                        }));
                    });

                    index();
                    getResume();
                } else {
                    $("#export-excel").hide()
                    $("#project-empty").removeClass("d-none");
                }

                loadingOnScreenRemove();
            }
        });
    }

    //CARD RESUMO DE ENTREGAS E GRAFICO
    //FORMATAR NUMERO INTEIRO
    function numberWithDecimal(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    //CRIANDO GRAFICO
    let myChart = null;
    function inicializeChart(colors, dataValues) {
        if (myChart !== null) {
            myChart.destroy();
        }
        const ctx = document.getElementById('myChart');
        myChart = new Chart(ctx, {
            id: 'custom_canvas_background_color',
            type: 'doughnut',
            data: {
                labels: ['Postados: ', 'Em trânsito: ', 'Saiu para entrega: ', 'Problema na entrega: ', 'Não informado: ', 'Entregues: '],
                datasets: [{
                    data: dataValues,
                    backgroundColor: chartDefaultColorsLabel,
                    borderColor: chartDefaultColorsLabel,
                    borderWidth: 1,
                    cutout: "83%",
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {display: false},
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            title: (tooltipItem) => `${tooltipItem[0].label}`,
                            label: (tooltipItem) => `  ${numberWithDecimal(tooltipItem.dataset.data[tooltipItem.dataIndex])}`
                        }
                    },
                },
            }
        });

    }

    function showLoading(loadOnAny,loadingSelector,loadingSettings ){
        loadOnAny(loadingSelector, false, loadingSettings);
        $('#graphic-loading').append($('.loader-any-container')[6]).show().css('z-index','2');
    }

    //GERANDO DADOS DO CARD E DO GRAFICO
    function getResume() {
        let loadingSelector = '#percentual-posted, #percentual-dispatched, #percentual-out, #percentual-exception, #percentual-unknown, #percentual-delivered, #graphic-loading';
        let loadingSettings = {
            styles: {
                container: {
                    height: '36px',
                    minHeight: "0px",
                    justifyContent: "center",
                },
                loader: {
                    width: '20px',
                    height: '20px',
                    borderWidth: '3px',
                }
            }
        };

        showLoading(loadOnAny,loadingSelector,loadingSettings);

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
                inicializeChart(chartDefaultColorsLabel, [1, 0, 0, 0, 0, 0]);
                loadOnAny(loadingSelector, true);
                $('#graphic-loading').append($('.loader-any-container')[6]).hide();

            },
            success: response => {
                if (isEmpty(response.data)) {
                    alertCustom('error', 'Erro ao carregar resumo dos rastreios');
                    inicializeChart(chartDefaultColorsLabel, [1, 0, 0, 0, 0, 0]);
                    return;
                }
                setDataView(response.data);
                loadOnAny(loadingSelector, true);
                $('#graphic-loading').append($('.loader-any-container')[6]).hide();

            }
        });
    }

    function verifyValueIsZero(values) {
        if (values < 1 || typeof values == null) {
            return true;
        }
        return false;
    }

    function setDataView(data) {
        let {total, posted, dispatched, out_for_delivery, delivered, exception, unknown} = data;
        let thousand = 10000;

        $('#total-products').text(total > thousand ? `${parseFloat(numberWithDecimal(total)).toFixed(1)}K` : numberWithDecimal(total));

        $('#percentual-posted .resume-number').html(posted <= 0 ? posted = 0 : posted = numberWithDecimal(posted));

        $('#percentual-dispatched .resume-number').html(dispatched <= 0 ? dispatched = 0 : dispatched = numberWithDecimal(dispatched));
        
        $('#percentual-out .resume-number').html(out_for_delivery <= 0 ? out_for_delivery = 0 : out_for_delivery = numberWithDecimal(out_for_delivery));

        $('#percentual-exception .resume-number').html(exception <= 0 ? exception = 0 : exception = numberWithDecimal(exception));
        
        $('#percentual-unknown .resume-number').html(unknown <= 0 ? unknown = 0 : unknown = numberWithDecimal(unknown));

        $('#percentual-delivered .resume-number').html(delivered <= 0 ? delivered = 0 : delivered = numberWithDecimal(delivered));

        //add this line here for all $('#percentual-delivered .resume-percentual').html('(' + (delivered ? (delivered * total / 100).toFixed(2) : '0.00') + '%)');
        //add in html <span class="resume-percentual">(0.00%)</span> to show per cent

        if (verifyValueIsZero(data.total)) {
            if ($('#noData').length > 0) {
                return;
            }
            $('#dataCharts').append('<img id="noData" src="/modules/global/img/sem-dados.svg" />')
            $('#data-labels').append('<span id="warning-text" class="d-flex" style="margin-top: 28%;"> Nenhum rastreamento encontrado </span>')
            $('#myChart, .labels, .total-container').hide();

        } else {
            if ($('#noData').length > 0) {
                $('#noData, #warning-text').remove();
            }

            $('#myChart, .labels, .total-container').show();
            inicializeChart(chartDefaultColorsLabel, [posted, dispatched, out_for_delivery, exception, unknown, delivered]);
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
                $('#pagination-trackings').html("");


                if (isEmpty(response.data)) {
                    $('#dados_tabela').html(`
                    <tr class="text-center">
                      <td colspan="6" style="vertical-align: middle;height:257px;">
                        <img style="width:124px;margin-right:12px;" src="${$("#dados_tabela").attr("img-empty")}">
                        Nenhum rastreamento encontrado
                      </td>
                    </tr>`);
                    return;
                }

                let grayRow = false;
                let lastSale = '';

                $.each(response.data, function (index, tracking) {

                    if (lastSale !== tracking.sale) {
                        grayRow = !grayRow;
                    }

                    let htmlButtonAdd = `
                    <a class='tracking-add pointer mr-10' title="Adicionar">
                        <span id="add-tracking-code" class='o-add-1 text-primary border border-primary'></span>
                    </a>
                    <input maxlength="18" minlength="10" class="col-sm-7 form-control font-weight-bold input-tracking-code fake-label" placeholder="Clique para adicionar" value="${tracking.tracking_code}" style="padding-bottom: 5px;">`
                    ;

                    let htmlButtonEdit = `
                    <a class='tracking-edit pointer ml-5 mr-50' title="Editar">
                      <span class="text-right o-edit-1"></span>
                    </a>
                    
                    <a class='tracking-detail pointer' title="Visualizar" tracking='${tracking.id}'><span class="o-eye-1"></span></a>`
                    ;

                    let dados = `
                        <tr ${grayRow ? 'class="td-odd"' : ''}>

                            ${lastSale !== tracking.sale ? `
                                <td class="detalhes_venda pointer table-title" venda="${tracking.sale}">
                                    #${tracking.sale}
                                </td>`
                                :
                                `<td>
                                </td>`
                            }

                            <td>
                                <span style="max-width: 330px; display:block; margin: 0px 0px 0px 0px;">
                                    ${tracking.product.amount}x ${tracking.product.name} ${tracking.product.description ? '(' + tracking.product.description + ')' : ''}
                                </span>
                            </td>

                            <td>${tracking.approved_date}</td>

                            <td class="text-center col-sm-2">
                                <span class="badge ${statusEnum[tracking.tracking_status_enum]}">
                                    ${tracking.tracking_status}
                                </span>
                                
                                ${systemStatus[tracking.system_status_enum]}

                                ${tracking.is_chargeback_recovered ? '<img class="orange-gradient ml-10" width="20px" src="/modules/global/img/svg/chargeback.svg" title="Chargeback recuperado">' : ''}
                            </td>

                            <td class="text-left">

                                <div class="buttons d-flex mr-15">
                                    ${tracking.tracking_status_enum ? `
                                    <input maxlength="18" minlength="10" class="col-sm-7 form-control font-weight-bold input-tracking-code" readonly placeholder="Informe o código de rastreio" value="${tracking.tracking_code}">` + htmlButtonEdit
                                    :htmlButtonAdd}

                                    <a class='tracking-save pointer ml-5 mr-5 text-center' title="Salvar" pps='${tracking.pps_id}'style="display:none">
                                        <i id='pencil' class='o-checkmark-1 text-white'></i>
                                    </a>

                                    <a class='tracking-close pointer text-dark' data-code='${tracking.tracking_code}' title="Fechar" style="display:none">
                                        <div class="set-rotate"> <i class='o-add-1 set-close'></i> </div>
                                    </a>

                                </div>

                            </td>

                        </tr>`
                    ;
                    $('#dados_tabela').append(dados);
                    lastSale = tracking.sale;
                });
                pagination(response, 'trackings', index);
            }
        });
    }

    //modal de detalhes
    $(document).on('click', '.tracking-detail', function () {
        tracking_id = $(this).attr('tracking');

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

                        $('#table-checkpoint').append(
                            `<tr>
                              <td>${checkpoint.created_at}</td>
                              <td>
                                  <span class="badge badge-${statusEnum[checkpoint.tracking_status_enum]}">${checkpoint.tracking_status}</span>
                              </td>
                              <td>${checkpoint.event}</td>
                          </tr>`
                        );
                    }
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
                        .trigger('click');

                    let buttons = `
                        <a class='tracking-edit pointer ml-5 mr-50' title="Editar">
                            <span class="text-right o-edit-1"></span>
                        </a>
                        <a class='tracking-detail pointer' title="Visualizar" tracking='${tracking.id}'><span class="o-eye-1"></span></a>`
                    ;

                    td.append(buttons);

                    let statusBadge = btnSave.parent().parent().parent().find('.badge');

                        statusBadge.removeClass('statusWithoutInfo')
                            .addClass(statusEnum[tracking.tracking_status_enum])
                            .html(tracking.tracking_status);

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

    $('.btn-light-1').click(function () {
        var collapse = $('#icon-filtro')
        var text = $('#text-filtro')

        text.fadeOut(10);
        if (collapse.css('transform') == 'matrix(1, 0, 0, 1, 0, 0)' || collapse.css('transform') == 'none') {
            collapse.css('transform', 'rotate(180deg)')
            text.text('Minimizar filtros').fadeIn();
        } else {
            collapse.css('transform', 'rotate(0deg)')
            text.text('Filtros avançados').fadeIn()
        }
    });

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            index();
            getResume();
        }
    });
});
