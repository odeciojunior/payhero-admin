window.defaultWithdrawal = function(gatewayId) {

    let availableBalanceText = onlyNumbers($('#available-balance-' + gatewayId).html());
    let toTransferText = onlyNumbers($('#withdrawal-value-' + gatewayId).val());
    let availableBalance = parseInt(availableBalanceText);
    let toTransfer = parseFloat(toTransferText);

    if ($('#modal-withdrawal').css('display') === 'none') {
        $('#modal-withdrawal').removeAttr("style")
    }

    if (!verifyWithdrawalIsValid(toTransfer, availableBalance, gatewayId)) {
        return;
    }

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
                if (response.data.user_pending === true || response.data.company_pending === true) {
                    modalDocsPending(response.data)
                } else {
                    dataWithdrawal = {
                        bigger_value: toTransfer,
                        lower_value: 0,
                    };

                    modalCustomWithdrawal(gatewayId,true, dataWithdrawal)
                }

                $("#modal-withdrawal-custom").modal("show");
            }
        });
}

window.customWithdrawal = function(gatewayId) {

    let availableBalanceText = onlyNumbers($('#available-balance-' + gatewayId).html());
    let toTransferText = onlyNumbers($('#withdrawal-value-' + gatewayId).val());
    let availableBalance = parseInt(availableBalanceText);
    let toTransfer = parseFloat(toTransferText);

    if (!verifyWithdrawalIsValid(toTransfer, availableBalance, gatewayId)) {
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

        if (debitValue != undefined) {
            let biggerValueIsZero = dataWithdrawal.bigger_value - removeFormatNumbers(debitValue);
            let lowerValueIsZero = dataWithdrawal.lower_value - removeFormatNumbers(debitValue);
            let debitVerify = removeFormatNumbers(currentBalance) - removeFormatNumbers(debitValue);

            if (debitVerify < 1) {
                totalBalanceNegative = true;
                modalDebitPending(currentBalance, debitValue);
            } else if (biggerValueIsZero < 1 && lowerValueIsZero < 1) {
                withdrawRequestValid = true;
                modalDebitWithdrawal(dataWithdrawal.bigger_value, debitValue, withdrawal);
            }
        }

        if (withdrawRequestValid === false && totalBalanceNegative === false) {
            modalCustomWithdrawal(gatewayId, singleValue, dataWithdrawal, debitValue)
        }

        $("#modal-withdrawal-custom").modal("show");
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
}

function modalDebitPending (currentBalance, debitValue) {

    const $modal = $("#debit-pending-informations")
    const $footer = $("#modal-withdrawal-custom-footer")

    const $modalCustomBody = $("#modal-body-withdrawal-custom")
    const $modalCustomTitle = $("#modal-title-withdrawal-custom")

    $modalCustomBody
        .html('')
        .addClass('d-none')
    $modalCustomTitle
        .text("Não é possivel realizar este saque")
        .parent()
        .addClass('debit-pending');

    let result = currentBalance - removeFormatNumbers(debitValue);
    $modal
        .removeClass('d-none')
        .html(`
                <h3 class="text-center mt-10" id="text-title-debit-pending">
                    Você tem débitos pendentes superiores ao <br> valor do seu saldo disponível.
                </h3>
                <p id="text-description-debit-pending">
                    Você só poderá solicitar um saque quando seu saldo disponível for maior <br>
                    que o valor dos débitos pendentes.
                </p>

                <div id="debit-items">
                    <div class="row mx-0">
                        <div class='col-7'><p> VALOR SOLICITADO </p></div>
                        <div class="col-5 pl-0 text-right">
                            <span class="currency">
                                <span id="requested-amount-withdrawal" class="text-right" style="color: #636363;">
                                    ${formatMoney(removeFormatNumbers(currentBalance))}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="row mx-0 my-20" style="background-color:#FFF2F2;;">
                        <div class='col-7 d-flex align-items-center py-20'><p> DÉBITOS PENDENTES </p></div>
                        <div class="col-5 pl-0 d-flex align-items-center justify-content-end">
                            <span class="currency">
                                <span id="value-withdrawal-debt-pending" class="text-right" style="color: #FF003D;">
                                    - ${formatMoney(removeFormatNumbers(debitValue))}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class='col-7'><p> FINAL </p></div>
                        <div class="col-5 pl-0 text-right">
                            <span class="currency">
                                <span id="value-withdrawal-received" class="text-right" style="color: #636363;">
                                    ${formatMoney(result)}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            `)
        .show();

    $footer
        .html(`
            <hr>
            <div class="row justify-content-center w-p100">
                <button class="btn col-auto s-btn-border" data-dismiss="modal" aria-label="Close"
                style="background-color: #2E85EC; color: #FFF">
                    Ok, entendi!
                </button>
            </div>
        `);
}
function modalDebitWithdrawal(currentBalance, debitValue, withdrawal) {
    const $modal = $("#debit-pending-informations")
    const $footer = $("#modal-withdrawal-custom-footer")

    const $modalCustomBody = $("#modal-body-withdrawal-custom")
    const $modalCustomTitle = $("#modal-title-withdrawal-custom")

    $modalCustomBody
        .html('')
        .addClass('d-none')
    $modalCustomTitle
        .text("Não é possivel realizar este saque")
        .parent()
        .addClass('debit-pending');

    const $amountWithdrawal = $("#requested-amount-withdrawal")
    $amountWithdrawal.text(formatMoney(withdrawal));

    let result = removeFormatNumbers(withdrawal) - removeFormatNumbers(debitValue);
    $modal
        .removeClass('d-none')
        .html(`
                <h3 class="text-center mt-10" id="text-title-debit-pending">
                    Você tem débitos pendentes superiores ao <br> valor do seu saldo disponível.
                </h3>
                <p id="text-description-debit-pending">
                    Você só poderá solicitar um saque quando seu valor solicitado for maior <br>
                    que o valor dos débitos pendentes.
                </p>

                <div id="debit-items">
                    <div class="row mx-0">
                        <div class='col-7'><p> VALOR SOLICITADO </p></div>
                        <div class="col-5 pl-0 text-right">
                            <span class="currency">
                                <span id="requested-amount-withdrawal" class="text-right" style="color: #636363;">
                                    ${formatMoney(removeFormatNumbers(currentBalance))}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="row mx-0 my-20" style="background-color:#FFF2F2;;">
                        <div class='col-7 d-flex align-items-center py-20'><p> DÉBITOS PENDENTES </p></div>
                        <div class="col-5 pl-0 d-flex align-items-center justify-content-end">
                            <span class="currency">
                                <span id="value-withdrawal-debt-pending" class="text-right" style="color: #FF003D;">
                                    - ${formatMoney(removeFormatNumbers(debitValue))}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class='col-7'><p> FINAL </p></div>
                        <div class="col-5 pl-0 text-right">
                            <span class="currency">
                                <span id="value-withdrawal-received" class="text-right" style="color: #636363;">
                                    ${formatMoney(result)}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            `)
        .show();

    $footer
        .html(`
            <hr>
            <div class="row justify-content-center w-p100">
                <button class="btn col-auto s-btn-border" data-dismiss="modal" aria-label="Close"
                style="background-color: #2E85EC; color: #FFF">
                    Ok, entendi!
                </button>
            </div>
        `);
}
function modalCustomWithdrawal(gatewayId, singleValue, dataWithdrawal, debitValue = 0) {
    const $options = optionsValuesWithdrawal(singleValue, dataWithdrawal);

    const $modal = $("#modal-body-withdrawal-custom")
    const $footer = $("#modal-withdrawal-custom-footer")

    const $modalDebitPending = $('#debit-pending-informations')
    const $modalCustomTitle = $("#modal-title-withdrawal-custom")

    $modalDebitPending
        .html('')
        .addClass('d-none')

    $modalCustomTitle
        .text("Confirmar Saque")
        .parent()
        .removeClass('debit-pending')

    $modal
        .removeClass('d-none')
        .html(`
                <h3 id="text-title-withdrawal-custom" class="text-center mb-1">
                    ${singleValue ? "Valor a ser sacado" : "Valores disponíveis:"}
                </h3>
                <p id="text-description-withdrawal-custom" class="text-center ${singleValue ? "" : "mb-30"}">
                    ${singleValue ? "" : "Selecione o valor que mais se encaixa a sua solicitação"}
                </p>
                <div class="text-center">
                    <div id="more-than-on-values-show">
                        ${$options}
                    </div>
                </div>
            `);

    if (!isEmptyValue(debitValue)) {
        const $newValueSelected = $modal.find(".s-btn.green")
        const $value = $newValueSelected.text().trim();

        let result = $newValueSelected.data("value") - removeFormatNumbers(debitValue)
        $modalDebitPending
            .removeClass('d-none')
            .html(`
                    <h3 class="text-center mt-10 mb-0" id="text-title-debit-pending"> Débitos pendentes </h3>
                    <p class="mt-5" id="text-description-debit-pending">
                        Você tem alguns valores em aberto
                    </p>

                    <div id="debit-items">
                        <div class="row mx-0">
                            <div class='col-7'><p> VALOR SOLICITADO </p></div>
                            <div class="col-5 pl-0 text-right">
                                <span class="currency">
                                    <span id="requested-amount-withdrawal" class="text-right" style="color: #636363;">
                                        ${$value}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="row mx-0 my-20" style="background-color:#FFF2F2;">
                            <div class='col-7 d-flex align-items-center py-20'><p> DÉBITOS PENDENTES </p></div>
                            <div class="col-5 pl-0 d-flex align-items-center justify-content-end">
                                <span class="currency">
                                    <span id="value-withdrawal-debt-pending" class="text-right" style="color: #FF003D;">
                                        - ${formatMoney(removeFormatNumbers(debitValue))}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="row mx-0">
                            <div class='col-7'><p>VALOR A RECEBER</p></div>
                            <div class="col-5 pl-0 text-right">
                                <span class="currency">
                                    <span id="value-withdrawal-received" class="text-right" style="color: #1BE4A8;">
                                        ${formatMoney(result)}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                `)
            .show();
    }

    $footer
        .html(`
                <div class="row justify-content-center w-p100">
                    <button id="bt-cancel-withdrawal" data-dismiss="modal" aria-label="Close"
                    class="btn col-auto s-btn-border mr-10"
                    style="color: #959595;">
                        Cancelar
                    </button>

                    <button id="bt-confirm-withdrawal-modal-custom"
                    class="btn btn-success col-auto btn-confirmation s-btn-border m-0"
                    style="background-color: #1BE4A8;">
                        <strong>Confirmar</strong>
                    </button>
                </div>
            `);

    const $event = $("#bigger-value, #lower-value, #single-value")
    $event.off("click");
    $event.on("click", function () {
        const $value = $(this);
        const $amountWithdrawal = $("#requested-amount-withdrawal");

        $event.removeClass("green");
        $value.addClass("green");
        $amountWithdrawal.text($value.text().trim());

        if (debitValue != undefined) {
            const $valueWithdrawal = $("#value-withdrawal-received")

            let result = $value.data("value") - removeFormatNumbers(debitValue);
            $valueWithdrawal.text(formatMoney(result));
        }
    });

    $(document).off('click', '#bt-confirm-withdrawal-modal-custom');
    $(document).on('click', '#bt-confirm-withdrawal-modal-custom', function (e) {

        var click = $(this);
        if (click.data('clicked')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        click.data('clicked', true);

        window.setTimeout(function(){
            click.removeData('clicked');
        }, 2000);

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
}
function modalDocsPending(data) {
    const $modal = $("#debit-pending-informations")
    const $footer = $("#modal-withdrawal-custom-footer")

    const $modalCustomBody = $("#modal-body-withdrawal-custom")
    const $modalCustomTitle = $("#modal-title-withdrawal-custom")

    let description =
        `Parece que ainda existe pendencias com seus documentos <br>
         Seria bom conferir se todos os documentos já foram cadastrados`

    if (data.company_pending) {
        description =
            `Parece que ainda existe pendencias com os documentos de sua empresa <br>
             Seria bom conferir se todos os documentos já foram cadastrados.`
    }

    $modalCustomBody
        .html('')
        .addClass('d-none')
    $modalCustomTitle
        .text("Você tem documentos pendentes")
        .parent()
        .removeClass('debit-pending');

    $modal
        .removeClass('d-none')
        .html(`
                <div class="text-center my-10">
                    <svg width="151" height="150" viewBox="0 0 151 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M75.5 150C116.921 150 150.5 116.421 150.5 75C150.5 33.5786 116.921 0 75.5 0C34.0786 0 0.5 33.5786 0.5 75C0.5 116.421 34.0786 150 75.5 150Z" fill="url(#paint0_linear_729_70)"/>
                        <path d="M120.5 150H30.5V53C34.742 52.9952 38.8089 51.308 41.8084 48.3085C44.808 45.3089 46.4952 41.242 46.5 37H104.5C104.496 39.1014 104.908 41.1828 105.713 43.1238C106.518 45.0648 107.7 46.8268 109.191 48.308C110.672 49.7991 112.434 50.9816 114.375 51.787C116.317 52.5924 118.398 53.0047 120.5 53V150Z" fill="white"/>
                        <path d="M75.5 102C88.7548 102 99.5 91.2548 99.5 78C99.5 64.7452 88.7548 54 75.5 54C62.2452 54 51.5 64.7452 51.5 78C51.5 91.2548 62.2452 102 75.5 102Z" fill="#4285F4"/>
                        <path d="M83.9853 89.3139L75.5 80.8286L67.0147 89.3139L64.1863 86.4854L72.6716 78.0002L64.1863 69.5149L67.0147 66.6865L75.5 75.1717L83.9853 66.6865L86.8137 69.5149L78.3284 78.0002L86.8137 86.4854L83.9853 89.3139Z" fill="white"/>
                        <path d="M88.5 108H62.5C60.8431 108 59.5 109.343 59.5 111C59.5 112.657 60.8431 114 62.5 114H88.5C90.1569 114 91.5 112.657 91.5 111C91.5 109.343 90.1569 108 88.5 108Z" fill="#DFEAFB"/>
                        <path d="M97.5 120H53.5C51.8431 120 50.5 121.343 50.5 123C50.5 124.657 51.8431 126 53.5 126H97.5C99.1569 126 100.5 124.657 100.5 123C100.5 121.343 99.1569 120 97.5 120Z" fill="#DFEAFB"/>
                        <defs>
                        <linearGradient id="paint0_linear_729_70" x1="75.5" y1="0" x2="75.5" y2="150" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#E3ECFA"/>
                        <stop offset="1" stop-color="#DAE7FF"/>
                        </linearGradient>
                        </defs>
                    </svg>
                </div>
                <h3 class="text-center" id="text-title-withdrawal-custom">
                    Não é possível realizar saque sem a confirmação dos documentos.
                </h3>
                <p id="text-description-withdrawal-custom">
                    ${description}
                </p>
            `)
        .show();

    $footer
        .html(`
            <div class="row justify-content-center w-p100">
                <a class="pointer" href="${data.route}">
                    <button class="btn col-auto s-btn-border mr-10" style="background-color: #2E85EC; color: #FFF">
                        Enviar documentos
                    </button>
                </a>

                <button class="btn btn-outline-default col-auto s-btn-border m-0"
                style="border-color: #484848;" data-dismiss="modal" aria-label="Close">
                    <strong style="color: #484848">Deixar para depois</strong>
                </button>
            </div>
        `);
}
function manipulateModalSuccessWithdrawal() {
    $("#debit-pending-informations").html("");

    $("#modal-title-withdrawal-custom").text("Sucesso!");
    $(".modal-body #modal-body-withdrawal-custom").html(`
        <svg style="max-width: 70px; max-height: 70px;" class="checkmark"
        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
        <h3 id="text-title-withdrawal-custom" class="text-center">
            <strong>Sua solicitação foi para avaliação!</strong>
        </h3>`);
    $("#modal-withdrawal-custom-footer").html(`
            <div style="width:100%;text-align:center;padding-top:3%">
                <span class="btn btn-success btn-return" data-dismiss="modal">
                    Retornar
                </span>
            </div>`);
}

function verifyWithdrawalIsValid(toTransfer, availableBalance, gatewayId) {
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

    // if(toTransfer < 5000){
    //     alertCustom('error', 'Valor mínimo de saque  R$ 50,00');
    //     return;
    // }
    return true;
}
function optionsValuesWithdrawal(singleValue, dataWithdrawal) {
    const biggerValue = formatMoney(dataWithdrawal.bigger_value);
    const lowerValue = formatMoney(dataWithdrawal.lower_value);

    if (singleValue) {
        return `
                <div id="just-value-show" class="text-center">
                    <div id="single-value" data-value="${dataWithdrawal.bigger_value}">
                        ${biggerValue}
                    </div>
                </div>
            `;
    }

    return `
            <div>
                <div class="row justify-content-center w-p100 m-0">
                    <div class="col-auto btn btn-primary s-btn s-btn-border mr-10" id="lower-value" data-value="${dataWithdrawal.lower_value}">
                        ${lowerValue}
                    </div>

                    <div class="col-auto btn btn-primary s-btn s-btn-border green" id="bigger-value" data-value="${dataWithdrawal.bigger_value}">
                        ${biggerValue}
                    </div>
                </div>
            </div>
        `;
}
function removeFormatNumbers(number) {
    number += '';
    return number.replace(/,/g, "").replace(/\./g, "");
}
function formatMoney(value) {
    return (value / 100).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
    })
        .replace(/\s+/g, '')
        .replace('-', '- ');
}

function notHaveTwoRequisition(click, e) {
    if (click.data('clicked')) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
    click.data('clicked', true);

    window.setTimeout(function(){
        click.removeData('clicked');
    }, 2000);
}

