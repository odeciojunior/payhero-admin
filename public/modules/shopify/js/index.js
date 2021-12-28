$(document).ready(function () {
    getCompanies();

    function getCompanies() {
        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                verifyCompanies(response.data);
                loadingOnScreenRemove();
            },
        });
    }

    function verifyCompanies(companies) {
        if (isEmpty(companies)) {
            htmlCompanyNotFound();
            return;
        }

        let hasCompanyApproved = false;
        $("#select_companies").empty();
        $(companies).each(function (index, company) {
            if (company.capture_transaction_enabled) {
                hasCompanyApproved = true;
                $("#select_companies").append(
                    `<option value=${company.id}> ${company.name}</option>`
                );
            }
        });

        if (hasCompanyApproved) {
            $("#btn-integration-model").show();
            $("#button-information")
                .show()
                .addClass("d-flex")
                .css("display", "flex");

            getShopifyIntegrations();
        } else {
            htmlAllCompanyNotApproved();
        }
    }

    function getShopifyIntegrations() {
        $.ajax({
            method: "GET",
            url: "/api/apps/shopify",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let shopifyIntegrations = response.data;

                $("#content").html("");

                if (isEmpty(shopifyIntegrations)) {
                    htmlIntegrationShopifyNotFound();
                    return;
                }

                htmlHasIntegrationShopify();
                $(shopifyIntegrations).each(function (index, shopifyIntegration) {
                    $("#content").append(`
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                            <div class="card shadow card-edit" project="${shopifyIntegration.id}">
                                <img class="card-img-top img-fluid w-full" onerror="this.src = '/modules/global/img/produto.png'" src="${!shopifyIntegration.project_photo ? "/modules/global/img/produto.png" : shopifyIntegration.project_photo}"  alt="Photo Project"/>
                                <div class="card-body">
                                    <div class='row'>
                                        <div class='col-md-12'>
                                            <h4 class="card-title">${shopifyIntegration.project_name}</h4>
                                            <p class="card-text sm">Criado em ${shopifyIntegration.created_at}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            },
        });
    }

    function htmlCompanyNotFound() {
        $("#btn-integration-model, #button-information, #no-integration-found").hide();
        $("#empty-companies-error").show();
    }

    function htmlAllCompanyNotApproved() {
        $("#btn-integration-model, #button-information, #no-integration-found").hide();
        $("#companies-not-approved-getnet").show();
    }

    function htmlIntegrationShopifyNotFound() {
        $("#empty-companies-error, #companies-not-approved-getnet").hide();
        $("#btn-integration-model, #button-information, #no-integration-found, #integration-actions").show();
        $("#button-information")
            .show()
            .addClass("d-flex")
            .css("display", "flex");
        $(".modal-title").html("Adicionar nova integração com Shopify");
        $("#bt_integration").addClass("btn-save");
        $("#bt_integration").text("Realizar integração");
    }

    function htmlHasIntegrationShopify() {
        $(".modal-title").html("Adicionar nova integração com Shopify");
        $("#bt_integration").addClass("btn-save");
        $("#bt_integration").text("Realizar integração");
        $("#integration-actions").show();
        $("#button-information")
            .show()
            .addClass("d-flex")
            .css("display", "flex");
    }

    $("#btn-integration-model").on("click", function () {
        $("#modal_add_integracao").modal("show");
        $("#form_add_integration").show();
    });

    $("#bt_integration").on("click", function () {
        if ($("#token").val() == "" || $("#url_store").val() == "" || $("#company").val() == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }

        saveIntegration();
    });

    function saveIntegration() {
        let form_data = new FormData(document.getElementById("form_add_integration"));

        loadingOnScreen();

        $.ajax({
            method: "POST",
            url: "/api/apps/shopify",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom("success", response.message);
                getCompanies();
            },
        });
    }
});
