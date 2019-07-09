function deleteRow(element) {
    $(element).closest('tr').remove();
}

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
            error: function (response) {
                alertCustom('error', String(response.message));
                //$("#modal-add-body").html('nao encontrado');
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
                        dataType: "json",
                        data: form_data,
                        error: function (response) {
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom('error', String(response.errors[error]));
                                }
                            } else {
                                alertCustom('error', String(response.responseJSON.message));
                            }
                        },
                        success: function (response) {
                            alertCustom("success", response.message);
                            updateDomains();
                        }
                    });
                });

            }
        })
    });

    function updateDomains() {
        $(".loading").css("visibility", "visible");
        $("#domain-table-body").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");

        $.ajax({
            method: "GET",
            url: '/domains',
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function (response) {
                $("#domain-table-body").html(response.message);
            },
            success: function (response) {

                $("#domain-table-body").html('');

                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += '<td style="vertical-align: middle;">' + value.domain + '</td>';
                    dados += '<td style="vertical-align: middle;">' + value.ip_domain + '</td>';
                    dados += '<td>';
                    if (value.status === 3) {
                        dados += '<span class="badge badge-success">' + value.status_translated + '</span>';
                    } else {
                        dados += '<span class="badge badge-danger">' + value.status_translated + '</span>';
                    }

                    dados += '</td>';

                    dados += "<td style='vertical-align: middle'><a role='button' class='details-domain pointer' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal' style='margin-right:10px' ><i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                    dados += "<td style='vertical-align: middle'><a role='button' class='edit-domain pointer' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal' style='margin-right:10px' ><i class='material-icons gradient'>edit</i> </a></td>";
                    dados += "<td style='vertical-align: middle'><a role='button' class='delete-domain pointer' domain='" + value.id + "' data-target='#modal-delete' data-toggle='modal' style='margin-right:10px' ><i class='material-icons gradient'>delete_outline</i> </a></td>";

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

                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            $("#modal_add_size").addClass('modal-lg');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);
                            //$('#dominio-value').mask('#.###,#0', {reverse: true});

                            $("#bt_add_record").unbind('click');
                            $("#bt_add_record").on("click", function () {

                                $("#new_registers").after("<tr data-row='" + ($("#new_registers_table tr").length) + "' data-save='0'><td>" + $("#tipo_registro").val() + "</td><td>" + $("#nome_registro").val() + "</td><td>" + $("#valor_registro").val() + "</td><td><button type='button' data-row='" + ($("#new_registers_table tr").length) + "' class='btn btn-danger remove-record' onclick='deleteRow(this)'>Remover</button></td></tr>");

                                //$('#form-edit-domain').append('<input type="hidden" name="tipo_registro_' + qtd_novos_registros + '" id="tipo_registro_' + qtd_novos_registros + '" value="' + $("#tipo_registro").val() + '" />');

                                // $("#novos_registros").after("<tr registro='" + qtd_novos_registros + "'><td>" + $("#tipo_registro").val() + "</td><td>" + $("#nome_registro").val() + "</td><td>" + $("#valor_registro").val() + "</td><td><button type='button' class='btn btn-danger remover_entrada'>Remover</button></td></tr>");
                                //
                                // $('#editar_dominio').append('<input type="hidden" name="tipo_registro_' + qtd_novos_registros + '" id="tipo_registro_' + qtd_novos_registros + '" value="' + $("#tipo_registro").val() + '" />');
                                // $('#editar_dominio').append('<input type="hidden" name="nome_registro_' + qtd_novos_registros + '" id="nome_registro_' + qtd_novos_registros + '" value="' + $("#nome_registro").val() + '" />');
                                // $('#editar_dominio').append('<input type="hidden" name="valor_registro_' + qtd_novos_registros + '" id="valor_registro_' + (qtd_novos_registros++) + '" value="' + $("#valor_registro").val() + '" />');
                                //
                                // $(".remover_entrada").unbind("click");
                                //
                                // $(".remover_entrada").on("click", function () {
                                //
                                //     var novo_registro = $(this).parent().parent();
                                //     var id_registro = novo_registro.attr('registro');
                                //     novo_registro.remove();
                                //     alert(id_registro);
                                //     $("#tipo_registro_" + id_registro).remove();
                                //     $("#nome_registro_" + id_registro).remove();
                                //     $("#valor_registro_" + id_registro).remove();
                                // });
                                //
                                // $("#tipo_registro").val("A");
                                // $("#nome_registro").val("");
                                // $("#valor_registro").val("");
                            });

                            $(".remover_registro").unbind('click');
                            $(".remover_registro").on("click", function () {

                                var id_registro = $(this).attr('id-registro');

                                var row = $(this).parent().parent();

                                if ($(row).attr('data-save') == 0) {
                                    //nao esta salva, remover somente da tela
                                    $(row).remove();
                                } else {
                                    //esta salvo, remover do sistema
                                    $.ajax({
                                        method: "POST",
                                        url: "/domains/deleterecord",
                                        data: {
                                            id_record: id_registro,
                                            id_domain: dominio
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        error: function (response) {
                                            if (response.status === 422) {
                                                for (error in response.errors) {
                                                    alertCustom('error', String(response.errors[error]));
                                                }
                                            } else {
                                                alertCustom('error', String(response.responseJSON.message));
                                            }
                                        },
                                        success: function (response) {
                                            $(row).remove();
                                            alertCustom("success", response.message);
                                            updateDomains();
                                        },
                                    });
                                }

                            });

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {

                                var tbl = $('#new_registers_table tr').map(function (rowIdx, row) {
                                    if ((rowIdx > 0) && ($(row).attr('data-save') == 0)) {
                                        var rowObj = $(row).find('td').map(function (cellIdx, cell) {
                                            var retVal = {};
                                            retVal[cellIdx] = cell.textContent.trim();
                                            return retVal;
                                        }).get();
                                        var retVal = {};
                                        retVal[rowIdx] = rowObj;
                                    }

                                    return retVal;
                                }).get();

                                $.ajax({
                                    method: "PUT",
                                    url: "/domains/" + dominio,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        data: JSON.stringify(tbl),
                                        projectId: projectId,
                                        domain: dominio,
                                    },
                                    error: function (response) {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        } else {
                                            alertCustom("error", response.message);
                                        }
                                    },
                                    success: function (response) {
                                        alertCustom("success", response.message);
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
                            data: {
                                id: dominio
                            },
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (response) {
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                } else {
                                    alertCustom("error", response.responseJSON.message)
                                }
                            },
                            success: function (response) {
                                alertCustom("success", response.message);
                                updateDomains();
                            }

                        })

                    });
                })

            }
        });
    }

});
