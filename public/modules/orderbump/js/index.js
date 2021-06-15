$(() => {

    let projectId = $(window.location.pathname.split('/')).get(-1);

    function index() {

        loadOnTable('#table-order-bump tbody', '#table-order-bump');

        let link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        link = '/api/orderbump' + (link || '');

        $.ajax({
            method: 'GET',
            url: link,
            data: {
                project_id: projectId
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                let rules = resp.data;
                let table = $('#table-order-bump tbody');
                if (rules.length) {
                    table.html('');
                    for (let rule of rules) {
                        let row = `<tr>
                                       <td>${rule.description}</td>
                                       <td>${rule.active_flag ? `<span class="badge badge-success">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`}</td>
                                       <td>
                                           <a class="pointer mg-responsive show-order-bump" data-id="${rule.id}" title="Visualizar"><i class="o-eye-1"></i></a>
                                           <a class="pointer mg-responsive edit-order-bump" data-id="${rule.id}" title="Editar" ><i class="o-edit-1"></i></a>
                                           <a class="pointer mg-responsive destroy-order-bump" data-id="${rule.id}" title="Excluir"><i class="o-bin-1"></i></a>
                                       </td>
                                   </tr>`;
                        table.append(row);
                    }
                    pagination(resp, 'order-bump', index);
                } else {
                    table.html('<tr class="text-center"><td colspan="3">Nenhum order bump configurado</td></tr>');
                    table.parent().addClass('table-striped');
                }
            }
        });
    }

    $('#tab_order_bump').on('click', function (){
        index();
    });

    $(document).on('click', '.show-order-bump', function () {
        let id = $(this).data('id');
        $.ajax({
            url: '/api/orderbump/' + id,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                let rule = resp.data;
                let applyOnPlans = rule.apply_on_plans
                    .map(plan => plan.name + (plan.description ? ` - ${plan.description}` : ''))
                    .join(' / ');
                let offerPlans = rule.offer_plans
                    .map(plan => plan.name + (plan.description ? ` - ${plan.description}` : ''))
                    .join(' / ');
                $('#order-bump-show-table .order-bump-description').html(rule.description);
                $('#order-bump-show-table .order-bump-discount').html(rule.discount + '%');
                $('#order-bump-show-table .order-bump-apply-plans').html(applyOnPlans);
                $('#order-bump-show-table .order-bump-offer-plans').html(offerPlans);
                $('#order-bump-show-table .order-bump-status').html(rule.active_flag ? `<span class="badge badge-success">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`);
                $('#modal-show-order-bump').modal('show');
            }
        })
    });

    $(document).on('click', '.edit-order-bump', function () {
        let id = $(this).data('id');
        $.ajax({
            url: '/api/orderbump/' + id,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                let rule = resp.data;
                let applyOnPlansInput = $('#update-apply-on-plans-order-bump');
                let offerPlansInput = $('#update-offer-plans-order-bump');

                $('#update-description-order-bump').val(rule.description);
                $('#update-discount-order-bump').val(rule.discount);

                let applyOnPlans = [];
                applyOnPlansInput.html('');
                for (let plan of rule.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    applyOnPlansInput.append(`<option value="${plan.id}">${plan.name + (plan.description ? ` - ${plan.description}` : '')}</option>`);
                }
                applyOnPlansInput.val(applyOnPlans);

                offerPlansInput.html('');
                let offerPlans = [];
                for (let plan of rule.offer_plans) {
                    offerPlans.push(plan.id);
                    offerPlansInput.append(`<option value="${plan.id}">${plan.name + (plan.description ? ` - ${plan.description}` : '')}</option>`);
                }
                offerPlansInput.val(offerPlans);

                if (rule.active_flag) {
                    $('#update-active-flag-order-bump').val(1).prop('checked', true);
                } else {
                    $('#update-active-flag-order-bump').val(0).prop('checked', false);
                }

                $('#btn-update-order-bump').data('id', id);
                $('#modal-update-order-bump').modal('show');
            }
        })
    });

    $('#btn-store-order-bump').on('click', function () {
        let formData = new FormData(document.querySelector('#form-store-order-bump'));
        formData.append('project_id', projectId);
        $.ajax({
            method: 'POST',
            url: '/api/orderbump',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                alertCustom('success', resp.message);
                $('#modal-store-order-bump').modal('hide');
                $('#store-description-order-bump, #store-discount-order-bump').val('');
                $("#store-apply-on-plans-order-bump, #store-offer-plans-order-bump")
                    .val(null)
                    .trigger('change');
                index();
            }
        })
    });

    $('#btn-update-order-bump').on('click', function () {
        let id = $(this).data('id');
        let formData = new FormData(document.querySelector('#form-update-order-bump'));
        formData.append('project_id', projectId);
        $.ajax({
            method: 'POST',
            url: '/api/orderbump/' + id,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                alertCustom('success', resp.message);
                $('#modal-update-order-bump').modal('hide');
                index();
            }
        })
    });
    // load delete modal
    $(document).on('click', '.destroy-order-bump', function (event) {
        let id = $(this).data('id');
        $('#modal-delete-order-bump .btn-delete').attr('order-bump-id', id);
        $("#modal-delete-order-bump").modal('show');
    });
    $(document).on('click', '#modal-delete-order-bump .btn-delete', function () {
        // let id = $(this).data('id');
        let id = $('#modal-delete-order-bump .btn-delete').attr('order-bump-id');
        $.ajax({
            method: 'POST',
            url: '/api/orderbump/' + id,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                _method: 'DELETE'
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                alertCustom('success', resp.message);
                index();
            }
        })
    });

    $('#store-apply-on-plans-order-bump, #update-apply-on-plans-order-bump').on('select2:select', function () {
        let selectPlan = $(this);
        if ((selectPlan.val().length > 1 && selectPlan.val().includes('all'))) {
            selectPlan.val('all').trigger("change");
        }
    });

    //Search plan
    let select2Configs = {
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
                let elemId = this.$element.attr('id');
                if (['store-apply-on-plans-order-bump', 'update-apply-on-plans-order-bump'].includes(elemId) && res.meta.current_page === 1) {
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
    $('#store-apply-on-plans-order-bump, #store-offer-plans-order-bump').select2({dropdownParent: $('#modal-store-order-bump'), ...select2Configs});
    $('#update-apply-on-plans-order-bump, #update-offer-plans-order-bump').select2({dropdownParent: $('#modal-update-order-bump'), ...select2Configs});
});
