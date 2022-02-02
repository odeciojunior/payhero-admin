$(() => {

    let projectId = $(window.location.pathname.split('/')).get(-1);

    function index() {

        loadOnTable('#table-order-bump tbody', '#table-order-bump');

        let link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        link = '/api/orderbump' + (link || '');

        $('#tab-order-bump-panel').find('.no-gutters').css('display', 'none');
        $('#table-order-bump').find('thead').css('display', 'none');

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
                    $('#tab-order-bump-panel').find('.no-gutters').css('display', 'flex');
                    $('#table-order-bump').find('thead').css('display', 'contents');

                    for (let rule of rules) {
                        let row = `<tr>
                                       <td>${rule.description}</td>
                                       <td class="text-center">${rule.active_flag ? `<span class="badge badge-success">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`}</td>
                                       <td>
                                            <div class='d-flex justify-content-end align-items-center'>
                                                <a class="pointer mg-responsive show-order-bump" data-id="${rule.id}" title="Visualizar"><span class="o-eye-1"></span></a>
                                                <a class="pointer mg-responsive edit-order-bump" data-id="${rule.id}" title="Editar" ><span class="o-edit-1"></span></a>
                                                <a class="pointer mg-responsive destroy-order-bump" data-id="${rule.id}" title="Excluir" data-toggle="modal" data-target="#modal-delete-order-bump"><span class="o-bin-1"></span></a>
                                            </div>
                                       </td>
                                   </tr>`;
                        table.append(row);
                    }

                    pagination(resp, 'order-bump', index);
                } else {
                    table.html(`
                        <tr class="text-center">
                            <td colspan="3">
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/modules/global/img/empty-state-table.png' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum order bump configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro order bump para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-order-bump' data-toggle="modal" data-target="#modal-store-order-bump">Adicionar order bump</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
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
        event.preventDefault();

        let id = $(this).data('id');

        $('#btn-delete-orderbump').unbind('click');
        $(document).on('click', '#btn-delete-orderbump', function () {
            $.ajax({
                method: 'DELETE',
                url: '/api/orderbump/' + id,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    index();

                    alertCustom('success', response.message);
                }
            })
        });
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
