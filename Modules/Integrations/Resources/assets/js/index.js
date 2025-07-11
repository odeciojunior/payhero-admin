$(document).ready(function () {

    $('.company-navbar').change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        loadOnAny('#page-integrates');
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                companiesAndProjects = data2
                $('.company_name').val(companiesAndProjects.company_default_fullname);
                onlyData();
            });
        });
    });

    var companiesAndProjects = ''

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data
        $('.company_name').val(companiesAndProjects.company_default_fullname);
        refreshIntegrations();
        createIntegration();
    });

    function onlyData() {

        $("#content-error").css('display', 'none');
        $("#content-script").css('display', 'none');
        $("#card-table-integrate").css('display', 'none');
        $("#pagination-integrates").css('display', 'none');
        $.ajax({
            method: "GET",
            url: "/api/integrations?resume=true&page=1&company_id=" + $('.company-navbar').val(),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadOnAny('#page-integrates', true);
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    $(".page-header").find('.store-integrate').css('display', 'none');
                    $("#content-error").find('.store-integrate').css('display', 'block');

                    $("#content-error").css('display', 'block');
                    $("#content-script").css('display', 'none');
                    $("#card-table-integrate").css('display', 'none');
                    $("#card-integration-data").css('display', 'none');
                } else {
                    $(".page-header").find('.store-integrate').css('display', 'block');
                    $("#content-error").find('.store-integrate').css('display', 'none');
                    $("#content-error").hide();
                    updateIntegrationTableData(response);
                    pagination(response, 'integrates');
                }

                getIntegration();
                refreshToken();
                deleteIntegration();
                loadOnAny('#page-integrates', true);
            }
        });
    }

    let integrationTypeEnum = {
        external: 'Integração externa',
        checkout_api: 'Checkout API'
    };
    let integrationTypeEnumBadge = {
        admin: 'default',
        personal: 'default',
        external: 'success',
        checkout_api: 'primary'
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

    function refreshIntegrations(page = 1) {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/integrations?resume=true&page=" + page + "&company_id=" + $('.company-navbar').val(),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();//loadOnAny('#page-integrates',true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    pagination(response, 'integrates');

                    $(".page-header").find('.store-integrate').css('display', 'none');
                    $("#content-error").find('.store-integrate').css('display', 'block');

                    $("#content-error").css('display', 'block');
                    $("#content-script").css('display', 'none');
                    $("#card-table-integrate").css('display', 'none');
                    $("#card-integration-data").css('display', 'none');
                } else {
                    $(".page-header").find('.store-integrate').css('display', 'block');
                    $("#content-error").find('.store-integrate').css('display', 'none');

                    $("#content-error").hide();
                    updateIntegrationTableData(response);
                    pagination(response, 'integrates');
                }

                getIntegration();
                refreshToken();
                deleteIntegration();
                loadingOnScreenRemove();//loadOnAny('#page-integrates',true);
            }
        });
    }

    // Atualiza tabela de dados com a lista de integrações
    function updateIntegrationTableData(response) {

        $("#content-script").css("display", "block");
        $("#card-table-integrate").css("display", "block");
        $("#card-integration-data").css("display", "block");

        // $("#text-info").css('display', 'block');
        $("#card-table-integrate").css("display", "block");
        $("#table-body-integrates").html("");

        if (response.data.length > 0 && (response.data.findIndex((e) => e.integration_type_enum == "4") != -1 || response.data.findIndex((e) => e.integration_type_enum == "5") != -1)) {
            $("#content-script").show();
            $("#input-url-antifraud").val(response.data[0].antifraud_url);
        } else {
            $("#content-script").hide();
            $("#input-url-antifraud").val("");
        }

        $.each(response.data, function (index, value) {
            let disabled = "active" !== value.status ? " disabled" : "";
            dados = "";
            dados += "<tr>";

            dados += '<td>';
            dados += '<div class="fullInformation-api ellipsis-text">' + value.description + '</div>';
            if (value.integration_type_enum !== 5) {
                dados += '<div><span class="subdescription font-size-12">' + integrationTypeEnum[value.integration_type] + '</span></div>';
            }
            dados += '<span class="subdescription font-size-12">Criada em ' + value.register_date + '</span> <div class="container-tooltips-api"></div>';
            dados += '</td>';

            dados += '<td style="vertical-align: middle;">';
            dados += '<div class="input-group input-group-lg">';
            dados +=
                '<input type="text" class="form-control font-sm brr inptToken" id="inputToken-' +
                value.id_code +
                '" value="' +
                value.access_token +
                '" disabled="disabled" style="background: #F1F1F1;">';
            dados += '<div class="input-group-append">';
            dados +=
                '<button class="btn btn-primary bg-white btnCopiarLink" data-code="' +
                value.id_code +
                '" type="button" data-placement="top" data-toggle="tooltip" title="Copiar token" style="width: 48px; height: 48px;">';
            dados += '<img src="/build/global/img/icon-copy-b.svg">';
            dados += "</button>";
            dados += "</div>";
            dados += "</div>";
            dados += "</td>";

            dados += '<td class="text-right">';
            dados +=
                '<button class="btn pointer edit-integration" style="background-color:transparent;" integration="' +
                value.id_code +
                '"' +
                disabled +
                ' title="Editar integração"><span class=""><img src="/build/global/img/pencil-icon.svg"/></span></button>';
            //dados += '<button class="btn pointer refresh-integration" style="background-color:transparent;" integration="' + value.id_code + '"' + disabled + ' title="Regerar token"><span class="o-reload-1"></span></button>';
            dados +=
                '<button class="btn pointer delete-integration" style="background-color:transparent;" integration="' +
                value.id_code +
                '"' +
                disabled +
                ' title="Deletar token"><span class=""><img src="/build/global/img/icon-trash-tale.svg"/></span></button>';
            dados += "</td>";

            dados += "</tr>";
            $("#table-body-integrates").append(dados);
        });

        $("#integrations_stored").html("" + response.resume.total + "");
        $("#integrations_active").html("" + response.resume.active + "");
        $("#posts_received").html("" + response.resume.received + "");
        $("#posts_sent").html("" + response.resume.sent + "");

        $('.fullInformation-api').bind('mouseover', function () {
            var $this = $(this);

            if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
                $this.attr({
                    'data-toggle': "tooltip",
                    'data-placement': "top",
                    'data-title': $this.text()
                }).tooltip({ container: ".container-tooltips-api" })
                $this.tooltip("show")
            }
        });
    }

    // Obtem os dados da integração
    function getIntegration() {
        $(".edit-integration").unbind("click");
        $(".edit-integration").on("click", function () {
            $("#modal-edit-integration").find('input[name="description"]').val("");
            $("#modal-edit-integration").find('input[name="postback"]').val("");

            let integration_id = $(this).attr("integration");
            $("#modal-edit-integration").modal("show");
            $.ajax({
                method: "GET",
                url: "/api/integrations/" + integration_id,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    if (!isEmpty(response)) {
                        if (response.token_type_enum == 4) {
                            $("#modal-edit-integration").find('input[name="postback"]').val(response.postback);
                            $("#modal-edit-integration").find(".postback-container").show();
                        } else {
                            $("#modal-edit-integration").find(".postback-container").hide();
                            $("#modal-edit-integration").find('input[name="postback"]').val("");
                        }

                        $("#modal-edit-integration").find('input[name="description"]').val(response.description);
                        $("#modal-edit-integration")
                            .find('input[name="token_type_enum"]')
                            .val(response.token_type_enum);

                        editIntegration(integration_id);
                    } else {
                        alertCustom("error", "Erro ao obter dados da integração");
                    }
                },
            });
        });
    }

    // Edita os dados da integração
    function editIntegration(integration_id) {
        $("#btn-edit-integration").unbind("click");
        $("#btn-edit-integration").on("click", function () {
            let description = $("#modal-edit-integration").find('input[name="description"]').val();
            let token_type_enum = $("#modal-edit-integration").find('input[name="token_type_enum"]').val();
            let postback = $("#modal-edit-integration").find('input[name="postback"]').val();

            loadingOnScreen();
            $.ajax({
                method: "PUT",
                url: "api/integrations/" + integration_id,
                data: {
                    description: description,
                    postback: postback,
                    token_type_enum: token_type_enum,
                },
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: (response) => {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $(".close").click();
                    loadingOnScreenRemove();
                    refreshIntegrations();
                    alertCustom("success", response.message);
                },
            });
        });
    }

    // Regerar token integração
    function refreshToken() {
        $(".refresh-integration").unbind("click");
        $(".refresh-integration").on("click", function () {
            let integrationId = $(this).attr("integration");
            let url = "api/integrations/" + integrationId + "/refreshtoken";
            $("#modal-refresh-integration").modal("show");
            $("#btn-refresh-integration").unbind("click");
            $("#btn-refresh-integration").on("click", function () {
                loadingOnScreen();
                $.ajax({
                    method: "POST",
                    url: url,
                    data: { integrationId: integrationId },
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr("content"),
                        Accept: "application/json",
                    },
                    error: (response) => {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                    },
                    success: (response) => {
                        loadingOnScreenRemove();
                        refreshIntegrations();
                        alertCustom("success", response.message);
                    },
                });
            });
        });
    }

    // Excluir integração
    function deleteIntegration() {
        $(".delete-integration").unbind("click");
        $(".delete-integration").on("click", function () {
            let integrationId = $(this).attr("integration");
            let url = "api/integrations/" + integrationId;
            $("#modal-delete-integration").modal("show");
            $("#btn-delete-integration").unbind("click");
            $("#btn-delete-integration").on("click", function () {
                loadingOnScreen();
                $.ajax({
                    method: "DELETE",
                    url: url,
                    data: { integrationId: integrationId },
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr("content"),
                        Accept: "application/json",
                    },
                    error: (response) => {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                    },
                    success: (response) => {
                        loadingOnScreenRemove();
                        refreshIntegrations();
                        alertCustom("success", response.message);
                    },
                });
            });
        });
    }

    // Adiciona nova integração
    function createIntegration() {
        $(".store-integrate").unbind();
        $(".store-integrate").on("click", function () {
            loadingOnScreenRemove();
            $("#btn-save-integration").unbind();
            $("#btn-save-integration").on("click", function () {
                let description = $("#modal-integrate").find("input[name='description']").val();
                let companyHash = $('.company-navbar').val();
                if (description == '') {
                    alertCustom('error', 'O campo Descrição é obrigatório');
                } else {
                    loadingOnScreen();
                    storeIntegration(description, companyHash);
                }
            });
            $("#modal-integrate").modal("show");
        });
    }

    function storeIntegration(description, companyHash) {
        $.ajax({
            method: "POST",
            url: "/api/integrations",
            data: {
                description: description,
                company_id: companyHash,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                $(".close").click();
                alertCustom("success", "Integração criada com sucesso!");
                loadingOnScreenRemove();
                refreshIntegrations();
            },
        });
    }

    //Botao de copiar
    $(document).on("click", ".btnCopiarLink", function () {
        var tmpInput = $("<input>");
        $("body").append(tmpInput);

        var btn_code = $(this).data("code");
        var copyText = $("#inputToken-" + btn_code).val();
        tmpInput.val(copyText).select();
        document.execCommand("copy");
        tmpInput.remove();
        alertCustom("success", "Token copiado!");
    });

    //Botao de copiar URL Antifraud
    $(document).on("click", ".btnCopiarLinkAntifraud", function () {
        var tmpInput = $("<input>");
        $("body").append(tmpInput);
        var copyText = $("#input-url-antifraud").val();
        tmpInput.val(copyText).select();
        document.execCommand("copy");
        tmpInput.remove();
        alertCustom("success", "Antifraud URL copiada!");
    });

    function pagination(response, model) {
        if (response.meta.last_page == 1) {
            $("#pagination-integrates").css({ "background": "#f4f4f4" })
            $("#primeira_pagina_" + model).hide();
            $("#ultima_pagina_" + model).hide();
        } else {
            $("#pagination-" + model).html("");

            var first_page = "<button id='first_page' class='btn nav-btn'>1</button>";

            $("#pagination-" + model).append(first_page);

            if (response.meta.current_page == "1") {
                $("#first_page").attr("disabled", true).addClass("nav-btn").addClass("active");
            }

            $("#first_page").on("click", function () {
                refreshIntegrations(1);
            });

            for (x = 3; x > 0; x--) {
                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-" + model).append(
                    "<button id='page_" +
                    (response.meta.current_page - x) +
                    "' class='btn nav-btn'>" +
                    (response.meta.current_page - x) +
                    "</button>"
                );

                $("#page_" + (response.meta.current_page - x)).on("click", function () {
                    refreshIntegrations($(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page =
                    "<button id='current_page' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page").attr("disabled", true).addClass("nav-btn").addClass("active");
            }
            for (x = 1; x < 4; x++) {
                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-" + model).append(
                    "<button id='page_" +
                    (response.meta.current_page + x) +
                    "' class='btn nav-btn'>" +
                    (response.meta.current_page + x) +
                    "</button>"
                );

                $("#page_" + (response.meta.current_page + x)).on("click", function () {
                    refreshIntegrations($(this).html());
                });
            }

            if (response.meta.last_page != "1") {
                var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page").attr("disabled", true).addClass("nav-btn").addClass("active");
                }

                $("#last_page").on("click", function () {
                    refreshIntegrations(response.meta.last_page);
                });
            }
        }
    }

    function handleIntegrationTypeChange(e) {
        let type = e.target.value;
        let companiesContainer = $(".companies-container");
        let postbackContainer = $(".postback-container");
        let descriptionInput = $("#description");

        descriptionInput.val("");

        // type = 4 is Checkout API type
        if (type == 4) {
            companiesContainer.addClass("d-flex").removeClass("d-none");
            postbackContainer.addClass("d-flex").removeClass("d-none");
        } else {
            //type = 3 is Profitfy (External Integration)
            //if(type == 3) {
            //descriptionInput.val('Profitfy');
            //}
            companiesContainer.addClass("d-none").removeClass("d-flex");
            postbackContainer.addClass("d-none").removeClass("d-flex");
        }
    }

    $("#select-enum-list").on("change", handleIntegrationTypeChange);

    // Obtem os campos dos filtros
    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/core/companies?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $.each(response.data, function (i, company) {
                        if (companyIsApproved(company)) {
                            $("#companies").append(
                                $("<option>", {
                                    value: company.id,
                                    text: company.name,
                                })
                            );
                        }
                    });
                }

                loadingOnScreenRemove();
            },
        });
    }

    //opens the creation modal automatically if it comes with the parameter in the url
    if (window.location.href.includes("#add_checkout_api")) {
        $('select[name="token_type_enum"]').val(4).change();
        $("#modal-integrate").modal("show");
    }

});
