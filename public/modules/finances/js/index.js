$(document).ready(function () {

    // $.getScript("modules/withdrawals/js/index.js", function () {
    // })

    $('.withdrawal-value').mask('#.###,#0', {reverse: true});

    $("#pop-antecipacao").click(function () {
        if ($("#antecipa-popover").css('display') == 'none') {
            $("#antecipa-popover").fadeIn(200);
        } else {
            $("#antecipa-popover").fadeOut(100);
        }
    });

    $("#transfers_company_select").on("change", function () {
        $("#extract_company_select").val($(this).val());
        $('#custom-input-addon').val('');
        updateBalances();
    });

    $("#extract_company_select").on("change", function () {
        $("#transfers_company_select").val($(this).val());
        updateBalances();
    });

    updateBalances();

    function updateBalances() {

        $(".price").append("<span class='loading'>" +
            "<span class='loaderSpan' >" +
            "</span>" +
            "</span>");

        $.ajax({
            url: "/finances/getbalances/" + $("#transfers_company_select").val(),
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            },
            success: function (response) {
                $('.saldoPendente').html('<span class="currency">R$</span><span class="pending-balance">0,00</span>');
                $('.removeSpan').remove();
                $('.disponivelAntecipar').append('<span class="currency removeSpan">R$</span><span class="antecipable-balance removeSpan">0,00</span>');
                $('.saldoDisponivel').html('<span class="currency">R$</span><span class="available-balance">0,00 <i class="material-icons ml-5" style="color: #44a44b;">arrow_forward</i></span>');
                $('.saltoTotal').html('<span class="currency">R$</span><span class="total-balance">0,00</span>');
                $('.totalConta').html('<span class="currency">R$</span><span class="total-balance">0,00</span>');
                $('.total_available').html('<span class="currency">R$</span>' + response.available_balance);
                $(".currency").html(response.currency);
                $(".available-balance").html(response.available_balance);
                $(".antecipable-balance").html(response.antecipable_balance);
                $(".pending-balance").html(response.pending_balance);
                $(".total-balance").html(response.total_balance);
                $(".loading").remove();
                $("#div-available-money").unbind('click');
                $("#div-available-money").on("click", function () {
                    $(".withdrawal-value").val(response.available_balance);
                });
                $.getScript('modules/withdrawals/js/index.js')
                $("#table-withdrawals-body").html('');

            }
        });

    }

    $('#bt-withdrawal').on('click', function () {
        if ($('#custom-input-addon').val() == '') {
            alertCustom('error', 'Valor do saque inválido!');
        } else {
            $.ajax({
                url: "/withdrawals/getaccountinformation/" + $("#transfers_company_select").val(),
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                error: function () {
                    //
                },
                success: function (response) {
                    if (response.data.user_documents_status == 'pending') {
                        let route = '/profile'
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!")
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' +
                            '<h3 align="center"><strong>Documentos pessoais ainda não validados</strong></h3>' +
                            '<h4 align="center">Parece que ainda existe pendencias com seus documentos</h4>' +
                            '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' +
                            '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + route + '">clique aqui</a></h5>')
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.documents_status == 'pending') {
                        let companie = $('#transfers_company_select').val();
                        let route = '/companies/' + companie + '/edit'
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!")
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' +
                            '<h3 align="center"><strong>Documentos da empresa ainda não validados</strong></h3>' +
                            '<h4 align="center">Parece que ainda existe pendencias com os documentos de sua empresa</h4>' +
                            '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' +
                            '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + route + '">clique aqui</a></h5>')
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else {
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Confirmar Saque")
                        $('#modal_body').html('<div>' +
                            '<h5>Verifique os dados da conta:</h5>' +
                            '<h4>Banco:<span id="modal-withdrawal-bank"></span></h4>' +
                            '<h4>Agência:<span id="modal-withdrawal-agency"></span><span id="modal-withdrawal-agency-digit"></span></h4>' +
                            '<h4>Conta:<span id="modal-withdrawal-account"></span><span id="modal-withdrawal-account-digit"></span></h4>' +
                            '<h4>Documento:<span id="modal-withdrawal-document"></span></h4>' +
                            '<hr>' +
                            '<h3>Valor do saque:<span id="modal-withdrawal-value" class=\'greenGradientText\'></span></h3>' +
                            '</div>')
                        $('#modal-withdraw-footer').html('<button id="bt-confirm-withdrawal" class="btn btn-success" style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">' +
                            '<strong>Confirmar</strong></button>' +
                            '<button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal" aria-label="Close" style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">' +
                            '<strong>Cancelar</strong></button>');

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
                            // $.getScript('modules/withdrawals/js/index.js')
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
                                error: function (response) {
                                    loadingOnScreenRemove()

                                    if (response.status === 422) {
                                        for (error in response.errors) {
                                            alertCustom('error', String(response.errors[error]));
                                        }
                                    } else if (response.status === 400) {
                                        $('#modal-withdrawal').modal('show');

                                        $('#modal-withdrawal-title').text("Oooppsssss!")
                                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' +
                                            '<h3 align="center"><strong>Algo deu errado!</strong></h3>' +
                                            '<h4 align="center">' + String(response.responseJSON.message) + '</h4>' +
                                            '<h4 align="center">Entre em contato com o suporte para mais informações</h4>')
                                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                                    } else {
                                        alertCustom('error', String(response.responseJSON.message));
                                    }

                                },
                                success: function (response) {
                                    loadingOnScreenRemove()
                                    $('#modal-withdrawal').modal('show');
                                    $('#modal-withdrawal-title').text("Sucesso!")
                                    $('#modal_body').html('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' +
                                        '<h3 align="center"><strong>Saque realizado com sucesso!</strong></h3>' +
                                        '<h4 align="center">Sua solicitação foi para avaliação</h4>' +
                                        '<h4 align="center">Em alguns instantes seu dinheiro estara em sua conta</h4>')
                                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                                    $('.btn-return').click(function () {
                                        $('#modal_body').modal('hide');
                                        updateWithdrawalsTable()
                                        updateBalances();

                                    });

                                }
                            })
                        });
                    }
                }
            });

        }

    });

    $('#custom-input-addon').on('change paste keyup', function () {
        let availableBalanceText = $('.available-balance').html().replace(',', '').replace('.', '')
        let toTransferText = $('#custom-input-addon').val().replace(',', '').replace('.', '')
        let availableBalance = parseInt(availableBalanceText);
        let toTransfer = parseFloat(toTransferText);
        if (toTransfer > availableBalance) {
            alertCustom('error', 'O valor requerido ultrapassa o limite disponivel')
            toTransferText = $('#custom-input-addon').val()
            toTransfer = toTransferText.slice(0, -2)
            $('#custom-input-addon').val(toTransfer);
            $('#custom-input-addon').val().update();
            $('.withdrawal-value').mask('#.###,#0', {reverse: true});

        } else {
        }
    })

});







