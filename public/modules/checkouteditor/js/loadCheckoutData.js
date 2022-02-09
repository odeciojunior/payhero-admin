// const { fill } = require("lodash");

$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-1);


    $("#tab-checkout").on("click", function () {
        loadData();
    });

    let checkout = null;

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

        var quillTextbar = new Quill("#topbar_content", {
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

        quillTextbar.on('selection-change', function(range, oldRange, source) {
            if (range === null && oldRange !== null) {
                console.log('blur');
                $('#topbar_content').removeClass('focus-in');
            } else if (range !== null && oldRange === null){
                console.log('focus');
                $('#topbar_content').addClass('focus-in');
            }
                
        });

        var quillThanksPage = new Quill("#post_purchase_message_content", {
            modules: {
                toolbar: "#post_purchase_message_content_toolbar_container",
            },
            theme: "snow",
            formats: formats,
        });

        quillThanksPage.on("text-change", function () {
            if (quillThanksPage.getLength() < limit) {
                $(".shop-message-preview-content").empty();
                $(".shop-message-preview-content").append(
                    $(quillThanksPage.root.innerHTML)
                );
                $("#save_changes").fadeIn("slow", "swing");    
            }else{
                quillThanksPage.deleteText(limit, quillThanksPage.getLength());
            }
            
        });

        quillThanksPage.on('selection-change', function(range, oldRange, source) {
            if (range === null && oldRange !== null) {
                console.log('blur');
                $('#post_purchase_message_content').removeClass('focus-in');
            } else if (range !== null && oldRange === null) {
                console.log('focus');
                $('#post_purchase_message_content').addClass('focus-in');
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
                if(checkout) {
                    fillForm(checkout);
                    $('#checkout_editor_id').val(checkout.id)
                }

                $(document).on("submit", "#checkout_editor", function (e) {
                    e.preventDefault();

                    $('.error-input').removeClass('error-input');
                    $('.checkout-error').hide();

                    $('#save_load').fadeIn('slow', 'linear');
                    $('#save_changes').fadeOut('slow', 'linear');

                    $('.select-type').addClass('low-opacity')
                    $('.visual').addClass('low-opacity')
                    $('.payment').addClass('low-opacity')
                    $('.post-purchase-pages').addClass('low-opacity')
                    $('.preview').addClass('low-opacity')

                    let checkoutForm = document.getElementById("checkout_editor");
                    let formData = new FormData(checkoutForm);

                    if(quillTextbar.getText().trim().length === 0){
                        formData.append( "topbar_content", '');
                    }else{
                        formData.append( "topbar_content", $("#topbar_content").children().html());
                    }                   
                
                    formData.append("post_purchase_message_content", $("#post_purchase_message_content").children().html());


                    if(!formData.get('automatic_discount_credit_card')){
                        $('#automatic_discount_credit_card').val(0);
                        formData.append("automatic_discount_credit_card", 0);
                    }

                    if(!formData.get('automatic_discount_bank_slip')){
                        $('#automatic_discount_bank_slip').val(0);
                        formData.append("automatic_discount_bank_slip", 0);
                    }

                    if(!formData.get('automatic_discount_pix')){
                        $('#automatic_discount_pix').val(0);
                        formData.append("automatic_discount_pix", 0);
                    }

                    if (!$("#theme_ready_enabled").is(":checked")) {
                        var primaryColor = $('label[for="' + $("input[name=theme_enum]:checked").attr("id") +'"]').children(".theme-primary-color").attr("data-color");
                        var secondaryColor = $('label[for="' + $("input[name=theme_enum]:checked").attr("id") + '"]').children(".theme-secondary-color").attr("data-color");

                        formData.append("color_primary", primaryColor);
                        formData.append("color_secondary", secondaryColor);
                        formData.append("color_buy_button", primaryColor);
                    }else{
                        formData.set("theme_enum", 0);
                    }
                    
                    if ($("#default_finish_color").is(":checked")) {
                        formData.append("color_buy_button", '#23d07d');
                    }

                    $(
                        "#checkout_editor input[type=checkbox]:not(:checked)"
                    ).map(function () {
                        formData.append(
                            $(this).attr("name"),
                            $(this).is(":checked") ? 1 : 0
                        );
                    });

                    // Printar Form
                    for (var form of formData.entries()) {
                        console.log(form[0] + ": " + form[1]);
                    }

                    if(validadeForm(formData)) {
                        $.ajax({
                            method: "POST",
                            url: "/api/checkouteditor/" + checkout.id,
                            processData: false,
                            cache: false,
                            contentType: false,
                            dataType: "json",
                            headers: {
                                Authorization: $('meta[name="access-token"]').attr(
                                    "content"
                                ),
                                Accept: "application/json",
                            },
                            data: formData,
                            success: function () {
                                $('.select-type').removeClass('low-opacity')
                                $('.visual').removeClass('low-opacity')
                                $('.payment').removeClass('low-opacity')
                                $('.post-purchase-pages').removeClass('low-opacity')
                                $('.preview').removeClass('low-opacity')
    
                                $('#save_load').fadeOut('slow', 'linear');
                                $('#save_success').fadeIn('slow', 'linear');
    
                                setTimeout(function() {
                                    $('#save_success').fadeOut('slow', 'linear');
                                }, 4000)
                            },
                            error: function (response) {
                                console.log(response.responseText);
                                $('.select-type').removeClass('low-opacity')
                                $('.visual').removeClass('low-opacity')
                                $('.payment').removeClass('low-opacity')
                                $('.post-purchase-pages').removeClass('low-opacity')
                                $('.preview').removeClass('low-opacity')
    
                                $('#save_load').fadeOut('slow', 'linear');
                                $('#save_error').fadeIn('slow', 'linear');
    
                                setTimeout(function() {
                                    $('#save_error').fadeOut('slow', 'linear');
                                }, 4000);
                            },
                        });
                    }else{
                        $('.select-type').removeClass('low-opacity')
                        $('.visual').removeClass('low-opacity')
                        $('.payment').removeClass('low-opacity')
                        $('.post-purchase-pages').removeClass('low-opacity')
                        $('.preview').removeClass('low-opacity')

                        $('#save_load').fadeOut('slow', 'linear');
                        $('#save_empty_fields').fadeIn('slow', 'linear');

                        setTimeout(function() {
                            $('#save_empty_fields').fadeOut('slow', 'linear');
                        }, 4000);
                    }

                    
                });

                setTimeout(()=>{
                   $('#save_changes').hide();
                },0)
                

                loadOnAny(".checkout-container", true);
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadOnAny(".checkout-container", true);
            },
        });

        $("#cancel_button").on("click", function () {
            fillForm(checkout);
            $("#save_changes").fadeOut("slow", "swing");
        });

        function fillForm(checkout) {
            if (checkout.checkout_type_enum == 1) {$("#checkout_editor #checkout_type_steps").prop("checked",true);
                $("#checkout_editor .visual-content-left").addClass("three-steps");
                $("#checkout_editor .visual-content-left").removeClass("unique");
                $("#checkout_editor .visual-content-mobile").addClass("three-steps");
                $("#checkout_editor .visual-content-mobile").removeClass("unique");
                $("#checkout_editor .steps-lines").slideDown("slow", "swing");
                $("#checkout_editor #finish_button_preview_desktop_visual").slideDown("slow", "swing");
                $("#checkout_editor #finish_button_preview_mobile_visual").slideDown("slow", "swing");
            } else {
                $("#checkout_editor #checkout_type_unique").prop("checked",true);
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
                $("#checkout_editor .logo-content").show();
                $("#checkout_editor .logo-mobile").show();
            } else {
                $("#checkout_editor #checkout_logo_enabled").prop("checked", false);
                $("#checkout_editor #checkout_logo_enabled").prop("value", 0);
                $("#checkout_editor .logo-content").hide();
                $("#checkout_editor .logo-mobile").hide();
            }

            if (checkout.checkout_logo) {
                replacePreview("checkout_logo", checkout.checkout_logo, "Image.jpg");
                $("#logo_preview_mobile").attr("src", checkout.checkout_logo);
                $("#logo_preview_desktop").attr("src", checkout.checkout_logo);
            }

            if (checkout.checkout_banner) {
                replacePreview("checkout_banner", checkout.checkout_banner, "Image.jpg");
                $("#preview_banner_img_desktop").attr("src", checkout.checkout_banner);
                $("#preview_banner_img_mobile").attr("src", checkout.checkout_banner);
            }

            if (checkout.checkout_banner_enabled) {
                $("#checkout_editor #checkout_banner_enabled").prop("checked", true);
                $("#checkout_editor #checkout_banner_enabled").prop("value", 1);
                $("#checkout_editor .banner-top-content").show();
                $("#checkout_editor .preview-banner").show();
                $("#checkout_editor #banner_type").show();
                $('#logo_preview_desktop_div').addClass('has-banner');
            } else {
                $("#checkout_editor #checkout_banner_enabled").prop("checked", false);
                $("#checkout_editor #checkout_banner_enabled").prop("value", 0);
                $("#checkout_editor .banner-top-content").hide();
                $("#checkout_editor .preview-banner").hide();
                $("#checkout_editor #banner_type").hide();
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

            $("#checkout_editor #countdown_time").val(
                checkout.countdown_time || 15
            );

            if ( checkout.countdown_description != "" || !checkout.countdown_description) {
                $("#checkout_editor #countdown_description").val( checkout.countdown_description );
            }

            if ( checkout.countdown_finish_message != "" || !checkout.countdown_finish_message ) {
                $("#checkout_editor #countdown_finish_message").val( checkout.countdown_finish_message );
            }

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


            quillTextbar.root.innerHTML = checkout.topbar_content;


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
                    $("#checkout_editor #notifications_interval_15").prop(
                        "checked",
                        true
                    );
                    break;
                case 30:
                    $("#checkout_editor #notifications_interval_30").prop(
                        "checked",
                        true
                    );
                    break;
                case 45:
                    $("#checkout_editor #notifications_interval_45").prop(
                        "checked",
                        true
                    );
                    break;
                case 60:
                    $("#checkout_editor #notifications_interval_60").prop(
                        "checked",
                        true
                    );
                    break;
                default:
                    $("#checkout_editor #notifications_interval_15").prop(
                        "checked",
                        true
                    );
                    break;
            }

            if (checkout.notification_buying_enabled) {
                $("#checkout_editor #notification_buying_enabled").prop(
                    "checked",
                    true
                );
                $("#checkout_editor #notification_buying_enabled").prop(
                    "value",
                    1
                );
            } else {
                $("#checkout_editor #notification_buying_enabled").prop(
                    "checked",
                    false
                );
                $("#checkout_editor #notification_buying_enabled").prop(
                    "value",
                    0
                );
            }

            if (checkout.notification_bought_30_minutes_enabled) {
                $(
                    "#checkout_editor #notification_bought_30_minutes_enabled"
                ).prop("checked", true);
                $(
                    "#checkout_editor #notification_bought_30_minutes_enabled"
                ).prop("value", 1);
            } else {
                $(
                    "#checkout_editor #notification_bought_30_minutes_enabled"
                ).prop("checked", false);
                $(
                    "#checkout_editor #notification_bought_30_minutes_enabled"
                ).prop("value", 0);
            }

            if (checkout.notification_bought_last_hour_enabled) {
                $(
                    "#checkout_editor #notification_bought_last_hour_enabled"
                ).prop("checked", true);
                $(
                    "#checkout_editor #notification_bought_last_hour_enabled"
                ).prop("value", 1);
            } else {
                $(
                    "#checkout_editor #notification_bought_last_hour_enabled"
                ).prop("checked", false);
                $(
                    "#checkout_editor #notification_bought_last_hour_enabled"
                ).prop("value", 0);
            }

            if (checkout.notification_just_bought_enabled) {
                $("#checkout_editor #notification_just_bought_enabled").prop(
                    "checked",
                    true
                );
                $("#checkout_editor #notification_just_bought_enabled").prop(
                    "value",
                    1
                );
            } else {
                $("#checkout_editor #notification_just_bought_enabled").prop(
                    "checked",
                    false
                );
                $("#checkout_editor #notification_just_bought_enabled").prop(
                    "value",
                    0
                );
            }

            if (
                checkout.notification_buying_enabled &&
                checkout.notification_bought_30_minutes_enabled &&
                checkout.notification_bought_last_hour_enabled &&
                checkout.notification_just_bought_enabled
            ) {
                $("#checkout_editor #notification-table .selectable-all").prop("checked",true);
            }

            $("#checkout_editor #notification_buying_minimum").val(checkout.notification_buying_minimum || 0);
            $("#checkout_editor #notification_bought_30_minutes_minimum").val(checkout.notification_bought_30_minutes_minimum || 0);
            $("#checkout_editor #notification_bought_last_hour_minimum").val(checkout.notification_bought_last_hour_minimum || 0);
            $("#checkout_editor #notification_just_bought_minimum").val(checkout.notification_just_bought_minimum || 0);


            const form = document.querySelector("#checkout_editor");
            const selectableCheckboxes = form.querySelectorAll(".selectable-notification:checked");

            if(selectableCheckboxes.length > 0 && selectableCheckboxes.length < 4) {
                $('#selectable-all-notification').addClass('dash-check');
                $('#selectable-all-notification').prop('checked', true);
            }
    
            if(selectableCheckboxes.length == 0) {
                $('#selectable-all-notification').prop('checked', false);
                $('#selectable-all-notification').removeClass('dash-check');
            }
            
            if (selectableCheckboxes.length == 4){
                $('#selectable-all-notification').removeClass('dash-check');
                $('#selectable-all-notification').prop('checked', true);
            }

            if (checkout.social_proof_enabled) {
                $("#checkout_editor #social_proof_enabled").prop(
                    "checked",
                    true
                );
                $("#checkout_editor #social_proof_enabled").prop("value", 1);
                $("#checkout_editor .social-proof-content").show();
            } else {
                $("#checkout_editor #social_proof_enabled").prop("checked",false);
                $("#checkout_editor #social_proof_enabled").prop("value", 0);
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

            for (let company of checkout.companies) {
                if (
                    company.id == checkout.company_id ||
                    company.capture_transaction_enabled
                ) {
                    $("#checkout_editor #companies").append(
                        `<option  class="sirius-select-option" value="${company.id}"
                        ${company.id === checkout.company_id ? "selected" : ""}
                        ${company.status == "pending" ? "disabled" : ""}
                        ${company.active_flag == 0 ? "disabled" : ""}
                    >
                        ${
                            company.status == "pending"
                                ? company.name + " (documentos pendentes)"
                                : company.name
                        }
                    </option>
                  `
                    );
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
                $("#checkout_editor #credit_card_enabled").prop(
                    "checked",
                    true
                );
                $("#checkout_editor #credit_card_enabled").prop("value", 1);
                $(".credit-card-container").show("slow", "swing");
                $(".accepted-payment-card-creditcard").slideDown(
                    "slow",
                    "swing"
                );
            } else {
                $("#checkout_editor #credit_card_enabled").prop(
                    "checked",
                    false
                );
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
                $("#checkout_editor #quantity_selector_enabled").prop(
                    "checked",
                    true
                );
                $("#checkout_editor #quantity_selector_enabled").prop(
                    "value",
                    1
                );
            } else {
                $("#checkout_editor #count-selector-label").removeClass(
                    "active"
                );
                $("#checkout_editor #quantity_selector_enabled").prop(
                    "checked",
                    false
                );
                $("#checkout_editor #quantity_selector_enabled").prop(
                    "value",
                    0
                );
            }

            if (checkout.email_required) {
                $("#checkout_editor #checkout-email-label").addClass("active");
                $("#checkout_editor #email_required").prop("checked", true);
                $("#checkout_editor #email_required").prop("value", 1);
            } else {
                $("#checkout_editor #checkout-email-label").removeClass(
                    "active"
                );
                $("#checkout_editor #email_required").prop("checked", false);
                $("#checkout_editor #email_required").prop("value", 0);
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
                $("#checkout_editor #post_purchase_message_enabled").prop("checked", true);
                $("#checkout_editor #post_purchase_message_enabled").prop("value", 1);
                $(".thanks-page-content").show("slow", "swing");
                $(".shop-message-preview").slideDown("slow", "swing");
            } else {
                $("#checkout_editor #post_purchase_message_enabled").prop("checked", false);
                $("#checkout_editor #post_purchase_message_enabled").prop( "value", 0);
                $(".thanks-page-content").hide("slow", "swing");
                $(".shop-message-preview").slideUp("slow", "swing");
            }

            $("#checkout_editor #post_purchase_message_title").val(
                checkout.post_purchase_message_title ||
                    "Obrigado por comprar conosco!"
            );

    
            quillThanksPage.root.innerHTML = checkout.post_purchase_message_content;

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

            $("#checkout_editor #support_phone").val(
                checkout.support_phone || ""
            );

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
                $('.theme-ready-first-line').addClass('low-opacity');
                $(".theme-ready-second-line").hide("slow", "swing");
            } else {
                $("#checkout_editor #theme_ready_enabled").prop("checked", false);
                $("#checkout_editor #theme_ready_enabled").prop("value", 0);
                $("input[name=theme_enum][value=" + checkout.theme_enum + "]").attr("checked", true);

                $(":root").css("--primary-color", checkout.color_primary);
                $(":root").css("--secondary-color", checkout.color_secondary);
                $(":root").css("--finish-button-color", checkout.color_buy_button);

                $(".custom-theme-content").hide("slow", "swing");
                $('.theme-ready-first-line').removeClass('low-opacity');
                $(".theme-ready-second-line").show("slow", "swing");
            }

        }

        function validadeForm(form) {
            var  validated = false;

            
            if(form.get('countdown_enabled') == "1") {
                if(form.get('countdown_time') !=  '' && form.get('countdown_time') != null){
                    validated = true;
                }else{
                    $('#countdown_time').addClass('error-input');
                    $('#countdown_time_error').show('slow', 'linear');
                    scrollToElement('countdown_time');
                    return false;
                }

                if(form.get('countdown_finish_message') !=  '' && form.get('countdown_finish_message') != null){
                    validated = true;
                }else{
                    $('#countdown_finish_message').addClass('error-input');
                    $('#countdown_finish_message_error').show('slow', 'linear');
                    scrollToElement('countdown_finish_message');
                    return false;
                }
            }


            if(form.get('topbar_enabled') == "1"){
                if(quillTextbar.getText().trim().length > 0){
                    validated = true;
                }else{
                    $('#topbar_content').addClass('error-input');
                    scrollToElement('topbar_content');
                    $('#topbar_content_error').show('slow', 'linear');
                    return false;
                }
            } 

            if(form.get('notifications_enabled') == "1"){
                if(form.get('notification_buying_minimum') !=  ''){
                    validated = true;
                }else{
                    $('#notification_buying_minimum').addClass('error-input');
                    $('#notification_buying_minimum_error').show('slow', 'linear');
                    scrollToElement('notification_buying_minimum');
                    $('#notification_error').show('slow', 'linear');
                    return false;
                }

                if(form.get('notification_bought_30_minutes_minimum') !=  ''){
                    validated = true;
                }else{
                    $('#notification_bought_30_minutes_minimum').addClass('error-input');
                    $('#notification_error').show('slow', 'linear');
                    scrollToElement('notification_bought_30_minutes_minimum');
                    return false;
                }
    
    
                if(form.get('notification_bought_last_hour_minimum') !=  ''){
                    validated = true;
                }else{
                    $('#notification_bought_last_hour_minimum').addClass('error-input');
                    $('#notification_error').show('slow', 'linear');
                    scrollToElement('notification_bought_last_hour_minimum');
                    
                    return false;
                }
    
                if(form.get('notification_just_bought_minimum') !=  ''){
                    validated = true;
                }else{
                    $('#notification_just_bought_minimum').addClass('error-input');
                    $('#notification_error').show('slow', 'linear');
                    scrollToElement('notification_just_bought_minimum');
                    return false;
                }
            } 

            


            if(form.get('social_proof_enabled') == "1"){
                if(form.get('social_proof_message') !=  '' && form.get('social_proof_message') != null){
                    validated = true;
                }else{
                    $('#social_proof_message').addClass('error-input');
                    $('#social_proof_message_error').show('slow', 'linear');
                    scrollToElement('social_proof_message');
                    return false;
                }

                if(form.get('social_proof_minimum') !=  '' && form.get('social_proof_minimum') != null){
                    validated = true;
                }else{
                    $('#social_proof_minimum').addClass('error-input');
                    $('#social_proof_minimum_error').show('slow', 'linear');
                    scrollToElement('social_proof_minimum');
                    return false;
                }
            }

            if(form.get('bank_slip_due_days') !=  '' && form.get('bank_slip_due_days') != null){
                validated = true;
            }else{
                $('#bank_slip_due_days').addClass('error-input');
                $('#bank_slip_due_days_error').show('slow', 'linear');
                scrollToElement('bank_slip_due_days');
                return false;
            }

            if(form.get('post_purchase_message_enabled') == "1"){
                if(form.get('post_purchase_message_title') !=  '' && form.get('post_purchase_message_title') != null){
                    validated = true;
                }else{
                    $('#post_purchase_message_title').addClass('error-input');
                    $('#post_purchase_message_title_error').show('slow', 'linear');
                    scrollToElement('post_purchase_message_title');
                    return false;
                }

                if(quillThanksPage.getText().trim().length > 0){
                    validated = true;
                }else{
                    $('#post_purchase_message_content').addClass('error-input');
                    $('#post_purchase_message_content_error').show('slow', 'linear');
                    scrollToElement('post_purchase_message_content');
                    return false;
                }   
            }

            console.log('-------------------------------------------');
            console.log(form.get('checkout_logo').size);

            if(form.get('checkout_logo_enabled') == "1"){
                if(form.get('checkout_logo')){
                    console.log('Valido')
                    validated = true;  
                }else{
                    console.log('não válido')
                    scrollToElement('checkout_logo_enabled');
                    $('#checkout_logo_error').show('slow', 'linear');
                    return false;
                }
            }

            if(form.get('whatsapp_enabled') == "1"){
                if($('#support_phone').val() == ''){
                    validated = true;      
                }else{
                    if(testRegex(/^\([1-9]{2}\)\s(?:[2-8]|9[1-9])[0-9]{3}\-[0-9]{4}$/,$('#support_phone').val())){
                        validated = true;      
                    }else{
                        $('#support_phone').addClass('error-input');
                        $('#support_phone_error').show('slow', 'linear');
                        scrollToElement('support_phone');
                        return false;
                    }
                }
            }


            
            return validated;
        }
    }

    function scrollToElement(elementId){
        $([document.documentElement, document.body]).animate({
            scrollTop: ($(`#${elementId}`).position().top - 200)
        }, 2000);
    }

    function testRegex(re, str) {
        if (re.test(str)) {
            return true;
        } else {
            return false;
        }
    }
});
