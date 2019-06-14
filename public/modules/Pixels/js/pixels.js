$(function () {

    var projectId = $("#project-id").val();

    $("#adicionar_pixel").on('click', function () {
        $("#modal_add_tamanho").addClass('modal_simples');
        $("#modal_add_tamanho").removeClass('modal-lg');

        $("#modal_add_body").html("<div style='text-align:center;'>Carregando...</div>");

        $.ajax({
            method: "GET",
            url: "/pixels/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal_add").hide();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function (data) {
                $('#modal_add_body').html(data);
                $("#cadastrar").unbind('click');
                $("#cadastrar").on('click', function () {
                    if ($("#nome").val() === '' || $("#cod_pixel").val() === '' || $("#plataforma").val() === '' || $("#status_pixel").val() === '') {
                        alertCustom('error', 'Dados informados inválidos');
                        return false;
                    }

                    $(".loading").css("visibility", "visible");

                    var formData = new FormData(document.getElementById('cadastrar_pixel'));
                    formData.append('projeto', projectId);

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
                        error: function () {
                            $("#modal_add_produto").hide();
                            $(".loading").css("visibility", "hidden");
                            alertCustom('error', 'Ocorreu algum erro');
                        }, success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom("success", "Pixel Adicionado!");
                            $("#modal_add_produto").hide();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        }
                    });
                });
            }
        });

    });


    
});
