var has_api_integration = false;
$(function () {
    loadingOnScreen();
    exportReports();

    changeCompany();
    changeCalendar();

    if (sessionStorage.info) {
        let info = JSON.parse(sessionStorage.getItem("info"));
        $("input[name=daterange]").val(info.calendar);
    }

    getCompaniesAndProjects().done(function (data) {
        getProjects(data);
    });
});

let salesUrl = "/api/reports/sales";
let mktUrl = "/api/reports/marketing";

let company = "";
let date = "";
let sales_status = "";

$(".company-navbar").change(function () {
    if (verifyIfCompanyIsDefault($(this).val())) return;

    loadingOnScreen();

    $("#select_projects").val($("#select_projects option:first").val());
    $(
        "#revenue-generated, #qtd-aproved, #qtd-boletos, #qtd-recusadas, #qtd-chargeback, #qtd-dispute, #qtd-reembolso, #qtd-pending, #qtd-canceled, #percent-credit-card, #percent-values-boleto,#credit-card-value,#boleto-value, #percent-boleto-convert#percent-credit-card-convert, #percent-desktop, #percent-mobile, #qtd-cartao-convert, #qtd-boleto-convert, #ticket-medio"
    ).html("<span>" + "<span class='loaderSpan' >" + "</span>" + "</span>");

    $("#select_projects").html("");
    sessionStorage.removeItem("info");

    updateCompanyDefault().done(function (data1) {
        getCompaniesAndProjects().done(function (data2) {
            getProjects(data2, "company-navbar");
        });
    });
});

window.fillProjectsSelect = function () {
    return $.ajax({
        method: "GET",
        url: "/api/projects?select=true",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
        },
        success: function success(response) {
            return response;
        },
    });
};

function changeSaleStatus() {
    $("#status-graph").on("change", function () {
        if (sales_status !== $(this).val()) {
            sales_status = $(this).val();

            $(".sirius-select-container").addClass("disabled");
            $('input[name="daterange"]').attr("disabled", "disabled");

            Promise.all([salesStatus($(this).find("option:selected").val())])
                .then(() => {
                    $(".sirius-select-container").removeClass("disabled");
                    $('input[name="daterange"]').removeAttr("disabled", "disabled");
                })
                .catch(() => {
                    $(".sirius-select-container").removeClass("disabled");
                    $('input[name="daterange"]').removeAttr("disabled", "disabled");
                });
        }
    });
}

function getProjects(data, origin = "") {
    loadingOnScreen();
    $.ajax({
        method: "GET",
        url: "/api/sales/projects-with-sales",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            loadingOnScreenRemove();
        },
        success: function success(response) {
            if (!isEmpty(response) || data.has_api_integration) {
                $(".div-filters").hide();
                $("#project-empty").hide();
                $("#project-not-empty").show();
                $("#export-excel > div >").show();
                $.each(response, function (c, project) {
                    $("#select_projects").append($("<option>", { value: project.project_id, text: project.name }));
                });
                if (data.has_api_integration)
                    $("#select_projects").append($("<option>", { value: "API-TOKEN", text: "Vendas por API" }));
                $("#select_projects option:first").attr("selected", "selected");
                if (sessionStorage.info) {
                    $("#select_projects").val(JSON.parse(sessionStorage.getItem("info")).company);
                    $("#select_projects")
                        .find("option:selected")
                        .text(JSON.parse(sessionStorage.getItem("info")).companyName);
                }
                company = $("#select_projects").val();
                updateReports();
                changeSaleStatus();
                $(".div-filters").show();
                loadingOnScreenRemove();
            } else {
                if (!isEmpty(data.company_default_projects)) {
                    $(".div-filters").hide();
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel > div >").show();
                    // $.each(data.company_default_projects, function (i, project) {
                    //     $("#select_projects").append($("<option>", {value: project.project_id,text: project.name,}));
                    // });
                    if (data.has_api_integration)
                        $("#select_projects").append($("<option>", { value: "API-TOKEN", text: "Vendas por API" }));
                    $("#select_projects option:first").attr("selected", "selected");
                    if ($("#select_projects option").length == 0) $("#select_projects").next().css("display", "none");
                    updateReports();
                    changeSaleStatus();
                    $(".div-filters").show();
                    loadingOnScreenRemove();
                } else {
                    loadingOnScreenRemove();
                    $(".div-filters").hide();
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }
            }
        },
    });
    loadingOnScreenRemove();
}

function barGraph(data, labels, total) {
    const titleTooltip = (tooltipItems) => {
        return "";
    };

    const legendMargin = {
        id: "legendMargin",
        beforeInit(chart, legend, options) {
            const fitValue = chart.legend.fit;
            chart.legend.fit = function () {
                fitValue.bind(chart.legend)();
                return (this.height += 20);
            };
        },
    };

    const ctx = document.getElementById("salesChart").getContext("2d");

    const myChart = new Chart(ctx, {
        plugins: [legendMargin],
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    axis: "x",
                    label: "",
                    data,
                    color: "#E8EAEB",
                    backgroundColor: ["rgba(46, 133, 236, 1)"],
                    borderRadius: 4,
                    barThickness: 24,
                    fill: false,
                },
            ],
        },
        options: {
            indexAxis: "x",
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                },
                subtitle: {
                    display: true,
                    align: "start",
                    text: `${total} cliente(s) recorrente(s)`,
                    color: "#E8EAEB",
                    font: {
                        size: "14",
                        family: "'Inter'",
                        weight: "normal",
                    },
                    padding: {
                        top: 0,
                        bottom: 15,
                    },
                },
            },

            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        padding: 0,
                        color: "#747474",
                        align: "center",
                        font: {
                            family: "Inter",
                            size: 12,
                        },
                    },
                },
                y: {
                    grid: {
                        color: "#ECE9F1",
                        drawBorder: true,
                    },
                    min: 0,
                    max: 40,
                    ticks: {
                        padding: 0,
                        stepSize: 10,
                        font: {
                            family: "Inter",
                            size: 14,
                        },
                        color: "#A2A3A5",
                    },
                },
            },
            interaction: {
                mode: "index",
                borderRadius: 4,
                usePointStyle: true,
                yAlign: "bottom",
                padding: 15,
                titleSpacing: 10,
                callbacks: {
                    title: titleTooltip,
                    label: function (tooltipItem) {
                        return tooltipItem.raw + " recorrência(s)";
                    },
                    labelPointStyle: function (context) {
                        return {
                            pointStyle: "rect",
                            borderRadius: 4,
                            rotatio: 0,
                        };
                    },
                },
            },
        },
    });
}

function salesResume() {
    let salesTransactions = `
        <span class="title">N de transações</span>
        <div class="d-flex">
            <strong class="number">0</strong>
        </div>
    `;

    let salesAverageTicket = `
        <span class="title">Ticket Médio</span>
        <div class="d-flex">
            <span class="detail">R$</span>
            <strong class="number">0,00</strong>
        </div>
    `;

    let salesComission = `
        <span class="title">Comissão total</span>
        <div class="d-flex">
            <span class="detail">R$</span>
            <strong class="number">0,00</strong>
        </div>
    `;

    let salesNumberChargeback = `
        <span class="title">Total em Chargebacks</span>
        <div class="d-flex">
            <span class="detail">R$</span>
            <strong class="number">0,00</strong>
        </div>
    `;

    $("#reports-content .onPreLoad *").remove();
    $("#sales-transactions,#sales-average-ticket,#sales-comission,#sales-number-chargeback").html(skeLoad);

    $.ajax({
        method: "GET",
        url:
            salesUrl +
            "/resume?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#sales-number-chargeback").html(salesNumberChargeback);
            $("#sales-comission").html(salesComission);
            $("#sales-average-ticket").html(salesAverageTicket);
            $("#sales-transactions").html(salesTransactions);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data != undefined) {
                let { average_ticket, chargeback, comission, transactions } = response.data;

                salesTransactions = `
                    <span class="title">N de transações</span>
                    <div class="d-flex">
                        <strong class="number">${transactions == undefined ? 0 : transactions}</strong>
                    </div>
                `;

                salesAverageTicket = `
                    <span class="title">Ticket Médio</span>
                    <div class="d-flex">
                        <span class="detail">R$</span>
                        <strong class="number">${
                            average_ticket == undefined ? "0,00" : removeMoneyCurrency(average_ticket)
                        }</strong>
                    </div>
                `;

                salesComission = `
                    <span class="title">Comissão total</span>
                    <div class="d-flex">
                        <span class="detail">R$</span>
                        <strong class="number">${
                            comission == undefined ? "0,00" : removeMoneyCurrency(comission)
                        }</strong>
                    </div>
                `;

                salesNumberChargeback = `
                    <span class="title">Total em Chargebacks</span>
                    <div class="d-flex">
                        <span class="detail">R$</span>
                        <strong class="number">${
                            chargeback == undefined ? "0,00" : removeMoneyCurrency(chargeback)
                        }</strong>
                    </div>
                `;
            }

            $("#sales-number-chargeback").html(salesNumberChargeback);
            $("#sales-comission").html(salesComission);
            $("#sales-average-ticket").html(salesAverageTicket);
            $("#sales-transactions").html(salesTransactions);
        },
    });
}

function distribution() {
    let distributionHtml = `
        <div class="d-flex box-graph-dist no-distribution-graph">
            <div class="info-graph">
                <div class="no-sell">
                    ${noGraph}
                </div>
            </div>
        </div>
    `;
    $("#card-distribution .onPreLoadBig *").remove();
    $("#block-distribution").html(skeLoadBig);

    return $.ajax({
        method: "GET",
        url:
            salesUrl +
            "/distribuitions?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-distribution").html(distributionHtml);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { approved, canceled, chargeback, other, pending, refunded, refused, total } = response.data;
            if (total !== "0") {
                let series = [
                    approved.percentage,
                    pending.percentage,
                    canceled.percentage,
                    refused.percentage,
                    refunded.percentage,
                    chargeback.percentage,
                    other.percentage,
                ];

                distributionHtml = `
                <div class="d-flex box-graph-dist">
                    <div class="info-graph">
                        <h6 class="font-size-14 grey">Quantidade total de vendas</h6>
                        <em>
                            <strong class="grey">${total}</strong>
                        </em>
                    </div>
                </div>
                <div class="d-flex box-distribution secondary" style="display: ${
                    approved.percentage == "0.00" ? "none" : "flex"
                }">
                    <div class="distribution-area">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#1BE4A8" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Aprovadas</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${approved.amount}</strong>
                        </div>
                        <div class="item right"><small class="grey font-size-14">${approved.percentage}%</small></div>
                    </div>

                    <div class="distribution-area" style="display: ${pending.percentage == "0.00" ? "none" : "flex"}">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FFBA06" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Pendentes</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${pending.amount}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${pending.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area" style="display: ${canceled.percentage == "0.00" ? "none" : "flex"}">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#665FE8" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Canceladas</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${canceled.amount}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${canceled.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area" style="display: ${refused.percentage == "0.00" ? "none" : "flex"}">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FF2F2F" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Recusadas</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${refused.amount}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${refused.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area" style="display: ${refunded.percentage == "0.00" ? "none" : "flex"}">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#00C2FF" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Reembolsos</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${refunded.amount}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${refunded.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area" style="display: ${
                        chargeback.percentage == "0.00" ? "none" : "flex"
                    }">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#D10000" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Chargebacks</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${chargeback.amount}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${chargeback.percentage}%</small>
                        </div>
                    </div>

                    <div class="distribution-area" style="display: ${other.percentage == "0.00" ? "none" : "flex"}">
                        <div class="item">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#767676" stroke-width="3"/>
                                </svg>
                            </span>
                            <small class="font-size-14">Outros</small>
                        </div>
                        <div class="item right">
                            <strong class="grey font-size-14">${other.amount}</strong>
                        </div>
                        <div class="item right">
                            <small class="grey font-size-14">${other.percentage}%</small>
                        </div>
                    </div>
                </div>
                `;

                $("#block-distribution").html(distributionHtml);
                $(".box-graph-dist").prepend('<div class="distribution-graph-seller"></div>');
                distributionGraph(series);
            } else {
                $("#block-distribution").html(distributionHtml);
            }
        },
    });
}

function distributionGraph(series) {
    new Chartist.Pie(
        ".distribution-graph-seller",
        {
            series,
        },
        {
            donut: true,
            donutWidth: 30,
            donutSolid: true,
            startAngle: 270,
            showLabel: false,
            chartPadding: 0,
            labelOffset: 0,
            height: 123,
        }
    );
}

function loadDevices() {
    let deviceBlock = `
        <div class="container d-flex value-price" style="visibility: hidden; height: 10px;">
            <h4 id='products' class="font-size-24 bold grey">
                0
            </h4>
        </div>
        <div class="empty-products pad-0">
            ${noData}
            <p class="noone">Sem dados</p>
        </div>
    `;
    $("#card-devices .onPreLoad *").remove();
    $("#block-devices").html(skeLoad);

    return $.ajax({
        method: "GET",
        url:
            mktUrl +
            "/devices?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-devices").html(deviceBlock);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data !== null) {
                let { desktop, mobile } = response.data;

                if (desktop.total !== 0 || mobile.total !== 0) {
                    deviceBlock = `
                     <div class="row container-payment gadgets">
                        <div class="container container-devices">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment">
                                        <div class="box-ico">
                                            <span class="ico-cart align-items justify-around">
                                                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#636363"/></svg>
                                            </span>
                                        </div>Smartphones
                                    </div>

                                    <div class="box-payment-option option">
                                        <div class="col-payment">
                                            <div class="box-payment center">
                                                <span>${mobile.approved}</span>
                                                /<small>${mobile.total}</small>
                                            </div>
                                        </div>
                                        <div class="col-payment">
                                            <div class="box-payment right">
                                                <strong class="grey font-size-16">${mobile.percentage_approved}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container container-devices">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment">
                                        <div class="box-ico">
                                            <span class="ico-cart align-items justify-around">
                                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 2.83333C0 1.26853 1.26853 0 2.83333 0H14.1667C15.7315 0 17 1.26853 17 2.83333V11.3333C17 12.8981 15.7315 14.1667 14.1667 14.1667H11.3333V14.875C11.3333 15.2662 11.6505 15.5833 12.0417 15.5833H12.75C13.1412 15.5833 13.4583 15.9005 13.4583 16.2917C13.4583 16.6829 13.1412 17 12.75 17H4.25C3.8588 17 3.54167 16.6829 3.54167 16.2917C3.54167 15.9005 3.8588 15.5833 4.25 15.5833H4.95833C5.34953 15.5833 5.66667 15.2662 5.66667 14.875V14.1667H2.83333C1.26853 14.1667 0 12.8981 0 11.3333V2.83333ZM10.0376 15.5833C9.95928 15.3618 9.91667 15.1234 9.91667 14.875V14.1667H7.08333V14.875C7.08333 15.1234 7.04072 15.3618 6.96242 15.5833H10.0376ZM14.1667 12.75C14.9491 12.75 15.5833 12.1157 15.5833 11.3333H1.41667C1.41667 12.1157 2.05093 12.75 2.83333 12.75H14.1667ZM15.5833 2.83333C15.5833 2.05093 14.9491 1.41667 14.1667 1.41667H2.83333C2.05093 1.41667 1.41667 2.05093 1.41667 2.83333V9.91667H15.5833V2.83333Z" fill="#636363"/></svg>
                                            </span>
                                        </div> Desktop
                                    </div>
                                    <div class="box-payment-option option">
                                        <div class="col-payment">
                                            <div class="box-payment center">
                                                <span>${desktop.approved}</span>
                                                /<small>${desktop.total}</small>
                                            </div>
                                        </div>
                                        <div class="col-payment">
                                            <div class="box-payment right">
                                                <strong class="grey font-size-16">${desktop.percentage_approved}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                    `;
                    $("#block-devices").html(deviceBlock);
                } else {
                    $("#block-devices").html(deviceBlock);
                }
            } else {
                $("#block-devices").html(deviceBlock);
            }
        },
    });
}

function typePayments() {
    let paymentsHtml = `
        <div class="container d-flex value-price" style="visibility: hidden; height: 10px;">
            <h4 id='products' class="font-size-24 bold grey">
                0
            </h4>
        </div>
        <div class="empty-products pad-0">
            ${noData}
            <p class="noone">Sem dados</p>
        </div>
    `;

    $("#card-payments .onPreLoad *").remove();
    $("#block-payments").html(skeLoad);

    let card = `
        <span class="ico-cart align-items justify-around">
            <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"></path>
            </svg>
        </span>
    `;

    let cardPix = `
        <span class="ico-cart align-items justify-around">
            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.0917 14.6992L11.2386 17.4934C11.2386 17.4935 11.2386 17.4935 11.2385 17.4935C10.7895 17.9331 10.1784 18.1819 9.539 18.1819C8.89962 18.1819 8.28848 17.9331 7.83946 17.4935C7.83944 17.4935 7.83942 17.4935 7.8394 17.4934L5.03555 14.7473C5.25932 14.7066 5.47877 14.643 5.69026 14.5573C6.09053 14.3951 6.45468 14.1565 6.76142 13.8548C6.76156 13.8547 6.7617 13.8546 6.76184 13.8544L9.62672 11.0486C9.63431 11.0423 9.64564 11.0376 9.65905 11.0376C9.67247 11.0376 9.6838 11.0423 9.69138 11.0486L12.5458 13.8441C12.5459 13.8442 12.5461 13.8443 12.5462 13.8445C12.8529 14.1462 13.217 14.3848 13.6173 14.5471C13.7717 14.6097 13.9303 14.6605 14.0917 14.6992ZM4.42939 14.8013V14.3013L4.42881 14.8013H4.42939Z" stroke="#636363"/>
                <path d="M7.83943 1.1885L7.83943 1.1885C8.06167 0.970876 8.32607 0.797705 8.61781 0.679373C8.90956 0.561035 9.22261 0.5 9.539 0.5C9.85539 0.5 10.1684 0.561035 10.4602 0.679373C10.7519 0.797705 11.0163 0.970876 11.2386 1.1885L14.0915 3.98232C13.9301 4.02103 13.7716 4.0718 13.6173 4.13437C13.2169 4.29669 12.8527 4.53537 12.546 4.83712C12.5459 4.83722 12.5458 4.83733 12.5457 4.83743L9.68545 7.63858C9.68543 7.6386 9.6854 7.63863 9.68538 7.63865C9.6801 7.64377 9.6708 7.64844 9.659 7.64844C9.64719 7.64844 9.6379 7.64377 9.63261 7.63865C9.63259 7.63863 9.63256 7.6386 9.63254 7.63858L6.7618 4.82716C6.76163 4.827 6.76147 4.82684 6.7613 4.82667C6.45464 4.525 6.09053 4.28638 5.6903 4.12408C5.47883 4.03833 5.25941 3.97475 5.03566 3.934L7.83943 1.1885ZM4.42939 3.87995H4.42874C4.42852 3.87995 4.4283 3.87995 4.42808 3.87995L4.42939 4.37995V3.87995Z" stroke="#636363"/>
                <mask id="path-3-inside-1_389_560" fill="white">
                <path d="M18.229 7.33393L16.0327 5.18328C15.9832 5.20325 15.9303 5.21373 15.8767 5.21414H14.8782C14.3585 5.2156 13.8603 5.41771 13.4918 5.77661L10.6303 8.57858C10.5029 8.70356 10.3515 8.80265 10.1848 8.87018C10.0182 8.93771 9.83957 8.97233 9.65922 8.97206C9.47888 8.97235 9.30027 8.93776 9.13361 8.8703C8.96695 8.80283 8.81554 8.70381 8.68806 8.57892L5.81589 5.76644C5.4474 5.40754 4.94923 5.20543 4.4295 5.20397H3.20149C3.15091 5.2031 3.10092 5.19322 3.05396 5.1748L0.848899 7.33393C0.579765 7.59746 0.366275 7.91033 0.220621 8.25466C0.0749664 8.59898 0 8.96803 0 9.34073C0 9.71343 0.0749664 10.0825 0.220621 10.4268C0.366275 10.7711 0.579765 11.084 0.848899 11.3475L3.05373 13.5065C3.10073 13.4881 3.15076 13.4782 3.20137 13.4774H4.42927C4.949 13.4759 5.44717 13.2738 5.81566 12.9149L8.68771 10.1033C8.94936 9.85873 9.29719 9.72225 9.65893 9.72225C10.0207 9.72225 10.3685 9.85873 10.6302 10.1033L13.4917 12.9051C13.8601 13.264 14.3583 13.4662 14.8781 13.4675H15.8766C15.9302 13.4679 15.9831 13.4784 16.0327 13.4983L18.229 11.3475C18.4981 11.084 18.7116 10.7711 18.8572 10.4268C19.0029 10.0825 19.0779 9.71343 19.0779 9.34073C19.0779 8.96803 19.0029 8.59898 18.8572 8.25466C18.7116 7.91033 18.4981 7.59746 18.229 7.33393Z"/>
                </mask>
                <path d="M16.0327 5.18328L16.7323 4.46879L16.265 4.01119L15.6585 4.25593L16.0327 5.18328ZM15.8767 5.21414V6.21417L15.8845 6.21411L15.8767 5.21414ZM14.8782 5.21414V4.21414L14.8754 4.21415L14.8782 5.21414ZM13.4918 5.77661L12.7941 5.06025L12.7922 5.06211L13.4918 5.77661ZM10.6303 8.57858L9.93064 7.86407L9.93003 7.86467L10.6303 8.57858ZM9.65922 8.97206L9.66071 7.97206L9.65764 7.97206L9.65922 8.97206ZM8.68806 8.57892L9.38787 7.86459L9.3877 7.86443L8.68806 8.57892ZM5.81589 5.76644L6.51553 5.05195L6.51361 5.05007L5.81589 5.76644ZM4.4295 5.20397L4.43231 4.20397H4.4295V5.20397ZM3.20149 5.20397L3.18428 6.20382L3.19288 6.20397H3.20149V5.20397ZM3.05396 5.1748L3.41896 4.2438L2.81662 4.00765L2.35434 4.46029L3.05396 5.1748ZM0.848899 7.33393L0.149274 6.61942L0.149262 6.61943L0.848899 7.33393ZM0 9.34073H-1H0ZM0.848899 11.3475L1.54854 10.633L1.54854 10.633L0.848899 11.3475ZM3.05373 13.5065L2.35409 14.221L2.81626 14.6736L3.41853 14.4376L3.05373 13.5065ZM3.20137 13.4774V12.4774H3.19281L3.18425 12.4775L3.20137 13.4774ZM4.42927 13.4774V14.4774L4.43208 14.4774L4.42927 13.4774ZM5.81566 12.9149L6.51338 13.6313L6.5152 13.6295L5.81566 12.9149ZM8.68771 10.1033L8.00479 9.37284L7.99639 9.3807L7.98817 9.38875L8.68771 10.1033ZM10.6302 10.1033L11.3298 9.3888L11.3215 9.38072L11.3131 9.37284L10.6302 10.1033ZM13.4917 12.9051L12.7921 13.6196L12.7939 13.6213L13.4917 12.9051ZM14.8781 13.4675L14.8754 14.4675H14.8781V13.4675ZM15.8766 13.4675L15.8836 12.4675H15.8766V13.4675ZM16.0327 13.4983L15.6594 14.426L16.2655 14.6699L16.7323 14.2128L16.0327 13.4983ZM18.229 11.3475L17.5293 10.633L17.5293 10.6331L18.229 11.3475ZM18.9286 6.61943L16.7323 4.46879L15.333 5.89777L17.5293 8.04842L18.9286 6.61943ZM15.6585 4.25593C15.7259 4.22875 15.7973 4.21473 15.869 4.21417L15.8845 6.21411C16.0632 6.21273 16.2405 6.17776 16.4069 6.11063L15.6585 4.25593ZM15.8767 4.21414H14.8782V6.21414H15.8767V4.21414ZM14.8754 4.21415C14.099 4.21633 13.3508 4.51807 12.7941 5.06025L14.1895 6.49298C14.3699 6.31736 14.6179 6.21488 14.881 6.21414L14.8754 4.21415ZM12.7922 5.06211L9.93064 7.86407L11.3299 9.29309L14.1914 6.49112L12.7922 5.06211ZM9.93003 7.86467C9.8966 7.89746 9.85575 7.92455 9.80928 7.94338L10.5604 9.79698C10.8472 9.68076 11.1091 9.50966 11.3305 9.29249L9.93003 7.86467ZM9.80928 7.94338C9.76278 7.96222 9.71222 7.97214 9.66071 7.97206L9.65773 9.97206C9.96691 9.97252 10.2736 9.91319 10.5604 9.79698L9.80928 7.94338ZM9.65764 7.97206C9.60605 7.97214 9.55542 7.96222 9.50885 7.94337L8.75837 9.79723C9.04511 9.9133 9.35172 9.97255 9.6608 9.97206L9.65764 7.97206ZM9.50885 7.94337C9.4623 7.92453 9.42137 7.89741 9.38787 7.86459L7.98824 9.29325C8.20971 9.51021 8.47161 9.68114 8.75837 9.79723L9.50885 7.94337ZM9.3877 7.86443L6.51553 5.05195L5.11624 6.48093L7.98841 9.29341L9.3877 7.86443ZM6.51361 5.05007C5.95695 4.5079 5.2087 4.20615 4.43231 4.20397L4.42669 6.20396C4.68976 6.2047 4.93785 6.30719 5.11816 6.48281L6.51361 5.05007ZM4.4295 4.20397H3.20149V6.20397H4.4295V4.20397ZM3.2187 4.20412C3.28688 4.20529 3.35469 4.2186 3.41896 4.2438L2.68896 6.10581C2.84715 6.16783 3.01495 6.20091 3.18428 6.20382L3.2187 4.20412ZM2.35434 4.46029L0.149274 6.61942L1.54852 8.04844L3.75359 5.88932L2.35434 4.46029ZM0.149262 6.61943C-0.213698 6.97484 -0.502759 7.39792 -0.700368 7.86507L1.14161 8.64424C1.23531 8.42274 1.37323 8.22009 1.54854 8.04843L0.149262 6.61943ZM-0.700368 7.86507C-0.897998 8.33227 -1 8.83376 -1 9.34073H1C1 9.1023 1.04793 8.8657 1.14161 8.64424L-0.700368 7.86507ZM-1 9.34073C-1 9.8477 -0.897998 10.3492 -0.700368 10.8164L1.14161 10.0372C1.04793 9.81576 1 9.57916 1 9.34073H-1ZM-0.700368 10.8164C-0.502759 11.2835 -0.213698 11.7066 0.149262 12.062L1.54854 10.633C1.37323 10.4614 1.23531 10.2587 1.14161 10.0372L-0.700368 10.8164ZM0.149255 12.062L2.35409 14.221L3.75337 12.7921L1.54854 10.633L0.149255 12.062ZM3.41853 14.4376C3.35434 14.4628 3.2866 14.4761 3.2185 14.4772L3.18425 12.4775C3.01492 12.4804 2.84712 12.5135 2.68893 12.5755L3.41853 14.4376ZM3.20137 14.4774H4.42927V12.4774H3.20137V14.4774ZM4.43208 14.4774C5.20847 14.4752 5.95671 14.1735 6.51338 13.6313L5.11793 12.1985C4.93762 12.3742 4.68953 12.4766 4.42646 12.4774L4.43208 14.4774ZM6.5152 13.6295L9.38725 10.8179L7.98817 9.38875L5.11611 12.2003L6.5152 13.6295ZM9.37062 10.8338C9.44499 10.7643 9.54796 10.7222 9.65893 10.7222V8.72225C9.04642 8.72225 8.45372 8.95316 8.00479 9.37284L9.37062 10.8338ZM9.65893 10.7222C9.76991 10.7222 9.87287 10.7643 9.94724 10.8338L11.3131 9.37284C10.8641 8.95316 10.2714 8.72225 9.65893 8.72225V10.7222ZM9.93055 10.8179L12.7921 13.6196L14.1913 12.1905L11.3298 9.3888L9.93055 10.8179ZM12.7939 13.6213C13.3505 14.1637 14.0989 14.4655 14.8754 14.4675L14.8807 12.4676C14.6178 12.4669 14.3698 12.3644 14.1895 12.1888L12.7939 13.6213ZM14.8781 14.4675H15.8766V12.4675H14.8781V14.4675ZM15.8696 14.4675C15.798 14.467 15.7267 14.4531 15.6594 14.426L16.406 12.5706C16.2396 12.5036 16.0623 12.4688 15.8836 12.4676L15.8696 14.4675ZM16.7323 14.2128L18.9286 12.062L17.5293 10.6331L15.333 12.7838L16.7323 14.2128ZM18.9286 12.062C19.2916 11.7066 19.5806 11.2835 19.7782 10.8164L17.9363 10.0372C17.8426 10.2587 17.7046 10.4614 17.5293 10.633L18.9286 12.062ZM19.7782 10.8164C19.9759 10.3492 20.0779 9.8477 20.0779 9.34073H18.0779C18.0779 9.57916 18.0299 9.81576 17.9363 10.0372L19.7782 10.8164ZM20.0779 9.34073C20.0779 8.83376 19.9759 8.33227 19.7782 7.86507L17.9363 8.64424C18.0299 8.8657 18.0779 9.1023 18.0779 9.34073H20.0779ZM19.7782 7.86507C19.5806 7.39792 19.2916 6.97484 18.9286 6.61943L17.5293 8.04843C17.7046 8.22009 17.8426 8.42274 17.9363 8.64424L19.7782 7.86507Z" fill="#636363" mask="url(#path-3-inside-1_389_560)"/>
            </svg>
        </span>
    `;

    let cardBoleto = `
        <span class="ico-cart align-items justify-around">
            <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_386_407)">
                    <rect x="-161.098" y="-1313.01" width="646" height="1962" rx="12" fill="white"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4016 2.27981H2.40165C2.07013 2.27981 1.75218 2.41555 1.51776 2.65717C1.28333 2.89878 1.15163 3.22648 1.15163 3.56817V13.875C1.15172 14.2167 1.28346 14.5443 1.51787 14.7858C1.75229 15.0274 2.07019 15.1631 2.40165 15.1631H17.4019C17.7334 15.1631 18.0514 15.0273 18.2858 14.7857C18.5202 14.5441 18.6519 14.2164 18.6519 13.8747V3.56817C18.6519 3.39895 18.6196 3.23139 18.5567 3.07506C18.4939 2.91873 18.4018 2.77668 18.2857 2.65704C18.1696 2.5374 18.0317 2.44251 17.88 2.37779C17.7283 2.31306 17.5658 2.27977 17.4016 2.27981ZM2.40165 0.991455C1.7386 0.991455 1.10271 1.26293 0.633857 1.74616C0.165008 2.22939 -0.0983887 2.88479 -0.0983887 3.56817L-0.0983887 13.875C-0.0983887 14.5584 0.165008 15.2138 0.633857 15.6971C1.10271 16.1803 1.7386 16.4518 2.40165 16.4518H17.4019C17.7302 16.4518 18.0553 16.3851 18.3586 16.2556C18.6619 16.1261 18.9376 15.9363 19.1697 15.6971C19.4019 15.4578 19.586 15.1737 19.7116 14.8611C19.8373 14.5485 19.9019 14.2134 19.9019 13.875V3.56817C19.9019 3.22979 19.8373 2.89473 19.7116 2.58211C19.586 2.26948 19.4019 1.98543 19.1697 1.74616C18.9376 1.50689 18.6619 1.31709 18.3586 1.1876C18.0553 1.0581 17.7302 0.991455 17.4019 0.991455H2.40165Z" fill="#636363"/>
                    <path d="M4.34595 4.99976H6.27182V12.9399H4.34595V4.99976ZM7.23492 4.99976H8.19803V12.9399H7.23492V4.99976ZM14.9387 4.99976H15.9018V12.9399H14.9387V4.99976ZM11.087 4.99976H13.977V12.9399H11.087V4.99976ZM9.16113 4.99976H10.1242V12.9399H9.16113V4.99976Z" fill="#636363"/>
                </g>
                <defs>
                    <clipPath id="clip0_386_407">
                        <rect width="20.082" height="15.46" fill="white" transform="translate(0 0.991486)"/>
                    </clipPath>
                </defs>
            </svg>
        </span>
    `;

    return $.ajax({
        method: "GET",
        url:
            "/api/reports/resume/type-payments?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-payments").html(paymentsHtml);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data !== null) {
                var arrJson = Object.keys(response.data).map((key) => [key, response.data[key]]);

                paymentsHtml = `<div class="row container-payment" id="type-payment">`;
                arrJson.forEach((element) => {
                    paymentsHtml += `
                            <div
                                class="container ${
                                    element[0] == "credit_card"
                                        ? "creditCard"
                                        : element[0] == "pix"
                                        ? "cardPix"
                                        : element[0] == "boleto"
                                        ? "cardBoleto"
                                        : ""
                                }"
                            >
                                <div class="data-holder b-bottom">
                                    <div class="box-payment-option pad-0">
                                        <div class="col-payment grey box-image-payment ico-pay">
                                            <div class="box-ico">
                                                ${
                                                    element[0] == "credit_card"
                                                        ? card
                                                        : element[0] == "pix"
                                                        ? cardPix
                                                        : element[0] == "boleto"
                                                        ? cardBoleto
                                                        : ""
                                                }
                                            </div>${element[0] == "credit_card" ? "Cartão" : element[0]}
                                        </div>

                                        <div class="box-payment-option option">
                                            <div
                                                class="col-payment grey percentage-card"
                                                id='percent-credit-card'>
                                                ${element[1].percentage}
                                            </div>
                                            <div class="col-payment col-graph bar-payment">
                                                <div class="bar" style="width: ${element[1].percentage};">-</div>
                                            </div>
                                            <div class="col-payment end">
                                                <span class="money-td green bold grey" id='credit-card-value'>
                                                    R$ ${element[1].value}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                });
                paymentsHtml += `</div>`;
            }

            $("#block-payments").html(paymentsHtml);
        },
    });
}

function loadFrequenteSales() {
    let salesBlock = "";
    $("#card-most-sales .onPreLoad *").remove();
    $("#block-sales").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url:
            mktUrl +
            "/most-frequent-sales?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            salesBlock = `${noSales}`;
            $("#block-sales .ske-load").remove();
            $("#block-sales").removeClass("scroll-212");
            $("#block-sales").html(salesBlock);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data !== null) {
                $.each(response.data, function (i, item) {
                    let value = removeMoneyCurrency(item.value);
                    let newV = formatCash(String(parseFloat(value)).replace(".", ""));
                    salesBlock = `
                        <div class="box-payment-option pad-0">
                            <div class="d-flex align-items list-sales">
                                <div class="d-flex align-items">
                                    <div>
                                        <figure
                                            class="box-ico figure-ico"
                                            data-container="body"
                                            data-viewport=".container"
                                            data-placement="top"
                                            data-toggle="tooltip"
                                            title="${item.name} - ${item.description}"
                                        >
                                            <img class="photo" width="34px" height="34px" src="${item.photo}" alt="${item.description}">
                                        </figure>
                                    </div>
                                    <div>
                                        <span class="desc-product">${item.name}</span>
                                    </div>
                                </div>
                                <div class="grey font-size-14">${item.sales_amount}</div>
                                <div class="grey font-size-14 value"><strong>R$ ${newV}</strong></div>
                            </div>
                        </div>
                    `;
                    $("#block-sales .ske-load").remove();
                    $("#block-sales").addClass("scroll-212");
                    $("#block-sales").append(salesBlock);
                    $(".photo").on("error", function () {
                        $(this).attr("src", "https://azcend-digital-products.s3.amazonaws.com/admin/produto.svg");
                    });
                    $('[data-toggle="tooltip"]').tooltip({
                        container: "#block-sales",
                    });
                });

                if (response.data.length < 4) {
                    $("#card-most-sales .scrollbar, .scroll-212").height("auto");
                    salesBlock = `<div>${noListProducts}</div>`;
                    $("#block-sales").append(salesBlock);
                }
            } else {
                salesBlock = `${noSales}`;
                $("#block-sales .ske-load").remove();
                $("#block-sales").removeClass("scroll-212");
                $("#block-sales").html(salesBlock);
            }
        },
    });
}

function abandonedCarts() {
    let abandonedBlock = `
        <div style="position: relative;">
            ${noCart}
            <p class="noone">Sem dados</p>
        </div>
    `;
    $("#card-abandoned .onPreLoad *").remove();
    $("#block-abandoned").html(skeLoad);

    return $.ajax({
        method: "GET",
        url:
            salesUrl +
            "/abandoned-carts?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-abandoned").html(abandonedBlock);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data.percentage !== "0%") {
                let { percentage, value } = response.data;

                abandonedBlock = `
                    <div class="row container-payment height-auto">
                        <div class="container">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment">
                                        <div class="box-ico">
                                            <span class="ico-cart align-items justify-around">
                                                <svg width="18" height="18" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.11982 0.436309C0.64062 0.432235 0.255174 0.841681 0.251702 1.34176C0.248229 1.84184 0.64062 2.24314 1.11982 2.24722C1.7605 2.25265 2.27269 2.69985 2.66595 3.5488C3.03404 4.34243 3.14167 4.99554 3.1547 5.07676L4.18517 12.0091C4.50116 13.66 6.00126 14.9236 7.57691 14.9236H13.7614C15.3371 14.9236 16.8441 13.6483 17.1532 12.0374L18.2114 4.68062C18.4571 3.40049 17.5508 2.24722 16.2581 2.24722H3.96814C3.31531 1.12545 2.32044 0.446451 1.11982 0.436309ZM4.72774 4.05812H16.2581C16.4552 4.05812 16.5403 4.16705 16.5021 4.36942L15.4438 11.7262C15.3041 12.4479 14.5097 13.1127 13.7614 13.1127H7.57691C6.82945 13.1127 6.04121 12.4624 5.8945 11.6979L4.89094 4.7938C4.86056 4.60121 4.79025 4.29598 4.72774 4.05812ZM6.76262 15.829C6.04381 15.829 5.46043 16.4371 5.46043 17.1872C5.46043 17.9373 6.04381 18.5454 6.76262 18.5454C7.48142 18.5454 8.0648 17.9373 8.0648 17.1872C8.0648 16.4371 7.48142 15.829 6.76262 15.829ZM14.5757 15.829C13.8569 15.829 13.2735 16.4371 13.2735 17.1872C13.2735 17.9373 13.856 18.5454 14.5757 18.5454C15.2945 18.5454 15.8779 17.9373 15.8779 17.1872C15.8779 16.4371 15.2954 15.829 14.5757 15.829Z" fill="#636363"/></svg>
                                            </span>
                                        </div>Recuperados
                                    </div>

                                    <div class="box-payment-option option">
                                        <div class="col-payment">
                                            <div class="box-payment center">
                                                <span>${percentage}</span>
                                            </div>
                                        </div>
                                        <div class="col-payment">
                                            <div class="box-payment right">
                                                <strong class="grey font-size-16">${value}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            $("#block-abandoned").html(abandonedBlock);
        },
    });
}

function orderbump() {
    let orderbumpBlock = `
        <div class="d-flex align-items">
            <div class="balance col-4">
                <div class="box-ico-cash">
                    <span class="ico-cash">
                        <svg width="55" height="55" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.4968 19.0015L36.0525 19.0029L36.1734 19.0168L36.2611 19.0364L36.365 19.0708L36.4541 19.1112L36.5179 19.1468L36.5805 19.1883L36.6445 19.2382L36.7076 19.2965L36.802 19.4062L36.8736 19.5174L36.9271 19.6302L36.9624 19.7355L36.9781 19.8007L36.9873 19.853L36.9983 20.0015V27.5054C36.9983 28.0576 36.5506 28.5054 35.9983 28.5054C35.4854 28.5054 35.0628 28.1193 35.005 27.622L34.9983 27.5054L34.998 22.4155L20.7061 36.7071C20.3456 37.0676 19.7784 37.0953 19.3861 36.7903L19.2919 36.7071C18.9314 36.3466 18.9037 35.7794 19.2087 35.3871L19.2919 35.2929L33.583 21.0015H28.4968C27.9839 21.0015 27.5612 20.6154 27.5035 20.1181L27.4968 20.0015C27.4968 19.4492 27.9445 19.0015 28.4968 19.0015Z" fill="#E8EAEB"/>
                            <circle cx="27.5" cy="27.5" r="26.5" stroke="#E8EAEB" stroke-width="2"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="balance col-8">
                <h6 class="no-orderbump">Sem vendas por orderbump</h6>
                <p class="txt-no-orderbump">Ofereça mais um produto no checkout e aumente sua conversão</p>
            </div>
        </div>
    `;
    $("#card-orderbump .onPreLoad *").remove();
    $("#block-orderbump").prepend(skeLoad);

    $("#card-orderbump header").addClass("mt-0");

    return $.ajax({
        method: "GET",
        url:
            salesUrl +
            "/orderbump?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-orderbump").html(orderbumpBlock);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data.amount > 0) {
                let { amount, value } = response.data;
                value = removeMoneyCurrency(value);

                orderbumpBlock = `
                   <div class="d-flex align-items">
                       <div class="balance col-6">
                           <h6 class="grey font-size-14">
                               <span class="ico-coin">
                                   <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                       <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                   </svg>
                               </span>
                               Ganhos
                           </h6>
                           <small>R$</small>
                           <strong class="total grey">${value}</strong>
                       </div>
                       <div class="balance col-6">
                           <h6 class="grey font-size-14 qtd">Conversões</h6>
                           <strong class="total grey">${amount} vendas</strong>
                       </div>
                   </div>
                `;
            }

            $("#block-orderbump").html(orderbumpBlock);
            $("#card-orderbump header").removeClass("mt-0");
        },
    });
}

function upsell() {
    let upsellBlock = "";
    $("#card-upsell .onPreLoad *").remove();
    $("#block-upsell").prepend(skeLoad);
    $("#card-upsell header").addClass("mt-0");

    return $.ajax({
        method: "GET",
        url:
            salesUrl +
            "/upsell?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            upsellBlock = `${noUpsell}`;
            $("#block-upsell").html(upsellBlock);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data.amount > 0) {
                let { value, amount } = response.data;
                value = removeMoneyCurrency(value);

                upsellBlock = `
                    <div class="d-flex align-items">
                        <div class="balance col-6">
                            <h6 class="grey font-size-14">
                                <span class="ico-coin">
                                    <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                    </svg>
                                </span>
                                Ganhos
                            </h6>
                            <small>R$</small>
                            <strong class="total grey">${value}</strong>
                        </div>
                        <div class="balance col-6">
                            <h6 class="grey font-size-14 qtd">Conversões</h6>
                            <strong class="total grey">${amount} vendas</strong>
                        </div>
                    </div>
                `;
            } else {
                upsellBlock = `${noUpsell}`;
            }
            $("#block-upsell").html(upsellBlock);
            $("#card-upsell header").removeClass("mt-0");
        },
    });
}

function conversion() {
    let conversionBlock = `
        <div class="container d-flex value-price" style="visibility: hidden; height: 10px;">
            <h4 id='products' class="font-size-24 bold grey">
                0
            </h4>
        </div>
        <div class="empty-products pad-0">
            ${noData}
            <p class="noone">Sem dados</p>
        </div>
    `;
    $("#card-conversion .onPreLoad *").remove();
    $("#block-conversion").prepend(skeLoad);

    let card = `
        <span class="ico-cart align-items justify-around">
            <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"></path>
            </svg>
        </span>
        `;

    let card_pix = `
    <span class="ico-cart align-items justify-around">
        <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.0917 14.6992L11.2386 17.4934C11.2386 17.4935 11.2386 17.4935 11.2385 17.4935C10.7895 17.9331 10.1784 18.1819 9.539 18.1819C8.89962 18.1819 8.28848 17.9331 7.83946 17.4935C7.83944 17.4935 7.83942 17.4935 7.8394 17.4934L5.03555 14.7473C5.25932 14.7066 5.47877 14.643 5.69026 14.5573C6.09053 14.3951 6.45468 14.1565 6.76142 13.8548C6.76156 13.8547 6.7617 13.8546 6.76184 13.8544L9.62672 11.0486C9.63431 11.0423 9.64564 11.0376 9.65905 11.0376C9.67247 11.0376 9.6838 11.0423 9.69138 11.0486L12.5458 13.8441C12.5459 13.8442 12.5461 13.8443 12.5462 13.8445C12.8529 14.1462 13.217 14.3848 13.6173 14.5471C13.7717 14.6097 13.9303 14.6605 14.0917 14.6992ZM4.42939 14.8013V14.3013L4.42881 14.8013H4.42939Z" stroke="#636363"/>
            <path d="M7.83943 1.1885L7.83943 1.1885C8.06167 0.970876 8.32607 0.797705 8.61781 0.679373C8.90956 0.561035 9.22261 0.5 9.539 0.5C9.85539 0.5 10.1684 0.561035 10.4602 0.679373C10.7519 0.797705 11.0163 0.970876 11.2386 1.1885L14.0915 3.98232C13.9301 4.02103 13.7716 4.0718 13.6173 4.13437C13.2169 4.29669 12.8527 4.53537 12.546 4.83712C12.5459 4.83722 12.5458 4.83733 12.5457 4.83743L9.68545 7.63858C9.68543 7.6386 9.6854 7.63863 9.68538 7.63865C9.6801 7.64377 9.6708 7.64844 9.659 7.64844C9.64719 7.64844 9.6379 7.64377 9.63261 7.63865C9.63259 7.63863 9.63256 7.6386 9.63254 7.63858L6.7618 4.82716C6.76163 4.827 6.76147 4.82684 6.7613 4.82667C6.45464 4.525 6.09053 4.28638 5.6903 4.12408C5.47883 4.03833 5.25941 3.97475 5.03566 3.934L7.83943 1.1885ZM4.42939 3.87995H4.42874C4.42852 3.87995 4.4283 3.87995 4.42808 3.87995L4.42939 4.37995V3.87995Z" stroke="#636363"/>
            <mask id="path-3-inside-1_389_560" fill="white">
            <path d="M18.229 7.33393L16.0327 5.18328C15.9832 5.20325 15.9303 5.21373 15.8767 5.21414H14.8782C14.3585 5.2156 13.8603 5.41771 13.4918 5.77661L10.6303 8.57858C10.5029 8.70356 10.3515 8.80265 10.1848 8.87018C10.0182 8.93771 9.83957 8.97233 9.65922 8.97206C9.47888 8.97235 9.30027 8.93776 9.13361 8.8703C8.96695 8.80283 8.81554 8.70381 8.68806 8.57892L5.81589 5.76644C5.4474 5.40754 4.94923 5.20543 4.4295 5.20397H3.20149C3.15091 5.2031 3.10092 5.19322 3.05396 5.1748L0.848899 7.33393C0.579765 7.59746 0.366275 7.91033 0.220621 8.25466C0.0749664 8.59898 0 8.96803 0 9.34073C0 9.71343 0.0749664 10.0825 0.220621 10.4268C0.366275 10.7711 0.579765 11.084 0.848899 11.3475L3.05373 13.5065C3.10073 13.4881 3.15076 13.4782 3.20137 13.4774H4.42927C4.949 13.4759 5.44717 13.2738 5.81566 12.9149L8.68771 10.1033C8.94936 9.85873 9.29719 9.72225 9.65893 9.72225C10.0207 9.72225 10.3685 9.85873 10.6302 10.1033L13.4917 12.9051C13.8601 13.264 14.3583 13.4662 14.8781 13.4675H15.8766C15.9302 13.4679 15.9831 13.4784 16.0327 13.4983L18.229 11.3475C18.4981 11.084 18.7116 10.7711 18.8572 10.4268C19.0029 10.0825 19.0779 9.71343 19.0779 9.34073C19.0779 8.96803 19.0029 8.59898 18.8572 8.25466C18.7116 7.91033 18.4981 7.59746 18.229 7.33393Z"/>
            </mask>
            <path d="M16.0327 5.18328L16.7323 4.46879L16.265 4.01119L15.6585 4.25593L16.0327 5.18328ZM15.8767 5.21414V6.21417L15.8845 6.21411L15.8767 5.21414ZM14.8782 5.21414V4.21414L14.8754 4.21415L14.8782 5.21414ZM13.4918 5.77661L12.7941 5.06025L12.7922 5.06211L13.4918 5.77661ZM10.6303 8.57858L9.93064 7.86407L9.93003 7.86467L10.6303 8.57858ZM9.65922 8.97206L9.66071 7.97206L9.65764 7.97206L9.65922 8.97206ZM8.68806 8.57892L9.38787 7.86459L9.3877 7.86443L8.68806 8.57892ZM5.81589 5.76644L6.51553 5.05195L6.51361 5.05007L5.81589 5.76644ZM4.4295 5.20397L4.43231 4.20397H4.4295V5.20397ZM3.20149 5.20397L3.18428 6.20382L3.19288 6.20397H3.20149V5.20397ZM3.05396 5.1748L3.41896 4.2438L2.81662 4.00765L2.35434 4.46029L3.05396 5.1748ZM0.848899 7.33393L0.149274 6.61942L0.149262 6.61943L0.848899 7.33393ZM0 9.34073H-1H0ZM0.848899 11.3475L1.54854 10.633L1.54854 10.633L0.848899 11.3475ZM3.05373 13.5065L2.35409 14.221L2.81626 14.6736L3.41853 14.4376L3.05373 13.5065ZM3.20137 13.4774V12.4774H3.19281L3.18425 12.4775L3.20137 13.4774ZM4.42927 13.4774V14.4774L4.43208 14.4774L4.42927 13.4774ZM5.81566 12.9149L6.51338 13.6313L6.5152 13.6295L5.81566 12.9149ZM8.68771 10.1033L8.00479 9.37284L7.99639 9.3807L7.98817 9.38875L8.68771 10.1033ZM10.6302 10.1033L11.3298 9.3888L11.3215 9.38072L11.3131 9.37284L10.6302 10.1033ZM13.4917 12.9051L12.7921 13.6196L12.7939 13.6213L13.4917 12.9051ZM14.8781 13.4675L14.8754 14.4675H14.8781V13.4675ZM15.8766 13.4675L15.8836 12.4675H15.8766V13.4675ZM16.0327 13.4983L15.6594 14.426L16.2655 14.6699L16.7323 14.2128L16.0327 13.4983ZM18.229 11.3475L17.5293 10.633L17.5293 10.6331L18.229 11.3475ZM18.9286 6.61943L16.7323 4.46879L15.333 5.89777L17.5293 8.04842L18.9286 6.61943ZM15.6585 4.25593C15.7259 4.22875 15.7973 4.21473 15.869 4.21417L15.8845 6.21411C16.0632 6.21273 16.2405 6.17776 16.4069 6.11063L15.6585 4.25593ZM15.8767 4.21414H14.8782V6.21414H15.8767V4.21414ZM14.8754 4.21415C14.099 4.21633 13.3508 4.51807 12.7941 5.06025L14.1895 6.49298C14.3699 6.31736 14.6179 6.21488 14.881 6.21414L14.8754 4.21415ZM12.7922 5.06211L9.93064 7.86407L11.3299 9.29309L14.1914 6.49112L12.7922 5.06211ZM9.93003 7.86467C9.8966 7.89746 9.85575 7.92455 9.80928 7.94338L10.5604 9.79698C10.8472 9.68076 11.1091 9.50966 11.3305 9.29249L9.93003 7.86467ZM9.80928 7.94338C9.76278 7.96222 9.71222 7.97214 9.66071 7.97206L9.65773 9.97206C9.96691 9.97252 10.2736 9.91319 10.5604 9.79698L9.80928 7.94338ZM9.65764 7.97206C9.60605 7.97214 9.55542 7.96222 9.50885 7.94337L8.75837 9.79723C9.04511 9.9133 9.35172 9.97255 9.6608 9.97206L9.65764 7.97206ZM9.50885 7.94337C9.4623 7.92453 9.42137 7.89741 9.38787 7.86459L7.98824 9.29325C8.20971 9.51021 8.47161 9.68114 8.75837 9.79723L9.50885 7.94337ZM9.3877 7.86443L6.51553 5.05195L5.11624 6.48093L7.98841 9.29341L9.3877 7.86443ZM6.51361 5.05007C5.95695 4.5079 5.2087 4.20615 4.43231 4.20397L4.42669 6.20396C4.68976 6.2047 4.93785 6.30719 5.11816 6.48281L6.51361 5.05007ZM4.4295 4.20397H3.20149V6.20397H4.4295V4.20397ZM3.2187 4.20412C3.28688 4.20529 3.35469 4.2186 3.41896 4.2438L2.68896 6.10581C2.84715 6.16783 3.01495 6.20091 3.18428 6.20382L3.2187 4.20412ZM2.35434 4.46029L0.149274 6.61942L1.54852 8.04844L3.75359 5.88932L2.35434 4.46029ZM0.149262 6.61943C-0.213698 6.97484 -0.502759 7.39792 -0.700368 7.86507L1.14161 8.64424C1.23531 8.42274 1.37323 8.22009 1.54854 8.04843L0.149262 6.61943ZM-0.700368 7.86507C-0.897998 8.33227 -1 8.83376 -1 9.34073H1C1 9.1023 1.04793 8.8657 1.14161 8.64424L-0.700368 7.86507ZM-1 9.34073C-1 9.8477 -0.897998 10.3492 -0.700368 10.8164L1.14161 10.0372C1.04793 9.81576 1 9.57916 1 9.34073H-1ZM-0.700368 10.8164C-0.502759 11.2835 -0.213698 11.7066 0.149262 12.062L1.54854 10.633C1.37323 10.4614 1.23531 10.2587 1.14161 10.0372L-0.700368 10.8164ZM0.149255 12.062L2.35409 14.221L3.75337 12.7921L1.54854 10.633L0.149255 12.062ZM3.41853 14.4376C3.35434 14.4628 3.2866 14.4761 3.2185 14.4772L3.18425 12.4775C3.01492 12.4804 2.84712 12.5135 2.68893 12.5755L3.41853 14.4376ZM3.20137 14.4774H4.42927V12.4774H3.20137V14.4774ZM4.43208 14.4774C5.20847 14.4752 5.95671 14.1735 6.51338 13.6313L5.11793 12.1985C4.93762 12.3742 4.68953 12.4766 4.42646 12.4774L4.43208 14.4774ZM6.5152 13.6295L9.38725 10.8179L7.98817 9.38875L5.11611 12.2003L6.5152 13.6295ZM9.37062 10.8338C9.44499 10.7643 9.54796 10.7222 9.65893 10.7222V8.72225C9.04642 8.72225 8.45372 8.95316 8.00479 9.37284L9.37062 10.8338ZM9.65893 10.7222C9.76991 10.7222 9.87287 10.7643 9.94724 10.8338L11.3131 9.37284C10.8641 8.95316 10.2714 8.72225 9.65893 8.72225V10.7222ZM9.93055 10.8179L12.7921 13.6196L14.1913 12.1905L11.3298 9.3888L9.93055 10.8179ZM12.7939 13.6213C13.3505 14.1637 14.0989 14.4655 14.8754 14.4675L14.8807 12.4676C14.6178 12.4669 14.3698 12.3644 14.1895 12.1888L12.7939 13.6213ZM14.8781 14.4675H15.8766V12.4675H14.8781V14.4675ZM15.8696 14.4675C15.798 14.467 15.7267 14.4531 15.6594 14.426L16.406 12.5706C16.2396 12.5036 16.0623 12.4688 15.8836 12.4676L15.8696 14.4675ZM16.7323 14.2128L18.9286 12.062L17.5293 10.6331L15.333 12.7838L16.7323 14.2128ZM18.9286 12.062C19.2916 11.7066 19.5806 11.2835 19.7782 10.8164L17.9363 10.0372C17.8426 10.2587 17.7046 10.4614 17.5293 10.633L18.9286 12.062ZM19.7782 10.8164C19.9759 10.3492 20.0779 9.8477 20.0779 9.34073H18.0779C18.0779 9.57916 18.0299 9.81576 17.9363 10.0372L19.7782 10.8164ZM20.0779 9.34073C20.0779 8.83376 19.9759 8.33227 19.7782 7.86507L17.9363 8.64424C18.0299 8.8657 18.0779 9.1023 18.0779 9.34073H20.0779ZM19.7782 7.86507C19.5806 7.39792 19.2916 6.97484 18.9286 6.61943L17.5293 8.04843C17.7046 8.22009 17.8426 8.42274 17.9363 8.64424L19.7782 7.86507Z" fill="#636363" mask="url(#path-3-inside-1_389_560)"/>
        </svg>
    </span>
    `;

    let card_boleto = `
    <span class="ico-cart align-items justify-around">
        <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_386_407)">
                <rect x="-161.098" y="-1313.01" width="646" height="1962" rx="12" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4016 2.27981H2.40165C2.07013 2.27981 1.75218 2.41555 1.51776 2.65717C1.28333 2.89878 1.15163 3.22648 1.15163 3.56817V13.875C1.15172 14.2167 1.28346 14.5443 1.51787 14.7858C1.75229 15.0274 2.07019 15.1631 2.40165 15.1631H17.4019C17.7334 15.1631 18.0514 15.0273 18.2858 14.7857C18.5202 14.5441 18.6519 14.2164 18.6519 13.8747V3.56817C18.6519 3.39895 18.6196 3.23139 18.5567 3.07506C18.4939 2.91873 18.4018 2.77668 18.2857 2.65704C18.1696 2.5374 18.0317 2.44251 17.88 2.37779C17.7283 2.31306 17.5658 2.27977 17.4016 2.27981ZM2.40165 0.991455C1.7386 0.991455 1.10271 1.26293 0.633857 1.74616C0.165008 2.22939 -0.0983887 2.88479 -0.0983887 3.56817L-0.0983887 13.875C-0.0983887 14.5584 0.165008 15.2138 0.633857 15.6971C1.10271 16.1803 1.7386 16.4518 2.40165 16.4518H17.4019C17.7302 16.4518 18.0553 16.3851 18.3586 16.2556C18.6619 16.1261 18.9376 15.9363 19.1697 15.6971C19.4019 15.4578 19.586 15.1737 19.7116 14.8611C19.8373 14.5485 19.9019 14.2134 19.9019 13.875V3.56817C19.9019 3.22979 19.8373 2.89473 19.7116 2.58211C19.586 2.26948 19.4019 1.98543 19.1697 1.74616C18.9376 1.50689 18.6619 1.31709 18.3586 1.1876C18.0553 1.0581 17.7302 0.991455 17.4019 0.991455H2.40165Z" fill="#636363"/>
                <path d="M4.34595 4.99976H6.27182V12.9399H4.34595V4.99976ZM7.23492 4.99976H8.19803V12.9399H7.23492V4.99976ZM14.9387 4.99976H15.9018V12.9399H14.9387V4.99976ZM11.087 4.99976H13.977V12.9399H11.087V4.99976ZM9.16113 4.99976H10.1242V12.9399H9.16113V4.99976Z" fill="#636363"/>
            </g>
            <defs>
                <clipPath id="clip0_386_407">
                    <rect width="20.082" height="15.46" fill="white" transform="translate(0 0.991486)"/>
                </clipPath>
            </defs>
        </svg>
    </span>
    `;

    return $.ajax({
        method: "GET",
        url:
            salesUrl +
            "/conversion?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-conversion").html(conversionBlock);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.data !== null) {
                let { credit_card, pix, boleto } = response.data;
                const numbers = [credit_card.total, pix.total, boleto.total]
                    .map(Number)
                    .reduce((prev, value) => prev + value, 0);

                var SortArr = function (j) {
                    var arr = [];
                    for (var key in j) {
                        arr.push({ key: key, val: j[key] });
                    }
                    arr.sort(function (a, b) {
                        var intA = parseInt(a.val.percentage),
                            intB = parseInt(b.val.percentage);
                        if (intA > intB) return -1;
                        if (intA < intB) return 1;
                        return 0;
                    });
                    return arr;
                };

                var arrJson = SortArr(response.data);

                conversionBlock = `
                    <div class="row container-payment block-conversion">
                        <div class="container">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment">
                                        <div class="box-ico">
                                            ${
                                                arrJson[0].key == "credit_card"
                                                    ? card
                                                    : arrJson[0].key == "pix"
                                                    ? card_pix
                                                    : arrJson[0].key == "boleto"
                                                    ? card_boleto
                                                    : ""
                                            }
                                        </div>${arrJson[0].key == "credit_card" ? "Cartão" : arrJson[0].key}
                                    </div>

                                    <div class="box-payment-option option">
                                        <div class="col-payment">
                                            <div class="box-payment center">
                                                <span>${arrJson[0].val.approved}</span>
                                                /<small>${arrJson[0].val.total}</small>
                                            </div>
                                        </div>
                                        <div class="col-payment">
                                            <div class="box-payment right">
                                                <strong class="grey font-size-16">${arrJson[0].val.percentage}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment" style="text-transform: capitalize;">
                                        <div class="box-ico">
                                            ${
                                                arrJson[1].key == "credit_card"
                                                    ? card
                                                    : arrJson[1].key == "pix"
                                                    ? card_pix
                                                    : arrJson[1].key == "boleto"
                                                    ? card_boleto
                                                    : ""
                                            }
                                        </div> ${arrJson[1].key == "credit_card" ? "Cartão" : arrJson[1].key}
                                    </div>
                                    <div class="box-payment-option option">
                                        <div class="col-payment">
                                            <div class="box-payment center">
                                                <span>${arrJson[1].val.approved}</span>
                                                /<small>${arrJson[1].val.total}</small>
                                            </div>
                                        </div>
                                        <div class="col-payment">
                                            <div class="box-payment right">
                                                <strong class="grey font-size-16">${arrJson[1].val.percentage}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment" style="text-transform: capitalize;">
                                        <div class="box-ico">
                                        ${
                                            arrJson[2].key == "credit_card"
                                                ? card
                                                : arrJson[2].key == "pix"
                                                ? card_pix
                                                : arrJson[2].key == "boleto"
                                                ? card_boleto
                                                : ""
                                        }
                                        </div> ${arrJson[2].key == "credit_card" ? "Cartão" : arrJson[2].key}
                                    </div>
                                    <div class="box-payment-option option">
                                        <div class="col-payment">
                                            <div class="box-payment center">
                                                <span>${arrJson[2].val.approved}</span>
                                                /<small>${arrJson[2].val.total}</small>
                                            </div>
                                        </div>
                                        <div class="col-payment">
                                            <div class="box-payment right">
                                                <strong class="grey font-size-16">${arrJson[2].val.percentage}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            $("#block-conversion").html(conversionBlock);
        },
    });
}

function infoCard() {
    let cardHtml = "";
    $("#card-info .onPreLoad *").remove();
    $("#card-info").show();
    $("#block-info-card").html(skeLoad);

    Promise.all([typePayments(), conversion()])
        .then((result) => {
            if (result[0].data !== null) {
                let { credit_card } = result[0].data;
                let conversionCard = result[1].data;

                if (conversionCard.credit_card.total == "0") {
                    $("#card-info").hide();
                } else {
                    $("#card-info").show();
                }

                cardHtml = `
                <div style="padding: 0 24px;" class="d-flex align-items">
                    <div>
                        <span class="ico-coin seller">
                            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.7647 0.164368C15.6137 0.164368 14.6814 1.09666 14.6814 2.2477V14.7477C14.6814 15.8987 15.6137 16.831 16.7647 16.831H18.848C19.9991 16.831 20.9314 15.8987 20.9314 14.7477V2.2477C20.9314 1.09666 19.9991 0.164368 18.848 0.164368H16.7647ZM16.7647 2.2477H18.848V14.7477H16.7647V2.2477ZM9.47303 4.33103C8.32199 4.33103 7.38969 5.26333 7.38969 6.41437V14.7477C7.38969 15.8987 8.32199 16.831 9.47303 16.831H11.5564C12.7074 16.831 13.6397 15.8987 13.6397 14.7477V6.41437C13.6397 5.26333 12.7074 4.33103 11.5564 4.33103H9.47303ZM9.47303 6.41437H11.5564V14.7477H9.47303V6.41437ZM2.18136 8.4977C1.03031 8.4977 0.0980225 9.42999 0.0980225 10.581V14.7477C0.0980225 15.8987 1.03031 16.831 2.18136 16.831H4.26469C5.41573 16.831 6.34803 15.8987 6.34803 14.7477V10.581C6.34803 9.42999 5.41573 8.4977 4.26469 8.4977H2.18136ZM2.18136 10.581H4.26469V14.7477H2.18136V10.581Z" fill="#2E85EC"/>
                            </svg>
                        </span>
                    </div>
                    <div>
                        <span class="font-size-12">
                        Cartão representa <strong class="card-represent">${credit_card.percentage}</strong> das vendas
                        aprovadas e tem um índice de conversão de <strong class="conversion-card">${conversionCard.credit_card.percentage}</strong>
                        </span>
                    </div>
                </div>
            `;
                $("#block-info-card").html(cardHtml);
            } else {
                $("#card-info").hide();
            }
        })
        .catch((e) => function() {

        });
}

function recurrence() {
    let recurrenceHtml = "";
    $("#card-recurrence .onPreLoad *").remove();
    $("#block-recurrence").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: salesUrl + "/recurrence?project_id=" + $("#select_projects option:selected").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            recurrenceHtml = `${noRecurrence}`;
            $("#block-recurrence").html(recurrenceHtml);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { chart, total } = response.data;

            if (chart) {
                $("#block-recurrence").html('<canvas id="salesChart"></canvas>');
                let labels = [...chart.labels];
                let series = [...chart.values];
                barGraph(series, labels, total);
            } else {
                recurrenceHtml = `${noRecurrence}`;
                $("#block-recurrence").html(recurrenceHtml);
            }
        },
    });
}

const formatCash = (n) => {
    if (n < 1e3) return n;
    if (n >= 1e3 && n < 1e6) return +(n / 1e3).toFixed(1) + "K";
    if (n >= 1e6 && n < 1e9) return +(n / 1e6).toFixed(1) + "M";
    if (n >= 1e9 && n < 1e12) return +(n / 1e9).toFixed(1) + "B";
    if (n >= 1e12) return +(n / 1e12).toFixed(1) + "T";
};

function changeCalendar() {
    $(".onPreLoad *, .onPreLoadBig *").remove();

    var startDate = moment().subtract(30, "days").format("DD/MM/YYYY");
    var endDate = moment().format("DD/MM/YYYY");

    data = sessionStorage.getItem("info")
        ? JSON.parse(sessionStorage.getItem("info")).calendar
        : `${startDate}-${endDate}`;

    $('input[name="daterange"]').attr("value", `${startDate}-${endDate}`);
    $('input[name="daterange"]')
        .dateRangePicker({
            setValue: function (s) {
                if (s) {
                    let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                    $(this).html(s).data("value", normalize);
                    $('input[name="daterange"]').attr("value", normalize);
                    $('input[name="daterange"]').val(normalize);
                } else {
                    $('input[name="daterange"]').attr("value", `${startDate}-${endDate}`);
                    $('input[name="daterange"]').val(`${startDate}-${endDate}`);
                }
            },
        })
        .on("datepicker-change", function () {
            $.ajaxQ.abortAll();

            if (data !== $(this).val()) {
                data = $(this).val();

                updateStorage({ calendar: $(this).val() });
                updateReports();
            }
        })
        .on("datepicker-open", function () {
            $(".filter-badge-input").removeClass("show");
        })
        .on("datepicker-close", function () {
            $(this).removeClass("focused");
            if ($(this).data("value")) {
                $(this).addClass("active");
            }
        });
}

function changeCompany() {
    $("#select_projects").on("change", function () {
        $.ajaxQ.abortAll();

        if (company !== $(this).val()) {
            company = $(this).val();

            updateStorage({ company: $(this).val(), companyName: $(this).find("option:selected").text() });
            updateReports();
        }
    });
}

function updateReports() {
    $(".sirius-select-container").addClass("disabled");
    $('input[name="daterange"]').attr("disabled", "disabled");

    Promise.all([
        salesResume(),
        distribution(),
        loadDevices(),
        loadFrequenteSales(),
        abandonedCarts(),
        orderbump(),
        upsell(),
        infoCard(),
        //recurrence();
        salesStatus(),
    ])
        .then(() => {
            $(".sirius-select-container").removeClass("disabled");
            $('input[name="daterange"]').removeAttr("disabled");
        })
        .catch(() => {
            $(".sirius-select-container").removeClass("disabled");
            $('input[name="daterange"]').removeAttr("disabled");
        });
}

function convertToReal(tooltipItem) {
    let tooltipValue = tooltipItem.raw;
    tooltipValue = tooltipValue + "";
    tooltipValue = parseInt(tooltipValue.replace(/[\D]+/g, ""));
    tooltipValue = tooltipValue + "";
    tooltipValue = tooltipValue.replace(/([0-9]{2})$/g, ",$1");

    if (tooltipValue.length > 6) {
        tooltipValue = tooltipValue.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    }

    return "R$ " + tooltipValue;
}

function updateStorage(v) {
    var existing = sessionStorage.getItem("info");
    existing = existing ? JSON.parse(existing) : {};
    Object.keys(v).forEach(function (val, key) {
        existing[val] = v[val];
    });
    sessionStorage.setItem("info", JSON.stringify(existing));
}

function exportReports() {
    // show/hide modal de exportar relatórios
    $(".lk-export").on("click", function (e) {
        e.preventDefault();
        $(".inner-reports").addClass("focus");
        $(".line-reports").addClass("d-flex");
    });

    $(".reports-remove").on("click", function (e) {
        e.preventDefault();
        $(".inner-reports").removeClass("focus");
        $(".line-reports").removeClass("d-flex");
    });
}

function salesStatus(st) {
    let status = st == "" || st == undefined ? $("#status-graph option:selected").val() : st;
    let statusHtml = "";
    $("#card-status .onPreLoadBig *").remove();
    $("#block-status").html(skeLoadBig);

    return $.ajax({
        method: "GET",
        url:
            "/api/reports/resume/sales?project_id=" +
            $("#select_projects option:selected").val() +
            "&date_range=" +
            $("input[name='daterange']").val() +
            "&status=" +
            status,
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            statusHtml = `
                <div class="finances-values" style="visibility: hidden;">
                    <span>R$</span>
                    <strong>0</strong>
                </div>
                <div class="row d-flex empty-graph">
                    <div class="info-graph no-info-graph">
                        <div class="no-sell">
                            ${bigGraph}
                            <footer class="footer-no-info">
                                <p>Sem dados para gerar o gráfico</p>
                            </footer>
                        </div>
                    </div>
                </div>
            `;
            $("#block-status").html(statusHtml);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { chart, total, variation } = response.data;

            // <div class="finances-values" style="display:none;">
            //     <svg class="${variation.color}" width="18" height="14" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            //         <path d="M10.1237 0L16.9451 0.00216293L17.1065 0.023901L17.2763 0.0736642L17.4287 0.145306L17.4865 0.18052L17.5596 0.23218L17.6737 0.332676L17.8001 0.484464L17.8876 0.634047L17.9499 0.792176L17.9845 0.938213L18 1.125V7.88084C18 8.50216 17.4964 9.00583 16.8751 9.00583C16.3057 9.00583 15.835 8.58261 15.7606 8.03349L15.7503 7.88084L15.7495 3.8415L9.41947 10.1762C9.01995 10.5759 8.39457 10.6121 7.95414 10.2849L7.82797 10.1758L5.62211 7.96668L1.92041 11.6703C1.48121 12.1098 0.768994 12.1099 0.329622 11.6707C-0.069807 11.2713 -0.106236 10.6463 0.220416 10.2059L0.329304 10.0797L4.82693 5.57966C5.22645 5.17994 5.85182 5.14374 6.29225 5.47097L6.41841 5.58004L8.62427 7.78914L14.1597 2.25H10.1237C9.55424 2.25 9.08361 1.82677 9.00912 1.27766L8.99885 1.125C8.99885 0.50368 9.50247 0 10.1237 0Z" fill="#1BE4A8"/>
            //     </svg>
            //     <em class="${variation.color}">${variation.value}</em>
            // </div>

            statusHtml = `
                <div class="d-flex justify-content-between box-finances-values finances-sales">
                    <div class="finances-values">
                        <strong>${total}</strong>
                    </div>
                </div>
                <section style="margin-left: -15px;">
                    <div class="graph-reports">
                        <div class="new-sell-graph"></div>
                    </div>
                </section>
            `;

            if (total !== "0") {
                $("#block-status").html(statusHtml);
                $(".new-sell-graph").html("<canvas id=sales-graph></canvas>");
                let labels = [...chart.labels];
                let series = [...chart.values];
                newSellGraph(series, labels);
            } else {
                statusHtml = `
                    <div class="finances-values" style="visibility: hidden;">
                        <span>R$</span>
                        <strong>0</strong>
                    </div>
                    <div class="row d-flex empty-graph">
                        <div class="info-graph no-info-graph">
                            <div class="no-sell">
                                ${bigGraph}
                                <footer class="footer-no-info">
                                    <p>Sem dados para gerar o gráfico</p>
                                </footer>
                            </div>
                        </div>
                    </div>
                `;
                $("#block-status").html(statusHtml);
            }
        },
    });
}

function newSellGraph(data, labels, variant = null) {
    const legendMargin = {
        id: "legendMargin",
        beforeInit(chart, legend, options) {
            const fitValue = chart.legend.fit;
            chart.legend.fit = function () {
                fitValue.bind(chart.legend)();
                return (this.height += 20);
            };
        },
    };
    const ctx = document.getElementById("sales-graph").getContext("2d");
    var gradient = ctx.createLinearGradient(0, 0, 0, 450);
    gradient.addColorStop(0, "rgba(54,216,119, 0.23)");
    gradient.addColorStop(1, "rgba(255, 255, 255, 0)");

    const myChart = new Chart(ctx, {
        plugins: [legendMargin],
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: "Legenda",
                    data,
                    color: "#636363",
                    backgroundColor: gradient,
                    borderColor: "#1BE4A8",
                    borderWidth: 4,
                    fill: true,
                    borderRadius: 4,
                    barThickness: 30,
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: false },
            },
            responsive: true,
            layout: {
                padding: 0,
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            family: "Inter",
                            size: 12,
                        },
                        color: "#A2A3A5",
                    },
                },
                y: {
                    grid: {
                        color: "#ECE9F1",
                        drawBorder: false,
                    },

                    ticks: {
                        padding: 15,
                        font: {
                            family: "Inter",
                            size: 12,
                        },
                        color: "#A2A3A5",
                        callback: function (value) {
                            return formatCash(value);
                        },
                    },
                },
            },
            pointBackgroundColor: "#1BE4A8",
            // radius: (variant != '0%') ? 3 : 0,
            interaction: {
                intersect: false,
                mode: "index",
                borderRadius: 4,
                usePointStyle: true,
                yAlign: "bottom",
                padding: 10,
                titleSpacing: 10,
                callbacks: {
                    label: function (tooltipItem) {
                        return tooltipItem.raw == 0 ? "0" : tooltipItem.raw;
                    },
                    labelPointStyle: function (context) {
                        return {
                            pointStyle: "rect",
                            borderRadius: 4,
                            rotatio: 0,
                        };
                    },
                },
            },
        },
    });
}

function kConverter(num) {
    return num <= 999 ? num : (0.1 * Math.floor(num / 100)).toFixed(1).replace(".0", "");
}

function removeDuplcateItem(item) {
    for (i = 0; i < $(item).length; i++) {
        text = $(item).get(i);
        for (j = i + 1; j < $(item).length; j++) {
            text_to_compare = $(item).get(j);
            if (text.innerHTML == text_to_compare.innerHTML) {
                $(text_to_compare).remove();
                j--;
                maxlength = $(item).length;
            }
        }
    }
}

// abort all ajax
$.ajaxQ = (function () {
    var id = 0,
        Q = {};

    $(document).ajaxSend(function (e, jqx) {
        jqx._id = ++id;
        Q[jqx._id] = jqx;
    });
    $(document).ajaxComplete(function (e, jqx) {
        delete Q[jqx._id];
    });

    return {
        abortAll: function () {
            var r = [];
            $.each(Q, function (i, jqx) {
                r.push(jqx._id);
                jqx.abort();
            });
            return r;
        },
    };
})();

let skeLoad = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let bigGraph = `
<svg width="863" height="242" viewBox="0 0 863 242" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M748.537 160.747C793.828 168.612 825.042 215.402 863 223.075V242H6.10352e-05V127.825C42.2571 127.825 47.8961 162.696 67.7818 153.097C87.6674 143.497 108.556 77.4964 131.756 80.9871C154.956 84.4778 167.688 -8.14635 216.574 0.580398C265.459 9.30715 301.438 108.98 340.381 66.2193C379.324 23.4582 387.601 46.0209 429.03 37.2942C470.458 28.5674 488.607 133.109 527.846 129.769C567.086 126.428 577.561 213.379 617.605 205.314C666.13 195.541 661.906 190.067 683.483 177.391C705.059 164.714 724.398 156.555 748.537 160.747Z" fill="url(#paint0_linear_2642_647)"/>
<defs>
<linearGradient id="paint0_linear_2642_647" x1="431.5" y1="-3.8329e-05" x2="431.5" y2="229.5" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let noData = `
<svg width="275" height="122" viewBox="0 0 275 122" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="48" y="94" width="187" height="25" rx="4" fill="url(#paint0_linear_2696_543)"/>
<rect x="48" y="5" width="227" height="25" rx="4" fill="url(#paint1_linear_2696_543)"/>
<rect x="48" y="50" width="227" height="24" rx="4" fill="url(#paint2_linear_2696_543)"/>
<path opacity="0.6" d="M29 89C31.2091 89 33 90.7909 33 93L33 117C33 119.209 31.2091 121 29 121L5 121C2.79086 121 0.999999 119.209 0.999999 117L1 93C1 90.7909 2.79086 89 5 89L29 89Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 1C31.2091 1 33 2.79086 33 5L33 29C33 31.2091 31.2091 33 29 33L5 33C2.79086 33 0.999999 31.2091 0.999999 29L1 5C1 2.79086 2.79086 0.999999 5 0.999999L29 1Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 45C31.2091 45 33 46.7909 33 49L33 73C33 75.2091 31.2091 77 29 77L5 77C2.79086 77 0.999999 75.2091 0.999999 73L1 49C1 46.7909 2.79086 45 5 45L29 45Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<defs>
<linearGradient id="paint0_linear_2696_543" x1="141.5" y1="94" x2="235" y2="94" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint1_linear_2696_543" x1="161.5" y1="5" x2="275" y2="4.99999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint2_linear_2696_543" x1="75.2025" y1="74" x2="250.612" y2="73.9999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let skeLoadBig = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>

        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>

        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let noCart = `
<svg width="275" height="34" viewBox="0 0 275 34" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="48" y="4.5144" width="227" height="24" rx="4" fill="url(#paint0_linear_3261_496)"/>
<path opacity="0.6" d="M29 0.514404C31.2091 0.514404 33 2.30527 33 4.5144L33 28.5144C33 30.7235 31.2091 32.5144 29 32.5144L5 32.5144C2.79086 32.5144 0.999999 30.7235 0.999999 28.5144L1 4.5144C1 2.30526 2.79086 0.514403 5 0.514403L29 0.514404Z" fill="white" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<defs>
<linearGradient id="paint0_linear_3261_496" x1="75.2025" y1="28.5144" x2="250.612" y2="28.5143" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>

`;

let noListProducts = `
<svg width="200" height="90" viewBox="0 0 275 122" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="48" y="94" width="187" height="25" rx="4" fill="url(#paint0_linear_2696_543)"/>
<rect x="48" y="5" width="227" height="25" rx="4" fill="url(#paint1_linear_2696_543)"/>
<rect x="48" y="50" width="227" height="24" rx="4" fill="url(#paint2_linear_2696_543)"/>
<path opacity="0.6" d="M29 89C31.2091 89 33 90.7909 33 93L33 117C33 119.209 31.2091 121 29 121L5 121C2.79086 121 0.999999 119.209 0.999999 117L1 93C1 90.7909 2.79086 89 5 89L29 89Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 1C31.2091 1 33 2.79086 33 5L33 29C33 31.2091 31.2091 33 29 33L5 33C2.79086 33 0.999999 31.2091 0.999999 29L1 5C1 2.79086 2.79086 0.999999 5 0.999999L29 1Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 45C31.2091 45 33 46.7909 33 49L33 73C33 75.2091 31.2091 77 29 77L5 77C2.79086 77 0.999999 75.2091 0.999999 73L1 49C1 46.7909 2.79086 45 5 45L29 45Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<defs>
<linearGradient id="paint0_linear_2696_543" x1="141.5" y1="94" x2="235" y2="94" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint1_linear_2696_543" x1="161.5" y1="5" x2="275" y2="4.99999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint2_linear_2696_543" x1="75.2025" y1="74" x2="250.612" y2="73.9999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let noGraph = `
    <svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M55.5 111C86.1518 111 111 86.1518 111 55.5C111 24.8482 86.1518 0 55.5 0C24.8482 0 0 24.8482 0 55.5C0 86.1518 24.8482 111 55.5 111Z" fill="#F4F6FB"/>
        <path d="M88.7999 111H22.2V39.22C25.339 39.2165 28.3485 37.9679 30.5682 35.7483C32.7879 33.5286 34.0364 30.5191 34.04 27.38H76.96C76.9566 28.935 77.2617 30.4753 77.8576 31.9116C78.4534 33.3479 79.3282 34.6519 80.4313 35.7479C81.5273 36.8513 82.8313 37.7264 84.2678 38.3224C85.7043 38.9184 87.2447 39.2235 88.7999 39.22V111Z" fill="white"/>
        <path d="M55.5 75.48C65.3086 75.48 73.26 67.5286 73.26 57.72C73.26 47.9114 65.3086 39.96 55.5 39.96C45.6914 39.96 37.74 47.9114 37.74 57.72C37.74 67.5286 45.6914 75.48 55.5 75.48Z" fill="#E8EAEB"/>
        <path d="M61.7791 66.0922L55.5 59.8131L49.2209 66.0922L47.1279 63.9992L53.407 57.7201L47.1279 51.441L49.2209 49.348L55.5 55.6271L61.7791 49.348L63.8721 51.441L57.593 57.7201L63.8721 63.9992L61.7791 66.0922Z" fill="white"/>
        <path d="M65.1199 79.92H45.8799C44.6538 79.92 43.6599 80.9139 43.6599 82.14C43.6599 83.3661 44.6538 84.36 45.8799 84.36H65.1199C66.346 84.36 67.3399 83.3661 67.3399 82.14C67.3399 80.9139 66.346 79.92 65.1199 79.92Z" fill="#F4F6FB"/>
        <path d="M71.78 88.8H39.22C37.9939 88.8 37 89.7939 37 91.02C37 92.2461 37.9939 93.24 39.22 93.24H71.78C73.0061 93.24 74 92.2461 74 91.02C74 89.7939 73.0061 88.8 71.78 88.8Z" fill="#F4F6FB"/>
        </svg>
    <footer>
        <h4>Nada por aqui...</h4>
        <p>
            Não há dados suficientes
            para gerar este relatório.
        </p>
    </footer>
`;

let noRecurrence = `
    <div class="d-flex box-graph-dist">
        <div class="info-graph">
            <div class="no-sell">
                <svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M55.5 111C86.1518 111 111 86.1518 111 55.5C111 24.8482 86.1518 0 55.5 0C24.8482 0 0 24.8482 0 55.5C0 86.1518 24.8482 111 55.5 111Z" fill="#F4F6FB"/>
                    <path d="M88.7999 111H22.2V39.22C25.339 39.2165 28.3485 37.9679 30.5682 35.7483C32.7879 33.5286 34.0364 30.5191 34.04 27.38H76.96C76.9566 28.935 77.2617 30.4753 77.8576 31.9116C78.4534 33.3479 79.3282 34.6519 80.4313 35.7479C81.5273 36.8513 82.8313 37.7264 84.2678 38.3224C85.7043 38.9184 87.2447 39.2235 88.7999 39.22V111Z" fill="white"/>
                    <path d="M55.5 75.48C65.3086 75.48 73.26 67.5286 73.26 57.72C73.26 47.9114 65.3086 39.96 55.5 39.96C45.6914 39.96 37.74 47.9114 37.74 57.72C37.74 67.5286 45.6914 75.48 55.5 75.48Z" fill="#E8EAEB"/>
                    <path d="M61.7791 66.0922L55.5 59.8131L49.2209 66.0922L47.1279 63.9992L53.407 57.7201L47.1279 51.441L49.2209 49.348L55.5 55.6271L61.7791 49.348L63.8721 51.441L57.593 57.7201L63.8721 63.9992L61.7791 66.0922Z" fill="white"/>
                    <path d="M65.1199 79.92H45.8799C44.6538 79.92 43.6599 80.9139 43.6599 82.14C43.6599 83.3661 44.6538 84.36 45.8799 84.36H65.1199C66.346 84.36 67.3399 83.3661 67.3399 82.14C67.3399 80.9139 66.346 79.92 65.1199 79.92Z" fill="#F4F6FB"/>
                    <path d="M71.78 88.8H39.22C37.9939 88.8 37 89.7939 37 91.02C37 92.2461 37.9939 93.24 39.22 93.24H71.78C73.0061 93.24 74 92.2461 74 91.02C74 89.7939 73.0061 88.8 71.78 88.8Z" fill="#F4F6FB"/>
                </svg>
                <footer>
                    <h4>Nada por aqui...</h4>
                    <p>
                        Não há dados suficientes
                        para gerar este relatório.
                    </p>
                </footer>
            </div>
        </div>
    </div>
`;

let noUpsell = `
<div class="d-flex align-items">
    <div class="balance col-4">
        <div class="box-ico-cash">
            <span class="ico-cash">
            <svg width="55" height="55" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="27.5" cy="27.5" r="26.5" stroke="#E8EAEB" stroke-width="2"/>
                <path d="M13.3645 27.2382C12.8773 27.7315 12.8787 28.5298 13.3677 29.0213C13.8567 29.5128 14.6481 29.5114 15.1353 29.0181L28 15.9946L40.8647 29.0181C41.3519 29.5114 42.1433 29.5128 42.6323 29.0213C43.1213 28.5298 43.1227 27.7315 42.6355 27.2382L28.9739 13.408C28.4366 12.864 27.5634 12.864 27.0261 13.408L13.3645 27.2382ZM13.3645 39.8492C12.8773 40.3424 12.8787 41.1408 13.3677 41.6323C13.8567 42.1238 14.6481 42.1224 15.1353 41.6291L28 28.6056L40.8647 41.6291C41.3519 42.1224 42.1433 42.1238 42.6323 41.6323C43.1213 41.1408 43.1227 40.3424 42.6355 39.8492L28.9739 26.0189C28.4366 25.475 27.5634 25.475 27.0261 26.0189L13.3645 39.8492Z" fill="#E8EAEB"/>
            </svg>
            </span>
        </div>
    </div>
    <div class="balance col-8">
        <h6 class="no-orderbump">Sem vendas por upsell</h6>
        <p class="txt-no-orderbump">Ofereça mais um produto no checkout e aumente sua conversão</p>
    </div>
</div>
`;

let noSales = `
<div class="box-payment-option pad-0">
    <div class="d-flex align-items list-sales">
        <div class="d-flex align-items">
            <div class="no-sell">
                <svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M55.5 111C86.1518 111 111 86.1518 111 55.5C111 24.8482 86.1518 0 55.5 0C24.8482 0 0 24.8482 0 55.5C0 86.1518 24.8482 111 55.5 111Z" fill="#F4F6FB"/>
                    <path d="M88.7999 111H22.2V39.22C25.339 39.2165 28.3485 37.9679 30.5682 35.7483C32.7879 33.5286 34.0364 30.5191 34.04 27.38H76.96C76.9566 28.935 77.2617 30.4753 77.8576 31.9116C78.4534 33.3479 79.3282 34.6519 80.4313 35.7479C81.5273 36.8513 82.8313 37.7264 84.2678 38.3224C85.7043 38.9184 87.2447 39.2235 88.7999 39.22V111Z" fill="white"/>
                    <path d="M55.5 75.48C65.3086 75.48 73.26 67.5286 73.26 57.72C73.26 47.9114 65.3086 39.96 55.5 39.96C45.6914 39.96 37.74 47.9114 37.74 57.72C37.74 67.5286 45.6914 75.48 55.5 75.48Z" fill="#E8EAEB"/>
                    <path d="M61.7791 66.0922L55.5 59.8131L49.2209 66.0922L47.1279 63.9992L53.407 57.7201L47.1279 51.441L49.2209 49.348L55.5 55.6271L61.7791 49.348L63.8721 51.441L57.593 57.7201L63.8721 63.9992L61.7791 66.0922Z" fill="white"/>
                    <path d="M65.1199 79.92H45.8799C44.6538 79.92 43.6599 80.9139 43.6599 82.14C43.6599 83.3661 44.6538 84.36 45.8799 84.36H65.1199C66.346 84.36 67.3399 83.3661 67.3399 82.14C67.3399 80.9139 66.346 79.92 65.1199 79.92Z" fill="#f4f4f4"/>
                    <path d="M71.78 88.8H39.22C37.9939 88.8 37 89.7939 37 91.02C37 92.2461 37.9939 93.24 39.22 93.24H71.78C73.0061 93.24 74 92.2461 74 91.02C74 89.7939 73.0061 88.8 71.78 88.8Z" fill="#f4f4f4"/>
                </svg>
                <footer>
                    <h4>Nada por aqui...</h4>
                    <p>
                        Não há dados suficientes
                        para gerar este relatório.
                    </p>
                </footer>
            </div>
        </div>
    </div>
</div>
`;
