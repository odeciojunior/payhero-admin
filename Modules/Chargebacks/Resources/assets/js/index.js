$(document).ready(function () {

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
        placeholder: 'Nome da loja',
        allowClear: true,
        language: {
            noResults: function () {
                return 'Nenhuma loja encontrado';
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
            fantasy_name: $("#fantasy_name").val(),
            project: $("#project").val(),
            user: $("#usuario").val(),
            customer: $("#customer").val(),
            customer_document: $("#customer_document").val(),
            date_range: $("#date_range").val(),
            date_type: $("#date_type").val(),

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
            link = '/chargebacks/getchargebacks?' + getFilters();
        } else {
            link = '/chargebacks/getchargebacks' + link + '&' + getFilters();

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

                    dados = '';
                    dados += `
                                <tr>
                                    <td id='${value.id}'><span>${value.sale_code}</span><br><small>(${value.sale_id})</small></td>
                                    <td>${value.company}<br><small>(${value.user})</small></td>
                                    <td>${value.project}</td>
                                    <td>${value.customer}</td>
                                    <td>${value.transaction_date}</td>
                                    <td>${value.adjustment_date}</td>
                                    <td style='white-space: nowrap'><b>${value.amount}</b></td>                                      
                                    <td>
                                        <div class="row">
                                            <div class="col-4">
                                                <a role="button" class="detalhes_venda pointer" venda="${value.transaction_id}" data-target="#modal_detalhes" data-toggle="modal" 
                                                data-tooltip='tooltip' title="Detalhes da venda" style="margin-right:10px;">
                                                    <i class='material-icons gradient'>remove_red_eye</i>
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a role="button" class="detalhes_ckargeback pointer" ckargeback="${value.id}" data-target='#modal_detalhes' data-toggle='modal'
                                                 data-tooltip='tooltip' title="Detalhes do ckargeback" style='margin-right:10px;'>
                                                    <i class='material-icons gradient'>assignment</i>
                                                </a>
<!--                                                <a role='button' class='chargeback_details pointer' company='" + value.id + "' data-target='#details-modal' data-toggle='modal' -->
<!--                                                title='Visualizar' style='margin-right:10px;'><i class='material-icons gradient'>remove_red_eye</i></a>";-->
                                            </div>                                                                                           
                                        </div>
                                    </td>                                     
                                </tr>`;

                    $("#chargebacks-table-data").append(dados);

                });

                if (response.data == '') {
                    $('#chargebacks-table-data').html("<tr class='text-center'><td colspan='7' style='height: 70px;vertical-align: middle'> Nenhum chargeback encontrado nesse período</td></tr>");
                }
                pagination(response);

                vendaDetails();
                chargebackDetails();
            }
        });
    }

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

        let link = '/chargebacks/gettotalvalues?' + getFilters()
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
                $('#total-chargeback').html(response.total_chargeback);
                $('#total-chargeback-value').html(response.total_chargeback_value);
                $('#total-chargeback-tax').html(response.total_chargeback_tax + ' (' + response.total_chargeback + ' de ' + response.total_sale_approved + ' aprovadas' + ')');
            }
        });
    }

    function vendaDetails() {
        $(".detalhes_venda").on('click', function () {
            loadOnAny('#modal-details .modal-body');
            let venda = $(this).attr('venda');

            $('#modal_titulo').html('Detalhes da venda');

            $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

            let data = {sale_id: venda};

            $.ajax({
                method: "POST",
                url: '/sales/venda/detalhe',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (response) {
                    loadOnAny('#modal-details .modal-body', true);
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    $('.modal-body').html(response);

                    $('.subTotal').mask('#.###,#0', {reverse: true});
                    $("#refundAmount").mask('##.###,#0', {reverse: true});

                    loadOnAny('#modal-details .modal-body', true);
                }
            });
        });
    }

    function chargebackDetails() {
        $('.detalhes_ckargeback').unbind('click');
        $('.detalhes_ckargeback').on('click', function (event) {
            event.preventDefault();
            var ckargeback = $(this).attr('ckargeback');
            $('#modal_titulo').html('Detalhes do chargeback');
            loadOnAny('#modal-details .modal-body');

            $.ajax({
                method: "GET",
                url: "chargebacks/" + ckargeback,
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
