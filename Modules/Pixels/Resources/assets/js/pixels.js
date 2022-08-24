const statusPixel = {
    1: "success",
    0: "danger",
};

const srcPlatforms = {
    google_analytics: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/old-analytics",
    google_analytics_four: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/analytics",
    google_adwords: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/google-ads",
    facebook: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/facebook",
    outbrain: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/outbrain",
    taboola: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/taboola",
    pinterest: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/pinterest",
    uol_ads: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/uol-ads",
    tiktok: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/tiktok",
    kwai: "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/kwai",
};

$(function () {
    let currentPage;

    const projectId = $(window.location.pathname.split("/")).get(-1);

    //comportamentos da tela
    $(".tab_pixels").on("click", function () {
        $("#previewimage").imgAreaSelect({ remove: true });
        atualizarPixel(currentPage);
        $(this).off();
    });

    function isChecked(input, pixelAttribute = null) {
        if (pixelAttribute != null) {
            if (pixelAttribute == "1" || pixelAttribute == "true") {
                input.prop("checked", true).change();
            } else {
                input.prop("checked", false).change();
            }
        } else {
            if (input.is(":checked")) {
                input.attr("checked", false);
            } else {
                input.attr("checked", "checked");
            }
        }
    }

    /**
     * List All Pixel
     */
    function atualizarPixel() {
        let link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        currentPage = link;

        if (link == null) {
            link = "/api/project/" + projectId + "/pixels";
        } else {
            link = "/api/project/" + projectId + "/pixels" + link;
        }

        loadOnTable("#data-table-pixel", "#table-pixel");

        $("#pagination-pixels").html("");
        $("#tab_pixels-panel").find(".no-gutters").css("display", "none");
        $("#table-pixel").find("thead").css("display", "none");

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                $("#data-table-pixel").html(response.message);
            },
            success: function success(response) {
                $("#data-table-pixel, #pagination-pixels").html("");

                if (response.data == "") {
                    $("#table-pixel").addClass("table-striped");
                    $("#data-table-pixel").html(`
                        <tr class="text-center">
                            <td colspan="8" style="height: 70px; vertical-align: middle;">
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum pixel configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro pixel para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-pixel' data-toggle="modal" data-target="#modal-create-pixel">Adicionar pixel</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    $("#tab_pixels-panel").find(".no-gutters").css("display", "flex");
                    $("#table-pixel").find("thead").css("display", "contents");

                    $.each(response.data, function (index, value) {
                        $("#data-table-pixel").append(`
                            <tr>
                                <td>${value.name}</td>
                                <td>${value.code}</td>
                                <td>${value.platform_enum}</td>
                                <td class="text-center"><span class="badge badge-${
                                    statusPixel[value.status]
                                }">${value.status_translated}</span></td>
                                <td style='text-align:center'>
                                    <div class='d-flex justify-content-end align-items-center'>
                                        <a role='button' title='Visualizar' class='mg-responsive details-pixel pointer' pixel='${
                                            value.id
                                        }' data-target='#modal-details-pixel' data-toggle='modal'><span class=""><img src='/build/global/img/icon-eye.svg'/></span></a>
                                        <a role='button' title='Editar' class='mg-responsive edit-pixel pointer' pixel='${
                                            value.id
                                        }' data-toggle='modal' type='a'><span class=""><img src='/build/global/img/pencil-icon.svg'/></span></a>
                                        <a role='button' title='Excluir' class='mg-responsive delete-pixel pointer' pixel='${
                                            value.id
                                        }' data-toggle='modal' data-target='#modal-delete-pixel' type='a'><span class=''><img src='/build/global/img/icon-trash-tale.svg'/></span></a>
                                    </div>
                                </td>
                            </tr>
                        `);
                        $("#table-pixel").addClass("table-striped");
                    });

                    pagination(response, "pixels", atualizarPixel);
                }
            },
        });
    }

    /**
     * SHOW PIXEL
     */

    // Show Pixel
    $(document).on("click", ".details-pixel", function () {
        let pixel = $(this).attr("pixel");
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error() {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                renderDetailPixel(response);
            },
        });
    });

    // Rendere Modal Show Pixel
    function renderDetailPixel(pixel) {
        $("#modal-detail-pixel .pixel-description").html(pixel.name);
        $("#modal-detail-pixel .pixel-code").html(pixel.code);
        $("#modal-detail-pixel .pixel-platform").html(pixel.platform_enum);
        $("#modal-detail-pixel .pixel-status").html(
            pixel.status == 1
                ? '<span class="badge badge-success text-left">Ativo</span>'
                : '<span class="badge badge-danger">Desativado</span>'
        );
        $("#modal-detail-pixel").modal("show");
    }

    /**
     * Edit Pixel
     */
    let pixelEdit = {};
    $(document).on("click", ".edit-pixel", function () {
        $.ajax({
            method: "GET",
            url: `/api/project/${projectId}/pixels/${$(this).attr("pixel")}/edit`,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                const pixel = response.data;
                pixelEdit = pixel;
                renderModalPixelEdit(pixel);
                openModalEditPixel();
                $("#modal-edit-pixel").modal("show");
            },
        });
    });

    function renderModalPixelEdit(pixel, newPlatform = null) {
        resetInputs();

        const imgPlatform = $(".img-edit-selected");
        const codeEditInput = $(".code-edit");
        const conversionalEditInput = $(".conversional-edit");
        const singleEvent = $(".single-event-edit");
        const multipleEvent = $(".multiple-event-edit");

        let code = inputCodeByPlatform(pixel.platform, pixel.code, "", true);

        if (newPlatform == null) {
            newPlatform = pixel.platform;
        }

        if (pixel.platform !== "google_adwords") {
            singleEvent.addClass("d-none");
            multipleEvent.removeClass("d-none");
        } else {
            singleEvent.removeClass("d-none");
            multipleEvent.addClass("d-none");
        }

        $(".platform-edit").val(newPlatform);

        imgPlatform.attr("src", srcPlatforms[newPlatform]);

        $(".description-edit").val(pixel.name);

        if (Array.isArray(code)) {
            codeEditInput.val(code[0]);
            conversionalEditInput.val(code[1]);
        } else {
            codeEditInput.val(code);
            conversionalEditInput.val("");
        }

        $(".percentage-boleto-value-edit").val(pixel.value_percentage_purchase_boleto);
        $(".percentage-pix-value-edit").val(pixel.value_percentage_purchase_pix);

        // plans
        const plansInput = $(".apply_plans");
        plansInput.val(null).trigger("change");
        plansInput.html("");
        let applyOnPlans = [];
        for (let plan of pixel.apply_on_plans) {
            applyOnPlans.push(plan.id);
            plansInput.append(`
                <option value="${plan.id}">
                    ${plan.name + (plan.description ? " - " + plan.description : "")}
                </option>
            `);
        }
        plansInput.val(applyOnPlans);

        // Run Pixel
        isChecked($(".status-edit"), pixel.status);
        isChecked($(".checkout-edit"), pixel.checkout);
        isChecked($(".basic-data-edit"), pixel.basic_data);
        isChecked($(".delivery-edit"), pixel.delivery);
        isChecked($(".coupon-edit"), pixel.coupon);
        isChecked($(".payment-info-edit"), pixel.payment_info);
        isChecked($(".purchase-card-edit"), pixel.purchase_card);
        isChecked($(".purchase-boleto-edit"), pixel.purchase_boleto);
        isChecked($(".purchase-pix-edit"), pixel.purchase_pix);
        isChecked($(".purchase-all-edit"), pixel.purchase_all);
        isChecked($(".upsell-edit"), pixel.upsell);
        isChecked($(".purchase-upsell-edit"), pixel.purchase_upsell);
        isChecked($(".send-value-edit"), pixel.send_value_checkout);
        $("#single-event-edit").val(pixel.event_select).change();

        if (pixel.send_value_checkout == "true") {
            $(".send-value-edit").addClass("is-checked");
        } else {
            $(".send-value-edit").removeClass("is-checked");
        }

        // Manipulation Modal pixel
        changePlaceholderInput(newPlatform, codeEditInput, $("#text-type-code-edit"), $("#conversional-pixel-edit"));

        switch (newPlatform) {
            case "facebook":
                pixelFacebook(pixel);
                break;
            case "taboola":
            case "outbrain":
                pixelTaboolaOutbrain(pixel);
                break;
        }
    }

    function resetInputs() {
        $(".input-purchase-event-name-edit").val();
        $(
            "#select-facebook-integration-edit, #div-facebook-token-api-edit, #facebook-token-api-edit, .div-purchase-event-name-edit"
        ).hide();
    }

    /**
     * Edit Facebook Manipulation
     */
    function pixelFacebook(pixel) {
        if (pixel.is_api) {
            $("#facebook-token-api-edit").prop("readonly", false).val(pixel.facebook_token);
            $(".facebook-api-edit").prop("checked", "checked");
            $(".url_facebook_domain_edit").val(pixel.url_facebook_domain);
            $(".url_facebook_api_div_edit").show();
        } else {
            $(".url_facebook_domain_edit").val("");
            $(".url_facebook_api_div_edit").hide();
            $(".facebook-api-default-edit").prop("checked", "checked");
            $("#facebook-token-api-edit").prop("readonly", true).val("");
        }
        $("#select-facebook-integration-edit, #div-facebook-token-api-edit, #facebook-token-api-edit").show();
    }

    /**
     * Edit Outbrain Taboola Manipulation
     */
    function pixelTaboolaOutbrain(pixel) {
        $(".input-purchase-event-name-edit").val(pixel.purchase_event_name);
        $(".div-purchase-event-name-edit").show();
    }

    $("#modal-edit-pixel input[type=radio]").change(function () {
        if (this.value === "api") {
            $(".url_facebook_api_div_edit").show();
            $("#facebook-token-api-edit").prop("readonly", false).val(pixelEdit.facebook_token);
        } else {
            $(".url_facebook_api_div_edit").hide();
            $("#facebook-token-api-edit").prop("readonly", true).val();
        }
    });

    function openModalEditPixel() {
        $("#modal-edit-pixel #configure-edit-pixel").show();
    }

    function inputCodeByPlatform(platform, code, conversional = "", explode = false) {
        switch (platform) {
            case "google_adwords":
                return explode ? code.split("/") : `${code}/${conversional}`;
            case "facebook":
            case "google_analytics":
            case "google_analytics_four":
            case "taboola":
            case "outbrain":
            case "uol_ads":
            case "tiktok":
            case "kwai":
            default:
                return code;
        }
    }

    //Update Pixel
    $("#btn-update-pixel").on("click", function () {
        const inputDescriptionEdit = $("#modal-edit-pixel .description-edit").val();
        const inputPlatformEdit = $("#modal-edit-pixel .platform-edit").val();
        const isApi = $("#modal-edit-pixel input[type=radio]:checked").val();
        const inputCodeEdit = $("#modal-edit-pixel .code-edit").val();
        const inputConversionalEdit = $("#modal-edit-pixel .conversional-edit").val();
        const valuePercentagePurchaseBoleto = $("#modal-edit-pixel .percentage-boleto-value-edit").val();
        const valuePercentagePurchasePix = $("#modal-edit-pixel .percentage-pix-value-edit").val();
        const facebookTokenApi = $("#modal-edit-pixel #facebook-token-api-edit").val();
        const inputPurchaseEventName = $("#modal-edit-pixel .input-purchase-event-name-edit").val();
        const plansApply = $("#modal-edit-pixel .apply_plans").val();

        if (
            !validateDataPixelForm({
                name: inputDescriptionEdit,
                platform: inputPlatformEdit,
                is_api: isApi,
                code: inputCodeEdit,
                conversional: inputConversionalEdit,
                value_percentage_purchase_boleto: valuePercentagePurchaseBoleto,
                value_percentage_purchase_pix: valuePercentagePurchasePix,
                facebook_token_api: facebookTokenApi,
                purchase_event_name: inputPurchaseEventName,
                plans_apply: plansApply,
            })
        ) {
            return false;
        }

        let codeEdit = inputCodeByPlatform(inputPlatformEdit, inputCodeEdit, inputConversionalEdit);

        $.ajax({
            method: "PUT",
            url: `/api/project/${projectId}/pixels/${pixelEdit.id_code}`,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                name: inputDescriptionEdit,
                code: codeEdit,
                platform: inputPlatformEdit,
                status: $("#modal-edit-pixel .status-edit").is(":checked"),
                checkout: $("#modal-edit-pixel .checkout-edit").is(":checked"),
                basic_data: true, // $("#modal-edit-pixel .basic-data-edit").is(':checked'),
                delivery: true, // $("#modal-edit-pixel .delivery-edit").is(':checked'),
                coupon: true, // $("#modal-edit-pixel .coupon-edit").is(':checked'),
                payment_info: true, // $("#modal-edit-pixel .payment-info-edit").is(':checked'),
                purchase_card: $("#modal-edit-pixel .purchase-card-edit").is(":checked"),
                purchase_boleto: $("#modal-edit-pixel .purchase-boleto-edit").is(":checked"),
                purchase_pix: $("#modal-edit-pixel .purchase-pix-edit").is(":checked"),
                purchase_all: false, //$("#modal-edit-pixel .purchase-all-edit").is(':checked'),
                upsell: true, // $("#modal-edit-pixel .upsell-edit").is(':checked'),
                purchase_upsell: true, // $("#modal-edit-pixel .purchase-upsell-edit").is(':checked'),
                event_select: $("#modal-edit-pixel #single-event-edit").val(),
                send_value_checkout: $("#modal-edit-pixel .send-value-edit").is(":checked"),
                edit_pixel_plans: plansApply,
                purchase_event_name: inputPurchaseEventName,
                is_api: isApi,
                facebook_token_api: facebookTokenApi,
                value_percentage_purchase_boleto: valuePercentagePurchaseBoleto,
                value_percentage_purchase_pix: valuePercentagePurchasePix,
                url_facebook_domain_edit: $("#modal-edit-pixel .url_facebook_domain_edit").val(),
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success() {
                $("#modal-edit-pixel").modal("hide");
                alertCustom("success", "Pixel atualizado com sucesso");
                atualizarPixel(currentPage);
            },
        });
    });

    /**
     * DELETE PIXEL
     */

    // Open Modal Destroy Pixel
    $(document).on("click", ".delete-pixel", function (event) {
        event.preventDefault();

        let pixel = $(this).attr("pixel");

        // Delete Pixel
        $("#btn-delete").unbind("click");
        $("#btn-delete").on("click", function () {
            $.ajax({
                method: "DELETE",
                url: "/api/project/" + projectId + "/pixels/" + pixel,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: (function (_error3) {
                    function error() {
                        return _error3.apply(this, arguments);
                    }

                    error.toString = function () {
                        return _error3.toString();
                    };

                    return error;
                })(function (response) {
                    errorAjaxResponse(response);
                }),
                success: function success() {
                    alertCustom("success", "Pixel Removido com sucesso");
                    atualizarPixel(currentPage);
                },
            });
        });
    });

    /**
     * CREATE NEW PIXEL
     */

    // Open Modal New Pixel
    $(".add-pixel").on("click", function () {
        openModalCreatePixel();
        $("#modal-create-pixel").modal("show");
    });

    // change pixel platform
    $("img.img-selected").on("click", function () {
        openModalCreatePixel();
    });

    function openModalCreatePixel() {
        $("#configure-new-pixel").hide();
        $("#select-platform-pixel").show();
    }

    function changePlaceholderInput(value, inputPlatform, inputAW, inputConversional) {
        inputAW.html("").hide();
        inputConversional.hide();

        const singleEvent = $(".single-event");
        const multipleEvent = $(".multiple-event");
        switch (value) {
            case "facebook":
                inputPlatform.attr("placeholder", "52342343245553");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "google_adwords":
                inputAW.html("AW-").show();
                inputConversional.show();
                inputPlatform.attr("placeholder", "8981445741-4");
                inputPlatform.parent().parent().find("label").html("Código de conversão");
                singleEvent.removeClass("d-none");
                multipleEvent.addClass("d-none");
                break;
            case "google_analytics":
                inputPlatform.attr("placeholder", "UA-8984567741-3");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "google_analytics_four":
                inputPlatform.attr("placeholder", "G-KZSV4LMBAC");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "taboola":
                inputPlatform.attr("placeholder", "1010100");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "outbrain":
                inputPlatform.attr("placeholder", "00de2748d47f2asdl39877mash");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "uol_ads":
                inputPlatform.attr("placeholder", "hutu27");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "tiktok":
                inputPlatform.attr("placeholder", "C5OSDKKVNBDLN9M5C6UG");
                inputPlatform.parent().parent().find("label").html("ID");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            case "kwai":
                inputPlatform.attr("placeholder", "C5OSDKKVNBDLN9M5C6UG");
                inputPlatform.parent().parent().find("label").html("ID");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
            default:
                inputPlatform.attr("placeholder", "Código");
                inputPlatform.parent().parent().find("label").html("Código");
                singleEvent.addClass("d-none");
                multipleEvent.removeClass("d-none");
                break;
        }
    }

    $("img.logo-pixels-create").on("click", function () {
        const platform = $(this).data("value");
        $("#platform").val("").val(platform);
        $(".form-control").val("");

        $(".img-logo").attr("src", this.src);

        $(
            "#select-facebook-integration, #div-facebook-token-api, .purchase-event-name-div, .url_facebook_api_div"
        ).hide();

        changePlaceholderInput(platform, $("#code-pixel"), $("#input-code-pixel"), $("#conversional-pixel"));

        if (platform === "facebook") {
            $("#select-facebook-integration, #div-facebook-token-api").show();
            if ($("input[type=radio]").val() == "api") {
                $(".url_facebook_api_div").show();
                $("#facebook-token-api").attr("readonly", false);
            } else if ($("input[type=radio]").val() == "default") {
                $(".select-default-facebook").click();
                $("#facebook-token-api").attr("readonly", true);
            }
        } else if (["taboola", "outbrain"].includes(platform)) {
            $(".purchase-event-name-div").show();
        }

        $("input[type=radio]").change(function () {
            $(".url_facebook_api_div").hide();
            if (this.value === "api") {
                $(".url_facebook_api_div").show();
                $("#facebook-token-api").attr("readonly", false);
            } else {
                $("#facebook-token-api").attr("readonly", true);
            }
        });

        $("#add_pixel_plans").val(null).trigger("change");

        isChecked($("#modal-create-pixel .pixel-status"), true);
        isChecked($("#modal-create-pixel .checkout"), true);
        isChecked($("#modal-create-pixel .basic-data"), true);
        isChecked($("#modal-create-pixel .delivery"), true);
        isChecked($("#modal-create-pixel .coupon"), true);
        isChecked($("#modal-create-pixel .payment-info"), true);
        isChecked($("#modal-create-pixel .purchase-card"), true);
        isChecked($("#modal-create-pixel .purchase-boleto"), true);
        isChecked($("#modal-create-pixel .purchase-pix"), true);
        isChecked($("#modal-create-pixel .purchase-all"), true);
        isChecked($("#modal-create-pixel .upsell"), true);
        isChecked($("#modal-create-pixel .purchase-upsell"), true);

        $("#select-platform-pixel").hide();
        $("#configure-new-pixel").show();
    });

    function validateDataPixelForm(formData) {
        if (formData.name.length > 100) {
            alertCustom("error", "O campo Descrição permite apenas 100 caracteres");
            return false;
        }

        if (formData.name.length < 1) {
            alertCustom("error", "O campo Descrição é obrigatório");
            return false;
        }

        if (formData.code.length < 1) {
            alertCustom("error", "O campo Código é obrigatório");
            return false;
        }

        if (formData.platform == "google_adwords") {
            if (formData.conversional.length < 1) {
                alertCustom("error", "O campo de Rótulo de conversão é obrigatório");
                return false;
            }
        }

        if (formData.value_percentage_purchase_boleto.length > 3) {
            alertCustom("error", "O valore do campo % Valor Boleto está incorreto!");
            return false;
        }

        if (formData.value_percentage_purchase_pix.length > 3) {
            alertCustom("error", "O valore do campo % Valor PIX está incorreto!");
            return false;
        }

        if (
            formData.value_percentage_purchase_boleto.length > 0 &&
            (formData.value_percentage_purchase_boleto > 100 || formData.value_percentage_purchase_boleto < 10)
        ) {
            alertCustom("error", "O valores permitidos para o campo % Valor Boleto deve ser entre 10 e 100");
            return false;
        }

        if (
            formData.value_percentage_purchase_pix.length > 0 &&
            (formData.value_percentage_purchase_pix > 100 || formData.value_percentage_purchase_pix < 10)
        ) {
            alertCustom("error", "O valores permitidos para o campo % Valor PIX deve ser entre 10 e 100");
            return false;
        }

        if (formData.platform === "facebook" && formData.is_api === "api" && formData.facebook_token_api.length < 1) {
            alertCustom("error", "O campo Token Acesso API de Conversões é obrigatório");
            return false;
        }

        if (["taboola", "outbrain"].includes(formData.platform) && formData.purchase_event_name.length < 1) {
            alertCustom("error", "O campo Nome evento de conversão é obrigatório");
            return false;
        }

        if (formData.plans_apply == null) {
            alertCustom("error", "É obrigatório selecionar um ou mais planos");
            return false;
        }

        return true;
    }

    // Create
    $("#modal-create-pixel #single-event").on("change", function () {
        const switchValue = $("#modal-create-pixel #send_value_switch").parent();
        const sendValue = $("#modal-create-pixel .send-value");

        sendValue.prop("checked", false);
        if ($(this).val() !== "checkout") switchValue.addClass("disabled");
        else switchValue.removeClass("disabled");
    });

    // Edit
    $("#modal-edit-pixel #single-event-edit").on("change", function () {
        const switchValue = $("#modal-edit-pixel #send_value_switch-edit").parent();
        const sendValue = $("#modal-edit-pixel .send-value-edit");

        if ($(this).val() !== "checkout") {
            switchValue.addClass("disabled");
            sendValue.prop("checked", false);
        } else {
            switchValue.removeClass("disabled");
            sendValue.prop("checked", sendValue.hasClass("is-checked"));
        }
    });

    //Save Create new Pixel
    $("#modal-create-pixel #btn-store-pixel").on("click", function () {
        const formData = new FormData(document.querySelector("#modal-create-pixel  #form-register-pixel"));

        formData.append("status", $("#modal-create-pixel .pixel-status").is(":checked"));
        formData.append("checkout", $("#modal-create-pixel .checkout").is(":checked"));
        formData.append("basic_data", true); // $("#modal-create-pixel .basic-data").is(':checked'));
        formData.append("delivery", true); // $("#modal-create-pixel .delivery").is(':checked'));
        formData.append("coupon", true); // $("#modal-create-pixel .coupon").is(':checked'));
        formData.append("payment_info", true); // $("#modal-create-pixel .payment-info").is(':checked'));
        formData.append("purchase_card", $("#modal-create-pixel .purchase-card").is(":checked"));
        formData.append("purchase_boleto", $("#modal-create-pixel .purchase-boleto").is(":checked"));
        formData.append("purchase_pix", $("#modal-create-pixel .purchase-pix").is(":checked"));
        formData.append("purchase_all", false); //$("#modal-create-pixel .purchase-all").is(':checked'));
        formData.append("upsell", true); // $("#modal-create-pixel .upsell").is(':checked'));
        formData.append("purchase_upsell", true); // $("#modal-create-pixel .purchase-upsell").is(':checked'));
        formData.append("event_select", $("#modal-create-pixel #single-event").val());
        formData.append("send_value_checkout", $("#modal-create-pixel .send-value").is(":checked"));

        if (
            !validateDataPixelForm({
                name: formData.get("name"),
                platform: formData.get("platform"),
                is_api: formData.get("api-facebook"),
                code: formData.get("code"),
                conversional: formData.get("conversional"),
                value_percentage_purchase_boleto: formData.get("value_percentage_purchase_boleto"),
                value_percentage_purchase_pix: formData.get("value_percentage_purchase_pix"),
                facebook_token_api: formData.get("facebook-token-api"),
                purchase_event_name: formData.get("purchase-event-name"),
                plans_apply: formData.get("add_pixel_plans[]"),
            })
        ) {
            return false;
        }

        let codeSave = inputCodeByPlatform(
            formData.get("platform"),
            formData.get("code"),
            formData.get("conversional")
        );
        formData.set("code", codeSave);
        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/pixels",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal-create-pixel").modal("hide");
                alertCustom("success", response.message);
                atualizarPixel();
            },
        });
    });

    // Select Plans
    $("#add_pixel_plans").select2(Object.assign(selectPlan(), { dropdownParent: $("#modal-create-pixel") }));
    $(".edit-plans").select2(Object.assign(selectPlan(), { dropdownParent: $("#modal-edit-pixel") }));

    function selectPlan() {
        return {
            placeholder: "Nome do plano",
            multiple: true,
            language: {
                noResults: function () {
                    return "Nenhum plano encontrado";
                },
                searching: function () {
                    return "Procurando...";
                },
                loadingMore: function () {
                    return "Carregando mais planos...";
                },
            },
            ajax: {
                data: function (params) {
                    return {
                        list: "plan",
                        search: params.term,
                        project_id: projectId,
                        page: params.page || 1,
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
                    if (res.meta.current_page === 1) {
                        const allObject = {
                            id: "all",
                            name: "Qualquer plano",
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
    }

    $("#add_pixel_plans, .edit-plans").on("select2:select", function () {
        const planSelect = $(this);
        if (
            (planSelect.val().length > 1 && planSelect.val().includes("all")) ||
            (planSelect.val().includes("all") && planSelect.val() != "all")
        ) {
            planSelect.val("all").trigger("change");
        }
    });

    $(".btn-config-pixel").on("click", function () {
        $.ajax({
            method: "GET",
            url: "/api/projects/" + projectId + "/pixels/configs",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#metatag-verification-facebook").val(response.data.metatags_facebook);

                $("#modal-config-pixel").modal("show");
            },
        });
    });

    $(".btn-save-config-pixel").on("click", function () {
        $.ajax({
            method: "POST",
            url: "/api/projects/" + projectId + "/pixels/saveconfigs",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                "metatag-verification-facebook": $("#metatag-verification-facebook").val(),
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom("success", response.message);
                $("#modal-config-pixel").modal("hide");
            },
        });
    });
});
