$(document).ready(function () {
    index();

    function index(){
        $.ajax({
            method: "GET",
            url: "/api/apps/hotzapp/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                $('#content').html("");
                let projects = response.projects;
                for (let i = 0; i < projects.length; i++) {
                    $('#project_id').append('<option value="' + projects[i].id + '">' + projects[i].name + '</option>');
                }
                if(Object.keys(response.integrations).length === 0){
                    $("#no-integration-found").show();
                } else {
                    let integrations = response.integrations;
                    for (let i = 0; i < integrations.length; i++) {
                        renderIntegration(integrations[i]);
                    }
                    $("#no-integration-found").hide();
                }
            }
        });
    }

    function renderIntegration(data){
        $('#content').append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=` + data.id + ` style='cursor:pointer;'>
                                    <img class="card-img-top img-fluid w-full" src=` + data.project_photo + ` onerror="this.onerror=null;this.src='/modules/global/img/produto.png';" alt="` + data.project_name + `"/>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <h4 class="card-title">` + data.project_name + `</h4>
                                                <p class="card-text sm">Criado em ` + data.created_at + `</p>
                                            </div>
                                            <div class='col-md-2'>
                                                <a role='button' class='delete-integration pointer float-right mt-35' project=` + data.id + `>
                                                    <i class='material-icons gradient'>delete_outline</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
    }

    $('#btn-add-integration').on('click', function(){
        $(".modal-title").html('Adicionar nova Integração com HotZpp');
        $("#bt_integration").addClass('btn-save');
        $("#bt_integration").text('Adicionar integração');
        $("#modal_add_integracao").modal('show');
        $("#form_update_integration").hide();
        $("#form_add_integration").show();
    });

    $(document).on('click', '.card-edit', function () {

        $.ajax({
            method: "GET",
            url: "/api/apps/hotzapp/show" + $(this).attr('project'),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
               console.log(response)
            }
        });

        $(".modal-title").html('Editar nova Integração com HotZpp');
        $("#bt_integration").addClass('btn-update');
        $("#bt_integration").text('Atualizar');
        $("#modal_add_integracao").modal('show');
        $("#form_update_integration").show();
        $("#form_add_integration").hide();
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

    $(".btn-save").on('click', function () {
        if ($('#link').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        var form_data = new FormData(document.getElementById('form_add_integration'));

        $.ajax({
            method: "POST",
            url: "/api/apps/hotzapp",
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
                index();
                alertCustom('success', response.message);
            }
        });
    });

    $(document).on('click', '.delete-integration', function (e) {
        e.preventDefault();
        var project = $(this).attr('project');
        var card = $(this).parent().parent().parent().parent().parent();
        card.find('.card-edit').unbind('click');
        $.ajax({
            method: "DELETE",
            url: "/api/apps/hotzapp/" + project,
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
                index();
                alertCustom("success", response.message);
            }
        });
    });

    //             }
    //         }
    //     });
    // });
    // function updateIntegrations() {
    //     $.ajax({
    //         method: "GET",
    //         url: "/getintegrations/",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         error: function error() {
    //             loadingOnScreenRemove();
    //             alertCustom('error', 'Ocorreu algum erro');
    //         },
    //         success: function success(response) {
    //             loadingOnScreenRemove();
    //             $("#project-integrated").html("");
    //             $("#project-integrated").html(response);
    //
    //             $(".card-edit").unbind('click');
    //             $('.card-edit').on('click', function () {
    //                 $(".modal_integracao_body").html("");
    //                 var project = $(this).attr('project');
    //                 $(".modal-title").html("Editar Integração com Hotzapp");
    //                 $(".modal_integracao_body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
    //                 var data = {projectId: project};
    //                 $.ajax({
    //                     method: "GET",
    //                     url: "/apps/hotzapp/" + project + "/edit",
    //                     headers: {
    //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                     },
    //                     error: function error() {
    //                         //
    //                     }, success: function success(response) {
    //                         $("#bt_integration").addClass('btn-update');
    //                         $("#bt_integration").text('Atualizar');
    //                         $("#btn-modal").show();
    //                         $(".modal_integracao_body").html(response);
    //                         $("#modal_add_integracao").modal('show');
    //
    //                         $('.check').on('click', function () {
    //                             if ($(this).is(':checked')) {
    //                                 $(this).val(1);
    //                             } else {
    //                                 $(this).val(0);
    //                             }
    //                         });
    //
    //                         $(".btn-update").unbind('click');
    //                         $(".btn-update").on('click', function () {
    //                             if ($('#link').val() == '') {
    //                                 alertCustom('error', 'Dados informados inválidos');
    //                                 return false;
    //                             }
    //                             var integrationId = $('#integration_id').val();
    //                             var form_data = new FormData(document.getElementById('form_update_integration'));
    //
    //                             $.ajax({
    //                                 method: "POST",
    //                                 url: "/apps/hotzapp/" + integrationId,
    //                                 headers: {
    //                                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
    //                                 },
    //                                 processData: false,
    //                                 contentType: false,
    //                                 cache: false,
    //                                 data: form_data,
    //                                 error: function (_error) {
    //                                     function error(_x) {
    //                                         return _error.apply(this, arguments);
    //                                     }
    //
    //                                     error.toString = function () {
    //                                         return _error.toString();
    //                                     };
    //
    //                                     return error;
    //                                 }(function (response) {
    //                                     if (response.status == '422') {
    //                                         for (error in response.responseJSON.errors) {
    //                                             alertCustom('error', String(response.responseJSON.errors[error]));
    //                                         }
    //                                     }
    //                                 }),
    //                                 success: function success(response) {
    //                                     updateIntegrations();
    //                                     alertCustom("success", response.message);
    //                                 }
    //                             });
    //                         });
    //                     }
    //                 });
    //             });
    //
    //             $(".delete-integration").unbind('click');
    //             $('.delete-integration').on('click', function (e) {
    //                 e.preventDefault();
    //                 var project = $(this).attr('project');
    //                 var card = $(this).parent().parent().parent().parent().parent();
    //                 card.find('.card-edit').unbind('click');
    //                 $.ajax({
    //                     method: "DELETE",
    //                     url: "/apps/hotzapp/" + project,
    //                     headers: {
    //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                     },
    //                     error: function (_error2) {
    //                         function error(_x2) {
    //                             return _error2.apply(this, arguments);
    //                         }
    //
    //                         error.toString = function () {
    //                             return _error2.toString();
    //                         };
    //
    //                         return error;
    //                     }(function (response) {
    //                         if (response.status == '422') {
    //                             for (error in response.responseJSON.errors) {
    //                                 alertCustom('error', String(response.responseJSON.errors[error]));
    //                             }
    //                         }
    //                     }),
    //                     success: function success(response) {
    //                         updateIntegrations();
    //                         alertCustom("success", response.message);
    //                     }
    //                 });
    //             });
    //         }
    //     });
    // }
});
