function deleteRow(element) {
    $(element).closest('tr').remove();
}

var statusDomain = {
    1: 'warning',
    2: 'warning',
    3: 'success',
    4: 'danger'
}

var globalDomain = void 0;
var fromNew = 'false';
var newDomain = '';
var responseDomainsVar = void 0;

$(document).ready(function () {

    var domainName = void 0;
    var projectId = $("#project-id").val();

    $("#tab-domains").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        updateDomains();
    });

    updateDomains();

    $("#add-domain").on('click', function (e) {
        resetFooter();
        showElements('.modal-footer');
        loadOnModal('#modal-add-body');
        $('#btn-modal').hide();
        e.preventDefault();
        $.ajax({
            method: "GET",
            url: "/domains/create",
            data: {'project_id': projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {

                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom("error", response.message);
                }
                loadingOnScreenRemove();
            },
            success: function success(response) {
                modalAddDomain(response);
                loadingOnScreenRemove();
                $('form').submit(function (evt) {
                    evt.preventDefault();
                });

                $(".btn-save").unbind();
                $(".btn-save").click(function () {
                    // loadOnModal('#modal-add-body')
                    loadOnModalDomainEspecial('#modal-add-body');
                    $('#btn-modal').hide();
                    domainName = $('#name').val();
                    $('#btn-modal').attr('disabled', 'disabled');
                    var form_data = new FormData(document.getElementById('form-add-domain'));
                    form_data.append('project_id', projectId);

                    newDomain = $('.fildName').val();

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
                                alertCustom('error', String(response.responseJSON.message));
                            }
                            $('#modal-content').modal('hide');
                        }),
                        success: function success(response) {
                            globalDomain = response.data['id_code'];
                            fromNew = 'true';
                            // modalDomainEdit esta em função do globalDomain
                            var responseDomains = response;
                            modalDomainEdit(responseDomains, true);
                            updateDomains();
                        }
                    });
                });
            }
        });
    });

    function updateDomains(link = null) {

        loadOnTable('#domain-table-body', '#tabela-dominios');

        if (link == null) {
            link = '/domains?' + 'project=' + projectId;
        } else {
            link = '/domains' + link + '&project=' + projectId;
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function error(response) {
                $("#domain-table-body").html(response.message);
            },
            success: function success(response) {

                $('#domain-table-body').html('');

                if (response.data == '') {
                    $("#domain-table-body").html("<tr class='text-center'><td colspan='4' style='height: 70px; vertical-align: middle;'>Nenhum dominio encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        modalUpdateDomains(index, value);
                    });
                }

                pagination(response, 'domain', updateDomains);

                $(".details-domain").unbind('click');
                $(".details-domain").on('click', function () {
                    resetFooter();
                    hideElements('.modal-footer');
                    loadOnModal('#modal-add-body');
                    dnsDomains($(this).attr('domain'), $(this).attr('status'));
                });

                $(".edit-domain").unbind('click');
                $(".edit-domain").on("click", function () {
                    resetFooter();
                    showElements('.modal-footer', '5000');
                    loadOnModal('#modal-add-body');
                    $('#btn-modal').hide();
                    $("#modal-add-body").html("");
                    globalDomain = $(this).attr('domain');
                    modalDomainEdit();
                });

                $(".delete-domain").on('click', function (event) {
                    event.preventDefault();
                    var dominio = $(this).attr('domain');

                    resetFooter();
                    showElements('.modal-footer');

                    $("#modal_excluir_titulo").html("Remover dominio?");

                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on("click", function () {
                        $("#fechar_modal_excluir").click();
                        loadingOnScreen();
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
                            error: function (_error2) {
                                function error(_x3) {
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
                                } else {
                                    alertCustom("error", response.responseJSON.message);
                                }
                                loadingOnScreenRemove();
                            }),
                            success: function success(response) {
                                alertCustom("success", response.message);
                                loadingOnScreenRemove();
                                updateDomains();
                            }

                        });
                    });
                });
            }
        });
    }

    function dnsDomains(domain, status) {
        $('#btn-modal').hide();
        $.ajax({
            method: "GET",
            url: '/domains/getDomainData/' + domain,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function (_error3) {
                function error(_x4) {
                    return _error3.apply(this, arguments);
                }

                error.toString = function () {
                    return _error3.toString();
                };

                return error;
            }(function (response) {
                $('#modal-button-close').click();
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));
                }
            }),
            success: function success(response) {
                loadingOnScreenRemove();
                if (status == 3) {
                    modalVerified(response)
                } else {
                    modalVerify(response);
                }

                $('.btn-verifyDomain').on('click', function () {
                    loadOnModal('#modal-add-body');
                    $('#btn-modal').hide();
                    var domain = $(this).attr('domain');
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
                        error: function (_error4) {
                            function error(_x5) {
                                return _error4.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error4.toString();
                            };

                            return error;
                        }(function (response) {
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom('error', String(response.errors[error]));
                                }
                            } else {
                                // alertCustom('error', String(response.responseJSON.message));
                            }
                            modalErrorRegistry();
                        }),
                        success: function success(response) {
                            modalSuccessRegistry();
                        }
                    });
                });
            }
        });
    }

    function modalRegisterDomain(responseDomain) {
        loadingOnScreenRemove();
        modalVerify(responseDomain);
    }

    function modalDomainEdit(responseDomains, fromSave) {
        $('#especialModalTitle').remove();
        $('#btn-modal').removeAttr("data-dismiss");
        var data = {dominio: globalDomain};
        responseDomainsVar = responseDomains;
        $("#modal-title").html("Editar Domínio").show();
        $.ajax({
            method: "GET",
            url: "/domains/" + globalDomain + "/edit",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                //
            }, success: function success(response) {
                //predefinições da modal.
                modalEdit(response, fromSave);

                //adiciona ip do usuario no campo do nome
                if (newDomain != '' && !$('#shopify').data('shopfy')) {
                    $('#nome_registro').val(newDomain);
                }

                $(".remover_registro").unbind('click');
                $(".remover_registro").on("click", function () {
                    loadingOnScreen();
                    responseDomainsVar = responseDomainsVar;
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
                                id_domain: globalDomain
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (_error5) {
                                function error(_x6) {
                                    return _error5.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error5.toString();
                                };

                                return error;
                            }(function (response) {
                                if (response.status === 422) {
                                    for (error in response.errors) {
                                        alertCustom('error', String(response.errors[error]));
                                    }
                                } else {
                                    alertCustom('error', String(response.responseJSON.message));
                                }
                                loadingOnScreenRemove();
                            }),
                            success: function success(response) {
                                loadingOnScreenRemove();
                                $(row).remove();
                                alertCustom("success", response.message);
                                updateDomains();
                            }
                        });
                    }
                });

                /*ADICIONA CAMPO EXTRA PARA PRIORIDADE*/
                $('#tipo_registro').keyup(function () {
                    if ($(this).val() == "MX") {
                        addPriorityField();
                    } else {
                        removePriorityField();
                    }
                });
                $('option').click(function () {
                    if ($('#tipo_registro').val() == "MX") {
                        addPriorityField();
                    } else {
                        removePriorityField();
                    }
                });
                /*ADICIONA CAMPO EXTRA PARA PRIORIDADE*/

                $(".btn-update").unbind('click');
                $(".btn-update").on('click', function () {
                    loadOnModal("#modal-add-body");
                    $('#modal_add_size').removeClass('modal-lg');
                    $('#btn-modal').hide();
                    var tbl = $('#new_registers_table tr').map(function (rowIdx, row) {
                        if (rowIdx > 0 && $(row).attr('data-save') == 0) {
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
                        url: "/domains/" + globalDomain,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            data: JSON.stringify(tbl),
                            projectId: projectId,
                            domain: globalDomain
                        },
                        error: function (_error6) {
                            function error(_x7) {
                                return _error6.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error6.toString();
                            };

                            return error;
                        }(function (response) {
                            if (response.status == '422') {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            } else {
                                alertCustom("error", response.responseJSON.message);
                            }
                            loadingOnScreenRemove();
                            modalDomainEdit(responseDomainsVar);
                        }),
                        success: function success(response) {
                            loadingOnScreenRemove();
                            if (fromNew == 'true') {

                                dnsDomains(responseDomainsVar.data['id_code']);

                                updateDomains();

                                fromNew = "false";
                                newDomain = "";
                                responseDomainsVar = "";
                            } else {
                                alertCustom("success", response.message);
                                $('tr').removeClass('alert-info');
                                updateDomains();
                                modalDomainEdit();
                            }
                        }
                    });
                });
            }
        });
    }

    //SO ALTERAÇÃO DE HTML

    $('.modal').on('hidden.bs.modal', function () {
        resetFooter();
        resetHtml();
        $('.modal-footer').css('display', '');
        $('#btn-modal').show();
        $('#modal-title').show();
        $('#especialModalTitle').remove();
    });

    function resetHtml(whereToReset) {
        $(whereToReset).html('');
    }

    function resetFooter() {
        $('#btn-next').remove();
        $('#txt-modal-alert').remove();
    }

    function hideElements(reference) {
        var time = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '0';

        $(reference).hide(time);
    }
    function showElements(reference) {
        var time = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '0';

        $(reference).show(time);
    }

    function modalSuccessRegistry() {
        loadingOnScreenRemove();
        $('#modal-add-body').children().hide('slow');
        $('#btn-modal').hide();
        $('#modal-title').html('Tudo certo!');
        $('#modal-add-body').html('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' + '<h3 align="center"><strong>Domínio registrado</strong></h3>' + '<h4 align="center">Tudo pronto já podemos começar</h4>' + '<h4 align="center">O checkout transparente e o servidor de email já estão configurados apenas aguardando suas vendas.</h4>' + '<div style="width:100%;text-align:center;padding-top:3%">' + '<span class="btn btn-success" onclick="' + updateDomains() + '" data-dismiss="modal" style="font-size: 25px">Começar</span>' + '</div>');
    }

    function modalErrorRegistry() {
        loadingOnScreenRemove();
        $('#modal-add-body').children().hide('slow');
        $('#btn-modal').hide();
        $('#modal-title').html('Oppsssss...');
        $('#modal-add-body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Domínio ainda não registrado</strong></h3>' + '<h4 align="center">Parece que o seu dominio ainda não foi liberado</h4>' + '<h4 align="center">Seria bom conferir as configurações no seu provedor de dominio, caso tenha alguma duvida em como realizar a configuração <span class="red pointer" data-dismiss="modal" data-toggle="modal" data-target="#modal-detalhes-dominio">clique aqui</span></h4>' + '<div style="width:100%;text-align:center;padding-top:3%">' + '<span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span>' + '</div>');
    }

    function modalUpdateDomains(index, value) {
        var dados = '';
        dados += '<tr>';
        dados += '<td class="text-center">' + value.domain + '</td>';
        dados += '<td><span class="badge badge-' + statusDomain[value.status] + '">' + value.status_translated + '</span></td>';
        dados += "<td style='text-align:center;'>"
        dados += "<a role='button' class='mg-responsive details-domain pointer'       status='" + value.status + "' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i> </a>"
        dados += "<a role='button' class='mg-responsive edit-domain    pointer'       status='" + value.status + "' domain='" + value.id + "' data-target='#modal-content' data-toggle='modal'><i class='material-icons gradient'>edit</i> </a>"
        dados += "<a role='button' class='mg-responsive delete-domain  pointer'       status=''                     domain='" + value.id + "' data-target='#modal-delete'  data-toggle='modal'><i class='material-icons gradient'>delete_outline</i> </a>";
        dados += "</td>";
        dados += '</tr>';
        $("#domain-table-body").append(dados);
        $('#tabela-dominios').addClass('table-striped')
    }

    function modalVerify(responseDomain) {
        hideElements('.modal-footer');
        $('#modal-title').html('Verificação');
        $('#modal-add-body').children().hide('slow');
        $('#modal-add-body').html('');
        $('#modal-add-body').append('<div class="swal2-icon swal2-info swal2-animate-info-icon" style="display: flex;">i</div>' +
            '<h3 align="center"><strong>Domínio cadastrado</strong></h3>' +
            '<h4 align="center">Agora falta pouco</h4>' +
            '<h4 align="center">Você só precisa adicionar essas novas entradas <strong>DNS</strong>' +
            ' onde você registrou seu dominio' + responseDomain.data["domainHost"] + '. Logo apos clique em <strong style="color:green">verificar</strong>!</h4>' +
            '<div id="tableDomain" style="width:100%">' +
            '<table class="table table-striped">' + '<thead></thead>' +
            '<tbody id="tableDomainBody">' +
            '</tbody></table></div>' +
            '<div style="width:100%;text-align:center;padding-top:3%">' +
            '<button class="btn btn-success btn-verifyDomain" domain="' + responseDomain.data['id_code'] + '" style="font-size: 25px">Verificar</button>' +
            '</div>').show('slow');
        $.each(responseDomain.data['zones'], function (index, value) {
            $('#tableDomainBody').append('<tr>' + '<td class="table-title"><b>Novo servidor DNS :</b></td>' + '<td>' + value + '</td>' + '</tr>');
        });
        $('#modal-add-body').show('slow');
    }

    function modalVerified(responseDomain) {
        hideElements('.modal-footer');
        $('#modal-title').html('Verificação');
        $('#modal-add-body').children().hide('slow');
        $('#modal-add-body').html('');
        $('#modal-add-body').append('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' +
            '<h3 align="center"><strong>Domínio verificado</strong></h3>' +
            '<h4 align="center">Tudo pronto</h4>' +
            '<h4 align="center">O checkout transparente e o servidor de email já estão configurados apenas aguardando suas vendas.</h4>' +
            '<h6 align="center">Caso tenha problemas ou modifique algo no domínio, ultilize o botão abaixo para verificar se está tudo certo</h6>'+
            '<div style="width:100%;text-align:center;padding-top:3%">' +
            '<button class="btn btn-success btn-verifyDomain" domain="' + responseDomain.data['id_code'] + '" style="font-size: 25px">Verificar</button>' +
            '</div>').show('slow');
        $('#modal-add-body').show('slow');
    }

    function modalEdit(response, fromSave) {
        $("#btn-modal").removeAttr('disabled');
        $("#btn-modal").addClass('btn-update');
        if (fromSave == true) {
            $("#btn-modal").text('Proximo');
        } else {
            $("#btn-modal").text('Atualizar');
        }
        $("#modal_add_size").addClass('modal-lg');
        $('#btn-modal').removeAttr("data-dismiss");
        // $("#btn-modal").show();
        $("#modal-add-body").html(response);
        loadingOnScreenRemove();

        $("#bt_add_record").unbind('click');
        $('#bt_add_record').on("click", function (e) {
            e.preventDefault();
            if ($('#nome_registro').val() != '' && $('#valor_registro').val() != '') {

                if ($('#new_registers_table').html() == undefined) {
                    $('#divCustomDomain').html("<table id='new_registers_table' class='table table-hover table-bordered table-stripped unify' style='table-layout: fixed;'>" + "<thead>" + "<tr>" + "<th class='col-2'>Tipo</th>" + "<th class='col-2'>Nome</th>" + "<th class='col-6'>Conteúdo</th>" + "<th class='col-2'></th>" + "</tr>" + "</thead>" + "<tbody id='new_registers'>" + "</tbody>" + "</table>");
                }
                $("#new_registers").after("<tr class='alert-info' data-row='" + $("#new_registers_table tr").length + "' data-save='0'>" + "<td>" + $("#tipo_registro").val() + "</td>" + "<td>" + $("#nome_registro").val() + "</td>" + "<td>" + $("#valor_registro").val() + "</td>" + "<td hidden='hidden'>" + $("#valor_prioridade").val() + "</td>" + "<td class='col-12 text-center align-middle'>" + "<button type='button' data-row='" + $("#new_registers_table tr").length + "' class='btn btn-danger remove-record' onclick='deleteRow(this)'>Remover</button>" + "</td></tr>");
                $('#nome_registro').val('');
                $('#valor_registro').val('');
            } else {
                alertCustom('error', 'Os campos Nome e Valor devem ser preenchidos');
            }
        });
    }

    function modalAddDomain(response) {
        $("#modal-title").text('Novo domínio');
        $('#btn-modal').removeAttr('data-dismiss');
        $("#btn-modal").addClass('btn-save');
        $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
        $("#modal-add-body").html(response);
    }

    function addPriorityField() {
        if ($('#valor_prioridade').html() == undefined) {
            $('#valor_registro').parent().remove();
            $('#nome_registro').parent().remove();
            $('#tipo_registro').parent().after('<div class="col-sm-12 col-md-5 col-lg-3 mb-3">' +
                '<input id="nome_registro" class="input-pad" placeholder="Nome"></div>' +
                '<div class="col-sm-12 col-md-5 col-lg-3 mb-3">' +
                '<input id="valor_registro" class="input-pad" placeholder="Valor"></div>' +
                '<div class="col-sm-12 col-md-2 col-lg-2 mb-3">' +
                '<input id="valor_prioridade" class="input-pad" data-mask="0#" placeholder="Prioridade"></div>');
        }
        $('#valor_prioridade').mask('0#');
    }

    function removePriorityField() {
        console.log($('#valor_registro').html());
        if ($('#valor_prioridade').html() != undefined) {
            $('#valor_registro, #valor_prioridade, #nome_registro').parent().remove();
            $('#tipo_registro').parent().after('<div class="col-sm-12 col-md-6 col-lg-4 mb-3">' +
                '<input id="nome_registro" class="input-pad" placeholder="Nome"></div>' +
                '<div class="col-sm-12 col-md-6 col-lg-4 mb-3">' +
                '<input id="valor_registro" class="input-pad" placeholder="Valor"></div>');
        }
    }

    function pagination(response) {

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        if (response.meta.last_page == '1') {
            return false;
        }

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").attr('disabled', true).addClass('nav-btn').addClass('active');
        }

        $('#primeira_pagina').unbind("click");
        $('#primeira_pagina').on("click", function () {
            updateDomains('?page=1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                updateDomains('?page=' + $(this).html());
            });
        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

            $("#pagination").append(pagina_atual);

            $("#pagina_atual").attr('disabled', true).addClass('nav-btn').addClass('active');
        }

        for (x = 1; x < 4; x++) {

            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                updateDomains('?page=' + $(this).html());
            });
        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
                $("#ultima_pagina").addClass('nav-btn');
                $("#ultima_pagina").addClass('active');
            }

            $('#ultima_pagina').on("click", function () {
                updateDomains('?page=' + response.meta.last_page);
            });
        }

    }
});

function loadOnModalDomainEspecial(whereToLoad) {

    $(whereToLoad).children().hide('fast');
    $('#modal-title').after('<h3 id="especialModalTitle" style="weight:bold; color:black"></h3>');
    $('#modal-title').hide();
    $(whereToLoad).append("<div id='loaderModal' class='loadingModal'>" + "<div class='loaderModal'>" + "</div>" + "</div>");
    $('#loadingOnScreen').append("<div class='blockScreen'></div>");

    $('#especialModalTitle').html('Iniciando ... ');

    setTimeout(function () {
        $('#especialModalTitle').html('Configurando domínio');
    }, 1000);
    setTimeout(function () {
        $('#especialModalTitle').html('Configurando entradas DNS');
    }, 6000);
    setTimeout(function () {
        $('#especialModalTitle').html('Preparando servidores de Email');
    }, 13000);
    setTimeout(function () {
        $('#especialModalTitle').html('Preparando checkout transparente');
    }, 20000);
    setTimeout(function () {
        $('#especialModalTitle').html('Finalizando ... ');
    }, 25000);
}
