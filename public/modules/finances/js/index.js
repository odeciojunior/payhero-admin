$(document).ready(function () {

    $('.withdrawal-value').mask('#.###,#0', {reverse: true});

    $(document).on('click', function (e) {
        if ($("#antecipa-popover").is(':visible') && (!$(e.target).hasClass('anticipation'))) {
            $("#antecipa-popover").fadeOut(100);
        }
    });

    $("#pop-antecipacao").click(function () {
        if ($("#antecipa-popover").css('display') == 'none') {
            $("#antecipa-popover").delay(200).fadeIn(200);
        } else {
            $("#antecipa-popover").fadeOut(100);
        }
    });

    $("#transfers_company_select").on("change", function () {
        $("#extract_company_select option[value=" + $('#transfers_company_select option:selected').val() + "]")
            .prop("selected", true);
        $('#custom-input-addon').val('');
        updateBalances();
        updateTransfersTable();
        updateWithdrawalsTable();
    });

    $("#extract_company_select").on("change", function () {
        $("#transfers_company_select option[value=" + $('#extract_company_select option:selected').val() + "]")
            .prop("selected", true);
        $('#custom-input-addon').val('');
        updateTransfersTable();
        updateBalances();
        updateWithdrawalsTable();
    });

    updateBalances();

    function updateBalances() {

        $(".price").append("<span class='loading'>" + "<span class='loaderSpan' >" + "</span>" + "</span>");
        loadOnTable('#withdrawals-table-data', '#withdrawalsTable');

        $.ajax({
            url: "/finances/getbalances/",
            type: "GET",
            data: {company: $("#transfers_company_select option:selected").val()},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.message);
                }
            },
            success: function success(response) {
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
                $.getScript('modules/withdrawals/js/index.js');
                $("#table-withdrawals-body").html('');
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
                url: "/withdrawals/getaccountinformation/" + $("#transfers_company_select").val(),
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                error: function error() {
                    //
                },
                success: function success(response) {
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
                                url: "/withdrawals",
                                type: "POST",
                                data: {
                                    company_id: $('#transfers_company_select').val(),
                                    withdrawal_value: $('#custom-input-addon').val()
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                },
                                error: function (_error) {
                                    function error(_x) {
                                        return _error.apply(this, arguments);
                                    }

                                    error.toString = function () {
                                        return _error.toString();
                                    };

                                    return error;
                                }(function (response) {
                                    loadingOnScreenRemove();

                                    if (response.status === 422) {
                                        for (error in response.errors) {
                                            alertCustom('error', String(response.errors[error]));
                                        }
                                    } else if (response.status === 400) {
                                        $('#modal-withdrawal').modal('show');

                                        $('#modal-withdrawal-title').text("Oooppsssss!");
                                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Algo deu errado!</strong></h3>' + '<h4 align="center">' + String(response.responseJSON.message) + '</h4>' + '<h4 align="center">Entre em contato com o suporte para mais informações</h4>');
                                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                                    } else {
                                        alertCustom('error', String(response.responseJSON.message));
                                    }
                                }),
                                success: function success(response) {
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

    $("#btn-disponible-antecipation").unbind('click');
    $("#btn-disponible-antecipation").on('click', function () {
        // loading("#balance-after-anticipation",'');
        $('#balance-after-anticipation').html("<span class='loaderSpan' >" + "</span>")
        let company = $("#transfers_company_select").val();
        $("#tax-value").html('');

        $.ajax({
            method: 'GET',
            url: '/api/anticipations/' + company,
            headers: {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content'),
            },
            error: function (response) {
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else if (response.status === 400) {
                    $("#balance-after-anticipation").html(response.responseJSON.data['valueAntecipable'] + ',00');
                    $("#tax-value").html(response.responseJSON.data['taxValue'] + ',00');
                    alertCustom("error", response.responseJSON.message)
                } else {
                    alertCustom("error", response.responseJSON.message)
                }
            },
            success: function (response) {
                $("#balance-after-anticipation").html(response.data['valueAntecipable']);
                $("#tax-value").html(response.data['taxValue']);
            }
        });

    });

    $("#btn-anticipation").unbind('click');
    $("#btn-anticipation").on('click', function () {
        loadingOnScreen();
        let company = $("#transfers_company_select").val();

        $.ajax({
            method: 'POST',
            url: '/api/anticipations',
            data: {company: company},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            error: function (response) {
                loadingOnScreenRemove();
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else if (response.status === 400) {
                    $("#balance-after-anticipation").html(response.responseJSON.data['valueAntecipable']);
                    $("#tax-value").html(response.responseJSON.data['taxValue']);
                    alertCustom("error", response.responseJSON.message)
                } else {
                    alertCustom("error", response.responseJSON.message)
                }
            },
            success: function (response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                updateBalances()

            }
        })

    });

    /**
     * Module Finances
     */

    updateTransfersTable();

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
            data: {company: $("#extract_company_select").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                $("#table-transfers-body").html('Erro ao encontrar dados');
            },
            success: function success(response) {

                $("#table-transfers-body").html('');

                if (response.data == '') {

                    $("#table-transfers-body").html("<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>");
                    $("#pagination-transfers").html("");
                } else {
                    data = '';

                    $.each(response.data, function (index, value) {
                        data += '<tr >';
                        data += '<td style="vertical-align: middle;">' + value.reason + '<a style="cursor:pointer;" class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" sale="' + value.sale_id + '">' + '<span style="color:black;">' + value.transaction_id + '</span>' + '</a></td>';
                        data += '<td style="vertical-align: middle;">' + value.date + '</td>';
                        if (value.type_enum === 1) {
                            data += '<td style="vertical-align: middle; color:green;">' + value.value + ' <span style="color:red;">' + value.anticipable_value + '</span> </td>';
                        } else {
                            data += '<td style="vertical-align: middle; color:red;">' + value.value + '</td>';
                        }
                        data += '</tr>';
                    });

                    $("#table-transfers-body").html(data);

                    pagination(response, 'transfers', updateTransfersTable);
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

    updateWithdrawalsTable();

    function updateWithdrawalsTable(link = null) {

        loadOnTable('table-withdrawals-body', 'transfersTable');
        $("#table-withdrawals-body").html('');

        if (link == null) {
            link = '/withdrawals';
        } else {
            link = '/withdrawals' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            data: {company: $("#extract_company_select").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#table-withdrawals-body").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#withdrawals-table-data").html('');
                if (response.data == '') {
                    $("#withdrawals-table-data").html("<tr><td colspan='5' class='text-center'>Nenhum saque realizado até o momento</td></tr>");
                    $("#withdrawals-pagination").html("");
                } else {
                    $.each(response.data, function (index, value) {
                        data = '';
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

});
