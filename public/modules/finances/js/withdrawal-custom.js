    $("#bt-withdrawal, #bt-withdrawal_m").on("click", function () {
        const availableBalanceText = $(".available-balance")
            .html()
            .replace(/,/g, "")
            .replace(/\./g, "");
        const toTransferText = $("#custom-input-addon")
            .val()
            .replace(/,/g, "")
            .replace(/\./g, "");
        const availableBalance = parseInt(availableBalanceText);
        const toTransfer = parseFloat(toTransferText);

        if (!verifyWithdrawalIsValid(toTransfer, availableBalance)) {
            return;
        }

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
                Authorization: $('meta[name="access-token"]').attr(
                    "content"
                ),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                manipulateModalWithdrawal(response.data);
    
                $("#bt-confirm-withdrawal").unbind("click");
                $("#bt-confirm-withdrawal").on("click", function () {
                    loadOnModal("#modal-body");
    
                    $("#bt-confirm-withdrawal").attr(
                        "disabled",
                        "disabled"
                    );
                    $.ajax({
                        url: "/api/withdrawals",
                        type: "POST",
                        data: {
                            company_id: $(
                                "#transfers_company_select"
                            ).val(),
                            withdrawal_value: $(".s-btn.green").data(
                                "value"
                            ),
                        },
                        dataType: "json",
                        headers: {
                            Authorization: $(
                                'meta[name="access-token"]'
                            ).attr("content"),
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
    
                            $(".btn-return").on("click", function () {
                                $("#custom-input-addon").val("");
                            });
    
                            $(".btn-return").click(function () {
                                $(
                                    ".modal-body #modal-body-withdrawal"
                                ).modal("hide");
                            });
    
                            updateBalances();
                        },
                        complete: (response) => {
                            $("#bt-confirm-withdrawal").removeAttr(
                                "disabled"
                            );
                        },
                    });
                });
            },
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
            alertCustom(
                "error",
                "O valor requerido ultrapassa o limite disponivel"
            );
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

        return true;
    }

    function manipulateModalWithdrawal(dataWithdrawal) {
        dataWithdrawal = {
            lower_value: dataWithdrawal.lower_value,
            bigger_value: dataWithdrawal.bigger_value,
        };

        const currentBalance = $(".available-balance")
            .text()
            .replace(/,/g, "")
            .replace(/\./g, ""); // SALDO DISPONIVEL
        const withdrawal = $("#custom-input-addon")
            .val()
            .replace(/,/g, "")
            .replace(/\./g, ""); // SAQUE DESEJADO
        const debitValue = $(".debt-balance")
            .text()
            .replace(/,/g, "")
            .replace(/\./g, ""); // DIVIDA ATUAL

        const singleValue = modalValueIsSingleValue(
            dataWithdrawal,
            currentBalance,
            withdrawal,
            debitValue
        );

        let withdrawRequestValid = false;
        let totalBalanceNegative = false;

        $("#modal-body-withdrawal, #modal-withdraw-footer").html("");

        if (debitValue != undefined) {
            let biggerValueIsZero =
                dataWithdrawal.bigger_value -
                removeFormatNumbers(debitValue);
            let lowerValueIsZero =
                dataWithdrawal.lower_value -
                removeFormatNumbers(debitValue);
            let debitVerify =
                removeFormatNumbers(currentBalance) -
                removeFormatNumbers(debitValue);

            if (debitVerify < 1) {
                totalBalanceNegative = true;

                $("#modal-withdrawal-title").text(
                    "Não é possivel realizar este saque"
                );

                $("#debit-pending-informations")
                    .html(
                        `
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
                                    <span
                                        class="currency"
                                        style="font: normal normal 300 19px/13px Roboto;
                                                color: #41DC8F;"
                                    >
                                        <span id="requested-amount-withdrawal" class="text-right" style="color: #41DC8F;">${formatMoney(
                                            removeFormatNumbers(
                                                currentBalance
                                            )
                                        )}</span>
                                        </span>
                                </div>
                            </div>
                            <div class="row" style="background-color:#F41C1C1A;">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-value-debt-pending">DÉBITOS PENDENTES</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Roboto; color: #E61A1A;">
                                        <span id="value-withdrawal-debt-pending" class="text-right" style="color: #F41C1C;">${formatMoney(
                                            removeFormatNumbers(debitValue)
                                        )}</span>
                                    </span>
                                </div>
                            </div>
                             <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-amount-receivable">SALDO FINAL</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span
                                        class="currency"
                                        style="font: normal normal 300 19px/13px Roboto;
                                                color: #E61A1A;"
                                    >
                                        <span id="value-withdrawal-received" class="text-right" style="color: #5E5E5E;"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `
                    )
                    .show();

                let result =
                    currentBalance - removeFormatNumbers(debitValue);
                $("#value-withdrawal-received").text(formatMoney(result));

                $("#modal-withdraw-footer").html(`
                    <hr>
                    <div class="col-md-12 text-center">
                        <button
                            class="btn col-5 s-btn-border"
                            data-dismiss="modal"
                            aria-label="Close"
                            style="font-size:20px; width:180px; border-radius: 12px; color:#FFFFFF; background-color: #2E85EC;">
                            Ok, entendi!
                        </button>
                    </div>
                `);
            } else if (biggerValueIsZero < 1 && lowerValueIsZero < 1) {
                // DOIS VALORES DO SAQUE SÃO MENORES QUE O VALOR DO DEBITO PENDENTE
                withdrawRequestValid = true;
                $("#modal-body-withdrawal").html("");
                $("#text-description-debit-pending").html("");

                $("#modal-withdrawal-title").text(
                    "Não é possivel realizar este saque"
                );

                $("#debit-pending-informations")
                    .html(
                        `
                    <div class="col-12">
                        <h3 class="text-center mt-10" id="text-title-debit-pending"> Você tem débitos pendentes superiores ao <br> valor solicitado no saque.</h3>

                        <div id="debit-itens">
                            <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;">VALOR SOLICITADO</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span
                                        class="currency"
                                        style="font: normal normal 300 19px/13px Roboto;
                                                color: #41DC8F;"
                                    >
                                        <span id="requested-amount-withdrawal" class="text-right" style="color: #41DC8F;">${
                                            dataWithdrawal.bigger_value
                                        }</span>
                                        </span>
                                </div>
                            </div>
                            <div class="row" style="background-color:#F41C1C1A;">
                                <div class='col-md-8 mt-10' >
                                    <p style="color: #5A5A5A;" id="modal-text-value-debt-pending">DÉBITOS PENDENTES</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Roboto; color: #E61A1A;">
                                        <span id="value-withdrawal-debt-pending" class="text-right" style="color: #F41C1C;">${formatMoney(
                                            removeFormatNumbers(debitValue)
                                        )}</span>
                                    </span>
                                </div>
                            </div>
                             <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-amount-receivable">VALOR NEGATIVO</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span
                                        class="currency"
                                        style="font: normal normal 300 19px/13px Roboto;
                                                color: #E61A1A;"
                                    >
                                        <span id="value-withdrawal-received" class="text-right" style="color: #5E5E5E;"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `
                    )
                    .show();

                $("#modal-withdraw-footer").html(`
                    <hr>
                    <div class="col-md-12 text-center">
                        <button
                            class="btn col-5 s-btn-border"
                            data-dismiss="modal"
                            aria-label="Close"
                            style="font-size:20px; width:200px; border-radius: 12px; color:#FFFFFF; background-color: #2E85EC;">
                            Ok, entendi!
                        </button>
                    </div>
                `);

                $("#requested-amount-withdrawal").text(
                    formatMoney(withdrawal)
                );

                let result =
                    removeFormatNumbers(withdrawal) -
                    removeFormatNumbers(debitValue);
                $("#value-withdrawal-received").text(formatMoney(result));
                $("#debit-pending-informations").show();
            }
        }

        if (
            withdrawRequestValid === false &&
            totalBalanceNegative === false
        ) {
            const htmlModal = optionsValuesWithdrawal(
                singleValue,
                dataWithdrawal
            );

            $(
                "#modal-body-withdrawal, #debit-pending-informations, #text-title-debit-pending,#text-description-debit-pending"
            ).html("");
            $("#modal-withdrawal-title").html("").html("Confirmar Saque");

            $("#modal-body-withdrawal").html(`
                <div>
                    <div class="mt-10 mb-10">
                        <h3 class="text-center mb-1">
                            ${
                                singleValue
                                    ? "Saque disponível:"
                                    : "Saques disponíveis:"
                            }
                        </h3>
                        <p class="text-center">
                            ${
                                singleValue
                                    ? ""
                                    : "Selecione o valor que mais se encaixa a sua solicitação"
                            }
                        </p>
                        <h3 class="text-center">
                            <div class="radio-custom radio-primary mt-25" id="more-than-on-values-show">
                                ${htmlModal}
                            </div>

                        </h3>
                    </div>
                </div>
            `);

            if (debitValue != undefined && debitValue != "0,00") {
                $("#debit-pending-informations")
                    .html(
                        `
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
                                    <span
                                        class="currency"
                                        style="font: normal normal 300 19px/13px Roboto;
                                                color: #41DC8F;"
                                    >
                                        <span id="requested-amount-withdrawal" class="text-right" style="color: #41DC8F;">${
                                            dataWithdrawal.bigger_value
                                        }</span>
                                        </span>
                                </div>
                            </div>
                            <div class="row" style="background-color:#F41C1C1A;">
                                <div class='col-md-8 mt-10' >
                                    <p style="color: #5A5A5A;" id="modal-text-value-debt-pending">DÉBITOS PENDENTES</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span class="currency" style="font: normal normal 300 19px/13px Roboto; color: #E61A1A;">
                                        <span id="value-withdrawal-debt-pending" class="text-right" style="color: #F41C1C;">${formatMoney(
                                            removeFormatNumbers(debitValue)
                                        )}</span>
                                    </span>
                                </div>
                            </div>
                             <div class="row">
                                <div class='col-md-8 mt-10'>
                                    <p style="color: #5A5A5A;" id="modal-text-amount-receivable">VALOR A RECEBER</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span
                                        class="currency"
                                        style="font: normal normal 300 19px/13px Roboto;
                                                color: #E61A1A;"
                                    >
                                        <span id="value-withdrawal-received" class="text-right" style="color: #5E5E5E;"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `
                    )
                    .show();

                const newValueSelected = $(
                    `#${$("#modal-body-withdrawal .s-btn.green").attr(
                        "id"
                    )}`
                );
                $("#requested-amount-withdrawal").text(
                    newValueSelected.text().trim()
                );
                $("#value-withdrawal-received").text(
                    formatMoney(
                        newValueSelected.data("value") -
                            removeFormatNumbers(debitValue)
                    )
                );
            }

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
        }

        $("#modal-withdrawal-custom").modal("show");

        $("#bigger-value, #lower-value, #single-value").click(function () {
            $("#bigger-value, #lower-value, #single-value").removeClass(
                "green"
            );
            const optionSelected = $(this).attr("id");
            const newValueSelected = $(`#${optionSelected}`).addClass(
                "green"
            );
            $("#requested-amount-withdrawal").text(
                newValueSelected.text().trim()
            );

            if (debitValue != undefined) {
                let result =
                    $(`#${optionSelected}`).data("value") -
                    removeFormatNumbers(debitValue);
                $("#value-withdrawal-received").text(formatMoney(result));
            }
        });
    }

    function modalValueIsSingleValue(
        dataWithdrawal,
        currentBalance,
        withdrawal,
        debitValue
    ) {
        let valueLowerIsNegative = 2;

        if (debitValue != undefined) {
            valueLowerIsNegative =
                dataWithdrawal.lower_value -
                removeFormatNumbers(debitValue);
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

// });

