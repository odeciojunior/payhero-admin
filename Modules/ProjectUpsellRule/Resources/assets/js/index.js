$(document).ready(function () {
    let projectId = $(window.location.pathname.split("/")).get(-1);
    let countdownInterval = null;

    $(".value-mask").maskMoney({ thousands: ".", decimal: ",", allowZero: true, prefix: "" });

    function formatDouble(number) {
        return number.replace(".", "").replace(",", ".");
    }
    function formatMoney(number) {
        return (Math.round(number * 100) / 100).toFixed(2).replace(".", ",");
    }
    //store type
    $("#us_type_value").click(function () {
        $("#us_percent_opt").hide();
        $("#us_money_opt").show();
        $("#us_money_opt input").focus();
        $("#add_discount_upsell").val(formatDouble($("#us_money_opt input").val()));
    });

    $("#us_type_percent").click(function () {
        $("#us_money_opt").hide();
        $("#us_percent_opt").show();
        $("#us_percent_opt input").focus();
        $("#add_discount_upsell").val($("#us_percent_opt input").val());
    });
    $("#us_money_opt input").change(function () {
        $("#add_discount_upsell").val(formatDouble($("#us_money_opt input").val()));
        // .replace('.','').replace(',','.')
    });
    $("#us_percent_opt input").change(function () {
        $("#add_discount_upsell").val($("#us_percent_opt input").val());
    });

    //update type
    $("#usu_type_value").click(function () {
        $("#usu_percent_opt").hide();
        $("#usu_money_opt").show();
        $("#usu_money_opt input").focus();
        $("#edit_discount_upsell").val(formatDouble($("#usu_money_opt input").val()));
    });

    $("#usu_type_percent").click(function () {
        $("#usu_money_opt").hide();
        $("#usu_percent_opt").show();
        $("#usu_percent_opt input").focus();
        $("#edit_discount_upsell").val($("#usu_percent_opt input").val());
    });
    $("#usu_money_opt input").change(function () {
        $("#edit_discount_upsell").val(formatDouble($("#usu_money_opt input").val()));
        // .replace('.','').replace(',','.')
    });
    $("#usu_percent_opt input").change(function () {
        $("#edit_discount_upsell").val($("#usu_percent_opt input").val());
    });

    $(".tab_upsell").on("click", function () {
        loadUpsell();
        $(this).off();
    });

    function loadUpsell() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = "/api/projectupsellrule";
        } else {
            link = "/api/projectupsellrule" + link;
        }

        loadOnTable("#data-table-upsell", "#table-upsell");
        $("#pagination-upsell").children().attr("disabled", "disabled");

        $("#tab_upsell-panel").find(".no-gutters").css("display", "none");
        $("#table-upsell").find("thead").css("display", "none");

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            data: { project_id: projectId },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let dataTable = $("#data-table-upsell");
                dataTable.html("");
                if (response.data == "") {
                    $("#pagination-container-upsell").addClass("d-none").removeClass("d-flex")
                    pagination(response, "upsell", loadUpsell);
                    $(".div-config").hide();
                    $("#data-table-upsell").html(`
                        <tr class='text-center'>
                            <td colspan='3' style='height: 70px;vertical-align: middle'>
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum upsell configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro upsell para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-upsell' data-toggle="modal" data-target="#modal_add_upsell">Adicionar upsell</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                    $("#table-upsell").addClass("table-striped");

                } else {
                    $("#tab_upsell-panel").find(".no-gutters").css("display", "flex");
                    $("#table-upsell").find("thead").css("display", "contents");
                    $("#pagination-container-upsell").addClass("d-flex").removeClass("d-none")


                    $("#table-upsell").addClass("table-striped");
                    $("#count-upsell").html(response.meta.total);
                    let data = "";

                    $.each(response.data, function (index, value) {
                        data += `
                            <tr>

                                <td>

                                    <div class="fullInformation-upsel ellipsis-text">
                                        ${value.description}
                                    </div>

                                    <div class="container-tooltips-upsel"></div>

                                </td>


                                <td class="text-center">${value.active_flag
                                ? `<span class="badge badge-success">Ativo</span>`
                                : `<span class="badge badge-disable">Desativado</span>`}
                                </td>

                                <td style='text-align:center'>

                                    <div class='d-flex justify-content-end align-items-center'>

                                        <a role='button' title='Visualizar' class='mg-responsive details-upsell pointer' data-upsell="${value.id}">

                                            <span class="">
                                                <img src='/build/global/img/icon-eye.svg'/>
                                            </span>

                                        </a>

                                        <a role='button' title='Editar' class='pointer edit-upsell mg-responsive' data-upsell="${value.id}">

                                            <span class=''>
                                                <img src='/build/global/img/pencil-icon.svg'/>
                                            </span>

                                        </a>

                                        <a role='button' title='Excluir' class='pointer delete-upsell mg-responsive' data-upsell="${value.id}" data-toggle="modal" data-target="#modal-delete-upsell">

                                            <span class=''>
                                                <img src='/build/global/img/icon-trash-tale.svg'/>
                                            </span>

                                        </a>

                                    </div>

                                </td>

                            </tr>
                        `;
                    });
                    dataTable.append(data);
                    $(".div-config").show();

                    $('.fullInformation-upsel').bind('mouseover', function () {
                        var $this = $(this);

                        if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
                            $this.attr({
                                'data-toggle': "tooltip",
                                'data-placement': "top",
                                'data-title': $this.text()
                            }).tooltip({ container: ".container-tooltips-upsel" })
                            $this.tooltip("show")
                        }
                    });


                    pagination(response, "upsell", loadUpsell);
                }
            },
        });

        if (!["shopify", "woocommerce"].includes($("#project_type").val())) {
            $(".use-variants-upsell").prop("checked", false).val(0).closest(".switch-holder").hide();
        }
    }

    $(document).on("click", ".add-upsell", function () {
        $("#modal_add_upsell .modal-title").html("Novo upsell");
        $(".bt-upsell-save").show();
        $(".bt-upsell-update").hide();
        $("#form_add_upsell").show();
        $("#form_edit_upsell").hide();
        $("#add_active_flag").prop("checked", true).trigger("change");
        $("#modal_add_upsell .use-variants-upsell").prop("checked", true).trigger("change");
        $("#add_description_upsell").val("");
        $("#add_discount_upsell").val("");
        $("#us_money_opt input").val("");
        $("#us_percent_opt input").val("");
    });

    $(document).on("click", ".edit-upsell", function (event) {
        event.preventDefault();
        let upsellId = $(this).data("upsell");
        $("#modal_add_upsell .modal-title").html("Editar upsell");
        $(".bt-upsell-save").hide();
        $(".bt-upsell-update").show();
        $("#form_add_upsell").hide();
        $("#form_edit_upsell").show();
        $("#edit_description_upsell").val("");
        $("#edit_discount_upsell").val("");
        $("#form_edit_upsell .upsell-id").val(upsellId);

        $.ajax({
            method: "GET",
            url: "/api/projectupsellrule/" + upsellId + "/edit",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                let upsell = response.data;
                $("#edit_description_upsell").val(`${upsell.description}`);

                if (upsell.type == 1) {
                    $("#usu_type_value").trigger("click");
                    $("#usu_money_opt input").val(formatMoney(upsell.discount));
                } else {
                    $("#usu_percent_opt input").val(upsell.discount);
                    $("#usu_type_percent").trigger("click");
                }
                $("#edit_discount_upsell").val(`${upsell.discount}`);

                $("#edit_active_flag")
                    .prop("checked", upsell.active_flag === 1)
                    .val(upsell.active_flag);
                $("#form_edit_upsell .use-variants-upsell")
                    .prop("checked", upsell.use_variants === 1)
                    .val(upsell.use_variants)
                    .trigger("change");

                // Seleciona a opção do select de acordo com o que vem do banco
                $("#edit_apply_on_shipping, #edit_apply_on_plans, #edit_offer_on_plans").html("");

                let applyOnShipping = [];
                for (let shipping of upsell.apply_on_shipping) {
                    applyOnShipping.push(shipping.id);
                    $("#edit_apply_on_shipping").append(
                        `<option value="${shipping.id}">${shipping.name + (shipping.information ? " - " + shipping.information : "")
                        }</option>`
                    );
                }
                $("#edit_apply_on_shipping").val(applyOnShipping);

                let applyOnPlans = [];
                for (let plan of upsell.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    $("#edit_apply_on_plans").append(
                        `<option value="${plan.id}">${plan.name + (plan.description ? " - " + plan.description : "")
                        }</option>`
                    );
                }
                $("#edit_apply_on_plans").val(applyOnPlans);

                let offerOnPlans = [];
                for (let plan of upsell.offer_on_plans) {
                    offerOnPlans.push(plan.id);
                    $("#edit_offer_on_plans").append(
                        `<option value="${plan.id}">${plan.name + (plan.description ? " - " + plan.description : "")
                        }</option>`
                    );
                }
                $("#edit_offer_on_plans").val(offerOnPlans);

                setShippingSelect2("#edit_apply_on_shipping");
                setPlanSelect2("#edit_apply_on_plans");
                setPlanSelect2("#edit_offer_on_plans");

                $("#modal_add_upsell").modal("show");
                // END
            },
        });
    });

    $(document).on("click", ".bt-upsell-save", function () {
        var form_data = new FormData(document.getElementById("form_add_upsell"));
        form_data.append("project_id", projectId);

        $(".bt-upsell-save").attr("disabled", true);

        $.ajax({
            method: "POST",
            url: "/api/projectupsellrule",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function error(response) {
                errorAjaxResponse(response);
                $(".bt-upsell-save").attr("disabled", false);
            },
            success: function success(response) {
                $("#modal_add_upsell").modal("hide");
                loadUpsell();
                alertCustom("success", response.message);
                $("#add_apply_on_plans, #add_offer_on_plans").val(null).trigger("change");
                $(".bt-upsell-save").attr("disabled", false);
            },
        });
    });

    $(document).on("click", ".delete-upsell", function (event) {
        event.preventDefault();

        let upsellId = $(this).data("upsell");

        $("#btn-delete-upsell").unbind("click");
        $("#btn-delete-upsell").on("click", function () {
            $.ajax({
                method: "DELETE",
                url: "/api/projectupsellrule/" + upsellId,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    loadUpsell();
                    alertCustom("success", response.message);
                },
            });
        });
    });

    $(document).on("click", ".bt-upsell-update", function (event) {
        event.preventDefault();

        var form_data = new FormData(document.getElementById("form_edit_upsell"));
        let upsellId = $("#form_edit_upsell .upsell-id").val();
        $.ajax({
            method: "POST",
            url: "/api/projectupsellrule/" + upsellId,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal_add_upsell").modal("hide");
                loadUpsell();
                alertCustom("success", response.message);
            },
        });
    });
    $(document).on("click", ".details-upsell", function (event) {
        event.preventDefault();
        let upsellId = $(this).data("upsell");
        $.ajax({
            method: "GET",
            url: "/api/projectupsellrule/" + upsellId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let upsell = response.data;
                $(".upsell-description").html("");
                $(".upsell-discount").html("");
                $(".upsell-status").html("");
                $(".upsell-apply-shipping").html("");
                $(".upsell-apply-plans").html("");
                $(".upsell-offer-plans").html("");
                $(".upsell-description").html(`${upsell.description}`);
                $(".upsell-discount").html(`${upsell.discount != 0 ? `${upsell.discount}` : `Valor sem desconto`}`);
                if (upsell.discount != 0) {
                    if (upsell.type == 1) {
                        // $(".upsell-discount").html(formatMoney(upsell.discount));
                        $(".upsell-discount").html(
                            upsell.discount.toLocaleString("pt-br", { minimumFractionDigits: 2 })
                        );

                        $(".upsell-discount").prepend("R$");
                    } else {
                        $(".upsell-discount").append("%");
                    }
                }

                $(".upsell-status").html(
                    `${upsell.active_flag
                        ? `<span class="badge badge-success text-left">Ativo</span>`
                        : `<span class="badge badge-danger">Desativado</span>`
                    }`
                );
                for (let applyShipping of upsell.apply_on_shipping) {
                    $(".upsell-apply-shipping").append(`<span>${applyShipping.name}</span><br>`);
                }
                for (let applyPlan of upsell.apply_on_plans) {
                    $(".upsell-apply-plans").append(`<span>${applyPlan.name}</span><br>`);
                }
                for (let offerPlan of upsell.offer_on_plans) {
                    $(".upsell-offer-plans").append(`<span>${offerPlan.name}</span><br>`);
                }
                $("#modal-detail-upsell").modal("show");
            },
        });
    });

    //Search shipping
    function setShippingSelect2(element) {
        const $element = typeof element === "string" ? $(element) : element;

        let configs = {
            placeholder: "Nome do frete",
            multiple: true,
            dropdownParent: $("#modal_add_upsell"),
            language: {
                noResults: function () {
                    return "Nenhum frete encontrado";
                },
                searching: function () {
                    return "Procurando...";
                },
                loadingMore: function () {
                    return "Carregando mais fretes...";
                },
            },
            ajax: {
                data: function (params) {
                    return {
                        list: "shipping",
                        search: params.term,
                        project_id: projectId,
                        page: params.page || 1,
                    };
                },
                method: "GET",
                url: "/api/shippings/user-shippings",
                delay: 300,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                processResults: function (res) {
                    if (res.meta.current_page === 1) {
                        let allObject = {
                            id: "all",
                            name: `Qualquer frete`,
                            information: "",
                        };
                        res.data.unshift(allObject);
                    }

                    return {
                        results: $.map(res.data, function (obj) {
                            return { id: obj.id, text: obj.name + (obj.information ? " - " + obj.information : "") };
                        }),
                        pagination: {
                            more: res.meta.current_page !== res.meta.last_page,
                        },
                    };
                },
            },
        };
        $element.select2(configs);
    }

    setShippingSelect2("#add_apply_on_shipping");
    setShippingSelect2("#edit_apply_on_shipping");
    $("#add_apply_on_shipping").html(`<option value="all">Qualquer frete</option>`).val("all");

    //Search plan
    function setPlanSelect2(element) {
        const $element = typeof element === "string" ? $(element) : element;

        const useVariants = $element.closest("form").find(".use-variants-upsell").prop("checked") ? 1 : 0;
        const targetName = useVariants ? "plano" : "produto";

        let configs = {
            placeholder: `Nome do ${targetName}`,
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $("#modal_add_upsell"),
            language: {
                noResults: function () {
                    return `Nenhum ${targetName} encontrado`;
                },
                searching: function () {
                    return "Procurando...";
                },
                loadingMore: function () {
                    return `Carregando mais ${targetName}s...`;
                },
            },
            ajax: {
                data: function (params) {
                    return {
                        list: "plan",
                        search: params.term,
                        project_id: projectId,
                        page: params.page || 1,
                        variants: useVariants,
                    };
                },
                method: "GET",
                url: "/api/plans/user-plans",
                delay: 300,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                processResults: function (res) {
                    let elemId = this.$element.attr("id");
                    if (["add_apply_on_plans", "edit_apply_on_plans"].includes(elemId) && res.meta.current_page === 1) {
                        let allObject = {
                            id: "all",
                            name: `Qualquer ${targetName}`,
                            description: "",
                        };
                        res.data.unshift(allObject);
                    }
                    return {
                        results: $.map(res.data, function (obj) {
                            return { id: obj.id, text: obj.name + (obj.description ? " - " + obj.description : "") };
                        }),
                        pagination: {
                            more: res.meta.current_page !== res.meta.last_page,
                        },
                    };
                },
            },
        };

        $element.select2(configs);
    }

    setPlanSelect2("#add_apply_on_plans");
    setPlanSelect2("#edit_apply_on_plans");
    setPlanSelect2("#add_offer_on_plans");
    setPlanSelect2("#edit_offer_on_plans");

    $(".use-variants-upsell")
        .on("change", function () {
            const slider = $(this);
            const form = slider.closest("form");

            const applyContainer = form.find(".apply-on-plan-container");
            const offerContainer = form.find(".offer-plan-container");

            const applyLabel = applyContainer.find("label");
            const offerLabel = offerContainer.find("label");

            const applySelect = applyContainer.find("select");
            const offerSelect = offerContainer.find("select");

            if (slider.prop("checked")) {
                applyLabel.text("Ao comprar os planos:");
                offerLabel.text("Oferecer os planos:");
                applySelect.html(`<option value="all">Qualquer plano</option>`).val("all").trigger("change");
            } else {
                applyLabel.text("Ao comprar os produtos:");
                offerLabel.text("Oferecer os produtos:");
                applySelect.html(`<option value="all">Qualquer produto</option>`).val("all").trigger("change");
            }

            offerSelect.html("").val("").trigger("change");

            setPlanSelect2(applySelect);
            setPlanSelect2(offerSelect);
        })
        .trigger("change");

    const select2HasAll = [
        "#add_apply_on_shipping",
        "#edit_apply_on_shipping",
        "#add_apply_on_plans",
        "#edit_apply_on_plans",
        "#add_offer_on_plans",
        "#edit_offer_on_plans",
    ].join(", ");
    $(select2HasAll).on("select2:select", function () {
        let selectPlan = $(this);
        if (
            (selectPlan.val().length > 1 && selectPlan.val().includes("all")) ||
            (selectPlan.val().includes("all") && selectPlan.val() !== "all")
        ) {
            selectPlan.val("all").trigger("change");
        }
    });

    // Config
    $(document).on("click", "#config-upsell", function (event) {
        event.preventDefault();

        $.ajax({
            method: "GET",
            url: "/api/projectupsellconfig/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let upsellConfig = response.data;
                $("#header_config").val(`${upsellConfig.header}`);
                $("#countdown_time_config").val(`${upsellConfig.countdown_time}`);

                if (upsellConfig.countdown_flag) {
                    $("#countdown_flag").prop("checked", true);
                } else {
                    $("#countdown_flag").prop("checked", false);
                }
                if (upsellConfig.has_upsell) {
                    $("#btn-preview-config").prop("href", upsellConfig.checkout_url).show();
                }

                $("#modal_config_upsell").modal("show");
            },
        });
    });

    $(document).on("click", ".bt-upsell-config-update", function (event) {
        event.preventDefault();
        if ($("#countdown_flag").is(":checked") && $("#countdown_time_config").val() == "") {
            alertCustom("error", "Preencha o campo Contagem");
            return false;
        }

        if ($("#countdown_time_config").val() < 1 || $("#countdown_time_config").val() > 60) {
            alertCustom("error", "Contador deve ter um valor entre 1 e 60 minutos.");
            return false;
        }

        let form_data = new FormData(document.getElementById("form_config_upsell"));
        let header = $("#header_config").val();
        let countdownTime = $("#countdown_time_config").val();
        let countDownFlag = $("#countdown_flag").val();

        form_data.set("header", header);
        form_data.set("countdown_time", countdownTime);
        form_data.set("countdown_flag", countDownFlag);

        $.ajax({
            method: "POST",
            url: "/api/projectupsellconfig/" + projectId,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom("success", response.message);
                $("#modal_config_upsell").modal("hide");
            },
        });
    });
});
