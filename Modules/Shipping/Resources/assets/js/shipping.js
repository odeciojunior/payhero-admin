let statusShipping = {
    1: "success",
    0: "danger",
}

let activeShipping = {
    1: "success",
    0: "danger",
}

$(document).ready(function () {

    let projectId = $(window.location.pathname.split('/')).get(-1);

    loadMelhorEnvioOptions();

    function loadMelhorEnvioOptions() {
        $.ajax({
            url: "/api/apps/melhorenvio",
            data: {
              completed: 1
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: resp => {
                let options = ``;
                for (let integration of resp.data) {
                    options += `<option class="menv${integration.id}" value="melhorenvio-${integration.id}">${integration.name} (integração com a API do Melhor Envio)</option>`;
                }
                $('.shipping-type select').each(function () {
                    $(this).append(options);
                });
            },
            error: resp => {
            }
        });
    }

    //comportamentos da tela
    $(".tab-fretes").on('click', function () {
        $(this).off();
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarFrete();
    });

    $(document).on('change', '#shipping-type', function () {
        // altera campo value dependendo do tipo do frete
        let selected = $(this).val();
        if (selected === 'static') {
            $('.information-shipping-row').show();
            $('.value-shipping-row').show();
            $('.zip-code-origin-shipping-row').hide();
            $('.options-shipping-row').hide();
            $('.shipping-description').attr('placeholder', 'Frete grátis');
        } else if (selected === 'pac') {
            $('.information-shipping-row').show();
            $('.value-shipping-row').hide();
            $('.zip-code-origin-shipping-row').show();
            $('.options-shipping-row').hide();
            $('.shipping-description').attr('placeholder', 'PAC');
        } else if (selected === 'sedex') {
            $('.information-shipping-row').show();
            $('.value-shipping-row').hide();
            $('.zip-code-origin-shipping-row').show();
            $('.options-shipping-row').hide();
            $('.shipping-description').attr('placeholder', 'SEDEX');
        } else if (selected.includes('melhorenvio')) {
            $('.information-shipping-row').hide();
            $('.value-shipping-row').hide();
            $('.zip-code-origin-shipping-row').show();
            $('.options-shipping-row').show();
            $('.shipping-description').attr('placeholder', 'Melhor Envio');
        }
    });

    $('.shipping-value').mask('#.##0,00', {reverse: true});
    $('.rule-shipping-value').mask('#.##0,00', {reverse: true});

    $('.rule-shipping-value').on('blur', function () {
        if ($(this).val().length == 1) {
            let val = '0,0' + $(this).val();
            $('.rule-shipping-value').val(val);
        } else if ($(this).val().length == 2) {
            let val = '0,' + $(this).val();
            $('.rule-shipping-value').val(val);
        }
    });

    setSelect2Plugin('#shipping-plans-add', '.shipping-plans-add-container')
    setSelect2Plugin('#shipping-plans-edit', '.shipping-plans-edit-container')
    setSelect2Plugin('#shipping-not-apply-plans-add', '.shipping-not-apply-plans-add-container')
    setSelect2Plugin('#shipping-not-apply-plans-edit', '.shipping-not-apply-plans-edit-container')

    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    //Limpa campos
    function clearFields() {
        $('.shipping-description').val('');
        $('.shipping-info').val('');
        $('.shipping-value').val('');
        $('.shipping-zipcode').val('');
        $('.rule-shipping-value').val('');
        $('#shipping-plans-add').html('');
        $('#shipping-not-apply-plans-add').html('');

        var elem = $('#shipping-plans-add')
        elem.html('')
        elem.append('<option value="all">Qualquer plano</option>');
        elem.val('all').trigger('change')
    }

    clearFields()

    $(".shipping-description").keyup(function () {
        if ($(this).val().length > 60) {
            $(this).parent().children("#shipping-name-error").html("O campo descrição permite apenas 60 caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-name-error").html("");
        }
    });

    $(".shipping-info").keyup(function () {
        if ($(this).val().length > 100) {
            $(this).parent().children("#shipping-information-error").html("O campo tempo de entrega estimado permite apenas 100 caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-information-error").html("");
        }
    });

    $(".shipping-value").keyup(function () {
        if ($.trim($(this).val()).length > 8) {
            $(this).parent().children("#shipping-value-error").html("O campo valor permite apenas 6  caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-value-error").html("");
        }
    });

    // carregar modal de detalhes
    $(document).on('click', '.detalhes-frete', function () {
        let frete = $(this).attr('frete');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/shippings/" + frete,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                switch (response.type) {
                    case 'static':
                        $('#modal-detail-shipping #shipping-type').html('Estático');
                        break;
                    case 'pac':
                        $('#modal-detail-shipping #shipping-type').html('PAC - Caculado automaticamente');
                        break;
                    case 'sedex':
                        $('#modal-detail-shipping #shipping-type').html('SEDEX - Caculado automaticamente');
                        break;
                    case 'melhorenvio':
                        $('#modal-detail-shipping #shipping-type').html('MelhorEnvio - Caculado automaticamente');
                        break;
                }
                $('#modal-detail-shipping .shipping-description').html(response.name);
                $('#modal-detail-shipping .shipping-value').html(response.type != 'static' ? ' Calculado automaticamente' : response.value);
                $('#modal-detail-shipping .shipping-info').html(response.information);
                $('#modal-detail-shipping .rule-shipping-value').html(response.rule_value);
                $('#modal-detail-shipping .shipping-status').html(response.status == 1 ? '<span class="badge badge-success text-left">Ativo</span>' : '<span class="badge badge-danger">Desativado</span>');
                $('#modal-detail-shipping .shipping-pre-selected').html(response.pre_selected == 1 ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-primary">Não</span>');

                $('#modal-detail-shipping').modal('show');
            }
        });
    });

    // carregar modal de edicao
    $(document).on("click", '.editar-frete', function () {
        let frete = $(this).attr('frete');
        $(this).attr('frete');

        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/shippings/" + frete + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {

                $('#modal-edit-shipping .shipping-id').val(response.id_code).change();

                switch (response.type) {
                    case 'static':
                        $('#modal-edit-shipping #shipping-type').prop("selectedIndex", 0).change();
                        break;
                    case 'pac':
                        $('#modal-edit-shipping #shipping-type').prop("selectedIndex", 1).change();
                        break;
                    case 'sedex':
                        $('#modal-edit-shipping #shipping-type').prop("selectedIndex", 2).change();
                        break;
                    case 'melhorenvio':
                        $('#modal-edit-shipping .menv'+response.melhorenvio_integration_id).prop('selected', true);
                        $('#modal-edit-shipping #shipping-type').change();
                        break;
                }
                $('#modal-edit-shipping .shipping-description').val(response.name);
                $('#modal-edit-shipping .shipping-info').val(response.information);
                $('#modal-edit-shipping .shipping-value').val(response.value);
                $('#modal-edit-shipping .rule-shipping-value').val(response.rule_value).trigger('input');
                $('#modal-edit-shipping .shipping-zipcode').val(response.zip_code_origin);
                $('#modal-edit-shipping .shipping-status').prop('checked', !!response.status).change();
                $('#modal-edit-shipping .shipping-pre-selected').prop('checked', !!response.pre_selected).change();
                $('#modal-edit-shipping .shipping-receipt').prop('checked', !!response.receipt).change();
                $('#modal-edit-shipping .shipping-ownhand').prop('checked', !!response.own_hand).change();

                // Seleciona a opção do select de acordo com o que vem do banco
                var applyOnPlansEl = $('#modal-edit-shipping .shipping-plans-edit')
                applyOnPlansEl.html('')
                var applyOnPlans = []
                for (let plan of response.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    applyOnPlansEl.append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
                }
                applyOnPlansEl.val(applyOnPlans).trigger('change')

                var notApplyOnPlansEl = $('#modal-edit-shipping .shipping-not-apply-plans-edit')
                notApplyOnPlansEl.html('')
                var notApplyOnPlans = []
                for (let plan of response.not_apply_on_plans) {
                    notApplyOnPlans.push(plan.id);
                    notApplyOnPlansEl.append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
                }
                notApplyOnPlansEl.val(notApplyOnPlans).trigger('change')

                $('#modal-edit-shipping').modal('show');
            }
        });
    });

    //carregar modal delecao
    $(document).on('click', '.excluir-frete', function (event) {
        event.preventDefault();

        let frete = $(this).attr('frete');

        //deletar frete
        $('#btn-delete-frete').unbind('click');
        $('#btn-delete-frete').on('click', function () {
            $.ajax({
                method: "DELETE",
                url: "/api/project/" + projectId + "/shippings/" + frete,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    errorAjaxResponse(response);
                },
                success: function success(data) {
                    atualizarFrete();

                    alertCustom("success", "Frete Removido com sucesso");
                }
            });
        });
    });

    //cria novo frete
    $("#modal-create-shipping .btn-save").click(function () {
        let formData = new FormData(document.getElementById('form-add-shipping'));

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/shippings",
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
            },
            success: function success(data) {
                alertCustom("success", data.message);
                atualizarFrete();
                clearFields();
            }
        });
    });

    //atualizar frete
    $("#modal-edit-shipping .btn-update").on('click', function () {
        let formData = new FormData(document.querySelector('#modal-edit-shipping #form-update-shipping'));
        formData.set('status', $('#modal-edit-shipping .shipping-status').is(':checked') ? 1 : 0);
        formData.set('pre_selected', $('#modal-edit-shipping .shipping-pre-selected').is(':checked') ? 1 : 0);
        formData.set('receipt', $('#modal-edit-shipping .shipping-receipt').is(':checked') ? 1 : 0);
        formData.set('own_hand', $('#modal-edit-shipping .shipping-ownhand').is(':checked') ? 1 : 0);
        let frete = $('#modal-edit-shipping .shipping-id').val();

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/shippings/" + frete,
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
            },
            success: function success() {
                alertCustom("success", "Frete atualizado com sucesso");
                atualizarFrete();
            }
        });
    });



    function atualizarFrete() {

        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/project/' + projectId + '/shippings';
        } else {
            link = '/api/project/' + projectId + '/shippings' + link;
        }

        loadOnTable('#dados-tabela-frete', '#tabela_fretes');

        $('#tab-fretes-panel').find('.no-gutters').css('display', 'none');
        $('#tabela-fretes').find('thead').css('display', 'none');

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                $("#dados-tabela-frete").html(response.message);
                errorAjaxResponse(response);
            },
            success: function success(response) {

                $("#dados-tabela-frete").html('');

                if (response.data == '') {
                    $("#dados-tabela-frete").html(`
                        <tr class='text-center'>
                            <td colspan='8' style='height: 70px; vertical-align: middle;'>
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum frete configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro frete para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-shipping' data-toggle="modal" data-target="#modal-create-shipping">Adicionar frete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    $('#tab-fretes-panel').find('.no-gutters').css('display', 'flex');
                    $('#tabela-fretes').find('thead').css('display', 'contents');
                    $('#count-fretes').html(response.meta.total);

                    $.each(response.data, function (index, value) {

                        let dados = `<tr>
                                        <td style="vertical-align: middle; display: none;">${value.zip_code_origin}</td>
                                        <td style="vertical-align: middle;">${value.type_name}</td>
                                        <td style="vertical-align: middle;">${value.name}</td>
                                        <td style="vertical-align: middle;">${value.value}</td>
                                        <td style="vertical-align: middle;">${value.information}</td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <span class="badge badge-${statusShipping[value.status]}">${value.status_translated}</span>
                                        </td>
                                        <td class="text-center display-sm-none display-m-none" style="vertical-align: middle;">
                                            <span class="badge badge-${activeShipping[value.pre_selected]}">${value.pre_selected_translated}</span>
                                        </td>
                                        <td style='text-align:center'>
                                            <div class='d-flex justify-content-end align-items-center'>
                                                <a role='button' title='Visualizar' class='pointer detalhes-frete mg-responsive' frete="${value.shipping_id}"><span class="o-eye-1"></span></a>
                                                <a role='button' title='Editar' class='pointer editar-frete mg-responsive' frete="${value.shipping_id}"><span class='o-edit-1'></span></a>
                                                <a role='button' title='Excluir' class='pointer excluir-frete mg-responsive' frete="${value.shipping_id}" data-toggle='modal' data-target='#modal-delete-shipping'><span class='o-bin-1'></span></a>
                                            </div>
                                        </td>
                                     </tr>`;
                        $("#dados-tabela-frete").append(dados);
                    });

                    if ($('#dados-tabela-frete').children('tr:first').children('td:first').css('display') == 'none') {
                        $('#dados-tabela-frete').children('tr').children('td:nth-child(2)').css('padding-left', '30px');
                    } else {
                        $('#dados-tabela-frete').children('tr').children('td:nth-child(2)').css('padding-left', '');
                    }

                    pagination(response, 'shippings', atualizarFrete);
                }
            }
        });
    }

    function setSelect2Plugin(el, dropdownParent) {
        el = $(el)
        el.select2({
            placeholder: 'Nome do plano',
            multiple: true,
            dropdownParent: $(dropdownParent),
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
                    if ((elemId === 'shipping-plans-add' || elemId === 'shipping-plans-edit') && res.meta.current_page === 1) {
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

        $(document).on('show.bs.modal', '#modal-create-shipping', () => {
            $('#modal-create-shipping #shipping-type').val('static').change();
        })

        el.on('select2:select', function () {
            let selectPlan = $(this);
            if ((selectPlan.val().length > 1 && selectPlan.val().includes('all')) || (selectPlan.val().includes('all') && selectPlan.val() !== 'all')) {
                selectPlan.val('all').trigger("change");
            }
        });
    }
});
