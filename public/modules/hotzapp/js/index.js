$(document).ready(function () {

    $('.check').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
    $(".card-edit").unbind('click');
    $('.card-edit').on('click', function () {
        var project = $(this).attr('project');
        $(".modal_integracao_body").html("");
        $(".modal-title").html("Editar Integração com Hotzapp");
        $(".modal_integracao_body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
        var data = {projectId: project};
        $.ajax({
            method: "GET",
            url: "/apps/hotzapp/" + project + "/edit",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            }, success: function (response) {
                $("#bt_add_integration").addClass('btn-update');
                $("#bt_add_integration").text('Atualizar');
                $("#btn-modal").show();
                $(".modal_integracao_body").html(response);
                $("#modal_add_integracao").modal('show');

                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });

                $(".btn-update").unbind('click');
                $(".btn-update").on('click', function () {
                    var integrationId = $('#integration_id').val();
                    var form_data = new FormData(document.getElementById('form_update_integration'));

                    $.ajax({
                        method: "POST",
                        url: "/apps/hotzapp/" + integrationId,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: form_data,
                        error: function (response) {
                            loadingOnScreenRemove()
                            if (response.status == '422') {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            }
                        },
                        success: function (response) {
                            loadingOnScreenRemove()
                            alertCustom("success", response.message);
                        }
                    });

                });
            }
        });
    });
    $("#bt_add_integration").on("click", function () {

        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        loadingOnScreen();

        var form_data = new FormData(document.getElementById('form_add_integration'));

        $.ajax({
            method: "POST",
            url: "/apps/hotzapp",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove()
                alertCustom('error', response.responseJSON.message);//'Ocorreu algum erro'
            },
            success: function (response) {
                loadingOnScreenRemove()
                alertCustom('success', response.message);
                // window.location.href='/apps/hotzapp/';
            },
        });

    });
});
