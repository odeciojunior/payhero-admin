$(document).ready(function () {
    atualizar();
    let transfersCompanySelect = $("#transfers_company_select");
    let extractCompanySelect = $("#extract_company_select");

    function atualizar() {
        $.ajax({
            method: "GET",
            url: "/api/companies",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {

                if (!isEmpty(response.data)) {

                    $('.page-content').show();
                    $('.content-error').hide();
 
                    //transfers_company_select
                    fillSelectAndCheckWithModelFields(
                        transfersCompanySelect,
                        {company: response.data},
                        null,
                        {company: selectItemsFunction}
                    );
                    transfersCompanySelect.on("change", function () {
                        $("#transfers_company_select option[value=" + $('#transfers_company_select option:selected').val() + "]")
                            .prop("selected", true);
                        $('#custom-input-addon').val('');
                        updateBalances();
                        //updateWithdrawalsTable(null, "transfersCompanySelect:change");
                        // updateTransfersTable();
                    });

                    //extract_company_select
                    fillSelectAndCheckWithModelFields(
                        extractCompanySelect,
                        {company: response.data},
                        null,
                        {company: selectItemsFunction}
                    );
                    extractCompanySelect.on("change", function () {
                        $("#extract_company_select option[value=" + $('#extract_company_select option:selected').val() + "]")
                            .prop("selected", true);
                        // $('#custom-input-addon').val('');
                        updateTransfersTable();
                        // updateBalances();
                        // updateWithdrawalsTable();
                    });

                    updateBalances();
                    updateTransfersTable();
                } else {
                    $('.page-content').hide();
                    $('.content-error').show();
                }

            }
        });
    }

    function selectItemsFunction(item) {
        return {value: item.id_code, text: item.fantasy_name};
    }

    $('.withdrawal-value').mask('#.###,#0', {reverse: true});

    $(document).on('click', function (e) {
        if ($("#antecipa-popover").is(':visible') && (!$(e.target).hasClass('anticipation'))) {
            $("#antecipa-popover").fadeOut(100);
        }
    });

    $("#pop-antecipacao").click(function () {
        // if ($("#antecipa-popover").css('display') == 'none') {
        //     $("#antecipa-popover").delay(200).fadeIn(200);
        // } else {
        //     $("#antecipa-popover").fadeOut(100);
        // }
    });

    function updateBalances() {
        loadOnAny('.price', false, {
            styles: {
                container: {
                    minHeight: '31px',
                    justifyContent: 'flex-start',
                },
                loader: {
                    width: '20px',
                    height: '20px',
                    borderWidth: '4px'
                },
            },
            insertBefore: '.grad-border',
        });
        loadOnTable('#withdrawals-table-data', '#withdrawalsTable');
        $.ajax({
            url: "api/finances/getbalances/",
            type: "GET",
            data: {company: $("#transfers_company_select option:selected").val()},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('.price', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                $('.saldoPendente').html('<span class="currency">R$</span><span class="pending-balance">0,00</span>');
                $('.removeSpan').remove();
                $('.disponivelAntecipar').append('<span class="currency removeSpan">R$</span><span class="antecipable-balance removeSpan">0,00</span>');
                $('.saldoDisponivel').html('<span class="currency">R$</span><span class="available-balance">0,00 <i class="material-icons ml-5" style="color: #44a44b;">arrow_forward</i></span>');
                $('.saltoTotal').html('<span class="currency">R$</span><span class="total-balance">0,00</span>');
                $('.totalConta').html('<span class="currency">R$</span><span class="total-balance">0,00</span>');
                $('.total_available').html('<span class="currency">R$</span>' + isEmpty(response.available_balance));
                $(".currency").html(isEmpty(response.currency));
                $(".available-balance").html(isEmpty(response.available_balance));
                $(".antecipable-balance").html(isEmpty(response.antecipable_balance));
                $(".pending-balance").html(isEmpty(response.pending_balance));
                $(".total-balance").html(isEmpty(response.total_balance));
                $(".loading").remove();
                $("#div-available-money").unbind('click');
                $("#div-available-money").on("click", function () {
                    $(".withdrawal-value").val(isEmpty(response.available_balance));
                });
                // $.getScript('modules/withdrawals/js/index.js');
                // $("#withdrawals-table-data").html('');
                updateWithdrawalsTable();
                loadOnAny('.price', true);
            }
        });

        function isEmpty(value) {
            if (value.length === 0) {
                return 0;
            } else {
                return value
            }
        }
    }

    $('#bt-withdrawal').on('click', function () {
        var availableBalanceText = $('.available-balance').html().replace(',', '').replace('.', '');
        var toTransferText = $('#custom-input-addon').val().replace(',', '').replace('.', '');
        var availableBalance = parseInt(availableBalanceText);
        var toTransfer = parseFloat(toTransferText);
        if (toTransfer > availableBalance) {
            alertCustom('error', 'O valor requerido ultrapassa o limite disponivel');
            toTransferText = $('#custom-input-addon').val();
            toTransfer = toTransferText.slice(0, -2);
            $('#custom-input-addon').val('');
            $('.withdrawal-value').mask('#.###,#0', {reverse: true});
        } else if ($('#custom-input-addon').val() == '') {
            alertCustom('error', 'Valor do saque inválido!');
        } else {
            $.ajax({
                url: "/api/withdrawals/getaccountinformation/" + transfersCompanySelect.val(),
                type: "GET",
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    if (response.data.user_documents_status == 'pending') {
                        var route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos pessoais ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com seus documentos</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.documents_status == 'pending') {
                        var companie = $('#transfers_company_select').val();
                        var _route = '/companies/' + companie + '/edit';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos da empresa ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com os documentos de sua empresa</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else {
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Confirmar Saque");
                        $('#modal_body').html('<div>' + '<h5>Verifique os dados da conta:</h5>' + '<h4>Banco:<span id="modal-withdrawal-bank"></span></h4>' + '<h4>Agência:<span id="modal-withdrawal-agency"></span><span id="modal-withdrawal-agency-digit"></span></h4>' + '<h4>Conta:<span id="modal-withdrawal-account"></span><span id="modal-withdrawal-account-digit"></span></h4>' + '<h4>Documento:<span id="modal-withdrawal-document"></span></h4>' + '<hr>' + '<h3>Valor do saque:<span id="modal-withdrawal-value" class=\'greenGradientText\'></span>' + '<span id="taxValue" class="text-gray-dark" style="font-size: 14px; color:#999999" title="Taxa de saque">- R$3,80</span>' + '</h3>' + '</div>');
                        $('#modal-withdraw-footer').html('<button id="bt-confirm-withdrawal" class="btn btn-success" style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">' + '<strong>Confirmar</strong></button>' + '<button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal" aria-label="Close" style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">' + '<strong>Cancelar</strong></button>');

                        $("#modal-withdrawal-value").html(' R$ ' + $('#custom-input-addon').val() + ' ');
                        $("#modal-withdrawal-bank").html('  ' + response.data.bank);
                        $("#modal-withdrawal-agency").html('  ' + response.data.account);
                        if (response.data.agency_digit != '' && response.data.agency_digit != null) {

                            $("#modal-withdrawal-agency-digit").html(' - ' + response.data.agency_digit);
                        }
                        $("#modal-withdrawal-account").html('  ' + response.data.account);
                        if (response.data.account_digit != '' && response.data.account_digit != null) {

                            $("#modal-withdrawal-account-digit").html(' - ' + response.data.account_digit);
                        }
                        $("#modal-withdrawal-document").html('  ' + response.data.document);

                        $("#bt-confirm-withdrawal").unbind("click");
                        $("#bt-confirm-withdrawal").on("click", function () {
                            loadOnModal('#modal-body');
                            $.ajax({
                                url: "/api/withdrawals",
                                type: "POST",
                                data: {
                                    company_id: $('#transfers_company_select').val(),
                                    withdrawal_value: $('#custom-input-addon').val()
                                },
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: (response) => {
                                    loadingOnScreenRemove();
                                    errorAjaxResponse(response);
                                },
                                success: (response) => {
                                    loadingOnScreenRemove();
                                    $('#modal-withdrawal').modal('show');
                                    $('#modal-withdrawal-title').text("Sucesso!");
                                    $('#modal_body').html('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' + '<h3 align="center"><strong>Saque realizado com sucesso!</strong></h3>' + '<h4 align="center">Sua solicitação foi para avaliação</h4>' + '<h4 align="center">Em alguns instantes seu dinheiro estara em sua conta</h4>');
                                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                                    $('.btn-return').on('click', function () {
                                        $('#custom-input-addon').val('');
                                    });

                                    $('.btn-return').click(function () {
                                        $('#modal_body').modal('hide');
                                        updateWithdrawalsTable();
                                        updateBalances();
                                    });
                                }
                            });
                        });
                    }
                }
            });
        }
    });

    /**
     * Modulo Anticipations
     */
    // $("#btn-disponible-antecipation").unbind('click');
    // $("#btn-disponible-antecipation").on('click', function () {
    //     // loading("#balance-after-anticipation",'');
    //     $('#balance-after-anticipation').html("<span class='loaderSpan' >" + "</span>")
    //     let company = transfersCompanySelect.val();
    //     $("#tax-value").html('');

    //     $.ajax({
    //         method: 'GET',
    //         url: '/api/anticipations/' + company,
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         error: (response) => {
    //             console.log(response);
    //             $("#balance-after-anticipation").html(response.responseJSON.data.valueAntecipable);
    //             $(".loaderSpan").remove();
    //             errorAjaxResponse(response);
    //         },
    //         success: (response) => {
    //             $("#balance-after-anticipation").html(response.data['valueAntecipable']);
    //             $("#tax-value").html(response.data['taxValue']);
    //         }
    //     });
    // });

    $("#btn-anticipation").unbind('click');
    $("#btn-anticipation").on('click', function () {
        loadingOnScreen();
        $.ajax({
            method: 'POST',
            url: '/api/anticipations',
            data: {company: $("#transfers_company_select option:selected").val()},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                updateBalances()
            }
        })

    });

    /**
     * Module Finances
     */
    function updateTransfersTable(link = null) {
        loadOnTable('#table-transfers-body', '#transfersTable');
        if (link == null) {
            link = '/transfers';
        } else {
            link = '/transfers' + link;
        }
        $.ajax({
            method: "GET",
            url: link,
            data: {company: $("#extract_company_select option:selected").val()},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {

                $("#table-transfers-body").html('');

                if (response.data == '') {

                    $("#table-transfers-body").html("<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>");
                    $("#pagination-transfers").html("");
                } else {
                    data = '';

                    $.each(response.data, function (index, value) {
                        data += '<tr >';
                        data += '<td style="vertical-align: middle;">' + value.reason + '<a style="cursor:pointer;" class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" venda="' + value.sale_id + '">' + '<span style="color:black;">' + value.transaction_id + '</span>' + '</a></td>';
                        data += '<td style="vertical-align: middle;">' + value.date + '</td>';
                        if (value.type_enum === 1) {
                            data += '<td style="vertical-align: middle; color:green;">' + value.value + ' <span style="color:red;">' + value.anticipable_value + '</span> </td>';
                        } else {
                            data += '<td style="vertical-align: middle; color:red;">' + value.value + '</td>';
                        }
                        data += '</tr>';
                    });

                    $("#table-transfers-body").html(data);

                    paginationTransfersTable(response);
                }
            }
        });
    }

    /**
     * Withdrawl
     */
    var statusWithdrawals = {
        1: 'warning',
        2: 'primary',
        3: 'success',
        4: 'danger'
    };

    function updateWithdrawalsTable(link = null) {
        loadOnTable('withdrawals-table-data', 'transfersTable');
        $("#withdrawals-table-data").html("");
        if (link == null) {
            link = '/api/withdrawals';
        } else {
            link = '/api/withdrawals' + link;
        }
        $.ajax({
            method: "GET",
            url: link,
            data: {company: $("#transfers_company_select option:selected").val()},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#withdrawals-table-data").html('');
                if (response.data === '' || response.data === undefined || response.data.length === 0) {
                    $("#withdrawals-table-data").html("<tr><td colspan='5' class='text-center'>Nenhum saque realizado até o momento</td></tr>");
                    $("#withdrawals-pagination").html("");
                } else {
                    $.each(response.data, function (index, value) {
                        let data = '';
                        data += '<tr>';
                        data += "<td>" + value.account_information + "</td>";
                        data += "<td>" + value.date_request + "</td>";
                        data += "<td>" + value.date_release + "</td>";
                        data += "<td>" + value.value + "</td>";
                        data += '<td class="shipping-status">';
                        data += '<span class="badge badge-' + statusWithdrawals[value.status] + '">' + value.status_translated + '</span>';
                        data += '</td>';
                        data += '</tr>';
                        $("#withdrawals-table-data").append(data);
                        $('#withdrawalsTable').addClass('table-striped')
                    });
                    pagination(response, 'withdrawals', updateWithdrawalsTable);
                }

            }
        });
    }

    function paginationTransfersTable(response) {
        $("#pagination-transfers").html("");
        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";
        $("#pagination-transfers").append(primeira_pagina);
        if (response.meta.current_page == '1') {
            $("#primeira_pagina").attr('disabled', true);
            $("#primeira_pagina").addClass('nav-btn');
            $("#primeira_pagina").addClass('active');
        }
        $('#primeira_pagina').unbind("click");
        $('#primeira_pagina').on("click", function () {
            updateTransfersTable('?page=1');
        });
        for (x = 3; x > 0; x--) {
            if (response.meta.current_page - x <= 1) {
                continue;
            }
            $("#pagination-transfers").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");
            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                updateTransfersTable('?page=' + $(this).html());
            });
        }
        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";
            $("#pagination-transfers").append(pagina_atual);
            $("#pagina_atual").attr('disabled', true).addClass('nav-btn').addClass('active');
        }
        for (x = 1; x < 4; x++) {
            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }
            $("#pagination-transfers").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");
            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                updateTransfersTable('?page=' + $(this).html());
            });
        }
        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";
            $("#pagination-transfers").append(ultima_pagina);
            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
                $("#ultima_pagina").addClass('nav-btn');
                $("#ultima_pagina").addClass('active');
            }
            $('#ultima_pagina').on("click", function () {
                updateTransfersTable('?page=' + response.meta.last_page);
            });
        }
        $('table').addClass('table-striped');
    }

});
