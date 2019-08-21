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
            data: {project: projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                loadingOnScreenRemove();
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            }, success: function success(data) {
                if (data.message === 'error') {
                    $("#modal-plans-error").modal('show');
                } else {
                    loadingOnScreenRemove();
                    $("#btn-modal").addClass('btn-save');
                    $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
                    $("#btn-modal").show();
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
                        // var opt = new_div.find('option:selected');
                        // opt.remove();
                        // var select = new_div.find('select');
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
                        var hasNoValue;
                        $('.products_amount').each(function() {
                            if ($(this).val() == '' || $(this).val() == 0) {
                                hasNoValue = true;
                            }
                        });
                        if (hasNoValue) {
                            alertCustom('error', 'Dados informados inválidos');
                            return false;
                        }

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
                            error: function (_error) {
                                function error(_x) {
                                    return _error.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error.toString();
                                };

                                return error;
                            }(function (data) {
                                loadingOnScreenRemove();
                                $("#modal_add_produto").hide();
                                $(".loading").css("visibility", "hidden");
                                if (data.status == '400') {
                                    alertCustom('error', response.responseJSON.message); //'Ocorreu algum erro'
                                }
                                if (data.status == '422') {
                                    for (error in data.responseJSON.errors) {
                                        alertCustom('error', String(data.responseJSON.errors[error]));
                                    }
                                }
                            }), success: function success() {
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
    function updatePlan() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#data-table-plan', '#table-plans');

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
            error: function (_error2) {
                function error() {
                    return _error2.apply(this, arguments);
                }

                error.toString = function () {
                    return _error2.toString();
                };

                return error;
            }(function () {
                $("#data-table-plan").html('Erro ao encontrar dados');
                if (response.status == '422') {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {

                    alertCustom('error', response.responseJSON.message);
                }
            }),
            success: function success(response) {

                if (isEmpty(response.data)) {
                    $("#data-table-plan").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {

                    $("#data-table-plan").html('');
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td id=""     class=""                                                     style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td id=""     class=""                                                     style="vertical-align: middle;">' + value.description + '</td>';
                        data += '<td id="link" class="display-sm-none display-m-none"                       style="vertical-align: middle;">' + value.code + '</td>';
                        data += '<td id=""     class="display-lg-none display-xlg-none"                     style="vertical-align: middle;"><a class="material-icons pointer gradient" onclick="copyToClipboard(\'#link\')"> file_copy</a></td>';
                        data += '<td id=""     class=""                                                     style="vertical-align: middle;">' + value.price + '</td>';
                        data += '<td id=""     class="">';
                        if (value.status === 1) {
                            data += '<span class="badge badge-success mr-10">Ativo</span>';
                        } else {
                            data += '<span class="badge badge-danger">Desativado</span>';
                        }

                        data += '</td>';
                        data += "<td style='text-align:center' class='mg-responsive'>"
                        data += "<a class='mg-responsive pointer details-plan'    plan='" + value.id + "'  role='button'                                                 ><i class='material-icons gradient'>remove_red_eye</i></a>"
                        data += "<a class='mg-responsive pointer edit-plan'                     plan='" + value.id + "'  role='button'data-toggle='modal' data-target='#modal-content' ><i class='material-icons gradient'>edit</i></a>"
                        data += "<a class='mg-responsive pointer delete-plan'     plan='" + value.id + "'  role='button'data-toggle='modal' data-target='#modal-delete'  ><i class='material-icons gradient'>delete_outline</i></a>";
                        data += "</td>";
                        data += '</tr>';

                        $("#data-table-plan").append(data);
                        $('#table-plans').addClass('table-striped');
                    });

                    pagination(response, 'plans', updatePlan);
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
                        error: function (_error3) {
                            function error(_x3) {
                                return _error3.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error3.toString();
                            };

                            return error;
                        }(function (response) {
                            if (response.status == '422') {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            } else {
                                alertCustom("error", response.responseJSON.message);
                            }
                            loadingOnScreenRemove();
                        }), success: function success(response) {
                            if (response.message == 'error') {
                                alertCustom('error', 'Ocorreu um erro ao tentar buscar dados plano!');
                            } else {
                                $("#modal-title").html('Detalhes do Plano <br>');
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
                    // $("#modal_add_size").addClass('modal-lg');
                    loadOnModal('#modal-add-body');
                    $("#modal-add-body").html("");
                    var plan = $(this).attr('plan');
                    $("#modal-title").html("Editar Plano");
                    // $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {planId: plan, project: projectId};

                    $.ajax({
                        method: "GET",
                        url: "/plans/" + plan + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                            loadingOnScreenRemove()
                        }, success: function success(response) {
                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);
                            $('.products_amount').mask('0#');
                            loadingOnScreenRemove()

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
                                // var opt = new_div.find('option:selected');
                                // opt.remove();
                                // var select = new_div.find('select');
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
                                var hasNoValue;
                                $('.products_amount').each(function() {
                                    if ($(this).val() == '' || $(this).val() == 0) {
                                        hasNoValue = true;
                                    }
                                });
                                if (hasNoValue) {
                                    alertCustom('error', 'Dados informados inválidos');
                                    return false;
                                }

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
                                    error: function (_error4) {
                                        function error(_x4) {
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
                                        updatePlan();
                                    }),
                                    success: function success(data) {
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
                            error: function (_error5) {
                                function error(_x5) {
                                    return _error5.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error5.toString();
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
                            success: function success(response) {
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
});
