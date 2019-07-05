$(function () {
    var projectId = $("#project-id").val();

    $('#tab_pixels').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarPixel();
    });
    atualizarPixel();
    //criar novo pixel
    $("#add-pixel").on('click', function () {
        $("#modal-title").html('Adicionar Pixel <br><hr class="my-0">');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal_add_size").removeClass('modal-lg');

        $("#modal-add-body").html("<div style='text-align:center;'>Carregando...</div>");

        $.ajax({
            method: "GET",
            url: "/pixels/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function (data) {
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);

                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });

                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-pixel'));
                    formData.append('project', projectId);
                    formData.append('checkout', $("#checkout").val());
                    formData.append('purchase_card', $("#purchase_card").val());
                    formData.append('purchase_boleto', $("#purchase_boleto").val());

                    $.ajax({
                        method: "POST",
                        url: "/pixels",
                        headers: {
                            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        error: function (data) {
                            $("#modal_add_produto").hide();
                            $(".loading").css("visibility", "hidden");
                            alertCustom('error', 'Ocorreu algum erro');
                        }, success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom("success", "Pixel Adicionado!");
                            atualizarPixel();
                        }
                    });
                });
            }
        });
    });
    function atualizarPixel() {
        $("#data-table-pixel").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");
        $.ajax({
            method: "GET",
            url: "/pixels",
            data: {project: projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#data-table-pixel").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#data-table-pixel").html('');
                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += '<td class="shipping-id " style="vertical-align: middle;">' + value.name + '</td>';
                    data += '<td class="shipping-type " style="vertical-align: middle;">' + value.code + '</td>';
                    data += '<td class="shipping-value " style="vertical-align: middle;">' + value.platform + '</td>';
                    data += '<td class="shipping-status " style="vertical-align: middle;">';
                    data += '<td class="shipping-id" style="vertical-align: middle;">' + value.name + '</td>';
                    data += '<td class="shipping-type" style="vertical-align: middle;">' + value.code + '</td>';
                    data += '<td class="shipping-value" style="vertical-align: middle;">' + value.platform + '</td>';
                    data += '<td class="shipping-status" style="vertical-align: middle;">';
                    if (value.status == 1) {
                        data += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        data += '<span class="badge badge-danger">Desativado</span>';
                    }
                    data += '</td>';

                    data += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger details-pixel'  pixel='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger edit-pixel'  pixel='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger delete-pixel'  pixel='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
                    data += '</tr>';
                    $("#data-table-pixel").append(data);
                });
                if (response.data == '') {
                    $("#data-table-pixel").html("<tr class=''><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
                }
                $(".details-pixel").unbind('click');
                $(".details-pixel").on('click', function () {
                    var pixel = $(this).attr('pixel');
                    $("#modal-title").html('Detalhes do pixel <br><hr>');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
                    var data = {pixelId: pixel};
                    $("#btn-modal").hide();
                    $.ajax({
                        method: "GET",
                        url: "/pixels/" + pixel,
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
                $(".edit-pixel").unbind('click');
                $(".edit-pixel").on('click', function () {
                    $("#modal-add-body").html("");
                    var pixel = $(this).attr('pixel');
                    $("#modal-title").html("Editar Pixel<br><hr>");
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {pixelId: pixel};
                    $.ajax({
                        method: "GET",
                        url: "/pixels/" + pixel + "/edit",
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
                            $('.check').on('click', function () {
                                if ($(this).is(':checked')) {
                                    $(this).val(1);
                                } else {
                                    $(this).val(0);
                                }
                            });

                            // changeType();
                            // $('#shipping-value').mask('#.###,#0', {reverse: true});

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {

                                $.ajax({
                                    method: "PUT",
                                    url: "/pixels/" + pixel,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        name: $("#name_pixel").val(),
                                        code: $("#code").val(),
                                        platform: $("#platform").val(),
                                        status: $("#status").val(),
                                        checkout: $("#checkout").val(),
                                        purchase_card: $("#purchase_card").val(),
                                        purchase_boleto: $("#purchase_boleto").val(),
                                        purchase_card: $("#purchase_card").val(),

                                    },
                                    error: function () {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    },
                                    success: function (data) {
                                        alertCustom("success", "Pixel atualizado com sucesso");
                                        atualizarPixel();
                                    }
                                });

                            });
                        }
                    });

                });

                $('.delete-pixel').on('click', function (event) {
                    event.preventDefault();
                    var pixel = $(this).attr('pixel');
                    $("#modal_excluir_titulo").html("Remover Pixel?");
                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#fechar_modal_excluir").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/pixels/" + pixel,
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
                                alertCustom("success", "Pixel Removido com sucesso");
                                atualizarPixel();
                            }

                        })
                    });

                });
            }
        });
    }

});
