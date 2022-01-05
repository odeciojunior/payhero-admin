// const { fill } = require("lodash");

$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-1);

    userProject = {
        company_id: "n7vJOGY5LGKXdax",
    };

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

        dropifyOptions = {
            messages: {
                default: "",
                replace: "",
                remove: "Remover",
                error: "",
            },
            error: {
                fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
                minWidth: "",
                maxWidth: "A imagem deve ter largura menor que 300px.",
                minHeight: "",
                maxHeight: "A imagem deve ter altura menor que 300px.",
                fileExtension:
                    "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
            },
            tpl: {
                message:
                    '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Clique ou arraste e solte aqui</span></p></div>',
                clearButton:
                    '<button type="button" class="dropify-clear o-bin-1"></button>',
            },
            imgFileExtensions: ["png", "jpg", "jpeg", "svg"],
        };

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
                fillForm(checkout);

                $(document).on("submit", "#checkout_editor", function (e) {
                    e.preventDefault();

                    let checkoutForm = document.getElementById("checkout_editor");
                    let formData = new FormData(checkoutForm);
                    
                    formData.append('topbar_content', $("#topbar_content").children().html());
                    formData.append('post_purchase_message_content', $("#post_purchase_message_content").children().html());

                    if(!$('#theme_ready_enabled').is(':checked')){

                        var primaryColor = $('label[for="' + $("input[name=theme_ready]:checked").attr("id") +'"]')
                            .children(".theme-primary-color")
                            .attr("data-color");

                        var secondaryColor = $('label[for="' + $("input[name=theme_ready]:checked").attr("id") +'"]')
                            .children(".theme-secondary-color")
                            .attr("data-color");

                            formData.set('color_primary', primaryColor);
                            formData.set('color_secondary', secondaryColor);
                            formData.set('color_buy_button', primaryColor);
                    }

                    $('#checkout_editor input[type=checkbox]:not(:checked)').map(function () {
                        formData.append($(this).attr('name'), $(this).is(':checked') ? 1 : 0);
                    });                    
                    
                    // if($('#checkout_logo').get(0).files.length == 0){
                    //     formData.set('checkout_logo', new File([checkout.checkout_logo], 'checkout_logo', { type: 'image/png'}));
                    // }                    
                    
                    // console.log(formData.get('checkout_logo'));

                    // formData.set('checkout_banner', new File([base64ImageToBlob($('#checkout_banner_hidden').val())], "checkout_banner", { type: 'image/png'}));
                    
                    console.log(formData.get('checkout_banner'));

                    // Printar Form
                    for (var form of formData.entries()) {
                        console.log(form[0]+ ': ' + form[1]); 
                    }

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
                        error: function (response) {
                            errorAjaxResponse(response);
                        },
                        success: function (response) {
                            alertCustom("success", response.message);
                        },
                    });
                });

                loadOnAny(".checkout-container", true);
            },
            error: (response) => {
                errorAjaxResponse(response);

                $("#checkout_logo").dropify(dropifyOptions);

                // drEventLogo.settings.defaultFile = imagenUrl;

                // drEventLogo.on("dropify.fileReady", function (event, element) {
                //     var files = event.target.files;
                //     var done = function (url) {
                //         $("#logo_preview").attr("src", url);
                //     };
                //     if (files && files.length > 0) {
                //         file = files[0];

                //         if (URL) {
                //             done(URL.createObjectURL(file));
                //         } else if (FileReader) {
                //             reader = new FileReader();
                //             reader.onload = function (e) {
                //                 done(reader.result);
                //             };
                //             reader.readAsDataURL(file);
                //         }
                //     }
                // });

                loadOnAny(".checkout-container", true);
            },
        });

        $("#cancel_button").on("click", function () {
            fillForm(checkout);
            $("#changing_container").fadeOut("slow", "swing");
        });

        function fillForm(checkout) {

            $("#checkout_editor #checkout_editor_id").prop("value", checkout.id);

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
                $("#checkout_editor .visual-content-mobile").removeClass(
                    "three-steps"
                );
                $("#checkout_editor .visual-content-mobile").addClass("unique");
                $("#checkout_editor .steps-lines").slideUp("slow", "swing");
                $(
                    "#checkout_editor #finish_button_preview_desktop_visual"
                ).slideUp("slow", "swing");
                $(
                    "#checkout_editor #finish_button_preview_mobile_visual"
                ).slideUp("slow", "swing");
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

            
            if (checkout.checkout_logo != '') {
                dropifyOptions.defaultFile = checkout.checkout_logo;
            }

            var drEventLogo = $("#checkout_logo").dropify(dropifyOptions);

            $("#checkout_logo").attr('src', checkout.checkout_logo);

            // Seta imagem no preview
            $("#logo_preview").attr("src", checkout.checkout_logo);

            drEventLogo.on("dropify.fileReady", function (event, element) {
                var files = event.target.files;
                var done = function (url) {
                    $("#logo_preview").attr("src", url);
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

            if (checkout.checkout_banner_enabled) {
                $("#checkout_editor #checkout_banner_enabled").prop("checked", true);
                $("#checkout_editor #checkout_banner_enabled").prop("value", 1);
                $("#checkout_editor .banner-top-content").show();
                $("#checkout_editor .preview-banner").show();
            } else {
                $("#checkout_editor #checkout_banner_enabled").prop("checked", false);
                $("#checkout_editor #checkout_banner_enabled").prop("value", 0);
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

            if(checkout.countdown_description == "" || checkout.countdown_description == null){
                $("#checkout_editor #countdown_description").val("Aproveite o desconto extra ao comprar no Cartão ou pelo PIX! É por tempo limitado.");
            }else{
                $("#checkout_editor #countdown_description").val(checkout.countdown_description);  
            }

            if(checkout.countdown_finish_message == "" || checkout.countdown_finish_message == null){
                $("#checkout_editor #countdown_finish_message").val("Seu tempo acabou! Você precisa finalizar sua compra imediatamente.");
            }else{
                $("#checkout_editor #countdown_finish_message").val(checkout.countdown_finish_message);  
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
            quillTextbar.clipboard.dangerouslyPasteHTML(
                0,
                checkout.topbar_content
            );

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


            if(checkout.notification_buying_enabled) {
                $("#checkout_editor #notification_buying_enabled").prop("checked", true);
                $("#checkout_editor #notification_buying_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #notification_buying_enabled").prop("checked", false);
                $("#checkout_editor #notification_buying_enabled").prop("value", 0);
            }

            if(checkout.notification_bought_30_minutes_enabled) {
                $("#checkout_editor #notification_bought_30_minutes_enabled").prop("checked", true);
                $("#checkout_editor #notification_bought_30_minutes_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #notification_bought_30_minutes_enabled").prop("checked", false);
                $("#checkout_editor #notification_bought_30_minutes_enabled").prop("value", 0);
            }
            
            if(checkout.notification_bought_last_hour_enabled) {
                $("#checkout_editor #notification_bought_last_hour_enabled").prop("checked", true);
                $("#checkout_editor #notification_bought_last_hour_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #notification_bought_last_hour_enabled").prop("checked", false);
                $("#checkout_editor #notification_bought_last_hour_enabled").prop("value", 0);
            }

            if(checkout.notification_just_bought_enabled) {
                $("#checkout_editor #notification_just_bought_enabled").prop("checked", true);
                $("#checkout_editor #notification_just_bought_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #notification_just_bought_enabled").prop("checked", false);
                $("#checkout_editor #notification_just_bought_enabled").prop("value", 0);
            }
            
            

            if (checkout.notification_buying_enabled &&
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
                $("#checkout_editor #social_proof_enabled").prop("value", 1);
                $("#checkout_editor .social-proof-content").show();
            } else {
                $("#checkout_editor #social_proof_enabled").prop("checked", false);
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
                if (company.id == checkout.company_id || company.capture_transaction_enabled) {
                    $('#checkout_editor #companies').append(
                        `<option value="${company.id}"
                        ${(company.id === checkout.company_id ? 'selected' : '')}
                        ${(company.status == 'pending' ? 'disabled' : '')}
                        ${(company.active_flag == 0 ? 'disabled' : '')}
                    >
                        ${(company.status == 'pending' ? company.name + ' (documentos pendentes)' : company.name)}
                    </option>
                  `);
                }
            }

            if(checkout.cpf_enabled){
                $("#checkout_editor #cpf_enabled").prop("checked", true);
                $("#checkout_editor #cpf_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #cpf_enabled").prop("checked", false);
                $("#checkout_editor #cpf_enabled").prop("value", 0);
            }
            

            if(checkout.cnpj_enabled){
                $("#checkout_editor #cnpj_enabled").prop("checked", true);
                $("#checkout_editor #cnpj_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #cnpj_enabled").prop("checked", false);
                $("#checkout_editor #cnpj_enabled").prop("value", 0);
            }
            
            if(checkout.bank_slip_enabled){
                $("#checkout_editor #bank_slip_enabled").prop("checked", true);
                $("#checkout_editor #bank_slip_enabled").prop("value", 1);
            }else{
                $("#checkout_editor #bank_slip_enabled").prop("checked", false);
                $("#checkout_editor #bank_slip_enabled").prop("value", 0);
            }
            
            if(checkout.pix_enabled){
                $("#checkout_editor #pix_enabled").prop("checked",true);
                $("#checkout_editor #pix_enabled").prop("value",true);
            }{
                $("#checkout_editor #pix_enabled").prop("checked",false);
                $("#checkout_editor #pix_enabled").prop("value",false);
            }
            

            if (checkout.credit_card_enabled) {
                $("#checkout_editor #credit_card_enabled").prop("checked", true);
                $("#checkout_editor #credit_card_enabled").prop("value", 1);
                $(".credit-card-container").show("slow", "swing");
                $(".accepted-payment-card-creditcard").slideDown(
                    "slow",
                    "swing"
                );
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
                $("#checkout_editor #post_purchase_message_enabled").prop("checked",true);
                $("#checkout_editor #post_purchase_message_enabled").prop("value",true);
                $(".thanks-page-content").show("slow", "swing");
                $(".shop-message-preview").slideDown("slow", "swing");
            } else {
                $("#checkout_editor #post_purchase_message_enabled").prop("checked", false);
                $("#checkout_editor #post_purchase_message_enabled").prop("value", 0);
                $(".thanks-page-content").hide("slow", "swing");
                $(".shop-message-preview").slideUp("slow", "swing");
            }

            $("#checkout_editor #post_purchase_message_title").val(
                checkout.post_purchase_message_title ||
                    "Obrigado por comprar conosco!"
            );

            var quillThanksPage = new Quill(
                "#checkout_editor #post_purchase_message_content",
                {
                    modules: {
                        toolbar:
                            "#post_purchase_message_content_toolbar_container",
                        clipboard: {
                            matchVisual: false,
                        },
                    },
                    theme: "snow",
                    formats: ["bold", "italic", "underline"],
                }
            );

            quillThanksPage.clipboard.dangerouslyPasteHTML( 0, checkout.post_purchase_message_content );

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

            
            if(checkout.support_phone_verified == 1) {
                $('#verify_phone_open').hide();
                $('#verified_phone_open').show();
            }else{
                $('#verify_phone_open').show();
                $('#verified_phone_open').hide();
            }

            if (checkout.theme_enum == 0) {
                $(":root").css("--primary-color", checkout.color_primary);
                $(":root").css("--secondary-color", checkout.color_secondary);
                $(":root").css("--finish-button-color", checkout.color_buy_button);

                $("#color_primary").val(checkout.color_primary);
                $("#color_secondary").val(checkout.color_secondary);
                $("#color_buy_button").val(checkout.color_buy_button);

                $("#checkout_editor #theme_ready_enabled").prop("checked",true);
                $("#checkout_editor #theme_ready_enabled").prop("value",true);
                $(".custom-theme-content").show("slow", "swing");
                $(".theme-ready-content").hide("slow", "swing");
            } else {
                $("#checkout_editor #theme_ready_enabled").prop("checked", false);
                $("#checkout_editor #theme_ready_enabled").prop("value", 0);
                $("input[name=theme_ready][value=" + checkout.theme_enum + "]").attr("checked", true);

                $(":root").css("--primary-color", checkout.color_primary);
                $(":root").css("--secondary-color", checkout.color_secondary);
                $(":root").css("--finish-button-color", checkout.color_buy_button);
            }
        }
    }

    function base64ImageToBlob(str) {
        // Extract content type and base64 payload from original string
        var pos = str.indexOf(';base64,');
        var type = str.substring(5, pos);
        var b64 = str.substr(pos + 8);
      
        // Decode base64
        var imageContent = atob(b64);
      
        // Create an ArrayBuffer and a view (as unsigned 8-bit)
        var buffer = new ArrayBuffer(imageContent.length);
        var view = new Uint8Array(buffer);
      
        // Fill the view, using the decoded base64
        for(var n = 0; n < imageContent.length; n++) {
          view[n] = imageContent.charCodeAt(n);
        }
      
        // Convert ArrayBuffer to Blob
        var blob = new Blob([buffer], { type: type });
      
        return blob;
      }

});
