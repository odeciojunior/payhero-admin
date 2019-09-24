let statusPixel = {
    1: "success",
    0: "danger",
};

$(function () {
    let projectId = $("#project-id").val();

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

    //carrega os itens na tabela
    atualizarPixel();

    // carregar modal de detalhes
    $(document).on('click', '.details-pixel', function () {
        let pixel = $(this).attr('pixel');
        let data = {pixelId: pixel};
        $.ajax({
            method: "GET",
            url: "/api/pixels/" + pixel,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom("error", "Erro ao carregar detalhes do pixel");
            }, success: function success(response) {
                renderDetailPixel(response);
            }
        });
    });

    function renderDetailPixel(pixel) {
        $('#modal-detail-pixel .detail-description').html(pixel.name);
        $('#modal-detail-pixel .detail-code').html(pixel.code);
        $('#modal-detail-pixel .detail-platform').html(pixel.platform);
        $('#modal-detail-pixel .detail-status').html(pixel.status == 1
            ? '<span class="badge badge-success text-left">Ativo</span>'
            : '<span class="badge badge-danger">Desativado</span>');
        $('#modal-detail-pixel').modal('show');
    }

    // carregar modal de edicao
    $(document).on('click', '.edit-pixel', function () {
        let pixel = $(this).attr('pixel');
        let data = {pixelId: pixel};
        $.ajax({
            method: "GET",
            url: "/api/pixels/" + pixel + "/edit",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom("error", "Erro ao carregar modal de ediçao");
            }, success: function success(response) {
                renderEditPixel(response);
                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
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
        if (pixel.platform == 'google') {
            $('#modal-edit-pixel .pixel-platform').prop("selectedIndex", 1).change();
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
        formData.append('project_id', projectId);
        formData.append('checkout', $("#modal-create-pixel .pixel-checkout").val());
        formData.append('purchase_card', $("#modal-create-pixel .pixel-purchase-card").val());
        formData.append('purchase_boleto', $("#modal-create-pixel .pixel-purchase-boleto").val());

        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/pixels",
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
                if (data.status == '422') {
                    for (error in data.responseJSON.errors) {
                        alertCustom('error', String(data.responseJSON.errors[error]));
                    }
                }
            }), success: function success() {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Pixel Adicionado!");
                atualizarPixel();
            }
        });
    });

    //atualizar pixel
    $(document).on('click', '#modal-edit-pixel .btn-update', function () {
        loadingOnScreen();
        let pixel = $('#modal-edit-pixel .pixel-id').val();
        $.ajax({
            method: "PUT",
            url: "/api/pixels/" + pixel,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
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
            error: function () {
                loadingOnScreenRemove();
                alertCustom("error", "Erro ao atualizar pixel");
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
            url: "/api/pixels/" + pixel,
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
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Pixel Removido com sucesso");
                atualizarPixel();
            }

        });
    });

    function atualizarPixel() {
        let link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#data-table-pixel', '#table-pixel');

        if (link == null) {
            link = '/api/pixels?' + 'project=' + projectId;
        } else {
            link = '/api/pixels' + link + '&project=' + projectId;
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                $("#data-table-pixel").html(response.message);
            },
            success: function success(response) {
                $("#data-table-pixel").html('');
                if (response.data == '') {
                    $("#data-table-pixel").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        let data = `<tr>
                                    <td>${value.name}</td>
                                    <td>${value.code}</td>
                                    <td>${value.platform}</td>
                                    <td><span class="badge badge-${statusPixel[value.status]}">${value.status_translated}</span></td>
                                    <td style='text-align:center'>
                                        <a role='button' class='mg-responsive details-pixel pointer' pixel='${value.id}' data-target='#modal-details-pixel' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></a>
                                        <a role='button' class='mg-responsive edit-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><i class='material-icons gradient'>edit</i></a>
                                        <a role='button' class='mg-responsive delete-pixel pointer' pixel='${value.id}' data-toggle='modal' type='a'><i class='material-icons gradient'>delete_outline</i></a>
                                    </td>
                                </tr>`;
                        $("#data-table-pixel").append(data);
                        $('#table-pixel').addClass('table-striped');
                    });
                }

                pagination(response, 'pixels', atualizarPixel);
            }
        });
    }

    function pagination(response) {
        if (response.meta.last_page == 1) {
            $("#primeira_pagina_pixel").hide();
            $("#ultima_pagina_pixel").hide();
        } else {

            $("#pagination-pixels").html("");

            let primeira_pagina_pixel = "<button id='primeira_pagina_pixel' class='btn nav-btn'>1</button>";

            $("#pagination-pixels").append(primeira_pagina_pixel);

            if (response.meta.current_page == '1') {
                $("#primeira_pagina_pixel").attr('disabled', true);
                $("#primeira_pagina_pixel").addClass('nav-btn');
                $("#primeira_pagina_pixel").addClass('active');
            }

            $('#primeira_pagina_pixel').on("click", function () {
                atualizarPixel('?page=1');
            });

            for (x = 3; x > 0; x--) {

                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-pixels").append("<button id='pagina_pixel_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

                $('#pagina_pixel_' + (response.meta.current_page - x)).on("click", function () {
                    atualizarPixel('?page=' + $(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                let pagina_atual_pixel = "<button id='pagina_atual_pixel' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-pixels").append(pagina_atual_pixel);

                $("#pagina_atual_pixel").attr('disabled', true);
                $("#pagina_atual_pixel").addClass('nav-btn');
                $("#pagina_atual_pixel").addClass('active');
            }
            for (x = 1; x < 4; x++) {

                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-pixels").append("<button id='pagina_pixel_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

                $('#pagina_pixel_' + (response.meta.current_page + x)).on("click", function () {
                    atualizarPixel('?page=' + $(this).html());
                });
            }

            if (response.meta.last_page != '1') {
                let ultima_pagina_pixel = "<button id='ultima_pagina_pixel' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-pixels").append(ultima_pagina_pixel);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#ultima_pagina_pixel").attr('disabled', true);
                    $("#ultima_pagina_pixel").addClass('nav-btn');
                    $("#ultima_pagina_pixel").addClass('active');
                }

                $('#ultima_pagina_pixel').on("click", function () {
                    atualizarPixel('?page=' + response.meta.last_page);
                });
            }
        }
    }
});
