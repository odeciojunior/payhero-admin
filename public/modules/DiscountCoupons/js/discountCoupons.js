$(function () {
    var projectId = $("#project-id").val();

    $('#tab_coupons').on('click', function () {
        atualizarCoupon();
    });
    atualizarCoupon();

    $("#add-coupon").on('click', function () {
        $("#modal-title").html('Adicionar Cupom <br><hr class="my-0">');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal_add_size").removeClass('modal-lg');

        $("#modal-add-body").html("<div style='text-align:center;'>Carregando...</div>");

        $.ajax({
            method: "GET",
            url: '/couponsdiscounts/create',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            }, success: function (data) {
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);
                if ($("#tipo_cupom").val() == 1) {
                    $("#valor_cupom_cadastrar").mask('#.###,#0', {reverse: true});

                } else {
                    $('#valor_cupom_cadastrar').mask('##0,00%', {reverse: true});

                }

                $("#tipo_cupom").on('change', function () {
                    if ($("#tipo_cupom").val() == 1) {
                        $("#valor_cupom_cadastrar").mask('#.###,#0', {reverse: true});

                    } else {
                        $('#valor_cupom_cadastrar').mask('##0,00%', {reverse: true});

                    }
                });

                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-coupon'));
                    formData.append("project", projectId);

                    $.ajax({
                        method: "POST",
                        url: "/couponsdiscounts",
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
                            alertCustom("success", "Cupom Adicionado!");
                            atualizarCoupon();
                        }
                    });
                });
            }
        });

    });
    function atualizarCoupon() {
        $("#data-table-coupon").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");
        $.ajax({
            method: "GET",
            url: '/couponsdiscounts',
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                $("#data-table-coupon").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#data-table-coupon").html('');

                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += '<td class="shipping-id" style="vertical-align: middle;">' + value.name + '</td>';
                    data += '<td class="shipping-type" style="vertical-align: middle;">' + value.type + '</td>';
                    data += '<td class="shipping-value" style="vertical-align: middle;">' + value.value + '</td>';
                    data += '<td class="shipping-zip-code-origin" style="vertical-align:">' + value.code + '</td>';
                    data += '<td class="shipping-status" style="vertical-align: middle;">';
                    if (value.status === 1) {
                        data += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        data += '<span class="badge badge-danger">Desativado</span>';
                    }

                    data += '</td>';

                    data += '<td class="shipping-pre-selected text-center" style="vertical-align: middle;">';

                    data += '</td>';

                    data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger details-coupon'  coupon='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger edit-coupon'  coupon='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger delete-coupon'  coupon='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";

                    data += '</tr>';
                    $("#data-table-coupon").append(data);
                });
                if (response.data == '') {
                    $("#data-table-coupon").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
                }
                $(".details-coupon").unbind('click');
                $(".details-coupon").on('click', function () {
                    var coupon = $(this).attr('coupon');
                    $("#modal-title").html('Detalhes do Cupom <br><hr>');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");

                    var data = {couponId: coupon};
                    $("#btn-modal").hide();
                    $.ajax({
                        method: "GET",
                        url: "/couponsdiscounts/" + coupon,
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
                $(".edit-coupon").unbind('click');
                $(".edit-coupon").on('click', function () {
                    $("#modal-add-body").html("");
                    var coupon = $(this).attr('coupon');
                    $("#modal-title").html("Editar Cupom<br><hr>");
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");

                    var data = {couponId: coupon};

                    $.ajax({
                        method: "GET",
                        url: "/couponsdiscounts/" + coupon + "/edit",
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
                            if ($("#type").val() == 1) {
                                $("#value").mask('#.###,#0', {reverse: true});

                            } else {
                                $('#value').mask('##0,00%', {reverse: true});

                            }

                            $("#type").on('change', function () {
                                if ($("#type").val() == 1) {
                                    $("#value").mask('#.###,#0', {reverse: true});

                                } else {
                                    $('#value').mask('##0,00%', {reverse: true});

                                }
                            });

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                var formData = new FormData(document.getElementById('form-update-coupon'));
                                formData.append("project", projectId);
                                $.ajax({
                                    method: "POST",
                                    url: "/couponsdiscounts/" + coupon,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    error: function () {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    },
                                    success: function (data) {
                                        alertCustom("success", "Cupom atualizado com sucesso");
                                        atualizarCoupon();
                                    }
                                });

                            });
                        }
                    });

                });
                $('.delete-coupon').on('click', function (event) {
                    event.preventDefault();
                    var coupon = $(this).attr('coupon');
                    $("#modal_excluir_titulo").html("Remover Cupom?");
                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#fechar_modal_excluir").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/couponsdiscounts/" + coupon,
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
                                alertCustom("success", "Cupom Removido com sucesso");
                                atualizarCoupon();
                            }

                        })
                    });

                });

            }
        });
    }
});
