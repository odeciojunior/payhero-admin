function deleteRow(element) {
    $(element).closest('tr').remove();
}

$(document).ready(function () {
    let domainName;
    var projectId = $("#project-id").val();

    $("#tab-domains").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        updateDomains();
    });

    updateDomains();

    $("#add-domain").on('click', function (e) {
        loadOnModal('#modal-add-body');
        e.preventDefault();

        $("#modal-title").html('Novo domínio');
        $('#btn-modal').removeAttr('data-dismiss')

        $.ajax({
            method: "GET",
            url: "/domains/create",
            data: {'project_id': projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                alertCustom('error', String(response.message));
            },
            success: function (response) {

                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
                $("#modal-add-body").html(response);

                $('form').submit(function (evt) {
                    evt.preventDefault();
                });

                $(".btn-save").unbind();
                $(".btn-save").click(function () {
                    loadOnModal('#modal-add-body')
                    domainName = $('#name').val();
                    $('#btn-modal').attr('disabled', 'disabled');
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
                            // loadingOnScreenRemove()
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom('error', String(response.errors[error]));
                                }
                            } else {
                                alertCustom('error', String(response.responseJSON.message));
                            }
                            $('#modal-content').modal('hide');
                        },
                        success: function (response) {
                            modalRegisterDomain(response)
                            updateDomains();
                        }
                    });
                });

            }
        })
    });

    function updateDomains() {
        loadOnTable('#domain-table-body', '#tabela-dominios');

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
                    dados += "<td style='min-width:200px;'>" +
                        "<a role='button' class='details-domain pointer mr-30' status='" + value.status + "' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i> </a>" +
                        "<a role='button' class='edit-domain pointer' status='" + value.status + "' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal'><i class='material-icons gradient'>edit</i> </a>" +
                        "<a role='button' class='delete-domain pointer ml-30' domain='" + value.id + "' data-target='#modal-delete' data-toggle='modal'><i class='material-icons gradient'>delete_outline</i> </a>"
                    "</td>";
                    // dados += "<td style='vertical-align: middle'><a role='button' class='details-domain pointer' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal' style='margin-right:10px' ><i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                    // dados += "<td style='vertical-align: middle'><a role='button' class='edit-domain pointer' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal' style='margin-right:10px' ><i class='material-icons gradient'>edit</i> </a></td>";
                    // dados += "<td style='vertical-align: middle'><a role='button' class='delete-domain pointer' domain='" + value.id + "' data-target='#modal-delete' data-toggle='modal' style='margin-right:10px' ><i class='material-icons gradient'>delete_outline</i> </a></td>";

                    dados += '</tr>';
                    $("#domain-table-body").append(dados);
                });

                if (response.data == '') {
                    $("#domain-table-body").html("<tr class='text-center'><td colspan='4' style='height: 70px; vertical-align: middle;'>Nenhum dominio encontrado</td></tr>")
                }

                $(".details-domain").unbind('click');
                $(".details-domain").on('click', function () {

                        if ($(this).attr('status') == 1) {
                            modalRegisterDomainButton($(this).attr('domain'));
                            loadOnModal('#modal-add-body')
                        } else {
                            loadOnModal('#modal-add-body');
                            modalSuccessRegistry();
/*                            $('#modal_add_size').removeClass('modal-lg');
                            $("#modal-title").html('Detalhes do domínio');
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

                                    $(".refresh-domain").unbind('click');
                                    $(".refresh-domain").on('click', function () {
                                        var domain = $(this).attr('data-domain');
                                        $.ajax({
                                            method: "POST",
                                            url: '/domains/recheck/',
                                            data: {
                                                domain: domain,
                                                project: projectId
                                            },
                                            headers: {
                                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                            },
                                            error: function (response) {
                                                $('#modal-button-close').click();
                                                if (response.status === 422) {
                                                    for (error in response.errors) {
                                                        alertCustom('error', String(response.errors[error]));
                                                    }
                                                } else {
                                                    alertCustom('error', String(response.responseJSON.message));
                                                }
                                            },
                                            success: function (response) {
                                                $('#modal-button-close').click();
                                                alertCustom("success", response.message);
                                            }
                                        });

                                    });
                                }
                            });*/
                        }
                    }
                );

                $(".edit-domain").unbind('click');
                $(".edit-domain").on("click", function () {
                    if ($(this).attr('status') == 1) {
                        modalRegisterDomainButton($(this).attr('domain'));
                        loadOnModal('#modal-add-body')
                    } else {
                        loadOnModal('#modal-add-body')
                        $("#modal-add-body").html("");
                        var dominio = $(this).attr('domain');
                        $("#modal-title").html("Editar Domínio");
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
                                $('#bt_add_record').on("click", function (e) {
                                    e.preventDefault();
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
                                    loadOnModel('#model-add-body');
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

                                    loadingOnScreen()

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
                                            loadingOnScreenRemove()
                                            if (response.status == '422') {
                                                for (error in response.responseJSON.errors) {
                                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                                }
                                            } else {
                                                alertCustom("error", response.message);
                                            }
                                        },
                                        success: function (response) {
                                            loadingOnScreenRemove()
                                            alertCustom("success", response.message);
                                            updateDomains();
                                        }
                                    });

                                });
                            }
                        });
                    }
                });

                $(".delete-domain").on('click', function (event) {
                    event.preventDefault();
                    var dominio = $(this).attr('domain');

                    $("#modal_excluir_titulo").html("Remover dominio?");

                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on("click", function () {
                        $("#fechar_modal_excluir").click();
                        loadingOnScreen()
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
                                loadingOnScreenRemove()
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                } else {
                                    alertCustom("error", response.responseJSON.message)
                                }
                            },
                            success: function (response) {
                                loadingOnScreenRemove()
                                alertCustom("success", response.message);
                                updateDomains();
                            }

                        })

                    });
                })
            }
        });
    }

    function modalRegisterDomainButton(domain) {
        console.log(domain);
        $.ajax({
            method: "GET",
            url: '/domains/getDomainData/' + domain,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function (response) {
                $('#modal-button-close').click();
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));
                }
            },
            success: function (response) {
                modalRegisterDomain(response);
            }
        });
    }

    function modalRegisterDomain(responseDomain) {
        $('#modal-title').html('Verificação');
        $('#btn-modal').removeAttr('disabled');
        $('#modal-add-body').children().hide('slow');
        $('#modal-add-body').delay(2000).html('');
        $('#modal-add-body').append('<div class="swal2-icon swal2-info swal2-animate-info-icon" style="display: flex;">i</div>' +
            '<h3 align="center"><strong>Dominio cadastrado</strong></h3>' +
            '<h4 align="center">Agora falta pouco</h4>' +
            '<h4 align="center">Você só precisa adicionar essas novas entradas <strong>DNS</strong> onde você registrou seu dominio. Logo apos clique em <strong style="color:green">verificar</strong>!</h4>' +
            '<div id="tableDomain" style="width:100%">' +
            '<table class="table table-striped">' +
            '<thead></thead>' +
            '<tbody id="tableDomainBody">' +
            '</tbody>' +
            '</table>' +
            '</div>' +
            '<div style="width:100%;text-align:center;padding-top:3%">' +
            '<button class="btn btn-success btn-verifyDomain" domain="' + responseDomain.data['id_code'] + '" style="font-size: 25px">Verificar</button>' +
            '</div>').show('slow');
        $.each(responseDomain.data['zones'], function (index, value) {
            $('#tableDomainBody').append('<tr>' +
                '<td class="table-title"><b>Novo servidor DNS :</b></td>' +
                '<td>' + value + '</td>' +
                '</tr>')
        });
        $('#btn-modal').hide();

        $('.btn-verifyDomain').on('click',function(){
            var domain = $(this).attr('domain');
            console.log('chego')
            loadOnModal('#modal-add-body');
            $.ajax({
                method: "POST",
                url: '/domains/recheck/',
                data: {
                    domain: domain,
                    project: projectId
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                error: function (response) {
                    if (response.status === 422) {
                        for (error in response.errors) {
                            alertCustom('error', String(response.errors[error]));
                        }
                    } else {
                        // alertCustom('error', String(response.responseJSON.message));
                    }
                    modalErrorRegistry();
                },
                success: function (response) {
                    modalSuccessRegistry();
                }
            });
        });
    }

    function modalSuccessRegistry() {
        $('#modal-add-body').children().hide('slow');
        $('#btn-modal').hide();
        $('#modal-title').html('Tudo certo!');
        $('#modal-add-body').html('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' +
            '<h3 align="center"><strong>Dominio registrado</strong></h3>' +
            '<h4 align="center">Tudo pronto já podemos começar</h4>' +
            '<h4 align="center">O checkout transparente e o servidor de email já estão configurados apenas aguardanddo suas vendas.</h4>' +
            '<div style="width:100%;text-align:center;padding-top:3%">' +
            '<span class="btn btn-success" onclick="'+updateDomains()+'" data-dismiss="modal" style="font-size: 25px">Começar</span>' +
            '</div>');
    }

    function modalErrorRegistry() {
        $('#modal-add-body').children().hide('slow');
        $('#modal-title').html('Oppsssss...');
        $('#modal-add-body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' +
            '<h3 align="center"><strong>Dominio ainda não registrado</strong></h3>' +
            '<h4 align="center">Parece que o seu dominio ainda não foi liberado</h4>' +
            '<h4 align="center">Seria bom conferir as configurações no seu provedor de dominio, caso tenha alguma duvida em como realizar a configuração <span class="red pointer" data-dismiss="modal" data-toggle="modal" data-target="#modal-detalhes-dominio">clique aqui</span></h4>' +
            '<div style="width:100%;text-align:center;padding-top:3%">' +
            '<span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span>' +
            '</div>');
    }

    function openHelper(){
        $('#modal-content').modal('hide');
        $('#modal-detalhes-dominio').modal('show');
    }


})
;
