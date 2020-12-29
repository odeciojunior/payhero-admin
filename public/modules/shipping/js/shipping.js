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

    //comportamentos da tela
    $("#tab-fretes").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarFrete();
    });

    $(document).on('change', '.shipping-type', function () {
        // altera campo value dependendo do tipo do frete
        let selected = $(this).val();
        if (selected === 'static') {
            $('.shipping-description').attr('placeholder', 'Frete grátis');
            $(".value-shipping-row").css('display', 'block');
            $(".zip-code-origin-shipping-row").css('display', 'none');
        } else if (selected == 'pac') {
            $('.shipping-description').attr('placeholder', 'PAC');
            $(".value-shipping-row").css('display', 'none');
            $(".zip-code-origin-shipping-row").css('display', 'block');
        } else if (selected == 'sedex') {
            $('.shipping-description').attr('placeholder', 'SEDEX');
            $(".value-shipping-row").css('display', 'none');
            $(".zip-code-origin-shipping-row").css('display', 'block');
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
    }

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

    //carrega os itens na tabela
    atualizarFrete();

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
                        $('#modal-detail-shipping .shipping-type').html('Estático');
                        break;
                    case 'pac':
                        $('#modal-detail-shipping .shipping-type').html('PAC - Caculado automaticamente');
                        break;
                    default:
                        $('#modal-detail-shipping .shipping-type').html('SEDEX - Caculado automaticamente');
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

                $('#modal-edit-shipping .shipping-id').val(response.id_code);

                switch (response.type) {
                    case 'pac':
                        $('#modal-edit-shipping .shipping-type').prop("selectedIndex", 0).change();
                        break;
                    case 'sedex':
                        $('#modal-edit-shipping .shipping-type').prop("selectedIndex", 1).change();
                        break;
                    case 'static':
                        $('#modal-edit-shipping .shipping-type').prop("selectedIndex", 2).change();
                        break;
                }
                $('#modal-edit-shipping .shipping-description').val(response.name);
                $('#modal-edit-shipping .shipping-info').val(response.information);
                $('#modal-edit-shipping .shipping-value').val(response.value);
                $('#modal-edit-shipping .rule-shipping-value').val(response.rule_value);
                $('#modal-edit-shipping .rule-shipping-value').trigger('input');
                $('#modal-edit-shipping .shipping-zipcode').val(response.zip_code_origin);
                if (response.status == 1) {
                    $('#modal-edit-shipping .shipping-status').attr('checked', true);
                } else {
                    $('#modal-edit-shipping .shipping-status').attr('checked', false);
                }
                if (response.pre_selected == 1) {
                    $('#modal-edit-shipping .shipping-pre-selected').attr('checked', true);
                } else {
                    $('#modal-edit-shipping .shipping-pre-selected').attr('checked', false);
                }

                // Seleciona a opção do select de acordo com o que vem do banco
                var plans = $('#modal-edit-shipping .shipping-plans-edit')
                plans.html('')
                let applyOnPlans = []
                for (let plan of response.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    plans.append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
                }
                plans.val(applyOnPlans).trigger('change')

                $('#modal-edit-shipping').modal('show');
            }
        });
    });

    //carregar modal delecao
    $(document).on('click', '.excluir-frete', function (event) {
        let frete = $(this).attr('frete');
        $("#modal-delete-shipping .btn-delete").attr('frete', frete);
        $("#modal-delete-shipping").modal('show');
    });

    //cria novo frete
    $("#modal-create-shipping .btn-save").click(function () {
        let formData = new FormData(document.getElementById('form-add-shipping'));
        loadingOnScreen();

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
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", data.message);
                atualizarFrete();
                clearFields();
            }
        });
    });

    //atualizar frete
    $("#modal-edit-shipping .btn-update").on('click', function () {
        let formData = new FormData(document.querySelector('#modal-edit-shipping #form-update-shipping'));
        formData.append('status', $('#modal-edit-shipping .shipping-status').val());
        formData.append('pre_selected', $('#modal-edit-shipping .shipping-pre-selected').val());
        let frete = $('#modal-edit-shipping .shipping-id').val();
        loadingOnScreen();
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
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Frete atualizado com sucesso");
                atualizarFrete();
            }
        });
    });

    //deletar frete
    $(document).on("click", '#modal-delete-shipping .btn-delete', function () {
        let frete = $(this).attr('frete');
        $.ajax({
            method: "DELETE",
            url: "/api/project/" + projectId + "/shippings/" + frete,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", "Frete Removido com sucesso");
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
                    $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {

                        let dados = `<tr>
                                        <td style="vertical-align: middle; display: none;">${value.zip_code_origin}</td>
                                        <td style="vertical-align: middle;">${value.type_name}</td>
                                        <td style="vertical-align: middle;">${value.name}</td>
                                        <td style="vertical-align: middle;">${value.value}</td>
                                        <td style="vertical-align: middle;">${value.information}</td>
                                        <td style="vertical-align: middle;">
                                        <span class="badge badge-${statusShipping[value.status]}">${value.status_translated}</span>
                                        </td>
                                        <td class="text-center display-sm-none display-m-none" style="vertical-align: middle;">
                                            <span class="badge badge-${activeShipping[value.pre_selected]}">${value.pre_selected_translated}</span>
                                        </td>
                                        <td style='text-align:center'>
                                            <a role='button' title='Visualizar' class='pointer detalhes-frete mg-responsive' frete="${value.shipping_id}"><img src="/modules/global/img/svg/eye.svg" style="width: 24px"></a>
                                            <a role='button' title='Editar' class='pointer editar-frete mg-responsive' frete="${value.shipping_id}"><img src='/modules/global/img/svg/edit.svg' style='width: 24px'></a>
                                            <a role='button' title='Excluir' class='pointer excluir-frete mg-responsive' frete="${value.shipping_id}"><img src='/modules/global/img/svg/sirius-lixo.svg' style='width: 24px'></a>
                                        </td>
                                     </tr>`;
                        $("#dados-tabela-frete").append(dados);
                    });

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

        el.on('select2:select', function () {
            let selectPlan = $(this);
            if ((selectPlan.val().length > 1 && selectPlan.val().includes('all')) || (selectPlan.val().includes('all') && selectPlan.val() !== 'all')) {
                selectPlan.val('all').trigger("change");
            }
        });
    }
});
