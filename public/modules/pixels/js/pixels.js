let statusPixel = {
    1: "success",
    0: "danger",
};

let formatPlatform = {
    1: 'Facebook',
    2: 'Google Adwords',
    3: 'Google Analytics',
    4: 'Google Analytics 4.0',
    5: 'Taboola',
    6: 'Outbrain'
}



$(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);


    //comportamentos da tela
    $('.tab_pixels').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarPixel();
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
        let value = $("#modal-create-pixel #select-platform option:selected").val();

        $("#meta-tag-facebook").hide();
        // $("#modal-create-pixel .facebook-meta-tag-tooltip .tooltip-inner").css('background', '#fff');


        if (value == 'facebook') {
            $("#meta-tag-facebook").show();
        }
    });

    $("#select-platform").change(function () {
        let value = $(this).val();
        $("#outbrain-info").hide();
        $("#google-analytics-info, #meta-tag-facebook").hide();

        if (value === 'facebook') {
            $("#input-code-pixel").html('').hide();
            $("#meta-tag-facebook").show();
            $("#code-pixel").attr("placeholder", '52342343245553');
        } else if (value === 'google_adwords') {
            $("#input-code-pixel").html('AW-').show();
            $("#code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
        } else if (value === 'google_analytics') {
            $("#input-code-pixel").html('').hide();
            $("#google-analytics-info").show();
            $("#code-pixel").attr("placeholder", 'UA-8984567741-3');
        } else if (value === 'google_analytics_four') {
            $("#input-code-pixel").html('').hide();
            $("#google-analytics-info").show();
            $("#code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
        } else if (value === 'outbrain') {
            $("#input-code-pixel").html('').hide();
            $("#outbrain-info").show();
            $("#code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
        } else {
            $("#input-code-pixel").html('').hide();
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

    // carregar modal de edicao
    $(document).on('click', '.edit-pixel', function () {
        let pixel = $(this).attr('pixel');
        $("#edit_pixel_plans").val(null).trigger('change');

        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/pixels/" + pixel + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            }, success: function success(response) {
                let pixel = response.data;
                renderEditPixel(pixel);

                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });


                // troca o placeholder dos inputs
                $("#modal-edit-pixel #select-platform").change(function () {
                    let value = $(this).val();
                    $("#modal-edit-pixel #google-analytics-info,#modal-edit-pixel #meta-tag-facebook").hide();
                    $("#modal-edit-pixel #outbrain-info-edit").hide();

                    if (value === 'facebook') {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                        $("#modal-edit-pixel #meta-tag-facebook").show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", '52342343245553');
                    } else if (value === 'google_adwords') {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('AW-').show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                    } else if (value === 'google_analytics') {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                        $("#modal-edit-pixel #google-analytics-info").show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", 'UA-8984567741-3');
                    } else if (value === 'google_analytics_four') {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                        $("#modal-edit-pixel #google-analytics-info").show();
                        $("#code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                    } else if (value === 'outbrain') {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                        $("#modal-edit-pixel #outbrain-info-edit").show();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                    } else {
                        $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                        $("#modal-edit-pixel #code-pixel").attr("placeholder", 'Código');
                    }
                });

                if (pixel.platform === 'facebook') {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                    $("#modal-edit-pixel #meta-tag-facebook").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", '52342343245553');
                } else if (pixel.platform === 'google_adwords') {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('AW-').show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                } else if (pixel.platform === 'google_analytics_four') {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                    $("#modal-edit-pixel #google-analytics-info").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                } else if (pixel.platform === 'google_analytics') {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                    $("#modal-edit-pixel #google-analytics-info").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", 'UA-8984567741-3');
                } else if (pixel.platform === 'outbrain') {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                    $("#modal-edit-pixel #outbrain-info-edit").show();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                } else {
                    $("#modal-edit-pixel #input-code-pixel-edit").html('').hide();
                    $("#modal-edit-pixel #code-pixel").attr("placeholder", 'Código');
                }
            }
        });
    });

    function renderEditPixel(pixel) {
        $('#modal-edit-pixel .pixel-id').val(pixel.id_code);
        $('#modal-edit-pixel .pixel-description').val(pixel.name);
        $('#modal-edit-pixel .pixel-code').val(pixel.code);
        $("#modal-edit-pixel .pixel-code-meta-tag-facebook").val(pixel.code_meta_tag_facebook);

        if (pixel.platform == 'facebook') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 0).change();
        }
        if (pixel.platform == 'google_adwords') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 1).change();
        }
        if (pixel.platform == 'google_analytics') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 2).change();
        }
        if (pixel.platform == 'google_analytics_four') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 3).change();
        }
        if (pixel.platform == 'taboola') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 4).change();
        }
        if (pixel.platform == 'outbrain') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 5).change();
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
        let pixel = $(this).attr('pixel');
        $("#modal-delete-pixel .btn-delete").attr("pixel", pixel);
        $("#modal-delete-pixel").modal('show');
    });

    //criar novo pixel
    $("#modal-create-pixel .btn-save").on('click', function () {
        let formData = new FormData(document.querySelector('#modal-create-pixel  #form-register-pixel'));
        formData.append('checkout', $("#modal-create-pixel .pixel-checkout").val());
        formData.append('purchase_card', $("#modal-create-pixel .pixel-purchase-card").val());
        formData.append('purchase_boleto', $("#modal-create-pixel .pixel-purchase-boleto").val());

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

            }), success: function success() {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Pixel Adicionado!");
                atualizarPixel();
                clearFields();
            }
        });
    });

    //atualizar pixel
    $(document).on('click', '#modal-edit-pixel .btn-update', function () {
        loadingOnScreen();
        let pixel = $('#modal-edit-pixel .pixel-id').val();
        $.ajax({
            method: "PUT",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
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
                code_meta_tag_facebook: $("#modal-edit-pixel #code_meta_tag_facebook").val()
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Pixel atualizado com sucesso");
                atualizarPixel();
            }
        });
    });

    // deletar pixel
    $(document).on('click', '#modal-delete-pixel .btn-delete', function () {
        loadingOnScreen();
        let pixel = $(this).attr('pixel');
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
                atualizarPixel();
            }

        });
    });

    function atualizarPixel() {

        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

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
                        let data = `<tr>
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
                    let value = $(this).val();
                    $("#outbrain-info").hide();
                    $("#google-analytics-info, #meta-tag-facebook").hide();

                    if (value === 'facebook') {
                        $("#input-code-pixel").html('').hide();
                        $("#meta-tag-facebook").show();
                        $("#code-pixel").attr("placeholder", '52342343245553');
                    } else if (value === 'google_adwords') {
                        $("#input-code-pixel").html('AW-').show();
                        $("#code-pixel").attr("placeholder", '8981445741-4/AN7162ASNSG');
                    } else if (value === 'google_analytics') {
                        $("#input-code-pixel").html('').hide();
                        $("#google-analytics-info").show();
                        $("#code-pixel").attr("placeholder", 'UA-8984567741-3');
                    } else if (value === 'google_analytics_four') {
                        $("#input-code-pixel").html('').hide();
                        $("#google-analytics-info").show();
                        $("#code-pixel").attr("placeholder", 'G-KZSV4LMBAC');
                    } else if (value === 'outbrain') {
                        $("#input-code-pixel").html('').hide();
                        $("#outbrain-info").show();
                        $("#code-pixel").attr("placeholder", '00de2748d47f2asdl39877mash');
                    } else {
                        $("#input-code-pixel").html('').hide();
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
                        let allObject = {
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
        let selectPlan = $(this);
        if ((selectPlan.val().length > 1 && selectPlan.val().includes('all')) || (selectPlan.val().includes('all') && selectPlan.val() != 'all')) {
            selectPlan.val('all').trigger("change");
        }
    });
});
