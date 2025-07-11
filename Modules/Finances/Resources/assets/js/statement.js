function getRequestTime(data = "") {
    let request_date = "";
    if (!isEmpty(data.date_request))
        request_date = `<div class="d-block d-md-none"> Solicitado em  </div>
                        <div class="bold-mobile"> ${data.date_request} </div>`;

    if (!isEmpty(data.date_request_time))
        request_date += `<span class="subdescription font-size-12"> ás ${data.date_request_time.replace(
            ":",
            "h"
        )} </small>`;

    return request_date;
}
function getReleaseTime(data = "") {
    let release_date = "";
    if (!isEmpty(data.date_release))
        release_date = `<div class="d-block d-md-none"> Liberado em  </div>
                        <div class="bold-mobile"> ${data.date_release} </div>`;

    if (!isEmpty(data.date_release_time))
        release_date += `<span class="subdescription font-size-12"> ás ${data.date_release_time.replace(
            ":",
            "h"
        )} </small>`;

    return release_date;
}
window.loadStatementTable = function () {
    if (window.gatewayCode == "w7YL9jZD6gp4qmv") {
        updateAccountStatementData();
    } else {
        updateTransfersTable();
    }
};

window.updateTransfersTable = function (link = null) {
    $("#table-transfers-body").html("");

    loadOnTable("#table-transfers-body", "#transfersTable");
    $("#pagination-transfers").children().attr("disabled", "disabled");

    if (link == null) {
        link = "/api/transfers";
    } else {
        link = "/api/transfers" + link;
    }

    let data = {
        company_id: $('.company-navbar').val(),
        gateway_id: window.gatewayCode,
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
            $("#available-in-period").html(`<span class="currency">R$ </span>0,00`);
        },
        success: (response) => {
            $("#table-transfers-body").html("");

            let balance_in_period = response.meta.balance_in_period;
            let parseValue = parseFloat(balance_in_period.replace(".", "").replace(",", "."));
            let availableInPeriod = $("#available-in-period");
            availableInPeriod.html(
                `<span ${parseValue < 0 ? ' style="color:red;"' : ""
                }><span class="currency">R$ </span>${balance_in_period}</span>`
            );
            if (parseValue < 0) {
                availableInPeriod
                    .html(`<span style="color:red;"><span class="currency">R$ </span>${balance_in_period}</span>`)
                    .parent()
                    .find(".grad-border")
                    .removeClass("green")
                    .addClass("red");
            } else if (parseValue > 0) {
                availableInPeriod
                    .html(`<span class="currency">R$ </span>${balance_in_period}`)
                    .parent()
                    .find(".grad-border")
                    .removeClass("red")
                    .addClass("green");
            } else {
                availableInPeriod
                    .html(`<span class="currency">R$ </span>0,00`)
                    .parent()
                    .find(".grad-border")
                    .removeClass("red")
                    .addClass("green");
            }

            if (response.data == "") {
                $("#pagination-container-transfers").removeClass("d-flex").addClass("d-none")

                $("#pagination-transfers").css({ "background": "#f4f4f4" })
                $("#table-transfers-body").html(`
                    <tr class='text-center bg-transparent'>
                        <td style='height: 300px; border-radius: 16px !important' colspan='11' >
                            <div class="d-flex justify-content-center align-items-center h-p100">
                                <div class="row m-0 row justify-content-center align-items-center h-p100 font-size-16">
                                        <img style='width:124px; margin-right:12px;' alt=""
                                        src='${$("#table-transfers-body").attr("img-empty")}'>
                                    Nenhum dado encontrado
                                </div>
                            </div>
                        </td>
                    </tr>
                `);
                $("#pagination-transfers").html("");
            } else {
                $("#pagination-container-transfers").removeClass("d-none").addClass("d-flex")
                data = "";

                $.each(response.data, function (index, value) {
                    data += '<tr class="s-table table-finance-schedule-new">';
                    let dateRequest = getRequestTime(value);
                    let dateRelease = getReleaseTime(value);

                    if (value.is_owner && value.sale_id) {
                        data += `<td style="grid-area: sale" class="sale-finance-schedule ">
                            <span class="d-block mb-10"> ${value.reason} </span>
                            <a class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" venda="${value.sale_id}">
                                <span class="transfers-sale">#${value.sale_id}</span>
                            </a>
                        </td>`;
                    } else {
                        if (value.reason === "Antecipação") {
                            data += `<td style="grid-area: sale" class="sale-finance-schedule">
                                        <span class="d-block mb-10"> ${value.reason} </span>
                                        <span class="transfers-sale"> #${value.anticipation_id} </span>
                                    </td>`;
                        } else {
                            data += `<td style="grid-area: sale" class="sale-finance-schedule">
                                        <span class="d-block mb-10"> ${value.reason} </span>
                                        ${value.sale_id ? `<span class="transfers-sale"> #${value.sale_id}</span>` : ""}
                                    </td>`;
                        }
                    }

                    data += `<td class="date-start-finance-transfers text-left" style="grid-area: date-start"> ${dateRequest} </td>
                             <td class="date-end-finance-transfers text-left" style="grid-area: date-end"> ${dateRelease} </td>`;

                    if (value.type_enum === 1) {
                        data += `<td class="value-finance-schedule" style="grid-area: value">
                                    <span class="font-md-size-20 bold" style="color:#41DC8F"> R$ </span>
                                    <strong class="font-md-size-20" style="color:#41DC8F"> ${value.value} </strong>`;
                        if (value.reason === "Antecipação") {
                            data += `<br><small style='color:#543333;'>(Taxa: ${value.tax})</small> </td>`;
                        } else {
                            data += `</td>`;
                        }
                    } else {
                        data += `<td class="value-finance-schedule" style="grid-area: value">
                                    <span class="font-md-size-20 bold" style="color:red"> R$ </span>
                                    <strong class="font-md-size-20" style="color:red"> ${value.value} </strong></td> `;
                    }
                    data += "</tr>";
                });
                $("#pagination-transfers").css({ "background": "#ffffff" })


                $("#table-transfers-body").html(data);

                paginationTransfersTable(response);
            }
        },
    });

    function paginationTransfersTable(response) {

        $("#pagination-transfers").html("");
        let primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";
        $("#pagination-transfers").append(primeira_pagina);

        if (response.meta.current_page == "1") {
            $("#primeira_pagina").attr("disabled", true);
            $("#primeira_pagina").addClass("nav-btn");
            $("#primeira_pagina").addClass("active");
        }

        $("#primeira_pagina").unbind("click");
        $("#primeira_pagina").on("click", function () {
            updateTransfersTable("?page=1");
        });

        for (x = 3; x > 0; x--) {
            if (response.meta.current_page - x <= 1) {
                continue;
            }
            $("#pagination-transfers").append(
                "<button id='pagina_" +
                (response.meta.current_page - x) +
                "' class='btn nav-btn'>" +
                (response.meta.current_page - x) +
                "</button>"
            );
            $("#pagina_" + (response.meta.current_page - x)).on("click", function () {
                updateTransfersTable("?page=" + $(this).html());
            });
        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            let pagina_atual =
                "<button id='pagina_atual' class='btn nav-btn active'>" + response.meta.current_page + "</button>";
            $("#pagination-transfers").append(pagina_atual);
            $("#pagina_atual").attr("disabled", true).addClass("nav-btn").addClass("active");
        }

        for (x = 1; x < 4; x++) {
            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }
            $("#pagination-transfers").append(
                "<button id='pagina_" +
                (response.meta.current_page + x) +
                "' class='btn nav-btn'>" +
                (response.meta.current_page + x) +
                "</button>"
            );
            $("#pagina_" + (response.meta.current_page + x)).on("click", function () {
                updateTransfersTable("?page=" + $(this).html());
            });
        }

        if (response.meta.last_page != "1") {
            let ultima_pagina =
                "<button id='ultima_pagina' class='btn nav-btn mr-0'>" + response.meta.last_page + "</button>";
            $("#pagination-transfers").append(ultima_pagina);
            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr("disabled", true);
                $("#ultima_pagina").addClass("nav-btn");
                $("#ultima_pagina").addClass("active");
            }
            $("#ultima_pagina").on("click", function () {
                updateTransfersTable("?page=" + response.meta.last_page);
            });
        }
        $("table").addClass("table-striped");
    }
};

window.updateAccountStatementData = function () {
    loadOnAnyEllipsis("#nav-statement #available-in-period-statement");

    $("#table-statement-body").html("");
    $("#pagination-statement").html("");
    loadOnTable("#table-statement-body", "#statementTable");

    let link =
        "/api/transfers?" +
        "company_id=" + $('.company-navbar').val() +
        "&gateway_id=" + window.gatewayCode +
        "&dateRange=" + $("#date_range_statement").val() +
        "&sale=" + encodeURIComponent($("#statement_sale").val()) +
        "&status=" + $("#statement_status_select").val() +
        "&statement_data_type=" + $("#statement_data_type_select").val() +
        "&payment_method=" + $("#payment_method").val() +
        "&withdrawal_id=" + $("#withdrawal_id").val();

    $(".numbers").hide();

    $.ajax({
        method: "GET",
        url: link,
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: () => {
            $("#available-in-period").html(`
                <span>
                   <small class="font-size-12">R$ </small> 0,00
                </span>`);

            loadOnAnyEllipsis("#nav-statement #available-in-period-statement", true);

            let error = "Erro ao gerar o extrato";
            $("#export-excel").addClass("d-none");
            $("#table-statement-body").html(`
            <tr class='text-center bg-transparent'>
                        <td style='height: 300px; border-radius: 16px !important' colspan='11' >
                            <div class="d-flex justify-content-center align-items-center h-p100">
                                <div class="row m-0 row justify-content-center align-items-center h-p100 font-size-16">
                                        <img style='width:124px; margin-right:12px;' alt=""
                                        src='${$("#table-transfers-body").attr("img-empty")}'>
                                    Erro ao gerar extrato
                                </div>
                            </div>
                        </td>
                    </tr>
            `);
            errorAjaxResponse(error);
        },
        success: (response) => {
            updateClassHTML();

            let items = response.items;

            if (isEmpty(items)) {
                loadOnAnyEllipsis("#nav-statement #available-in-period-statement", true);
                $("#pagination-transfers").css({ "background": "#f4f4f4" })
                $("#export-excel").addClass("d-none");
                $("#table-statement-body").html(`
                    <tr class='text-center bg-transparent'>
                        <td style='height: 300px; border-radius: 16px !important' colspan='11' >
                            <div class="d-flex justify-content-center align-items-center h-p100">
                                <div class="row m-0 row justify-content-center align-items-center h-p100 font-size-16">
                                    <img style='width:124px; margin-right:12px;' alt=""
                                         src='${$("#table-transfers-body").attr("img-empty")}'>
                                        Nenhum dado encontrado
                                </div>
                            </div>
                        </td>
                    </tr>
                `);
                return false;
            }

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

            items.forEach(function (item) {
                let data = {
                    date_request: item.transactionDate,
                    date_release: item.date,
                };

                let dateRequest = getRequestTime(data);
                let dateRelease = getReleaseTime(data);

                let dataTable = `<tr class="s-table table-finance-schedule">`;
                if (item.order && item.order.hashId && item.isInvite) {
                    dataTable += `
                        <td class="text-center sale-finance-schedule">
                            <a>
                                <span class="transfers-sale m-0 p-0 border-0" style="grid-area: sale;">#${item.order.hashId}</span>
                            </a>
                        </td>
                    `;
                } else if (item.order && item.order.hashId) {
                    dataTable += `
                        <td class="text-center sale-finance-schedule">
                             <a class="detalhes_venda disabled pointer-md" data-target="#modal_detalhes" data-toggle="modal" venda="${item.order.hashId}">
                                <span class="transfers-sale m-0 p-0 border-0" style="grid-area: sale;">#${item.order.hashId}</span>
                            </a>
                        </td>
                    `;
                } else {
                    dataTable += `
                        <td class="text-center sale-finance-schedule">
                            <span class="transfers-sale m-0 p-0 border-0" style="grid-area: sale; color: #5D5D5D">${item.details.description}</span>
                        </td>
                    `;
                }

                dataTable += `
                    <td class="date-start-finance-transfers text-left" style="grid-area: date-start">${dateRequest}</td>
                    <td class="date-end-finance-transfers text-left" style="grid-area: date-end">${dateRelease}</td>
                     <td style="grid-area: status;align-self: center;" class="text-center status-finance-schedule">
                        <span data-toggle="tooltip" data-placement="left" title="${item.details.status}"
                        class="badge badge-sm badge-${statusExtract[item.details.type]}">
                            ${item.details.status}
                        </span>
                     </td>`;

                if (item.amount > 0) {
                    dataTable += `
                        <td class="text-left value-finance-schedule" style="grid-area: value;">
                            <strong class="font-md-size-20" style="color:green">
                                ${item.amount
                            .toLocaleString("pt-BR", {
                                style: "currency",
                                currency: "BRL",
                            })
                            .replace(/\s+/g, "")
                            .replace("-", "- ")}
                            </strong>
                        </td>
                    </tr>`;
                } else {
                    dataTable += `
                        <td class="text-left value-finance-schedule" style="grid-area: value;">
                            <strong class="font-md-size-20" style="color:red">
                                ${item.amount
                            .toLocaleString("pt-BR", {
                                style: "currency",
                                currency: "BRL",
                            })
                            .replace(/\s+/g, "")
                            .replace("-", "- ")}
                            </strong>
                        </td>
                    </tr>`;
                }

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

            $("#statement-money #available-in-period-statement").html(`
                <span${isNegativeStatement ? ' style="color:red;"' : ""}>
                   <small class="font-size-12">R$ </small> ${totalInPeriod.toLocaleString("pt-BR")}
                </span>`);

            let availableInPeriod = $("#available-in-period");
            availableInPeriod.html(
                `<span ${isNegativeStatement ? ' style="color:red;"' : ""
                }><span class="currency">R$ </span>${totalInPeriod.toLocaleString("pt-BR")}</span>`
            );

            paginationStatement();

            $("#export-excel").removeClass("d-none");
            $("#pagination-statement span").addClass("jp-hidden");
            $("#pagination-statement a").removeClass("active").addClass("btn nav-btn");
            $("#pagination-statement a.jp-current").addClass("active");
            $("#pagination-statement a").on("click", function () {
                $("#pagination-statement a").removeClass("active");
                $(this).addClass("active");
            });

            $("#pagination-statement").on("click", function () {
                $("#pagination-statement span").remove();
            });

            loadOnAnyEllipsis("#nav-statement #statement-money  #available-in-period-statement", true);
        },
    });

    function updateClassHTML(dataTable = 0) {
        if (dataTable.length > 0) {
            $("#table-statement-body").append(dataTable);
            $("#statementTable").addClass("table-striped");
        } else {
            $("#table-statement-body").html("");
        }
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
};

$(window).on("load", function () {
    //atualiza a table de extrato
    $(document).on("click", "#bt_filtro, #bt_filtro_statement", function () {
        $("#pagination-container-transfers").removeClass("d-flex").addClass("d-none")
        $("#extract_company_select option[value=" + $("#extract_company_select option:selected").val() + "]").prop(
            "selected",
            true
        );

        $("#transferred_value").hide();
        loadStatementTable();
    });

    function getFilters(urlParams = false) {
        let data = {
            'company': $('.company-navbar').val(),
            'reason': $("#reason").val(),
            'transaction': $("#transaction").val().replace('#', ''),
            'type': $("#type").val(),
            'value': $("#transaction-value").val(),
            'date_range': $("#date_range").val(),
            'date_type': $("#date_type").val(),
            'email': $('#email_finance_export').val(),
            'format': exportFinanceFormat,
            'gateway_id': window.gatewayCode,
        };

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += "&" + param + "=" + data[param];
            }
            return encodeURI(params);
        } else {
            return data;
        }
    }

    function extractExport() {
        let data = getFilters();

        $.ajax({
            method: "POST",
            url: "/api/finances/export",
            data: data,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
                $("#bt_get_csv").prop("disabled", false);
                $("#bt_get_xls").prop("disabled", false);
            },
            success: (response) => {
                $("#export-email").text(response.email);
                $("#alert-export").show().shake();

                setTimeout(function () {
                    $("#bt_get_csv").prop("disabled", false);
                    $("#bt_get_xls").prop("disabled", false);
                }, 6000);
            },
        });
    }

    let exportFinanceFormat = "";
    $("#bt_get_xls").on("click", function () {
        $("#bt_get_csv").prop("disabled", true);
        $("#bt_get_xls").prop("disabled", true);
        $("#modal-export-old-finance-getnet").modal("show");
        exportFinanceFormat = "xls";
    });

    $("#bt_get_csv").on("click", function () {
        $("#bt_get_csv").prop("disabled", true);
        $("#bt_get_xls").prop("disabled", true);
        $("#modal-export-old-finance-getnet").modal("show");
        exportFinanceFormat = "csv";
    });

    $(".btn-confirm-export-old-finance-getnet").on("click", function () {
        var regexEmail = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
        var email = $("#email_finance_export").val();

        if (email == "" || !regexEmail.test(email)) {
            alertCustom("error", "Preencha o e-mail corretamente");
            return false;
        } else {
            extractExport();
            $("#modal-export-old-finance-getnet").modal("hide");
        }
    });

    $(".nav-link-finances-show-export").on("click", function () {
        $("#export-excel").removeClass("d-none");
    });

    $(".nav-link-finances-hide-export").on("click", function () {
        $("#export-excel").addClass("d-none");
    });

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            $("#extract_company_select option[value=" + $("#extract_company_select option:selected").val() + "]").prop(
                "selected",
                true
            );
            updateTransfersTable();
        }
    });

    $(".btn-light-1").on("click", function () {
        var collapse = $(this).find("#icon-filtro");
        var text = $(this).find("#text-filtro");

        text.fadeOut(10);
        if (collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" || collapse.css("transform") == "none") {
            collapse.css("transform", "rotate(180deg)");
            text.text("Minimizar filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }
    });
    //abaixo função para apagar numero zerado no botão de valor na aba extrato
    document.getElementById("transaction-value").addEventListener("focusout", inputOutOfFocus);
    function inputOutOfFocus() {
        if ($("#transaction-value").val() == "0,00") {
            document.getElementById("transaction-value").value = null;
        }
    }

    $("#custom-input-addon, .custom-input-addon-m").on("input change", (e) => {
        let value = e.target.value;
        $("#custom-input-addon").val(value);
        $(".custom-input-addon-m").val(value);
    });
});
