$(document).ready(function () {

    var projectId = $("#project-id").val();

    $("#tab-domains").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        updateDomains();
    });
    updateDomains();

    $("#add-domain").on('click', function () {

        $("#modal-title").html('Cadastrar domínio');

        $.ajax({
            method: "GET",
            url: "/domains/create",
            data: {'project_id': projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal-add-body").html('nao encontrado');
            },
            success: function (response) {

                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();

                $("#modal-add-body").html(response);

                $(".btn-save").unbind();
                $(".btn-save").click(function () {

                    var form_data = new FormData(document.getElementById('form-add-domain'));
                    form_data.append('project_id', projectId);

                    $.ajax({
                        method: "POST",
                        url: "/domains",
                        processData: false,
                        contentType: false,
                        cache: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        data: form_data,
                        error: function (response) {
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom('error', String(response.errors[error]));
                                }
                            }
                        },
                        success: function (data) {
                            alertCustom("success", data.message);
                            updateDomains();
                        }
                    });
                });

            }
        })
    });

    function updateDomains() {

        $("#domain-table-body").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");

        $.ajax({
            method: "GET",
            url: '/domains',
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                $("#domain-table-body").html('Erro ao encontrar dados');
            },
            success: function (response) {

                $("#domain-table-body").html('');

                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += '<td class="text-center" style="vertical-align: middle;">' + value.domain + '</td>';
                    dados += '<td class="text-center" style="vertical-align: middle;">' + value.ip_domain + '</td>';
                    dados += '<td class="text-center">';
                    if (value.status === 1) {
                        dados += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        dados += '<span class="badge badge-danger">Desativado</span>';
                    }

                    dados += '</td>';

                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger details-domain'  domain='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger edit-domain'  domain='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger delete-domain'  domain='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";

                    dados += '</tr>';
                    $("#domain-table-body").append(dados);
                });

                if (response.data == '') {
                    $("#domain-table-body").html("<tr class='text-center'><td colspan='4' style='height: 70px; vertical-align: middle;'>Nenhum dominio encontrado</td></tr>")
                }

                $(".details-domain").unbind('click');
                $(".details-domain").on('click', function () {

                    var dominio = $(this).attr('domain');

                    $("#modal-title").html('Detalhes do dominio <br><hr>');
                    var data = {dominioId: dominio};

                    $("#btn-modal").hide();

                    $.ajax({
                        method: "GET",
                        url: "/domains/" + dominio,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            $("#modal-add-body").html(response);
                        }
                    });
                });

                $(".edit-domain").unbind('click');
                $(".edit-domain").on("click", function () {
                    $("#modal-add-body").html("");
                    var dominio = $(this).attr('domain');
                    $("#modal-title").html("Editar Domínio<br><hr>");
                    // $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {dominio: dominio};
                    $.ajax({
                        method: "GET",
                        url: "/domains/" + dominio + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            // $("#btn-save-updated").addClass('btn-update');
                            // $("#btn-save-updated").text('Atualizar');
                            // $("#btn-save-updated").show();
                            // $("#modal-add-body").html(response);
                            // changeType();
                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            $("#modal_add_size").addClass('modal-lg');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);
                            $('#dominio-value').mask('#.###,#0', {reverse: true});

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {

                                $.ajax({
                                    method: "PUT",
                                    url: "/domains/" + dominio,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        type: $("#dominio-type").val(),
                                        name: $("#dominio-name").val(),
                                        information: $("#dominio-information").val(),
                                        value: $("#dominio-value").val(),
                                        zip_code_origin: $("#dominio-zip-code-origin").val(),
                                        status: $("#dominio-status").val(),
                                        pre_selected: $("#dominio-pre-selected").val(),
                                    },
                                    error: function () {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    },
                                    success: function (data) {
                                        alertCustom("success", "dominio atualizado com sucesso");
                                        updateDomains();
                                    }
                                });

                            });
                        }
                    });
                });

                $(".delete-domain").on('click', function (event) {
                    event.preventDefault();
                    var dominio = $(this).attr('domain');

                    $("#modal_excluir_titulo").html("Remover dominio?");

                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on("click", function () {
                        $("#fechar_modal_excluir").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/domains/" + dominio,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                            },
                            success: function (data) {
                                alertCustom("success", "dominio Removido com sucesso");
                                updateDomains();
                            }

                        })

                    });
                })
            }
        });
    }

});
