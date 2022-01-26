let statusCupons = {
    1: "success",
    0: "danger",
};

$(function () {

    let projectId = $(window.location.pathname.split('/')).get(-1);

    //comportamento da tela
    var cuponType = 0;
    $('.coupon-value').mask('00%', {reverse: true});
    $(document).on('change', '.coupon-type', function () {
        if ($(this).val() == 1) {
            cuponType = 1;
            $(".coupon-value").mask('#.##0,00', {reverse: true}).removeAttr('maxlength');
        } else {
            cuponType = 0;
            $('.coupon-value').mask('00%', {reverse: true});
        }
    });
    $(".rule-value").mask('#.##0,00', {reverse: true}).removeAttr('maxlength');

    $('.rule-value').on('blur', function () {
        applyMaskManually(this);
    });

    $('.coupon-value').on('blur', function () {
        if(cuponType==1){
            applyMaskManually(this);
        }
    });

    function applyMaskManually(classValue){
        if ($(classValue).val().length == 1) {
            let val = '0,0' + $(classValue).val();
            $(classValue).val(val);
        } else if ($(classValue).val().length == 2) {
            let val = '0,' + $(classValue).val();
            $(classValue).val(val);
        }
    }

    $('.tab_coupons').on('click', function () {
        atualizarCoupon();
        $(this).off();
    });

    // carregar modal de detalhes
    $(document).on('click', '.details-coupon', function () {
        let coupon = $(this).attr('coupon');
        $("#btn-modal").hide();
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $('#modal-detail-coupon .coupon-name').html(response.data.name);
                $('#modal-detail-coupon .coupon-code').html(response.data.code);
                $('#modal-detail-coupon .coupon-type').html(response.data.type);
                $('#modal-detail-coupon .coupon-value').html(response.data.type == 'Valor' ? 'R$ ' + response.data.value : response.data.value + '%');
                $('#modal-detail-coupon .rule-value').html('R$ ' + response.data.rule_value);
                $('#modal-detail-coupon .coupon-status').html(response.data.status == '1'
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
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $('#modal-edit-coupon .coupon-id').val(coupon);
                $('#modal-edit-coupon .coupon-name').val(response.name);
                $('#modal-edit-coupon .coupon-value').val(response.value);
                $('#modal-edit-coupon .rule-value').val(response.rule_value);
                $('#modal-edit-coupon .rule-value').trigger('input');
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

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {

                errorAjaxResponse(response);
            },
            success: function success() {

                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Cupom Adicionado!");
                atualizarCoupon();
                clearFields();
            }
        });
    });

    //atualizar cupom
    $("#modal-edit-coupon .btn-update").on('click', function () {
        let formData = new FormData(document.getElementById('form-update-coupon'));
        let coupon = $('#modal-edit-coupon .coupon-id').val();

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                if (response.status === 400) {
                    atualizarCoupon();
                }

                errorAjaxResponse(response);
            },
            success: function success(data) {

                alertCustom("success", data.message);
                atualizarCoupon();
            }
        });
    });

    //deletar cupom
    $('#modal-delete-coupon .btn-delete').on('click', function () {
        let coupon = $(this).attr('coupon');

        $.ajax({
            method: "DELETE",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(data) {

                alertCustom("success", "Cupom Removido com sucesso");
                atualizarCoupon();
            }

        });
    });

    function atualizarCoupon() {

        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/project/' + projectId + '/couponsdiscounts';
        } else {
            link = '/api/project/' + projectId + '/couponsdiscounts' + link;
        }

        loadOnTable('#data-table-coupon', '#tabela-coupom');
        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#data-table-coupon").html('');

                if (response.data == '') {
                    $("#data-table-coupon").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $('#count-coupons').html(response.meta.total)
                    $.each(response.data, function (index, value) {
                        let data = `<tr>
                            <td class="shipping-id">${value.name}</td>
                            <td class="shipping-type">${value.type}</td>
                            <td class="shipping-value">${value.value}</td>
                            <td class="shipping-zip-code-origin">${value.code}</td>
                            <td class="shipping-status text-center" style="vertical-align: middle">
                                <span class="badge badge-${statusCupons[value.status]}">${value.status_translated}</span>
                            </td>
                            <td style="text-align:center">
                                <a role="button" title='Visualizar' class="mg-responsive details-coupon pointer" coupon="${value.id}"><span class="o-eye-1"></span></a>
                                <a role="button" title='Editar' class="mg-responsive edit-coupon pointer" coupon="${value.id}"><span class="o-edit-1"></span> </a>
                                <a role="button" title='Excluir' class="mg-responsive delete-coupon pointer" coupon="${value.id}"><span class='o-bin-1'></span></a>
                            </td>
                        </tr>`;

                        $("#data-table-coupon").append(data);
                    });
                    pagination(response, 'coupons', atualizarCoupon);
                }
            }
        });
    }

    //Limpa campos
    function clearFields() {
        $('.coupon-name').val('');
        $('.coupon-value').val('');
        $('.coupon-code').val('');
        $('.rule-value').val('');

    }
});
