$(document).ready(function () {

    var projectId = $("#project-id").val();

    $("#tab-domains").on('click', function () {
        updateDomains();
    });
    updateDomains();

    $("#add-domain").on('click', function () {

        $("#modal_add_title").html('Cadastrar domínio');
 
        $.ajax({
            method: "GET",
            url: "/domains/create",
            data: {'project_id': projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal_add_body").html('nao encontrado');
            },
            success: function (response) {

                $("#modal_add_body").html(response);

                $(".btn-save").unbind();
                $(".btn-save").click(function () {

                    $.ajax({
                        method: "POST",
                        url: "/domains/",
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
                            project: projectId,
                        },
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

                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger detalhes-dominio'  domain='" + value.id + "' data-target='#modal-detalhes-dominio' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger editar-dominio'  domain='" + value.id + "' data-target='#modal-detalhes-dominio' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger excluir-dominio'  domain='" + value.id + "'  data-toggle='modal' data-target='#modal_excluir' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";

                    dados += '</tr>';
                    $("#domain-table-body").append(dados);
                });

                if (response.data === '') {
                    $("#domain-table-body").html("<tr class='text-center'><td colspan='4' style='height: 70px; vertical-align: middle;'>Nenhum dominio encontrado</td></tr>")
                }

                $(".detalhes-dominio").unbind('click');
                $(".detalhes-dominio").on('click', function () {

                    var dominio = $(this).attr('domain');

                    $("#modal-dominio-titulo").html('Detalhes do dominio <br><hr>');
                    var data = {dominioId: dominio};

                    $("#btn-save-updated").hide();

                    $.ajax({
                        method: "GET",
                        url: "/domains/" + dominio,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            $("#modal-dominio-body").html(response);
                        }
                    });
                });

                $(".editar-dominio").unbind('click');
                $(".editar-dominio").on("click", function () {
                    var dominio = $(this).attr('dominio');

                    $("#modal-dominio-titulo").html("Editar dominio<br><hr>");
                    $("#modal-dominio-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");

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
                            $("#btn-save-updated").addClass('btn-update');
                            $("#btn-save-updated").text('Atualizar');
                            $("#btn-save-updated").show();
                            $("#modal-dominio-body").html(response);
                            changeType();
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

                $(".excluir-dominio").on('click', function (event) {
                    event.preventDefault();
                    var dominio = $(this).attr('dominio');

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
