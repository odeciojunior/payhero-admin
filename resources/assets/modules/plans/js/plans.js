$(function () {
    var projectId = $("#project-id").val();

    $('#tab_plans').on('click', function () {
        updatePlan();
    });

    /**
     *  Verifica se a array de objetos que retorna do ajax esta vazio
     * @returns {boolean}
     * @param data
     */

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    /**
     * Add new Plan
     */
    $("#add-plan").on('click', function () {

        $.ajax({
            method: "GET",
            url: '/plans/create',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                loadingOnScreenRemove();
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            }, success: function (data) {
                console.log(data);
                if (data.message === 'error') {
                    $("#modal-plans-error").modal('show');
                } else {
                    loadingOnScreenRemove();
                    $("#btn-modal").addClass('btn-save');
                    $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
                    $("#btn-modal").show();
                    console.log(data);
                    $('#modal-add-body').html(data.data['view']);
                    $("#modal-content").modal('show');

                    $("#modal_add_size").addClass('modal_simples');
                    $("#modal-title").html('Adicionar Plano');

                    $('.products_amount').mask('0#');

                    $(document).on('click', '.btnDelete', function (event) {
                        event.preventDefault();
                        $(this).parent().parent().remove();
                    });

                    //product
                    $('#price').mask('#.###,#0', {reverse: true});
                    var qtd_products = '1';

                    var div_products = $('#products_div_' + qtd_products).parent().clone();

                    /**
                     * Add new product in array
                     */
                    $('#add_product_plan').on('click', function () {

                        qtd_products++;

                        var new_div = div_products.clone();
                        var opt = new_div.find('option:selected');
                        opt.remove();
                        var select = new_div.find('select');
                        var input = new_div.find('.products_amount');

                        input.addClass('products_amount');

                        div_products = new_div;

                        $('#products').append('<div class="">' + new_div.html() + '</div>');
                        $('.products_amount').mask('0#');

                    });

                    /**
                     * Save new Plan
                     */
                    $(".btn-save").unbind('click');
                    $(".btn-save").on('click', function () {

                        var formData = new FormData(document.getElementById('form-register-plan'));
                        formData.append("project", projectId);
                        loadingOnScreen();
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
                                loadingOnScreenRemove();
                                $("#modal_add_produto").hide();
                                $(".loading").css("visibility", "hidden");
                                if (data.status == '422') {
                                    for (error in data.responseJSON.errors) {
                                        alertCustom('error', String(data.responseJSON.errors[error]));
                                    }
                                }
                            }, success: function () {
                                loadingOnScreenRemove();
                                $(".loading").css("visibility", "hidden");
                                alertCustom("success", "Plano Adicionado!");
                                updatePlan();
                            }
                        });
                    });
                }

            }
        });

    });

    /**
     * Update Table Plan
     */
    function updatePlan(link = null) {
        loadOnTable('#data-table-plan', '#table-plans');
        $("#data-table-plan").html("<tr class=''><td colspan='11'>Carregando...</td></tr>");

        if (link == null) {
            link = '/plans';
        } else {
            link = '/plans' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                $("#data-table-plan").html('Erro ao encontrar dados');
                if (response.status == '422') {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {

                    alertCustom('error', response.responseJSON.message);

                }
            },
            success: function (response) {

                if (isEmpty(response.data)) {
                    $("#data-table-plan").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
                } else {

                    $("#data-table-plan").html('');
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td class="shipping-id " style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td class="shipping-type " style="vertical-align: middle;">' + value.description + '</td>';
                        data += '<td class="shipping-value " style="vertical-align: middle;">' + value.code + '</td>';
                        data += '<td class="shipping-zip-code-origin " style="vertical-align: middle;">' + value.price + '</td>';
                        data += '<td class="shipping-status">';
                        if (value.status === 1) {
                            data += '<span class="badge badge-success mr-10">Ativo</span>';
                        } else {
                            data += '<span class="badge badge-primary">Desativado</span>';
                        }

                        data += '</td>';

                        data += "<td style='min-width:200px;'>" +
                            "<a class='pointer details-plan mr-30' plan='" + value.id + "'  role='button'><img src='/modules/global/img/svg/eye.svg' style='width: 24px'></a>" +
                            "<a class='pointer edit-plan' plan='" + value.id + "' data-target='#modal-content' data-toggle='modal' role='button'><img src='/modules/global/img/svg/edit.svg' style='width: 24px'></a>" +
                            "<a class='pointer delete-plan ml-30' plan='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' role='button'><i class='material-icons gradient'>delete_outline</i></a>"
                        "</td>";

                        data += '</tr>';

                        $("#data-table-plan").append(data);
                    });

                    pagination(response, 'plans')
                }

                /**
                 * Details Plan
                 */
                $(".details-plan").unbind('click');
                $('.details-plan').on('click', function () {
                    var plan = $(this).attr('plan');
                    var data = {planId: plan, project: projectId};

                    $.ajax({
                        method: "GET",
                        url: "/plans/" + plan,
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function (response) {
                            if (response.status == '422') {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            } else {
                                alertCustom("error", response.responseJSON.message)
                            }
                            loadingOnScreenRemove();
                        }, success: function (response) {
                            if (response.message == 'error') {
                                alertCustom('error', 'Ocorreu um erro ao tentar buscar dados plano!');
                            } else {
                                $("#modal-title").html('Detalhes do Plano <br><hr>');
                                $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
                                $("#btn-modal").hide();

                                $("#modal-add-body").html(response.data['view']);
                                $("#modal-content").modal('show');

                            }

                        }
                    });
                });

                /**
                 * Edit Plan
                 */
                $(".edit-plan").unbind('click');
                $(".edit-plan").on('click', function () {
                    $("#modal-add-body").html("");
                    var plan = $(this).attr('plan');
                    $("#modal-title").html("Editar Plano<br><hr>");
                    // $("#modal_add_size").addClass('modal-lg');
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
                            $('.products_amount').mask('0#');

                            $(document).on('click', '.btnDelete', function (event) {
                                event.preventDefault();
                                $(this).parent().parent().remove();
                            });

                            //product
                            $('#plan-price').mask('#.###,#0', {reverse: true});
                            var qtd_products = '1';

                            var div_products = $('#products_div_1').clone();

                            $('#add_product_plan').on('click', function () {

                                qtd_products++;

                                var new_div = div_products.clone();
                                var opt = new_div.find('option:selected');
                                opt.remove();
                                var select = new_div.find('select');
                                var input = new_div.find('.products_amount');

                                input.addClass('products_amount');

                                div_products = new_div;

                                $('#products').append('<div class="row">' + new_div.html() + '</div>');
                                $('.products_amount').mask('0#');

                            });

                            /**
                             * Update Plan
                             */
                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                var formData = new FormData(document.getElementById('form-update-plan'));
                                formData.append("project", projectId);
                                loadingOnScreen();
                                $.ajax({
                                    method: "POST",
                                    url: "/plans/" + plan,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    error: function (response) {
                                        loadingOnScreenRemove();
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                        updatePlan();
                                    },
                                    success: function (data) {
                                        loadingOnScreenRemove();
                                        alertCustom("success", "Plano atualizado com sucesso");
                                        updatePlan();
                                    }
                                });

                            });
                        }
                    });

                });

                /**
                 * Delete Plan
                 */
                $('.delete-plan').on('click', function (event) {
                    event.preventDefault();
                    var plan = $(this).attr('plan');
                    $("#modal_excluir_titulo").html("Remover Cupom?");
                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#fechar_modal_excluir").click();
                        loadingOnScreen();
                        $.ajax({
                            method: "DELETE",
                            url: "/plans/" + plan,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (response) {
                                loadingOnScreenRemove();
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                                if (response.status == '400') {
                                    alertCustom('error', response.responseJSON.message);
                                }
                            },
                            success: function (response) {
                                loadingOnScreenRemove();
                                alertCustom('success', response.message);
                                updatePlan();
                            }

                        });
                    });

                });

            }
        });
    }

    /**
     *  Pagination
     * @param response
     * @param model
     */
    function pagination(response, model) {
        if (response.meta.last_page == 1) {
            $("#primeira_pagina_" + model).hide();
            $("#ultima_pagina_" + model).hide();
        } else {
            $("#pagination-" + model).html("");

            var first_page = "<button id='first_page' class='btn nav-btn'>1</button>";

            $("#pagination-" + model).append(first_page);

            if (response.meta.current_page == '1') {
                $("#first_page").attr('disabled', true).addClass('nav-btn').addClass('active');
            }

            $('#first_page').on("click", function () {
                updatePlan('?page=1');
            });

            for (x = 3; x > 0; x--) {

                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

                $('#page_' + (response.meta.current_page - x)).on("click", function () {
                    updatePlan('?page=' + $(this).html());
                });

            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page = "<button id='current_page' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page").attr('disabled', true).addClass('nav-btn').addClass('active');

            }
            for (x = 1; x < 4; x++) {

                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

                $('#page_' + (response.meta.current_page + x)).on("click", function () {
                    updatePlan('?page=' + $(this).html());
                });

            }

            if (response.meta.last_page != '1') {
                var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page").attr('disabled', true).addClass('nav-btn').addClass('active');
                }

                $('#last_page').on("click", function () {
                    updatePlan('?page=' + response.meta.last_page);
                });
            }
        }

    };
});
