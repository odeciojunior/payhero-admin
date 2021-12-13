window.defaultWithdrawal = function(gatewayId) {

    let availableBalanceText = onlyNumbers($('#available-balance-' + gatewayId).html());
    let toTransferText = onlyNumbers($('#withdrawal-value-' + gatewayId).val());
    let availableBalance = parseInt(availableBalanceText);
    let toTransfer = parseFloat(toTransferText);

    if ($('#modal-withdrawal').css('display') === 'none') {
        $('#modal-withdrawal').removeAttr("style")
    }

    if (toTransfer > availableBalance) {

        alertCustom('error', 'O valor requerido ultrapassa o limite disponivel');
        toTransferText = $('#withdrawal-value-' + gatewayId).val();
        toTransfer = toTransferText.slice(0, -2);
        $('#withdrawal-value-' + gatewayId).val('');
        $('.withdrawal-value').maskMoney({thousands: '.', decimal: ',', allowZero: true});

    } else if ($('#withdrawal-value-' + gatewayId).val() == '') {
        alertCustom('error', 'Valor do saque inválido!');

    } else {
        $.ajax({
                url: "/api/withdrawals/getaccountinformation",
                type: "POST",
                dataType: "json",
                data: {
                    company_id: $("#transfers_company_select").val(),
                    withdrawal_value: $('#withdrawal-value-' + gatewayId).val()
                },
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    if (response.data.user_documents_status == 'pending') {
                        let route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos pessoais ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com seus documentos</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.documents_status == 'pending') {
                        let companie = $('#transfers_company_select').val();
                        let _route = '/companies/' + companie + '/edit';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos da empresa ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com os documentos de sua empresa</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.email_verified == 'false') {
                        let _route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Email de usuário ainda não foi verificado</strong></h3>' + '<h4 align="center">Para maior segurança é necessário validar o e-mail do usuário na página de perfil</h4>' + '<h5 align="center">Deseja ir a pagina de perfil? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.cellphone_verified == 'false') {
                        let _route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Telefone de usuário ainda não foi verificado</strong></h3>' + '<h4 align="center">Para maior segurança é necessário validar o telefone do usuário na página de perfil</h4>' + '<h5 align="center">Deseja ir a pagina de perfil? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');

                    } else {

                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Confirmar Saque");

                        let confirmationData = `<div class="row">
                                                    <div class="col">
                                                        <h4>Verifique os dados da conta:</h4>
                                                        <div><b>Banco:</b><span id="modal-withdrawal-bank"></span></div>
                                                        <div><b>Agência:</b><span id="modal-withdrawal-agency"></span><span id="modal-withdrawal-agency-digit"></span></div>
                                                        <div><b>Conta:</b><span id="modal-withdrawal-account"></span><span id="modal-withdrawal-account-digit"></span></div>
                                                        <div><b>Documento:</b><span id="modal-withdrawal-document"></span></div>
                                                        </div>`;

                        confirmationData += `</div>
                                                <hr>
                                                <h4>Valor do saque: <span id="modal-withdrawal-value" class='greenGradientText'></span>`;

                        $('#modal_body').html(confirmationData);

                        $("#modal-withdraw-footer").html(`
                            <div class="col-md-12 text-center">
                                <button
                                    id="bt-cancel-withdrawal"
                                    class="btn col-5 s-btn-border"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                    style="font-size:20px; width:200px; border-radius: 12px; color:#818181;">
                                    Cancelar
                                </button>

                                <button
                                    id="bt-confirm-withdrawal"
                                    class="btn btn-success col-5 btn-confirmation s-btn-border"
                                    style="background-color: #41DC8F;font-size:20px; width:200px;">
                                    <strong>Confirmar</strong>
                                </button>
                            </div>
                        `);

                        $("#modal-withdrawal-value").html(' R$ ' + $('#withdrawal-value-' + gatewayId).val() + ' ');
                        $("#modal-withdrawal-bank").html('  ' + response.data.bank);
                        $("#modal-withdrawal-agency").html('  ' + response.data.agency);
                        if (response.data.agency_digit != '' && response.data.agency_digit != null) {
                            $("#modal-withdrawal-agency-digit").html(' - ' + response.data.agency_digit);
                        }
                        $("#modal-withdrawal-account").html('  ' + response.data.account);
                        if (response.data.account_digit != '' && response.data.account_digit != null) {

                            $("#modal-withdrawal-account-digit").html(' - ' + response.data.account_digit);
                        }
                        $("#modal-withdrawal-document").html('  ' + response.data.document);

                        $("#bt-confirm-withdrawal").off("click");
                        $("#bt-confirm-withdrawal").on("click", function () {
                            loadOnModal('#modal-body');

                            $("#bt-confirm-withdrawal").attr('disabled', 'disabled');
                            $.ajax({
                                url: "/api/withdrawals",
                                type: "POST",
                                data: {
                                    company_id: $('#transfers_company_select').val(),
                                    withdrawal_value: $('#withdrawal-value-' + gatewayId).val(),
                                    gateway_id: gatewayId,
                                },
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: (response) => {
                                    errorAjaxResponse(response);
                                },
                                success: (response) => {
                                    $('#modal-withdrawal').modal('show');
                                    $('#modal-withdrawal-title').text("Sucesso!");
                                    $('#modal_body').html('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' + '<h3 align="center"><strong>Sua solicitação foi para avaliação!</strong></h3>');
                                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                                    $('.btn-return').on('click', function () {
                                        $('#withdrawal-value-' + gatewayId).val('');
                                    });
                                    $('#gateway-skeleton').show();
                                    $('#container-all-gateways').html('');
                                    $('#val-skeleton').show();
                                    $('#container_val').css('display','none');
                                    $('#skeleton-withdrawal').show();
                                    $('#container-withdraw').html('');
                                    $('#empty-history').hide();
                                    $('.asScrollable').hide();
                                    updateStatements();
                                    updateWithdrawals();

                                    $('.btn-return').on(function () {
                                        $('#modal_body').modal('hide');
                                    });
                                },
                                complete: (response) => {
                                    $("#bt-confirm-withdrawal").removeAttr('disabled');
                                }
                            });
                        });
                    }
                }
            }
        );
    }
}

window.customWithdrawal = function(gatewayId) {

    let availableBalanceText = onlyNumbers($('#available-balance-' + gatewayId).html());
    let toTransferText = onlyNumbers($('#withdrawal-value-' + gatewayId).val());
    let availableBalance = parseInt(availableBalanceText);
    let toTransfer = parseFloat(toTransferText);

    if (!verifyWithdrawalIsValid(toTransfer, availableBalance)) {
        return;
    }

    $("#request-withdrawal-" + gatewayId).attr("disabled", "disabled");


    $.ajax({
        url: "/api/withdrawals/getWithdrawalValues",
        type: "POST",
        dataType: "json",
        data: {
            company_id: $("#transfers_company_select").val(),
            gateway_id: gatewayId,
            withdrawal_value: $("#withdrawal-value-" + gatewayId).val(),
        },
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: (response) => {
            errorAjaxResponse(response);
        },
        success: (response) => {
            manipulateModalWithdrawal(response.data);
        },
        complete: (response) => {
            $("#request-withdrawal-" + gatewayId).removeAttr("disabled");
            loadingOnScreenRemove();
        }
    });

    $(document).off('click', '#bt-confirm-withdrawal-modal-custom');
    $(document).on('click', '#bt-confirm-withdrawal-modal-custom', function () {

        loadOnModal("#modal-body-withdrawal-custom");

        $("#bt-confirm-withdrawal-modal-custom").attr("disabled", "disabled");

        $.ajax({
            url: "/api/withdrawals",
            type: "POST",
            data: {
                company_id: $("#transfers_company_select").val(),
                withdrawal_value: $(".s-btn.green").data("value"),
                gateway_id: gatewayId,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadingOnScreenRemove();
                loadOnAny(".price", true);
                manipulateModalSuccessWithdrawal();

                $(".btn-return").off("click");
                $(".btn-return").on("click", function () {
                    $("#withdrawal-value-" + gatewayId).val("");
                    $(".modal-body, #modal-body-withdrawal-custom").modal("hide");
                });
                $('#gateway-skeleton').show();
                $('#container-all-gateways').html('');
                $('#val-skeleton').show();
                $('#container_val').css('display','none');
                $('#skeleton-withdrawal').show();
                $('#container-withdraw').html(' ');
                $('#empty-history').hide();
                $('.asScrollable').hide();
                updateStatements();
                updateWithdrawals();
            },
            complete: (response) => {
                $("#bt-confirm-withdrawal-modal-custom").removeAttr("disabled");
            },
        });
    });

    function verifyWithdrawalIsValid(toTransfer, availableBalance) {
        if (toTransfer < 1) {
            alertCustom("error", "Valor do saque inválido!");
            $("#withdrawal-value-" + gatewayId).val("");
            $(".withdrawal-value").maskMoney({
                thousands: ".",
                decimal: ",",
                allowZero: true,
            });
            return false;
        }

        if (toTransfer > availableBalance) {
            alertCustom("error", "O valor requerido ultrapassa o limite disponivel");
            $("#withdrawal-value-" + gatewayId).val("");
            $(".withdrawal-value").maskMoney({
                thousands: ".",
                decimal: ",",
                allowZero: true,
            });
            return false;
        }

        if ($("#withdrawal-value-" + gatewayId).val() == "") {
            alertCustom("error", "Valor do saque inválido!");
            return false;
        }

        return true;
    }

    function manipulateModalWithdrawal(dataWithdrawal) {
        dataWithdrawal = {
            lower_value: dataWithdrawal.lower_value,
            bigger_value: dataWithdrawal.bigger_value,
        };

        const currentBalance = $("#available-balance-" + gatewayId).text().replace(/,/g, "").replace(/\./g, "");
        const withdrawal = $("#withdrawal-value-" + gatewayId).val().replace(/,/g, "").replace(/\./g, "");
        const debitValue = onlyNumbers($("#pending-debt-" + gatewayId).val());

        const singleValue = modalValueIsSingleValue(dataWithdrawal, currentBalance, withdrawal, debitValue);

        let withdrawRequestValid = false;
        let totalBalanceNegative = false;

        $("#modal-body-withdrawal-custom, #modal-withdrawal-custom-footer").html("");

        if (debitValue != undefined) {
            let biggerValueIsZero = dataWithdrawal.bigger_value - removeFormatNumbers(debitValue);
            let lowerValueIsZero = dataWithdrawal.lower_value - removeFormatNumbers(debitValue);
            let debitVerify = removeFormatNumbers(currentBalance) - removeFormatNumbers(debitValue);

            if (debitVerify < 1) {
                totalBalanceNegative = true;

                $("#modal-withdrawal-custom-title").text(
                    "Não é possivel realizar este saque"
                );

                $("#debit-pending-informations").html(`
                    <div class="col-12">
                        <h3 class="text-center mt-10" id="text-title-debit-pending">Você tem débitos pendentes superiores ao <br> valor do seu saldo disponível.</h3>
                            <p style="color: #959595;" class="text-center" id="text-description-debit-pending">
                            Você só poderá solicitar um saque quando seu saldo disponível for maior <br> que o valor dos débitos pendentes.
                        </p>
                        <div id="debit-itens">
                            <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;">SALDO DISPONÍVEL</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #41DC8F;">
                                        <span id="requested-amount-withdrawal" class="text-right" style="color: #41DC8F;">${formatMoney(removeFormatNumbers(currentBalance))}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="row" style="background-color:#F41C1C1A;">
                                <div class='col-md-8 d-flex align-items-center py-10'>
                                    <p class="m-0" style="color: #5A5A5A;" id="modal-text-value-debt-pending">DÉBITOS PENDENTES</p>
                                </div>
                                <div class="col-md-4 d-flex align-items-center justify-content-end">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #E61A1A;">
                                        <span id="value-withdrawal-debt-pending" class="text-right" style="color: #F41C1C;">
                                            ${formatMoney(removeFormatNumbers(debitValue))}
                                        </span>
                                    </span>
                                </div>
                            </div>
                                <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-amount-receivable">SALDO FINAL</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #E61A1A;">
                                        <span id="value-withdrawal-received" class="text-right" style="color: #5E5E5E;"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`
                ).show();

                let result = currentBalance - removeFormatNumbers(debitValue);

                $("#value-withdrawal-received").text(formatMoney(result));

                $("#modal-withdrawal-custom-footer").html(`
                    <hr>
                    <div class="col-md-12 text-center">
                        <button class="btn col-5 s-btn-border" data-dismiss="modal" aria-label="Close" style="font-size:20px; width:180px; border-radius: 12px; color:#FFFFFF; background-color: #2E85EC;">
                            Ok, entendi!
                        </button>
                    </div>
                `);
            } else if (biggerValueIsZero < 1 && lowerValueIsZero < 1) {

                withdrawRequestValid = true;
                $("#modal-body-withdrawal-custom").html("").addClass('d-none');
                $("#text-description-debit-pending").html("");

                $("#modal-withdrawal-custom-title").text(
                    "Não é possivel realizar este saque"
                );

                $("#debit-pending-informations")
                    .html(`
                    <div class="col-12">
                        <h3 class="text-center mt-10" id="text-title-debit-pending"> Você tem débitos pendentes superiores ao <br> valor solicitado no saque.</h3>
                        <div id="debit-itens">
                            <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;">VALOR SOLICITADO</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #41DC8F;">
                                        <span id="requested-amount-withdrawal" class="text-right" style="color: #41DC8F;">
                                            ${dataWithdrawal.bigger_value}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="row" style="background-color:#F41C1C1A;">
                                <div class='col-md-8 d-flex align-items-center py-10'>
                                    <p class="m-0" style="color: #5A5A5A;" id="modal-text-value-debt-pending">DÉBITOS PENDENTES</p>
                                </div>
                                <div class="col-md-4 d-flex align-items-center justify-content-end">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #E61A1A;">
                                        <span id="value-withdrawal-debt-pending" class="text-right" style="color: #F41C1C;">
                                            ${formatMoney(removeFormatNumbers(debitValue))}
                                        </span>
                                    </span>
                                </div>
                            </div>
                                <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-amount-receivable">VALOR NEGATIVO</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #E61A1A;">
                                        <span id="value-withdrawal-received" class="text-right" style="color: #5E5E5E;"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).show();

                $("#modal-withdrawal-custom-footer").html(`
                    <hr>
                    <div class="col-md-12 text-center">
                        <button class="btn col-5 s-btn-border" data-dismiss="modal" aria-label="Close" style="font-size:20px; width:200px; border-radius: 12px; color:#FFFFFF; background-color: #2E85EC;">
                            Ok, entendi!
                        </button>
                    </div>
                `);

                $("#requested-amount-withdrawal").text(formatMoney(withdrawal));

                let result = removeFormatNumbers(withdrawal) - removeFormatNumbers(debitValue);
                $("#value-withdrawal-received").text(formatMoney(result));
                $("#debit-pending-informations").show();
            }
        }

        if (withdrawRequestValid === false && totalBalanceNegative === false) {
            const htmlModal = optionsValuesWithdrawal(singleValue, dataWithdrawal);

            $("#modal-body-withdrawal-custom, #debit-pending-informations, #text-title-debit-pending,#text-description-debit-pending").html("");
            $("#modal-body-withdrawal-custom").removeClass('d-none');
            $("#modal-withdrawal-custom-title").html("").html("Confirmar Saque");

            $("#modal-body-withdrawal-custom").html(`
                <div>
                    <div class="mt-10 mb-10">
                        <h3 class="text-center mb-1">
                            ${singleValue ? "Saque disponível:" : "Saques disponíveis:"}
                        </h3>
                        <p class="text-center">
                            ${singleValue ? "" : "Selecione o valor que mais se encaixa a sua solicitação"}
                        </p>
                        <h3 class="text-center">
                            <div class="radio-custom radio-primary mt-25" id="more-than-on-values-show">
                                ${htmlModal}
                            </div>
                        </h3>
                    </div>
                </div>
            `);

            if (debitValue != undefined && debitValue != "0,00" && debitValue != '000') {
                $("#debit-pending-informations")
                    .html(`
                    <div class="col-12">
                        <h3 class="text-center mt-10" id="text-title-debit-pending"> Débitos pendentes</h3>
                        <p style="color: #959595;" class="text-center" id="text-description-debit-pending">
                            Você tem alguns valores em aberto, confira:
                        </p>
                        <div id="debit-itens">
                            <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;">VALOR SOLICITADO</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #41DC8F;">
                                        <span id="requested-amount-withdrawal" class="text-right" style="color: #41DC8F;">
                                            ${dataWithdrawal.bigger_value}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="row" style="background-color:#F41C1C1A;">
                                <div class='col-md-8 d-flex align-items-center py-10'>
                                    <p class="m-0" style="color: #5A5A5A;" id="modal-text-value-debt-pending">DÉBITOS PENDENTES</p>
                                </div>
                                <div class="col-md-4 d-flex align-items-center justify-content-end">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #E61A1A;">
                                        <span id="value-withdrawal-debt-pending" class="text-right" style="color: #F41C1C;">
                                            ${formatMoney(removeFormatNumbers(debitValue))}
                                        </span>
                                    </span>
                                </div>
                            </div>
                                <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-amount-receivable">VALOR A RECEBER</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Muli; color: #E61A1A;">
                                        <span id="value-withdrawal-received" class="text-right" style="color: #5E5E5E;"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).show();

                const newValueSelected = $(`#${$("#modal-body-withdrawal-custom .s-btn.green").attr("id")}`);

                $("#requested-amount-withdrawal").text(newValueSelected.text().trim());

                $("#value-withdrawal-received").text(formatMoney(newValueSelected.data("value") - removeFormatNumbers(debitValue)));
            }

            $("#modal-withdrawal-custom-footer").html(`
                <div class="col-md-12 text-center">
                    <button id="bt-cancel-withdrawal" class="btn col-5 s-btn-border" data-dismiss="modal" aria-label="Close" style="font-size:20px; width:200px; border-radius: 12px; color:#818181;">
                        Cancelar
                    </button>

                    <button id="bt-confirm-withdrawal-modal-custom" class="btn btn-success col-5 btn-confirmation s-btn-border" style="background-color: #41DC8F;font-size:20px; width:200px;">
                        <strong>Confirmar</strong>
                    </button>
                </div>
            `);
        }

        $("#modal-withdrawal-custom").modal("show");

        $("#bigger-value, #lower-value, #single-value").off("click");
        $("#bigger-value, #lower-value, #single-value").on("click", function () {
            $("#bigger-value, #lower-value, #single-value").removeClass("green");

            const optionSelected = $(this).attr("id");
            const newValueSelected = $(`#${optionSelected}`).addClass("green");
            $("#requested-amount-withdrawal").text(newValueSelected.text().trim());

            if (debitValue != undefined) {
                let result = $(`#${optionSelected}`).data("value") - removeFormatNumbers(debitValue);
                $("#value-withdrawal-received").text(formatMoney(result));
            }
        });
    }

    function manipulateModalSuccessWithdrawal() {
        $("#debit-pending-informations").html("");

        $("#modal-withdrawal-custom-title").text("Sucesso!");
        $(".modal-body #modal-body-withdrawal-custom").html(`
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
            <h3 align="center">
                <strong>Sua solicitação foi para avaliação!</strong>
            </h3>`);
        $("#modal-withdrawal-custom-footer").html(`
            <div style="width:100%;text-align:center;padding-top:3%">
                <span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">
                    Retornar
                </span>
            </div>`);
    }

    function modalValueIsSingleValue(dataWithdrawal, currentBalance, withdrawal, debitValue) {

        let valueLowerIsNegative = 2;

        if (debitValue != undefined) {
            valueLowerIsNegative = dataWithdrawal.lower_value - removeFormatNumbers(debitValue);
        }

        return (
            valueLowerIsNegative < 1 ||
            dataWithdrawal.lower_value == 0 ||
            dataWithdrawal.bigger_value == withdrawal ||
            dataWithdrawal.lower_value == withdrawal ||
            currentBalance == dataWithdrawal.bigger_value
        );
    }

    function removeFormatNumbers(number) {
        number += '';
        return number.replace(/,/g, "").replace(/\./g, "");
    }

    function optionsValuesWithdrawal(singleValue, dataWithdrawal) {
        const biggerValue = formatMoney(dataWithdrawal.bigger_value);

        const lowerValue = formatMoney(dataWithdrawal.lower_value);

        if (singleValue) {
            return `
                <div id="just-value-show" class="text-center mt-25 radio-custom radio-primary">
                    <div class="btn btn-primary mr-4 s-btn s-btn-border green" id="single-value" data-value="${dataWithdrawal.bigger_value}">
                        ${biggerValue}
                    </div>
                </div>
            `;
        }

        return `
            <div class="">
                <div class="row justify-content-center">

                    <div class="btn btn-primary mr-4 s-btn s-btn-border" id="lower-value" data-value="${dataWithdrawal.lower_value}">
                        ${lowerValue}
                    </div>

                        <div class="btn btn-primary s-btn s-btn-border green" id="bigger-value" data-value="${dataWithdrawal.bigger_value}">
                        ${biggerValue}
                    </div>
                </div>
            </div>
        `;
    }

    function formatMoney(value) {
        return (value / 100).toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
        });
    }
}

