$(document).ready(function () {

    let statusObject = {
        1: 'Aprovada',
        2: 'Pendente',
        3: 'Recusado',
        4: 'ChargeBack',
        5: 'Cancelada',
        6: 'Em processo',
        7: 'Estornada',
        8: 'Estorno Parcial',
        10: 'BlackList',
        20: 'Revisão Antifraude',
        21: 'Cancelada Antifraude',
        22: 'Estornado',
        24: 'Em disputa',
        99: 'Erro Sistema',
        null: 'Em Processo'
    };

    let badgeObject = {
        1: 'badge-success',
        2: 'badge-pendente',
        3: 'badge-danger',
        4: 'badge-danger',
        5: 'badge-danger',
        6: 'badge-secondary',
        7: 'badge-danger',
        8: 'badge-warning',
        10: 'badge-dark',
        20: 'badge-antifraude',
        21: 'badge-danger',
        22: 'badge-danger',
        24: 'badge-antifraude',
        99: 'badge-danger',
        null: 'badge-primary',
    };

    datePicker();
    atualizar();
    getTotalValues();


    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
        getTotalValues();
    });

    //Search project
    $('#project').select2({
        placeholder: 'Nome do projeto',
        allowClear: true,
        language: {
            noResults: function () {
                return 'Nenhum projeto encontrado';
            },
            searching: function () {
                return 'Procurando...';
            }
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'project',
                    search: params.term,
                };
            },
            method: "POST",
            url: '/projects/getproject',
            delay: 300,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name};
                    })
                };
            },
        }
    });
    //Search user
    $('#usuario').select2({
        placeholder: 'Nome do usuário',
        allowClear: true,
        language: {
            noResults: function () {
                return 'Nenhum usuário encontrado';
            },
            searching: function () {
                return 'Procurando...';
            }
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'user',
                    search: params.term,
                };
            },
            method: "POST",
            url: '/users/searchuser',
            delay: 300,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name};
                    })
                };
            },
        }
    });
    //Search client
    $('#customer').select2({
        placeholder: 'Nome do cliente',
        allowClear: true,
        language: {
            noResults: function () {
                return 'Nenhum cliente encontrado';
            },
            searching: function () {
                return 'Procurando...';
            }
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'client',
                    search: params.term,
                };
            },
            method: "POST",
            url: '/customers/searchcustomer',
            delay: 300,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name};
                    })
                };
            },
        }
    });

    function datePicker() {
        //DatePicker
        let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
        let endDate = moment().format('YYYY-MM-DD');
        $('#date_range').daterangepicker({
            startDate: moment().subtract(30, 'days'),
            endDate: moment(),
            opens: 'center',
            maxDate: moment().add(3, 'month'),
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
                'Próximos 30 dias': [moment(), moment().add(29, 'days')],
                'Este mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Vitalício': [moment('2018-01-01 00:00:00'), moment()]
            }
        }, function (start, end) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
        });
    }

    function getFilters(urlParams = true) {

        let current_url = window.location.href;
        let vazio = current_url.includes('vazio') ? 'true' : '';

        let data = {
            transaction: $("#transaction").val().split('#').join(''),
            // fantasy_name: $("#fantasy_name").val(),
            project: $("#project").val() ?? '',
            customer: $("#customer").val() ?? '',
            customer_document: $("#customer_document").val() ?? '',
            date_range: $("#date_range").val() ?? '',
            date_type: $("#date_type").val() ?? '',
            order_by_expiration_date: $("#expiration_date").is(":checked") ? 1 : 0,
            status: $("#status").val() ?? '',
            is_contested: $("#is_contested").val() ?? '',
        }
        if (urlParams) {
            let params = "";
            let isFirst = true;
            for (let param in data) {
                params += `${(isFirst ? '' : '&')}${param}=${data[param]}`;
                isFirst = false;
            }
            return encodeURI(params);
        }
        return data;
    }

    function atualizar(link = null) {

        loadOnTable('#chargebacks-table-data', '#chargebacks-table');

        if (link == null) {
            link = '/contestations/getcontestations?' + getFilters();
        } else {
            link = '/contestations/getcontestations' + link + '&' + getFilters();

        }
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            },
            success: function (response) {
                $('#chargebacks-table-data').html('');
                $('#chargebacks-table').addClass('table-striped');

                $.each(response.data, function (index, value) {

                    const objectArray = Object.entries(value.sale_blacklist);
                    let valuesObject = ``;

                    objectArray.forEach(([key, value]) => {
                        valuesObject += `${Object.keys(value)} - ${Object.values(value)}`;
                    });

                    dados = '';
                    dados += `
                                <tr>
                                    <td id='${value.id}'><span>${value.sale_code}</span></td>
                                    <td title="${value.company}">${value.company_limit}<br><small class="text-muted">(${value.project})</small></td>
                                    <td>${value.customer}<br><small class="text-muted"> Pagamento em ${value.adjustment_date}</small></td>
                                   `;

                    if (value.sale_status in statusObject) {
                        dados += `<td class='copy_link'>
                                    <div class="d-flex justify-content-center align-items-center" >
                                        <span class='badge ${badgeObject[value.sale_status]} ${value.sale_status === 10 ? 'pointer' : 'cursor-default'}' data-toggle="tooltip" data-html="true" data-placement="top" title="${valuesObject}">${statusObject[value.sale_status]}</span>
                                        ${value.sale_has_valid_tracking ? '<i class="material-icons font-size-20 text-success cursor-default ml-5" data-toggle="tooltip" title="Rastreamento válido">local_shipping</i>' : value.sale_only_digital_products ? '<i class="material-icons font-size-20 text-info cursor-default ml-5" data-toggle="tooltip" title="A venda não tem produtos físicos">computer</i>' : '<i class="material-icons font-size-20 text-danger cursor-default ml-5" data-toggle="tooltip" title="Rastreamento inválido ou não informado">local_shipping</i>'}
                                        ${value.sale_is_chargeback_recovered ? '<img class="orange-gradient ml-5" src="/global/img/svg/chargeback.svg" width="20px" title="Chargeback recuperado">' : ''}
                                    </div>
                                </td>`;
                    } else {
                        dados += `<td><span class='badge badge-danger'> Vazio</span></td>`;
                    }
                    dados += `
                                    <td>${value.file_date}<br>
                                    <small class="text-muted"> ${value.has_expired ? 'Expirado em ' : 'Expira em '} ${value.expiration}</small>
                                    </td>
                                    <td>${value.reason}</td>
<!--                                    <td style='white-space: nowrap'><b>${value.amount}</b></td>-->
                                    <td>
                                       <a  role='button' class='contetation_file pointer ${value.has_expired ? 'd-none' : ''} ' style="margin-right:5px" contestation='${value.id}'>
                                       <span class="o-download-cloud-1"></span>
                                        </a>
                                        <a role='button' class='detalhes_venda pointer' venda='${value.sale_id}'>
                                            <span class="o-eye-1"></span>
                                        </a>
                                  </td>
                                </tr>`;

                    $("#chargebacks-table-data").append(dados);

                });

                if (response.data == '') {
                    $('#chargebacks-table-data').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma contestação encontrado</td></tr>");
                }
                pagination(response);

                contestationDetails();

                $(".contestation_pdf").unbind('click');
                $(".contestation_pdf").on('click', function (event) {
                    event.preventDefault();
                    $("#observation").val('');
                    $("#pdf-modal").modal('show');
                    $("#update-contestation-pdf").on('click', function () {

                        let files = new FormData();
                        files.append('file_contestation', $('#file_contestation')[0].files[0]);

                        loadOnAny('#pdf-modal .modal-user-pdf-body');

                        $.ajax({
                            method: "POST",
                            url: "contestations/send-contestation",
                            processData: false,
                            contentType: false,
                            data: files,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (response) {
                                errorAjaxResponse(response);
                            },
                            success: function (response) {
                                $('#pdf-modal').modal('hide');
                                alertCustom('success', response.message);
                            },
                            complete: function (data) {
                                loadOnAny('#pdf-modal .modal-user-pdf-body', true);
                            }

                        });

                        $(".icon-observation-value_" + response.data.id).addClass('green')

                    });

                });


            }
        });
    }

    $(document).on('change', '.check-status', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(3);
        }
        $.ajax({
            method: "POST",
            url: '/contestations/update-is-contested',
            data: {
                is_contested: $(this).val(),
                contestation_id: $(this).data('contestation'),
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, textStatus, request) {
                alertCustom('success', response.message);
            }
        });
    });

    function getTotalValues() {
        loadOnAny('.total-number', false, {
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

        let link = '/contestations/gettotalvalues?' + getFilters()
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                loadOnAny('.total-number', true);
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadOnAny('.total-number', true);
                $('#total-contestation').html(response.total_contestation);
                $('#total-contestation-value').html(response.total_contestation_value);
                $('#total-contestation-tax').html(' (' + response.total_contestation_tax + ' de ' + response.total_sale_approved + ')');
                $('#total-chargeback-tax').html(' (' + response.total_chargeback_tax + ')');
                $('#total-chargeback-tax-val').html(response.total_chargeback);

            }
        });
    }

    function contestationDetails() {
        $('.detalhes_ckargeback').unbind('click');
        $('.detalhes_ckargeback').on('click', function (event) {
            event.preventDefault();
            var ckargeback = $(this).attr('ckargeback');
            $('#modal_titulo').html('Detalhes da contestação');
            loadOnAny('#modal-details .modal-body');

            $.ajax({
                method: "GET",
                url: "contestations/" + ckargeback,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (response) {
                    loadOnAny('#modal-details .modal-body', true);
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    $('.modal-body').html('');
                    loadOnAny('#modal-details .modal-body', true);
                    $('.modal-body').html(response);
                }
            });
        });
    }

    function pagination(response) {

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").attr('disabled', true);
            $("#primeira_pagina").addClass('nav-btn');
            $("#primeira_pagina").addClass('active');
        }

        $('#primeira_pagina').on("click", function () {
            atualizar('?page=1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                atualizar('?page=' + $(this).html());
            });

        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";

            $("#pagination").append(pagina_atual);

            $("#pagina_atual").attr('disabled', true);
            $("#pagina_atual").addClass('nav-btn');
            $("#pagina_atual").addClass('active');

        }
        for (x = 1; x < 4; x++) {

            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                atualizar('?page=' + $(this).html());
            });

        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
                $("#ultima_pagina").addClass('nav-btn');
                $("#ultima_pagina").addClass('active');
            }

            $('#ultima_pagina').on("click", function () {
                atualizar('?page=' + response.meta.last_page);
            });
        }

    }

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            atualizar();
            getTotalValues();
        }
    });


});
