var statusPixel = {
    1: "success",
    0: "danger",
};

function _defineProperty(obj, key, value) {
    if (key in obj) {
        Object.defineProperty(obj, key, {value: value, enumerable: true, configurable: true, writable: true});
    } else {
        obj[key] = value;
    }
    return obj;
}

$(function () {
    //create
    function renderCreatePixel(){
        let form = `<form id='form-register-pixel' method="post" action="/api/pixels">
                        <input type="hidden" value="${$("meta[name='csrf-token']").attr('content')}" name="_token">
                        <div class="container-fluid">
                            <div class="panel" data-plugin="matchHeight">
                                <div style="width:100%">
                                    <div class="row">
                                        <div class="form-group col-12 mt-4">
                                            <label for="name">Descrição</label>
                                            <input name="name" type="text" class="form-control" id="name" placeholder="Descrição" maxlength='30'>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="platform">Plataforma</label>
                                            <select name="platform" type="text" class="form-control" id="platform">
                                                <option value="facebook">Facebook</option>
                                                <option value="google">Google</option>
                                                <option value="null" disabled='disabled'>Taboola (em breve)</option>
                                                <option value="null" disabled='disabled'>Outbrain (em breve)</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="status">Status</label>
                                            <select name="status" type="text" class="form-control" id="status_pixel">
                                                <option value="1">Ativo</option>
                                                <option value="0">Desativado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-12">
                                            <label for="code">Código</label>
                                            <input name="code" type="text" class="form-control" id="code" placeholder="Código" maxlength='30'>
                                        </div>
                                    </div>
                                    <div class='mb-1'>
                                        <label>Rodar Pixel:</label>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-md-3">
                                            <div class="switch-holder">
                                                <label for="checkout" class='mb-10'>Checkout:</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" value="" name='checkout' id='checkout' class='check' checked>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="switch-holder">
                                                <label for="cartao">Purchase (cartão):</label>
                                                <br>
                                                <label class='switch'>
                                                    <input type="checkbox" value="" name='purchase_card' id='purchase_card' class='check' checked>
                                                    <span class='slider round'></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="switch-holder">
                                                <label for="boleto">Purchase (boleto):</label>
                                                <br>
                                                <label class='switch'>
                                                    <input type="checkbox" value="" name='purchase_boleto' id='purchase_boleto' class='check' checked>
                                                    <span class='slider round'></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>`;

        $("#modal-add-body").html(form);
    }

    //edit
    function renderEditPixel(pixel){
        let form = `<form id="form-update-pixel" method="post" action="/api/pixels">
                        <input type="hidden" value="${$("meta[name='csrf-token']").attr('content')}" name="_token">
                        <input type="hidden" value="${pixel.id}" name="id" id='pixelId'>
                        <div class="row">
                            <div class="form-group col-xl-12 mt-4">
                                <label for="name">Descrição</label>
                                <input value="${pixel.name != '' ? pixel.name : ''}" name="name" type="text" class="input-pad" id="name_pixel" placeholder="Descrição" maxlength='30'>
                            </div>
                            <div class="form-group col-6">
                                <label for="platform">Plataforma</label>
                                <select name="platform" type="text" class="form-control select-pad" id="platform">
                                    <option value="facebook" ${pixel.platform == 'facebook' ? 'selected' : ''}>Facebook</option>
                                    <option value="google" ${pixel.platform == 'google' ? 'selected' : ''}>Google</option>
                                    <option value="null" disabled='disabled'>Taboola (em breve)</option>
                                    <option value="null" disabled='disabled'>Outbrain (em breve)</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="status">Status</label>
                                <select name="status" type="text" class="form-control select-pad" id="status">
                                    <option value="1" ${pixel.status == '1' ? 'selected' : ''}>Ativo</option>
                                    <option value="0" ${pixel.status == '0' ? 'selected' : ''}>Desativado</option>
                                </select>
                            </div>
                            <div class="form-group col-xl-12">
                                <label for="code">Código</label>
                                <input value="${pixel.code != '' ? pixel.code : ''}" name="code" type="text" class="input-pad" id="code" placeholder="Código" maxlength='30'>
                            </div>
                        </div>
                        <div class='mb-1'>
                            <label>Rodar Pixel:</label>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="switch-holder">
                                    <label for="Checkout">Checkout:</label>
                                    <br>
                                    <label class='switch'>
                                        <input type="checkbox" ${pixel.checkout == '1' ? 'value="1" checked=""' : 'value="0"'} name='checkout' id='checkout' class='check'>
                                        <span class='slider round'></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="switch-holder">
                                    <label for="cartao">Purchase (cartão):</label>
                                    <br>
                                    <label class='switch'>
                                        <input type="checkbox" ${pixel.purchase_card == '1' ? 'value="1" checked=""' : 'value="0"'} name='purchase_card' id='purchase_card' class='check'>
                                        <span class='slider round'></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="switch-holder">
                                    <label for="boleto">Purchase (boleto):</label>
                                    <br>
                                    <label class='switch'>
                                        <input type="checkbox" ${pixel.purchase_boleto == '1' ? 'value="1" checked=""' : 'value="0"'} name='purchase_boleto' id='purchase_boleto' class='check'>
                                        <span class='slider round'></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>`;

        $("#modal-add-body").html(form);
    }

    //show
    function renderDetailPixel(pixel){
        let form = `<table class='table table-striped' style='width: 100%'>
                        <tbody>
                            <tr>
                                <td class="table-title">Descrição</td>
                                <td style='width: 20px'></td>
                                <td class='text-left'>${pixel.name}</td>
                                <br>
                            </tr>
                            <tr>
                                <td class="table-title">Code</td>
                                <td style='width: 20px'></td>
                                <td class='text-left'>${pixel.code}</td>
                            </tr>
                            <tr>
                                <td class="table-title">Plataforma</td>
                                <td style='width: 20px'></td>
                                <td class='text-left'>${pixel.platform}</td>
                            </tr>
                            <tr>
                                <td class="table-title">Status</td>
                                <td style='width: 20px'></td>
                                <td class='text-left'>
                                ${  
                                    pixel.status == 1
                                    ? '<span class="badge badge-success text-left">Ativo</span>'
                                    : '<span class="badge badge-danger">Desativado</span>'
                                }
                                 </td>
                            </tr>
                        </tbody>
                    </table>`;

        $("#modal-add-body").html(form);
    }

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
        $("#btn-modal").addClass('btn-save');
        $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
        $("#btn-modal").show();
        //$('#modal-add-body').html(data);
        renderCreatePixel();
        loadingOnScreenRemove();

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
            formData.append('project_id', projectId);
            formData.append('checkout', $("#checkout").val());
            formData.append('purchase_card', $("#purchase_card").val());
            formData.append('purchase_boleto', $("#purchase_boleto").val());
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
    });

    function atualizarPixel() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

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
                        data = '';
                        data += '<tr >';
                        data += '<td >' + value.name + '</td>';
                        data += '<td >' + value.code + '</td>';
                        data += '<td >' + value.platform + '</td>';
                        data += '<td ><span class="badge badge-' + statusPixel[value.status] + '">' + value.status_translated + '</span></td>';
                        data += "<td style='text-align:center'>"
                        data += "<a role='button' class='mg-responsive details-pixel pointer'   pixel='" + value.id + "'  data-target='#modal-content' data-toggle='modal'         type='a'><i class='material-icons gradient'>remove_red_eye</i> </a>"
                        data += "<a role='button' class='mg-responsive edit-pixel    pointer'   pixel='" + value.id + "'  data-target='#modal-content' data-toggle='modal'         type='a'><i class='material-icons gradient'>edit</i></a>"
                        data += "<a role='button' class='mg-responsive delete-pixel  pointer'   pixel='" + value.id + "'  data-toggle='modal'          data-target='#modal-delete' type='a'><i class='material-icons gradient'>delete_outline</i> </a>";
                        data += "</td>";

                        data += '</tr>';
                        $("#data-table-pixel").append(data);
                        $('#table-pixel').addClass('table-striped');
                    });
                }

                pagination(response, 'pixels', atualizarPixel);

                // details pixel
                $(".details-pixel").unbind('click');
                $(".details-pixel").on('click', function () {
                    var pixel = $(this).attr('pixel');
                    $("#modal-title").html('Detalhes do pixel');
                    loadOnModal('#modal-add-body');
                    var data = {pixelId: pixel};
                    $("#btn-modal").hide();
                    $.ajax({
                        method: "GET",
                        url: "/api/pixels/" + pixel,
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        }, success: function success(response) {
                            //$("#modal-add-body").html(response);
                            loadingOnScreenRemove();
                            renderDetailPixel(response);
                        }
                    });
                });

                // edit pixel
                $(".edit-pixel").unbind('click');
                $(".edit-pixel").on('click', function () {
                    $("#modal-add-body").html("");
                    var pixel = $(this).attr('pixel');
                    $("#modal-title").html("Editar Pixel");
                    loadOnModal('#modal-add-body');
                    var data = {pixelId: pixel};
                    $.ajax({
                        method: "GET",
                        url: "/api/pixels/" + pixel + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        }, success: function success(response) {
                            loadingOnScreenRemove();
                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            //$("#modal-add-body").html(response)
                            renderEditPixel(response);
                            $("#btn-modal").show();
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
                                    url: "/api/pixels/" + pixel,
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
