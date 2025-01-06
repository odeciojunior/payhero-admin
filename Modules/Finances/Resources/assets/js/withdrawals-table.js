window.loadWithdrawalsTable = function (link = null) {
    let statusWithdrawals = {
        1: "warning",
        2: "primary",
        3: "success",
        4: "danger",
        5: "primary",
        6: "primary",
        7: "warning",
        8: "primary",
        9: "partially-liquidating",
        10: "success",
    };

    $("#withdrawals-table-data").html("");
    loadOnTable("#withdrawals-table-data", "#transfersTable");
    $("#pagination-withdrawals").children().attr("disabled", "disabled");

    if (link == null) {
        link = "/api/withdrawals";
    } else {
        link = "/api/withdrawals" + link;
    }

    function getRequestTime(data = "") {
        let request_date = "";
        if (!isEmpty(data.date_request))
            request_date = `<div class="d-block d-md-none"> Solicitado em  </div>
                            <div class="bold-mobile"> ${data.date_request} </div>`;

        if (!isEmpty(data.date_request_time))
            request_date += `<span class="subdescription font-size-12"> às ${data.date_request_time.replace(
                ":",
                "h"
            )} </span>`;

        return request_date;
    }
    function getReleaseTime(data = "") {
        let release_date = "";
        if (!isEmpty(data.date_release))
            release_date = `<div class="d-block d-md-none"> Liberado em  </div>
                            <div class="bold-mobile"> ${data.date_release} </div>`;

        if (!isEmpty(data.date_release_time))
            release_date += `<span class="subdescription font-size-12"> às ${data.date_release_time.replace(
                ":",
                "h"
            )} </span>`;

        return release_date;
    }

    function removeFormatNumbers(number) {
        return number.replace(/,/g, "").replace(/\./g, "");
    }

    function formatMoney(value) {
        return (value / 100)
            .toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL",
            })
            .replace(/\s+/g, "")
            .replace("-", "- ");
    }
    $.ajax({
        method: "GET",
        url: link,
        data: {
            company_id: $(".company-navbar").val(),
            gateway_id: window.gatewayCode,
        },
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: (response) => {
            errorAjaxResponse(response);
        },
        success: (response) => {
            $("#withdrawals-table-data").html("");
            if (response.data === "" || response.data === undefined || response.data.length === 0) {
                $("#pagination-container").removeClass("d-flex").addClass("d-none");

                $("#pagination-withdrawals").css({ background: "#f4f4f4" });
                const emptyImage = $("#withdrawals-table-data").attr("img-empty");
                $("#withdrawals-table-data").html(
                    `<tr style='border-radius: 16px;'>
                            <td colspan='6' class='text-center' style='vertical-align: middle;height:300px;'>
                                <div class="row justify-content-center align-items-center h-p100 font-size-16">
                                    <img style="width: 124px;" src='${emptyImage}' alt="">Nenhum saque realizado até o momento
                                </div>
                            </td>
                        </tr>`
                );
                $("#withdrawals-pagination").html("");
            } else {
                $("#pagination-container").removeClass("d-none").addClass("d-flex");
                $.each(response.data, function (index, data) {
                    let tableData = "";
                    let dateRequest = getRequestTime(data);
                    let dateRelease = getReleaseTime(data);

                    tableData += `<tr class="s-table table-finance-transfers">;


                            <td class="sale-finance-transfers" style="grid-area: sale">

                                <div class="fullInformation-transfer">
                                    #${data.id}
                                </div>
                                <div class="container-tooltips-transfer"></div>

                            </td>

                            <td class="text-left truncate bank-finance-transfers" style="grid-area: bank">

                                <div class="fullInformation-transfer ellipsis-text" style="color: #636363;">
                                    ${data.account_information_bank}
                                </div>

                                <span class="subdescription font-size-12"> ${data.account_information} </span>
                            </td>;

                            <td class="text-left date-start-finance-transfers" style="grid-area: date-start">
                                ${dateRequest}
                            </td>;

                            <td class="text-left date-end-finance-transfers" style="grid-area: date-end">
                                ${dateRelease}
                            </td>;

                            <td class="shipping-status text-center status-finance-transfers" style="grid-area: status">
                                <span class="badge badge-${statusWithdrawals[data.status]} "> ${
                        data.status_translated
                    } </span>
                            </td>`;

                    if (data.tax_value > 0) {
                        tableData += `
                                        <td class="text-left value-finance-transfers" style="grid-area: value">

                                            <span class="font-md-size-20 bold"> R$ </span>

                                            <strong class="font-md-size-20"> ${data.value} </strong>

                                            <br>

                                            <small>(taxa de R$10,00)</small>
                                    `;
                    } else {
                        tableData += `
                                        <td colspan="2" class="text-left value-finance-transfers" style="grid-area: value">
                                            <span class="font-md-size-20 bold"> R$ </span>
                                            <strong class="font-md-size-20"> ${data.value}</strong>
                                    `;
                    }

                    if (
                        window.gatewayCode == "w7YL9jZD6gp4qmv" &&
                        data.debt_pending_value != null &&
                        data.debt_pending_value != "R$0,00"
                    ) {
                        tableData += `
                                        <br>

                                        <a role='button' class='pending_debit_withdrawal_id' withdrawal_id='${data.id}'>
                                            <small style="color: #ED1C24;">
                                                - ${data.debt_pending_value}
                                            </small>
                                        </a>
                                    `;
                    }
                    tableData += "</td>";

                    if (window.gatewayCode == "w7YL9jZD6gp4qmv") {
                        tableData += `
                                        </td>

                                        <td class="d-none d-lg-table-cell">
                                            <a role='button' class='details_transaction pointer' withdrawal='${data.id}'>
                                                <span class=''>
                                                    <img src='/build/global/img/icon-eye.svg'/>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>`;
                    }
                    tableData += "</tr>";

                    $("#withdrawals-table-data").append(tableData);
                    $("#withdrawalsTable").addClass("table-striped");

                    $(".fullInformation-transfer").bind("mouseover", function () {
                        var $this = $(this);

                        if (this.offsetWidth < this.scrollWidth && !$this.attr("title")) {
                            $this
                                .attr({
                                    "data-toggle": "tooltip",
                                    "data-placement": "top",
                                    "data-title": $this.text(),
                                })
                                .tooltip({ container: ".container-tooltips-transfer" });
                            $this.tooltip("show");
                        }
                    });
                });
                pagination(response, "withdrawals", loadWithdrawalsTable);
            }
        },
    });
};
