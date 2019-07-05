$(function () {
    var projectId = $("#project-id").val();

    $('#tab_plans').on('click', function () {
        atualizarPlan();
    });
    $("#add-plan").on('click', function () {
        $("#modal-title").html('Adicionar Plano <br><hr class="my-0">');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal_add_size").addClass('modal-lg');

        $("#modal-add-body").html("<div style='text-align:center;'>Carregando...</div>");
        $.ajax({
            method: "GET",
            url: '/plans/create',
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

                $(document).on('click', '.btnDelete', function (event) {
                    event.preventDefault();
                    $(this).parent().parent().remove();
                });

                //product
                $('#price').mask('#.###,#0', {reverse: true});
                var qtd_produtos = '1';

                var div_produtos = $('#produtos_div_' + qtd_produtos).parent().clone();

                $('#add_product_plan').on('click', function () {

                    qtd_produtos++;

                    var nova_div = div_produtos.clone();
                    var opt = nova_div.find('option:selected');
                    opt.remove();
                    var select = nova_div.find('select');
                    var input = nova_div.find('.products_amount');

                    // select.attr('id', 'product_' + qtd_produtos);
                    // select.attr('name', 'product_' + qtd_produtos);
                    // input.attr('name', 'products_amount_' + qtd_produtos);
                    input.addClass('products_amount');

                    div_produtos = nova_div;

                    $('#products').append('<div class="">' + nova_div.html() + '</div>');

                });

                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-plan'));
                    formData.append("project", projectId);

                    $.ajax({
                        method: "POST",
                        url: "/plans",
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
                            if (data.status == '422') {
                                for (error in data.responseJSON.errors) {
                                    alertCustom('error', String(data.responseJSON.errors[error]));
                                }
                            }
                        }, success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom("success", "Plano Adicionado!");
                            atualizarPlan();
                        }
                    });
                });
            }
        });

    });
    function atualizarPlan() {
        $("#data-table-plan").html("<tr class=''><td colspan='11'Carregando...></td></tr>");
        $.ajax({
            method: "GET",
            url: '/plans',
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                $("#data-table-plan").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#data-table-plan").html('');
                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += '<td class="shipping-id " style="vertical-align: middle;">' + value.name + '</td>';
                    // data += '<td class="shipping-type " style="vertical-align: middle;">' + value.description + '</td>';
                    data += '<td class="shipping-value " style="vertical-align: middle;">' + value.code + '</td>';
                    data += '<td class="shipping-zip-code-origin " style="vertical-align:">' + value.price + '</td>';
                    data += '<td class="shipping-status " style="vertical-align: middle;">';
                    if (value.status === 1) {
                        data += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        data += '<span class="badge badge-danger">Desativado</span>';
                    }

                    data += '</td>';

                    data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';

                    data += '</td>';

                    data += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger details-plan'  plan='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger edit-plan'  plan='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class=''><button class='btn btn-sm btn-outline btn-danger delete-plan'  plan='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";

                    data += '</tr>';
                    $("#data-table-plan").append(data);
                });

                if (response.data == '') {
                    $("#data-table-plan").html("<tr class=''><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
                }
                $(".details-plan").unbind('click');
                $('.details-plan').on('click', function () {
                    var plan = $(this).attr('plan');
                    $("#modal-title").html('Detalhes do Plano <br><hr>');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
                    var data = {planId: plan};
                    $("#btn-modal").hide();
                    $.ajax({
                        method: "GET",
                        url: "/plans/" + plan,
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
                $(".edit-plan").unbind('click');
                $(".edit-plan").on('click', function () {
                    $("#modal-add-body").html("");
                    var plan = $(this).attr('plan');
                    $("#modal-title").html("Editar Plano<br><hr>");
                    $("#modal_add_size").addClass('modal-lg');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {planId: plan};
                    $.ajax({
                        method: "GET",
                        url: "/plans/" + plan + "/edit",
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

                            $(document).on('click', '.btnDelete', function (event) {
                                event.preventDefault();
                                $(this).parent().parent().remove();
                            });

                            //product
                            $('#plan-price').mask('#.###,#0', {reverse: true});
                            var qtd_produtos = '1';

                            var div_produtos = $('#produtos_div_1').clone();

                            $('#add_product_plan').on('click', function () {

                                qtd_produtos++;

                                var nova_div = div_produtos.clone();
                                var opt = nova_div.find('option:selected');
                                opt.remove();
                                var select = nova_div.find('select');
                                var input = nova_div.find('.products_amount');

                                // select.attr('id', 'product_' + qtd_produtos);
                                // select.attr('name', 'product_' + qtd_produtos);
                                // input.attr('name', 'products_amount_' + qtd_produtos);
                                input.addClass('products_amount');

                                div_produtos = nova_div;

                                $('#products').append('<div class="row">' + nova_div.html() + '</div>');

                            });

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                var formData = new FormData(document.getElementById('form-update-plan'));
                                formData.append("project", projectId);
                                $.ajax({
                                    method: "POST",
                                    url: "/plans/" + plan,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:formData,
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
                                        alertCustom("success", "Plano atualizado com sucesso");
                                        atualizarPlan();
                                    }
                                });

                            });
                        }
                    });

                });

                $('.delete-plan').on('click', function (event) {
                    event.preventDefault();
                    var plan = $(this).attr('plan');
                    $("#modal_excluir_titulo").html("Remover Cupom?");
                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#fechar_modal_excluir").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/plans/" + plan,
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
                                alertCustom("success", "Plano Removido com sucesso");
                                atualizarPlan();
                            }

                        })
                    });

                });

            }
        });
    }
});
