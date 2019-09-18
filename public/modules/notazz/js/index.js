$(document).ready(function () {
    updateIntegrations();
    $("#btn-add-integration").on("click", function () {

        $.ajax({
            method: "GET",
            url: "/apps/notazz/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                if (response.message == 'Nenhum projeto encontrado') {
                    var route = '/projects/create';
                    $('#modal-project').modal('show');
                    $('#modal-project-title').text("Oooppsssss!");
                    $('#modal_project_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Você não possui projetos para realizar integração</strong></h3>' + '<h5 align="center">Deseja criar seu primeiro projeto? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                } else {
                    $(".modal-title").html('Adicionar nova Integração com a Notazz');
                    $(".modal_integracao_body").html("");
                    $("#bt_integration").addClass('btn-save');
                    $("#bt_integration").text('Adicionar integração');
                    $(".modal_integracao_body").html(response);
                    $("#modal_add_integracao").modal('show');

                    $(".btn-save").unbind('click');
                    $(".btn-save").on('click', function () {
                        if ($('#token_webhook').val() == '' || $('#token_api').val() == '' || $('#token_logistics').val() == '') {
                            alertCustom('error', 'Dados informados inválidos');
                            return false;
                        }
                        var form_data = new FormData(document.getElementById('form_add_integration'));

                        $.ajax({
                            method: "POST",
                            url: "/apps/notazz",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            error: function error(response) {
                                alertCustom('error', response.responseJSON.message); //'Ocorreu algum erro'
                            },
                            success: function success(response) {
                                updateIntegrations();
                                alertCustom('success', response.message);
                            }
                        });
                    });
                }
            }
        });
    });
    function updateIntegrations() {
        $.ajax({
            method: "GET",
            url: "/apps/getnotazzintegrations",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                loadingOnScreenRemove();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $("#project-integrated").html("");
                $("#project-integrated").html(response);

                $(".card-edit").unbind('click');
                $('.card-edit').on('click', function () {
                    $(".modal_integracao_body").html("");
                    var project = $(this).attr('project');
                    $(".modal-title").html("Editar Integração com a Notazz");
                    $(".modal_integracao_body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {projectId: project};
                    $.ajax({
                        method: "GET",
                        url: "/apps/notazz/" + project + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error(response) {
                            //
                        }, success: function success(response) {
                            $("#bt_integration").addClass('btn-update');
                            $("#bt_integration").text('Atualizar');
                            $("#btn-modal").show();
                            $(".modal_integracao_body").html(response);
                            $("#modal_add_integracao").modal('show');

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {
                                if ($('#token_webhook').val() == '' || $('#token_api').val() == '' || $('#token_logistics').val() == '') {
                                    alertCustom('error', 'Dados informados inválidos');
                                    return false;
                                }
                                var integrationId = $('#integration_id').val();
                                var form_data = new FormData(document.getElementById('form_update_integration'));

                                $.ajax({
                                    method: "POST",
                                    url: "/apps/notazz/" + integrationId,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    error: function (_error) {
                                        function error(_x) {
                                            return _error.apply(this, arguments);
                                        }

                                        error.toString = function () {
                                            return _error.toString();
                                        };

                                        return error;
                                    }(function (response) {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    }),
                                    success: function success(response) {
                                        updateIntegrations();
                                        alertCustom("success", response.message);
                                    }
                                });
                            });
                        }
                    });
                });
                $(".delete-integration").unbind('click');
                $('.delete-integration').on('click', function (e) {
                    e.preventDefault();
                    var project = $(this).attr('project');
                    var card = $(this).parent().parent().parent().parent().parent();
                    card.find('.card-edit').unbind('click');
                    $.ajax({
                        method: "DELETE",
                        url: "/apps/notazz/" + project,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function (_error2) {
                            function error(_x2) {
                                return _error2.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error2.toString();
                            };

                            return error;
                        }(function (response) {
                            if (response.status == '422') {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            }
                        }),
                        success: function success(response) {
                            updateIntegrations();
                            alertCustom("success", response.message);
                        }
                    });
                });
            }
        });
    }
});
