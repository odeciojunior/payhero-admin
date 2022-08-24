$(document).ready(function () {
    let projectId = $(window.location.pathname.split("/")).get(-1);
    let countdownInterval = null;
    let descriptionconfig;

    ClassicEditor.create(document.querySelector("#description_config"), {
        language: "pt-br",
        uiColor: "#F1F4F5",
        toolbar: ["heading", "|", "bold", "italic", "|", "link", "|", "undo", "redo"],
    })
        .then((newEditor) => {
            descriptionconfig = newEditor;
        })
        .catch((error) => {
            console.error(error);
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

                    $("#table-upsell").addClass("table-striped");
                    $("#count-upsell").html(response.meta.total);
                    let data = "";
                    $.each(response.data, function (index, value) {
                        data += `
                        <tr>
                            <td>${value.description}</td>
                            <td class="text-center">${
                                value.active_flag
                                    ? `<span class="badge badge-success">Ativo</span>`
                                    : `<span class="badge badge-danger">Desativado</span>`
                            }</td>
                            <td style='text-align:center'>
                                <div class='d-flex justify-content-end align-items-center'>
                                    <a role='button' title='Visualizar' class='mg-responsive details-upsell pointer' data-upsell="${
                                        value.id
                                    }"><span class=""><img src='/build/global/img/icon-eye.svg'/></span></a>
                                    <a role='button' title='Editar' class='pointer edit-upsell mg-responsive' data-upsell="${
                                        value.id
                                    }"><span class=''><img src='/build/global/img/pencil-icon.svg'/></span></a>
                                    <a role='button' title='Excluir' class='pointer delete-upsell mg-responsive' data-upsell="${
                                        value.id
                                    }" data-toggle="modal" data-target="#modal-delete-upsell"><span class=''><img src='/build/global/img/icon-trash-tale.svg'/></span></a>
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                    dataTable.append(data);
                    $(".div-config").show();

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
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                errorAjaxResponse(response);
            }, success: function (response) {
                let upsell = response.data;
                $("#edit_description_upsell").val(`${upsell.description}`);
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
                        `<option value="${shipping.id}">${
                            shipping.name + (shipping.information ? " - " + shipping.information : "")
                        }</option>`
                    );
                }
                $("#edit_apply_on_shipping").val(applyOnShipping);

                let applyOnPlans = [];
                for (let plan of upsell.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    $("#edit_apply_on_plans").append(
                        `<option value="${plan.id}">${
                            plan.name + (plan.description ? " - " + plan.description : "")
                        }</option>`
                    );
                }
                $("#edit_apply_on_plans").val(applyOnPlans);

                let offerOnPlans = [];
                for (let plan of upsell.offer_on_plans) {
                    offerOnPlans.push(plan.id);
                    $("#edit_offer_on_plans").append(
                        `<option value="${plan.id}">${
                            plan.name + (plan.description ? " - " + plan.description : "")
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
            },
            success: function success(response) {
                $("#modal_add_upsell").modal("hide");
                loadUpsell();
                alertCustom("success", response.message);
                $("#add_apply_on_plans, #add_offer_on_plans").val(null).trigger("change");
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
                $(".upsell-discount").html(`${upsell.discount != 0 ? `${upsell.discount}%` : `Valor sem desconto`}`);

                $(".upsell-status").html(
                    `${
                        upsell.active_flag
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
                $("#title_config").val(`${upsellConfig.title}`);
                descriptionconfig.setData(`${upsellConfig.description ?? " "}`);
                $("#countdown_time_config").val(`${upsellConfig.countdown_time}`);

                if (upsellConfig.countdown_flag) {
                    $("#countdown_flag").prop("checked", true);
                } else {
                    $("#countdown_flag").prop("checked", false);
                }
                if (upsellConfig.has_upsell) {
                    $(".btn-view-config").show();
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
        let title = $("#title_config").val();
        let description = descriptionconfig.getData();
        let countdownTime = $("#countdown_time_config").val();
        let countDownFlag = $("#countdown_flag").val();

        form_data.set("header", header);
        form_data.set("title", title);
        form_data.set("description", description);
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
    $(document).on("click", ".btn-return-to-config", function (event) {
        event.preventDefault();
        $("#modal-view-upsell-config").modal("hide");
        $("#modal_config_upsell").modal("show");
    });
    $(document).on("click", ".btn-view-config", function (event) {
        event.preventDefault();
        $("#modal_config_upsell").modal("hide");

        $.ajax({
            method: "POST",
            url: "/api/projectupsellconfig/previewupsell",
            dataType: "json",
            data: {
                project_id: projectId,
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let upsell = response.data;

                $("#div-upsell-products").html("");

                $("#upsell-header").html(upsell.header);
                $("#upsell-title").html(upsell.title);
                $("#upsell-description").html(upsell.description);

                if (upsell.countdown_flag) {
                    $("#timer_upsell").show();
                    startCountdown(upsell.countdown_time);
                } else {
                    $("#timer_upsell").hide();
                }

                let data = "";

                for (let key in upsell.plans) {
                    let plan = upsell.plans[key];
                    data += `<div class="product-info">
                                <div class="d-flex flex-column">`;
                    for (let product of plan.products) {
                        let firstVariant = Object.keys(product)[0];
                        data += `<div class="product-row">
                                    <img src="${product[firstVariant].photo}" class="product-img">
                                    <div class="ml-4">
                                        <h3>${product[firstVariant].amount}x ${product[firstVariant].name}</h3>`;
                        if (Object.keys(product).length > 1) {
                            data += `<select class="product-variant">`;
                            for (let i in product) {
                                data += `<option value="${i}">${product[i].description}</option>`;
                            }
                            data += `</select>`;
                        } else {
                            data += `<span class="text-muted">${product[firstVariant].description}</span>`;
                        }
                        data += `</div>
                             </div>`;
                    }
                    data += `</div>
                                <div class="d-flex flex-column mt-4 mt-md-0">`;
                    if (plan.discount) {
                        data += `<span class="original-price line-through">R$ ${plan.original_price}</span>
                                                 <div class="d-flex mb-2">
                                                     <span class="price font-30 mr-1" style="line-height: .8">R$ ${plan.price}</span>
                                                     <span class="discount text-success font-weight-bold">${plan.discount}% OFF</span>
                                                 </div>`;
                    }

                    if (!isEmpty(plan.installments)) {
                        data += `<div class="form-group">
                                    <select class="installments">`;
                        for (let installment of plan.installments) {
                            data += `<option value="${installment["amount"]}">${installment["amount"]}X DE R$ ${installment["value"]}</option>`;
                        }
                        data += `</select>
                             </div>`;
                    } else {
                        data += `<h2 class="text-primary mb-md-4"><b>R$ ${plan.price}</b></h2>`;
                    }
                    data += `<button class="btn btn-success btn-lg btn-buy">COMPRAR AGORA</button>
                         </div>
                    </div>`;

                    if (parseInt(key) !== upsell.plans.length - 1) {
                        data += `<hr class="plan-separator">`;
                    }
                }

                $("#div-upsell-products").append(data);

                $("#modal-view-upsell-config").modal("show");
            },
        });
    });

    function setIntervalAndExecute(fn, t) {
        fn();
        return setInterval(fn, t);
    }

    function startCountdown(countdownTime) {
        let countdown = new Date().getTime() + countdownTime * 60000;

        if (countdownInterval !== null) {
            clearInterval(countdownInterval);
        }

        countdownInterval = setIntervalAndExecute(() => {
            let now = new Date().getTime();
            let distance = countdown - now;

            if (distance > 0) {
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                $("#minutes").text(minutes.toString().padStart(2, "0"));
                $("#seconds").text(seconds.toString().padStart(2, "0"));
            } else {
                countdown = new Date().getTime() + countdownTime * 60000;
            }
        }, 1000);
    }
});
