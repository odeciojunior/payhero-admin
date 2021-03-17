const statusPixel = {
    1: "success",
    0: "danger",
};

const formatPlatform = {
    1: 'Facebook',
    2: 'Google Adwords',
    3: 'Google Analytics',
    4: 'Google Analytics 4.0',
    5: 'Taboola',
    6: 'Outbrain'
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

    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    if ($(':checkbox').is(':checked')) {
        $(':checkbox').val(1);
    } else {
        $(':checkbox').val(0);
    }

    $("#add-pixel").on('click', function () {
        const valueSelectPlatform = $("#modal-create-pixel #select-platform option:selected").val();

        $(".purchase-event-name-div, #api-facebook").hide();

        if (valueSelectPlatform == 'facebook') {
            $("#api-facebook").show();
        } else if (['taboola', 'outbrain'].includes(valueSelectPlatform)) {
            $(".purchase-event-name-div").show();
        }

        $("input[type=radio]").change(function () {
            if (this.value === 'api') {
                $("#div-facebook-token-api").show()
            } else {
                $("#div-facebook-token-api").hide()
            }
        });
    });

    $("#select-platform").change(function () {
        const value = $(this).val();
        $("#outbrain-info, #google-analytics-info, #api-facebook, .purchase-event-name-div, #div-facebook-token-api, #input-code-pixel").hide();

        $("#input-code-pixel").html('');
        switch (value) {
            case "facebook":
                $("#api-facebook").show();
                $("#code-pixel").attr("placeholder", '52342343245553');
                break;
            case "google_adwords":
                $("#input-code-pixel").html('AW-').show();
                $("#code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                break;
            case "google_analytics":
                $("#google-analytics-info").show();
                $("#code-pixel").attr("placeholder", 'UA-8984567741-3');
                break;
            case "google_analytics_four":
                $("#google-analytics-info").show();
                $("#code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                break;
            case "taboola":
                $("#code-pixel").attr("placeholder", '1010100');
                $(".purchase-event-name-div").show();
                break;
            case "outbrain":
                $(".purchase-event-name-div").show();
                $("#code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                break;
            default:
                $("#code-pixel").attr("placeholder", 'Código');
        }
    });

    // carregar modal de detalhes
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

    function renderDetailPixel(pixel) {
        $('#modal-detail-pixel .pixel-description').html(pixel.name);
        $('#modal-detail-pixel .pixel-code').html(pixel.code);
        $('#modal-detail-pixel .pixel-platform').html(pixel.platform);
        $('#modal-detail-pixel .pixel-status').html(pixel.status == 1
            ? '<span class="badge badge-success text-left">Ativo</span>'
            : '<span class="badge badge-danger">Desativado</span>');
        $('#modal-detail-pixel').modal('show');
    }

    $("#modal-edit-pixel input[type=radio]").change(function () {
        if (this.value === 'api') {
            $("#modal-edit-pixel #div-facebook-token-api").show()
        } else {
            $("#modal-edit-pixel #div-facebook-token-api").hide()
        }
    });
    // carregar modal de edicao
    $(document).on('click', '.edit-pixel', function () {
        $("#edit_pixel_plans").val(null).trigger('change');

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
                renderEditPixel(pixel);

                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });

                $("#modal-edit-pixel.api-facebook-check").change(function () {
                    if (this.value === 'api') {
                        $("#modal-edit-pixel#div-facebook-token-api").show()
                    } else {
                        $("#modal-edit-pixel#div-facebook-token-api").hide()
                    }
                });


                // troca o placeholder dos inputs
                $("#modal-edit-pixel #select-platform").change(function () {
                    const value = $(this).val();
                    $("#modal-edit-pixel #input-code-pixel-edit, #modal-edit-pixel #api-facebook,#modal-edit-pixel .purchase-event-name-div, #modal-edit-pixel #div-facebook-token-api").hide();


                    if (value === 'facebook') {
                        $("#modal-edit-pixel #api-facebook").show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", '52342343245553');
                    } else if (value === 'google_adwords') {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('AW-').show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                    } else if (value === 'google_analytics') {
                        $("#modal-edit-pixel #google-analytics-info").show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", 'UA-8984567741-3');
                    } else if (value === 'google_analytics_four') {
                        $("#modal-edit-pixel #google-analytics-info").show();
                        $("#code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                    } else if (value === 'taboola') {
                        $("#modal-edit-pixel .purchase-event-name-div").show();
                    } else if (value === 'outbrain') {
                        $("#modal-edit-pixel .purchase-event-name-div").show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                    } else {
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", 'Código');
                    }
                });

                $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                if (pixel.platform === 'facebook') {
                    if (pixel.is_api) {
                        $("#modal-edit-pixel #div-facebook-token-api").show();
                    } else {
                        $("#modal-edit-pixel #div-facebook-token-api").hide();
                    }

                    $("#modal-edit-pixel #api-facebook").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", '52342343245553');
                } else if (pixel.platform === 'google_adwords') {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('AW-').show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                } else if (pixel.platform === 'google_analytics_four') {
                    $("#modal-edit-pixel #google-analytics-info").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                } else if (pixel.platform === 'google_analytics') {
                    $("#modal-edit-pixel #google-analytics-info").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", 'UA-8984567741-3');
                } else if (pixel.platform === 'taboola') {
                    $("#modal-edit-pixel .purchase-event-name-div").show();
                } else if (pixel.platform === 'outbrain') {
                    $("#modal-edit-pixel .purchase-event-name-div").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                } else {
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", 'Código');
                }
            }
        });
    });

    function renderEditPixel(pixel) {
        $('#modal-edit-pixel .pixel-id').val(pixel.id_code);
        $('#modal-edit-pixel .pixel-description').val(pixel.name);
        $('#modal-edit-pixel .pixel-code').val(pixel.code);
        $('#modal-edit-pixel #percentage-value').val(pixel.value_percentage_purchase_boleto);

        if (pixel.platform == 'facebook') {
            $("#modal-edit-pixel #facebook-token-api").val('');
            if (pixel.is_api) {
                $('#modal-edit-pixel #default-api-facebook').prop("checked", 'checked');
                $('#modal-edit-pixel #api-facebook').prop("checked", 'checked');
                $("#modal-edit-pixel #facebook-token-api").val(pixel.facebook_token);
                $("#modal-edit-pixel #div-facebook-token-api").show();
            } else {
                $('#modal-edit-pixel #api-facebook').prop("checked", false);
                $('#modal-edit-pixel #default-api-facebook').prop("checked", true);
                $("#modal-edit-pixel #div-facebook-token-api").hide();
            }
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 0).change();
        } else if (pixel.platform == 'google_adwords') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 1).change();
        } else if (pixel.platform == 'google_analytics') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 2).change();
        } else if (pixel.platform == 'google_analytics_four') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 3).change();
        } else if (pixel.platform == 'taboola') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 4).change();
            $("#modal-edit-pixel .purchase-event-name").val(pixel.purchase_event_name);
        } else if (pixel.platform == 'outbrain') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 5).change();
            $("#modal-edit-pixel .purchase-event-name").val(pixel.purchase_event_name);
        }

        if (pixel.status == '1') { //Ativo
            $('#modal-edit-pixel .pixel-status').prop("selectedIndex", 0).change();
        } else {//Desativado
            $('#modal-edit-pixel .pixel-status').prop("selectedIndex", 1).change();
        }

        if (pixel.checkout == '1') {
            $('#modal-edit-pixel .pixel-checkout').val(1).prop('checked', true);
        } else {
            $('#modal-edit-pixel .pixel-checkout').val(0).prop('checked', false);
        }
        if (pixel.purchase_card == '1') {
            $('#modal-edit-pixel .pixel-purchase-card').val(1).prop('checked', true);
        } else {
            $('#modal-edit-pixel .pixel-purchase-card').val(0).prop('checked', false);
        }
        if (pixel.purchase_boleto == '1') {
            $('#modal-edit-pixel .pixel-purchase-boleto').val(1).prop('checked', true);
        } else {
            $('#modal-edit-pixel .pixel-purchase-boleto').val(0).prop('checked', false);
        }

        $("#modal-edit-pixel .apply_plans").html('');
        let applyOnPlans = [];
        for (let plan of pixel.apply_on_plans) {
            applyOnPlans.push(plan.id);
            $("#modal-edit-pixel .apply_plans").append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
        }
        $("#modal-edit-pixel .apply_plans").val(applyOnPlans);

        $('#modal-edit-pixel').modal('show');


    }

    //carregar modal delecao
    $(document).on('click', '.delete-pixel', function (event) {
        const pixel = $(this).attr('pixel');
        $("#modal-delete-pixel .btn-delete").attr("pixel", pixel);
        $("#modal-delete-pixel").modal('show');
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

        if (formData.value_percentage_purchase_boleto.length < 1) {
            alertCustom('error', 'O campo % Valor Boleto é obrigatório')
            return false;
        }
        console.log();
        if (isNaN(parseInt(formData.value_percentage_purchase_boleto))) {
            alertCustom('error', 'O campo % Valor Boleto permite apenas numeros');
            return false;
        }

        if (formData.value_percentage_purchase_boleto > 100 || formData.value_percentage_purchase_boleto < 10) {
            alertCustom('error', 'O valores permitidos para o campo % Valor Boleto deve ser entre 10 e 100')
            return false;
        }

        if (formData.platform == 'facebook' && formData.is_api == 'api' && formData.facebook_token_api.length < 1) {
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

    //criar novo pixel
    $("#modal-create-pixel .btn-save").on('click', function () {
        const formData = new FormData(document.querySelector('#modal-create-pixel  #form-register-pixel'));

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

        formData.set('value_percentage_purchase_boleto', parseInt(formData.get('value_percentage_purchase_boleto')));
        formData.append('checkout', $("#modal-create-pixel .pixel-checkout").val());
        formData.append('purchase_card', $("#modal-create-pixel .pixel-purchase-card").val());
        formData.append('purchase_boleto', $("#modal-create-pixel .pixel-purchase-boleto").val());
        formData.append('purchase_event_name', $("#modal-create-pixel #purchase-event-name").val());

        loadingOnScreen();
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
            error: function (_error) {
                function error(_x) {
                    return _error.apply(this, arguments);
                }

                error.toString = function () {
                    return _error.toString();
                };

                return error;
            }(function (response) {
                loadingOnScreenRemove();
                $("#modal_add_produto").hide();
                $(".loading").css("visibility", "hidden");
                errorAjaxResponse(response);

            }), success: function success(response) {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                if (response.success) {
                    alertCustom("success", response.message);
                    atualizarPixel();
                    clearFields();
                } else {
                    alertCustom("error", response.message);
                }
            }
        });
    });

    //atualizar pixel
    $(document).on('click', '#modal-edit-pixel .btn-update', function () {
        if (!validateDataPixelForm({
            'name': $("#modal-edit-pixel .pixel-description").val(),
            'platform': $("#modal-edit-pixel .pixel-platform").val(),
            'is_api': $("#modal-edit-pixel input[type=radio]:checked").val(),
            'code': $("#modal-edit-pixel .pixel-code").val(),
            'value_percentage_purchase_boleto': $("#modal-edit-pixel #percentage-value").val(),
            'facebook_token_api': $("#modal-edit-pixel #facebook-token-api").val(),
            'purchase_event_name': $("#modal-edit-pixel .purchase-event-name").val(),
            'plans_apply': $("#modal-edit-pixel .apply_plans").val()
        })) {
            return false;
        }

        loadingOnScreen();
        const pixelId = $('#modal-edit-pixel .pixel-id').val();

        $.ajax({
            method: "PUT",
            url: `/api/project/${projectId}/pixels/${pixelId}`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                name: $("#modal-edit-pixel .pixel-description").val(),
                code: $("#modal-edit-pixel .pixel-code").val(),
                platform: $("#modal-edit-pixel .pixel-platform").val(),
                status: $("#modal-edit-pixel .pixel-status").val(),
                checkout: $("#modal-edit-pixel .pixel-checkout").val(),
                purchase_card: $("#modal-edit-pixel .pixel-purchase-card").val(),
                purchase_boleto: $("#modal-edit-pixel .pixel-purchase-boleto").val(),
                edit_pixel_plans: $("#modal-edit-pixel .apply_plans").val(),
                purchase_event_name: $("#modal-edit-pixel .purchase-event-name").val(),
                is_api: $("#modal-edit-pixel input[type=radio]:checked").val(),
                facebook_token_api: $("#modal-edit-pixel #facebook-token-api").val(),
                value_percentage_purchase_boleto: $("#modal-edit-pixel #percentage-value").val(),
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success() {
                loadingOnScreenRemove();
                $("#modal-edit-pixel").modal('hide');
                alertCustom("success", "Pixel atualizado com sucesso");
                atualizarPixel(currentPage);
            }
        });
    });

    // deletar pixel
    $(document).on('click', '#modal-delete-pixel .btn-delete', function () {
        loadingOnScreen();
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
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            }),
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Pixel Removido com sucesso");
                atualizarPixel(currentPage);
            }

        });
    });

    function atualizarPixel() {
        let link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        currentPage = link;

        if (link == null) {
            link = '/api/project/' + projectId + '/pixels';
        } else {
            link = '/api/project/' + projectId + '/pixels' + link;
        }

        loadOnTable('#data-table-pixel', '#table-pixel');

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
                $("#data-table-pixel").html('');
                if (response.data == '') {
                    $("#data-table-pixel").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                    $('#table-pixel').addClass('table-striped');

                } else {
                    $('#count-pixels').html(response.meta.total)
                    $.each(response.data, function (index, value) {
                        const data = `<tr>
                                    <td>${value.name}</td>
                                    <td>${value.code}</td>
                                    <td>${formatPlatform[value.platform_enum]}</td>
                                    <td><span class="badge badge-${statusPixel[value.status]}">${value.status_translated}</span></td>
                                    <td style='text-align:center'>
                                        <a role='button' title='Visualizar' class='mg-responsive details-pixel pointer' pixel='${value.id}' data-target='#modal-details-pixel' data-toggle='modal'><span class="o-eye-1"></span></a>
                                        <a role='button' title='Editar' class='mg-responsive edit-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><span class="o-edit-1"></span></a>
                                        <a role='button' title='Excluir' class='mg-responsive delete-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><span class='o-bin-1'></span></a>
                                    </td>
                                </tr>`;
                        $("#data-table-pixel").append(data);
                        $('#table-pixel').addClass('table-striped');
                    });

                }
                pagination(response, 'pixels', atualizarPixel);

                $("#select-platform").change(function () {
                    const value = $(this).val();
                    $("#input-code-pixel").html('').hide();

                    $("#google-analytics-info, #api-facebook, .purchase-event-name-div").hide();

                    if (value === 'facebook') {
                        $("#api-facebook").show();
                        $("#code-pixel").attr("placeholder", '52342343245553');
                    } else if (value === 'google_adwords') {
                        $("#input-code-pixel").html('AW-').show();
                        $("#code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                    } else if (value === 'google_analytics') {
                        $("#code-pixel").attr("placeholder", 'UA-8984567741-3');
                    } else if (value === 'google_analytics_four') {
                        $("#code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                    } else if (value === 'taboola') {
                        $(".purchase-event-name-div").show();
                        $("#code-pixel").attr("placeholder", '1010100');
                    } else if (value === 'outbrain') {
                        $(".purchase-event-name-div").show();
                        $("#code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                    } else {
                        $("#code-pixel").attr("placeholder", 'Código');
                    }
                });
            }
        });
    }

    function clearFields() {
        $('.pixel-description').val('');
        $('.pixel-code').val('');
    }

    $('#add_pixel_plans').select2(Object.assign(selectPlan(), {dropdownParent: $('#modal-create-pixel')}));
    $('#edit_pixel_plans').select2(Object.assign(selectPlan(), {dropdownParent: $('#modal-edit-pixel')}));

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

    $("#add_pixel_plans, #edit_pixel_plans").on('select2:select', function () {
        const planSelect = $(this);
        if ((planSelect.val().length > 1 && planSelect.val().includes('all')) || (planSelect.val().includes('all') && planSelect.val() != 'all')) {
            planSelect.val('all').trigger("change");
        }
    });
});
