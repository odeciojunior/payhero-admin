$(document).ready(function () {

    index();

    function index() {
        $.ajax({
            method: "GET",
            url: "/api/apps/notazz/",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            dataType: "json",
            error: function error(response) {
                // loadingOnScreenRemove();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                $('#content').html("");
                if (Object.keys(response.data).length === 0) {
                    $("#no-integration-found").show();
                } else {
                    $(response.data).each(function (index, data) {
                        $('#content').append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" integration=` + data.id + ` style='cursor:pointer;'>
                                    <img class="card-img-top img-fluid w-full" src="${!data.project_photo ? '/modules/global/img/produto.png' : data.project_photo}"/>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <h4 class="card-title">` + data.project_name + `</h4>
                                                <p class="card-text sm">Criado em ` + data.created_at + `</p>
                                            </div>
                                            <div class='col-md-2'>
                                                <a role='button' class='delete-integration pointer float-right mt-35' integration=` + data.id + ` data-toggle='modal' data-target='#modal-delete' type='a'>
                                                    <i class='material-icons gradient'>delete_outline</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });

                    $(".delete-integration").unbind('click');
                    $('.delete-integration').on('click', function (e) {
                        e.preventDefault();
                        var integration_id = $(this).attr('integration');
                        var card = $(this).parent().parent().parent().parent().parent();
                        card.find('.card-edit').unbind('click');
                        $.ajax({
                            method: "DELETE",
                            url: "/api/apps/notazz/" + integration_id,
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            dataType: "json",
                            error: function (_error2) {
                                function error(_x2) {
                                    return _error2.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error2.toString();
                                };

                                return error;
                            }(function (response) {
                                if (response.status === 422) {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                } else {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            }),
                            success: function success(response) {
                                index();
                                alertCustom('success', response.message);
                            }
                        });
                    });

                    $(".card-edit").unbind('click');
                    $('.card-edit').on('click', function () {
                        var integration_id = $(this).attr('integration');
                        $.ajax({
                            method: "GET",
                            url: "/api/projects?select=true",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            dataType: "json",
                            error: function error(response) {
                                alertCustom('error', 'Ocorreu algum erro');
                            },
                            success: function success(response) {
                                $("#select_projects_edit").html('');
                                $(response.data).each(function (index, data) {
                                    $("#select_projects_edit").append("<option value='" + data.id + "'>" + data.name + "</option>");
                                });

                                $(".modal-title").html("Editar Integração com Notazz");

                                $.ajax({
                                    method: "GET",
                                    url: "/api/apps/notazz/" + integration_id,
                                    headers: {
                                        'Authorization': $('meta[name="access-token"]').attr('content'),
                                        'Accept': 'application/json',
                                    },
                                    dataType: "json",
                                    error: function error(response) {
                                        //
                                    },
                                    success: function success(response) {

                                        $("#select_projects_edit").val(response.data.project_id);
                                        $("#select_invoice_type_edit").val(response.data.invoice_type);
                                        $('#integration_id').val(response.data.id);
                                        $('#token_api_edit').val(response.data.token_api);
                                        $('#token_webhook_edit').val(response.data.token_webhook);
                                        $('#token_logistics_edit').val(response.data.token_logistics);
                                        $('#start_date_edit').val(response.data.start_date);

                                        $("#modal_add_integracao").modal('show');
                                        $("#form_add_integration").hide();
                                        $("#form_update_integration").show();

                                        $("#bt_integration").addClass('btn-update');
                                        $("#bt_integration").removeClass('btn-save');
                                        $("#bt_integration").text('Atualizar');
                                        $("#btn-modal").show();

                                        $(".btn-update").unbind('click');
                                        $(".btn-update").on('click', function () {

                                            var integrationId = $('#integration_id').val();
                                            var form_data = new FormData(document.getElementById('form_update_integration'));

                                            $.ajax({
                                                method: "POST",
                                                url: "/api/apps/notazz/" + integrationId,
                                                headers: {
                                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                                    'Accept': 'application/json',
                                                },
                                                dataType: "json",
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
                                                    if (response.status === 422) {
                                                        for (error in response.responseJSON.errors) {
                                                            alertCustom('error', String(response.responseJSON.errors[error]));
                                                        }
                                                    } else {
                                                        alertCustom('error', response.message);
                                                    }
                                                }),
                                                success: function success(response) {
                                                    index();
                                                    alertCustom('success', response.message);
                                                }
                                            });
                                        });
                                    }
                                });
                            }
                        });
                    });
                }
            }
        });
    }

    function create() {

        $.ajax({
            method: "GET",
            url: "/api/projects?select=true",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            dataType: "json",
            error: function error(response) {
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                if (Object.keys(response.data).length === 0) {
                    var route = '/projects/create';
                    $('#modal-project').modal('show');
                    $('#modal-project-title').text("Oooppsssss!");
                    $('#modal_project_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Você não possui projetos para realizar integração</strong></h3>' + '<h5 align="center">Deseja criar seu primeiro projeto? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                } else {
                    $("#select_projects_create").html('');
                    $(response.data).each(function (index, data) {
                        $("#select_projects_create").append("<option value='" + data.id + "'>" + data.name + "</option>");
                    });
                    $(".modal-title").html('Adicionar nova Integração com Notazz');
                    $("#bt_integration").addClass('btn-save');
                    $("#bt_integration").text('Adicionar integração');
                    $("#modal_add_integracao").modal('show');
                    $("#form_update_integration").hide();
                    $("#form_add_integration").show();

                    $(".btn-save").unbind('click');
                    $(".btn-save").on('click', function () {
                        if ($('#token_api_create').val() == '') {
                            alertCustom('error', 'Datos invalidos, o campo Token Api é obrigatorio.');
                            return false;
                        }

                        var select_projects_create = $('#select_projects_create').val();
                        var select_invoice_type_create = $('#select_invoice_type_create').val();
                        var token_api_create = $('#token_api_create').val();
                        var token_webhook_create = $('#token_webhook_create').val();
                        var token_logistics_create = $('#token_logistics_create').val();
                        var start_date_create = $('#start_date_create').val();

                        if ($('#start_date_create').val() != '') {
                            swal({
                                title: 'Data inicial de geração de notas fiscais foi definida.',
                                type: 'warning',
                                text: "Uma data inicial para geração de notas fiscais foi selecionada, será gerada as notas fiscais de todas as vendas aprovadas apartir da data selecionada, deseja continuar?",
                                showCancelButton: true,
                                confirmButtonColor: '#3085D6',
                                cancelButtonColor: '#DD3333',
                                cancelButtonText: 'Cancelar',
                                confirmButtonText: 'Continuar'
                            }).then(function (data) {
                                if (data.value) {
                                    //ok

                                    $.ajax({
                                        method: "POST",
                                        url: "/api/apps/notazz",
                                        headers: {
                                            'Authorization': $('meta[name="access-token"]').attr('content'),
                                            'Accept': 'application/json',
                                        },
                                        dataType: "json",
                                        data: {
                                            select_projects_create: select_projects_create,
                                            select_invoice_type_create: select_invoice_type_create,
                                            token_api_create: token_api_create,
                                            token_webhook_create: token_webhook_create,
                                            token_logistics_create: token_logistics_create,
                                            start_date_create: start_date_create
                                        },
                                        error: function error(response) {
                                            if (response.status === 422) {
                                                for (error in response.errors) {
                                                    alertCustom('error', String(response.errors[error]));
                                                }
                                            } else {
                                                alertCustom('error', response.responseJSON.message);
                                            }
                                        },
                                        success: function success(response) {
                                            $("#no-integration-found").hide();
                                            index();
                                            alertCustom('success', response.message);
                                        }
                                    });

                                } else {
                                    //cancel

                                }
                            });
                        } else {
                            $.ajax({
                                method: "POST",
                                url: "/api/apps/notazz",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                dataType: "json",
                                data: {
                                    select_projects_create: select_projects_create,
                                    select_invoice_type_create: select_invoice_type_create,
                                    token_api_create: token_api_create,
                                    token_webhook_create: token_webhook_create,
                                    token_logistics_create: token_logistics_create,
                                    start_date_create: start_date_create
                                },
                                error: function error(response) {
                                    if (response.status === 422) {
                                        for (error in response.errors) {
                                            alertCustom('error', String(response.errors[error]));
                                        }
                                    } else {
                                        alertCustom('error', response.responseJSON.message);
                                    }
                                },
                                success: function success(response) {
                                    $("#no-integration-found").hide();
                                    index();
                                    alertCustom('success', response.message);
                                }
                            });
                        }
                    });
                }
            }
        });
    }

    $("#btn-add-integration").on("click", function () {
        create();
    });
});
