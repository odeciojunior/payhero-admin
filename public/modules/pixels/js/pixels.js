let statusPixel = {
    1: "success",
    0: "danger",
};

$(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    //comportamentos da tela
    $('#tab_pixels').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarPixel();
    });

    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    if ($(':checkbox').is(':checked')) {
        $(':checkbox').val(1);
    } else {
        $(':checkbox').val(0);
    }

    $("#select-platform").change(function () {
        let value = $(this).val();

        if (value === 'facebook') {
            $("#input-code-pixel").html('').hide();
            $("#code-pixel").attr("placeholder", '52342343245553');
        } else if (value === 'google_adwords') {
            $("#input-code-pixel").html('AW-').show();
            $("#code-pixel").attr("placeholder", '8981445741-4');
        } else if (value === 'google_analytics') {
            $("#input-code-pixel").html('UA-').show();
            $("#code-pixel").attr("placeholder", '8984567741-3');
        } else {
            $("#input-code-pixel").html('').hide();
            $("#code-pixel").attr("placeholder", 'Código');
        }

    });

    //carrega os itens na tabela
    atualizarPixel();

    // carregar modal de detalhes
    $(document).on('click', '.details-pixel', function () {
        let pixel = $(this).attr('pixel');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error() {
                errorAjaxResponse(response);

            }, success: function success(response) {
                renderDetailPixel(response);
            }
        });
    });

    function renderDetailPixel(pixel) {
        $('#modal-detail-pixel .pixel-description').html(pixel.name);
        $('#modal-detail-pixel .pixel-code').html(pixel.code);
        $('#modal-detail-pixel .pixel-platform').html(pixel.platform);
        $('#modal-detail-pixel .pixel-status').html(pixel.status == 1
            ? '<span class="badge badge-success text-left">Ativo</span>'
            : '<span class="badge badge-danger">Desativado</span>');
        $('#modal-detail-pixel').modal('show');
    }

    // carregar modal de edicao
    $(document).on('click', '.edit-pixel', function () {
        let pixel = $(this).attr('pixel');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/pixels/" + pixel + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            }, success: function success(response) {
                renderEditPixel(response);
                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });

                $("#select-platform").change(function () {
                    let value = $(this).val();

                    if (value === 'facebook') {
                        $("#code-pixel-edit").html('').hide();
                        $("#code-pixel").attr("placeholder", '52342343245553');
                    } else if (value === 'google_adwords') {
                        $("#code-pixel-edit").html('AW-').show();
                        $("#code-pixel").attr("placeholder", '8981445741-4');
                    } else if (value === 'google_analytics') {
                        $("#code-pixel-edit").html('UA-').show();
                        $("#code-pixel").attr("placeholder", '8984567741-3');
                    } else {
                        $("#code-pixel-edit").html('').hide();
                        $("#code-pixel").attr("placeholder", 'Código');
                    }

                });
            }
        });
    });

    function renderEditPixel(pixel) {
        $('#modal-edit-pixel .pixel-id').val(pixel.id_code);
        $('#modal-edit-pixel .pixel-description').val(pixel.name);

        if (pixel.platform == 'facebook') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 0).change();
        }
        if (pixel.platform == 'google_adwords') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 1).change();
        }
        if (pixel.platform == 'google_analytics') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 2).change();
        }
        if (pixel.platform == 'taboola') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 3).change();
        }
        if (pixel.platform == 'outbrain') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 4).change();
        }

        if (pixel.status == '1') { //Ativo
            $('#modal-edit-pixel .pixel-status').prop("selectedIndex", 0).change();
        } else {//Desativado
            $('#modal-edit-pixel .pixel-status').prop("selectedIndex", 1).change();
        }
        $('#modal-edit-pixel .pixel-code').val(pixel.code);
        if (pixel.checkout == '1') {
            $('#modal-edit-pixel .pixel-checkout').val(1).prop('checked', true);
        } else {
            $('#modal-edit-pixel .pixel-checkout').val(0).prop('checked', false);
        }
        if (pixel.purchase_card == '1') {
            $('#modal-edit-pixel .pixel-purchase-card').val(1).prop('checked', true);
        } else {
            $('#modal-edit-pixel .pixel-purchase-card').val(0).prop('checked', false);
        }
        if (pixel.purchase_boleto == '1') {
            $('#modal-edit-pixel .pixel-purchase-boleto').val(1).prop('checked', true);
        } else {
            $('#modal-edit-pixel .pixel-purchase-boleto').val(0).prop('checked', false);
        }
        $('#modal-edit-pixel').modal('show');
    }

    //carregar modal delecao
    $(document).on('click', '.delete-pixel', function (event) {
        let pixel = $(this).attr('pixel');
        $("#modal-delete-pixel .btn-delete").attr("pixel", pixel);
        $("#modal-delete-pixel").modal('show');
    });

    //criar novo pixel
    $("#modal-create-pixel .btn-save").on('click', function () {
        let formData = new FormData(document.querySelector('#modal-create-pixel  #form-register-pixel'));
        formData.append('checkout', $("#modal-create-pixel .pixel-checkout").val());
        formData.append('purchase_card', $("#modal-create-pixel .pixel-purchase-card").val());
        formData.append('purchase_boleto', $("#modal-create-pixel .pixel-purchase-boleto").val());

        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/pixels",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
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
                errorAjaxResponse(response);

            }), success: function success() {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Pixel Adicionado!");
                atualizarPixel();
                clearFields();
            }
        });
    });

    //atualizar pixel
    $(document).on('click', '#modal-edit-pixel .btn-update', function () {
        loadingOnScreen();
        let pixel = $('#modal-edit-pixel .pixel-id').val();
        $.ajax({
            method: "PUT",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                name: $("#modal-edit-pixel .pixel-description").val(),
                code: $("#modal-edit-pixel .pixel-code").val(),
                platform: $("#modal-edit-pixel .pixel-platform").val(),
                status: $("#modal-edit-pixel .pixel-status").val(),
                checkout: $("#modal-edit-pixel .pixel-checkout").val(),
                purchase_card: $("#modal-edit-pixel .pixel-purchase-card").val(),
                purchase_boleto: $("#modal-edit-pixel .pixel-purchase-boleto").val()
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            },
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Pixel atualizado com sucesso");
                atualizarPixel();
            }
        });
    });

    // deletar pixel
    $(document).on('click', '#modal-delete-pixel .btn-delete', function () {
        loadingOnScreen();
        let pixel = $(this).attr('pixel');
        $.ajax({
            method: "DELETE",
            url: "/api/project/" + projectId + "/pixels/" + pixel,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (_error3) {
                function error() {
                    return _error3.apply(this, arguments);
                }

                error.toString = function () {
                    return _error3.toString();
                };

                return error;
            }(function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            }),
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Pixel Removido com sucesso");
                atualizarPixel();
            }

        });
    });

    function atualizarPixel() {

        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/project/' + projectId + '/pixels';
        } else {
            link = '/api/project/' + projectId + '/pixels' + link;
        }

        loadOnTable('#data-table-pixel', '#table-pixel');

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                $("#data-table-pixel").html(response.message);
            },
            success: function success(response) {
                $("#data-table-pixel").html('');
                if (response.data == '') {
                    $("#data-table-pixel").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                    $('#table-pixel').addClass('table-striped');

                } else {
                    $.each(response.data, function (index, value) {
                        let data = `<tr>
                                    <td>${value.name}</td>
                                    <td>${value.code}</td>
                                    <td>${value.platform}</td>
                                    <td><span class="badge badge-${statusPixel[value.status]}">${value.status_translated}</span></td>
                                    <td style='text-align:center'>
                                        <a role='button' title='Visualizar' class='mg-responsive details-pixel pointer' pixel='${value.id}' data-target='#modal-details-pixel' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></a>
                                        <a role='button' title='Editar' class='mg-responsive edit-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><i class='material-icons gradient'>edit</i></a>
                                        <a role='button' title='Excluir' class='mg-responsive delete-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><i class='material-icons gradient'>delete_outline</i></a>
                                    </td>
                                </tr>`;
                        $("#data-table-pixel").append(data);
                        $('#table-pixel').addClass('table-striped');
                    });

                }
                pagination(response, 'pixels', atualizarPixel);

                $("#select-platform").change(function () {
                    let value = $(this).val();

                    if (value === 'facebook') {
                        $("#input-code-pixel").html('').hide();
                        $("#code-pixel").attr("placeholder", '52342343245553');
                    } else if (value === 'google_adwords') {
                        $("#input-code-pixel").html('AW-').show();
                        $("#code-pixel").attr("placeholder", '8981445741-4');
                    } else if (value === 'google_analytics') {
                        $("#input-code-pixel").html('UA-').show();
                        $("#code-pixel").attr("placeholder", '8984567741-3');
                    } else {
                        $("#input-code-pixel").html('').hide();
                        $("#code-pixel").attr("placeholder", 'Código');
                    }

                });
            }
        });
    }
    function clearFields() {
        $('.pixel-description').val('');
        $('.pixel-code').val('');
    }
});
