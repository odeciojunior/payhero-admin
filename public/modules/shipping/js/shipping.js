var statusShipping = {
    1: "success",
    0: "danger",
}

var activeShipping = {
    1: "success",
    0: "danger",
}

$(document).ready(function () {

    var projectId = $("#project-id").val();

    //comportamentos da tela
    $("#tab-fretes").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarFrete();
    });

    $(document).on('change', '.shipping-type', function () {
        // altera campo value dependendo do tipo do frete
        var selected = $(this).val();
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

    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $(".shipping-description").keyup(function () {
        if ($(this).val().length > 30) {
            $(this).parent().children("#shipping-name-error").html("O campo descrição permite apenas 30 caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-name-error").html("");
        }
    });

    $(".shipping-info").keyup(function () {
        if ($(this).val().length > 30) {
            $(this).parent().children("#shipping-information-error").html("O campo tempo de entrega estimado permite apenas 30 caracteres");
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
        var frete = $(this).attr('frete');
        var data = {freteId: frete};

        $.ajax({
            method: "GET",
            url: "/api/shippings/" + frete,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom("error", "Erro ao carregar detalhes do frete");
            }, success: function success(response) {
                switch (response.type) {
                    case 'static':
                        $('#modal-detail-shipping .shipping-type').html('Estático');
                        break;
                    case 'pac':
                        $('#modal-detail-shipping .shipping-type').html('PAC - Caculado automáticamente');
                        break;
                    default:
                        $('#modal-detail-shipping .shipping-type').html('SEDEX - Caculado automáticamente');
                        break;
                }
                $('#modal-detail-shipping .shipping-description').html(response.name);
                $('#modal-detail-shipping .shipping-value').html(response.type != 'static' ? ' Calculado automáticamente' : response.value);
                $('#modal-detail-shipping .shipping-info').html(response.information);
                $('#modal-detail-shipping .shipping-status').html(response.status == 1 ? '<span class="badge badge-success text-left">Ativo</span>' : '<span class="badge badge-danger">Desativado</span>');
                $('#modal-detail-shipping .shipping-pre-selected').html(response.pre_selected == 1 ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-primary">Não</span>');

                $('#modal-detail-shipping').modal('show');
            }
        });
    });

    // carregar modal de edicao
    $(document).on("click", '.editar-frete', function () {
        var frete = $(this).attr('frete');
        $(this).attr('frete');

        var data = {frete: frete};

        $.ajax({
            method: "GET",
            url: "/api/shippings/" + frete + "/edit",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom("error", "Erro ao tentar editar frete");
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
                $('#modal-edit-shipping .shipping-zipcode').val(response.zip_code_origin);
                if (response.status == 1) {
                    $('#modal-edit-shipping .shipping-status').val(1).prop('checked', true);
                } else {
                    $('#modal-edit-shipping .shipping-status').val(0).prop('checked', false);
                }
                if (response.pre_selected == 1) {
                    $('#modal-edit-shipping .shipping-pre-selected').val(1).prop('checked', true);
                } else {
                    $('#modal-edit-shipping .shipping-pre-selecteds').val(0).prop('checked', false);
                }

                $('#modal-edit-shipping').modal('show');
            }
        });
    });

    //carregar modal delecao
    $(document).on('click', '.excluir-frete', function (event) {
        var frete = $(this).attr('frete');
        $("#modal-delete-shipping .btn-delete").attr('frete', frete);
        $("#modal-delete-shipping").modal('show');
    });

    //cria novo frete
    $("#modal-create-shipping .btn-save").click(function () {
        var formData = new FormData(document.getElementById('form-add-shipping'));
        formData.append("project_id", projectId);
        loadingOnScreen();

        $.ajax({
            method: "POST",
            url: "/api/shippings",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (_error2) {
                function error(_x) {
                    return _error2.apply(this, arguments);
                }

                error.toString = function () {
                    return _error2.toString();
                };

                return error;
            }(function (response) {
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));

                }
            }),
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", data.message);
                atualizarFrete();
            }
        });
    });

    //atualizar frete
    $("#modal-edit-shipping .btn-update").on('click', function () {
        var formData = new FormData(document.querySelector('#modal-edit-shipping #form-update-shipping'));
        formData.append("project_id", projectId);
        formData.append('status', $('#modal-edit-shipping .shipping-status').val());
        formData.append('pre_selected', $('#modal-edit-shipping .shipping-pre-selected').val());
        let frete = $('#modal-edit-shipping .shipping-id').val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/shippings/" + frete,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
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
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));
                }
            }),
            success: function success(data) {
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
            url: "/api/shippings/" + frete,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (_error4) {
                function error(_x3) {
                    return _error4.apply(this, arguments);
                }

                error.toString = function () {
                    return _error4.toString();
                };

                return error;
            }(function (response) {
                loadingOnScreenRemove();
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
                if (response.status == '400') {
                    alertCustom('error', response.responseJSON.message);
                }
            }),
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", "Frete Removido com sucesso");
                atualizarFrete();
            }
        });
    });

    function atualizarFrete(link = null) {

        loadOnTable('#dados-tabela-frete', '#tabela_fretes');
        if (link == null) {
            link = '/api/shippings?' + 'project=' + projectId;
        } else {
            link = '/api/shippings' + link + '&project=' + projectId;
        }
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function error(response) {
                $("#dados-tabela-frete").html(response.message);
            },
            success: function success(response) {

                $("#dados-tabela-frete").html('');

                if (response.data == '') {
                    $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        let dados = `<tr>
                                        <td style="vertical-align: middle; display: none;">${value.zip_code_origin}</td>
                                        <td style="vertical-align: middle;">${value.type}</td>
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
                                            <a role='button' class='pointer detalhes-frete mg-responsive' frete="${value.shipping_id}"><i class='material-icons gradient'>remove_red_eye</i></a>
                                            <a role='button' class='pointer editar-frete mg-responsive' frete="${value.shipping_id}"><i class='material-icons gradient'> edit </i></a>
                                            <a role='button' class='pointer excluir-frete mg-responsive' frete="${value.shipping_id}"><i class='material-icons gradient'> delete_outline </i></a>
                                        </td>
                                     </tr>`;
                        $("#dados-tabela-frete").append(dados);
                    });

                    pagination(response, 'shippings', atualizarFrete);
                }
            }
        });
    }
});
