$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    let termsaffiliates;

    ClassicEditor
        .create(document.querySelector('#termsaffiliates'), {
            language: 'pt-br',
            uiColor: '#F1F4F5',
            toolbar: [
                'heading', '|',
                'bold', 'italic', '|',
                'link', '|',
                'undo', 'redo'
            ]
        })
        .then(newEditor => {
            termsaffiliates = newEditor;
        })
        .catch(error => {
            console.error(error);
        });


    $('.percentage-affiliates').mask('###', {'translation': {0: {pattern: /[0-9*]/}}});

    // COMPORTAMENTOS DA TELA
    $('#tab-info').click(() => {
        show();
    });

    $(".tab_configuration").click(function () {
        $("#image-logo-email").imgAreaSelect({remove: true});
        $("#previewimage").imgAreaSelect({remove: true});
        updateConfiguracoes();
        $(this).off();
    });

    $('.toggler').on('click', function () {

        let target = $(this).data('target');

        if ($(target).hasClass('show')) {
            $(this).find('.showMore').html('add');
        } else {
            $(this).find('.showMore').html('remove');
        }
    });

    $('.status-url-affiliates').on('change', function () {
        if ($(this).prop('selectedIndex') == 0) {
            $('.div-url-affiliate').hide();
        } else {
            $('.div-url-affiliate').show();
        }
    });

    //parcelas
    let parcelas = '';
    let parcelasJuros = '';
    $(".installment_amount").on('change', function () {
        parcelas = parseInt($(".installment_amount option:selected").val());
        parcelasJuros = parseInt($(".parcelas-juros option:selected").val());
        verificaParcelas(parcelas, parcelasJuros);
    });

    $(".parcelas-juros").on('change', function () {
        parcelas = parseInt($(".installment_amount option:selected").val());
        parcelasJuros = parseInt($(".parcelas-juros option:selected").val());
        verificaParcelas(parcelas, parcelasJuros);
    });

    function verificaParcelas(parcelas, parcelasJuros) {
        if (parcelas < parcelasJuros) {
            $("#error-juros").css('display', 'block');
            return true;
        } else {
            $("#error-juros").css('display', 'none');
            return false;
        }
    }

    // frete
    $("#shippement").on('change', function () {
        if ($(this).val() == 0) {
            $("#div-carrier").hide();
            $("#div-shipment-responsible").hide();
        } else {
            $("#div-carrier").show();
            $("#div-shipment-responsible").show();
        }
    });

    //images
    let p = $("#previewimage");
    $('#photoProject').unbind('change');
    $("#photoProject").on('change', function () {
        let imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photoProject").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr('src', oFREvent.target.result).fadeIn();

            p.on('load', function () {

                let img = document.getElementById('previewimage');
                let x1, x2, y1, y2;

                if (img.naturalWidth > img.naturalHeight) {
                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                    x2 = x1 + (y2 - y1);
                } else {
                    if (img.naturalWidth < img.naturalHeight) {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                        y2 = y1 + (x2 - x1);
                    } else {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    }
                }

                $('#previewimage').imgAreaSelect({
                    x1: x1, y1: y1, x2: x2, y2: y2,
                    aspectRatio: '1:1',
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    onSelectEnd: function (img, selection) {
                        $('#photo_x1').val(selection.x1);
                        $('#photo_y1').val(selection.y1);
                        $('#photo_w').val(selection.width);
                        $('#photo_h').val(selection.height);
                    }
                });
            })
        };
    });

    $("#previewimage").on("click", function () {
        $("#photoProject").click();
    });

    $("#image-logo-email").on('click', function () {
        $("#photo-logo-email").click();
    });

    let ratio = '1:1';

    $('#ratioImage').unbind('change');
    $("#ratioImage").on('change', function () {
        ratio = $('#ratioImage option:selected').val();
        $("#image-logo-email").imgAreaSelect({remove: true});
        updateConfiguracoes();
        imgNatural(ratio);
    });

    let photoLogo = $("#image-logo-email");
    $("#photo-logo-email").on('change', function () {
        $(".container-image").css('display', 'block');
        let imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photo-logo-email").files[0]);
        imageReader.onload = function (ofREvent) {
            photoLogo.attr('src', ofREvent.target.result).fadeIn();
            photoLogo.on('load', function () {
                let img = document.getElementById("image-logo-email");
                $('input[name="logo_h"]').val(img.clientWidth);
                $('input[name="logo_w"]').val(img.clientHeight);
            });
        }

    });

    // FIM - COMPORTAMENTOS DA TELA

    show();

    //carrega detalhes do projeto
    function show() {
        loadingOnScreen();

        loadOnAny('#tab_info_geral .card', false, {
            styles: {
                container: {
                    minHeight: '250px'
                }
            }
        });

        $.ajax({
            url: '/api/projects/' + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                window.location.replace(`${location.origin}/projects`);
                $('.page-content').show()
                loadingOnScreenRemove();
            },
            success: (response) => {

                let project = response.data;
                let project_type = 'my_products';
                if (project.shopify_id != null) project_type = 'shopify';
                if (project.woocommerce_id != null) project_type = 'woocommerce';

                $('#project_type').val(project_type);
                $('.title-pad').text(project.name);
                $('#show-photo').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.svg');
                $('#created_at').text('Criado em ' + project.created_at);
                if (project.status == '1') {
                    $('#show-status').text('Ativo').addClass('badge-success');
                } else {
                    $('#show-status').text('Inativo').addClass('badge-danger');
                }
                $('#show-description').text(project.description);

                // $('#value-cancel').text('1.2K')
                let approvedSalesValue = parseFloat(project.approved_sales_value).toLocaleString('pt-BR')

                $('#value-chargeback').text(project.chargeback_count)
                $('#value-open-tickets').text(project.open_tickets)
                $('#value-without-tracking').text(project.without_tracking)
                $('#total-approved').text(project.approved_sales)
                $('#total-approved-value').text(approvedSalesValue)

                $('.page-content').show()
                loadOnAny('#tab_info_geral .card', true);
                loadingOnScreenRemove();
            }
        });
    }

    $("#copy-link-affiliation").on("click", function () {
        var copyText = document.getElementById("url-affiliates");
        copyText.select();
        document.execCommand("copy");

        alertCustom('success', 'Link copiado!');
    });

    function renderProjectConfig(data) {
        let {project, companies, userProject, shopifyIntegrations, projectUpsell} = data;

        $('#percentage-affiliates').mask('000', {
            reverse: true,
            onKeyPress: function (val, e, field, options) {
                if (val > 100) {
                    $('#percentage-affiliates').val('')
                }
            }
        });

        $('#update-project #previewimage').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.svg');
        $('#update-project #name').val(project.name);
        // $('#cost_currency_type').val(project.cost_currency_type);
        $('#update-project #description').text(project.description);
        if (project.visibility === 'public') {
            $('#update-project #visibility').prop('selectedIndex', 0).change();
        } else {
            $('#update-project #visibility').prop('selectedIndex', 1).change();
        }
        $('#update-project #image-logo-email').attr('src', project.logo ? project.logo : '/modules/global/img/projeto.svg');
        $('#update-project #url-page').val(project.url_page ? project.url_page : 'https://');
        $('#update-project #contact').val(project.contact);
        $('#update-project #support_phone').val(project.support_phone);
        $('#update-project #invoice-description').val(project.invoice_description);
        $('#update-project #companies').html('');

        let company_selected = null;
        for (let company of companies) {
            if (company.id == userProject.company_id) company_selected = company;
            if (company.id == userProject.company_id || company.capture_transaction_enabled) {
                if (company.id === userProject.company_id) company_selected = company;
                $('#update-project #companies').append(
                    `<option value="${company.id}"
                    ${(company.id === userProject.company_id ? 'selected' : '')}
                    ${(company.company_document_status == 'pending' ? 'disabled' : '')}
                    ${(company.active_flag == 0 ? 'disabled' : '')}
                >
                    ${(company.company_document_status == 'pending' ? company.name + ' (documentos pendentes)' : company.name)}
                </option>
              `);
            }
        }

        function validateInputPixInCheckout(company_selected) {

            let pix_element = $("#pix")

            if (company_selected.has_pix_key) {
                $("#pix option[value='1']").prop("disabled", false).css("backgroundColor", "white").html('Sim')
            } else {
                pix_element.val(0);
                $("#pix option[value='1']").prop("disabled", true).css("backgroundColor", "grey").html('Sim (A empresa selecionada não possui a chave do PIX)')
            }

        }

        $("#companies").on("change", function () {
            let company_sel = null;

            for (let company of companies) {
                if (company.id === $(this).val()) company_sel = company;
            }

            if (company_sel)
                validateInputPixInCheckout(company_sel);
        })

        validateInputPixInCheckout(company_selected);

        $('#update-project .installment_amount').prop('selectedIndex', project.installments_amount - 1).change();
        $('#update-project .parcelas-juros').prop('selectedIndex', project.installments_interest_free - 1).change();
        if (project.boleto === 1) {
            $('#update-project #boleto').prop('selectedIndex', 0).change();
        } else {
            $('#update-project #boleto').prop('selectedIndex', 1).change();

        }
        $('#boleto_due_days').val(project.boleto_due_days);
        $('#update-project #boleto_redirect').val(project.boleto_redirect);
        $('#update-project #card_redirect').val(project.card_redirect);
        $('#update-project #pix_redirect').val(project.pix_redirect);
        $('#update-project #analyzing_redirect').val(project.analyzing_redirect);
        $('#update-project #pix').val(project.pix);
        termsaffiliates.setData(project.terms_affiliates ?? ' ');

        if (project.automatic_affiliation == 1) {
            $('#update-project .automatic-affiliation').prop('selectedIndex', 1).change();
        } else {
            $('#update-project .automatic-affiliation').prop('selectedIndex', 0).change();
        }

        if (project.cookie_duration == 0) {
            $('#update-project .cookie-duration').prop('selectedIndex', 0).change();
        } else if (project.cookie_duration == 7) {
            $('#update-project .cookie-duration').prop('selectedIndex', 1).change();
        } else if (project.cookie_duration == 15) {
            $('#update-project .cookie-duration').prop('selectedIndex', 2).change();
        } else if (project.cookie_duration == 30) {
            $('#update-project .cookie-duration').prop('selectedIndex', 3).change();
        } else if (project.cookie_duration == 60) {
            $('#update-project .cookie-duration').prop('selectedIndex', 4).change();
        } else if (project.cookie_duration == 180) {
            $('#update-project .cookie-duration').prop('selectedIndex', 5).change();
        } else if (project.cookie_duration == 365) {
            $('#update-project .cookie-duration').prop('selectedIndex', 6).change();
        }

        if (project.status_url_affiliates == 1) {
            $('#update-project .status-url-affiliates').prop('checked', true)
            $('.div-url-affiliate').show('fast', 'linear')
        } else {
            $('.div-url-affiliate').prop('checked', false)
        }

        if (project.commission_type_enum == 1) {
            $('#update-project .commision-type-enum').prop('selectedIndex', 0).change();
        } else {
            $('#update-project .commission-type-enum').prop('selectedIndex', 1).change();
        }

        $('#update-project #percentage-affiliates').val(project.percentage_affiliates);
        $('#update-project #url-affiliates').val(project.url_affiliates);

        $('#shopify-integration-pending, #bt-change-shopify-integration, #bt-shopify-sincronization-product, #bt-shopify-sincronization-template').hide();
        if (project.woocommerce_id) {
            $('#update-project #woocommerce-configs').show();
            $('.listWooCommerceConfiguration').show();
        }
        if (project.shopify_id) {
            $('#update-project #shopify-configs').show();
            $('.listShopifyConfiguration').show();
            if (shopifyIntegrations.length !== 0) {
                $('#div-shopify-token').show();
                $('#div-shopify-permissions').show();
                $('#shopify-token').val(shopifyIntegrations[0].token);
                if (shopifyIntegrations[0].status !== 1) {
                    $('#bt-change-shopify-integration')
                        .attr('integration-status', shopifyIntegrations[0].status)
                        .show();
                    $('#bt-change-shopify-integration span').html(shopifyIntegrations[0].status === 2 ? 'Desfazer integração com shopify' : 'Integrar com shopify');
                } else if (shopifyIntegrations[0].status === 1) {
                    $('#shopify-integration-pending').show();
                }
                if (shopifyIntegrations[0].status !== 3) {
                    $('#bt-shopify-sincronization-product, #bt-shopify-sincronization-template')
                        .attr('integration-status', shopifyIntegrations[0].status)
                        .show();
                }

                $('#skiptocart-input').prop('checked', shopifyIntegrations[0].skip_to_cart).val(shopifyIntegrations[0].skip_to_cart);
            }
        }

        $("#checkout_type").val(project.checkout_type);
        $("#credit_card_discount").val(project.credit_card_discount);
        $("#billet_discount").val(project.billet_discount);
        $("#pix_discount").val(project.pix_discount);

        // Verificação de email de contato
        if (project.contact_verified) {
            contactVerified();
        } else {
            contactNotVerified();
        }

        // Verificação de telefone de suporte
        if (project.support_phone_verified) {
            supportphoneVerified();
        } else {
            supportphoneNotVerified();
        }

        if (project.discount_recovery_status) {
            $('#discount_recovery_status').prop('checked', true)
            $('#discount_recovery_value').show('fast', 'linear')
            $('#discount-recovery-alert').show('fast', 'linear')
        } else {
            $('#discount_recovery_status').prop('checked', false)
        }

        if (project.discount_recovery_value >= 10) {
            $('#discount_recovery_value').val(project.discount_recovery_value)
        } else {
            $('#discount_recovery_value').val(10)
        }
        if (project.whatsapp_button == 1) {
            $('#whatsapp_button .whatsapp_button_yes').attr('selected', true);
        } else {
            $('#whatsapp_button .whatsapp_button_no').attr('selected', true);
        }
        $('#pre_selected_installment').val(project.pre_selected_installment);
        $('#required_email_checkout').val(project.required_email_checkout);
        $('#document_type_checkout').val(project.document_type_checkout);
        //select cartão de credito no checkout
        // if (project.credit_card == 1) {
        //     $('#credit_card .credit_card_yes').attr('selected', true);
        // } else {
        //     $('#credit_card .credit_card_no').attr('selected', true);
        // }

        //inicializando switchs
        $('[name=countdown_timer_flag]').prop('checked', !!(project.countdown_timer_flag))
        $('[name=countdown_timer_color]').val(project.countdown_timer_color)
        $('[name=countdown_timer_time]').val(project.countdown_timer_time)
        $('[name=countdown_timer_description]').val(project.countdown_timer_description)
        $('[name=countdown_timer_finished_message]').val(project.countdown_timer_finished_message || 'Seu tempo acabou! Você precisa finalizar sua compra imediatamente.')
        $('.color-options').find('[data-color="' + project.countdown_timer_color + '"]').addClass('active');

        $('.custom_message_box').hide('fast', 'linear');
        if(project.custom_message_configs &&  project.custom_message_configs.active){
            $('[name=custom_message_switch]').prop('checked', project.custom_message_configs.active);
            $('[name=custom_message_title]').val(project.custom_message_configs.title);
            $('[name=custom_message_content]').val(project.custom_message_configs.message);
            $('.custom_message_box').show('fast', 'linear');
        }

        let is_checked_finalizing_purchase_config = !!(project.finalizing_purchase_config_toogle)
        let is_checked_checkout_notification_config = !!(project.checkout_notification_config_toogle)

        $('[name=checkout_notification_config_toogle]').prop('checked', is_checked_checkout_notification_config)
        if (is_checked_checkout_notification_config) {
            $('.checkout_notification_config').removeClass('d-none')
        }

        $('[name=checkout_notification_config_time]').val((project.checkout_notification_config_time || 30))
        $('[name=checkout_notification_mobile]').val((project.checkout_notification_config_mobile || 1))
        // $('[name=checkout_notification_config_messages]').val((project.checkout_notification_config_message || [] ))

        if (project.checkout_notification_config_messages) {

            let config_nessages_keys = Object.keys(project.checkout_notification_config_messages);

            config_nessages_keys.map((id) => {
                $('input[name="checkout_notification_config_messages[' + id + ']"]').prop("checked", true)
            });

        }

        if (project.checkout_notification_config_messages_min_value) {

            let obj = project.checkout_notification_config_messages_min_value
            Object.keys(obj).forEach(key => {
                $('input[name="checkout_notification_config_messages_min_value[' + key + ']"]').val(obj[key])
            });

        }

        $('[name=finalizing_purchase_config_toogle]').prop('checked', is_checked_finalizing_purchase_config)
        if (is_checked_finalizing_purchase_config) {
            $('.finalizing_purchase_config').removeClass('d-none')
        }
        $('[name=finalizing_purchase_config_text]').val((project.finalizing_purchase_config_text || 'Outras {visitantes} pessoas estão finalizando a compra neste momento.'))
        $('[name=finalizing_purchase_config_min_value]').val((project.finalizing_purchase_config_min_value || 10))

        if (project.countdown_timer_flag) {
            $('.countdown-config').show('fast', 'linear')
        } else {
            $('.countdown-config').hide('fast', 'linear')
        }

        $('#product_amount_selector').prop('checked', !!(project.product_amount_selector));
    }

    function supportphoneVerified() {
        $("#message_not_verified_support_phone").css("display", "none");
        $("#input_group_support_phone").css("border-color", "forestgreen");
        $("#support_phone").css("border-color", "forestgreen");
        $("#input_group_support_phone").append().html("<i class='fas fa-check' data-toggle='tooltip' data-placement='left' title='Telefone de suporte verificado!' style='color:forestgreen;'></i>");
    }

    function supportphoneNotVerified() {
        $("#message_not_verified_support_phone").css("display", "");
        $("#input_group_support_phone").css("border-color", "red");
        $("#support_phone").css("border-color", "red");
        $("#input_group_support_phone").append().html("<i class='fas fa-times' data-toggle='tooltip' data-placement='left' title='Telefone de suporte não verificado!' style='color:#ff0000;'></i>");
    }

    function contactVerified() {
        $("#message_not_verified_contact").css("display", "none");
        $("#input_group_contact").css("border-color", "forestgreen");
        $("#contact").css("border-color", "forestgreen");
        $("#input_group_contact").append().html("<i class='fas fa-check' data-toggle='tooltip' data-placement='left' title='Contato verificado!' style='color:forestgreen;'></i>");
    }

    function contactNotVerified() {
        $("#message_not_verified_contact").css("display", "");
        $("#input_group_contact").css("border-color", "red");
        $("#contact").css("border-color", "red");
        $("#input_group_contact").append().html("<i class='fas fa-times' data-toggle='tooltip' data-placement='left' title='Contato não verificado!' style='color:red;'></i>");
    }

    // Verificar email de contato
    $("#btn_verify_support_phone").on("click", function () {
        event.preventDefault();
        let support_phone = $("#support_phone").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/verifysupportphone',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                support_phone: support_phone,
            }, error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();

            }, success: function (response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
            }
        });
    });

    $("#match_support_phone_verifycode_form").on("submit", function (event) {
        event.preventDefault();
        let verify_code = $("#support_phone_verify_code").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/matchsupportphoneverifycode',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                verifyCode: verify_code
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                $('#modal_verify_support_phone').modal('hide');
                $('#support_phone_verify_code').val('');
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                supportphoneVerified();
            }
        });
    });

    $("#match_contact_verifycode_form").on("submit", function (event) {
        event.preventDefault();
        let verify_code = $("#contact_verify_code").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/matchcontactverifycode',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                verifyCode: verify_code
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                $('#modal_verify_contact').modal('hide');
                $('#contact_verify_code').val('');
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                contactVerified();
            }
        });
    });

    $('#credit_card_discount').mask('000', {
        reverse: true,
        onKeyPress: function (val, e, field, options) {
            if (val > 100) {
                $('#credit_card_discount').val('')
            }
        }
    });

    $('#billet_discount').mask('000', {
        reverse: true,
        onKeyPress: function (val, e, field, options) {
            if (val > 100) {
                $('#billet_discount').val('')
            }
        }
    });

    // Verificar email de contato
    $("#btn_verify_contact").on("click", function () {
        event.preventDefault();
        loadingOnScreen();
        let contact = $("#contact").val();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/verifycontact',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                contact: contact
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
            }
        });
    });

    //carrega a tela de edicao do projeto
    function updateConfiguracoes() {
        loadOnAny('#tab_configuration_project .card');
        $.ajax({
            method: "GET",
            url: "/api/projects/" + projectId + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                loadOnAny('#tab_configuration_project .card', true);
                errorAjaxResponse(response);

            }, success: function (data) {
                renderProjectConfig(data);
                loadOnAny('#tab_configuration_project .card', true);
            }
        });
    }

    //abre o modal de delecao
    $('#bt-delete-project').on('click', function (event) {
        event.preventDefault();
        let name = $("#name").val();
        $("#modal_excluir_titulo").html("Remover projeto " + name + " ?");

        $("#modal-delete-project .btn-delete").on('click', function () {
            $("#modal-delete").modal('hide');
            loadingOnScreen()
            $.ajax({
                method: "DELETE",
                url: "/api/projects/" + projectId,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    errorAjaxResponse(response);

                    // alertCustom('error', 'Ocorreu algum erro');
                    loadingOnScreenRemove()
                },
                success: function (data) {
                    loadingOnScreenRemove();

                    if (data == 'success') {
                        window.location = "/projects";
                    } else {
                        alertCustom('error', "Erro ao deletar projeto");
                    }
                }
            });
        });

    });

    //atualiza as configuracoes do projeto
    $("#bt-update-project").on('click', function (event) {
        if ($('#photo_w').val() == '0' || $('#photo_h').val() == '0') {
            alertCustom('error', 'Selecione as dimensões da imagem de capa');
            return false;
        }

        event.preventDefault();
        loadingOnScreen();

        parcelas = parseInt($(".installment_amount option:selected").val());
        parcelasJuros = parseInt($(".parcelas-juros option:selected").val());

        $('#terms_affiliates').val(termsaffiliates.getData());

        let verify = verificaParcelas(parcelas, parcelasJuros);
        let statusUrlAffiliates = 0;

        if ($('#status-url-affiliates').prop('checked')) {
            statusUrlAffiliates = 1;
        }

        let formData = new FormData(document.getElementById("update-project"));

        formData.append('status_url_affiliates', statusUrlAffiliates);

        let discountCard = $('#credit_card_discount').val().replace('%', '');
        let discountBillet = $('#billet_discount').val().replace('%', '');
        let discountPix = $('#pix_discount').val().replace('%', '');

        discountBillet = (discountBillet == '') ? 0 : discountBillet;
        discountCard = (discountCard == '') ? 0 : discountCard;

        formData.append('credit_card_discount', discountCard);
        formData.append('billet_discount', discountBillet);
        formData.append('pix_discount', discountPix);
        formData.set('countdown_timer_flag', $('[name=countdown_timer_flag]').is(':checked') ? '1' : '0');
        formData.set('product_amount_selector', $('#product_amount_selector').is(':checked') ? '1' : '0');
        formData.set('custom_message_switch', $('[name=custom_message_switch]').is(':checked') ? '1' : '0');

        formData.set('finalizing_purchase_config_toogle', $('[name=finalizing_purchase_config_toogle]').is(':checked') ? '1' : '0');
        formData.set('checkout_notification_config_toogle', $('[name=checkout_notification_config_toogle]').is(':checked') ? '1' : '0');

        if (!verify) {
            $.ajax({
                method: "POST",
                url: "/api/projects/" + projectId,
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: formData,
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);

                }, success: function (response) {
                    alertCustom('success', response.message);

                    $('html, body').animate({
                        scrollTop: $('#bt-update-project').offset().top
                    }, 'slow');

                    $("#image-logo-email").imgAreaSelect({remove: true});
                    $("#previewimage").imgAreaSelect({remove: true});
                    show();
                    loadingOnScreenRemove();


                }
            });
        } else {
            $("#error-juros").css('display', 'block');
            loadingOnScreenRemove();
        }

    });

    // Sincroniza template do shopify
    $("#bt-shopify-sincronization-template").on('click', function (event) {
        event.preventDefault();

        $("#modal-change-shopify-integration-title").html('Sincronizar template');
        $("#modal-change-shopify-integration-text").html(`
            Antes de sincronizar um novo tema em sua loja, tenha em mente que as configurações
            feitas antes serão atualizadas, podendo alterar o funcionamento de sua loja.
            Em caso de dúvidas, entre em contato com o suporte pelo chat.
        `);

        $("#bt-modal-change-shopify-integration").unbind('click');
        $("#bt-modal-change-shopify-integration").on('click', function () {
            $("#bt-close-modal-change-shopify-integration").click();
            loadingOnScreen();

            $.ajax({
                method: 'POST',
                url: '/api/apps/shopify/synchronize/templates',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {
                    project_id: projectId
                },
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);

                    // window.location.reload();
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    alertCustom('success', response.message);
                }
            });
        });
    });

    $('#bt-change-shopify-integration').on('click', function (event) {
        event.preventDefault();

        let integrationStatus = $(this).attr('integration-status');

        if (integrationStatus == 2) {
            $("#modal-change-shopify-integration-title").html("Desfazer integração com shopify?");
            $("#modal-change-shopify-integration-text").html('Ao realizar essa operação os pagamentos não serão processados pelo checkout do CloudFox');
        } else if (integrationStatus == 3) {
            $("#modal-change-shopify-integration-title").html("Integrar com shopify?");
            $("#modal-change-shopify-integration-text").html('Ao realizar essa operação os pagamentos serão processados pelo checkout do CloudFox');
        }

        $("#bt-modal-change-shopify-integration").on('click', function () {

            $("#bt-close-modal-change-shopify-integration").click();

            loadingOnScreen();
            if (integrationStatus == 2) {
                $.ajax({
                    method: "POST",
                    url: "/api/apps/shopify/undointegration",
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    data: {
                        project_id: projectId
                    },

                    error: function (response) {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                        window.location.reload();
                    },
                    success: function (response) {
                        loadingOnScreenRemove();
                        alertCustom('success', response.message);
                        window.location.reload();
                    }
                });
            } else {
                $.ajax({
                    method: "POST",
                    url: "/api/apps/shopify/reintegration",
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    data: {
                        project_id: projectId
                    },

                    error: function (response) {
                        errorAjaxResponse(response);
                        loadingOnScreenRemove()
                        window.location.reload();
                    },
                    success: function (response) {
                        loadingOnScreenRemove();
                        alertCustom('success', response.message);
                        window.location.reload();
                    }
                });
            }
        });
    });

    // Sincroniza produtos do shopify/
    $("#bt-shopify-sincronization-product").on('click', function (event) {
        event.preventDefault();

        $("#modal-change-shopify-integration-title").html('Sincronizar produtos');
        $("#modal-change-shopify-integration-text").html('Sincronizar produtos da sua loja shopify com o CloudFox');

        $("#bt-modal-change-shopify-integration").unbind('click');
        $("#bt-modal-change-shopify-integration").on('click', function () {
            $("#bt-close-modal-change-shopify-integration").click();
            loadingOnScreen();

            $.ajax({
                method: 'POST',
                url: '/api/apps/shopify/synchronize/products',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {
                    project_id: projectId
                },
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                    window.location.reload();
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    alertCustom('success', response.message);
                }
            });
        });

    });

    function validToken() {
        const shopifyToken = $('#shopify-token').val();
        const regex = new RegExp('^([a-zA-Z0-9_]{10,100})$');

        if (regex.exec(shopifyToken) == null) {
            alertCustom('error', 'O token deve ter entre 10 e 100 letras e números');
            return false;
        }

        return true;
    }

    let btnTokenClick = "enable click";
    $('.btn-edit-token').on('click', function (event) {
        event.preventDefault();

        if (btnTokenClick == "enable click") {
            btnTokenClick = "update click";
            $('#shopify-token').prop("disabled", false);
            $('.btn-edit-token').text('Salvar').addClass('bg-grey-700');
            return;
        }

        if (!validToken()) {
            return false;
        }

        loadingOnScreen();
        $.ajax({
            method: 'POST',
            url: '/api/apps/shopify/updatetoken',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project_id: projectId,
                token: $('#shopify-token').val(),
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadingOnScreenRemove();
                btnTokenClick = "enable click";
                $('#shopify-token').prop("disabled", true);
                $('.btn-edit-token').text('Alterar').removeClass('bg-grey-700');
                alertCustom('success', response.message);
            }
        });

    });

    $('#bt-shopify-verify-permissions').on('click', function (event) {
        event.preventDefault();
        loadingOnScreen();
        $.ajax({
            method: 'POST',
            url: '/api/apps/shopify/verifypermissions',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project_id: projectId,
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });

    $('#skiptocart-input').on('change', function () {
        let input = $(this);
        let messageTitle = '';
        let messageDescription = '';
        [messageTitle, messageDescription] = messagesSwalSkipToCart(input);

        swal({
            title: messageTitle,
            text: messageDescription,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085D6',
            cancelButtonColor: '#DD3333',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Confirmar'
        }).then(function (data) {
            if (data.value) {
                switchSkipToCartOpacity(input, '.5', true);
                ajaxUpdateSkipToCart(input);
            } else {
                changeSwitchSkipToCart(input);
            }
        }).catch(function (reason) {
            alertCustom('error', 'Ocorreu um erro, tente novamente mais tarde!');
        });
    });

    function switchSkipToCartOpacity(input, value, disabled) {
        input.attr('disabled', disabled)
            .parent()
            .parent()
            .css('opacity', value);
    }

    function messagesSwalSkipToCart(input) {
        if (input.is(":checked")) {
            return [
                'Deseja habilitar o skip to cart?',
                'Habilitando o skip to cart o template é atualizado podendo ocorrer erros, em caso de dúvidas, entre em contato com o suporte pelo chat antes de atualizar..'
            ];
        } else {
            return [
                'Deseja desabilitar o skip to cart?',
                'Desabilitando o skip to cart o template é atualizado podendo ocorrer erros, em caso de dúvidas, entre em contato com o suporte pelo chat antes de atualizar..'
            ];
        }
    }

    function changeSwitchSkipToCart(input) {
        if (input.is(":checked")) {
            input.prop("checked", '')
        } else {
            input.prop("checked", 'checked')
        }
    }

    function ajaxUpdateSkipToCart(input) {
        $.ajax({
            method: 'POST',
            url: '/api/apps/shopify/skiptocart',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project_id: projectId,
                skip_to_cart: parseInt(input.val()),
            },
            error: function (response) {
                errorAjaxResponse(response);
                changeSwitchSkipToCart(input);
                switchSkipToCartOpacity(input, '1', false);
            },
            success: function (response) {
                alertCustom('success', response.message);
                switchSkipToCartOpacity(input, '1', false);
            }
        });
    }

    $("#bt-shopify-sync-trackings").on("click", function () {

        $.ajax({
            method: 'POST',
            url: '/api/apps/shopify/synchronize/trackings',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project_id: projectId,
            },
            error: function (response) {
                alertCustom('success', 'Os códigos de rastreio sendo importados...')
            },
            success: function (response) {
                alertCustom('success', response.message);
            }
        });
    });

    $('.discount-recovery').on("click", function () {
        recoveryDiscountColor()
    })

    recoveryDiscountColor()

    function recoveryDiscountColor() {
        let chk = $('.discount-recovery').prop('checked');
        if (chk) {
            $('#discount_recovery_value').show('fast', 'linear')
            $('#discount-recovery-alert').show('fast', 'linear')
        } else {
            $('#discount_recovery_value').hide('fast', 'linear')
            $('#discount-recovery-alert').hide('fast', 'linear')
        }
    }

    $('.discount-recovery').on("click", function () {
        recoveryDiscountColor()
    })

    $('.status-url-affiliates').on("click", function () {
        statusUrlAffiliatesColor()
    })

    $('#countdown_timer_flag').off().on('click', function () {
        let checked = $('[name=countdown_timer_flag]').prop('checked');
        if (checked) {
            $('.countdown-config').show('fast', 'linear')
        } else {
            $('.countdown-config').hide('fast', 'linear')
        }
    });

    $('#custom_message_switch').off().on('click', function () {
        let checked = $('[name=custom_message_switch]').prop('checked');
        if (checked) {
            $('.custom_message_box').show('fast', 'linear')
        } else {
            $('.custom_message_box').hide('fast', 'linear')
        }
    });



    statusUrlAffiliatesColor()

    function statusUrlAffiliatesColor() {
        let chk = $('.status-url-affiliates').prop('checked');
        if (chk) {
            $('.div-url-affiliate').show('fast', 'linear')
        } else {
            $('.div-url-affiliate').hide('fast', 'linear')
        }
    }

    let colorOptions = $('.color-options > div');
    colorOptions.on('click', function () {
        $('[name=countdown_timer_color]').val($(this).data('color'));
        colorOptions.removeClass('active');
        $(this).addClass('active');
    });

    $('#finalizing_purchase_config').on("click", function () {
        let is_checked = $('#finalizing_purchase_config').prop('checked');

        if (is_checked) {
            $('.finalizing_purchase_config').removeClass('d-none')
            $('#finalizing_purchase_config_error').show('fast', 'linear')
        } else {
            $('.finalizing_purchase_config').addClass('d-none')
            $('#finalizing_purchase_config_error').hide('fast', 'linear')
        }
    })


    $('#checkout_notification_config').on("click", function () {
        let is_checked = $('#checkout_notification_config').prop('checked');

        if (is_checked) {
            $('.checkout_notification_config').removeClass('d-none')
            $('#checkout_notification_config_error').show('fast', 'linear')
        } else {
            $('.checkout_notification_config').addClass('d-none')
            $('#checkout_notification_config_error').hide('fast', 'linear')
        }
    })

    let firstCategory = [
        "tab-domains",
        "tab_plans",
        "tab-fretes",
    ]

    let secondCategory = [
        "tab_pixels",
        "tab_upsell",
        "tab_order_bump",
        "tab_coupons",
        "tab_reviews",
    ]

    let thirdCategory = [
        "tab_sms",
    ]

    $('.nav-tabs-horizontal .nav-link').click((e) => {
        let currentActive = $('.nav-link.active')
        let currentElement = e.target.id

        if (currentActive.attr('id') !== currentElement) {
            currentActive.removeClass('active')
        }

        if ($.inArray(currentElement, firstCategory) !== -1) {
            $('#first-category').css('color', '#2E85EC')
            $('#second-category').css('color', '#9C9C9C')
            $('#third-category').css('color', '#9C9C9C')
        }

        if ($.inArray(currentElement, secondCategory) !== -1) {
            $('#first-category').css('color', '#9C9C9C')
            $('#second-category').css('color', '#2E85EC')
            $('#third-category').css('color', '#9C9C9C')
        }

        if ($.inArray(currentElement, thirdCategory) !== -1) {
            $('#first-category').css('color', '#9C9C9C')
            $('#second-category').css('color', '#9C9C9C')
            $('#third-category').css('color', '#2E85EC')
        }
    })

    $('.slick-track').on('click', function () {
        $('.nav-tabs-horizontal .tab-pane').removeClass('active show');
    });

    function formatMoney(value) {
        return ((value / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }));
    }

    let pix_account_element = $("#pix_discount");
    let credit_card_discount_element = $("#credit_card_discount");
    let billet_discount_element = $("#billet_discount");

    if (pix_account_element.val() < 1) {
        setZetoToPixDiscount(pix_account_element);
    }
    if (credit_card_discount_element.val() < 1) {
        setZetoToPixDiscount(credit_card_discount_element);
    }
    if (billet_discount_element.val() < 1) {
        setZetoToPixDiscount(billet_discount_element);
    }

    pix_account_element.on("change", function () {
        if ($(this).val() < 1) {
            setZetoToPixDiscount(pix_account_element);
        }
    })
    credit_card_discount_element.on("change", function () {
        if ($(this).val() < 1) {
            setZetoToPixDiscount(credit_card_discount_element);
        }
    })
    billet_discount_element.on("change", function () {
        if ($(this).val() < 1) {
            setZetoToPixDiscount(billet_discount_element);
        }
    })

    function setZetoToPixDiscount(element) {
        element.val(0);
    }

    $('.nav-tabs-horizontal-custom').on('click', '.nav-link', function() {
        $('.slick-slider').find('.nav-link').each(function() {
            if ($(this).hasClass('show')) {
                $(this).addClass('active');
            }
        });
    });

    $('.slick-slider').on('click', '.nav-link', function() {
        let tabId = $(this).attr('id');

        $('.slick-slider').find('.nav-link').each(function() {
            if ($(this).attr('id') != tabId) {
                $(this).removeClass('show');
            }
        });
    });
});
