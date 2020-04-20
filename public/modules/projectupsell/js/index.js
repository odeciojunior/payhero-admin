$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    let countdownInterval = null;
    loadUpsell();
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
                                <a role='button' title='Editar' class='pointer edit-upsell mg-responsive' data-upsell="${upsell.id}"><i class='material-icons gradient'> edit </i></a>
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
                $('#edit_apply_on_plans, #edit_offer_on_plans').html('');

                let applyOnPlans = [];
                for(let plan of upsell.apply_on_plans){
                    applyOnPlans.push(plan.id);
                    $('#edit_apply_on_plans').append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
                }
                $('#edit_apply_on_plans').val(applyOnPlans);

                let offerOnPlans = [];
                for(let plan of upsell.offer_on_plans){
                    offerOnPlans.push(plan.id);
                    $('#edit_offer_on_plans').append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
                }
                $('#edit_offer_on_plans').val(offerOnPlans);

                loadingOnScreenRemove();
                $('#modal_add_upsell').modal('show');
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
                $("#add_apply_on_plans, #add_offer_on_plans").val(null).trigger('change');
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

    //Search plan
    $('#add_apply_on_plans, #edit_apply_on_plans, #add_offer_on_plans, #edit_offer_on_plans').select2({
        placeholder: 'Nome do plano',
        multiple: true,
        dropdownParent: $('#modal_add_upsell'),
        language: {
            noResults: function () {
                return 'Nenhum plano encontrado';
            },
            searching: function () {
                return 'Procurando...';
            },
            loadingMore: function () {
                return 'Carregando mais planos…';
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
                if ((elemId === 'add_apply_on_plans' || elemId === 'edit_apply_on_plans') && res.meta.current_page === 1) {
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
    });

    $('#add_apply_on_plans, #edit_apply_on_plans').on('select2:select', function () {
        let selectPlan = $(this);
        if ((selectPlan.val().length > 1 && selectPlan.val().includes('all')) || (selectPlan.val().includes('all') && selectPlan.val() !== 'all')) {
            selectPlan.val('all').trigger("change");
        }
    });

    CKEDITOR.replace('description_config', {
        language: 'br',
        uiColor: '#F1F4F5',
        height: 70,
        toolbarGroups: [
            {name: 'basicstyles', groups: ['basicstyles']},
            {name: 'paragraph', groups: ['list', 'blocks']},
            {name: 'links', groups: ['links']},
            {name: 'styles', groups: ['styles']},
        ],
        removeButtons: 'Anchor,Superscript,Subscript',
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
                loadingOnScreenRemove();
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
                if (upsellConfig.has_upsell) {
                    $('.btn-view-config').show();
                }

                $('#modal_config_upsell').modal('show');
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
                // $('#modal_config_upsell').modal('hide');
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });
    $(document).on('click', '.btn-return-to-config', function (event) {
        event.preventDefault();
        $('#modal-view-upsell-config').modal('hide');
        $('#modal_config_upsell').modal('show');
    });
    $(document).on('click', '.btn-view-config', function (event) {
        event.preventDefault();
        $('#modal_config_upsell').modal('hide');
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/projectupsellconfig/previewupsell",
            dataType: "json",
            data: {
                project_id: projectId,
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            }, success: function success(response) {
                loadingOnScreenRemove();
                let upsell = response.data;

                $('#div-upsell-products').html('');

                $('#upsell-header').html(upsell.header);
                $('#upsell-title').html(upsell.title);
                $('#upsell-description').html(upsell.description);

                if (upsell.countdown_flag) {
                    $('#timer').show();
                    startCountdown(upsell.countdown_time);
                }else{
                    $('#timer').hide();
                }

                let data = "";

                for (let key in upsell.plans) {

                    let plan = upsell.plans[key];
                    data += `<div class="product-info">
                                <div class="d-flex flex-column">`;
                    for (let product of plan.products) {
                        let firstVariant = Object.keys(product)[0];
                        data += `<div class="product-row">
                                    <img src="${product[firstVariant].photo}" class="product-img">
                                    <div class="ml-4">
                                        <h3>${product[firstVariant].amount}x ${product[firstVariant].name}</h3>`;
                        if (Object.keys(product).length > 1) {
                            data += `<select class="product-variant">`;
                            for (let i in product) {
                                data += `<option value="${i}">${product[i].description}</option>`;
                            }
                            data += `</select>`;
                        } else {
                            data += `<span class="text-muted">${product[firstVariant].description}</span>`;
                        }
                        data += `</div>
                             </div>`;
                    }
                    data += `</div>
                                <div class="d-flex flex-column mt-4 mt-md-0">`;
                                    if(plan.discount) {
                                        data += `<span class="original-price line-through">R$ ${plan.original_price}</span>
                                                 <div class="d-flex mb-2">
                                                     <span class="price font-30 mr-1" style="line-height: .8">R$ ${plan.price}</span>
                                                     <span class="discount text-success font-weight-bold">${plan.discount}% OFF</span>
                                                 </div>`;
                                    }

                    if (!isEmpty(plan.installments)) {
                        data += `<div class="form-group">
                                    <select class="installments">`;
                        for (let installment of plan.installments) {
                            data += `<option value="${installment['amount']}">${installment['amount']}X DE R$ ${installment['value']}</option>`;
                        }
                        data += `</select>
                             </div>`;
                    } else {
                        data += `<h2 class="text-primary mb-md-4"><b>R$ ${plan.price}</b></h2>`;
                    }
                    data += `<button class="btn btn-success btn-lg btn-buy">COMPRAR AGORA</button>
                         </div>
                    </div>`;

                    if (parseInt(key) !== (upsell.plans.length - 1)) {
                        data += `<hr class="plan-separator">`;
                    }
                }

                $('#div-upsell-products').append(data);

                $('#modal-view-upsell-config').modal('show');
            }
        });

    });

    function setIntervalAndExecute(fn, t) {
        fn();
        return (setInterval(fn, t));
    }

    function startCountdown(countdownTime) {

        let countdown = new Date().getTime() + countdownTime * 60000;

        if(countdownInterval !== null){
            clearInterval(countdownInterval);
        }

        countdownInterval = setIntervalAndExecute(() => {
            let now = new Date().getTime();
            let distance = countdown - now;

            if (distance > 0) {
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                $('#minutes').text(minutes.toString().padStart(2, '0'));
                $('#seconds').text(seconds.toString().padStart(2, '0'));
            } else {
                countdown = new Date().getTime() + countdownTime * 60000;
            }
        }, 1000);
    }
});
