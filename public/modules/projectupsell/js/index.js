$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    loadUpsell();
    loadPlans();
    $('#tab_upsell').on('click', function () {
        loadUpsell();
    })
    function loadUpsell() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/projectupsellrule';
        } else {
            link = '/api/projectupsellrule' + link;
        }

        loadOnTable('#data-table-upsell', '#table-upsell');
        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            data: {project_id: projectId},
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
            },
            success: function success(response) {
                $('#data-table-upsell').html('');
                let projectUpsell = response.data;
                let upsellLength = response.data.length;
                if (projectUpsell != '') {
                    let dados = '';
                    for (let upsell of projectUpsell) {
                        dados = `
                        <tr>
                            <td>${upsell.description}</td>
                            <td>${upsell.active_flag ? `<span class="badge badge-success text-left">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`}</td>
                            <td style='text-align:center'>
                                <a role='button' title='Visualizar' class='mg-responsive details-upsell pointer' data-upsell="${upsell.id}" data-target='#modal-detail-upsell' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></a>
                                <a role='button' title='Editar' class='pointer edit-upsell mg-responsive' data-upsell="${upsell.id}" data-toggle="modal" data-target="#modal_add_upsell"><i class='material-icons gradient'> edit </i></a>
                                <a role='button' title='Excluir' class='pointer delete-upsell mg-responsive' data-upsell="${upsell.id}" data-toggle="modal" data-target="#modal-delete-upsell"><i class='material-icons gradient'> delete_outline </i></a>
                            </td>
                        </tr>
                        `;
                        $('#data-table-upsell').append(dados);
                    }
                } else {
                    $('#data-table-upsell').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhum upsell encontrado</td></tr>");
                }
                if (upsellLength > 0) {
                    $('.div-config').show();
                } else {
                    $('.div-config').hide();
                }
                $('#table-upsell').addClass('table-striped');
                pagination(response, 'upsell', loadUpsell);

            }
        });
    }

    $("#add-upsell").on('click', function () {
        $('#modal_add_upsell .modal-title').html("Novo upsell");
        $(".bt-upsell-save").show();
        $(".bt-upsell-update").hide();
        $('#form_add_upsell').show();
        $('#form_edit_upsell').hide()
        $('#add_description_upsell').val('');
        $('#add_discount_upsell').val('');
    });

    $(document).on('click', '.edit-upsell', function (event) {
        event.preventDefault();
        let upsellId = $(this).data('upsell');
        $('#modal_add_upsell .modal-title').html("Editar upsell");
        $(".bt-upsell-save").hide();
        $(".bt-upsell-update").show();
        $("#form_add_upsell").hide();
        $("#form_edit_upsell").show();
        $('#edit_description_upsell').val('');
        $('#edit_discount_upsell').val('');
        $('#form_edit_upsell .upsell-id').val(upsellId);
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/projectupsellrule/" + upsellId + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {

            }, success: function (response) {
                let upsell = response.data;
                $('#edit_description_upsell').val(`${upsell.description}`);
                $('#edit_discount_upsell').val(`${upsell.discount}`);

                if (upsell.active_flag) {
                    $('#edit_active_flag').val(1).prop('checked', true);
                } else {
                    $('#edit_active_flag').val(0).prop('checked', false);
                }
                // Seleciona a opção do select de acordo com o que vem do banco
                let equalApplyArray = [];
                let equalOfferArray = [];
                let differentApplyArray = [];
                let differentOfferArray = [];
                let selectApplyIdsArray = [];
                let selectOfferIdsArray = [];
                $("#edit_apply_on_plans").find('option').each(function () {
                    selectApplyIdsArray.push($(this).val());
                    for (let plan of upsell.apply_on_plans) {
                        if (plan.id == $(this).val()) {
                            equalApplyArray.push(plan.id);
                            $("#edit_apply_on_plans").val(equalApplyArray);
                            $("#edit_apply_on_plans").trigger('change');
                        } else {
                            differentApplyArray[plan.id] = plan.name;
                        }
                    }
                });
                if (equalApplyArray.length != upsell.apply_on_plans.length) {
                    let idPlanArray = [];
                    for (let key in differentApplyArray) {
                        if (!selectApplyIdsArray.includes(key)) {
                            $('#edit_apply_on_plans').append(`<option value="${key}">${differentApplyArray[key]}</option>`);
                        }
                        idPlanArray.push(key);
                    }
                    $("#edit_apply_on_plans").val(idPlanArray);
                }
                $("#edit_offer_on_plans").find('option').each(function () {
                    selectOfferIdsArray.push($(this).val());
                    for (let plan of upsell.offer_on_plans) {
                        if (plan.id == $(this).val()) {
                            equalOfferArray.push(plan.id);
                            $("#edit_offer_on_plans").val(equalOfferArray);
                            $("#edit_offer_on_plans").trigger('change');
                        } else {
                            differentOfferArray[plan.id] = plan.name;
                        }
                    }
                });
                if (equalOfferArray.length != upsell.offer_on_plans.length) {
                    let idPlanArray = [];
                    for (let key in differentOfferArray) {
                        if (!selectOfferIdsArray.includes(key)) {
                            $('#edit_offer_on_plans').append(`<option value="${key}">${differentOfferArray[key]}</option>`);
                        }
                        idPlanArray.push(key);
                    }
                    $("#edit_offer_on_plans").val(idPlanArray);
                }
                if ($("#edit_apply_on_plans").val().includes('all')) {
                    $("#edit_apply_on_plans").find('option').each(function () {
                        if ($(this).val() != 'all') {
                            $(this).prop('disabled', true);
                        }
                    });
                    $("#edit_apply_on_plans").select2({
                        placeholder: 'Nome do plano',
                        multiple: true,
                        dropdownParent: $('#modal_add_upsell'),
                    });
                }
                loadingOnScreenRemove();
                // END
            }
        });
    });

    $(document).on('click', '.bt-upsell-save', function () {
        loadingOnScreen();
        var form_data = new FormData(document.getElementById('form_add_upsell'));
        form_data.append('project_id', projectId);
        $.ajax({
            method: "POST",
            url: "/api/projectupsellrule",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#modal_add_upsell').modal('hide');
                loadingOnScreenRemove();
                loadUpsell();
                alertCustom('success', response.message);
                $("#add_apply_on_plans").val(null).trigger('change');
                $("#add_offer_on_plans").val(null).trigger('change');
            }
        });
    });

    $(document).on('click', '.delete-upsell', function (event) {
        event.preventDefault();
        let upsellId = $(this).data('upsell');
        $('.btn-delete-upsell').on('click', function () {
            loadingOnScreen();
            $.ajax({
                method: "DELETE",
                url: "/api/projectupsellrule/" + upsellId,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    loadingOnScreenRemove()
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    loadUpsell();
                    alertCustom('success', response.message);
                }
            });
        });
    });

    $(document).on('click', '.bt-upsell-update', function (event) {
        event.preventDefault();
        loadingOnScreen();
        var form_data = new FormData(document.getElementById('form_edit_upsell'));
        let upsellId = $('#form_edit_upsell .upsell-id').val();
        $.ajax({
            method: "POST",
            url: "/api/projectupsellrule/" + upsellId,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#modal_add_upsell').modal('hide');
                loadingOnScreenRemove();
                loadUpsell();
                alertCustom('success', response.message);
            }
        });
    });
    $(document).on('click', '.details-upsell', function (event) {
        event.preventDefault();
        let upsellId = $(this).data('upsell');
        $.ajax({
            method: "GET",
            url: "/api/projectupsellrule/" + upsellId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            }, success: function success(response) {
                let upsell = response.data;
                $('.upsell-description').html('');
                $('.upsell-discount').html('');
                $('.upsell-status').html('');
                $('.upsell-apply-plans').html('');
                $('.upsell-offer-plans').html('');
                $('.upsell-description').html(`${upsell.description}`);
                $('.upsell-discount').html(`${upsell.discount != 0 ? `${upsell.discount}%` : `Valor sem desconto`}`);

                $('.upsell-status').html(`${upsell.active_flag ? `<span class="badge badge-success text-left">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`}`);
                for (let applyPlan of upsell.apply_on_plans) {
                    $('.upsell-apply-plans').append(`<span>${applyPlan.name}</span><br>`);
                }
                for (let offerPlan of upsell.offer_on_plans) {
                    $('.upsell-offer-plans').append(`<span>${offerPlan.name}</span><br>`);
                }
            }
        });
    });

    $('#add_apply_on_plans, #edit_apply_on_plans').select2({
        placeholder: 'Nome do plano',
        multiple: true,
        dropdownParent: $('#modal_add_upsell'),
        language: {
            noResults: function () {
                return 'Nenhum plano encontrado';
            },
            searching: function () {
                return 'Procurando...';
            }
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'plan',
                    search: params.term,
                    project_id: projectId,
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
                let allObject = {
                    id: 'all',
                    name: 'Todos',
                };
                res.data.unshift(allObject);
                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name};
                    })
                };
            },
        }
    });

    //Search plan
    $('#add_offer_on_plans, #edit_offer_on_plans').select2({
        placeholder: 'Nome do plano',
        multiple: true,
        dropdownParent: $('#modal_add_upsell'),
        language: {
            noResults: function () {
                return 'Nenhum plano encontrado';
            },
            searching: function () {
                return 'Procurando...';
            }
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'plan',
                    search: params.term,
                    project_id: projectId,
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
                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name};
                    })
                };
            },
        }
    });

    $('#add_apply_on_plans').on('select2:select', function () {
        let selectPlan = $(this);
        if (selectPlan.val().length > 1 && selectPlan.val().includes('all')) {
            selectPlan.val(null).trigger("change");
            selectPlan.val('all').trigger("change");
        } else {
            if (selectPlan.val() == 'all') {
                selectPlan.find('option').each(function () {
                    if ($(this).val() != 'all') {
                        $(this).prop('disabled', true);
                    }
                });
                //Search plan
                selectPlan.select2({
                    placeholder: 'Nome do plano',
                    multiple: true,
                    dropdownParent: $('#modal_add_upsell'),
                });
            }
        }
    });
    $('#add_apply_on_plans').on('select2:unselecting', function () {
        let selectPlan = $(this);
        if (selectPlan.val() == 'all') {
            selectPlan.find('option').each(function () {
                if ($(this).is(':disabled')) {
                    $(this).prop('disabled', false);
                }
            });
        }
    });

    $('#edit_apply_on_plans').on('select2:select', function () {
        let selectPlan = $(this);
        if (selectPlan.val().length > 1 && selectPlan.val().includes('all')) {
            selectPlan.val(null).trigger("change");
            selectPlan.val('all').trigger("change");
        } else {
            if (selectPlan.val() == 'all') {
                selectPlan.find('option').each(function () {
                    if ($(this).val() != 'all') {
                        $(this).prop('disabled', true);
                    }
                });
                //Search plan
                selectPlan.select2({
                    placeholder: 'Nome do plano',
                    multiple: true,
                    dropdownParent: $('#modal_add_upsell'),
                });
            }
        }
    });

    $('#edit_apply_on_plans').on('select2:unselecting', function () {
        let selectPlan = $(this);
        if (selectPlan.val() == 'all') {
            selectPlan.find('option').each(function () {
                if ($(this).is(':disabled')) {
                    $(this).prop('disabled', false);
                }
            });
        }
    });
    CKEDITOR.replace('description_config', {
        language: 'br',
        uiColor: '#F1F4F5',
        height: 250,
        toolbarGroups: [
            {name: 'basicstyles', groups: ['basicstyles']},
            {name: 'paragraph', groups: ['list', 'blocks']},
            {name: 'links', groups: ['links']},
            {name: 'styles', groups: ['styles']},
        ],
        removeButtons: 'Anchor,Superscript,Subscript',
    });

    $('#countdown_flag').on('change', function () {
        let switchFlag = $(this);
        if (switchFlag.is(':checked')) {
            $('.div-countdown-time').slideDown('fast');
        } else {
            $('.div-countdown-time').slideUp('fast');
        }
    });
    $(document).on('click', '#config-upsell', function (event) {
        event.preventDefault();
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/projectupsellconfig/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            }, success: function success(response) {
                loadingOnScreenRemove();
                let upsellConfig = response.data;
                $('#header_config').val(`${upsellConfig.header}`);
                $('#title_config').val(`${upsellConfig.title}`);
                CKEDITOR.instances.description_config.setData(`${upsellConfig.description}`);
                $('#countdown_time').val(`${upsellConfig.countdown_time}`);

                if (upsellConfig.countdown_flag) {
                    $('#countdown_flag').prop('checked', true);
                    $('.div-countdown-time').show();
                } else {
                    $('#countdown_flag').prop('checked', false);
                    $('.div-countdown-time').hide();
                }
            }
        });
    });
    $(document).on('click', '.bt-upsell-config-update', function (event) {
        event.preventDefault();
        if ($('#countdown_flag').is(':checked') && $('#countdown_time').val() == '') {
            alertCustom('error', 'Preencha o campo Contagem');
            return false;
        }
        loadingOnScreen();
        var form_data = new FormData(document.getElementById('form_config_upsell'));
        let description = CKEDITOR.instances.description_config.getData();
        form_data.set('description', description);

        $.ajax({
            method: "POST",
            url: "/api/projectupsellconfig/" + projectId,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#modal_config_upsell').modal('hide');
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });

    function loadPlans() {
        $('#add_apply_on_plans').html('');
        $('#add_offer_on_plans').html('');
        $('#edit_apply_on_plans').html('');
        $('#edit_offer_on_plans').html('');
        $.ajax({
            method: "GET",
            url: "/api/plans/user-plans",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {project_id: projectId},
            error: function error(response) {
            },
            success: function success(response) {
                for (let plan of response.data) {
                    $('#add_apply_on_plans').append(`<option value="${plan.id}">${plan.name}</option>`);
                    $('#add_offer_on_plans').append(`<option value="${plan.id}">${plan.name}</option>`);

                    $('#edit_apply_on_plans').append(`<option value="${plan.id}">${plan.name}</option>`);
                    $('#edit_offer_on_plans').append(`<option value="${plan.id}">${plan.name}</option>`);
                }
            }
        });
    }
});
