// CORES DO GRAFICO
const chartDefaultColorsLabel = [
    "rgba(51, 99, 143, 1)", // POSTADO (DARK-BLUE)
    "rgba(255, 205, 27, 1)", // EM TRÂNSITO (YELLOW)
    "rgba(0, 177, 255, 1)", // SAIU PARA ENTREGA (LIGHT-BLUE)
    "rgba(255, 47, 47, 1)", // PROBLEMA NA ENTREGA (RED)
    "rgba(185, 185, 185, 1)", // NAO INFORMADO (GRAY)
    "rgba(27, 228, 168, 1)", // ENTREGUES (LIGHT GREEN)
];

//CORES DA LEGENDA DO GRAFICO //
let tracking_id = "undefined";
const statusEnum = {
    1: "statusPosted",
    2: "statusInTransit",
    3: "statusDelivered",
    4: "statusOnDelivery",
    5: "statusProblem",
    "": "statusWithoutInfo",
};

//ICONES DO STATUS //
const systemStatus = {
    1: "",
    2: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" data-container=".page" title="O código foi reconhecido pela transportadora mas, ainda não teve nenhuma movimentação. Essa informação pode ser atualizada nos próximos dias">report_problem</i>`,
    3: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" data-container=".page" title="O código não foi reconhecido por nenhuma transportadora">report_problem</i>`,
    4: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" data-container=".page" title="A data de postagem da remessa é anterior a data da venda">report_problem</i>`,
    5: `<i class="material-icons ml-2 red-gradient" data-toggle="tooltip" data-container=".page" title="Já existe uma venda com esse código de rastreio cadastrado">report_problem</i>`,
    "": "",
};

$(() => {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#project-select").find("option").not(":first").remove();
        $("#project-select").val($("#project-select option:first").val());
        loadOnTable("#dados_tabela", "#tabela_trackings");
        let loadingSelector =
            "#percentual-posted, #percentual-dispatched, #percentual-out, #percentual-exception, #percentual-unknown, #percentual-delivered, #graphic-loading";
        let loadingSettings = {
            styles: {
                container: {
                    height: "36px",
                    minHeight: "0px",
                    justifyContent: "center",
                },
                loader: {
                    width: "20px",
                    height: "20px",
                    borderWidth: "3px",
                },
            },
        };
        window.showLoading(loadOnAny, loadingSelector, loadingSettings);
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                getProjects(data2, "company-navbar");
            });
        });
    });

    $(".applySelect2").select2({
        width: "100%",
        multiple: true,
        language: {
            noResults: function () {
                return "Nenhum resultado encontrado";
            },
            searching: function () {
                return "Procurando...";
            },
        },
    });

    $("#tracking-product-image").on("error", function () {
        $(this).attr("src", "https://nexuspay-digital-products.s3.amazonaws.com/admin/produto.svg");
    });

    $("#sale").on("change paste keyup select", function () {
        let val = $(this).val();

        if (val === "") {
            $("#date_updated").attr("disabled", false).removeClass("disableFields");
        } else {
            $("#date_updated").val(moment("2018-01-01").format("DD/MM/YYYY") + " - " + moment().format("DD/MM/YYYY"));
            $("#date_updated").attr("disabled", true).addClass("disableFields");
        }
    });

    $(document).on("click", ".copy", function () {
        let temp = $("<input>");
        $("body").append(temp);
        temp.val($(this).html()).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Código copiado!");
    });

    $(document).on("click", ".tracking-add, .tracking-edit", function (event) {
        let row = $(this).parent().parent();
        row.find(".input-tracking-code")
            .removeClass("fake-label")
            .prop("readonly", false)
            .focus()
            .removeAttr("placeholder");
        row.find(".tracking-save, .tracking-close").show();
        row.find(".tracking-detail, .tracking-add").hide();
        $(this).hide();
    });

    $(document).on("click", ".tracking-close", function (event) {
        let row = $(this).closest(".row");
        row.find(".input-tracking-code")
            .prop("readonly", true)
            .blur()
            .removeClass("border-danger")
            .attr("placeholder", "Clique para adicionar");

        if ($(this).attr("data-code").length < 1) {
            row.find(".input-tracking-code").addClass("fake-label").val("");
        }

        let compare = $(this).attr("data-code");
        if (row.find(".input-tracking-code").val() !== compare) {
            row.find(".input-tracking-code").val(compare);
        }

        row.find(".tracking-add, .tracking-detail, .tracking-edit").show();
        row.find(".tracking-save, .tracking-close").hide();
        $(this).hide();
    });

    $("#bt_filter").on("click", function () {
        $("#container-pagination-trackings").removeClass("d-flex").addClass("d-none");

        window.loadData();
    });

    let startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    let endDate = moment().format("YYYY-MM-DD");
    $("#date_updated").daterangepicker(
        {
            startDate: moment().subtract(30, "days"),
            endDate: moment(),
            opens: "right",
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
                "Últimos 30 dias": [moment().subtract(30, "days"), moment()],
                "Este mês": [moment().startOf("month"), moment().endOf("month")],
                "Mês passado": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                Vitalício: [moment("2018-01-01 00:00:00"), moment()],
            },
        },
        function (start, end) {
            startDate = start.format("YYYY-MM-DD");
            endDate = end.format("YYYY-MM-DD");
        }
    );

    function searchIsLocked(elementButton) {
        return elementButton.attr("block_search");
    }

    function lockSearch(elementButton) {
        elementButton.attr("block_search", "true");
        //set layout do button block
    }

    function unlockSearch(elementButton) {
        elementButton.attr("block_search", "false");
        //layout do button block
    }

    window.loadData = function () {
        elementButton = $("#bt_filter");
        $("#myChart").hide();
        if (searchIsLocked(elementButton) != "true") {
            lockSearch(elementButton);
            index();
            getResume();
        }
    };

    function getFilters(urlParams = false) {
        let data = {
            tracking_code: $("#tracking_code").val(),
            status: $("#status").val(),
            project: $("#project-select").val(),
            date_updated: $("#date_updated").val(),
            sale: $("#sale").val().replace("#", ""),
            transaction_status: $("#status_commission").val(),
            problem: $("#tracking_problem").prop("checked") ? 1 : 0,
            company: $(".company-navbar").val(),
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

    getCompaniesAndProjects().done(function (data) {
        getProjects(data);
    });

    /**
     * List Projects
     */
    function getProjects(data, origin = "") {
        if (origin == "") loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/sales/projects-with-sales",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                console.log("erro");
                console.log(response);
                if (origin == "") loadingOnScreen();
            },
            success: function success(response) {
                if (!isEmpty(response)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel").show();
                    $.each(response, function (i, project) {
                        $("#project-select").append($("<option>", { value: project.project_id, text: project.name }));
                    });
                    $("#project-select option:first").attr("selected", "selected");
                    window.loadData();
                    if (origin == "") loadingOnScreenRemove();
                } else {
                    if (!isEmpty(data.company_default_projects)) {
                        $("#project-empty").hide();
                        $("#project-not-empty").show();
                        $("#export-excel").show();
                        $("#projeto option:first").attr("selected", "selected");
                        window.loadData();
                        if (origin == "") loadingOnScreenRemove();
                    } else {
                        if (origin == "") loadingOnScreenRemove();
                        $("#project-empty").show();
                        $("#project-not-empty").hide();
                    }
                }
            },
        });
    }

    //CARD RESUMO DE ENTREGAS E GRAFICO
    //FORMATAR NUMERO INTEIRO
    function numberWithDecimal(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    //CRIANDO GRAFICO
    let myChart = null;
    function inicializeChart(colors, dataValues) {
        if (dataValues) dataValues = dataValues.map((n) => (n ? n.toString().split(".").join("") : "0"));
        $("#myChart").show();
        if (myChart !== null) {
            myChart.destroy();
        }
        const ctx = document.getElementById("myChart");
        myChart = new Chart(ctx, {
            id: "custom_canvas_background_color",
            type: "doughnut",
            data: {
                labels: [
                    "Postados: ",
                    "Em trânsito: ",
                    "Saiu para entrega: ",
                    "Problema na entrega: ",
                    "Não informado: ",
                    "Entregues: ",
                ],
                datasets: [
                    {
                        data: dataValues,
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 1,
                        cutout: "83%",
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            title: (tooltipItem) => `${tooltipItem[0].label}`,
                            label: (tooltipItem) =>
                                tooltipItem.dataset.data[tooltipItem.dataIndex] > 10000
                                    ? Math.round(tooltipItem.dataset.data[tooltipItem.dataIndex] / 1000, 1) + "K"
                                    : numberWithDecimal(tooltipItem.dataset.data[tooltipItem.dataIndex]),
                        },
                    },
                },
            },
        });
    }

    window.showLoading = function (loadOnAny, loadingSelector, loadingSettings) {
        loadOnAny(loadingSelector, false, loadingSettings);
        $("#graphic-loading").append($(".loader-any-container")[6]).show().css("z-index", "2");
    };

    //GERANDO DADOS DO CARD E DO GRAFICO
    function getResume() {
        let loadingSelector =
            "#percentual-posted, #percentual-dispatched, #percentual-out, #percentual-exception, #percentual-unknown, #percentual-delivered, #graphic-loading";
        let loadingSettings = {
            styles: {
                container: {
                    height: "36px",
                    minHeight: "0px",
                    justifyContent: "center",
                },
                loader: {
                    width: "20px",
                    height: "20px",
                    borderWidth: "3px",
                },
            },
        };

        window.showLoading(loadOnAny, loadingSelector, loadingSettings);

        $.ajax({
            method: "GET",
            url: "/api/tracking/resume?" + getFilters(true).substr(1),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
                inicializeChart(chartDefaultColorsLabel, [1, 0, 0, 0, 0, 0]);
                loadOnAny(loadingSelector, true);
                $("#graphic-loading").append($(".loader-any-container")[6]).hide();
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    alertCustom("error", "Erro ao carregar resumo dos rastreios");
                    inicializeChart(chartDefaultColorsLabel, [1, 0, 0, 0, 0, 0]);
                    return;
                }
                setDataView(response.data);
                loadOnAny(loadingSelector, true);
                $("#graphic-loading").append($(".loader-any-container")[6]).hide();
            },
        });
    }

    function verifyValueIsZero(values) {
        if (values < 1 || typeof values == null) {
            return true;
        }
        return false;
    }

    function setDataView(data) {
        let { total, posted, dispatched, out_for_delivery, exception, unknown, delivered } = data;
        const thousand = 10000;

        if (verifyValueIsZero(data.total)) {
            if ($("#noData").length > 0) {
                return;
            }
            $("#dataCharts").append('<img id="noData" src="/build/global/img/sem-dados.svg" />');
            $("#data-labels").append('<span id="warning-text" class="d-flex"> Nenhum rastreamento encontrado </span>');
            $("#myChart, .labels, .total-container").hide();
        } else {
            if ($("#noData").length > 0) {
                $("#noData, #warning-text").remove();
            }

            $(".labels, .total-container").show(); //#myChart,
            const myTimeoutChart = setTimeout(function () {
                inicializeChart(chartDefaultColorsLabel, [
                    posted,
                    dispatched,
                    out_for_delivery,
                    exception,
                    unknown,
                    delivered,
                ]);
            }, 3000);
        }
        const formatTotal = "<div>Total:<br> <b>" + numberWithDecimal(total) + "</b> </div>";

        $("#total-products")
            .text(total > thousand ? `${parseFloat(numberWithDecimal(total)).toFixed(1)}K` : numberWithDecimal(total))
            .attr("data-original-title", formatTotal);

        $("#percentual-posted .resume-number").html(posted <= 0 ? (posted = 0) : (posted = numberWithDecimal(posted)));

        $("#percentual-dispatched .resume-number").html(
            dispatched <= 0 ? (dispatched = 0) : (dispatched = numberWithDecimal(dispatched))
        );

        $("#percentual-out .resume-number").html(
            out_for_delivery <= 0 ? (out_for_delivery = 0) : (out_for_delivery = numberWithDecimal(out_for_delivery))
        );

        $("#percentual-exception .resume-number").html(
            exception <= 0 ? (exception = 0) : (exception = numberWithDecimal(exception))
        );

        $("#percentual-unknown .resume-number").html(
            unknown <= 0 ? (unknown = 0) : (unknown = numberWithDecimal(unknown))
        );

        $("#percentual-delivered .resume-number").html(
            delivered <= 0 ? (delivered = 0) : (delivered = numberWithDecimal(delivered))
        );

        //add this line here for all $('#percentual-delivered .resume-percentual').html('(' + (delivered ? (delivered * total / 100).toFixed(2) : '0.00') + '%)');
        //add in html <span class="resume-percentual">(0.00%)</span> to show per cent
    }

    function index(link = null) {
        if (link == null) {
            link = "/api/tracking?" + getFilters(true).substr(1);
        } else {
            link = "/api/tracking" + link + getFilters(true);
        }

        loadOnTable("#dados_tabela", "#tabela_trackings");
        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#dados_tabela").html("");
                $("#tabela_trackings").addClass("table-striped");
                $("#pagination-trackings").html("");

                if (isEmpty(response.data)) {
                    $("#container-pagination-trackings").removeClass("d-flex").addClass("d-none");
                    $("#dados_tabela").html(`
                    <tr class="text-center">
                      <td colspan="6" style="vertical-align: middle;height:257px;">
                        <img style="width:124px;margin-right:12px;" src="${$("#dados_tabela").attr("img-empty")}">
                        Nenhum rastreamento encontrado
                      </td>
                    </tr>`);
                    return;
                }

                let grayRow = false;
                let lastSale = "";

                $.each(response.data, function (index, tracking) {
                    if (lastSale !== tracking.sale) {
                        grayRow = !grayRow;
                    }

                    let dados = "";

                    dados += "<tr>";

                    dados += `${lastSale !== tracking.sale
                            ? `<td class="detalhes_venda pointer table-title col-sm-1" venda="${tracking.sale}" style="padding-right:4px">
                                    #${tracking.sale}
                                </td>`
                            : `<td></td>`
                        }`;

                    dados += `<td>
                                    <div class="fullInformation-tracking ellipsis-text" style="max-width: 240px; display:block; margin: 0px 0px 0px 0px;">
                                        ${tracking.product.amount}x ${tracking.product.name}
                                        ${tracking.product.description ? "(" + tracking.product.description + ")" : ""}
                                    </div>
                                    <div class="container-tooltips-tracking"></div>
                                </td>`;

                    dados += `<td class="col-sm-1">${tracking.approved_date}</td>`;

                    dados += `<td class="text-center col-sm-2">
                                    <span class="badge ${statusEnum[tracking.tracking_status_enum]}">
                                        ${tracking.tracking_status}
                                    </span>
                                </td>`;

                    dados += `<td style="width: 2%;padding: 0px !important;">
                                    ${systemStatus[tracking.system_status_enum] != undefined
                            ? systemStatus[tracking.system_status_enum]
                            : ""
                        }
                                    ${tracking.is_chargeback_recovered
                            ? `<img class="orange-gradient ml-10" width="20px" src="/build/global/img/svg/chargeback.svg" title="Chargeback recuperado">`
                            : ``
                        }
                                </td>`;

                    let save = `<div class="save-close buttons d-flex px-0" style="max-height: 35px;">
                                    <a id='pencil' class='o-checkmark-1 text-white tracking-save pointer mr-10 text-center default-buttons' title="Salvar" pps='${tracking.pps_id}'style="display:none; height:34px"></a>
                                    <div class='tracking-close pointer' data-code='${tracking.tracking_code}' title="Fechar" style="display:none; padding: 0px 7px 0px 9px !important; height:34px">
                                        &#x2715
                                    </div>
                                </div>`;

                    let edit_detail_save_close =
                        `
                            <div class="edit-detail" style="text-align:right; margin-top: 3px">
                                <a class='tracking-edit pointer default-buttons' title="Editar" style="margin-right: 20px; padding-top:8px; padding-bottom: 4px;">
                                    <span class="text-right o-edit-1"></span>
                                </a>
                                <a class='tracking-detail pointer col-5' title="Visualizar" tracking='${tracking.id}' style="margin-right: 0; vertical-align: middle;">
                                    <span class="o-eye-1" style="padding-left: 10px !important"></span>
                                </a>
                                ` +
                        save +
                        `
                            </div>`;

                    dados += `<td class="text-left mb-0" style="max-height:74px!important;">
                                <div class="row" style="max-height: 35px;">`;

                    dados += `${!tracking.tracking_status_enum
                            ? `<div class="col-7">
                            <input maxlength="30" minlength="10" class="mr-10 form-control font-weight-bold input-tracking-code fake-label" placeholder="Clique para adicionar" value="${tracking.tracking_code}" style="border-radius: 8px; max-height:35px; padding: 8px 0 8px 10px !important;">
                            </div>
                            <a class='tracking-add pointer mt-1 px-0 default-buttons' title="Adicionar">
                            <span id="add-tracking-code" class='o-add-1 text-primary border border-primary'></span>
                        </a>` + save
                            : ``
                        }`;

                    dados += `${tracking.tracking_status_enum
                            ? `<div class="col-7" >
                            <input maxlength="30" minlength="10" class="mr-10 form-control font-weight-bold input-tracking-code" readonly placeholder="Informe o código de rastreio" style="border-radius: 8px;" value="${tracking.tracking_code}">
                            </div>
                        ` +
                            edit_detail_save_close +
                            ``
                            : ``
                        }`;

                    dados += `</div>
                        </td>`;

                    dados += `</tr>`;

                    $("#dados_tabela").append(dados);
                    lastSale = tracking.sale;
                });
                pagination(response, "trackings", index);
                $("#container-pagination-trackings").removeClass("d-none").addClass("d-flex");

                $(".fullInformation-tracking").bind("mouseover", function () {
                    var $this = $(this);

                    if (this.offsetWidth < this.scrollWidth && !$this.attr("title")) {
                        $this
                            .attr({
                                "data-toggle": "tooltip",
                                "data-placement": "top",
                                "data-title": $this.text(),
                            })
                            .tooltip({ container: ".container-tooltips-tracking" });
                        $this.tooltip("show");
                    }
                });
            },
            complete: (response) => {
                unlockSearch($("#bt_filter"));
            },
        });
    }

    //modal de detalhes
    $(document).on("click", ".tracking-detail", function () {
        tracking_id = $(this).attr("tracking");

        loadOnAny("#modal-tracking-details");
        $("#modal-tracking").modal("show");

        $.ajax({
            method: "GET",
            url: "/api/tracking/" + $(this).attr("tracking"),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadOnAny("#modal-tracking-details", true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                let tracking = response.data;

                //preenche os campos
                $("#tracking-code").text(tracking.tracking_code);
                $("#tracking-product-image").attr("src", tracking.product.photo);
                $("#tracking-product-name").text(
                    tracking.product.name +
                    (tracking.product.description ? "(" + tracking.product.description + ")" : "")
                );
                $("#tracking-product-amount").text(tracking.amount + "x");
                $("#tracking-delivery-address").text(
                    "Endereço: " + tracking.delivery.street + ", " + tracking.delivery.number
                );
                $("#tracking-delivery-neighborhood").text("Bairro: " + tracking.delivery.neighborhood);
                $("#tracking-delivery-zipcode").text("CEP: " + tracking.delivery.zip_code);
                $("#tracking-delivery-city").text("Cidade: " + tracking.delivery.city + "/" + tracking.delivery.state);
                $("#modal-tracking-details .btn-notify-trackingcode").attr("tracking", tracking.id);

                if (tracking.link) {
                    $("#link-tracking a").attr("href", tracking.link);
                    $("#link-tracking").show();
                } else {
                    $("#link-tracking").hide();
                }

                $("#table-checkpoint").html("");
                if (!isEmpty(tracking.checkpoints)) {
                    for (let checkpoint of tracking.checkpoints) {
                        $("#table-checkpoint").append(
                            `<tr>
                              <td>${checkpoint.created_at}</td>
                              <td>
                                  <span class="text-secondary badge badge-${statusEnum[checkpoint.tracking_status_enum]
                            }">${checkpoint.tracking_status}</span>
                              </td>
                              <td>${checkpoint.event}</td>
                          </tr>`
                        );
                    }
                }

                loadOnAny("#modal-tracking-details", true);
            },
        });
    });

    $(document).on("click", ".input-tracking-code", function () {
        let row = $(this).parent().parent();
        $(".tracking-close").click();
        row.find(".tracking-edit, .tracking-add").click();
    });

    //salvar tracking
    $(document).on("click", ".tracking-save", function () {
        let btnSave = $(this);
        btnSave.prop("disabled", true);

        let tracking_code = btnSave.parent().parent().parent().find(".input-tracking-code").val();
        let ppsId = btnSave.attr("pps");

        $.ajax({
            method: "POST",
            url: "/api/tracking",
            data: { tracking_code: tracking_code, product_plan_sale_id: ppsId },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                btnSave.prop("disabled", false);
                errorAjaxResponse(response);

                btnSave.parent().parent().find(".input-tracking-code").addClass("border-danger");
                setTimeout(() => {
                    btnSave
                        .parent()
                        .parent()
                        .find(".input-tracking-code")
                        .val("")
                        .removeClass("border-danger")
                        .attr("placeholder", "Clique para adicionar");
                    btnSave.parent().parent().find(".tracking-close").trigger("click");
                }, 1000);
            },
            success: (response) => {
                if (!isEmpty(response.data.tracking_status)) {
                    let tracking = response.data;

                    let td = btnSave.closest(".row");
                    let col7 = td.find(".col-7");

                    td.find(".tracking-add, .edit-detail, .save-close").remove();
                    td.find(".input-tracking-code").removeClass("fake-label, border-danger");

                    let buttons = `
                    <div class="edit-detail" style="text-align:right; margin-top: 3px">
                        <a class='tracking-edit pointer default-buttons' title="Editar" style="margin-right: 20px; padding-top:8px; padding-bottom: 4px;">
                            <span class="text-right o-edit-1"></span>
                        </a>
                        <a class='tracking-detail pointer col-5' title="Visualizar" tracking='${response.data.id}' style="margin-right: 0; vertical-align: middle;">
                            <span class="o-eye-1" style="padding-left: 10px !important"></span>
                        </a>
                        <div class="save-close buttons d-flex px-0" style="max-height: 35px;">
                                <a id='pencil' class='o-checkmark-1 text-white tracking-save pointer mr-10 text-center default-buttons' title="Salvar" pps='${ppsId}'style="display:none; height:34px"></a>
                                <div class='tracking-close pointer' data-code='${response.data.tracking_code}' title="Fechar" style="display:none; padding: 0px 7px 0px 9px !important; height:34px">
                                    &#x2715
                                </div>
                            </div>
                    </div>`;

                    $(buttons).insertAfter(col7);
                    $(".tracking-close").click();

                    let statusBadge = btnSave.parent().parent().parent().parent().find(".badge");
                    statusBadge
                        .removeClass(
                            "statusPosted statusOnDelivery statusDelivered statusInTransit statusProblem statusWithoutInfo"
                        )
                        .addClass(statusEnum[tracking.tracking_status_enum])
                        .html(tracking.tracking_status);

                    alertCustom("success", "Código de rastreio salvo com sucesso");
                }
                btnSave.prop("disabled", false).hide();
            },
        });
    });

    //enviar e-mail com o codigo de rastreio
    $(document).on("click", "#modal-tracking-details .btn-notify-trackingcode", function () {
        $.ajax({
            method: "POST",
            url: "/api/tracking/notify/" + tracking_id,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: () => {
                alertCustom("success", "Notificação enviada com sucesso");
            },
        });
    });

    //exportar excel
    $("#btn-export-csv").on("click", function () {
        trackingsExport("csv");
    });

    $("#btn-export-xls").on("click", function () {
        trackingsExport("xlsx");
    });

    function trackingsExport(fileFormat) {
        let data = getFilters();
        data["format"] = fileFormat;
        $.ajax({
            method: "POST",
            url: "/api/tracking/export",
            data: data,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#export-email").text(response.email);
                $("#alert-export").show().shake();
            },
        });
    }

    //importar excel
    $("#btn-import-xls").on("click", function () {
        $("#input-import-xls").click();
    });

    $("#input-import-xls").on("change", function () {
        $("#btn-import-xls").prop("disabled", true);
        let form = new FormData();
        form.append("import.xlsx", this.files[0]);
        $(this).val(null);
        $.ajax({
            url: "/api/tracking/import",
            type: "post",
            processData: false,
            contentType: false,
            cache: false,
            data: form,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                $("#btn-import-xls").prop("disabled", false);
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#btn-import-xls").prop("disabled", false);
                alertCustom(
                    "success",
                    "A importação começou! Você receberá uma notificação quando tudo estiver pronto!"
                );
            },
        });
    });

    $(".btn-light-1").click(function () {
        var collapse = $("#icon-filtro");
        var text = $("#text-filtro");

        text.fadeOut(10);
        if (collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" || collapse.css("transform") == "none") {
            collapse.css("transform", "rotate(180deg)");
            text.text("Minimizar filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }
    });

    $("#filters").on("keypress", function (e) {
        if (e.keyCode == 13) {
            window.loadData();
        }
    });

    //COMPORTAMENTO DO FILTRO MULTIPLO
    function behaviorMultipleFilter(data, selectId) {
        var $select = $("#" + selectId);
        var values = $select.val();

        if ($(`#${selectId}`).val()[0] == "all" || $(`#${selectId}`).val()[0] == "") {
            var valueToRemove = $(`#${selectId}`).val()[0];
        }

        if (data.id != valueToRemove) {
            if (values) {
                var i = values.indexOf(valueToRemove);

                if (i >= 0) {
                    values.splice(i, 1);
                    $select.val(values).change();
                }
            }
        } else {
            if (values) {
                values.splice(0, values.lenght);
                $select.val(null).change();

                values.push(valueToRemove);
                $select.val(valueToRemove).change();
            }
        }
    }

    //NAO PERMITI QUE O FILTRO FIQUE VAZIO
    function deniedEmptyFilter(selectId) {
        let arrayValues = $(`#${selectId}`).val();
        let valueAmount = $(`#${selectId}`).val().length;

        if (valueAmount === 0) {
            if (selectId == "project") {
                arrayValues.push("all");
                arrayValues = $(`#${selectId}`).val("all").trigger("change");
            } else {
                arrayValues.push("");
                arrayValues = $(`#${selectId}`).val("").trigger("change");
            }
        }
    }

    $(".applySelect2").on("select2:select", function (evt) {
        var data = evt.params.data;
        var selectId = $(this).attr("id");
        behaviorMultipleFilter(data, selectId);

        $(`#${selectId}`).focus().scrollTop(0);
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(document).on("focusout", ".select2-selection__rendered", function () {
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(document).on("focusin", ".select2-selection__rendered", function () {
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(".applySelect2").on("change", function () {
        let idTarget = $(this).attr("id");
        deniedEmptyFilter(idTarget);
    });
    // FIM DO COMPORTAMENTO DO FILTRO
});
