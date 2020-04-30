$(document).ready(function () {
    let integrationTypeEnum = {
        admin: 'Admin',
        personal: 'Acesso Pessoal',
        external: 'Integração Externa',
    };
    let integrationTypeEnumBadge = {
        admin: 'default',
        personal: 'primary',
        external: 'warning',
    };
    let status = {
        active: 'Ativo',
        inactive: 'Inativo',
    };
    let statusBadge = {
        active: 'success',
        inactive: 'danger',
        // 3: 'warning',
    };

    refreshIntegrations();
    createIntegration();

    function refreshIntegrations(page = 1) {
        $.ajax({
            method: "GET",
            url: "/api/integrations?resume=true&page=" + page,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                // console.log(response);
                if (isEmpty(response.data)) {
                    $("#content-error").css('display', 'block');
                    $("#card-table-integrate").css('display', 'none');
                    $("#card-integration-data").css('display', 'none');
                } else {
                    $("#content-error").hide();
                    updateIntegrationTableData(response);
                    pagination(response, 'integrates');
                }
                refreshToken();
                deleteIntegration();
            }
        });
    }

    // Atualiza tabela de dados com a lista de integrações
    function updateIntegrationTableData(response) {

        $("#card-table-integrate").css('display', 'block');
        $("#card-integration-data").css('display', 'block');

        // $("#text-info").css('display', 'block');
        $("#card-table-integrate").css('display', 'block');
        $("#table-body-integrates").html('');

        $.each(response.data, function (index, value) {
            let disabled = ('active' !== value.status) ? ' disabled' : '';
            dados = '';
            dados += '<tr>';
            dados += '<td class="" style="vertical-align: middle;">';
            dados += '<strong class="mr-1">' + value.description + '</strong>';
            dados += '<br><small class="text-muted">Criada em: ' + value.register_date + '</small>';
            dados += '</td>';
            dados += '<td>';
            dados += '<span class="badge badge-' + integrationTypeEnumBadge[value.integration_type] + ' text-center">' + integrationTypeEnum[value.integration_type] + '</span>';
            dados += '</td>';
            dados += '<td>';
            dados += '<span class="badge badge-' + statusBadge[value.status] + ' text-center mt-1">' + status[value.status] + '</span>';
            dados += '</td>';
            //-----------------------------------
            //Access Token
            dados += '<td style="vertical-align: middle;">';
            dados += '<div class="input-group mb-2 mr-sm-2 mt-2">';
            dados += '<div class="input-group"><input type="text" class="form-control font-sm brr inptToken" id="inputToken' + value.id_code + '" value="' + value.access_token + '" disabled="disabled">';
            dados += '<div class="input-group-append"><div class="input-group-text p-1 p-lg-2">';
            dados += '<a href="#" class="btnCopiarLink" data-toggle="tooltip" title="Copiar token">';
            dados += '<i class="material-icons gray gradient"> file_copy </i>'
            dados += '</a></div></div></div>';
            dados += '</div>';
            //-----------------------------------
            dados += '</td>';
            dados += '<td class="text-center"><button class="btn pointer refresh-integration" style="background-color:transparent;" integration="' + value.id_code + '"' + disabled + ' title="Regerar token"><i class="material-icons gray gradient"> sync </i></button>';
            dados += '<button class="btn pointer delete-integration" style="background-color:transparent;" integration="' + value.id_code + '"' + disabled + ' title="Deletar token"><i class="material-icons gradient">delete</i></button></td>';
            dados += '</tr>';
            $("#table-body-integrates").append(dados);
        });

        $("#integrations_stored").html('' + response.resume.total + '');
        $("#integrations_active").html('' + response.resume.active + '');
        $("#posts_received").html('' + response.resume.received + '');
        $("#posts_sent").html('' + response.resume.sent + '');
    }

    // Regerar token integração
    function refreshToken() {
        $(".refresh-integration").unbind('click');
        $('.refresh-integration').on('click', function () {
            let integrationId = $(this).attr('integration');
            let url = 'api/integrations/' + integrationId + '/refreshtoken';
            $('#modal-refresh-integration').modal('show');
            $('#btn-refresh-integration').unbind('click');
            $('#btn-refresh-integration').on('click', function () {
                loadingOnScreen();
                $.ajax({
                    method: "POST",
                    url: url,
                    data: {integrationId: integrationId},
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    error: (response) => {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                    },
                    success: (response) => {
                        // console.log(response);
                        loadingOnScreenRemove();
                        refreshIntegrations();
                        alertCustom('success', response.message);
                    }
                });
            });
        });
    }

    // Excluir integração
    function deleteIntegration() {
        $('.delete-integration').unbind('click');
        $('.delete-integration').on('click', function () {
            let integrationId = $(this).attr('integration');
            let url = 'api/integrations/' + integrationId;
            $('#modal-delete-integration').modal('show');
            $('#btn-delete-integration').unbind('click');
            $('#btn-delete-integration').on('click', function () {
                loadingOnScreen();
                $.ajax({
                    method: "DELETE",
                    url: url,
                    data: {integrationId: integrationId},
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    error: (response) => {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                    },
                    success: (response) => {
                        // console.log(response);
                        loadingOnScreenRemove();
                        refreshIntegrations();
                        alertCustom('success', response.message);
                    }
                });
            });
        });
    }

    // Adiciona nova integração
    function createIntegration() {
        $("#store-integrate").unbind();
        $("#store-integrate").on('click', function () {
            loadingOnScreenRemove();
            $("#btn-save-integration").unbind();
            $("#btn-save-integration").on('click', function () {
                let description = $("#description").val();
                let tokenTypeEnum = $(".select-enum-list").val();
                if (description == '') {
                    alertCustom('error', 'O campo Descrição é obrigatório');
                } else {
                    loadingOnScreen();
                    storeIntegration(description, tokenTypeEnum);
                    ''
                }
            });
            $("#modal-integrate").modal('show');
        });
    }

    function storeIntegration(description, tokenTypeEnum) {
        $.ajax({
            method: "POST",
            url: "/api/integrations",
            data: {
                description: description,
                token_type_enum: tokenTypeEnum,
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                // console.log(response);
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                // console.log(response);
                $(".close").click();
                alertCustom('success', 'Integração criada com sucesso!');
                loadingOnScreenRemove();
                refreshIntegrations();
            }
        });
    }
    //Botao de copiar
    $(document).on("click", '.btnCopiarLink', function () {
        var tmpInput = $("<input>");
        $("body").append(tmpInput);
        var copyText = $(this).parent().parent().parent().find('.inptToken').val();
        tmpInput.val(copyText).select();
        document.execCommand("copy");
        tmpInput.remove();
        alertCustom('success', 'Token copiado!');
    });

    function pagination(response, model) {
        if (response.meta.last_page == 1) {
            $("#primeira_pagina_" + model).hide();
            $("#ultima_pagina_" + model).hide();
        } else {
            $("#pagination-" + model).html("");

            var first_page = "<button id='first_page' class='btn nav-btn'>1</button>";

            $("#pagination-" + model).append(first_page);

            if (response.meta.current_page == '1') {
                $("#first_page").attr('disabled', true).addClass('nav-btn').addClass('active');
            }

            $('#first_page').on("click", function () {
                refreshIntegrations(1);
            });

            for (x = 3; x > 0; x--) {

                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

                $('#page_' + (response.meta.current_page - x)).on("click", function () {
                    refreshIntegrations($(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page = "<button id='current_page' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page").attr('disabled', true).addClass('nav-btn').addClass('active');
            }
            for (x = 1; x < 4; x++) {

                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

                $('#page_' + (response.meta.current_page + x)).on("click", function () {
                    refreshIntegrations($(this).html());
                });
            }

            if (response.meta.last_page != '1') {
                var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page").attr('disabled', true).addClass('nav-btn').addClass('active');
                }

                $('#last_page').on("click", function () {
                    refreshIntegrations(response.meta.last_page);
                });
            }
        }
    }
});
