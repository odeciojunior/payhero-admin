$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-1);

    $(".value-mask").maskMoney({
        thousands: ".",
        decimal: ",",
        allowZero: true,
        prefix: "",
    });
    function formatDouble(number) {
        return number.replace(".", "").replace(",", ".");
    }
    function formatMoney(number) {
        return (Math.round(number * 100) / 100).toFixed(2).replace(".", ",");
    }
    //store type
    $("#ob_type_value").click(function () {
        $("#ob_percent_opt").hide();
        $("#ob_money_opt").show();
        $("#ob_money_opt input").focus();
        $("#store-discount-order-bump").val(
            formatDouble($("#ob_money_opt input").val())
        );
    });

    $("#ob_type_percent").click(function () {
        $("#ob_money_opt").hide();
        $("#ob_percent_opt").show();
        $("#ob_percent_opt input").focus();
        $("#store-discount-order-bump").val($("#ob_percent_opt input").val());
    });
    $("#ob_money_opt input").change(function () {
        $("#store-discount-order-bump").val(
            formatDouble($("#ob_money_opt input").val())
        );
        // .replace('.','').replace(',','.')
    });
    $("#ob_percent_opt input").change(function () {
        $("#store-discount-order-bump").val($("#ob_percent_opt input").val());
    });

    //update type
    $("#obu_type_value").click(function () {
        $("#obu_percent_opt").hide();
        $("#obu_money_opt").show();
        $("#obu_money_opt input").focus();
        $("#update-discount-order-bump").val(
            formatDouble($("#obu_money_opt input").val())
        );
    });

    $("#obu_type_percent").click(function () {
        $("#obu_money_opt").hide();
        $("#obu_percent_opt").show();
        $("#obu_percent_opt input").focus();
        $("#update-discount-order-bump").val($("#obu_percent_opt input").val());
    });
    $("#obu_money_opt input").change(function () {
        $("#update-discount-order-bump").val(
            formatDouble($("#obu_money_opt input").val())
        );
        // .replace('.','').replace(',','.')
    });
    $("#obu_percent_opt input").change(function () {
        $("#update-discount-order-bump").val($("#obu_percent_opt input").val());
    });

    function index() {
        loadOnTable("#table-order-bump tbody", "#table-order-bump");
        $("#pagination-invites").children().attr("disabled", "disabled");

        let link =
            arguments.length > 0 && arguments[0] !== undefined
                ? arguments[0]
                : null;
        link = "/api/orderbump" + (link || "");

        $("#tab-order-bump-panel").find(".no-gutters").css("display", "none");
        $("#table-order-bump").find("thead").css("display", "none");

        $.ajax({
            method: "GET",
            url: link,
            data: {
                project_id: projectId,
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (resp) => {
                errorAjaxResponse(resp);
            },
            success: (resp) => {
                let rules = resp.data;
                let table = $("#table-order-bump tbody");
                if (rules.length) {
                    $("#pagination-container-order-bump")
                        .removeClass("d-none")
                        .addClass("d-flex");
                    table.html("");
                    $("#tab-order-bump-panel")
                        .find(".no-gutters")
                        .css("display", "flex");
                    $("#table-order-bump")
                        .find("thead")
                        .css("display", "contents");

                    for (let rule of rules) {
                        let row = `<tr>

                                <td>

                                    <div class="fullInformation-order-bump ellipsis-text">
                                        ${rule.description}
                                    </div>

                                    <div class="container-tooltips-order-bump"></div>

                                </td>


                                <td class="text-center">${
                                    rule.active_flag
                                        ? `<span class="badge badge-success">Ativo</span>`
                                        : `<span class="badge badge-disable">Desativado</span>`
                                }
                                </td>

                                <td>
                                    <div class='d-flex justify-content-end align-items-center'>
                                        <a class="pointer mg-responsive show-order-bump" data-id="${
                                            rule.id
                                        }" title="Visualizar"><span class=""><img src='/build/global/img/icon-eye.svg'/></span></a>

                                        <a class="pointer mg-responsive edit-order-bump" data-id="${
                                            rule.id
                                        }" title="Editar" ><span class=""><img src='/build/global/img/pencil-icon.svg'/></span></a>

                                        <a class="pointer mg-responsive destroy-order-bump" data-id="${
                                            rule.id
                                        }" title="Excluir" data-toggle="modal" data-target="#modal-delete-order-bump"><span class=""><img src='/build/global/img/icon-trash-tale.svg'/></span></a>
                                    </div>
                                </td>
                                   </tr>`;
                        table.append(row);
                    }
                    pagination(resp, "order-bump", index);
                    $(".fullInformation").tooltip({
                        container: ".container-tooltips",
                    });

                    $(".fullInformation-order-bump").bind(
                        "mouseover",
                        function () {
                            var $this = $(this);

                            if (
                                this.offsetWidth < this.scrollWidth &&
                                !$this.attr("title")
                            ) {
                                $this
                                    .attr({
                                        "data-toggle": "tooltip",
                                        "data-placement": "top",
                                        "data-title": $this.text(),
                                    })
                                    .tooltip({
                                        container:
                                            ".container-tooltips-order-bump",
                                    });
                                $this.tooltip("show");
                            }
                        }
                    );
                } else {
                    $("#pagination-container-order-bump")
                        .removeClass("d-flex")
                        .addClass("d-none");

                    table.html(`
                        <tr class="text-center">
                            <td colspan="3">
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum order bump configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro order bump para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-order-bump' data-toggle="modal" data-target="#modal-store-order-bump">Adicionar order bump</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                    table.parent().addClass("table-striped");
                }
            },
        });

        if (!["shopify", "woocommerce"].includes($("#project_type").val())) {
            $(".use-variants-order-bump")
                .prop("checked", false)
                .val(0)
                .closest(".switch-holder")
                .hide();
        }
    }

    $("#tab_order_bump").on("click", function () {
        index();
    });

    $(document).on("click", ".show-order-bump", function () {
        let id = $(this).data("id");
        $.ajax({
            url: "/api/orderbump/" + id,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (resp) => {
                errorAjaxResponse(resp);
            },
            success: (resp) => {
                let rule = resp.data;
                let applyOnShipping = rule.apply_on_shipping
                    .map(
                        (shipping) =>
                            shipping.name +
                            (shipping.information
                                ? ` - ${shipping.information}`
                                : "")
                    )
                    .join(" / ");
                let applyOnPlans = rule.apply_on_plans
                    .map(
                        (plan) =>
                            plan.name +
                            (plan.description ? ` - ${plan.description}` : "")
                    )
                    .join(" / ");
                let offerPlans = rule.offer_plans
                    .map(
                        (plan) =>
                            plan.name +
                            (plan.description ? ` - ${plan.description}` : "")
                    )
                    .join(" / ");
                $("#order-bump-show-table .order-bump-description").html(
                    rule.description
                );

                if (rule.type == 1) {
                    $("#order-bump-show-table .order-bump-discount").html(
                        rule.discount.toLocaleString("pt-br", {
                            minimumFractionDigits: 2,
                        })
                    );
                    $("#order-bump-show-table .order-bump-discount").prepend(
                        "R$"
                    );
                } else {
                    $("#order-bump-show-table .order-bump-discount").html(
                        rule.discount
                    );
                    $("#order-bump-show-table .order-bump-discount").append(
                        "%"
                    );
                }
                $("#order-bump-show-table .order-bump-apply-shipping").html(
                    applyOnShipping
                );
                $("#order-bump-show-table .order-bump-apply-plans").html(
                    applyOnPlans
                );
                $("#order-bump-show-table .order-bump-offer-plans").html(
                    offerPlans
                );
                $("#order-bump-show-table .order-bump-status").html(
                    rule.active_flag
                        ? `<span class="badge badge-success">Ativo</span>`
                        : `<span class="badge badge-danger">Desativado</span>`
                );
                $("#modal-show-order-bump").modal("show");
            },
        });
    });

    $(document).on("click", ".edit-order-bump", function () {
        let id = $(this).data("id");
        $.ajax({
            url: "/api/orderbump/" + id,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (resp) => {
                errorAjaxResponse(resp);
            },
            success: (resp) => {
                let rule = resp.data;
                let applyOnShippingInput = $(
                    "#update-apply-on-shipping-order-bump"
                );
                let applyOnPlansInput = $("#update-apply-on-plans-order-bump");
                let offerPlansInput = $("#update-offer-plans-order-bump");

                $("#update-description-order-bump").val(rule.description);

                if (rule.type == 1) {
                    $("#obu_type_value").trigger("click");
                    $("#obu_money_opt input").val(formatMoney(rule.discount));
                } else {
                    $("#obu_percent_opt input").val(rule.discount);
                    $("#obu_type_percent").trigger("click");
                }
                $("#update-discount-order-bump").val(rule.discount);
                $("#update-active-flag-order-bump")
                    .val(rule.active_flag)
                    .prop("checked", rule.active_flag === 1);
                $("#modal-update-order-bump .use-variants-order-bump")
                    .val(rule.use_variants)
                    .prop("checked", rule.use_variants === 1)
                    .trigger("change");

                let applyOnShipping = [];
                applyOnShippingInput.html("");
                for (let shipping of rule.apply_on_shipping) {
                    applyOnShipping.push(shipping.id);
                    applyOnShippingInput.append(
                        `<option value="${shipping.id}">${
                            shipping.name +
                            (shipping.information
                                ? ` - ${shipping.information}`
                                : "")
                        }</option>`
                    );
                }
                applyOnShippingInput.val(applyOnShipping);

                let applyOnPlans = [];
                applyOnPlansInput.html("");
                for (let plan of rule.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    applyOnPlansInput.append(
                        `<option value="${plan.id}">${
                            plan.name +
                            (plan.description ? ` - ${plan.description}` : "")
                        }</option>`
                    );
                }
                applyOnPlansInput.val(applyOnPlans);

                offerPlansInput.html("");
                let offerPlans = [];
                for (let plan of rule.offer_plans) {
                    offerPlans.push(plan.id);
                    offerPlansInput.append(
                        `<option value="${plan.id}">${
                            plan.name +
                            (plan.description ? ` - ${plan.description}` : "")
                        }</option>`
                    );
                }
                offerPlansInput.val(offerPlans);

                setShippingSelect2(
                    "#update-apply-on-shipping-order-bump",
                    "#modal-update-order-bump"
                );
                setPlanSelect2(
                    "#update-apply-on-plans-order-bump",
                    "#modal-update-order-bump"
                );
                setPlanSelect2(
                    "#update-offer-plans-order-bump",
                    "#modal-update-order-bump"
                );

                $("#btn-update-order-bump").data("id", id);
                $("#modal-update-order-bump").modal("show");
            },
        });
    });

    $("#btn-store-order-bump").on("click", function () {
        let formData = new FormData(
            document.querySelector("#form-store-order-bump")
        );
        formData.append("project_id", projectId);

        let data = {};
        for (let pair of formData.entries()) {
            data[pair[0]] = pair[1];
        }

        $("#btn-store-order-bump").attr("disabled", true);
        $.ajax({
            method: "POST",
            url: "/api/orderbump",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            error: (resp) => {
                errorAjaxResponse(resp);
                $("#btn-store-order-bump").attr("disabled", false);
            },
            success: (resp) => {
                alertCustom("success", resp.message);
                $("#modal-store-order-bump").modal("hide");
                $(
                    "#store-description-order-bump, #store-discount-order-bump, #ob_money_opt input, #ob_percent_opt input"
                ).val("");
                $(
                    "#store-apply-on-shipping-order-bump, #store-apply-on-plans-order-bump, #store-offer-plans-order-bump"
                )
                    .val(null)
                    .trigger("change");
                index();
                $("#btn-store-order-bump").attr("disabled", false);
            },
        });
    });

    $("#btn-update-order-bump").on("click", function () {
        let id = $(this).data("id");
        let formData = new FormData(
            document.querySelector("#form-update-order-bump")
        );
        formData.append("project_id", projectId);
        $.ajax({
            method: "POST",
            url: "/api/orderbump/" + id,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            error: (resp) => {
                errorAjaxResponse(resp);
            },
            success: (resp) => {
                alertCustom("success", resp.message);
                $("#modal-update-order-bump").modal("hide");
                index();
            },
        });
    });

    // load delete modal
    $(document).on("click", ".destroy-order-bump", function (event) {
        event.preventDefault();

        let id = $(this).data("id");

        $("#btn-delete-orderbump").unbind("click");
        $("#btn-delete-orderbump").on("click", function () {
            $.ajax({
                method: "DELETE",
                url: "/api/orderbump/" + id,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                error: function (response) {
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    index();

                    alertCustom("success", response.message);
                },
            });
        });
    });

    //Search Shipping
    function setShippingSelect2(element, dropdownParent) {
        const $element = typeof element === "string" ? $(element) : element;
        const $dropdownParent =
            typeof element === "string" ? $(dropdownParent) : dropdownParent;

        let configs = {
            placeholder: "Nome do frete",
            multiple: true,
            dropdownParent: $dropdownParent,
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
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
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
                            return {
                                id: obj.id,
                                text:
                                    obj.name +
                                    (obj.information
                                        ? " - " + obj.information
                                        : ""),
                            };
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

    setShippingSelect2(
        "#store-apply-on-shipping-order-bump",
        "#modal-store-order-bump"
    );
    setShippingSelect2(
        "#update-apply-on-shipping-order-bump",
        "#modal-update-order-bump"
    );
    $("#store-apply-on-shipping-order-bump")
        .html(`<option value="all">Qualquer frete</option>`)
        .val("all");

    //Search plan
    function setPlanSelect2(element, dropdownParent) {
        const $element = typeof element === "string" ? $(element) : element;
        const $dropdownParent =
            typeof element === "string" ? $(dropdownParent) : dropdownParent;

        const useVariants = $dropdownParent
            .find(".use-variants-order-bump")
            .prop("checked")
            ? 1
            : 0;
        const targetName = useVariants ? "plano" : "produto";

        let configs = {
            placeholder: `Nome do ${targetName}`,
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $dropdownParent,
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
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                processResults: function (res) {
                    let elemId = this.$element.attr("id");
                    if (
                        [
                            "store-apply-on-plans-order-bump",
                            "update-apply-on-plans-order-bump",
                        ].includes(elemId) &&
                        res.meta.current_page === 1
                    ) {
                        let allObject = {
                            id: "all",
                            name: `Qualquer ${targetName}`,
                            description: "",
                        };
                        res.data.unshift(allObject);
                    }
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
                        pagination: {
                            more: res.meta.current_page !== res.meta.last_page,
                        },
                    };
                },
            },
        };

        $element.select2(configs);
    }

    const select2HasAll = [
        "#store-apply-on-plans-order-bump",
        "#update-apply-on-plans-order-bump",
        "#store-apply-on-shipping-order-bump",
        "#update-apply-on-shipping-order-bump",
    ].join(", ");
    $(select2HasAll).on("select2:select", function () {
        let selectPlan = $(this);
        if (selectPlan.val().length > 1 && selectPlan.val().includes("all")) {
            selectPlan.val("all").trigger("change");
        }
    });

    $(".use-variants-order-bump")
        .on("change", function () {
            const slider = $(this);
            const modal = slider.closest(".modal");

            const applyContainer = modal.find(".apply-on-plan-container");
            const offerContainer = modal.find(".offer-plan-container");

            const applyLabel = applyContainer.find("label");
            const offerLabel = offerContainer.find("label");

            const applySelect = applyContainer.find("select");
            const offerSelect = offerContainer.find("select");

            if (slider.prop("checked")) {
                applyLabel.text("Ao comprar os plano:");
                offerLabel.text("Oferecer os planos:");
                applySelect
                    .html(`<option value="all">Qualquer plano</option>`)
                    .val("all")
                    .trigger("change");
            } else {
                applyLabel.text("Ao comprar os produtos:");
                offerLabel.text("Oferecer os produtos:");
                applySelect
                    .html(`<option value="all">Qualquer produto</option>`)
                    .val("all")
                    .trigger("change");
            }

            offerSelect.html("").val("").trigger("change");

            setPlanSelect2(applySelect, modal);
            setPlanSelect2(offerSelect, modal);
        })
        .trigger("change");

    setPlanSelect2(
        "#store-apply-on-plans-order-bump",
        "#modal-store-order-bump"
    );
    setPlanSelect2("#store-offer-plans-order-bump", "#modal-store-order-bump");
    setPlanSelect2(
        "#update-apply-on-plans-order-bump",
        "#modal-update-order-bump"
    );
    setPlanSelect2(
        "#update-offer-plans-order-bump",
        "#modal-update-order-bump"
    );
});
