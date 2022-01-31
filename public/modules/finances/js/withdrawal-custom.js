$("#bt-withdrawal, #bt-withdrawal_m").on("click", function () {

    const availableBalanceText = $(".available-balance").html().replace(/,/g, "").replace(/\./g, "");
    const toTransferText = $("#custom-input-addon").val().replace(/,/g, "").replace(/\./g, "");
    const availableBalance = parseInt(availableBalanceText);
    const toTransfer = parseFloat(toTransferText);

    if (!verifyWithdrawalIsValid(toTransfer, availableBalance)) {
        return;
    }

    $("#bt-withdrawal, #bt-withdrawal_m").attr("disabled", "disabled");

    $.ajax({
        url: "/api/withdrawals/getWithdrawalValues",
        type: "POST",
        dataType: "json",
        data: {
            company_id: $("#transfers_company_select").val(),
            gateway_id: window.gatewayCode,
            withdrawal_value: $("#custom-input-addon").val(),
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
        complete: () => {
            $("#bt-withdrawal, #bt-withdrawal_m").removeAttr("disabled");
        }
    });
});
function verifyWithdrawalIsValid(toTransfer, availableBalance) {
    if (toTransfer < 1) {
        alertCustom("error", "Valor do saque inválido!");
        $("#custom-input-addon").val("");
        $(".withdrawal-value").maskMoney({
            thousands: ".",
            decimal: ",",
            allowZero: true,
        });
        return false;
    }

    if (toTransfer > availableBalance) {
        alertCustom("error", "O valor requerido ultrapassa o limite disponivel");
        $("#custom-input-addon").val("");
        $(".withdrawal-value").maskMoney({
            thousands: ".",
            decimal: ",",
            allowZero: true,
        });
        return false;
    }

    if ($("#custom-input-addon").val() == "") {
        alertCustom("error", "Valor do saque inválido!");
        return false;
    }

    if(toTransfer < 5000){
        alertCustom('error', 'Valor mínimo de saque  R$ 50,00');
        return;
    }

    return true;
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
function modalEmptyWithdrawal(withdrawal) {
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

    $modal
        .removeClass('d-none')
        .html(`
                <h3 class="text-center mt-10" id="text-title-debit-pending">
                    Você não possui nenhuma venda com o valor inferior ao saldo disponível para efetuar o saque.
                </h3>
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
function modalCustomWithdrawal(singleValue, dataWithdrawal, debitValue) {
    const $options = optionsValuesWithdrawal(singleValue, dataWithdrawal);

    const $modal = $("#modal-body-withdrawal-custom")
    const $footer = $("#modal-withdrawal-custom-footer")

    const $modalDebitPending = $('#debit-pending-informations')
    const $modalCustomTitle = $("#modal-title-withdrawal-custom")

    $modalDebitPending
        .html('')
        .addClass('d-none')

    $modalCustomTitle
        .text("Confirmar saque")
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
        const $newValueSelected = $modal.find(".value-select")
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

    const $event = $("#bigger-value, #lower-value")
    $event.off("click");
    $event.on("click", function () {
        const $value = $(this);
        const $amountWithdrawal = $("#requested-amount-withdrawal");

        $event.removeClass("green value-select");
        $value.addClass("green value-select");
        $amountWithdrawal.text($value.text().trim());

        if (debitValue != undefined) {
            const $valueWithdrawal = $("#value-withdrawal-received")

            let result = $value.data("value") - removeFormatNumbers(debitValue);
            $valueWithdrawal.text(formatMoney(result));
        }
    });

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
                withdrawal_value: $(".value-select").data("value"),
                gateway_id: window.gatewayCode,
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
                    $("#custom-input-addon").val("");
                    $(".modal-body #modal-body-withdrawal-custom").modal("hide");
                });

                updateBalances();
            },
            complete: (response) => {
                $("#bt-confirm-withdrawal-modal-custom").removeAttr("disabled");
            },
        });
    });
}

function manipulateModalWithdrawal(dataWithdrawal) {
    dataWithdrawal = {
        lower_value: dataWithdrawal.lower_value,
        bigger_value: dataWithdrawal.bigger_value,
    };

    const currentBalance = $(".available-balance").first().text().replace(/,/g, "").replace(/\./g, "");
    const withdrawal = $("#custom-input-addon").first().val().replace(/,/g, "").replace(/\./g, "");
    const debitValue = onlyNumbers($(".debt-balance").first().text().replace(/,/g, "").replace(/\./g, ""));

    const singleValue = modalValueIsSingleValue(dataWithdrawal, currentBalance, withdrawal, debitValue);

    let withdrawRequestValid = false;
    let totalBalanceNegative = false;

    if (debitValue != undefined) {
        let biggerValueIsZero = dataWithdrawal.bigger_value - removeFormatNumbers(debitValue);
        let lowerValueIsZero = dataWithdrawal.lower_value - removeFormatNumbers(debitValue);
        let debitVerify = removeFormatNumbers(currentBalance) - removeFormatNumbers(debitValue);

        if (debitVerify < 1 && debitValue > 1) {
            totalBalanceNegative = true;
            modalDebitPending(currentBalance, debitValue);
        } else if (biggerValueIsZero < 1 && lowerValueIsZero < 1 && debitValue > 1) {
            withdrawRequestValid = true;
            modalDebitWithdrawal(dataWithdrawal.bigger_value, debitValue, withdrawal);
        } else if (biggerValueIsZero < 1 && lowerValueIsZero < 1 ) {
            withdrawRequestValid = true;
            modalEmptyWithdrawal(withdrawal);
        }
    }

    if (withdrawRequestValid === false && totalBalanceNegative === false) {
        modalCustomWithdrawal(singleValue, dataWithdrawal, debitValue)
    }

    $("#modal-withdrawal-custom").modal("show");
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
                <div id="just-value-show" class="text-center mt-25">
                    <div id="single-value" class="value-select" data-value="${dataWithdrawal.bigger_value}">
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

                    <div class="col-auto btn btn-primary s-btn s-btn-border green value-select" id="bigger-value" data-value="${dataWithdrawal.bigger_value}">
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
    })
        .replace(/\s+/g, '')
        .replace('-', '- ')
}

