$(document).ready(function () {

    let maskOptions = {
        onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
            $('#document').mask(mask, maskOptions);
        }
    };

    $('#document').mask('000.000.000-000', maskOptions);
    $("#cellphone").mask("(00) 0000-00009");

    create();
    index();
    function index() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var cont = 0;

        if (link == null) {
            link = '/api/collaborators';
        } else {
            link = '/api/collaborators' + link;
        }

        // loadOnTable('#table-body-collaborators', '#table-collaborators');
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (isEmpty(response.data)) {
                    $("#content-error").css('display', 'block');
                } else {
                    $("#content-error").hide();
                    $("#card-table-collaborators").css('display', 'block');
                    // $("#card-invitation-data").css('display', 'block');

                    // $("#text-info").css('display', 'block');
                    $("#card-table-collaborators").css('display', 'block');
                    $("#table-body-collaborators").html('');
                    // $('#table-collaborators').addClass('table-striped');

                    $.each(response.data, function (index, value) {

                        data = '';
                        data += '<tr>';
                        data += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + (cont += 1) + '</button></td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.email + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.date + '</td>';
                        data += "<td class='text-center'><button class='btn pointer edit-collaborator' title='Editar' style='background-color:transparent;' collaborator='" + value.id + "'><i class='material-icons gradient'>edit</i></button>" +
                            "<button class='btn pointer delete-collaborator' title='Excluir' style='background-color:transparent;' collaborator='" + value.id + "'><i class='material-icons gradient'>delete</i></button>" +
                            "</td>";
                        data += '</tr>';
                        $("#table-body-collaborators").append(data);
                    });
                    pagination(response, 'collaborators');
                }

                $(".edit-collaborator").unbind('click');
                $('.edit-collaborator').on('click', function () {
                    var collaborator_id = $(this).attr('collaborator');

                    $.ajax({
                        method: "GET",
                        url: "/api/collaborators/" + collaborator_id,
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error() {
                            errorAjaxResponse(response);

                        },
                        success: function success(response) {
                            $('#modal_add_collaborator').modal('show');
                            $(".modal-title").html("Editar Colaborador");
                            $("#form_add_collaborator").hide();
                            $("#form_update_collaborator").show();
                            $("#btn_collaborator").addClass('btn-update');
                            $("#btn_collaborator").removeClass('btn-save');
                            $("#btn_collaborator").text('Atualizar');

                            $("#name_edit").val(response.data.name);
                            $("#email_edit").val(response.data.email);
                            $("#cellphone_edit").val(response.data.cellphone);
                            $("#document_edit").val(response.data.document);
                            $("#collaborator_id").val(response.data.id);

                            $('.check').on('change', function () {
                                if ($(this).is(':checked')) {
                                    $(this).val(1);
                                    $('#password_edit').removeAttr('disabled');
                                } else {
                                    $(this).val(0);
                                    $('#password_edit').attr('disabled', true);
                                }
                            });

                            $(".btn-update").unbind('click');
                            $(document).on('click', '.btn-update', function () {
                                if ($('#name_edit').val() == '' || $('#email_edit').val() == '' || $('#cellphone_edit').val() == '' || $('#document_edit').val() == '') {
                                    alertCustom('error', 'Dados informados inválidos');
                                    return false;
                                }

                                var collaboratorId = $('#collaborator_id').val();
                                var form_data = new FormData(document.getElementById('form_update_collaborator'));

                                $.ajax({
                                    method: "POST",
                                    url: "/api/collaborators/" + collaboratorId,
                                    headers: {
                                        'Authorization': $('meta[name="access-token"]').attr('content'),
                                        'Accept': 'application/json',
                                    },
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    error: function (response) {
                                        errorAjaxResponse(response);
                                    },
                                    success: function success(response) {
                                        index();
                                        alertCustom('success', response.message);
                                    }
                                });
                            });
                        }
                    });
                });

            }
        });
    }
    function create() {
        $(".btn-save").unbind('click');
        $(document).on('click', '.btn-save', function () {
            if ($('#name').val() == '' || $('#email').val() == '' || $('#cellphone').val() == '' || $('#document').val() == '' || $('#password').val() == '') {
                alertCustom('error', 'Dados informados inválidos');
                return false;
            }
            var form_data = new FormData(document.getElementById('form_add_collaborator'));

            $.ajax({
                method: "POST",
                url: "/api/collaborators",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                processData: false,
                contentType: false,
                cache: false,
                data: form_data,
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    // $("#no-integration-found").hide();
                    $('#modal_add_collaborator').modal('hide');
                    index();
                    clearFields();
                    alertCustom('success', response.message);
                }
            });
        });
    }

    $('#add-collaborator').on('click', function () {
        $('#modal_add_collaborator').modal('show');
        $(".modal-title").html('Adicionar novo colaborador');
        $("#btn_collaborator").addClass('btn-save');
        $("#btn_collaborator").text('Adicionar colaborador');
        $("#form_add_collaborator").show();
    });
    function clearFields() {
        $('#name').val('');
        $('#email').val('');
        $('#cellphone').val('');
        $('#document').val('');
        $('#password').val('');
    }
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
                index('?page=1');
            });

            for (x = 3; x > 0; x--) {

                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

                $('#page_' + (response.meta.current_page - x)).on("click", function () {
                    index('?page=' + $(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page = "<button id='current_page' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page").attr('disabled', true).addClass('nav-btn').addClass('active');
            }
            for (x = 1; x < 4; x++) {

                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

                $('#page_' + (response.meta.current_page + x)).on("click", function () {
                    index('?page=' + $(this).html());
                });
            }

            if (response.meta.last_page != '1') {
                var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page").attr('disabled', true).addClass('nav-btn').addClass('active');
                }

                $('#last_page').on("click", function () {
                    index('?page=' + response.meta.last_page);
                });
            }
        }
    }
});
