let statusCupons = {
    1: "success",
    0: "danger",
};

$(function () {

    let projectId = $(window.location.pathname.split('/')).get(-1);

    //comportamento da tela
    $('.coupon-value').mask('00%', {reverse: true});
    $(document).on('change', '.coupon-type', function () {
        if ($(this).val() == 1) {
            $(".coupon-value").mask('#.##0,00', {reverse: true}).removeAttr('maxlength');
        } else {
            $('.coupon-value').mask('00%', {reverse: true});
        }
    });

    $('#tab_coupons').on('click', function () {
        atualizarCoupon();
    });

    //carrega os itens na tabela
    atualizarCoupon();

    // carregar modal de detalhes
    $(document).on('click', '.details-coupon', function () {
        let coupon = $(this).attr('coupon');
        $("#btn-modal").hide();
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId +"/couponsdiscounts/" + coupon,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom('error', 'Erro ao exibir detalhes do cupom');
            }, success: function success(response) {
                $('#modal-detail-coupon .coupon-name').html(response.name);
                $('#modal-detail-coupon .coupon-code').html(response.code);
                $('#modal-detail-coupon .coupon-type').html(response.type == 1 ? 'Valor' : 'Porcentagem');
                $('#modal-detail-coupon .coupon-value').html(response.value);
                $('#modal-detail-coupon .coupon-status').html(response.status == 1
                    ? '<span class="badge badge-success text-left">Ativo</span>'
                    : '<span class="badge badge-danger">Desativado</span>');
                $('#modal-detail-coupon').modal('show');

            }
        });
    });

    // carregar modal de edicao
    $(document).on('click', '.edit-coupon', function () {
        let coupon = $(this).attr('coupon');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId +"/couponsdiscounts/" + coupon + "/edit",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom('error', 'Erro ao tentar editar cupom')
            }, success: function success(response) {
                $('#modal-edit-coupon .coupon-id').val(coupon);
                $('#modal-edit-coupon .coupon-name').val(response.name);
                $('#modal-edit-coupon .coupon-value').val(response.value);
                if (response.type == 1) {
                    $('#modal-edit-coupon .coupon-type').prop("selectedIndex", 1).change();
                } else {
                    $('#modal-edit-coupon .coupon-type').prop("selectedIndex", 0).change();
                }

                $('#modal-edit-coupon .coupon-code').val(response.code);

                if (response.status == 1) {
                    $('#modal-edit-coupon .coupon-status').prop("selectedIndex", 0).change();
                } else {
                    $('#modal-edit-coupon .coupon-status').prop("selectedIndex", 1).change();
                }
                $('#modal-edit-coupon').modal('show');
            }
        });
    });

    // carregar modal delecao
    $(document).on('click', '.delete-coupon', function (event) {
        let coupon = $(this).attr('coupon');
        $('#modal-delete-coupon .btn-delete').attr('coupon', coupon);
        $("#modal-delete-coupon").modal('show');
    });

    //cria novo cupom
    $('#modal-create-coupon .btn-save').on('click', function () {
        let formData = new FormData(document.getElementById('form-register-coupon'));
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts",
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

    //atualizar cupom
    $("#modal-edit-coupon .btn-update").on('click', function () {
        let formData = new FormData(document.getElementById('form-update-coupon'));
        let coupon = $('#modal-edit-coupon .coupon-id').val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
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

    //deletar cupom
    $('#modal-delete-coupon .btn-delete').on('click',  function () {
        let coupon = $(this).attr('coupon');
        loadingOnScreen();
        $.ajax({
            method: "DELETE",
            url: "/api/couponsdiscounts/" + coupon,
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

    function atualizarCoupon() {
        loadOnTable('#data-table-coupon', '#tabela-coupom');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/couponsdiscounts",
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
                        let data = `<tr>
                                        <td class="shipping-id">${value.name}</td>
                                        <td class="shipping-type">${value.type}</td>
                                        <td class="shipping-value">${value.value}</td>
                                        <td class="shipping-zip-code-origin">${value.code}</td>
                                        <td class="shipping-status" style="vertical-align: middle">
                                            <span class="badge badge-${statusCupons[value.status]}">${value.status_translated}</span>
                                        </td>
                                        <td style="text-align:center">
                                            <a role="button" class="mg-responsive details-coupon pointer" coupon="${value.id}"><i class="material-icons gradient">remove_red_eye</i></a>
                                            <a role="button" class="mg-responsive edit-coupon pointer" coupon="${value.id}"><i class="material-icons gradient">edit</i> </a>
                                            <a role="button" class="mg-responsive delete-coupon pointer" coupon="${value.id}"><i class="material-icons gradient">delete_outline</i></a>
                                        </td>
                                    </tr>`;

                        $("#data-table-coupon").append(data);
                    });
                    pagination(response, 'coupons', atualizarCoupon);
                }
            }
        });
    }

    function pagination(response) {
        if (response.meta.last_page == 1) {
            $("#primeira_pagina_pixel").hide();
            $("#ultima_pagina_pixel").hide();
        } else {

            $("#pagination-coupons").html("");

            let primeira_pagina_pixel = "<button id='primeira_pagina_coupons' class='btn nav-btn'>1</button>";

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
                let pagina_atual_coupons = "<button id='pagina_atual_coupons' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

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
                let ultima_pagina_coupons = "<button id='ultima_pagina_coupons' class='btn nav-btn'>" + response.meta.last_page + "</button>";

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
