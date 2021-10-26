const statusPixel = {
    1: "success",
    0: "danger",
};

const srcPlatforms = {
    'google_analytics': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/old-analytics',
    'google_analytics_four': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/analytics',
    'google_adwords': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/google-ads',
    'facebook': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/facebook',
    'outbrain': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/outbrain',
    'taboola': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/taboola',
    'pinterest': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/pinterest',
    'uol_ads': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/uol-ads',
    'tiktok': 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/tiktok'
}

$(function () {
    let currentPage;

    const projectId = $(window.location.pathname.split('/')).get(-1);

    //comportamentos da tela
    $('.tab_pixels').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarPixel(currentPage);
        $(this).off();
    });

    function isChecked(input, pixelAttribute = null) {
        if (pixelAttribute != null) {
            if (pixelAttribute == '1' || pixelAttribute == 'true') {
                input.prop('checked', true);
            } else {
                input.prop('checked', false);
            }
        } else {
            if (input.is(':checked')) {
                input.attr('checked', false);
            } else {
                input.attr('checked', 'checked');
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
            link = '/api/project/' + projectId + '/pixels';
        } else {
            link = '/api/project/' + projectId + '/pixels' + link;
        }

        loadOnTable('#data-table-pixel', '#table-pixel');
        $("#pagination-pixels").html('');

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                $("#data-table-pixel").html(response.message);
            },
            success: function success(response) {
                $("#data-table-pixel, #pagination-pixels").html('');

                if (response.data == '') {
                    $('#table-pixel').addClass('table-striped');
                    $("#data-table-pixel").html(`
                        <tr class="text-center">
                            <td colspan="8" style="height: 70px; vertical-align: middle;">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    `);
                    return;
                }

                $.each(response.data, function (index, value) {
                    $("#data-table-pixel").append(`
                        <tr>
                            <td>${value.name}</td>
                            <td>${value.code}</td>
                            <td>${value.platform_enum}</td>
                            <td><span class="badge badge-${statusPixel[value.status]}">${value.status_translated}</span></td>
                            <td style='text-align:center'>
                                <a role='button' title='Visualizar' class='mg-responsive details-pixel pointer' pixel='${value.id}' data-target='#modal-details-pixel' data-toggle='modal'><span class="o-eye-1"></span></a>
                                <a role='button' title='Editar' class='mg-responsive edit-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><span class="o-edit-1"></span></a>
                                <a role='button' title='Excluir' class='mg-responsive delete-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><span class='o-bin-1'></span></a>
                            </td>
                        </tr>
                    `);
                    $('#table-pixel').addClass('table-striped');
                });

                pagination(response, 'pixels', atualizarPixel);
            }
        });
    }

    /**
     * SHOW PIXEL
     */

    // Show Pixel
    $(document).on('click', '.details-pixel', function () {
        let pixel = $(this).attr('pixel');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error() {
                errorAjaxResponse(response);

            }, success: function success(response) {
                renderDetailPixel(response);
            }
        });
    });

    // Rendere Modal Show Pixel
    function renderDetailPixel(pixel) {
        $('#modal-detail-pixel .pixel-description').html(pixel.name);
        $('#modal-detail-pixel .pixel-code').html(pixel.code);
        $('#modal-detail-pixel .pixel-platform').html(pixel.platform_enum);
        $('#modal-detail-pixel .pixel-status').html(pixel.status == 1
            ? '<span class="badge badge-success text-left">Ativo</span>'
            : '<span class="badge badge-danger">Desativado</span>');
        $('#modal-detail-pixel').modal('show');
    }

    /**
     * Edit Pixel
     */
    let pixelEdit = {};
    $(document).on('click', '.edit-pixel', function () {
        $.ajax({
            method: "GET",
            url: `/api/project/${projectId}/pixels/${$(this).attr('pixel')}/edit`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                const pixel = response.data;
                console.log(pixel);
                pixelEdit = pixel;
                renderModalPixelEdit(pixel);
                openModalEditPixel();
                $("#modal-edit-pixel").modal('show');
            }
        });
    });

    function renderModalPixelEdit(pixel, newPlatform = null) {
        resetInputs();

        const imgPlatform = $(".img-edit-selected");
        const codeEditInput = $(".code-edit");

        if (newPlatform == null) {
            newPlatform = pixel.platform;
        }
        $(".platform-edit").val(newPlatform);

        imgPlatform.attr('src', srcPlatforms[newPlatform]);

        $(".description-edit").val(pixel.name);
        codeEditInput.val(pixel.code);
        $('.percentage-boleto-value-edit').val(pixel.value_percentage_purchase_boleto);

        // plans
        const plansInput = $(".apply_plans");
        plansInput.val(null).trigger('change');
        plansInput.html('');
        let applyOnPlans = [];
        for (let plan of pixel.apply_on_plans) {
            applyOnPlans.push(plan.id);
            plansInput.append(`
                <option value="${plan.id}">
                    ${plan.name + (plan.description ? ' - ' + plan.description : '')}
                </option>
            `);
        }
        plansInput.val(applyOnPlans);

        // Run Pixel
        isChecked($(".status-edit"), pixel.status);
        isChecked($(".checkout-edit"), pixel.checkout);
        isChecked($(".purchase-boleto-edit"), pixel.purchase_boleto);
        isChecked($(".purchase-card-edit"), pixel.purchase_card);
        isChecked($(".purchase-pix-edit"), pixel.purchase_pix);

        // Manipulation Modal pixel
        changePlaceholderInput(newPlatform, codeEditInput, $("#text-type-code-edit"));

        switch (newPlatform) {
            case 'facebook':
                pixelFacebook(pixel);
                break;
            case 'taboola':
            case 'outbrain':
                pixelTaboolaOutbrain(pixel);
                break;
        }
    }

    function resetInputs() {
        $(".input-purchase-event-name-edit").val();
        $("#select-facebook-integration-edit, #div-facebook-token-api-edit, #facebook-token-api-edit, .div-purchase-event-name-edit").hide();
    }

    /**
     * Edit Facebook Manipulation
     */
    function pixelFacebook(pixel) {
        if (pixel.is_api) {
            $("#facebook-token-api-edit").prop('readonly', false).val(pixel.facebook_token);
            $(".facebook-api-edit").prop('checked', 'checked');
            $(".url_facebook_domain_edit").val(pixel.url_facebook_domain);
            $(".url_facebook_api_div_edit").show();
        } else {
            $(".url_facebook_domain_edit").val('');
            $(".url_facebook_api_div_edit").hide();
            $(".facebook-api-default-edit").prop('checked', 'checked')
            $("#facebook-token-api-edit").prop('readonly', true).val('');
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
        if (this.value === 'api') {
            $(".url_facebook_api_div_edit").show();
            $("#facebook-token-api-edit").prop('readonly', false).val(pixelEdit.facebook_token);
        } else {
            $(".url_facebook_api_div_edit").hide();
            $("#facebook-token-api-edit").prop('readonly', true).val();
        }
    });

    $("#modal-edit-pixel .img-edit-selected").on('click', function () {
        openModalEditPixel(true);
    });

    function openModalEditPixel(selectPlatform = false) {
        if (selectPlatform) {
            $("#modal-edit-pixel #configure-edit-pixel").hide();
            $("#modal-edit-pixel #select-platform-edit-pixel").show();
        } else {
            $("#modal-edit-pixel #select-platform-edit-pixel").hide();
            $("#modal-edit-pixel #configure-edit-pixel").show();
        }
    }

    $("#modal-edit-pixel img.logo-pixels-edit").on('click', function () {
        renderModalPixelEdit(pixelEdit, $(this).data('value'));
        openModalEditPixel();
    });


    //Update Pixel
    $("#btn-update-pixel").on('click', function () {
        const inputDescriptionEdit = $("#modal-edit-pixel .description-edit").val();
        const inputPlatformEdit = $("#modal-edit-pixel .platform-edit").val();
        const isApi = $("#modal-edit-pixel input[type=radio]:checked").val();
        const inputCodeEdit = $("#modal-edit-pixel .code-edit").val();
        const valuePercentagePurchaseBoleto = $("#modal-edit-pixel .percentage-boleto-value-edit").val();
        const facebookTokenApi = $("#modal-edit-pixel #facebook-token-api-edit").val();
        const inputPurchaseEventName = $("#modal-edit-pixel .input-purchase-event-name-edit").val();
        const plansApply = $("#modal-edit-pixel .apply_plans").val();

        if (!validateDataPixelForm({
            'name': inputDescriptionEdit,
            'platform': inputPlatformEdit,
            'is_api': isApi,
            'code': inputCodeEdit,
            'value_percentage_purchase_boleto': valuePercentagePurchaseBoleto,
            'facebook_token_api': facebookTokenApi,
            'purchase_event_name': inputPurchaseEventName,
            'plans_apply': plansApply
        })) {
            return false;
        }

        $.ajax({
            method: "PUT",
            url: `/api/project/${projectId}/pixels/${pixelEdit.id_code}`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                name: inputDescriptionEdit,
                code: inputCodeEdit,
                platform: inputPlatformEdit,
                status: $("#modal-edit-pixel .status-edit").is(':checked'),
                checkout: $("#modal-edit-pixel .checkout-edit").is(':checked'),
                purchase_card: $("#modal-edit-pixel .purchase-card-edit").is(':checked'),
                purchase_boleto: $("#modal-edit-pixel .purchase-boleto-edit").is(':checked'),
                purchase_pix: $("#modal-edit-pixel .purchase-pix-edit").is(':checked'),
                edit_pixel_plans: plansApply,
                purchase_event_name: inputPurchaseEventName,
                is_api: isApi,
                facebook_token_api: facebookTokenApi,
                value_percentage_purchase_boleto: valuePercentagePurchaseBoleto,
                url_facebook_domain_edit: $("#modal-edit-pixel .url_facebook_domain_edit").val()
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success() {
                $("#modal-edit-pixel").modal('hide');
                alertCustom("success", "Pixel atualizado com sucesso");
                atualizarPixel(currentPage);
            }
        });
    });

    /**
     * DELETE PIXEL
     */

    // Open Modal Destroy Pixel
    $(document).on('click', '.delete-pixel', function (event) {
        $("#modal-delete-pixel .btn-delete").attr("pixel", $(this).attr('pixel'));
        $("#modal-delete-pixel").modal('show');
    });

    // Delete Pixel
    $(document).on('click', '#modal-delete-pixel .btn-delete', function () {
        const pixel = $(this).attr('pixel');
        $.ajax({
            method: "DELETE",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (_error3) {
                function error() {
                    return _error3.apply(this, arguments);
                }

                error.toString = function () {
                    return _error3.toString();
                };

                return error;
            }(function (response) {
                errorAjaxResponse(response);
            }),
            success: function success() {
                alertCustom("success", "Pixel Removido com sucesso");
                atualizarPixel(currentPage);
            }

        });
    });

    /**
     * CREATE NEW PIXEL
     */

    // Open Modal New Pixel
    $("#add-pixel").on('click', function () {
        openModalCreatePixel();
        $("#modal-create-pixel").modal('show');
    });

    // change pixel platform
    $("img.img-selected").on('click', function () {
        openModalCreatePixel();
    });

    function openModalCreatePixel() {
        $("#configure-new-pixel").hide();
        $("#select-platform-pixel").show();
    }

    function changePlaceholderInput(value, inputPlatform, inputAW) {
        inputAW.html('').hide();

        switch (value) {
            case "facebook":
                inputPlatform.attr("placeholder", '52342343245553');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case "google_adwords":
                inputAW.html('AW-').show();
                inputPlatform.attr("placeholder", '8981445741-4/AN7162ASNSG');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case "google_analytics":
                inputPlatform.attr("placeholder", 'UA-8984567741-3');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case "google_analytics_four":
                inputPlatform.attr("placeholder", 'G-KZSV4LMBAC');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case "taboola":
                inputPlatform.attr("placeholder", '1010100');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case "outbrain":
                inputPlatform.attr("placeholder", '00de2748d47f2asdl39877mash');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case 'uol_ads':
                inputPlatform.attr("placeholder", 'hutu27');
                inputPlatform.parent().parent().find('label').html('Código');
                break;
            case 'tiktok':
                inputPlatform.attr("placeholder", 'C5OSDKKVNBDLN9M5C6UG');
                inputPlatform.parent().parent().find('label').html('ID');
                break;
            default:
                inputPlatform.attr("placeholder", 'Código');
                inputPlatform.parent().parent().find('label').html('Código');
        }
    }

    $("img.logo-pixels-create").on('click', function () {
        const platform = $(this).data('value');
        $("#platform").val('').val(platform);
        $(".img-logo").attr('src', this.src);

        $("#select-facebook-integration, #div-facebook-token-api, .purchase-event-name-div, .url_facebook_api_div").hide();

        changePlaceholderInput(platform, $("#code-pixel"), $("#input-code-pixel"));

        if (platform === 'facebook') {
            $("#select-facebook-integration, #div-facebook-token-api").show();
            if ($("input[type=radio]").val() == 'api') {
                $(".url_facebook_api_div").show();
                $("#facebook-token-api").attr('readonly', false)
            } else if ($("input[type=radio]").val() == 'default') {
                $(".select-default-facebook").click();
                $("#facebook-token-api").attr('readonly', true)
            }
        } else if (['taboola', 'outbrain'].includes(platform)) {
            $(".purchase-event-name-div").show();
        }

        $("input[type=radio]").change(function () {
            $(".url_facebook_api_div").hide();
            if (this.value === 'api') {
                $(".url_facebook_api_div").show();
                $("#facebook-token-api").attr('readonly', false)
            } else {
                $("#facebook-token-api").attr('readonly', true)
            }
        });

        $("#select-platform-pixel").hide();
        $("#configure-new-pixel").show();
    });

    function validateDataPixelForm(formData) {
        if (formData.name.length > 100) {
            alertCustom('error', 'O campo Descrição permite apenas 100 caracteres')
            return false;
        }

        if (formData.name.length < 1) {
            alertCustom('error', 'O campo Descrição é obrigatório')
            return false;
        }

        if (formData.code.length < 1) {
            alertCustom('error', 'O campo Código é obrigatório')
            return false;
        }

        if (formData.value_percentage_purchase_boleto.length > 3) {
            alertCustom('error', 'O valore do campo % Valor Boleto está incorreto!')
            return false;
        }

        if (formData.value_percentage_purchase_boleto.length > 0 && (formData.value_percentage_purchase_boleto > 100 || formData.value_percentage_purchase_boleto < 10)) {
            alertCustom('error', 'O valores permitidos para o campo % Valor Boleto deve ser entre 10 e 100')
            return false;
        }

        if (formData.platform === 'facebook' && formData.is_api === 'api' && formData.facebook_token_api.length < 1) {
            alertCustom('error', 'O campo Token Acesso API de Conversões é obrigatório');
            return false;
        }

        if (['taboola', 'outbrain'].includes(formData.platform) && formData.purchase_event_name.length < 1) {
            alertCustom('error', 'O campo Nome evento de conversão é obrigatório');
            return false;
        }

        if (formData.plans_apply == null) {
            alertCustom('error', 'É obrigatório selecionar um ou mais planos');
            return false;
        }

        return true;
    }

    //Save Create new Pixel
    $("#modal-create-pixel #btn-store-pixel").on('click', function () {
        const formData = new FormData(document.querySelector('#modal-create-pixel  #form-register-pixel'));

        formData.append('status', $("#modal-create-pixel .status").is(':checked'));
        formData.append('checkout', $("#modal-create-pixel .checkout").is(':checked'));
        formData.append('purchase_card', $("#modal-create-pixel .purchase-card").is(':checked'));
        formData.append('purchase_boleto', $("#modal-create-pixel .purchase-boleto").is(':checked'));
        formData.append('purchase_pix', $("#modal-create-pixel .purchase-pix").is(':checked'));

        if (!validateDataPixelForm({
            'name': formData.get('name'),
            'platform': formData.get('platform'),
            'is_api': formData.get('api-facebook'),
            'code': formData.get('code'),
            'value_percentage_purchase_boleto': formData.get('value_percentage_purchase_boleto'),
            'facebook_token_api': formData.get('facebook-token-api'),
            'purchase_event_name': formData.get('purchase-event-name'),
            'plans_apply': formData.get('add_pixel_plans[]')
        })) {
            return false;
        }

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/pixels",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $("#modal-create-pixel").modal('hide');
                alertCustom("success", response.message);
                atualizarPixel();
            }
        });
    });

    // Select Plans
    $('#add_pixel_plans').select2(Object.assign(selectPlan(), {dropdownParent: $('#modal-create-pixel')}));
    $('.edit-plans').select2(Object.assign(selectPlan(), {dropdownParent: $('#modal-edit-pixel')}));

    function selectPlan() {
        return {
            placeholder: 'Nome do plano',
            multiple: true,
            language: {
                noResults: function () {
                    return 'Nenhum plano encontrado';
                },
                searching: function () {
                    return 'Procurando...';
                },
                loadingMore: function () {
                    return 'Carregando mais planos...';
                },
            },
            ajax: {
                data: function (params) {
                    return {
                        list: 'plan',
                        search: params.term,
                        project_id: projectId,
                        page: params.page || 1
                    };
                },
                method: "GET",
                url: "/api/plans/user-plans",
                delay: 300,
                dataType: 'json',
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                processResults: function (res) {
                    if (res.meta.current_page === 1) {
                        const allObject = {
                            id: 'all',
                            name: 'Qualquer plano',
                            description: ''
                        };
                        res.data.unshift(allObject);
                    }

                    return {
                        results: $.map(res.data, function (obj) {
                            return {id: obj.id, text: obj.name + (obj.description ? ' - ' + obj.description : '')};
                        }),
                        pagination: {
                            'more': res.meta.current_page !== res.meta.last_page
                        }
                    };
                },
            }
        }
    }

    $("#add_pixel_plans, .edit-plans").on('select2:select', function () {
        const planSelect = $(this);
        if ((planSelect.val().length > 1 && planSelect.val().includes('all')) || (planSelect.val().includes('all') && planSelect.val() != 'all')) {
            planSelect.val('all').trigger("change");
        }
    });

    $(".btn-config-pixel").on('click', function () {
        $.ajax({
            method: "GET",
            url: "/api/projects/" + projectId + "/pixels/configs",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $("#metatag-verification-facebook").val(response.data.metatags_facebook);

                $("#modal-config-pixel").modal("show");
            }
        });
    });

    $(".btn-save-config-pixel").on('click', function () {
        $.ajax({
            method: "POST",
            url: "/api/projects/" + projectId + "/pixels/saveconfigs",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                'metatag-verification-facebook': $("#metatag-verification-facebook").val(),
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom("success", response.message);
                $("#modal-config-pixel").modal('hide');
            }
        });
    });
});
