function _defineProperty(obj, key, value) {
    if (key in obj) {
        Object.defineProperty(obj, key, {value: value, enumerable: true, configurable: true, writable: true});
    } else {
        obj[key] = value;
    }
    return obj;
}

$(function () {
    var projectId = $("#project-id").val();

    $('#tab_pixels').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarPixel();
    });
    atualizarPixel();
    //criar novo pixel
    $("#add-pixel").on('click', function () {
        loadOnModal('#modal-add-body');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal-title").html('Novo pixel');
        $.ajax({
            method: "GET",
            url: "/pixels/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                loadingOnScreenRemove();

                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.errors[error]));
                }
                $("#modal-content").hide();
            },
            success: function success(data) {
                loadingOnScreenRemove();
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);

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

                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {
                    var formData = new FormData(document.getElementById('form-register-pixel'));
                    formData.append('project', projectId);
                    formData.append('checkout', $("#checkout").val());
                    formData.append('purchase_card', $("#purchase_card").val());
                    formData.append('purchase_boleto', $("#purchase_boleto").val());
                    loadingOnScreen();
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
            }
        });
    });
    function atualizarPixel() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#data-table-pixel', '#table-pixel');

        if (link == null) {
            link = '/pixels?' + 'project=' + projectId;
        } else {
            link = '/pixels' + link + '&project=' + projectId;
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
                        data = '';
                        data += '<tr class="shipping-id">';
                        data += '<td class="shipping-id" width="70%" style="width:120px;">' + value.name + '</td>';
                        data += '<td class="shipping-type" width="70%" style="width:120px;">' + value.code + '</td>';
                        data += '<td class="shipping-value"  width="70% "style="width:120px;">' + value.platform + '</td>';
                        data += '<td class="shipping-status" width="70%" style="width:100px;">';
                        if (value.status == 1) {
                            data += '<span class="badge badge-success">Ativo</span>';
                        } else {
                            data += '<span class="badge badge-danger">Desativado</span>';
                        }
                        data += '</td>';

                        data += "<td style='min-width:200px;'>" + "<a role='button' class='details-pixel pointer mr-30'  pixel='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='a'><i class='material-icons gradient'>remove_red_eye</i> </a>" + "<a role='button'class='edit-pixel pointer'  pixel='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='a'><i class='material-icons gradient'>edit</i></a>" + "<a role='button' class='delete-pixel pointer ml-30'  pixel='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='a'><i class='material-icons gradient'>delete_outline</i> </a>";
                        "</td>";

                        data += '</tr>';
                        $("#data-table-pixel").append(data);
                    });
                }

                pagination(response,'pixels',atualizarPixel);

                // details pixel
                $(".details-pixel").unbind('click');
                $(".details-pixel").on('click', function () {
                    var pixel = $(this).attr('pixel');
                    $("#modal-title").html('Detalhes do pixel');
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
                        error: function error() {
                            //
                        }, success: function success(response) {
                            $("#modal-add-body").html(response);
                        }
                    });
                });

                // edit pixel
                $(".edit-pixel").unbind('click');
                $(".edit-pixel").on('click', function () {
                    $("#modal-add-body").html("");
                    var pixel = $(this).attr('pixel');
                    $("#modal-title").html("Editar Pixel");
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {pixelId: pixel};
                    $.ajax({
                        method: "GET",
                        url: "/pixels/" + pixel + "/edit",
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
                            $('.check').on('click', function () {
                                if ($(this).is(':checked')) {
                                    $(this).val(1);
                                } else {
                                    $(this).val(0);
                                }
                            });

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                loadingOnScreen();
                                $.ajax({
                                    method: "PUT",
                                    url: "/pixels/" + pixel,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: _defineProperty({
                                        name: $("#name_pixel").val(),
                                        code: $("#code").val(),
                                        platform: $("#platform").val(),
                                        status: $("#status").val(),
                                        checkout: $("#checkout").val(),
                                        purchase_card: $("#purchase_card").val(),
                                        purchase_boleto: $("#purchase_boleto").val()
                                    }, 'purchase_card', $("#purchase_card").val()),
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
                                        if (response.status === 422) {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        } else {
                                            alertCustom('error', String(response.responseJSON.message));
                                        }
                                    }),
                                    success: function success(data) {
                                        loadingOnScreenRemove();
                                        alertCustom("success", "Pixel atualizado com sucesso");
                                        atualizarPixel();
                                    }
                                });
                            });
                        }
                    });
                });

                // delete pixel
                $('.delete-pixel').on('click', function (event) {
                    event.preventDefault();
                    var pixel = $(this).attr('pixel');
                    $("#modal_excluir_titulo").html("Remover Pixel?");
                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#fechar_modal_excluir").click();
                        loadingOnScreen();
                        $.ajax({
                            method: "DELETE",
                            url: "/pixels/" + pixel,
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
                                alertCustom("success", "Pixel Removido com sucesso");
                                atualizarPixel();
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

            $("#pagination-pixels").html("");

            var primeira_pagina_pixel = "<button id='primeira_pagina_pixel' class='btn nav-btn'>1</button>";

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
                var pagina_atual_pixel = "<button id='pagina_atual_pixel' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

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
                var ultima_pagina_pixel = "<button id='ultima_pagina_pixel' class='btn nav-btn'>" + response.meta.last_page + "</button>";

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
