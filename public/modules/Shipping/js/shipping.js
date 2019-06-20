$(document).ready(function () {

    var projectId = $("#project-id").val();

    $("#tab-fretes").on('click', function () {
        atualizarFrete();
    });
    atualizarFrete();

    function changeType() {
        $("#shipping-type").change(function () {
            // altera campo value dependendo do tipo do frete
            var selected = $("#shipping-type").val();
            if (selected === 'static') {
                $("#value-shipping-row").css('display', 'block');
            } else {
                $("#value-shipping-row").css('display', 'none');
            }

            //mask money
            $('#shipping-value').mask('#.###,#0', {reverse: true});

        });
    }
    //mask money
    $('#shipping-value').mask('#.###,#0', {reverse: true});

    $("#add-shipping").on('click', function () {
        $("#modal-frete-titulo").html('Cadastrar frete <br><hr>');
        $("#modal-frete-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");

        $.ajax({
            method: "GET",
            url: "/shippings/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal-frete-body").html('nao encontrado');
            },
            success: function (response) {
                $("#btn-save-updated").addClass('btn-save');
                $("#btn-save-updated").text('Salvar');
                $("#btn-save-updated").show();
                $("#modal-frete-body").html(response);
                changeType();

                $(".btn-save").unbind();
                $(".btn-save").click(function () {

                    $.ajax({
                        method: "POST",
                        url: "/shippings/",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            type: $("#shipping-type").val(),
                            name: $("#shipping-name").val(),
                            information: $("#shipping-information").val(),
                            value: $("#shipping-value").val(),
                            zip_code_origin: $("#shipping-zip-code-origin").val(),
                            status: $("#shipping-status").val(),
                            pre_selected: $("#shipping-pre-selected").val(),
                            project: projectId,
                        },
                        error: function (response) {
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom('error', String(response.errors[error]));
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
                    dados += '<td class="shipping-id text-center" style="vertical-align: middle; display: none;">' + value.shipping_id + '</td>';
                    dados += '<td class="shipping-type text-center" style="vertical-align: middle; display: none;">' + value.type + '</td>';
                    dados += '<td class="shipping-value text-center" style="vertical-align: middle; display: none;">' + value.value + '</td>';
                    dados += '<td class="shipping-zip-code-origin text-center" style="vertical-align: middle; display: none;">' + value.zip_code_origin + '</td>';
                    dados += '<td class="shipping-id text-center" style="vertical-align: middle;">' + value.type + '</td>';
                    dados += '<td class="shipping-name text-center" style="vertical-align: middle;">' + value.name + '</td>';
                    dados += '<td class="shipping-type text-center" style="vertical-align: middle;">' + value.value + '</td>';
                    dados += '<td class="shipping-information text-center" style="vertical-align: middle;">' + value.information + '</td>';
                    dados += '<td class="shipping-status text-center" style="vertical-align: middle;">';
                    if (value.status === 1) {
                        dados += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        dados += '<span class="badge badge-danger">Desativado</span>';
                    }

                    dados += '</td>';

                    dados += '<td class="shipping-pre-selected text-center" style="vertical-align: middle;">';
                    if (value.pre_selected === 1) {
                        dados += '<span class="badge badge-success">Sim</span>';
                    } else {
                        dados += '<span class="badge badge-primary"> Não </span>';
                    }

                    dados += '</td>';

                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger detalhes-frete'  frete='" + value.shipping_id + "' data-target='#modal-detalhes-frete' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger editar-frete'  frete='" + value.shipping_id + "' data-target='#modal-detalhes-frete' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger excluir-frete'  frete='" + value.shipping_id + "'  data-toggle='modal' data-target='#modal_excluir' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";

                    dados += '</tr>';
                    $("#dados-tabela-frete").append(dados);
                });

                if (response.data === '') {
                    $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum frete encontrado</td></tr>")
                }
                $(".detalhes-frete").unbind('click');
                $(".detalhes-frete").on('click', function () {

                    var frete = $(this).attr('frete');

                    $("#modal-frete-titulo").html('Detalhes do frete <br><hr>');
                    $("#modal-frete-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
                    var data = {freteId: frete};

                    $("#btn-save-updated").hide();

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
                            $("#modal-frete-body").html(response);

                        }
                    });
                });

                $(".editar-frete").unbind('click');
                $(".editar-frete").on("click", function () {
                    var frete = $(this).attr('frete');

                    $("#modal-frete-titulo").html("Editar Frete<br><hr>");
                    $("#modal-frete-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");

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
                            $("#btn-save-updated").addClass('btn-update');
                            $("#btn-save-updated").text('Atualizar');
                            $("#btn-save-updated").show();
                            $("#modal-frete-body").html(response);
                            changeType();
                            $('#shipping-value').mask('#.###,#0', {reverse: true});

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {

                                $.ajax({
                                    method: "PUT",
                                    url: "/shippings/" + frete,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        type: $("#shipping-type").val(),
                                        name: $("#shipping-name").val(),
                                        information: $("#shipping-information").val(),
                                        value: $("#shipping-value").val(),
                                        zip_code_origin: $("#shipping-zip-code-origin").val(),
                                        status: $("#shipping-status").val(),
                                        pre_selected: $("#shipping-pre-selected").val(),
                                    },
                                    error: function () {
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

});
