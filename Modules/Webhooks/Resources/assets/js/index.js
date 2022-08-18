$(document).ready(function () {
    refreshWebhooks();
    getCompanies();

    function refreshWebhooks(page = 1) {
        $.ajax({
            method: "GET",
            url: "/api/webhooks?resume=true&page=" + page,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function () {
                loadingOnScreen();
            },
            error: (response) => {
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
            },
            complete: function () {
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
                '<button type="button" class="btn pointer edit-webhook" style="background-color:transparent;" webhook="' +
                value.id +
                '"' +
                ' title="Editar webhook"><span class="o-edit-1"></span></button>';
            dados +=
                '<button type="button" class="btn pointer delete-webhook" style="background-color:transparent;" webhook="' +
                value.id +
                '"' +
                ' title="Deletar webhook"><span class="o-bin-1"></span></button>';
            dados += "</td>";
            dados += "</tr>";

            $("#table-body-webhook").append(dados);
        });
    }

    function getCompanies() {
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
            },
        });
    }

    $(".store-webhook").on("click", function () {
        $("#modal-webhook").modal("show");
    });

    $("#btn-save-webhook").on("click", function () {
        let data = validate("store");
        data ? storeWebhook(data) : errorAjaxResponse("");
    });

    function storeWebhook(data) {
        $.ajax({
            method: "POST",
            url: "/api/webhooks",
            data: {
                description: data.description,
                company_id: data.company_id,
                url: data.url,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#modal-webhook").modal("hide");
                alertCustom("success", "Webhook criado com sucesso");
                clearForm();
                refreshWebhooks();
            },
            complete: function () {
                loadingOnScreenRemove();
            },
        });
    }

    $("#table-body-webhook").on("click", ".edit-webhook", function () {
        let webhookId = $(this).attr("webhook");
        $.ajax({
            method: "GET",
            url: "/api/webhooks/" + webhookId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (!isEmpty(response.data)) {
                    let webhook = response.data;
                    $("#modal-edit-webhook #description_edit").val(
                        webhook.description
                    );
                    $("#modal-edit-webhook #url_edit").val(webhook.url);

                    $("#modal-edit-webhook #companies_edit")
                        .find("option")
                        .each(function () {
                            if ($(this).val() == webhook.company_id) {
                                $("#modal-edit-webhook .sirius-select")
                                    .prop("selectedIndex", $(this).index())
                                    .trigger("change");
                                $(
                                    "#modal-edit-webhook .sirius-select-text"
                                ).text(webhook.company_name);
                                return false;
                            }
                        });

                    $("#modal-edit-webhook #webhook_id").val(webhookId);

                    $("#modal-edit-webhook").modal("show");
                } else {
                    alertCustom("error", "Erro ao obter dados do webhook");
                }
            },
        });
    });

    $("#modal-edit-webhook").on("click", "#btn-edit-webhook", function () {
        let data = validate("update");
        data ? updateWebhook(data) : errorAjaxResponse("");
    });

    function updateWebhook(data) {
        let webhookId = $("#modal-edit-webhook #webhook_id").val();
        $.ajax({
            method: "PUT",
            url: "api/webhooks/" + webhookId,
            data: {
                description: data.description,
                company_id: data.company_id,
                url: data.url,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#modal-edit-webhook").modal("hide");
                alertCustom("success", response.message);
                clearForm();
                refreshWebhooks();
            },
            complete: function () {
                loadingOnScreenRemove();
            },
        });
    }

    function validate(event) {
        let description, company_id, url;

        if (event == "store") {
            description = $("#modal-webhook #description").val();
            company_id = $("#modal-webhook #companies").val();
            url = $("#modal-webhook #url").val();
        } else if (event == "update") {
            description = $("#modal-edit-webhook #description_edit").val();
            company_id = $("#modal-edit-webhook #companies_edit").val();
            url = $("#modal-edit-webhook #url_edit").val();
        }

        if (isEmpty(description)) {
            alertCustom("error", "Digite um nome para seu webhook");
            return false;
        }

        if (isEmpty(company_id)) {
            alertCustom("error", "Selecione uma empresa");
            return false;
        }

        if (isEmpty(url)) {
            alertCustom("error", "Digite uma URL");
            return false;
        }

        return { description: description, company_id: company_id, url: url };
    }

    $("#table-body-webhook").on("click", ".delete-webhook", function () {
        $("#modal-delete-webhook #webhook_id").val($(this).attr("webhook"));
        $("#modal-delete-webhook").modal("show");
    });

    $("#modal-delete-webhook").on("click", "#btn-delete-webhook", function () {
        let webhookId = $("#modal-delete-webhook #webhook_id").val();
        $.ajax({
            method: "DELETE",
            url: "api/webhooks/" + webhookId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#modal-delete-webhook").modal("hide");
                alertCustom("success", response.message);
                clearForm();
                refreshWebhooks();
            },
            complete: function () {
                loadingOnScreenRemove();
            },
        });
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

    function clearForm() {
        $(":text").val("");
        $("#companies, #companies_edit")
            .prop("selectedIndex", 0)
            .trigger("change");
    }
});
