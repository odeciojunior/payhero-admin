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
                deleteWebhook();

                loadingOnScreenRemove();
            },
        });
    }

    function updateWebhookTableData(response) {
        $("#card-table-webhook").css("display", "block");
        $("#card-webhook-data").css("display", "block");
        $("#table-body-webhook").html("");

        $.each(response.data, function (index, value) {
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

            dados += '<td class="" style="vertical-align: middle;">';
            dados += '<p class="description mb-0 mr-1">' + value.url + "</p>";
            dados +=
                '<small class="text-muted">' + value.company_name + "</small>";
            dados += "</td>";

            dados += '<td class="text-center">';
            dados +=
                '<button class="btn pointer edit-webhook" style="background-color:transparent;" webhook="' +
                value.id +
                '"' +
                ' title="Editar webhook"><span class="o-edit-1"></span></button>';
            dados +=
                '<button class="btn pointer delete-webhook" style="background-color:transparent;" webhook="' +
                value.id +
                '"' +
                ' title="Deletar webhook"><span class="o-bin-1"></span></button>';
            dados += "</td>";

            dados += "</tr>";
            $("#table-body-webhook").append(dados);
        });
    }

    function getWebhook() {
        $(".edit-webhook").unbind("click");
        $(".edit-webhook").on("click", function () {
            let webhook_id = $(this).attr("webhook");
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
                    if (!isEmpty(response.data)) {
                        let webhook = response.data;

                        $("#modal-edit-webhook")
                            .find('input[name="description"]')
                            .val(webhook.description);

                        $("#modal-edit-webhook")
                            .find('input[name="url"]')
                            .val(webhook.url);

                        $("#modal-edit-webhook .sirius-select")
                            .find("option")
                            .each(function () {
                                if ($(this).val() == webhook.company_id) {
                                    $("#modal-edit-webhook .sirius-select")
                                        .prop("selectedIndex", $(this).index())
                                        .trigger("change");
                                    return false;
                                }
                            });

                        $("#modal-edit-webhook .sirius-select-text").text(
                            webhook.company_name
                        );

                        $("#modal-edit-webhook").modal("show");

                        editWebhook(webhook_id);
                    } else {
                        alertCustom("error", "Erro ao obter dados do webhook");
                    }
                },
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
                let url = $("#modal-webhook").find("input[name='url']").val();
                let companyHash = $("#companies").val();

                if (description == "") {
                    alertCustom("error", "Digite um nome para seu webhook");
                } else if (!companyHash) {
                    alertCustom("error", "Selecione uma empresa");
                } else if (url == "") {
                    alertCustom("error", "Digite uma URL válida");
                } else {
                    loadingOnScreen();
                    storeWebhook(description, url, companyHash);
                }
            });
            $("#modal-webhook").modal("show");
        });
    }

    function storeWebhook(description, url, companyHash) {
        $.ajax({
            method: "POST",
            url: "/api/webhooks",
            data: {
                company_id: companyHash,
                description: description,
                url: url,
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
                clearForm();
                loadingOnScreenRemove();
                refreshWebhooks();
                alertCustom("success", "Webhook criado com sucesso!");
            },
        });
    }

    function editWebhook(webhook_id) {
        $("#btn-edit-webhook").unbind("click");
        $("#btn-edit-webhook").on("click", function () {
            let description = $("#modal-edit-webhook")
                .find('input[name="description"]')
                .val();

            let url = $("#modal-edit-webhook").find('input[name="url"]').val();

            let companyHash = $("#companies_edit").val();

            loadingOnScreen();

            $.ajax({
                method: "PUT",
                url: "api/webhooks/" + webhook_id,
                data: {
                    company_id: companyHash,
                    description: description,
                    url: url,
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
                            $("#companies, #companies_edit").append(
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

    function clearForm() {
        $(":text").val("");
        $("#companies, #companies_edit")
            .prop("selectedIndex", 0)
            .trigger("change");
    }
});
