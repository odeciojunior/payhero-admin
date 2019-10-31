$(() => {

    $(document).on('click', '.copy', function () {
        var temp = $("<input>");
        $("body").append(temp);
        temp.val($(this).html()).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Código copiado!');
    });

    $('#bt_filtro').on('click', function () {
        index();
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
                getResume();
            }
        });
    }

    function getResume() {
        $.ajax({
            method: 'GET',
            url: '/api/tracking/resume?' + 'tracking_code=' + $('#tracking_code').val() + '&status=' + $('#status').val()
                + '&project=' + $('#project-select').val() + '&date_updated=' + $('#date_updated').val(),
            data: {

            },
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
                if(isEmpty(response.data)){
                    alertCustom('error', 'Erro ao carregar resumo dos rastreios');
                } else {
                    $('#percentual-delivered').text(response.data.delivered + '%');
                    $('#percentual-dispatched').text(response.data.dispatched + '%');
                    $('#percentual-exception').text(response.data.exception + '%');
                    index();
                }
                loadOnAny('.page-content', true);
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
                                     <td>${tracking.product.name}</td>
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

    $(document).on('click', '.tracking-detail', function(){
        $('#modal-tracking').modal('show')
    });
});
