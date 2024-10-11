$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-1);
    $("#tab-checkout").on("click", function () {
        loadData();
    });

    let checkout = null;
    let quillTextbar;
    let quillThanksPage;
    let cropper;
    let bs_modal;
    let image;

    function loadData() {
        loadOnAny(".checkout-container", false, {
            styles: {
                container: {
                    minHeight: "500px",
                },
            },
        });

        // ----------------- Editor de Texto --------------------
        var formats = ["bold", "italic", "underline"];

        quillTextbar = new Quill("#topbar_content", {
            modules: {
                toolbar: "#topbar_content_toolbar_container",
            },
            placeholder: "",
            theme: "snow",
            formats: formats,
        });

        const limit = 250;

        quillTextbar.on("text-change", function () {
            if (quillTextbar.getLength() > limit) {
                quillTextbar.deleteText(limit, quillTextbar.getLength());
            }
            // $("#save_changes").fadeIn("slow", "swing");
        });

        quillTextbar.on("selection-change", function (range, oldRange, source) {
            if (range === null && oldRange !== null) {
                $("#topbar_content").removeClass("focus-in");
            } else if (range !== null && oldRange === null) {
                $("#topbar_content").addClass("focus-in");
            }
        });

        quillThanksPage = new Quill("#post_purchase_message_content", {
            modules: {
                toolbar: "#post_purchase_message_content_toolbar_container",
            },
            theme: "snow",
            formats: formats,
        });

        quillThanksPage.on("text-change", function () {
            if (quillThanksPage.getLength() < limit) {
                $(".shop-message-preview-content").empty();
                $(".shop-message-preview-content").append($(quillThanksPage.root.innerHTML));
                $("#save_changes").fadeIn("slow", "swing");
            } else {
                quillThanksPage.deleteText(limit, quillThanksPage.getLength());
            }
        });

        quillThanksPage.on("selection-change", function (range, oldRange, source) {
            if (range === null && oldRange !== null) {
                $("#post_purchase_message_content").removeClass("focus-in");
            } else if (range !== null && oldRange === null) {
                $("#post_purchase_message_content").addClass("focus-in");
            }
        });

        $.ajax({
            method: "GET",
            url: "/api/checkouteditor/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (response) => {
                checkout = response.data;
                if (checkout) {
                    fillForm();

                    $("#checkout_editor").on("input", function () {
                        $("#save_changes").fadeIn("slow", "swing");
                    });

                    $(".sirius-select").on("change", function () {
                        $("#save_changes").fadeIn("slow", "swing");
                    });
                }

                if (checkout.checkout_favicon_enabled == 1) {
                    $("#checkout_editor #checkout_favicon_enabled").prop("checked", true);
                    $("#checkout_editor #checkout_favicon_enabled").prop("value", 1);
                    $(".favicon-content").removeClass("low-opacity");
                } else {
                    $("#checkout_editor #checkout_favicon_enabled").prop("checked", false);
                    $("#checkout_editor #checkout_favicon_enabled").prop("value", 0);
                    $(".favicon-content").addClass("low-opacity");
                }

                if (checkout.checkout_favicon_type == "1") {
                    $("#favicon_logo").attr("checked", true);
                } else {
                    $("#favicon_uploaded").attr("checked", true);
                    $("#upload_favicon").removeClass("low-opacity");
                }

                if (checkout.checkout_favicon) {
                    $("#has_checkout_favicon").val("true");
                } else {
                    $("#has_checkout_favicon").val("false");
                }

                setTimeout(() => {
                    $("#save_changes").hide();
                }, 0);

                loadOnAny(".checkout-container", true);
            },
            error: (response) => {
                errorAjaxResponse(response);
                $("#checkout_editor").addClass("low-opacity");

                $("#checkout_logo").dropify({
                    messages: {
                        default: "",
                        replace: "",
                        error: "",
                    },
                    error: {
                        fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
                        fileExtension: "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
                        minWidth: "Largura mínima: 64px.",
                        minHeight: "Altura mínima: 64px.",
                    },
                    tpl: {
                        message:
                            '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Clique ou arraste e solte aqui</span></p></div>',
                        clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
                    },
                    imgFileExtensions: ["png", "jpg", "jpeg"],
                });

                $("#checkout_favicon").dropify({
                    messages: {
                        default: "",
                        replace: "",
                    },
                    error: {
                        fileSize: "",
                        fileExtension: "",
                    },
                    tpl: {
                        message: '<div class="dropify-message"><span class="file-icon" /></div>',
                    },
                    imgFileExtensions: ["png", "jpg", "jpeg", "ico"],
                });

                $("#checkout_banner").dropify({
                    messages: {
                        default: "",
                        replace: "",
                        remove: "Remover",
                        error: "",
                    },
                    error: {
                        fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
                        fileExtension: "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
                    },
                    tpl: {
                        message:
                            '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Faça upload do seu banner</span></p></div>',
                        clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
                    },
                    imgFileExtensions: ["png", "jpg", "jpeg"],
                });

                loadOnAny(".checkout-container", true);
            },
        });

        $("#cancel_button").on("click", function () {
            fillForm(checkout);
            $('.company-navbar').val($('#checkout_editor #companies').val()).change();
            $("#save_changes").fadeOut("slow", "swing");
        });
    }

    function fillForm() {
        if (checkout.checkout_step_type == 1) {
            $("#checkout_editor #number_steps").prop("checked", true);
        } else {
            $("#checkout_editor #icon_steps").prop("checked", true);
        }

        if (checkout.checkout_custom_border_radius == 1) {
            $("#checkout_editor #custom_border_radius").prop("checked", false);
        } else {
            $("#checkout_editor #custom_border_radius").prop("checked", true);
        }

        if (checkout.checkout_expanded_resume == 1) {
            $("#checkout_editor #expanded_resume").prop("checked", false);
        } else {
            $("#checkout_editor #expanded_resume").prop("checked", true);
        }

        if (checkout.checkout_custom_footer_enabled == 1) {
            $("#checkout_editor #custom_footer_enabled").prop("checked", false);
            $("#checkout_editor .custom_footer_content").hide();
            $("#checkout_editor .custom_footer_content").val();
        } else {
            $("#checkout_editor #custom_footer_enabled").prop("checked", true);
            $("#checkout_editor .custom_footer_content").show();
            $("#checkout_editor #custom_footer_message").val(checkout.checkout_custom_footer_message);
        }

        if (checkout.checkout_type_enum == 1) {
            $("#checkout_editor #checkout_type_steps").prop("checked", true);
            $("#checkout_editor .visual-content-left").addClass("three-steps");
            $("#checkout_editor .visual-content-left").removeClass("unique");
            $("#checkout_editor .visual-content-mobile").addClass("three-steps");
            $("#checkout_editor .visual-content-mobile").removeClass("unique");
            $("#checkout_editor .steps-lines").slideDown("slow", "swing");
            $("#checkout_editor #finish_button_preview_desktop_visual").slideDown("slow", "swing");
            $("#checkout_editor #finish_button_preview_mobile_visual").slideDown("slow", "swing");
        } else {
            $("#checkout_editor #checkout_type_unique").prop("checked", true);
            $("#checkout_editor .visual-content-left").removeClass("three-steps");
            $("#checkout_editor .visual-content-left").addClass("unique");
            $("#checkout_editor .visual-content-mobile").removeClass("three-steps");
            $("#checkout_editor .visual-content-mobile").addClass("unique");
            $("#checkout_editor .steps-lines").slideUp("slow", "swing");
            $("#checkout_editor #finish_button_preview_desktop_visual").slideUp("slow", "swing");
            $("#checkout_editor #finish_button_preview_mobile_visual").slideUp("slow", "swing");
        }

        if (checkout.checkout_logo_enabled == 1) {
            $("#checkout_editor #checkout_logo_enabled").prop("checked", true);
            $("#checkout_editor #checkout_logo_enabled").prop("value", 1);
            $(".logo-content").removeClass("low-opacity");
            $("#checkout_editor .logo-preview-container").show();
        } else {
            $("#checkout_editor #checkout_logo_enabled").prop("checked", false);
            $("#checkout_editor #checkout_logo_enabled").prop("value", 0);
            $(".logo-content").addClass("low-opacity");
            $("#checkout_editor .logo-preview-container").hide();
        }

        if (checkout.checkout_logo) {
            // replacePreview("checkout_logo", checkout.checkout_logo, "Image.jpg");
            $("#checkout_logo").attr("src", checkout.checkout_logo);
            $("#logo_preview_mobile").attr("src", checkout.checkout_logo);
            $("#logo_preview_desktop").attr("src", checkout.checkout_logo);
            $("#has_checkout_logo").val("true");
            $("#logo_preview_mobile").fadeIn("slow");
            $("#logo_preview_desktop").fadeIn("slow");
        } else {
            $("#logo_preview_mobile").fadeOut("slow");
            $("#logo_preview_desktop").fadeOut("slow");
        }

        logoImageDropfy();
        favicoImageDropfy();
        bannerImageDropfy();


        //  ----------------- Crop Modal ----------------------

        var $dataZoom = $("#dataZoom");
        bs_modal
            .on("shown.bs.modal", function () {
                cropper = new Cropper(image, {
                    highlight: false,
                    movable: false,
                    viewMode: 3,
                    aspectRatio: 1280 / 198,
                    zoom: function (e) {
                        var ratio = Math.round(e.ratio * 1000) / 10;
                        $dataZoom.text(ratio);
                    },
                });
            })
            .on("hidden.bs.modal", function () {
                cropper?.destroy();
                cropper = null;
            });

        $("#zoom-in").on("click", () => {
            cropper.zoom(0.1);
        });

        $("#zoom-out").on("click", () => {
            cropper.zoom(-0.1);
        });

        var lastNum;
        $("#zoom-slide").on("input change", () => {
            if (lastNum < $("#zoom-slide").val()) {
                cropper.zoom(0.1);
            } else {
                cropper.zoom(-0.1);
            }
            lastNum = $("#zoom-slide").val();
        });

        $("#crop-reset").on("click", () => {
            cropper.reset();
            $("#zoom-slide").val(0);
        });

        $(".img-profile input")
            .on("click", function (e) {
                e.stopPropagation();
            })
            .on("change", function () {
                let file = this.files[0];
                let reader = new FileReader();
                reader.onload = function (e) {
                    let img = $(".img-profile").addClass("cropping").find("img").attr("src", e.target.result);
                    cropper = new Cropper(img[0], {
                        aspectRatio: 1,
                        minContainerWidth: 150,
                        minContainerHeight: 150,
                    });
                    $("#btn-crop-cancel, #btn-crop").show();
                };
                reader.readAsDataURL(file);
            });

        $("#button-crop").on("click", function () {
            if (cropper) {
                var canvas = cropper.getCroppedCanvas();
                var src = canvas?.toDataURL("image/png", 0.7);

                $("#preview_banner_img_mobile").attr("src", src);
                $("#preview_banner_img_desktop").attr("src", src);

                $("#has_checkout_banner").val("true");

                $("#preview_banner_img_mobile").fadeIn("slow");
                $("#preview_banner_img_desktop").fadeIn("slow");

                // replacePreview("checkout_banner", src, "checkout_banner.jpg");

                cropper.getCroppedCanvas().toBlob(
                    (blob) => {
                        let dt = new DataTransfer();
                        let file = new File([blob], "banner." + blob.type.split("/")[1]);
                        dt.items.add(file);
                        document.querySelector("#checkout_banner").files = dt.files;
                    },
                    "image/jpeg",
                    0.8
                );
            }

            bs_modal.modal("hide");
        });

        $("#button-cancel-crop").on("click", function () {
            $("#checkout_banner").parent().find(".dropify-clear").trigger("click");
        });

        // replacePreview("checkout_banner", checkout.checkout_banner, "Image.jpg");

        if (checkout.checkout_banner_enabled) {
            $("#checkout_editor #checkout_banner_enabled").prop("checked", true);
            $("#checkout_editor #checkout_banner_enabled").prop("value", 1);
            $("#checkout_editor .banner-top-content").show();
            $("#checkout_editor .preview-banner").show();
            $("#checkout_editor #banner_type").show();
            $(".logo-div").addClass("has-banner");
            $(".logo-preview-container").addClass("has-banner");
            $(".menu-bar-mobile").hide("slow");
            // $(".purchase-menu-mobile").fadeIn("slow");
        } else {
            $("#checkout_editor #checkout_banner_enabled").prop("checked", false);
            $("#checkout_editor #checkout_banner_enabled").prop("value", 0);
            $("#checkout_editor .banner-top-content").hide();
            $("#checkout_editor .preview-banner").hide();
            $("#checkout_editor #banner_type").hide();
            $(".logo-div").removeClass("has-banner");
            $(".logo-preview-container").removeClass("has-banner");
            $(".menu-bar-mobile").show("slow");
            // $(".purchase-menu-mobile").fadeOut("slow");
        }

        if (checkout.checkout_banner_type === 1) {
            $("#checkout_editor #banner_type_wide").prop("checked", true);
            $(".preview-banner").removeClass("retangle-banner");
            $(".preview-banner").addClass("wide-banner");
            $(".logo-div").removeClass("has-retangle-banner");
        } else {
            $("#checkout_editor #banner_type_square").prop("checked", true);
            $(".preview-banner").addClass("retangle-banner");
            $(".preview-banner").removeClass("wide-banner");
            $(".logo-div").addClass("has-retangle-banner");
        }

        if (checkout.countdown_enabled) {
            $("#checkout_editor #countdown_enabled").prop("checked", true);
            $("#checkout_editor #countdown_enabled").prop("value", 1);
            $("#checkout_editor .countdown-content").show();
            $("#checkout_editor .countdown-preview").show();
        } else {
            $("#checkout_editor #countdown_enabled").prop("checked", false);
            $("#checkout_editor #countdown_enabled").prop("value", 0);
            $("#checkout_editor .countdown-content").hide();
            $("#checkout_editor .countdown-preview").hide();
        }

        $("#checkout_editor #countdown_time").val(checkout.countdown_time || 15);

        $("#checkout_editor #countdown_finish_message").val(
            checkout.countdown_finish_message ||
            "Seu tempo acabou! Você precisa finalizar sua compra agora para ganhar o desconto extra."
        );

        if (checkout.topbar_enabled) {
            $("#checkout_editor #topbar_enabled").prop("checked", true);
            $("#checkout_editor #topbar_enabled").prop("value", 1);
            $("#checkout_editor .textbar-content").show();
            $("#checkout_editor .textbar-preview").show();
        } else {
            $("#checkout_editor #topbar_enabled").prop("checked", false);
            $("#checkout_editor #topbar_enabled").prop("value", 0);
            $("#checkout_editor .textbar-content").hide();
            $("#checkout_editor .textbar-preview").hide();
        }

        quillTextbar.root.innerHTML =
            checkout.topbar_content ||
            "<p>Aproveite o <b>desconto extra</b> ao comprar no <u>Cartão ou pelo PIX!</u> É por <b>tempo limitado</b>.</p>";

        if (checkout.notifications_enabled) {
            $("#checkout_editor #notifications_enabled").prop("checked", true);
            $("#checkout_editor #notifications_enabled").prop("value", 1);
            $("#checkout_editor .sales-notifications-content").show();
        } else {
            $("#checkout_editor #notifications_enabled").prop("checked", false);
            $("#checkout_editor #notifications_enabled").prop("value", 0);
            $("#checkout_editor .sales-notifications-content").hide();
        }

        switch (checkout.notifications_interval) {
            case 15:
                $("#checkout_editor #notifications_interval_15").prop("checked", true);
                break;
            case 30:
                $("#checkout_editor #notifications_interval_30").prop("checked", true);
                break;
            case 45:
                $("#checkout_editor #notifications_interval_45").prop("checked", true);
                break;
            case 60:
                $("#checkout_editor #notifications_interval_60").prop("checked", true);
                break;
            default:
                $("#checkout_editor #notifications_interval_15").prop("checked", true);
                break;
        }

        if (checkout.notification_buying_enabled) {
            $("#checkout_editor #notification_buying_enabled").prop("checked", true);
            $("#checkout_editor #notification_buying_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #notification_buying_enabled").prop("checked", false);
            $("#checkout_editor #notification_buying_enabled").prop("value", 0);
        }

        if (checkout.notification_bought_30_minutes_enabled) {
            $("#checkout_editor #notification_bought_30_minutes_enabled").prop("checked", true);
            $("#checkout_editor #notification_bought_30_minutes_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #notification_bought_30_minutes_enabled").prop("checked", false);
            $("#checkout_editor #notification_bought_30_minutes_enabled").prop("value", 0);
        }

        if (checkout.notification_bought_last_hour_enabled) {
            $("#checkout_editor #notification_bought_last_hour_enabled").prop("checked", true);
            $("#checkout_editor #notification_bought_last_hour_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #notification_bought_last_hour_enabled").prop("checked", false);
            $("#checkout_editor #notification_bought_last_hour_enabled").prop("value", 0);
        }

        if (checkout.notification_just_bought_enabled) {
            $("#checkout_editor #notification_just_bought_enabled").prop("checked", true);
            $("#checkout_editor #notification_just_bought_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #notification_just_bought_enabled").prop("checked", false);
            $("#checkout_editor #notification_just_bought_enabled").prop("value", 0);
        }

        if (
            checkout.notification_buying_enabled &&
            checkout.notification_bought_30_minutes_enabled &&
            checkout.notification_bought_last_hour_enabled &&
            checkout.notification_just_bought_enabled
        ) {
            $("#checkout_editor #notification-table .selectable-all").prop("checked", true);
        }

        $("#checkout_editor #notification_buying_minimum").val(checkout.notification_buying_minimum || 0);
        $("#checkout_editor #notification_bought_30_minutes_minimum").val(
            checkout.notification_bought_30_minutes_minimum || 0
        );
        $("#checkout_editor #notification_bought_last_hour_minimum").val(
            checkout.notification_bought_last_hour_minimum || 0
        );
        $("#checkout_editor #notification_just_bought_minimum").val(checkout.notification_just_bought_minimum || 0);

        const form = document.querySelector("#checkout_editor");
        const selectableCheckboxes = form.querySelectorAll(".selectable-notification:checked");

        if (selectableCheckboxes.length > 0 && selectableCheckboxes.length < 4) {
            $("#selectable-all-notification").addClass("dash-check");
            $("#selectable-all-notification").prop("checked", true);
        }

        if (selectableCheckboxes.length == 0) {
            $("#selectable-all-notification").prop("checked", false);
            $("#selectable-all-notification").removeClass("dash-check");
        }

        if (selectableCheckboxes.length == 4) {
            $("#selectable-all-notification").removeClass("dash-check");
            $("#selectable-all-notification").prop("checked", true);
        }

        if (checkout.social_proof_enabled) {
            $("#checkout_editor #social_proof_enabled").prop("checked", true);
            $("#checkout_editor #social_proof_enabled").prop("value", 1);
            $("#checkout_editor .social-proof-content").show();
        } else {
            $("#checkout_editor #social_proof_enabled").prop("checked", false);
            $("#checkout_editor #social_proof_enabled").prop("value", 0);
            $("#checkout_editor .social-proof-content").hide();
        }

        $("#checkout_editor #social_proof_message").val(
            checkout.social_proof_message ||
            "Outras { num-visitantes } pessoas estão finalizando a compra neste momento."
        );
        $("#checkout_editor #social_proof_minimum").val(checkout.social_proof_minimum || 0);
        $("#checkout_editor #invoice_description").val(checkout.invoice_description || "");

        for (let company of checkout.companies) {
            const document = (company.document.replace(/\D/g, '').length > 11 ? 'CNPJ: ' : 'CPF: ') + company.document;
            if (company.status != "pending") {
                if (company.name.length > 20)
                    companyName = company.name.substring(0, 20) + '...';
                else
                    companyName = company.name;
                $("#checkout_editor #companies").append(`<option value="${company.id}" ${company.id === checkout.company_id ? "selected" : ""} data-toggle="tooltip" title="${document}" >
                                                            ${companyName}
                                                         </option>`);
            }
        }

        if (checkout.cpf_enabled) {
            $("#checkout_editor #cpf_enabled").prop("checked", true);
            $("#checkout_editor #cpf_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #cpf_enabled").prop("checked", false);
            $("#checkout_editor #cpf_enabled").prop("value", 0);
        }

        if (checkout.cnpj_enabled) {
            $("#checkout_editor #cnpj_enabled").prop("checked", true);
            $("#checkout_editor #cnpj_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #cnpj_enabled").prop("checked", false);
            $("#checkout_editor #cnpj_enabled").prop("value", 0);
        }

        if (checkout.bank_slip_enabled) {
            $("#checkout_editor #bank_slip_enabled").prop("checked", true);
            $("#checkout_editor #bank_slip_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #bank_slip_enabled").prop("checked", false);
            $("#checkout_editor #bank_slip_enabled").prop("value", 0);
        }

        if (checkout.pix_enabled) {
            $("#checkout_editor #pix_enabled").prop("checked", true);
            $("#checkout_editor #pix_enabled").prop("value", 1);
        }
        {
            $("#checkout_editor #pix_enabled").prop("checked", false);
            $("#checkout_editor #pix_enabled").prop("value", 0);
        }

        if (checkout.credit_card_enabled) {
            $("#checkout_editor #credit_card_enabled").prop("checked", true);
            $("#checkout_editor #credit_card_enabled").prop("value", 1);
            $(".credit-card-container").show("slow", "swing");
            $(".accepted-payment-card-creditcard").slideDown("slow", "swing");
        } else {
            $("#checkout_editor #credit_card_enabled").prop("checked", false);
            $("#checkout_editor #credit_card_enabled").prop("value", 0);
            $(".credit-card-container").hide("slow", "swing");
            $(".accepted-payment-card-creditcard").slideUp("slow", "swing");
        }

        if (checkout.bank_slip_enabled) {
            $("#checkout_editor #bank_slip_enabled").prop("checked", true);
            $("#checkout_editor #bank_slip_enabled").prop("value", 1);
            $(".bank-billet-container").show("slow", "swing");
            $(".accepted-payment-bank-billet").slideDown("slow", "swing");
        } else {
            $("#checkout_editor #bank_slip_enabled").prop("checked", false);
            $(".bank-billet-container").hide("slow", "swing");
            $(".accepted-payment-bank-billet").slideUp("slow", "swing");
        }

        if (checkout.pix_enabled) {
            $("#checkout_editor #pix_enabled").prop("checked", true);
            $("#checkout_editor #pix_enabled").prop("value", 1);
            $(".pix-container").show("slow", "swing");
            $(".accepted-payment-pix").slideDown("slow", "swing");
        } else {
            $("#checkout_editor #pix_enabled").prop("checked", false);
            $(".pix-container").hide("slow", "swing");
            $(".accepted-payment-pix").slideUp("slow", "swing");
        }

        if (checkout.quantity_selector_enabled) {
            $("#checkout_editor #count-selector-label").addClass("active");
            $("#checkout_editor #quantity_selector_enabled").prop("checked", true);
            $("#checkout_editor #quantity_selector_enabled").prop("value", 1);
        } else {
            $("#checkout_editor #count-selector-label").removeClass("active");
            $("#checkout_editor #quantity_selector_enabled").prop("checked", false);
            $("#checkout_editor #quantity_selector_enabled").prop("value", 0);
        }

        if (checkout.email_required) {
            $("#checkout_editor #checkout-email-label").addClass("active");
            $("#checkout_editor #email_required").prop("checked", true);
            $("#checkout_editor #email_required").prop("value", 1);
        } else {
            $("#checkout_editor #checkout-email-label").removeClass("active");
            $("#checkout_editor #email_required").prop("checked", false);
            $("#checkout_editor #email_required").prop("value", 0);
        }

        $("#checkout_editor #bank_slip_due_days").val(checkout.bank_slip_due_days || 1);

        $("#checkout_editor #installments_limit")
            .val(checkout.installments_limit || 1)
            .change();

        $("#checkout_editor #interest_free_installments")
            .val(checkout.interest_free_installments || 1)
            .change();

        $("#checkout_editor #preselected_installment")
            .val(checkout.preselected_installment || 1)
            .change();

        $("#checkout_editor #automatic_discount_credit_card").val(checkout.automatic_discount_credit_card || 0);

        $("#checkout_editor #automatic_discount_bank_slip").val(checkout.automatic_discount_bank_slip || 0);

        $("#checkout_editor #automatic_discount_pix").val(checkout.automatic_discount_pix || 0);

        if (checkout.post_purchase_message_enabled) {
            $("#checkout_editor #post_purchase_message_enabled").prop("checked", true);
            $("#checkout_editor #post_purchase_message_enabled").prop("value", 1);
            $(".thanks-page-content").show("slow", "swing");
            $(".shop-message-preview").slideDown("slow", "swing");
        } else {
            $("#checkout_editor #post_purchase_message_enabled").prop("checked", false);
            $("#checkout_editor #post_purchase_message_enabled").prop("value", 0);
            $(".thanks-page-content").hide("slow", "swing");
            $(".shop-message-preview").slideUp("slow", "swing");
        }

        $("#checkout_editor #post_purchase_message_title").val(
            checkout.post_purchase_message_title || "Obrigado por comprar conosco!"
        );
        $(".shop-message-preview-title").empty();
        $(".shop-message-preview-title").append(
            checkout.post_purchase_message_title || "Obrigado por comprar conosco!"
        );

        quillThanksPage.root.innerHTML =
            checkout.post_purchase_message_content ||
            "<p>Aproveite o <b>desconto extra</b> ao comprar no <u>Cartão ou pelo PIX!</u> É por <b>tempo limitado</b>.</p>";

        $(".shop-message-preview-content").empty();
        $(".shop-message-preview-content").append(checkout.post_purchase_message_content);

        if (checkout.whatsapp_enabled == 1) {
            $("#checkout_editor #whatsapp_enabled").prop("checked", true);
            $("#checkout_editor #whatsapp_enabled").prop("value", 1);
            $(".whatsapp-content").show("slow", "swing");
            $(".whatsapp-preview").slideDown("slow", "swing");
        } else {
            $("#checkout_editor #whatsapp_enabled").prop("checked", false);
            $("#checkout_editor #whatsapp_enabled").prop("value", 0);
            $(".whatsapp-content").hide("slow", "swing");
            $(".whatsapp-preview").slideUp("slow", "swing");
        }

        $("#checkout_editor #support_phone").val(checkout.support_phone || "");

        if (checkout.support_phone_verified == 1) {
            $("#verify_phone_open").hide();
            $("#verified_phone_open").show();
            $("#remove_phone").show();
        } else {
            $("#verify_phone_open").show();
            $("#verified_phone_open").hide();
            $("#remove_phone").hide();
        }

        if (checkout.theme_enum == 0 || !checkout.theme_enum) {
            $(":root").css("--primary-color", checkout.color_primary);
            $(":root").css("--secondary-color", checkout.color_secondary);
            $(":root").css("--finish-button-color", checkout.color_buy_button);

            $("#color_primary").val(checkout.color_primary);
            $("#color_secondary").val(checkout.color_secondary);
            $("#color_buy_button").val(checkout.color_buy_button);

            $("#checkout_editor #theme_ready_enabled").prop("checked", true);
            $("#checkout_editor #theme_ready_enabled").prop("value", 1);
            $(".custom-theme-content").show("slow", "swing");
            $(".theme-ready-first-line").addClass("low-opacity");
            $(".theme-ready-second-line").hide("slow", "swing");
        } else {
            $("#checkout_editor #theme_ready_enabled").prop("checked", false);
            $("#checkout_editor #theme_ready_enabled").prop("value", 0);
            $("input[name=theme_enum][value=" + checkout.theme_enum + "]").attr("checked", true);

            $(":root").css("--primary-color", checkout.color_primary);
            $(":root").css("--secondary-color", checkout.color_secondary);
            $(":root").css("--finish-button-color", checkout.color_buy_button);

            $(".custom-theme-content").hide("slow", "swing");
            $(".theme-ready-first-line").removeClass("low-opacity");
            $(".theme-ready-second-line").show("slow", "swing");
        }

        $("#checkout_editor_id").val(checkout.id);

        $("#upload_favicon .dropify-error").css("display", "none");

        $(".dropify-clear").hide();
    }

    function logoImageDropfy() {
        let drEventLogo = $("#checkout_logo").dropify({
            error: {
                fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
                fileExtension: "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
                minWidth: "Largura mínima: 64px.",
                minHeight: "Altura mínima: 64px.",
            },
            tpl: {
                message:
                    '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Clique ou arraste e solte aqui</span></p></div>',
                clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
            },
            imgFileExtensions: ["png", "jpg", "jpeg"],
            defaultFile: checkout.checkout_logo ? checkout.checkout_logo : "",
        });

        drEventLogo.on("dropify.fileReady", function (event, element) {
            let files = event.target.files;
            var done = function (url) {
                $("#logo_preview_mobile").attr("src", url);
                $("#logo_preview_desktop").attr("src", url);
                $("#has_checkout_logo").val("true");

                $("#logo_preview_mobile").fadeIn("slow");
                $("#logo_preview_desktop").fadeIn("slow");
            };
            if (files && files.length > 0) {
                file = files[0];
                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        drEventLogo.on("dropify.errors", function (event, element) {
            $("#logo_preview_mobile").attr("src", "");
            $("#logo_preview_desktop").attr("src", "");

            $("#logo_preview_mobile").fadeOut("slow");
            $("#logo_preview_desktop").fadeOut("slow");

            $("#checkout_logo_error").fadeIn("slow", "linear");
            $("#has_checkout_logo").val("false");

            $("#has_checkout_logo").val("false");
        });

        drEventLogo.on("dropify.afterClear", function (event, element) {
            if (checkout.checkout_logo_enabled == 1) {
                if ($("#has_checkout_logo").val() == "true") {
                    validated = true;
                } else {
                    scrollToElement("upload_logo");
                    $("#checkout_logo_error").show("slow", "linear");
                    return false;
                }
            }
            $("#has_checkout_logo").val("false");
        });
    }

    function favicoImageDropfy() {
        var drEventFavicon = $("#checkout_favicon").dropify({
            messages: {
                default: "",
                replace: "",
            },
            error: {
                fileSize: "",
                fileExtension: "",
            },
            tpl: {
                message: '<div class="dropify-message"><span class="file-icon" /></div>',
            },
            imgFileExtensions: ["png", "jpg", "jpeg", "ico"],
            defaultFile: checkout.checkout_favicon ? checkout.checkout_favicon : "",
        });

        drEventFavicon.on("dropify.errors", function (event, element) {
            $("#checkout_favicon_error").fadeIn("slow", "linear");
            $("#has_checkout_favicon").val("false");
        });

        drEventFavicon.on("dropify.fileReady", function (event, element) {
            $("#checkout_favicon_error").hide();
            $("#has_checkout_favicon").val("true");
        });
    }

    function bannerImageDropfy() {
        if (checkout.checkout_banner) {
            $("#preview_banner_img_desktop").attr("src", checkout.checkout_banner);
            $("#preview_banner_img_mobile").attr("src", checkout.checkout_banner);
            $("#has_checkout_banner").val("true");
        }

        var drEventBanner = $("#checkout_banner").dropify({
            messages: {
                default: "",
                replace: "",
                remove: "Remover",
                error: "",
            },
            error: {
                fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
                fileExtension: "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
            },
            tpl: {
                message:
                    '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Faça upload do seu banner</span></p></div>',
                clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
            },
            imgFileExtensions: ["png", "jpg", "jpeg"],
            defaultFile: checkout.checkout_banner ? checkout.checkout_banner : "",
        });

        bs_modal = $("#modal_banner");
        image = document.getElementById("cropped_image");
        let reader, file;

        drEventBanner.on("dropify.fileReady", function (event, element) {
            let files = event.target.files;
            let done = function (url) {
                image.src = url;
                bs_modal.modal("show");
            };

            if (files && files.length > 0) {
                file = files[0];

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        drEventBanner.on("dropify.errors", function (event, element) {
            $("#preview_banner_img_mobile").attr("src", "");
            $("#preview_banner_img_desktop").attr("src", "");
            $("#has_checkout_banner").val("false");
            $("#preview_banner_img_mobile").fadeOut("slow");
            $("#preview_banner_img_desktop").fadeOut("slow");
        });

        drEventBanner.on("dropify.afterClear", function (event, element) {
            $("#has_checkout_banner").val("false");
        });

        $(".dropify-clear").hide();
    }

    $(document).on("click", "#update_checkout_editor", function (e) {
        e.preventDefault();
        submitForm();
    });

    function submitForm() {
        $(".error-input").removeClass("error-input");
        $(".checkout-error").hide();

        $("#save_load").fadeIn("slow", "linear");
        $("#save_changes").fadeOut("slow", "linear");

        $(".select-type").addClass("low-opacity");
        $(".visual").addClass("low-opacity");
        $(".payment").addClass("low-opacity");
        $(".post-purchase-pages").addClass("low-opacity");
        $(".preview").addClass("low-opacity");

        let checkoutForm = document.getElementById("checkout_editor");
        let formData = new FormData(checkoutForm);

        if (quillTextbar.getText().trim().length === 0) {
            formData.append("topbar_content", "");
        } else {
            formData.append("topbar_content", $("#topbar_content").children().html());
        }

        if (quillThanksPage.getText().trim().length === 0) {
            formData.append("post_purchase_message_content", "");
        } else {
            formData.append(
                "post_purchase_message_content",
                $("#post_purchase_message_content").children().html()
            );
        }

        if (!formData.get("automatic_discount_credit_card")) {
            $("#automatic_discount_credit_card").val(0);
            formData.append("automatic_discount_credit_card", 0);
        }

        if (!formData.get("automatic_discount_bank_slip")) {
            $("#automatic_discount_bank_slip").val(0);
            formData.append("automatic_discount_bank_slip", 0);
        }

        if (!formData.get("automatic_discount_pix")) {
            $("#automatic_discount_pix").val(0);
            formData.append("automatic_discount_pix", 0);
        }

        if (!$("#theme_ready_enabled").is(":checked")) {
            var primaryColor = $('label[for="' + $("input[name=theme_enum]:checked").attr("id") + '"]')
                .children(".theme-primary-color")
                .attr("data-color");
            var secondaryColor = $('label[for="' + $("input[name=theme_enum]:checked").attr("id") + '"]')
                .children(".theme-secondary-color")
                .attr("data-color");

            formData.append("color_primary", primaryColor);
            formData.append("color_secondary", secondaryColor);
            formData.append("color_buy_button", primaryColor);
        } else {
            formData.set("theme_enum", 0);
        }

        if ($("#default_finish_color").is(":checked")) {
            formData.append("color_buy_button", "#23d07d");
        }

        $("#checkout_editor input[type=checkbox]:not(:checked)").map(function () {
            formData.append($(this).attr("name"), $(this).is(":checked") ? 1 : 0);
        });

        $("input[name='gender']:checked").val()

        if ($("input[name='type_steps']:checked").val() == 1) {
            formData.append("checkout_step_type", 1);
        } else {
            formData.append("checkout_step_type", 2);
        }

        if ($("#custom_border_radius").is(":checked")) {
            formData.append("checkout_custom_border_radius", 2);
        } else {
            formData.append("checkout_custom_border_radius", 1);
        }

        if ($("#expanded_resume").is(":checked")) {
            formData.append("checkout_expanded_resume", 2);
        } else {
            formData.append("checkout_expanded_resume", 1);
        }

        if ($("#custom_footer_enabled").is(":checked")) {
            formData.append("checkout_custom_footer_enabled", 2);
            formData.append("checkout_custom_footer_message", $("#custom_footer_message").val());
        } else {
            formData.append("checkout_custom_footer_enabled", 1);
        }


        if (validadeForm(formData)) {
            $.ajax({
                method: "POST",
                url: "/api/checkouteditor/" + checkout.id,
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                data: formData,
                success: function () {
                    $(".select-type").removeClass("low-opacity");
                    $(".visual").removeClass("low-opacity");
                    $(".payment").removeClass("low-opacity");
                    $(".post-purchase-pages").removeClass("low-opacity");
                    $(".preview").removeClass("low-opacity");

                    $("#save_load").fadeOut("slow", "linear");
                    $("#save_success").fadeIn("slow", "linear");

                    setTimeout(function () {
                        $("#save_success").fadeOut("slow", "linear");
                    }, 4000);
                },
                error: function (response) {
                    $('.select-type').removeClass('low-opacity')
                    $('.visual').removeClass('low-opacity')
                    $('.payment').removeClass('low-opacity')
                    $('.post-purchase-pages').removeClass('low-opacity')
                    $('.preview').removeClass('low-opacity')

                    $('#save_load').fadeOut('slow', 'linear');
                    $('#save_error').fadeIn('slow', 'linear');

                    if (response.responseJSON.message && response.responseJSON.message.search(/demo/) > 0) {
                        $('#save_error p').html(response.responseJSON.message);
                    }

                    setTimeout(function () {
                        $('#save_error').fadeOut('slow', 'linear');
                    }, 4000);
                },
            });
        } else {
            $(".select-type").removeClass("low-opacity");
            $(".visual").removeClass("low-opacity");
            $(".payment").removeClass("low-opacity");
            $(".post-purchase-pages").removeClass("low-opacity");
            $(".preview").removeClass("low-opacity");

            $("#save_load").fadeOut("slow", "linear");
            $("#save_empty_fields").fadeIn("slow", "linear");

            setTimeout(function () {
                $("#save_empty_fields").fadeOut("slow", "linear");
            }, 4000);
        }
    }

    function validadeForm(form) {
        var validated = false;

        if (form.get("countdown_enabled") == "1") {
            if (form.get("countdown_time") != "" && form.get("countdown_time") != null) {
                validated = true;
            } else {
                $("#countdown_time").addClass("error-input");
                $("#countdown_time_error").show("slow", "linear");
                scrollToElement("countdown_time");
                return false;
            }

            if (form.get("countdown_finish_message") != "" && form.get("countdown_finish_message") != null) {
                validated = true;
            } else {
                $("#countdown_finish_message").addClass("error-input");
                $("#countdown_finish_message_error").show("slow", "linear");
                scrollToElement("countdown_finish_message");
                return false;
            }
        }

        if (form.get("topbar_enabled") == "1") {
            if (quillTextbar.getText().trim().length > 0) {
                validated = true;
            } else {
                $("#topbar_content").addClass("error-input");
                scrollToElement("topbar_content");
                $("#topbar_content_error").show("slow", "linear");
                return false;
            }
        }

        if (form.get("notifications_enabled") == "1") {
            if (form.get("notification_buying_minimum") != "") {
                validated = true;
            } else {
                $("#notification_buying_minimum").addClass("error-input");
                $("#notification_buying_minimum_error").show("slow", "linear");
                scrollToElement("notification_buying_minimum");
                $("#notification_error").show("slow", "linear");
                return false;
            }

            if (form.get("notification_bought_30_minutes_minimum") != "") {
                validated = true;
            } else {
                $("#notification_bought_30_minutes_minimum").addClass("error-input");
                $("#notification_error").show("slow", "linear");
                scrollToElement("notification_bought_30_minutes_minimum");
                return false;
            }

            if (form.get("notification_bought_last_hour_minimum") != "") {
                validated = true;
            } else {
                $("#notification_bought_last_hour_minimum").addClass("error-input");
                $("#notification_error").show("slow", "linear");
                scrollToElement("notification_bought_last_hour_minimum");

                return false;
            }

            if (form.get("notification_just_bought_minimum") != "") {
                validated = true;
            } else {
                $("#notification_just_bought_minimum").addClass("error-input");
                $("#notification_error").show("slow", "linear");
                scrollToElement("notification_just_bought_minimum");
                return false;
            }
        }

        if (form.get("social_proof_enabled") == "1") {
            if (form.get("social_proof_message") != "" && form.get("social_proof_message") != null) {
                validated = true;
            } else {
                $("#social_proof_message").addClass("error-input");
                $("#social_proof_message_error").show("slow", "linear");
                scrollToElement("social_proof_message");
                return false;
            }

            if (form.get("social_proof_minimum") != "" && form.get("social_proof_minimum") != null) {
                validated = true;
            } else {
                $("#social_proof_minimum").addClass("error-input");
                $("#social_proof_minimum_error").show("slow", "linear");
                scrollToElement("social_proof_minimum");
                return false;
            }
        }

        if (form.get("company_id") != "" && form.get("company_id") != null) {
            validated = true;
        } else {
            $("#company_billing_error").show("slow", "linear");
            scrollToElement("companies");
            return false;
        }

        if (form.get("bank_slip_due_days") != "" && form.get("bank_slip_due_days") != null) {
            validated = true;
        } else {
            $("#bank_slip_due_days").addClass("error-input");
            $("#bank_slip_due_days_error").show("slow", "linear");
            scrollToElement("bank_slip_due_days");
            return false;
        }

        if (form.get("post_purchase_message_enabled") == "1") {
            if (form.get("post_purchase_message_title") != "" && form.get("post_purchase_message_title") != null) {
                validated = true;
            } else {
                $("#post_purchase_message_title").addClass("error-input");
                $("#post_purchase_message_title_error").show("slow", "linear");
                scrollToElement("post_purchase_message_title");
                return false;
            }

            if (quillThanksPage.getText().trim().length > 0) {
                validated = true;
            } else {
                $("#post_purchase_message_content").addClass("error-input");
                $("#post_purchase_message_content_error").show("slow", "linear");
                scrollToElement("post_purchase_message_content");
                return false;
            }
        }

        if (form.get("checkout_logo_enabled") == "1") {
            if ($("#has_checkout_logo").val() == "true") {
                validated = true;
            } else {
                scrollToElement("upload_logo");
                $("#checkout_logo_error").show("slow", "linear");
                return false;
            }
        }

        if (form.get("checkout_favicon_enabled") == "1") {
            if (form.get("checkout_favicon_type") == "1") {
                validated = true;
            } else {
                if ($("#has_checkout_favicon").val() == "true") {
                    validated = true;
                } else {
                    scrollToElement("upload_logo");
                    $("#checkout_favicon_error").fadeIn("slow", "linear");
                    return false;
                }
            }
        }

        if (form.get("checkout_banner_enabled") == "1") {
            if ($("#has_checkout_banner").val() == "true") {
                validated = true;
            } else {
                scrollToElement("banner_type");
                $("#checkout_banner_error").show("slow", "linear");
                return false;
            }
        }

        if (form.get("whatsapp_enabled") == "1") {
            if ($("#support_phone").val() == "") {
                validated = true;
            } else {
                if (testRegex(/^\([1-9]{2}\)\s(?:[2-8]|9[1-9])[0-9]{3}\-[0-9]{4}$/, $("#support_phone").val())) {
                    validated = true;
                } else {
                    $("#support_phone").addClass("error-input");
                    $("#support_phone_error").show("slow", "linear");
                    scrollToElement("support_phone");
                    return false;
                }
            }
        }

        return validated;
    }

    function scrollToElement(elementId) {
        $([document.documentElement, document.body]).animate(
            {
                scrollTop: $(`#${elementId}`).position().top - 200,
            },
            2000
        );
    }

    function testRegex(re, str) {
        if (re.test(str)) {
            return true;
        } else {
            return false;
        }
    }
});
