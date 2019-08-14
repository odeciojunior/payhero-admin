$(function () {
    var projectId = $("#project-id").val();

    $('#tab_coupons').on('click', function () {
        atualizarCoupon();
    });
    atualizarCoupon();

    $("#add-coupon").on('click', function () {
        loadOnModal('#modal-add-body');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal-title").html('Novo cupom');

        $.ajax({
            method: "GET",
            url: '/couponsdiscounts/create',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                loadingOnScreenRemove();
                $("#modal-content").hide();

                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));

                }
            }, success: function success(data) {
                loadingOnScreenRemove();
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);
                if ($("#type").val() == 1) {
                    $("#valor_cupom_cadastrar").mask('#.###,#0', {reverse: true}).removeAttr('maxlength');
                } else {
                    $('#valor_cupom_cadastrar').mask('00%', {reverse: true});
                }

                $("#type").on('change', function () {
                    if ($("#type").val() == 1) {
                        $("#valor_cupom_cadastrar").mask('#.###,#0', {reverse: true}).removeAttr('maxlength');
                    } else {
                        $('#valor_cupom_cadastrar').mask('00%', {reverse: true});
                    }
                });

                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-coupon'));
                    formData.append("project", projectId);
                    loadingOnScreen();
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
                        error: function (_error) {
                            function error(_x) {
                                return _error.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error.toString();
                            };

                            return error;
                        }(function (response) {
                            loadingOnScreenRemove();
                            $("#modal_add_produto").hide();
                            $(".loading").css("visibility", "hidden");
                            if (response.status === 422) {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            } else {
                                alertCustom('error', String(response.responseJSON.message));

                            }
                        }), success: function success() {
                            loadingOnScreenRemove();
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
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#data-table-coupon', '#tabela-coupom');

        if (link == null) {
            link = '/couponsdiscounts?' + 'project=' + projectId;
        } else {
            link = '/couponsdiscounts' + link + '&project=' + projectId;
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function error(response) {
                $("#data-table-coupon").html(response.message);
            },
            success: function success(response) {
                $("#data-table-coupon").html('');

                if (response.data == '') {
                    $("#data-table-coupon").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td class="shipping-id">' + value.name + '</td>';
                        data += '<td class="shipping-type">' + value.type + '</td>';
                        data += '<td class="shipping-value">' + value.value + '</td>';
                        data += '<td class="shipping-zip-code-origin">' + value.code + '</td>';
                        data += '<td class="shipping-status" style="vertical-align: middle;">';
                        if (value.status === 1) {
                            data += '<span class="badge badge-success mr-10">Ativo</span>';
                        } else {
                            data += '<span class="badge badge-danger">Desativado</span>';
                        }

                        data += '</td>';

                        data += "<td style='min-width:200px;'>" + "<a role='button' class='details-coupon pointer mr-30' coupon='" + value.id + "' data-target='#modal-content' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i> </a>" + "<a role='button' class='edit-coupon pointer' coupon='" + value.id + "' data-target='#modal-content' data-toggle='modal'><i class='material-icons gradient'>edit</i> </a>" + "<a role='button' class='delete-coupon pointer ml-30' coupon='" + value.id + "' data-target='#modal-delete' data-toggle='modal'><i class='material-icons gradient'>delete_outline</i> </a>";
                        "</td>";
                        data += '</tr>';
                        $("#data-table-coupon").append(data);
                    });
                    pagination(response);
                }

                $(".details-coupon").unbind('click');
                $(".details-coupon").on('click', function () {
                    var coupon = $(this).attr('coupon');
                    $("#modal-title").html('Detalhes do Cupom');
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
                        error: function error() {
                            //
                        }, success: function success(response) {
                            $("#modal-add-body").html(response);
                        }
                    });
                });
                $(".edit-coupon").unbind('click');
                $(".edit-coupon").on('click', function () {
                    $("#modal-add-body").html("");
                    var coupon = $(this).attr('coupon');
                    $("#modal-title").html("Editar Cupom");
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");

                    var data = {couponId: coupon};

                    $.ajax({
                        method: "GET",
                        url: "/couponsdiscounts/" + coupon + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        }, success: function success(response) {
                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);
                            if ($("#type").val() == 1) {
                                $("#value").mask('#.###,#0', {reverse: true}).removeAttr('maxlength');
                            } else {
                                $('#value').mask('00%', {reverse: true});
                            }

                            $("#type").on('change', function () {
                                if ($("#type").val() == 1) {
                                    $("#value").mask('#.###,#0', {reverse: true}).removeAttr('maxlength');
                                } else {
                                    $('#value').mask('00%', {reverse: true});
                                }
                            });

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                var formData = new FormData(document.getElementById('form-update-coupon'));
                                formData.append("project", projectId);
                                loadingOnScreen();
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
                                    error: function (_error2) {
                                        function error(_x3) {
                                            return _error2.apply(this, arguments);
                                        }

                                        error.toString = function () {
                                            return _error2.toString();
                                        };

                                        return error;
                                    }(function (response) {
                                        loadingOnScreenRemove();
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    }),
                                    success: function success(data) {
                                        loadingOnScreenRemove();
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
                        loadingOnScreen();
                        $.ajax({
                            method: "DELETE",
                            url: "/couponsdiscounts/" + coupon,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (_error3) {
                                function error() {
                                    return _error3.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error3.toString();
                                };

                                return error;
                            }(function () {
                                loadingOnScreenRemove();
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                            }),
                            success: function success(data) {
                                loadingOnScreenRemove();
                                alertCustom("success", "Cupom Removido com sucesso");
                                atualizarCoupon();
                            }

                        });
                    });
                });
            }
        });
    }

    function pagination(response) {
        if (response.meta.last_page == 1) {
            $("#primeira_pagina_pixel").hide();
            $("#ultima_pagina_pixel").hide();
        } else {

            $("#pagination-coupons").html("");

            var primeira_pagina_pixel = "<button id='primeira_pagina_coupons' class='btn nav-btn'>1</button>";

            $("#pagination-coupons").append(primeira_pagina_pixel);

            if (response.meta.current_page == '1') {
                $("#primeira_pagina_coupons").attr('disabled', true);
                $("#primeira_pagina_coupons").addClass('nav-btn');
                $("#primeira_pagina_coupons").addClass('active');
            }

            $('#primeira_pagina_coupons').on("click", function () {
                atualizarCoupon('?page=1');
            });

            for (x = 3; x > 0; x--) {

                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-coupons").append("<button id='pagina_coupons_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

                $('#pagina_coupons_' + (response.meta.current_page - x)).on("click", function () {
                    atualizarCoupon('?page=' + $(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var pagina_atual_coupons = "<button id='pagina_atual_coupons' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-coupons").append(pagina_atual_coupons);

                $("#pagina_atual_coupons").attr('disabled', true);
                $("#pagina_atual_coupons").addClass('nav-btn');
                $("#pagina_atual_coupons").addClass('active');
            }
            for (x = 1; x < 4; x++) {

                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-coupons").append("<button id='pagina_coupons_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

                $('#pagina_coupons_' + (response.meta.current_page + x)).on("click", function () {
                    atualizarCoupon('?page=' + $(this).html());
                });
            }

            if (response.meta.last_page != '1') {
                var ultima_pagina_coupons = "<button id='ultima_pagina_coupons' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-coupons").append(ultima_pagina_coupons);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#ultima_pagina_coupons").attr('disabled', true);
                    $("#ultima_pagina_coupons").addClass('nav-btn');
                    $("#ultima_pagina_coupons").addClass('active');
                }

                $('#ultima_pagina_coupons').on("click", function () {
                    atualizarCoupon('?page=' + response.meta.last_page);
                });
            }
        }
    }
});
