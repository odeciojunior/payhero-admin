var currentPage = null;
var atualizar = null;
var exportFormat = null;

$(document).ready(function () {
    //checkbox
    $(".check").on("click", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $(".transaction-value").mask('#.##0,00', {reverse: true}).removeAttr('maxlength');
    $('.transaction-value').on('blur', function () {
        if ($(this).val().length == 1) {
            let val = '0,0'+$(this).val();
            $('.transaction-value').val(val);
        } else if($(this).val().length == 2) {
            let val = '0,'+$(this).val();
            $('.transaction-value').val(val);
        }
    });

    $('#transaction').on('change paste keyup select', function () {
        let val = $(this).val();

        if (val === '') {
            $('#date_type').attr('disabled', false).removeClass('disableFields');
            $('#date_range').attr('disabled', false).removeClass('disableFields');
        } else {
            $('#date_type').attr('disabled', true).addClass('disableFields');
            $('#date_range').attr('disabled', true).addClass('disableFields');
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
        var regexEmail = new RegExp(
            /^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/
        );
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
        atualizar();
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
                Ontem: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este mês": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
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

    function hoverBilletPending() {
        if (verifyAccountFrozen() == true) {
            console.log($("#accountStatus").val());
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
                        $(this).css("background", "#545B62");
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

    function getFilters(urlParams = false) {
        let data = {
            'project': $("#projeto").val(),
            'payment_method': $("#forma").val(),
            'status': $("#status").val(),
            'client': $("#comprador").val(),
            'customer_document': $("#customer_document").val(),
            'date_type': $("#date_type").val(),
            'date_range': $("#date_range").val(),
            'transaction': $("#transaction").val().replace('#', ''),
            'cashback': $("#cashback").val(),
            'plan': $('#plan').val(),
            'coupon': $("#cupom").val(),
            'company': $("#empresa").val(),
            'value': $("#valor").val().replace(/[^\d]+/g, ''),
            'email_client': $("#email_cliente").val(),
            'upsell': $("#upsell").val(),
            'order_bump': $("#order-bump").val(),
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

    // FIM - COMPORTAMENTOS DA JANELA

    getProjects();
    getCompanies();

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
    function getProjects() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/projects?select=true",
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
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel > div >").show();

                    $.each(response.data, function (i, project) {
                        $("#projeto").append(
                            $("<option>", {
                                value: project.id,
                                text: project.name,
                            })
                        );
                    });

                    atualizar();
                } else {
                    $("#export-excel > div >").hide();
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }

                loadingOnScreenRemove();
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
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#cupom").html("");

                if (response.data.length > 0) {
                    $("#cupom").append("<option value=''>Todos cupons</option>");

                    $.each(response.data, function (i, coupon) {
                        $("#cupom").append($('<option>', {
                            value: coupon.id,
                            text: coupon.name
                        }));
                    });

                    atualizar();
                } else {
                    $("#cupom").append("<option value=''>Nenhum cupom encontrado</option>");
                }

                loadingOnScreenRemove();
            }
        });
    }

    // Obtem os campos dos filtros
    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $.each(response.data, function (i, company) {
                        $("#empresa").append($('<option>', {
                            value: company.id,
                            text: company.name
                        }));
                    });

                    atualizar();
                }

                loadingOnScreenRemove();
            }
        });
    }

    // Obtem lista de vendas
    atualizar = function (link = null) {
        currentPage = link;

        let updateResume = true;
        loadOnTable("#dados_tabela", "#tabela_vendas");

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
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#dados_tabela").html("");
                $("#tabela_vendas").addClass("table-striped");

                let statusArray = {
                    1: "success",
                    6: "primary",
                    7: "danger",
                    8: "warning",
                    4: "danger",
                    3: "danger",
                    2: "pendente",
                    12: "success",
                    20: "antifraude",
                    22: "danger",
                    23: "warning",
                    24: "antifraude",
                };

                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, value) {
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

                        let observation = "";
                        if (
                            !isEmpty(value.observation) ||
                            (value.observation === null && false) ||
                            (value.observation === "" && false)
                        ) {
                            observation = `<a data-toggle="tooltip" title="${value.observation}"
                                                role="button" class="sale_observation" venda="${value.id}">
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
                                    <a data-toggle="tooltip" title="Utilizado o cupom ${value.cupom_code}"
                                        role="button" style='margin-left: 5px;' >
                                            <img width="20px" src="/modules/global/img/coupon.svg" title="Cupom">
                                    </a>`;
                        }

                        let upsell = "";
                        if (value.upsell) {
                            upsell = `
                                    <a data-toggle="tooltip" title="Upsell"
                                        role="button" style='margin-left: 5px;' >
                                            <img width="20px" src="/modules/global/img/upsell.svg" title="Upsell">
                                    </a>`;
                        }

                        let has_order_bump = "";
                        if (value.has_order_bump) {
                            has_order_bump = `
                                            <a data-toggle="tooltip" title="Order Bump"
                                                role="button" style='margin-left: 5px;' >
                                                    <img width="20px" src="/modules/global/img/order-bump.svg" title="Order Bump">
                                            </a>`;
                        }

                        let cashback = "";
                        let cashbackIcon = "";
                        if (value.cashback_value != "0.00") {
                            cashbackIcon = `<a data-toggle="tooltip" title="${value.cashback_value}" role="button" style='margin-left: 5px;'>
                                                <span style="color: #5EE2A1; font-size: 26px; -webkit-text-stroke: 2px rgba(94, 226, 161, 0.1);" class="o-reload-1"></span>
                                            </a>`;

                            cashback = `<b style="color: #5EE2A1;">${value.total_paid}</b>`;
                        }

                        dados =
                            `  <tr class='` +
                            tableClass +
                            `'>
                                    <td class='text-center'>
                                        <br class="d-sm-none"/>
                                        ${value.sale_code} <br>
                                        <div class="d-flex flex-row align-items-center justify-content-center">
                                            ${cashbackIcon}
                                            ${upsell}
                                            ${has_order_bump}
                                            ${cupomCode}
                                        </div>
                                    </td>
                                    <td>${value.product}${
                                value.affiliate != null &&
                                value.user_sale_type == "producer"
                                    ? `<br><small>(Afiliado: ${value.affiliate})</small>`
                                    : ""
                            } <br> <small>${value.project}</small></td>
                                    <td class='display-sm-none display-m-none display-lg-none'>${
                                        value.client
                                    }</td>
                                    <td>
                                        <img src='/modules/global/img/cartoes/${
                                            value.brand
                                        }.png'  style='width: 45px'>
                                    </td>
                                    <td>
                                       <div class="text-sm-center text-md-left">
                                            <span class="badge badge-${
                                                statusArray[value.status]
                                            } ${
                                value.status_translate === "Pendente" && value.brand !='pix'
                                    ? "boleto-pending"
                                    : ""
                            }" ${
                                value.status_translate === "Pendente"
                                    ? 'status="' +
                                      value.status_translate +
                                      '" sale="' +
                                      value.id_default +
                                      '"'
                                    : ""
                            }>${value.status_translate}</span>
                                               ${
                                                   value.is_chargeback_recovered &&
                                                   value.status_translate ===
                                                       "Aprovado"
                                                       ? `
                                                <img class="orange-gradient ml-10" width="20px" src="/modules/global/img/svg/chargeback.svg" title="Chargeback recuperado">`
                                                       : ""
                                               }
                                        </div>
                                    </td>
                                    <td class='display-sm-none display-m-none'>${
                                        value.start_date
                                    }</td>
                                    <td class='display-sm-none'>${
                                        value.end_date
                                    }</td>
                                    <td style='white-space: nowrap;' class="text-center text-md-right">
                                        ${
                                            cashback
                                                ? cashback
                                                : `<b>${value.total_paid}</b> <br>`
                                        }
                                    </td>
                                    <td style="text-align: center">
                                        ${observation}
                                        <a role='button' class='detalhes_venda pointer' venda='${
                                            value.id
                                        }'>
                                            <span class="o-eye-1"></span>
                                        </a>
                                    </td>
                                </tr>`;

                        $(function () {
                            $('[data-toggle="tooltip"]').tooltip();
                        });

                        $("#dados_tabela").append(dados);
                    });

                    $("#date").val(
                        moment(new Date()).add(3, "days").format("YYYY-MM-DD")
                    );
                    $("#date").attr(
                        "min",
                        moment(new Date()).format("YYYY-MM-DD")
                    );
                } else {
                    $("#dados_tabela").html(
                        "<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                            $("#dados_tabela").attr("img-empty") +
                            "'>Nenhuma venda encontrada</td></tr>"
                    );
                }
                pagination(response, "sales", atualizar);
                $("#export-excel").show();
            },
        });

        if (updateResume) {
            salesResume();
        }
        hoverBilletPending();
    };

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

    // Resumo
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
                $("#total-sales").html(
                    '<span class="font-size-30 bold">0</span>'
                );
                $("#commission, #total").html(
                    '<span class="font-size-30 bold">R$ 0,00</span>'
                );

                if (response.total_sales) {
                    $("#total-sales, #commission, #total").text("");
                    $("#total-sales").html(
                        `<span class="font-size-30 bold"> ${response.total_sales} </span>`
                    );
                    $("#commission").html(
                        `R$ <span class="font-size-30 bold"> ${response.commission} </span>`
                    );
                    $("#total").html(
                        `R$ <span class="font-size-30 bold"> ${response.total} </span>`
                    );
                }
            },
        });
    }
    $("#projeto").on("change", function () {
        let value = $(this).val();
        $("#plan").val(null).trigger("change");
    });
    //Search plan
    $("#plan").select2({
        placeholder: "Nome do plano",
        // multiple: true,
        allowClear: true,
        dropdownParent: $(".align-items-baseline"),
        language: {
            noResults: function () {
                return "Nenhum plano encontrado";
            },
            searching: function () {
                return "Procurando...";
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
                return {
                    results: $.map(res.data, function (obj) {
                        return {
                            id: obj.id,
                            text:
                                obj.name +
                                (obj.description
                                    ? " - " + obj.description
                                    : ""),
                        };
                    }),
                };
            },
        },
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

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });
});
