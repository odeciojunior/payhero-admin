$(() => {
    // let projectId = $(window.location.pathname.split/')).get(-1);

    checkout = {
        checkout_type_enum: 0,
        logo_enabled: true,
        checkout_logo: "",
        checkout_banner_type: 1,
        checkout_banner_enabled: true,
        checkout_banner: "",
        countdown_enabled: true,
        countdown_time: 60,
        countdown_description: "Teste descrição contagem regressiva",
        countdown_finish_message: "Teste mensagem contagem regressiva finalizada",
        topbar_enabled: true,
        topbar_content: "Teste de <strong>Mensagem</strong>",
        notifications_enabled: true,
        notifications_interval: 60,
        notification_buying_enabled: true,
        notification_buying_minimum: 10,
        notification_bought_30_minutes_enabled: true,
        notification_bought_30_minutes_minimum: 10,
        notification_bought_last_hour_enabled: true,
        notification_bought_last_hour_minimum: 10,
        notification_just_bought_enabled: true,
        notification_just_bought_minimum: 10,
        social_proof_enabled: true,
        social_proof_message: "Teste",
        social_proof_minimum: 15,
        invoice_description: "Teste",
        company_id: 0,
        cpf_enabled: true,
        cnpj_enabled: false,
        credit_card_enabled: true,
        bank_slip_enabled: false,
        pix_enabled: true,
        quantity_selector_enabled: true,
        email_required: true,
        installments_limit: 3,
        interest_free_installments: 4,
        preselected_installment: 5,
        bank_slip_due_days: 3,
        automatic_discount_credit_card: 2,
        automatic_discount_bank_slip: 3,
        automatic_discount_pix: 4,
        post_purchase_message_enabled: true,
        post_purchase_message_title: "Obrigado!",
        post_purchase_message_content: "Teste de <strong>Mensagem</strong>",
        whatsapp_enabled: true,
        support_phone: "1190000-0000",
        theme_enum: 0,
        color_primary: "#5577a7",
        color_secondary: "#313c52",
        color_buy_button: "#23d07d",

    };

    var companies = [
        {
            id: "n7vJOGY5LGKXdax",
            country: "brazil",
            name: "VM SERVIÇOS ADMINISTRATIVOS E DIGITAIS EIRELI",
            company_document_status: "approved",
            capture_transaction_enabled: 1,
            company_has_sale_before_getnet: 1,
            active_flag: 1,
            has_pix_key: 1,
            company_type: "juridical person",
            user_address_document_status: "approved",
            user_personal_document_status: "approved",
        },

        {
            id: "n8vJOGY5LGdax",
            country: "brazil",
            name: "Teste 2",
            company_document_status: "pending",
            capture_transaction_enabled: 1,
            company_has_sale_before_getnet: 1,
            active_flag: 1,
            has_pix_key: 1,
            company_type: "juridical person",
            user_address_document_status: "approved",
            user_personal_document_status: "approved",
        },
    ];

    userProject = {
        company_id: "n7vJOGY5LGKXdax",
    };

    if (checkout.checkout_type_enum === 0) {
        $("#checkout_editor #checkout_type_steps").prop("checked", true);

        $("#checkout_editor .visual-content-left").addClass("three-steps");
        $("#checkout_editor .visual-content-left").removeClass("unique");
        $("#checkout_editor .visual-content-mobile").addClass("three-steps");
        $("#checkout_editor .visual-content-mobile").removeClass("unique");
        $("#checkout_editor .steps-lines").slideDown("slow", "swing");
        $("#checkout_editor #finish_button_preview_desktop_visual").slideDown(
            "slow",
            "swing"
        );
        $("#checkout_editor #finish_button_preview_mobile_visual").slideDown(
            "slow",
            "swing"
        );
    } else {
        $("#checkout_editor #checkout_type_unique").attr("checked", true);

        $("#checkout_editor .visual-content-left").removeClass("three-steps");
        $("#checkout_editor .visual-content-left").addClass("unique");
        $("#checkout_editor .visual-content-mobile").removeClass("three-steps");
        $("#checkout_editor .visual-content-mobile").addClass("unique");
        $("#checkout_editor .steps-lines").slideUp("slow", "swing");
        $("#checkout_editor #finish_button_preview_desktop_visual").slideUp(
            "slow",
            "swing"
        );
        $("#checkout_editor #finish_button_preview_mobile_visual").slideUp(
            "slow",
            "swing"
        );
    }

    if (checkout.logo_enabled) {
        $("#checkout_editor #logo_enabled").prop("checked", true);
        $("#checkout_editor .logo-content").show();
        $("#checkout_editor .logo-mobile").show();
    } else {
        $("#checkout_editor #logo_enabled").prop("checked", false);
        $("#checkout_editor .logo-content").hide();
        $("#checkout_editor .logo-mobile").hide();
    }

    if (checkout.checkout_logo !== "") {
        // ADICIONA IMAGEM NO DROPIFY
    }

    if (checkout.checkout_banner_enabled) {
        $("#checkout_editor #checkout_banner_enabled").prop("checked", true);
        $("#checkout_editor .banner-top-content").show();
        $("#checkout_editor .preview-banner").show();
    } else {
        $("#checkout_editor #checkout_banner_enabled").prop("checked", false);
        $("#checkout_editor .banner-top-content").hide();
        $("#checkout_editor .preview-banner").hide();
    }

    if (checkout.checkout_banner_type === 1) {
        $("#checkout_editor #banner_type_wide").prop("checked", true);
        $(".preview-banner").removeClass("retangle-banner");
        $(".preview-banner").addClass("wide-banner");
    } else {
        $("#checkout_editor #banner_type_square").prop("checked", true);
        $(".preview-banner").addClass("retangle-banner");
        $(".preview-banner").removeClass("wide-banner");
    }

    if (checkout.checkout_banner !== "") {
        // ADICIONA IMAGEM NO DROPIFY
    }

    if (checkout.countdown_enabled) {
        $("#checkout_editor #countdown_enabled").prop("checked", true);
        $("#checkout_editor .countdown-content").show();
        $("#checkout_editor .countdown-preview").show();
    } else {
        $("#checkout_editor #countdown_enabled").prop("checked", false);
        $("#checkout_editor .countdown-content").hide();
        $("#checkout_editor .countdown-preview").hide();
    }

    $("#checkout_editor #countdown_time").val(checkout.countdown_time || 15);
    $("#checkout_editor #countdown_description").val(
        checkout.countdown_description || ""
    );
    $("#checkout_editor #countdown_finish_message").val(
        checkout.countdown_finish_message || ""
    );

    if (checkout.topbar_enabled) {
        $("#checkout_editor #topbar_enabled").prop("checked", true);
        $("#checkout_editor .textbar-content").show();
        $("#checkout_editor .textbar-preview").show();
    } else {
        $("#checkout_editor #topbar_enabled").prop("checked", false);
        $("#checkout_editor .textbar-content").hide();
        $("#checkout_editor .textbar-preview").hide();
    }

    var quillTextbar = new Quill("#checkout_editor #topbar_content", {
        modules: {
            toolbar: "#topbar_content_toolbar_container",
            clipboard: {
                matchVisual: false,
            },
        },
        placeholder: "",
        theme: "snow",
        formats: ["bold", "italic", "underline"],
    });
    quillTextbar.clipboard.dangerouslyPasteHTML(0, checkout.topbar_content);

    if (checkout.notifications_enabled) {
        $("#checkout_editor #notifications_enabled").prop("checked", true);
        $("#checkout_editor .sales-notifications-content").show();
    } else {
        $("#checkout_editor #notifications_enabled").prop("checked", false);
        $("#checkout_editor .sales-notifications-content").hide();
    }

    switch (checkout.notifications_interval) {
        case 15:
            $("#checkout_editor #notification_interval_15").prop(
                "checked",
                true
            );
            break;
        case 30:
            $("#checkout_editor #notification_interval_30").prop(
                "checked",
                true
            );
            break;
        case 45:
            $("#checkout_editor #notification_interval_45").prop(
                "checked",
                true
            );
            break;
        case 60:
            $("#checkout_editor #notification_interval_60").prop(
                "checked",
                true
            );
            break;
        default:
            $("#checkout_editor #notification_interval_15").prop(
                "checked",
                true
            );
            break;
    }

    $("#checkout_editor #countdown_description").val(
        checkout.countdown_description || ""
    );

    $("#checkout_editor #notification_buying_enabled").prop(
        "checked",
        checkout.notification_buying_enabled || false
    );
    $("#checkout_editor #notification_bought_30_minutes_enabled").prop(
        "checked",
        checkout.notification_bought_30_minutes_enabled || false
    );
    $("#checkout_editor #notification_bought_last_hour_enabled").prop(
        "checked",
        checkout.notification_bought_last_hour_enabled || false
    );
    $("#checkout_editor #notification_just_bought_enabled").prop(
        "checked",
        checkout.notification_just_bought_enabled || false
    );

    if (
        checkout.notification_buying_enabled &&
        checkout.notification_bought_30_minutes_enabled &&
        checkout.notification_bought_last_hour_enabled &&
        checkout.notification_just_bought_enabled
    ) {
        $("#checkout_editor #notification-table .selectable-all").prop(
            "checked",
            true
        );
    }

    $("#checkout_editor #notification_buying_minimum").val(
        checkout.notification_buying_minimum || 0
    );
    $("#checkout_editor #notification_bought_30_minutes_minimum").val(
        checkout.notification_bought_30_minutes_minimum || 0
    );
    $("#checkout_editor #notification_bought_last_hour_minimum").val(
        checkout.notification_bought_last_hour_minimum || 0
    );
    $("#checkout_editor #notification_just_bought_minimum").val(
        checkout.notification_just_bought_minimum || 0
    );

    if (checkout.social_proof_enabled) {
        $("#checkout_editor #social_proof_enabled").prop("checked", true);
        $("#checkout_editor .social-proof-content").show();
    } else {
        $("#checkout_editor #social_proof_enabled").prop("checked", false);
        $("#checkout_editor .social-proof-content").hide();
    }

    $("#checkout_editor #social_proof_message").val(
        checkout.social_proof_message || ""
    );
    $("#checkout_editor #social_proof_minimum").val(
        checkout.social_proof_minimum || 0
    );
    $("#checkout_editor #invoice_description").val(
        checkout.invoice_description || ""
    );

    // --------------- Select companies ---------------
    let company_selected = null;
    for (let company of companies) {
        if (company.id == userProject.company_id) company_selected = company;

        if (
            company.id == userProject.company_id ||
            company.capture_transaction_enabled
        ) {
            if (company.id === userProject.company_id)
                company_selected = company;
            $("#companies").append(
                `<option value="${company.id}"
              ${company.id === userProject.company_id ? "selected" : ""}
              ${company.company_document_status == "pending" ? "disabled" : ""}
              ${company.active_flag == 0 ? "disabled" : ""}
          >
              ${
                  company.company_document_status == "pending"
                      ? company.name + " (documentos pendentes)"
                      : company.name
              }
          </option>
        `
            );
        }
    }

    $("#checkout_editor #cpf_enabled").prop(
        "checked",
        checkout.cpf_enabled || false
    );
    $("#checkout_editor #cnpj_enabled").prop(
        "checked",
        checkout.cnpj_enabled || false
    );

    $("#checkout_editor #bank_slip_enabled").prop(
        "checked",
        checkout.bank_slip_enabled || false
    );
    $("#checkout_editor #pix_enabled").prop(
        "checked",
        checkout.pix_enabled || false
    );

    if (checkout.credit_card_enabled) {
        $("#checkout_editor #credit_card_enabled").prop("checked", true);
        $(".credit-card-container").show("slow", "swing");
        $(".accepted-payment-card-creditcard").slideDown("slow", "swing");
    } else {
        $("#checkout_editor #credit_card_enabled").prop("checked", false);
        $(".credit-card-container").hide("slow", "swing");
        $(".accepted-payment-card-creditcard").slideUp("slow", "swing");
    }

    if (checkout.bank_slip_enabled) {
        $("#checkout_editor #bank_slip_enabled").prop("checked", true);
        $(".bank-billet-container").show("slow", "swing");
        $(".accepted-payment-bank-billet").slideDown("slow", "swing");
    } else {
        $("#checkout_editor #bank_slip_enabled").prop("checked", false);
        $(".bank-billet-container").hide("slow", "swing");
        $(".accepted-payment-bank-billet").slideUp("slow", "swing");
    }

    if (checkout.pix_enabled) {
        $("#checkout_editor #pix_enabled").prop("checked", true);
        $(".pix-container").show("slow", "swing");
        $(".accepted-payment-pix").slideDown("slow", "swing");
    } else {
        $("#checkout_editor #pix_enabled").prop("checked", false);
        $(".pix-container").hide("slow", "swing");
        $(".accepted-payment-pix").slideUp("slow", "swing");
    }

    if (checkout.quantity_selector_enabled) {
        $("#checkout_editor #count-selector-label").addClass("active");
        $("#checkout_editor #quantity_selector_enabled").prop(
            "checked",
            checkout.quantity_selector_enabled || true
        );
    } else {
        $("#checkout_editor #count-selector-label").removeClass("active");
        $("#checkout_editor #quantity_selector_enabled").prop(
            "checked",
            checkout.quantity_selector_enabled || false
        );
    }

    if (checkout.email_required) {
        $("#checkout_editor #checkout-email-label").addClass("active");
        $("#checkout_editor #email_required").prop(
            "checked",
            checkout.email_required || true
        );
    } else {
        $("#checkout_editor #checkout-email-label").removeClass("active");
        $("#checkout_editor #email_required").prop(
            "checked",
            checkout.email_required || false
        );
    }

    $("#checkout_editor #bank_slip_due_days").val(
        checkout.bank_slip_due_days || 1
    );
    $("#checkout_editor #installments_limit").val(
        checkout.installments_limit || 1
    );
    $("#checkout_editor #interest_free_installments").val(
        checkout.interest_free_installments || 1
    );
    $("#checkout_editor #preselected_installment").val(
        checkout.preselected_installment || 1
    );

    $("#checkout_editor #automatic_discount_credit_card").val(
        checkout.automatic_discount_credit_card || 1
    );
    $("#checkout_editor #automatic_discount_bank_slip").val(
        checkout.automatic_discount_bank_slip || 1
    );
    $("#checkout_editor #automatic_discount_pix").val(
        checkout.automatic_discount_pix || 1
    );

    if (checkout.post_purchase_message_enabled) {
        $("#checkout_editor #post_purchase_message_enabled").prop(
            "checked",
            true
        );
        $(".thanks-page-content").show("slow", "swing");
        $(".shop-message-preview").slideDown("slow", "swing");
    } else {
        $("#checkout_editor #post_purchase_message_enabled").prop(
            "checked",
            false
        );
        $(".thanks-page-content").hide("slow", "swing");
        $(".shop-message-preview").slideUp("slow", "swing");
    }

    $("#checkout_editor #post_purchase_message_title").val(
        checkout.post_purchase_message_title || "Obrigado por comprar conosco!"
    );

    var quillThanksPage = new Quill(
        "#checkout_editor #post_purchase_message_content",
        {
            modules: {
                toolbar: "#post_purchase_message_content_toolbar_container",
                clipboard: {
                    matchVisual: false,
                },
            },
            placeholder: "",
            theme: "snow",
            formats: ["bold", "italic", "underline"],
        }
    );
    quillThanksPage.clipboard.dangerouslyPasteHTML(0, checkout.post_purchase_message_content
    );

    // whatsapp_enabled: false,

    if(checkout.whatsapp_enabled){
        $("#checkout_editor #logo_enabled").prop("checked", true);
        $(".whatsapp-content").show("slow", "swing");
        $(".whatsapp-preview").slideDown("slow", "swing");
    }else{
        $("#checkout_editor #logo_enabled").prop("checked", false);
        $(".whatsapp-content").hide("slow", "swing");
        $(".whatsapp-preview").slideUp("slow", "swing");
    }

    $("#checkout_editor #support_phone").val(checkout.support_phone || "");

    if(checkout.theme_enum === 0){
        $(":root").css("--primary-color", checkout.color_primary);
        $(":root").css("--secondary-color", checkout.color_secondary);
        $(":root").css("--finish-button-color", checkout.color_buy_button);
        
        $('#color_primary').val(checkout.color_primary);
        $('#color_secondary').val(checkout.color_secondary);
        $('#color_buy_button').val(checkout.color_buy_button);

        $('#checkout_editor #theme_ready_enabled').prop('checked', true);
        $(".custom-theme-content").show("slow", "swing");
        $(".theme-ready-content").hide("slow", "swing");
    }else{
        $('#checkout_editor #theme_ready_enabled').prop('checked', false);

        switch (checkout.theme_enum) {
            case 1:
                $('#checkout_editor #theme_spaceship').prop('checked', true);
                    
                var primaryColor = $('label[for="' + $('#checkout_editor #theme_spaceship').attr("id") + '"]')
                    .children(".theme-primary-color")
                    .css("background-color");
                var secondaryColor = $('label[for="' + $('#checkout_editor #theme_spaceship').attr("id") + '"]')
                    .children(".theme-secondary-color")
                    .css("background-color");

                

                $(":root").css("--primary-color", primaryColor);
                $(":root").css("--secondary-color", secondaryColor);
                $(":root").css("--finish-button-color", primaryColor);
            break;

        }
    }
});
