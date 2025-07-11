var currentPage = null;
var exportFormat = null;

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

function loadData() {
    elementButton = $("#bt_filtro");
    if (searchIsLocked(elementButton) != "true") {
        lockSearch(elementButton);
        atualizar();
    }
}

function atualizar(link = null) {
    currentPage = link;

    let updateResume = true;
    loadOnTable("#dados_tabela", "#tabela_vendas");
    $("#pagination-sales").children().attr("disabled", "disabled");

    if (link == null) {
        link = "/api/sales?" + getFilters(true).substr(1);
    } else {
        link = "/api/sales" + link + getFilters(true);
        updateResume = false;
    }

    $.ajax({
        method: "GET",
        url: link,
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            loadingOnScreenRemove();
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $("#dados_tabela").html("");
            $("#tabela_vendas").addClass("table-striped");

            let statusArray = {
                1: "success",
                6: "primary",
                7: "disable",
                8: "warning",
                4: "danger",
                3: "danger",
                2: "pendente",
                12: "success",
                20: "antifraude",
                21: "danger",
                22: "disable",
                23: "warning",
                24: "antifraude",
            };

            if (!isEmpty(response.data)) {
                $("#export-excel").show();

                $.each(response.data, function (index, value) {
                    let start_date = "";

                    if (value.start_date) {
                        start_date = value.start_date.split(/\s/g); //data inicial

                        start_date = `
                            <span>
                                ${start_date[0]}
                            </span>
                            <br>
                            <small class="subdescription">
                                ${start_date[1]}
                            </small>`;
                    }

                    let end_date = "";

                    if (value.end_date) {
                        end_date = value.end_date.split(/\s/g); //data final

                        end_date = `
                            <span>
                                ${end_date[0]}
                            </span>
                            <br>
                            <small class="subdescription">
                                ${end_date[1]}
                            </small>`;
                    }

                    let tableClass = "";

                    if (
                        value.has_shopify_integration != null &&
                        value.shopify_order == null &&
                        value.status != 20 &&
                        value.date_before_five_minutes_ago
                    ) {
                        tableClass = "table-warning-roll";
                    } else {
                        tableClass = "";
                    }

                    if (value.woocommerce_retry_order != null) {
                        tableClass = "table-warning-roll";
                    } else {
                        tableClass = "";
                    }

                    let observation = "";

                    if (
                        !isEmpty(value.observation) ||
                        (value.observation === null && false) ||
                        (value.observation === "" && false)
                    ) {
                        observation = `
                            <a data-toggle="tooltip" title="${value.observation}" role="button" class="sale_observation" venda="${value.id}">
                                <span style="color: #44a44b" class="o-info-help-1"></span>
                            </a>`;
                    }

                    let cupomCode = "";

                    if (
                        !isEmpty(value.cupom_code) ||
                        (value.cupom_code === null && false) ||
                        (value.cupom_code === "" && false)
                    ) {
                        cupomCode = `
                            <a class="icon-transaction" data-toggle="tooltip" data-placement="top" title="Utilizado o cupom ${value.cupom_code}" role="button" style='margin-left: 5px;'>
                                <img width="25px" src="/build/global/img/icon-cupom-discout.svg">
                            </a>`;
                    }

                    let upsell = "";

                    if (value.upsell) {
                        upsell = `
                            <a class="icon-transaction" data-toggle="tooltip" data-placement="top" title="Upsell" role="button" style='margin-left: 5px;'>
                                <img width="20px" src="/build/global/img/icon-upsell.svg">
                            </a>`;
                    }

                    let has_order_bump = "";
                    if (value.has_order_bump) {
                        has_order_bump = `
                            <a class="icon-transaction" data-toggle="tooltip" data-placement="top" title="Order bump" role="button" style='margin-left: 5px;'>
                                <img id="order-bump" width="20px" src="/build/global/img/order-bump-icon-new.svg">
                            </a>`;
                    }

                    // disabled cashback
                    // let cashback = "";
                    // let cashbackIcon = "";
                    // if (value.cashback_value != "0.00") {
                    //     cashbackIcon = `
                    //         <a class="icon-transaction" data-toggle="tooltip" data-placement="top" title="${value.cashback_value}" role="button" style='margin-left: 5px;'>
                    //             <img width="27px" src="/build/global/img/icons-cashback.svg">
                    //         </a>`
                    //         ;

                    //     cashback = `<b style="color: #636363;">${value.total_paid}</b>`;
                    // }

                    if (value.status_translate === "Cancelado Antifraude") {
                        value.status_translate = "Cancelado <br> Antifraude";
                    }

                    dados = `
                        <tr class='${tableClass}'>

                            <td class='text-center'>

                                <br class="d-sm-none"/>
                                ${value.sale_code}
                                <br>

                                <div class="d-flex flex-row align-items-center justify-content-center">
                                    ${upsell}
                                    ${has_order_bump}
                                    ${cupomCode}
                                </div>
                            </td>

                            <td>
                                <div class="m-auto fullInformation-sales ellipsis-text">
                                    ${value.product}
                                </div>

                                ${
                                    value.affiliate != null && value.user_sale_type == "producer"
                                        ? ` <br> <small class="subdescription font-size-12"> (Afiliado: ${value.affiliate}) </small>`
                                        : ""
                                }

                                <div class="m-auto fullInformation-sales subdescription ellipsis-text">
                                    ${value.project}
                                </div>

                                <div class="container-tooltips-sales"></div>
                            </td>

                            <td class='d-none client-collumn'>

                                <div class="fullInformation-sales ellipsis-text">
                                    ${value.client}
                                </div>

                            </td>

                            <td>
                                <img src='/build/global/img/cartoes/${
                                    value.brand
                                }.png'  style='width: 55px; height: 36px;'>
                            </td>

                            <td class='text-center'>
                                <span class="status-sale badge badge-${statusArray[value.status]}
                                    ${
                                        value.status_translate === "Pendente" && value.brand != "pix"
                                            ? "boleto-pending"
                                            : ""
                                    }"
                                    ${
                                        value.status_translate === "Pendente"
                                            ? 'status="' + value.status_translate + '" sale="' + value.id_default + '"'
                                            : ""
                                    }>
                                    ${value.status_translate}
                                </span>

                            </td>

                            <td class='display-sm-none display-m-none text-left'>
                                ${start_date}
                            </td>

                            <td class='display-sm-none text-left'>
                                ${end_date}
                            </td>

                            <td class="text-center text-nowrap commission-fweight">
                                <b>${value.total_paid}<b><br>
                            </td>

                            <td style="text-align: center">
                                ${observation}
                                <a role='button' class='detalhes_venda pointer' venda='${value.id}'>
                                    <span>
                                        <img src="/build/global/img/icon-eye.svg">
                                    </span>
                                </a>
                            </td>

                        </tr>`;

                    $("#dados_tabela").append(dados);
                });

                $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                $("#date").attr("min", moment(new Date()).format("YYYY-MM-DD"));
                $("#container-pagination").show();

                $(".fullInformation-sales").bind("mouseover", function () {
                    var $this = $(this);

                    if (this.offsetWidth < this.scrollWidth && !$this.attr("title")) {
                        $this
                            .attr({
                                "data-toggle": "tooltip",
                                "data-placement": "top",
                                "data-title": $this.text(),
                            })
                            .tooltip({ container: ".container-tooltips-sales" });
                        $this.tooltip("show");
                    }
                });

                $(".icon-transaction").tooltip({ container: ".container-tooltips-sales" });
            } else {
                $("#dados_tabela").html(
                    "<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                        $("#dados_tabela").attr("img-empty") +
                        "'>Nenhuma venda encontrada</td></tr>"
                );
                $("#export-excel").hide();
            }
            pagination(response, "sales", atualizar);
            loadingOnScreenRemove();
        },
        complete: (response) => {
            unlockSearch($("#bt_filtro"));
        },
    });

    if (updateResume) {
        salesResume();
    }
    hoverBilletPending();
}

function getFilters(urlParams = false) {
    let transaction = $("#transaction").val().replace("#", "");
    let date_range = $("#date_range").val();
    if (transaction.length > 0) {
        date_range = moment("2018-01-01").format("DD/MM/YYYY") + " - " + moment().format("DD/MM/YYYY");
    }

    let data = {
        project: $("#projeto").val(),
        payment_method: $("#forma").val(),
        status: $("#status").val(),
        client: $("#comprador").val(),
        customer_document: $("#customer_document").val(),
        date_type: $("#date_type").val(),
        date_range: date_range,
        transaction: transaction,
        plan: $("#plan").val(),
        coupon: $("#cupom").val(),
        company: $(".company-navbar").val(),
        value:
            parseInt(
                $("#valor")
                    .val()
                    .replace(/[^\d]+/g, "")
            ) > 0
                ? $("#valor")
                      .val()
                      .replace(/[^\d]+/g, "")
                : "",
        email_client: $("#email_cliente").val(),
        upsell: $("#upsell").val(),
        order_bump: $("#order-bump").val(),
    };

    Object.keys(data).forEach((value) => {
        if (Array.isArray(data[value])) {
            data[value] = data[value].filter((value) => value).join(",");
        }
    });

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

function salesResume() {
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

    $.ajax({
        method: "GET",
        url: "/api/sales/resume",
        data: getFilters(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            loadOnAny(".number", true);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            loadOnAny(".number", true);
            $("#total-sales").html('<span class="font-size-30 bold">0</span>');
            $("#commission, #total").html(
                '<span style="color:#959595">R$</span> <span class="font-size-30 bold">0,00</span>'
            );

            if (response.total_sales) {
                $("#total-sales, #commission, #total").text("");
                let quant = response.total_sales;
                $("#total-sales").html(`<span class="font-size-30 bold"> ${quant.toLocaleString("pt-BR")} </span>`);

                let font_commission_style = "";
                if (`${response.commission}`.length == 12) {
                    font_commission_style = "font-size: 27px !important;";
                } else if (`${response.commission}`.length > 12) {
                    font_commission_style = "font-size: 25px !important;";
                }
                $("#commission").html(
                    `<span style="color:#959595">R$</span> <span class="font-size-30 bold" style="
                        ${font_commission_style} "> ${response.commission} </span>`
                );

                let font_total_style = "";
                if (`${response.total}`.length == 12) {
                    font_total_style = "font-size: 27px !important;";
                } else if (`${response.total}`.length > 12) {
                    font_total_style = "font-size: 25px !important;";
                }
                $("#total").html(
                    `<span style="color:#959595">R$</span> <span class="font-size-30 bold" style="
                        ${font_total_style} "> ${response.total} </span>`
                );
            }
        },
    });
}

function hoverBilletPending() {
    if (verifyAccountFrozen() == true) {
        $(document).on(
            {
                mouseenter: function () {
                    var status = $(this).attr("status");
                    $(this).removeAttr("style");
                    $(this).text(status);
                },
                mouseleave: function () {
                    var status = $(this).attr("status");
                    $(this).removeAttr("style");
                    $(this).text(status);
                },
            },
            ".boleto-pending"
        );
    } else {
        $(document).on(
            {
                mouseenter: function () {
                    $(this).css("cursor", "pointer").text("Regerar");
                    $(this).css("background", "#3D4456");
                },
                mouseleave: function () {
                    var status = $(this).attr("status");
                    $(this).removeAttr("style");
                    $(this).text(status);
                },
            },
            ".boleto-pending"
        );
    }
}

$(document).ready(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#projeto").find("option").not(":first").remove();
        $("#plan").find("option").not(":first").remove();
        $("#plan").data("select2").results.clear();
        $("#projeto").val($("#projeto option:first").val());
        $("#plan").val($("#plan option:first").val());
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
        loadOnTable("#dados_tabela", "#tabela_vendas");
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                getProjects(data2, "company-navbar");
            });
        });
    });

    //APLICANDO FILTRO MULTIPLO EM ELEMENTOS COM A CLASS (applySelect2)
    $(".applySelect2").select2({
        //dropdownParent : $('#bt_collapse'),
        width: "100%",
        multiple: true,
        language: {
            noResults: function () {
                return "Nenhum resultado encontrado";
            },
        },
    });

    //checkbox
    $(".check").on("click", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $("#valor").maskMoney({
        thousands: ".",
        decimal: ",",
        allowZero: true,
        prefix: "",
    });

    $(".transaction-value").mask("#.##0,00", { reverse: true }).removeAttr("maxlength");
    $(".transaction-value").on("blur", function () {
        if ($(this).val().length == 1) {
            let val = "0,0" + $(this).val();
            $(".transaction-value").val(val);
        } else if ($(this).val().length == 2) {
            let val = "0," + $(this).val();
            $(".transaction-value").val(val);
        }
    });

    $("#transaction").on("change paste keyup select", function () {
        let val = $(this).val();

        if (val === "") {
            $("#date_type").attr("disabled", false).removeClass("disableFields");
            $("#date_range").attr("disabled", false).removeClass("disableFields");
        } else {
            $("#date_range").val(moment("2018-01-01").format("DD/MM/YYYY") + " - " + moment().format("DD/MM/YYYY"));
            $("#date_type").attr("disabled", true).addClass("disableFields");
            $("#date_range").attr("disabled", true).addClass("disableFields");
        }
    });

    // COMPORTAMENTOS DA JANELA
    $("#bt_get_csv").on("click", function () {
        $("#modal-export-sale").modal("show");
        exportFormat = "csv";
    });

    $("#bt_get_xls").on("click", function () {
        $("#modal-export-sale").modal("show");
        exportFormat = "xlsx";
    });

    $(".btn-confirm-export-sale").on("click", function () {
        var regexEmail = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
        var email = $("#email_export").val();

        if (email == "" || !regexEmail.test(email)) {
            alertCustom("error", "Preencha o email corretamente");
            return false;
        } else {
            salesExport(exportFormat);
            $("#modal-export-sale").modal("hide");
        }
    });

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        $("#container-pagination").hide();
        //atualizar();
        loadData();
    });

    let startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    let endDate = moment().format("YYYY-MM-DD");
    $("#date_range").daterangepicker(
        {
            startDate: moment().subtract(30, "days"),
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
                Vitalício: [moment("2018-01-01 00:00:00"), moment()],
            },
        },
        function (start, end) {
            startDate = start.format("YYYY-MM-DD");
            endDate = end.format("YYYY-MM-DD");
        }
    );

    // FIM - COMPORTAMENTOS DA JANELA
    getCompaniesAndProjects().done(function (data) {
        getProjects(data);
    });

    function loadData() {
        elementButton = $("#bt_filtro");
        if (searchIsLocked(elementButton) != "true") {
            lockSearch(elementButton);
            atualizar();
        }
    }

    function searchIsLocked(elementButton) {
        return elementButton.attr("block_search");
    }

    //Carrega o modal para regerar boleto
    $(document).on("click", ".boleto-pending", function () {
        if (verifyAccountFrozen() == false) {
            let saleId = $(this).attr("sale");
            $("#modal_regerar_boleto #bt_send").attr("sale", saleId);

            $("#modal_regerar_boleto").modal("show");
        }
    });

    //Salvar boleto regerado
    $("#bt_send").on("click", function () {
        loadingOnScreen();
        let saleId = $(this).attr("sale");
        $.ajax({
            method: "POST",
            url: "/api/recovery/regenerateboleto",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                saleId: saleId,
                date: $("#date").val(),
                discountType: $("#discount_type").val(),
                discountValue: $("#discount_value").val(),
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                $("#modal_regerar_boleto").modal("hide");
                atualizar(currentPage);
            },
        });
    });

    // Obtem o os campos dos filtros
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
                if (origin == "") loadingOnScreen();
            },
            success: function success(response) {
                if (!isEmpty(response)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel > div >").show();
                    $.each(response, function (i, project) {
                        $("#projeto").append($("<option>", { value: project.project_id, text: project.name }));
                    });
                    $("#projeto option:first").attr("selected", "selected");
                    atualizar();
                    if (origin == "") loadingOnScreenRemove();
                } else {
                    if (!isEmpty(data.company_default_projects)) {
                        $("#project-empty").hide();
                        $("#project-not-empty").show();
                        $("#export-excel > div >").show();
                        // $.each(data.company_default_projects, function (i, project) {
                        //     $("#projeto").append($("<option>", {value: project.project_id,text: project.name,}));
                        // });
                        $("#projeto option:first").attr("selected", "selected");
                        atualizar();
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

    // Obtem os campos dos filtros
    function getCoupons() {
        var projectId = $("#projeto").val();
        projectId = projectId != "" ? projectId : null;

        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/couponsdiscounts?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#cupom").html("");

                if (response.data.length > 0) {
                    $("#cupom").append("<option value=''>Todos cupons</option>");

                    $.each(response.data, function (i, coupon) {
                        $("#cupom").append(
                            $("<option>", {
                                value: coupon.id,
                                text: coupon.name,
                            })
                        );
                    });

                    atualizar();
                } else {
                    $("#cupom").append("<option value=''>Nenhum cupom encontrado</option>");
                }

                loadingOnScreenRemove();
            },
        });
    }

    // Obtem os campos dos filtros
    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/core/companies?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $.each(response.data, function (i, company) {
                        $("#empresa").append(
                            $("<option>", {
                                value: company.id,
                                text: company.name,
                            })
                        );
                    });

                    atualizar();
                }

                loadingOnScreenRemove();
            },
        });
    }

    // Download do relatorio
    function salesExport(fileFormat) {
        let data = getFilters();
        data["format"] = fileFormat;
        data["email"] = $("#email_export").val();
        $.ajax({
            method: "POST",
            url: "/api/sales/export",
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

    //COMPORTAMENTO DO FILTRO MULTIPLO
    function behaviorMultipleFilter(data, selectId) {
        var $select = $(`#${selectId}`);
        var valueToRemove = "";
        var values = $select.val();

        if (data.id != "") {
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

                values.push("");
                $select.val("").change();
            }
        }
    }

    //NAO PERMITI QUE O FILTRO FIQUE VAZIO
    function deniedEmptyFilter(selectId) {
        let arrayValues = $(`#${selectId}`).val();
        let valueAmount = $(`#${selectId}`).val().length;

        if (valueAmount === 0) {
            arrayValues.push("");
            arrayValues = $(`#${selectId}`).val("").trigger("change");
        }
    }

    $(".applySelect2").on("select2:select", function (evt) {
        var data = evt.params.data;
        var selectId = $(this).attr("id");
        behaviorMultipleFilter(data, selectId);

        $(`#${selectId}`).focus();
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(".applySelect2").on("change", function () {
        let idTarget = $(this).attr("id");
        deniedEmptyFilter(idTarget);
    });

    $(document).on("focusout", ".select2-selection__rendered", function () {
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(document).on("focusin", ".select2-selection__rendered", function () {
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    // FIM DO COMPORTAMENTO DO FILTRO

    //LISTA PLANOS DE ACORDO COM O PROJETO(S)
    $("#projeto").on("change", function () {
        let value = $(this).val();
        $("#plan").val(null).trigger("change");
        $("#plan").data("select2").results.clear();
    });

    $("#plan").select2({
        language: {
            noResults: function () {
                return "Nenhum plano encontrado";
            },
            searching: function () {
                return "Procurando...";
            },
            errorLoading: function () {
                return "Os resultados não puderam ser carregados";
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: "plan",
                    search: params.term,
                    project_id: $("#projeto").val(),
                };
            },
            method: "GET",
            url: "/api/sales/user-plans",
            delay: 300,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processResults: function (res) {
                result = $.map(res.data, function (obj) {
                    return {
                        id: obj.id,
                        text: obj.name + (obj.description ? " - " + obj.description : ""),
                    };
                });

                if (res.data.length > 0) {
                    result.splice(0, 0, {
                        id: "",
                        text: "Todos os Planos",
                    });
                }

                return {
                    results: result,
                };
            },
        },
    });

    $(".btn-light-1").on("click", function () {
        var collapse = $("#icon-filtro");
        var text = $("#text-filtro");
        let remove;

        text.fadeOut(10);
        if (collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" || collapse.css("transform") == "none") {
            collapse.css("transform", "rotate(180deg)");
            text.html("Minimizar <br class='d-flex d-sm-none'> filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }
    });

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });

    $(".company_name").val($(".company-navbar").find("option:selected").text());
});
