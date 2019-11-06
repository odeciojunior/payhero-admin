$(() => {

    $('#tracking-product-image').on('error', function(){
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

    //alem do evento disparado no modal de vendas /modules/sales/detail.js
    $(document).on('click', '.btn-save-trackingcode', function(event){
        index();
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
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
    });

    getProducts();

    function getProducts() {

        loadOnAny('.page-content');

        $.ajax({
            method: 'GET',
            url: '/api/projects/user-projects',
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
            url: '/api/tracking/resume?' + 'tracking_code=' + $('#tracking_code').val() + '&status=' + $('#status').val()
                + '&project=' + $('#project-select').val() + '&date_updated=' + $('#date_updated').val(),
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
                    let {total, posted, dispatched, out_for_delivery, delivered, exception, not_informed} = response.data;

                    dispatched += posted;
                    dispatched += out_for_delivery;

                    $('#total-trackings').text(total);
                    $('#percentual-delivered').text(delivered ? delivered + ' (' +((delivered*100)/total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-dispatched').text(dispatched ? dispatched + ' (' +((dispatched*100)/total).toFixed(2) + '%)' : '0 (0.00%)');
                    $('#percentual-exception').text(exception ? exception + ' (' +((exception*100)/total).toFixed(2) + '%)' : '0 (0.00%)');
                }
                loadOnAny('.number', true);
            }
        });
    }

    function index(link = null) {

        if (link == null) {
            link = '/api/tracking?' + 'tracking_code=' + $('#tracking_code').val() + '&status=' + $('#status').val()
                + '&project=' + $('#project-select').val() + '&date_updated=' + $('#date_updated').val();
        } else {
            link = '/api/tracking' + link + '&tracking_code=' + $('#tracking_code').val() + '&status=' + $('#status').val()
                + '&project=' + $('#project-select').val() + '&date_updated=' + $('#date_updated').val();
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
                if (isEmpty(response.data)) {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='4' style='height: 70px;vertical-align: middle'> Nenhuma rastreamento encontrada</td></tr>");
                } else {
                    $.each(response.data, function (index, tracking) {
                        let badge;
                        switch (tracking.tracking_status_enum) {
                            case 1:
                                badge = 'primary';
                                break;
                            case 3:
                                badge = 'success';
                                break;
                            case 5:
                                badge = 'danger';
                                break;
                            default:
                                badge = 'info';
                                break;
                        }
                        let dados = `<tr>
                                     <td class="detalhes_venda pointer table-title" venda="${tracking.sale}">#${tracking.sale}</td>
                                     <td>${tracking.product.amount}x ${tracking.product.name} ${tracking.product.description ? '(' + tracking.product.description + ')' : ''}</td>
                                     <td class="copy pointer" title="Copiar código">${tracking.tracking_code}</td>
                                     <td>
                                        <span class="badge badge-${badge}">${tracking.tracking_status}</span>
                                     </td>
                                     <td>
                                        <a role='button' class='tracking-detail pointer' tracking='${tracking.id}'><i class='material-icons gradient'>remove_red_eye</i></button></a>
                                    </td>
                                 </tr>`;
                        $('#dados_tabela').append(dados);
                    });

                    pagination(response, 'trackings', index);
                }
            }
        });
    }

    $(document).on('click', '.tracking-detail', function () {

        $.ajax({
            method: 'GET',
            url: '/api/tracking/' + $(this).attr('tracking'),
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {

                let tracking = response.data;

                //preenche os campos
                $('#tracking-code').text(tracking.tracking_code);
                $('#tracking-product-image').attr('src', tracking.product.photo);
                $('#tracking-product-name').text(tracking.product.name + (tracking.product.description ? '(' + tracking.product.description + ')' : ''));
                $('#tracking-product-amount').text(tracking.amount);
                $('#tracking-delivery-address').text('Endereço: ' + tracking.delivery.street + ', ' + tracking.delivery.number);
                $('#tracking-delivery-zipcode').text('CEP: ' + tracking.delivery.zip_code);
                $('#tracking-delivery-city').text('Cidade: ' + tracking.delivery.city + '/' + tracking.delivery.state);

                //GRAFICO DO STATUS DA ENTREGA

                //reset modal
                $('.tracking-timeline .date-item, .tracking-timeline .step-item, .tracking-timeline .status-item').removeClass('active');
                $('.tracking-timeline .exception').remove();
                $('.tracking-timeline .date-item').text('');

                switch (tracking.tracking_status_enum) {
                    case 1: // caso o status seja 'postado', marca o circulo inicial
                        $('.tracking-timeline .date-item').eq(0).addClass('active').text(tracking.created_at);
                        $('.tracking-timeline .step-item').eq(0).addClass('active');
                        $('.tracking-timeline .status-item').eq(0).addClass('active');
                        break;
                    case 2: // caso o status seja 'em transito', marca o 2º e o anterior
                        for (let i = 0; i < 2; i++) {
                            $('.tracking-timeline .date-item').eq(i).addClass('active').text(tracking.created_at);
                            $('.tracking-timeline .step-item').eq(i).addClass('active');
                            $('.tracking-timeline .status-item').eq(i).addClass('active');
                        }
                        break;
                    case 3: // caso o status seja 'entregue', marca o 4º e os anteriores
                        for (let i = 0; i < 4; i++) {
                            $('.tracking-timeline .date-item').eq(i).addClass('active').text(tracking.created_at);
                            $('.tracking-timeline .step-item').eq(i).addClass('active');
                            $('.tracking-timeline .status-item').eq(i).addClass('active');
                        }
                        break;
                    case 4: // caso o status seja 'entregue', marca o 3º e os anteriores
                        for (let i = 0; i < 3; i++) {
                            $('.tracking-timeline .date-item').eq(i).addClass('active').text(tracking.created_at);
                            $('.tracking-timeline .step-item').eq(i).addClass('active');
                            $('.tracking-timeline .status-item').eq(i).addClass('active');
                        }
                        break;
                    case 5: // caso o status seja 'problema na entrega'
                        //verifica o ultimo status do historico e encontra sua posicao no grafico
                        lastItem = tracking.history[tracking.history.length - 1];
                        let index = 0;
                        if (lastItem) {
                            if (lastItem.tracking_status_enum === 2) {
                                index = 1;
                            }
                            if (lastItem.tracking_status_enum === 3) {
                                index = 2;
                            }
                        }

                        //adiciona um circulo representando 'problema na entrega' apos o ultimo status do historico
                        $('<div class="date-item exception">' + tracking.created_at + '</div>').insertAfter($('.tracking-timeline .date-item').eq(index));
                        $('<div class="step-item exception"><span class="step-line"></span><span class="step-dot"></span><span class="step-line"></span></div>').insertAfter($('.tracking-timeline .step-item').eq(index));
                        $('<div class="status-item exception">Problema na entrega</div>').insertAfter($('.tracking-timeline .status-item').eq(index));

                        //marca todos os circulos anteriores
                        for (let i = 0; i <= index; i++) {
                            $('.tracking-timeline .date-item').eq(i).addClass('active').text(tracking.created_at);
                            $('.tracking-timeline .step-item').eq(i).addClass('active');
                            $('.tracking-timeline .status-item').eq(i).addClass('active');
                        }
                        break;
                }

                //verifica se registro no historico de atualizacoes do tracking, caso exista usa a data do registro
                for (let register of tracking.history) {
                    switch (register.tracking_status_enum) {
                        case 1:
                            $('.tracking-timeline .date-item').eq(0).text(register.created_at);
                            break;
                        case 2:
                            $('.tracking-timeline .date-item').eq(1).text(register.created_at);
                            break;
                        case 4:
                            $('.tracking-timeline .date-item').eq(2).text(register.created_at);
                            break;
                    }
                }

                //FIM - GRAFICO DO STATUS DA ENTREGA

                $('#modal-tracking').modal('show')
            }
        });

    });
});
