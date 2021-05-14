$(document).ready(function () {

    var projectId = $("#project-id").val();

    $("#tab-fretes").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarFrete();
    });
    atualizarFrete();

    function changeType() {
        $("#shipping-type").change(function () {
            // altera campo value dependendo do tipo do frete
            var selected = $("#shipping-type").val();
            if (selected === 'static') {
                $("#value-shipping-row").css('display', 'block');
                $("#zip-code-origin-shipping-row").css('display', 'none');

            } else {
                $("#value-shipping-row").css('display', 'none');
                $("#zip-code-origin-shipping-row").css('display', 'block');

            }

            //mask money
            $('#shipping-value').mask('#.###,#0', {reverse: true});
        });
    }

    //mask money
    $('#shipping-value').mask('#.###,#0', {reverse: true});

    $("#add-shipping").on('click', function () {

        $("#modal-title").html('Cadastrar frete <br><hr>');
        $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");

        $.ajax({
            method: "GET",
            url: "/shippings/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal-add-body").html('nao encontrado');
            },
            success: function (response) {
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();
                $("#modal-add-body").html(response);
                $('#shipping-zip-code-origin').mask('00000-000');

                changeType();

                $(".btn-save").unbind();
                $(".btn-save").click(function () {
                    var formData = new FormData(document.getElementById('form-add-shipping'));
                    formData.append("project", projectId);
                    $.ajax({
                        method: "POST",
                        url: "/shippings",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        error: function (response) {
                            if (response.status == '422') {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            }
                        },
                        success: function (data) {
                            alertCustom("success", data.message);
                            atualizarFrete();
                        }
                    });
                });

            }
        })
    });

    function atualizarFrete() {
        $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");

        $.ajax({
            method: "GET",
            url: '/shippings',
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                $("#dados-tabela-frete").html('Erro ao encontrar dados');
            },
            success: function (response) {

                $("#dados-tabela-frete").html('');

                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += '<td class="shipping-id " style="vertical-align: middle; display: none;">' + value.shipping_id + '</td>';
                    dados += '<td class="shipping-type " style="vertical-align: middle; display: none;">' + value.type + '</td>';
                    dados += '<td class="shipping-value " style="vertical-align: middle; display: none;">' + value.value + '</td>';
                    dados += '<td class="shipping-zip-code-origin " style="vertical-align: middle; display: none;">' + value.zip_code_origin + '</td>';
                    dados += '<td class="shipping-id " style="vertical-align: middle;">' + value.type + '</td>';
                    dados += '<td class="shipping-name " style="vertical-align: middle;">' + value.name + '</td>';
                    dados += '<td class="shipping-type " style="vertical-align: middle;">' + value.value + '</td>';
                    dados += '<td class="shipping-information " style="vertical-align: middle;">' + value.information + '</td>';
                    dados += '<td class="shipping-status " style="vertical-align: middle;">';
                    if (value.status === 1) {
                        dados += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        dados += '<span class="badge badge-danger">Desativado</span>';
                    }

                    dados += '</td>';

                    dados += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                    if (value.pre_selected === 1) {
                        dados += '<span class="badge badge-success">Sim</span>';
                    } else {
                        dados += '<span class="badge badge-primary"> Não </span>';
                    }

                    dados += '</td>';

                    dados += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger detalhes-frete'  frete='" + value.shipping_id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger editar-frete'  frete='" + value.shipping_id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger excluir-frete'  frete='" + value.shipping_id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
                    dados += '</tr>';
                    $("#dados-tabela-frete").append(dados);
                });

                if (response.data == '') {
                    $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='6' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
                }
                $(".detalhes-frete").unbind('click');
                $(".detalhes-frete").on('click', function () {

                    var frete = $(this).attr('frete');

                    $("#modal-title").html('Detalhes do frete <br><hr>');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
                    var data = {freteId: frete};

                    $("#btn-modal").hide();

                    $.ajax({
                        method: "GET",
                        url: "/shippings/" + frete,
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            $("#modal-add-body").html(response);

                        }
                    });
                });

                $(".editar-frete").unbind('click');
                $(".editar-frete").on("click", function () {
                    $("#modal-add-body").html("");
                    var frete = $(this).attr('frete');

                    $("#modal-title").html("Editar Frete<br><hr>");
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");

                    var data = {frete: frete};

                    $.ajax({
                        method: "GET",
                        url: "/shippings/" + frete + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);
                            $('#shipping-zip-code-origin').mask('00000-000');

                            if ($("#shipping-type").val() == 'static') {
                                $("#value-shipping-row").css('display', 'block');
                                $("#zip-code-origin-shipping-row").css('display', 'none');

                            } else {
                                $("#value-shipping-row").css('display', 'none');
                                $("#zip-code-origin-shipping-row").css('display', 'block');

                            }
                            changeType();
                            $('#shipping-value').mask('#.###,#0', {reverse: true});

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                var formData = new FormData(document.getElementById('form-update-shipping'));
                                formData.append("project", projectId);
                                $.ajax({
                                    method: "POST",
                                    url: "/shippings/" + frete,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    error: function (response) {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    },
                                    success: function (data) {
                                        alertCustom("success", "Frete atualizado com sucesso");
                                        atualizarFrete();
                                    }
                                });

                            });
                        }
                    });
                });

                $(".excluir-frete").on('click', function (event) {
                    event.preventDefault();
                    var frete = $(this).attr('frete');

                    $("#modal_excluir_titulo").html("Remover Frete?");

                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on("click", function () {
                        $("#fechar_modal_excluir").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/shippings/" + frete,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                            },
                            success: function (data) {
                                alertCustom("success", "Frete Removido com sucesso");
                                atualizarFrete();
                            }

                        })

                    });
                })
            }
        });
    }

    $("#shippement").on('change', function () {
        if ($(this).val() == 0) {
            $("#div-carrier").hide();
            $("#div-shipment-responsible").hide();
        } else {
            $("#div-carrier").show();
            $("#div-shipment-responsible").show();
        }
    });

    $("#bt-add-shipping-config").unbind('click');
    $("#bt-add-shipping-config").on('click', function (event) {
        event.preventDefault();
        var formData = new FormData(document.getElementById('form-config-shipping'));

        $.ajax({
            method: "POST",
            url: "/shipping/config/" + projectId,
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            }, error: function () {
                //
            }, success: function () {
                alertCustom('success', 'Configuração atualizadas com sucesso');
                atualizarFrete();
            }
        });
    });

});
