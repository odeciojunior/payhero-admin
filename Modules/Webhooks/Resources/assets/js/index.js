$(document).ready(function () {
    let webhookTypeEnum = {
        external: "Webhook externo",
        checkout_api: "Checkout API",
    };

    let webhookTypeEnumBadge = {
        admin: "default",
        personal: "default",
        external: "success",
        checkout_api: "primary",
    };

    let status = {
        active: "Ativo",
        inactive: "Inativo",
    };

    let statusBadge = {
        active: "success",
        inactive: "danger",
    };

    refreshWebhooks();
    createWebhook();
    getCompanies();

    function refreshWebhooks(page = 1) {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/webhooks?resume=true&page=" + page,
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
                if (isEmpty(response.data)) {
                    $(".page-header")
                        .find(".store-webhook")
                        .css("display", "none");
                    $("#content-error")
                        .find(".store-webhook")
                        .css("display", "block");
                    $("#content-error").css("display", "block");
                    $("#content-script").css("display", "none");
                    $("#card-table-webhook").css("display", "none");
                    $("#card-webhook-data").css("display", "none");
                } else {
                    $(".page-header")
                        .find(".store-webhook")
                        .css("display", "block");
                    $("#content-error")
                        .find(".store-webhook")
                        .css("display", "none");
                    $("#content-error").hide();
                    updateWebhookTableData(response);
                    pagination(response, "webhooks");
                }

                getWebhook();
                refreshToken();
                deleteWebhook();
                loadingOnScreenRemove();
            },
        });
    }

    function updateWebhookTableData(response) {
        $("#content-script").css("display", "block");
        $("#card-table-webhook").css("display", "block");
        $("#card-webhook-data").css("display", "block");
        $("#card-table-webhook").css("display", "block");
        $("#table-body-webhook").html("");

        if (
            response.data.length > 0 &&
            response.data.findIndex((e) => e.webhook_type == "checkout_api") !=
                -1
        ) {
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

            dados += '<td class="" style="vertical-align: middle;">';
            dados +=
                '<p class="description mb-0 mr-1">' +
                value.description +
                "</p>";
            dados +=
                '<small class="text-muted">Criada em ' +
                value.register_date +
                "</small>";
            dados += "</td>";

            dados += '<td class="text-center">';
            dados +=
                '<span class="badge badge-' +
                webhookTypeEnumBadge[value.webhook_type] +
                ' text-center">' +
                webhookTypeEnum[value.webhook_type] +
                "</span>";
            dados += "</td>";

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

            dados += '<td class="text-center">';
            dados +=
                '<button class="btn pointer edit-webhook" style="background-color:transparent;" webhook="' +
                value.id_code +
                '"' +
                disabled +
                ' title="Editar webhook"><span class="o-edit-1"></span></button>';

            dados +=
                '<button class="btn pointer delete-webhook" style="background-color:transparent;" webhook="' +
                value.id_code +
                '"' +
                disabled +
                ' title="Deletar token"><span class="o-bin-1"></span></button>';
            dados += "</td>";

            dados += "</tr>";
            $("#table-body-webhook").append(dados);
        });

        $("#webhooks_stored").html("" + response.resume.total + "");
        $("#webhooks_active").html("" + response.resume.active + "");
        $("#posts_received").html("" + response.resume.received + "");
        $("#posts_sent").html("" + response.resume.sent + "");
    }

    function getWebhook() {
        $(".edit-webhook").unbind("click");
        $(".edit-webhook").on("click", function () {
            $("#modal-edit-webhook").find('input[name="description"]').val("");
            $("#modal-edit-webhook").find('input[name="postback"]').val("");
            let webhook_id = $(this).attr("webhook");
            $("#modal-edit-webhook").modal("show");
            $.ajax({
                method: "GET",
                url: "/api/webhooks/" + webhook_id,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    if (!isEmpty(response)) {
                        if (response.token_type_enum == 4) {
                            $("#modal-edit-webhook")
                                .find('input[name="postback"]')
                                .val(response.postback);
                            $("#modal-edit-webhook")
                                .find(".postback-container")
                                .show();
                        } else {
                            $("#modal-edit-webhook")
                                .find(".postback-container")
                                .hide();
                            $("#modal-edit-webhook")
                                .find('input[name="postback"]')
                                .val("");
                        }

                        $("#modal-edit-webhook")
                            .find('input[name="description"]')
                            .val(response.description);
                        $("#modal-edit-webhook")
                            .find('input[name="token_type_enum"]')
                            .val(response.token_type_enum);

                        editWebhook(webhook_id);
                    } else {
                        alertCustom("error", "Erro ao obter dados do webhook");
                    }
                },
            });
        });
    }

    function refreshToken() {
        $(".refresh-webhook").unbind("click");
        $(".refresh-webhook").on("click", function () {
            let webhookId = $(this).attr("webhook");
            let url = "api/webhooks/" + webhookId + "/refreshtoken";
            $("#modal-refresh-webhook").modal("show");
            $("#btn-refresh-webhook").unbind("click");
            $("#btn-refresh-webhook").on("click", function () {
                loadingOnScreen();
                $.ajax({
                    method: "POST",
                    url: url,
                    data: { webhookId: webhookId },
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    error: (response) => {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                    },
                    success: (response) => {
                        loadingOnScreenRemove();
                        refreshWebhooks();
                        alertCustom("success", response.message);
                    },
                });
            });
        });
    }

    function createWebhook() {
        $(".store-webhook").unbind();
        $(".store-webhook").on("click", function () {
            loadingOnScreenRemove();
            $("#btn-save-webhook").unbind();
            $("#btn-save-webhook").on("click", function () {
                let description = $("#modal-webhook")
                    .find("input[name='description']")
                    .val();
                let tokenTypeEnum = $("#select-enum-list").val();
                let postback = $("#modal-webhook")
                    .find("input[name='postback']")
                    .val();
                let companyHash = $("#companies").val();

                if (description == "") {
                    alertCustom("error", "O campo descrição é obrigatório");
                } else if (tokenTypeEnum == 4 && !companyHash) {
                    alertCustom("error", "O campo empresa é obrigatório");
                } else if (tokenTypeEnum == 4 && postback == "") {
                    alertCustom("error", "O campo postback é obrigatório");
                } else {
                    loadingOnScreen();
                    storeWebhook(
                        description,
                        tokenTypeEnum,
                        postback,
                        companyHash
                    );
                }
            });
            $("#modal-webhook").modal("show");
        });
    }

    function storeWebhook(description, tokenTypeEnum, postback, companyHash) {
        $.ajax({
            method: "POST",
            url: "/api/webhooks",
            data: {
                description: description,
                token_type_enum: tokenTypeEnum,
                postback: postback,
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
                alertCustom("success", "Webhook criado com sucesso!");
                loadingOnScreenRemove();
                refreshWebhooks();
            },
        });
    }

    function editWebhook(webhook_id) {
        $("#btn-edit-webhook").unbind("click");
        $("#btn-edit-webhook").on("click", function () {
            let description = $("#modal-edit-webhook")
                .find('input[name="description"]')
                .val();
            let token_type_enum = $("#modal-edit-webhook")
                .find('input[name="token_type_enum"]')
                .val();
            let postback = $("#modal-edit-webhook")
                .find('input[name="postback"]')
                .val();

            loadingOnScreen();
            $.ajax({
                method: "PUT",
                url: "api/webhooks/" + webhook_id,
                data: {
                    description: description,
                    postback: postback,
                    token_type_enum: token_type_enum,
                },
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                error: (response) => {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $(".close").click();
                    loadingOnScreenRemove();
                    refreshWebhooks();
                    alertCustom("success", response.message);
                },
            });
        });
    }

    function deleteWebhook() {
        $(".delete-webhook").unbind("click");
        $(".delete-webhook").on("click", function () {
            let webhookId = $(this).attr("webhook");
            let url = "api/webhooks/" + webhookId;
            $("#modal-delete-webhook").modal("show");
            $("#btn-delete-webhook").unbind("click");
            $("#btn-delete-webhook").on("click", function () {
                loadingOnScreen();
                $.ajax({
                    method: "DELETE",
                    url: url,
                    data: { webhookId: webhookId },
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    error: (response) => {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
                    },
                    success: (response) => {
                        loadingOnScreenRemove();
                        refreshWebhooks();
                        alertCustom("success", response.message);
                    },
                });
            });
        });
    }

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
            $("#primeira_pagina_" + model).hide();
            $("#ultima_pagina_" + model).hide();
        } else {
            $("#pagination-" + model).html("");

            var first_page =
                "<button id='first_page' class='btn nav-btn'>1</button>";

            $("#pagination-" + model).append(first_page);

            if (response.meta.current_page == "1") {
                $("#first_page")
                    .attr("disabled", true)
                    .addClass("nav-btn")
                    .addClass("active");
            }

            $("#first_page").on("click", function () {
                refreshWebhooks(1);
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

                $("#page_" + (response.meta.current_page - x)).on(
                    "click",
                    function () {
                        refreshWebhooks($(this).html());
                    }
                );
            }

            if (
                response.meta.current_page != 1 &&
                response.meta.current_page != response.meta.last_page
            ) {
                var current_page =
                    "<button id='current_page' class='btn nav-btn active'>" +
                    response.meta.current_page +
                    "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page")
                    .attr("disabled", true)
                    .addClass("nav-btn")
                    .addClass("active");
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

                $("#page_" + (response.meta.current_page + x)).on(
                    "click",
                    function () {
                        refreshWebhooks($(this).html());
                    }
                );
            }

            if (response.meta.last_page != "1") {
                var last_page =
                    "<button id='last_page' class='btn nav-btn'>" +
                    response.meta.last_page +
                    "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page")
                        .attr("disabled", true)
                        .addClass("nav-btn")
                        .addClass("active");
                }

                $("#last_page").on("click", function () {
                    refreshWebhooks(response.meta.last_page);
                });
            }
        }
    }

    function handleWebhookTypeChange(e) {
        let type = e.target.value;
        let companiesContainer = $(".companies-container");
        let postbackContainer = $(".postback-container");
        let descriptionInput = $("#description");

        descriptionInput.val("");

        if (type == 4) {
            companiesContainer.addClass("d-flex").removeClass("d-none");
            postbackContainer.addClass("d-flex").removeClass("d-none");
        } else {
            companiesContainer.addClass("d-none").removeClass("d-flex");
            postbackContainer.addClass("d-none").removeClass("d-flex");
        }
    }

    $("#select-enum-list").on("change", handleWebhookTypeChange);

    if (window.location.href.includes("#add_checkout_api")) {
        $('select[name="token_type_enum"]').val(4).change();
        $("#modal-webhook").modal("show");
    }
});
