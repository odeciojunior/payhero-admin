var statusRole = {
    4: 'success',
    5: 'primary',
    6: 'warning',
}
$(document).ready(function () {

    $('#document').mask('000.000.000-00');
    $("#cellphone").mask("(00) 00000-0009");

    $('#document_edit').mask('000.000.000-00');
    $("#cellphone_edit").mask("(00) 00000-0009");

    create();
    index();

    function index() {
        // loadOnTable('#table-body-collaborators', '#table-collaborators');

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
                    $("#card-table-collaborators").css('display', 'none');
                } else {
                    $("#content-error").css('display', 'none');
                    $("#card-table-collaborators").css('display', 'block');
                    $("#table-body-collaborators").html('');

                    $.each(response.data, function (index, value) {

                        data = '';
                        data += '<tr>';
                        data += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + (cont += 1) + '</button></td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.email + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">';
                        data += '<span class="badge badge-' + statusRole[value.role_id] + ' text-center">' + value.role_translated + '</span>';
                        data += '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.date + '</td>';
                        data += "<td class='text-center'><button class='btn pointer edit-collaborator' title='Editar' style='background-color:transparent;' collaborator='" + value.id + "'><i class='material-icons gradient'>edit</i></button>" +
                            "<button class='btn pointer delete-collaborator' title='Excluir' style='background-color:transparent;' collaborator='" + value.id + "'><i class='material-icons gradient'>delete</i></button>" +
                            "</td>";
                        data += '</tr>';
                        $("#table-body-collaborators").append(data);
                    });
                    pagination(response, 'collaborators');
                }

                // Excluir colaborador
                $('.delete-collaborator').unbind('click');
                $('.delete-collaborator').on('click', function () {
                    var collaborator_id = $(this).attr('collaborator');

                    $('#modal-delete-invitation').modal('show');

                    $('#btn-delete-collaborator').unbind('click');
                    $('#btn-delete-collaborator').on('click', function () {
                        loadingOnScreen();
                        $.ajax({
                            method: "DELETE",
                            url: "/api/collaborators/" + collaborator_id,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: (response) => {
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);
                            },
                            success: (response) => {
                                loadingOnScreenRemove();
                                index();
                                alertCustom('success', response.message);
                            }
                        });
                    });
                });

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

                            //select de funções
                            $('#role_edit > option[selected="selected"]').removeAttr('selected');
                            if (response.data.role == 'admin') {
                                $('#role_edit .opt-admin').attr('selected', true);
                            } else {
                                $('#role_edit .opt-attendance').attr('selected', true);
                            }

                            if (response.data.role == 'attendance') {
                                $('.div-permission-edit').show();
                            } else {
                                $('.div-permission-edit').hide();
                            }

                            if (response.data.refund_permission) {
                                $("#refund_permission_edit").val('refund');
                                $("#refund_permission_edit").attr('checked', 'checked');
                            } else {
                                $("#refund_permission_edit").val('');
                                $("#refund_permission_edit").attr('checked', false);
                            }

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
                            $(".btn-update").on('click', function () {
                                if ($('#name_edit').val() == '' || $('#email_edit').val() == '' || $('#cellphone_edit').val() == '' || $('#document_edit').val() == '') {
                                    alertCustom('error', 'Dados informados inválidos');
                                    return false;
                                }
                                loadingOnScreen();
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
                                        loadingOnScreenRemove();
                                        errorAjaxResponse(response);
                                    },
                                    success: function success(response) {
                                        loadingOnScreenRemove();
                                        $('#modal_add_collaborator').modal('hide');
                                        $(".check").prop('checked', false);
                                        $('#password_edit').attr('disabled', true);
                                        $('#password_edit').val('');
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
            loadingOnScreen();
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
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    // $("#no-integration-found").hide();
                    loadingOnScreenRemove();
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
        $("#form_update_collaborator").hide();
    });

    $('#role').on('change', function () {
        let selectedVal = $(this).val();
        // let permission = $('option:selected', this).data('permission');
        if (selectedVal == 'attendance') {
            $('.div-permission').show();
        } else {
            $('.div-permission').hide();
            $('#refund_permission').val('');
            $("#refund_permission").attr('checked', false);
        }
    });

    $('#role_edit').on('change', function () {
        let selectedVal = $(this).val();
        // let permission = $('option:selected', this).data('permission');
        if (selectedVal == 'attendance') {
            $('.div-permission-edit').show();
        } else {
            $('.div-permission-edit').hide();
            $('#refund_permission_edit').val('');
            $("#refund_permission_edit").attr('checked', false);
        }
    });

    $('#refund_permission').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val('refund');
        } else {
            $(this).val('');
        }
    });

    $('#refund_permission_edit').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val('refund');
        } else {
            $(this).val('');
        }
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
