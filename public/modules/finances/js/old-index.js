$(document).ready(function () {
    const statusWithdrawals = {
        1: "warning",
        2: "primary",
        3: "success",
        4: "danger",
        5: "in_review",
        6: "process",
        8: "primary",
        9: "partially-liquidating",
    };

    const statusExtract = {
        WAITING_FOR_VALID_POST: "warning",
        WAITING_LIQUIDATION: "warning",
        WAITING_WITHDRAWAL: "warning",
        WAITING_RELEASE: "warning",
        PAID: "success",
        REVERSED: "warning",
        ADJUSTMENT_CREDIT: "success",
        ADJUSTMENT_DEBIT: "danger",
        ERROR: "error",
    };

    function formatMoney(value) {
        return (value / 100).toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
        });
    }

    //Comportamentos da tela
    $("#date_range").daterangepicker({
        startDate: moment().startOf("week"),
        endDate: moment(),
        opens: "center",
        maxDate: moment().endOf("day"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: "Customizado",
        autoUpdateInput: true,
        locale: {
            locale: "pt-br",
            format: "DD/MM/YYYY",
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: "De",
            toLabel: "Até",
            customRangeLabel: "Customizado",
            weekLabel: "W",
            daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
            monthNames: [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro",
            ],
            firstDay: 0,
        },
        ranges: {
            Hoje: [moment(), moment()],
            Ontem: [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
            "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
            "Este mês": [moment().startOf("month"), moment().endOf("month")],
            "Mês passado": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
        },
    });

    $(".withdrawal-value").maskMoney({
        thousands: ".",
        decimal: ",",
        allowZero: true,
    });

    let balanceLoader = {
        styles: {
            container: {
                minHeight: "31px",
                justifyContent: "flex-start",
            },
            loader: {
                width: "20px",
                height: "20px",
                borderWidth: "4px",
            },
        },
        insertBefore: ".grad-border",
    };

    $("#date_range_statement_unique").val(moment().format("YYYY-MM-DD"));

    //END - Comportamentos da tela

    //Obtém as empresas
    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
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
                if (isEmpty(response.data)) {
                    $(".page-content").hide();
                    $("#empty-companies-error").show();
                    loadingOnScreenRemove();
                    return;
                }

                let itsApprovedTransactGetnet = false;

                $(".page-content").show();
                $("#menu-tabs-view").show();
                $(".content-error").hide();

                $(response.data).each(function (index, value) {
                    if (value.capture_transaction_enabled) {
                        itsApprovedTransactGetnet = true;
                        let dataHtml = `<option country="${value.country}" value="${value.id}">${value.name}</option>`;
                        $("#statement_company_select").append(dataHtml);
                        $("#transfers_company_select").append(dataHtml);
                        $("#transfers_company_select_mobile").append(dataHtml);
                    }
                });

                if (!itsApprovedTransactGetnet) {
                    $(".page-content").hide();
                    $("#companies-not-approved-getnet").show();
                    loadingOnScreenRemove();
                    return;
                }
                $(".card-show-content-finances").show();

                updateAccountStatementData();
                updateBalances();
                checkAllowed();
                loadingOnScreenRemove();
            },
        });
    }

    function paginationStatement() {
        $("#pagination-statement").jPages({
            containerID: "table-statement-body",
            perPage: 10,
            startPage: 1,
            startRange: 1,
            first: false,
            previous: false,
            next: false,
            last: false,
            delay: 1,
        });
    }

    getCompanies();

    $("#ir-agenda").on("click", function (e) {
        e.preventDefault();

        let company = $("#transfers_company_select").val();
        $("#statement_status_select").val("ADJUSTMENT_DEBIT");
        $("#statement_company_select").val(company);
        $("#bt_filtro_statement, #nav-statement-tab").click();
    });

    //Verifica se o saque está liberado
    function checkAllowed() {
        $.ajax({
            url: "/api/withdrawals/checkallowed",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
                $("#bt-withdrawal, #bt-withdrawal_m")
                    .prop("disabled", true)
                    .addClass("disabled");
            },
            success: (response) => {
                if (response.allowed && verifyAccountFrozen() == false) {
                    $("#bt-withdrawal, #bt-withdrawal_m")
                        .prop("disabled", false)
                        .removeClass("disabled");
                    $("#blocked-withdrawal").hide();
                } else {
                    $("#bt-withdrawal, #bt-withdrawal_m")
                        .prop("disabled", true)
                        .addClass("disabled");
                    $("#blocked-withdrawal").show();
                }
            },
        });
    }

    //atualiza o saldo
    $(document).on("change", "#transfers_company_select", function () {
        $(
            "#transfers_company_select option[value=" +
                $("#transfers_company_select option:selected").val() +
                "]"
        ).prop("selected", true);
        $("#custom-input-addon").val("");
        updateBalances();
        if ($(this).children("option:selected").attr("country") != "brazil") {
            $("#col_transferred_value").show();
        } else {
            $("#col_transferred_value").hide();
        }
    });

    function updateBalances() {
        // loadOnTable("#withdrawals-table-data", "#transfersTable");
        loadOnAny(".number", false, {
            styles: {
                container: {
                    minHeight: "32px",
                    height: "auto",
                },
                loader: {
                    width: "20px",
                    height: "20px",
                    borderWidth: "4px",
                },
            },
        });
        loadOnTable("#withdrawals-table-data", "#withdrawalsTable");

        $.ajax({
            url: "/api/old_finances/getbalances",
            type: "GET",
            data: {
                company: $("#transfers_company_select option:selected").val(),
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadOnAny(".number", true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadOnAny(".number", true);
                $(".saldoPendente").html(
                    '<span style="color:#959595">R$ </span><span class="font-size-30 bold pending-balance">0,00</span>'
                );
                $(".saldoDisponivel").html(
                    '<span style="color:#959595">R$ </span><span class="font-size-30 bold available-balance">0,00</span>'
                );
                $(".saltoTotal").html(
                    '<span style="color:#959595">R$ </span><span class="font-size-30 bold total-balance">0,00</span>'
                );
                $(".saldoBloqueado").html(
                    '<span style="color:#959595">R$ </span><span class="font-size-30 bold blocked-balance">0,00</span>'
                );
                $(".saldoDebito").html(
                    //'<span style="color:#959595">R$ </span><span class="font-size-30 bold debit-balance">0,00</span>'
                    '<span style="color:#959595">R$ </span><a href="javascript:;" id="go-to-pending-debt" class="currency debit-balance font-size-30 bold debit-balanc" style="color: #E61A1A;">0,00</a>'
                );

                // Saldo bloqueado
                $(".saldoBloqueado").html(
                    '<span style="color:#959595">R$ </span><span class="font-size-30 bold blocked-balance">' +
                        response.blocked_balance +
                        "</span>"
                );
                // Saldo Debito Pendente
                $("#go-to-pending-debt").show();
                $("#go-to-pending-debt").html(response.pending_debt_balance);

                $(".totalConta").html(
                    '<span style="color:#959595">R$ </span><span class="total-balance">0,00</span>'
                );
                $(".total_available").html(
                    '<span style="color:#959595">R$ </span>' +
                        isEmpty(response.available_balance)
                );

                $(".available-balance")
                    .html(isEmpty(response.available_balance))
                    .attr("data-value", isEmpty(response.available_balance));
                $(".debit-balance")
                    .html(isEmpty(response.pending_debt_balance))
                    .attr("data-value", isEmpty(response.pending_debt_balance));
                $(".pending-balance").html(isEmpty(response.pending_balance));
                $(".total-balance").html(isEmpty(response.total_balance));

                $("#div-available-money, #div-available-money_m").unbind(
                    "click"
                );
                $("#div-available-money, #div-available-money_m").on(
                    "click",
                    function () {
                        $(".withdrawal-value").val(
                            isEmpty(response.available_balance)
                        );
                    }
                );

                $("#go-to-pending-debt").on("click", function () {
                    selectPendingDebt();
                    $("#bt_filtro_statement").click();
                });

                loadWithdrawalsTable();
            },
        });

        function isEmpty(value) {
            if (value.length === 0) {
                return 0;
            } else {
                return value;
            }
        }

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

        // Fazer saque
        $("#bt-withdrawal, #bt-withdrawal_m").unbind("click");
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

        function manipulateModalWithdrawal(dataWithdrawal) {
            dataWithdrawal = {
                lower_value: dataWithdrawal.lower_value,
                bigger_value: dataWithdrawal.bigger_value,
            };

            const currentBalance = $(".available-balance")
                .data("value")
                .replace(/,/g, "")
                .replace(/\./g, ""); // SALDO DISPONIVEL
            const withdrawal = $("#custom-input-addon")
                .val()
                .replace(/,/g, "")
                .replace(/\./g, ""); // SAQUE DESEJADO
            const debitValue = $(".debit-balance")
                .data("value")
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

            $("#modal-withdrawal").modal("show");

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

        function removeFormatNumbers(number) {
            return number.replace(/,/g, "").replace(/\./g, "");
        }

        function manipulateModalSuccessWithdrawal() {
            $("#debit-pending-informations").html("");

            $("#modal-withdrawal-title").text("Sucesso!");
            $(".modal-body #modal-body-withdrawal").html(`
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                <h3 align="center">
                    <strong>Sua solicitação foi para avaliação!</strong>
                </h3>`);
            $("#modal-withdraw-footer").html(`
                <div style="width:100%;text-align:center;padding-top:3%">
                    <span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">
                        Retornar
                    </span>
                </div>`);

            $("#modal-withdrawal").modal("show");
        }

        function loadWithdrawalsTable(link = null) {
            $("#pagination-withdrawals, #withdrawals-table-data").html("");
            loadOnTable("#withdrawals-table-data", "#transfersTable");

            if (link == null) {
                link = "/api/withdrawals";
            } else {
                link = "/api/withdrawals" + link;
            }

            $.ajax({
                method: "GET",
                url: link,
                data: {
                    company: $(
                        "#transfers_company_select option:selected"
                    ).val(),
                },
                dataType: "json",
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
                    $("#withdrawals-table-data").html("");

                    if (
                        response.data === "" ||
                        response.data === undefined ||
                        response.data.length === 0
                    ) {
                        $("#withdrawals-table-data").html(
                            "<tr style='border-radius: 16px;'><td colspan='6' class='text-center' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                                $("#withdrawals-table-data").attr("img-empty") +
                                "'> Nenhum saque realizado até o momento</td></tr>"
                        );
                        $("#withdrawals-pagination").html("");
                        return;
                    }

                    let tableData = "";

                    $.each(response.data, function (index, data) {
                        tableData += `<tr class="s-table table-finance-transfers">
                            <td style="grid-area: codigo">#${
                                data.id
                            }</td>
                            <td class="text-left font-size-14" style="grid-area: sale"> <strong>${
                                data.account_information_bank
                            }</strong> <br> <small class="gray font-size-12">${
                            data.account_information
                        }</small> </td>
                            <td class="text-left font-size-14" style="grid-area: date-start"> <strong class="bold-mobile">${
                                data.date_request
                            } </strong> <br> <small class="gray font-size-12"> ${
                            data.date_request_time
                        } </small></td>
                            <td class="text-left font-size-14" style="grid-area: date-end"> <strong class="bold-mobile">${
                                data.date_release
                            } </strong> <br> <small class="gray font-size-12"> ${
                            data.date_release_time
                        } </small></td>
                            <td class="text-center" style="grid-area: status" class="shipping-status">
                                <span data-toggle="tooltip" data-placement="left" title="${
                                    data.status_translated
                                }" class="badge badge-${
                            statusWithdrawals[data.status]
                        }"> ${data.status_translated}</span>
                            </td>
                            <td class="text-left" style="grid-area: value"> <strong class="font-md-size-20">${
                                data.value
                            }</strong>
                        `;

                        if (
                            data.debt_pending_value != null &&
                            data.debt_pending_value != "R$ 0,00"
                        ) {
                            tableData += `<br> <a role='button' class='pending_debit_withdrawal_id pointer' withdrawal_id='${data.id}'><small class="gray" style="color: #F41C1C;">- ${data.debt_pending_value}</small></a>`;
                        }
                        tableData += `
                            </td>
                                <td class="d-none d-lg-block">
                                    <a role='button' class='details_transaction pointer' withdrawal='${data.id}'>
                                        <span class='o-eye-1'></span>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    $("#withdrawals-table-data").append(tableData);
                    $("#withdrawalsTable").addClass("table-striped");

                    $(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                    });

                    pagination(response, "withdrawals", loadWithdrawalsTable);
                },
            });
        }
    }

    //atualiza a table de extrato
    $(document).on("click", "#bt_filtro", function () {
        $(
            "#extract_company_select option[value=" +
                $("#extract_company_select option:selected").val() +
                "]"
        ).prop("selected", true);
        updateTransfersTable();
    });

    function updateTransfersTable(link = null) {
        $("#pagination-transfers, #table-transfers-body").html("");
        loadOnAnyEllipsis("#available-in-period", false, balanceLoader);

        loadOnTable("#table-transfers-body", "#transfersTable");
        if (link == null) {
            link = "/api/transfers";
        } else {
            link = "/api/transfers" + link;
        }

        let data = {
            company: $("#extract_company_select option:selected").val(),
            date_type: $("#date_type").val(),
            date_range: $("#date_range").val(),
            reason: $("#reason").val(),
            transaction: $("#transaction").val(),
            type: $("#type").val(),
            value: $("#transaction-value").val(),
        };

        $.ajax({
            method: "GET",
            url: link,
            data: data,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                let balance_in_period = response.meta.balance_in_period;
                let isNegative =
                    parseFloat(
                        balance_in_period.replace(".", "").replace(",", ".")
                    ) < 0;
                let availableInPeriod = $("#available-in-period");
                availableInPeriod.html(
                    `<span${
                        isNegative ? ' style="color:red;"' : ""
                    }><span class="currency">R$ </span>${balance_in_period}</span>`
                );
                if (isNegative) {
                    availableInPeriod
                        .html(
                            `<span style="color:red;"><span class="currency">R$ </span>${balance_in_period}</span>`
                        )
                        .parent()
                        .find(".grad-border")
                        .removeClass("green")
                        .addClass("red");
                } else {
                    availableInPeriod
                        .html(
                            `<span class="currency">R$ </span>${balance_in_period}`
                        )
                        .parent()
                        .find(".grad-border")
                        .removeClass("red")
                        .addClass("green");
                }

                loadOnAnyEllipsis("#available-in-period", true);

                if (response.data == "") {
                    $("#table-transfers-body").html(
                        "<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>"
                    );
                    return;
                }

                let data = "";

                $.each(response.data, function (index, value) {
                    data += "<tr >";
                    if (value.is_owner && value.sale_id) {
                        data += `<td style="vertical-align: middle;">
                            ${value.reason}
                            <a class="detalhes_venda disabled pointer-md" data-target="#modal_detalhes" data-toggle="modal" venda="${value.sale_id}">
                                <span style="color:black;">#${value.sale_id}</span>
                            </a><br>
                            <small>Venda em: ${value.sale_date}</small>
                         </td>`;
                    } else if (value.reason === "Antecipação") {
                        data += `<td style="vertical-align: middle;">${value.reason} <span style='color: black;'> #${value.anticipation_id} </span></td>`;
                    } else {
                        data += `<td style="vertical-align: middle;">${
                            value.reason
                        }${
                            value.sale_id
                                ? "<span> #" + value.sale_id + "</span>"
                                : ""
                        }</td>`;
                    }

                    data +=
                        '<td style="vertical-align: middle;">' +
                        value.date +
                        "</td>";
                    if (value.type_enum === 1) {
                        data += `<td style="vertical-align: middle; color:green;"> ${value.value}`;
                        if (value.reason === "Antecipação") {
                            data += `<br><small style='color:#543333;'>(Taxa: ${value.tax})</small> </td>`;
                        } else if (value.value_anticipable != "0,00") {
                            data += `<br><small style='color:#543333;'>(${value.value_anticipable} antecipados em <b>#${value.anticipation_id}</b> )</small> </td>`;
                        } else {
                            data += `</td>`;
                        }
                    } else {
                        data += `<td style="vertical-align: middle; color:red;"> ${value.value}</td> `;
                    }
                    data += "</tr>";
                });

                $("#table-transfers-body").html(data);

                pagination(response, "transfers", updateTransfersTable);
            },
        });
    }

    function updateAccountStatementData() {
        loadOnAnyEllipsis(
            "#nav-statement #available-in-period-statement",
            false,
            balanceLoader
        );

        $("#table-statement-body").html("");
        $("#pagination-statement").html("");
        loadOnTable("#table-statement-body", "#statementTable");

        let link =
            "/api/transfers/account-statement-data?dateRange=" +
            $("#date_range_statement").val() +
            "&company=" +
            $("#statement_company_select").val() +
            "&sale=" + encodeURIComponent(
            $("#statement_sale").val()) +
            "&status=" +
            $("#statement_status_select").val() +
            "&statement_data_type=" +
            $("#statement_data_type_select").val() +
            "&payment_method=" +
            $("#payment_method").val() +
            "&withdrawal_id=" +
            $("#withdrawal_id").val();

        $(".numbers").hide();

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadOnAnyEllipsis(
                    "#nav-statement #available-in-period-statement",
                    true
                );

                let error = "Erro ao gerar o extrato";
                $("#export-excel").css("opacity", 0);
                $("#table-statement-body").html(
                    "<tr style='border-radius: 16px;'><td style='padding:  10px !important' style='' colspan='11' class='text-center'>" +
                        error +
                        "</td></tr>"
                );
                errorAjaxResponse(error);
            },
            success: (response) => {
                updateClassHTML();

                let items = response.items;
                $("#statement-money #available-in-period-statement").html(
                    "R$ 0,00"
                );

                if (isEmpty(items)) {
                    loadOnAnyEllipsis(
                        "#nav-statement #available-in-period-statement",
                        true
                    );
                    $("#export-excel").css("opacity", 0);
                    $("#table-statement-body").html(
                        "<tr class='text-center'><td colspan='11' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                            $("#table-statement-body").attr("img-empty") +
                            "'>Nenhum dado encontrado</td></tr>"
                    );
                    return false;
                }

                items.forEach(function (item) {
                    let dataTable = `<tr class="s-table table-finance-schedule"><td style="vertical-align: middle; grid-area: sale;">`;

                    if (item.order && item.order.hashId) {
                        dataTable += `Transação`;

                        if (item.isInvite) {
                            dataTable += `
                                <a>
                                    <span class="bold">#${item.order.hashId}</span>
                                </a>
                            `;
                        } else {
                            dataTable += `
                                 <a class="detalhes_venda disabled pointer-md" data-target="#modal_detalhes" data-toggle="modal" venda="${item.order.hashId}">
                                    <span class="bold">#${item.order.hashId}</span>
                                </a>
                            `;
                        }
                        dataTable += `<br>
                                        <small>${item.details.description}</small>`;
                    } else {
                        dataTable += `${item.details.description}`;
                    }

                    dataTable += `
                         </td>
                        <td style="vertical-align: middle; grid-area: date">
                            ${item.date}
                        </td>
                         <td style="grid-area: status" class="text-center">
                            <span data-toggle="tooltip" data-placement="left" title="${
                                item.details.status
                            }" class="badge badge-sm badge-${
                        statusExtract[item.details.type]
                    } p-2">${item.details.status}</span>
                         </td>
                        <td class="text-xs-right text-md-left bold" style="vertical-align: middle;grid-area: value;};">
                        ${item.amount.toLocaleString("pt-BR", {
                            style: "currency",
                            currency: "BRL",
                        })}
                        </td>
                        </tr>`;

                    $(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                    });

                    updateClassHTML(dataTable);
                });

                let totalInPeriod = response.totalInPeriod ?? "0,00";

                let isNegativeStatement = false;
                if (totalInPeriod < 1) {
                    isNegativeStatement = true;
                }

                let aux = totalInPeriod.toLocaleString("pt-BR", {
                    style: "currency",
                    currency: "BRL",
                });

                $("#statement-money #available-in-period-statement").html(`
                    <span${isNegativeStatement ? ' style="color:red;"' : ""}>
                       <small class="font-size-12">R$ </small> ${totalInPeriod.toLocaleString(
                           "pt-BR"
                       )}
                    </span>`);
                paginationStatement();

                $("#export-excel").css("opacity", 1);
                $("#pagination-statement span").addClass("jp-hidden");
                $("#pagination-statement a")
                    .removeClass("active")
                    .addClass("btn nav-btn");
                $("#pagination-statement a.jp-current").addClass("active");
                $("#pagination-statement a").on("click", function () {
                    $("#pagination-statement a").removeClass("active");
                    $(this).addClass("active");
                });

                $("#pagination-statement").on("click", function () {
                    $("#pagination-statement span").remove();
                });

                loadOnAnyEllipsis(
                    "#nav-statement #statement-money  #available-in-period-statement",
                    true
                );
            },
        });
    }

    function updateClassHTML(dataTable = 0) {
        if (dataTable.length > 0) {
            $("#table-statement-body").append(dataTable);
            $("#statementTable").addClass("table-striped");
        } else {
            $("#table-statement-body").html("");
        }
    }

    //atualiza a table de statement
    $(document).on("click", "#bt_filtro_statement", function (e) {
        e.preventDefault();
        updateAccountStatementData();
    });

    let rangesToDateRangeStatement = {
        Hoje: [moment(), moment()],
        Ontem: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
        "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
        "Próximos 30 dias": [moment(), moment().add(29, "days")],
        "Este mês": [moment().startOf("month"), moment().endOf("month")],
        "Mês passado": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
        ],
    };

    let envDebug = $("meta[name=app-debug]").attr("content");

    //if (envDebug == 'true') {
    rangesToDateRangeStatement["Todo período"] = [
        moment().subtract(1, "year"),
        moment().add(40, "days"),
    ];
    //}

    $("#date_range_statement").daterangepicker({
        maxSpan: {
            days: 31,
        },
        startDate: moment().subtract(7, "days"),
        endDate: moment().add(7, "days"),
        opens: "center",
        maxDate: moment().add(1, "month"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: "Customizado",
        autoUpdateInput: true,
        locale: {
            locale: "pt-br",
            format: "DD/MM/YYYY",
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: "De",
            toLabel: "Até",
            customRangeLabel: "Customizado",
            weekLabel: "W",
            daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
            monthNames: [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro",
            ],
            firstDay: 0,
        },
        ranges: rangesToDateRangeStatement,
    });

    $('#statement_sale').on('change paste keyup select', function () {
        let val = $(this).val();

        if (val === "") {
            $("#date_range_statement")
                .attr("disabled", false)
                .removeClass("disableFields");
            $("#statement_data_type_select")
                .attr("disabled", false)
                .removeClass("disableFields");
        } else {
            $('#date_range_statement').attr('disabled', true).addClass('disableFields');
            $('#statement_data_type_select').attr('disabled', true).addClass('disableFields');
        }
    });

    $("#statement_status_select").on("change paste keyup select", function () {
        let val = $(this).val();

        if (val === "PENDING_DEBIT") {
            $("#date_range_statement")
                .attr("disabled", true)
                .addClass("disableFields");
            $("#payment_method")
                .attr("disabled", true)
                .addClass("disableFields");
            $("#statement_sale")
                .val("")
                .attr("disabled", true)
                .addClass("disableFields");
        } else {
            $("#date_range_statement")
                .attr("disabled", false)
                .removeClass("disableFields");
            $("#payment_method")
                .attr("disabled", false)
                .removeClass("disableFields");
            $("#statement_sale")
                .attr("disabled", false)
                .removeClass("disableFields");
        }
    });

    // Finances report

    let exportFinanceFormat = "xls";
    $("#bt_get_sale_xls").on("click", function () {
        $("#modal-export-finance-getnet").modal("show");
        exportFinanceFormat = "csv";
    });

    $("#bt_get_sale_csv").on("click", function () {
        $("#modal-export-finance-getnet").modal("show");
        exportFinanceFormat = "xls";
    });

    $(".btn-confirm-export-finance-getnet").on("click", function () {
        var regexEmail = new RegExp(
            /^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/
        );
        var email = $("#email_finance_export").val();

        if (email == "" || !regexEmail.test(email)) {
            alertCustom("error", "Preencha o e-mail corretamente");
            return false;
        } else {
            financesGetnetExport(exportFinanceFormat);
            $("#modal-export-finance-getnet").modal("hide");
        }
    });

    // Download do relatorio
    function financesGetnetExport(fileFormat) {
        let data = {
            dateRange: $("#date_range_statement").val(),
            company: $("#statement_company_select").val(),
            sale: $("#statement_sale").val(),
            status: $("#statement_status_select").val(),
            statement_data_type: $("#statement_data_type_select").val(),
            payment_method: $("#payment_method").val(),
            format: fileFormat,
            email: $("#email_finance_export").val(),
        };

        $.ajax({
            method: "POST",
            url: "/api/transfers/account-statement-data/export",
            data: data,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#export-finance-email").text(response.email);
                $("#alert-finance-export").show().shake();
            },
        });
    }

    $(".nav-link-finances-show-export").on("click", function () {
        $("#finances_export_btns")
            .css("opacity", 1)
            .removeClass("col-1")
            .addClass("col-6");

        $("#bt_get_sale_xls").removeClass("disabled");
        $("#bt_get_sale_csv").removeClass("disabled");
        $(".page-title").parent().removeClass("col-10");
    });

    $(".nav-link-finances-hide-export").on("click", function () {
        $("#finances_export_btns")
            .css("opacity", 0)
            .removeClass("col-6")
            .addClass("col-1");
        $("#bt_get_sale_xls").addClass("disabled");
        $("#bt_get_sale_csv").addClass("disabled");
        $(".page-title").parent().addClass("col-10");
    });

    $(".btn-light-1").click(function () {
        var collapse = $("#icon-filtro");
        var text = $("#text-filtro");

        text.fadeOut(10);
        if (
            collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" ||
            collapse.css("transform") == "none"
        ) {
            collapse.css("transform", "rotate(180deg)");
            text.text("Minimizar filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }
    });

    $("#pagination-statement").click(function () {
        setTimeout(function () {
            $(".s-table:visible").attr("style", "display: !");
            $(".table tr:visible:last > td:first").addClass("teste-1");
            $(".table tr:visible:last > td:last").addClass("teste-2");
        }, 100);
    });
});

/*$("#go-to-pending-debt").bind("click", function () {

    $("#nav-statement-tab").click();
    $('#statement_status_select option[value="PENDING_DEBIT"]').prop('selected', true);
    $("#bt_filtro_statement").click();
});*/

// $("#go-to-pending-debt").on("click", function () {
//     selectPendingDebt();
//     $("#bt_filtro_statement").click();
// });

$(document).on("click", ".pending_debit_withdrawal_id", function () {
    selectPendingDebt();

    $("#withdrawal_id").val($(this).attr("withdrawal_id"));
    $("#bt_filtro_statement").click();
    $("#withdrawal_id").val("");
});

function selectPendingDebt() {
    $("#nav-statement-tab").click();
    $('#statement_status_select option[value="PENDING_DEBIT"]').prop(
        "selected",
        true
    );

    $("#date_range_statement").attr("disabled", true).addClass("disableFields");
    $("#payment_method").attr("disabled", true).addClass("disableFields");
    $("#statement_sale")
        .val("")
        .attr("disabled", true)
        .addClass("disableFields");
}
