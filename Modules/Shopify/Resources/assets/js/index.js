$(document).ready(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#integration-actions").hide();
        $("#no-integration-found").hide();
        $("#project-empty").hide();
        $("#content").html("");
        loadingSkeletonCards($("#content"));
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                companiesAndProjects = data2;
                $(".company_name").val(
                    companiesAndProjects.company_default_fullname
                );
                $("#company-navbar-value").val($(".company-navbar").val());
                getCompanies("n");
            });
        });
    });

    loadingSkeletonCards($("#content"));

    companiesAndProjects = "";

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data;
        $(".company_name").val(companiesAndProjects.company_default_fullname);
        $("#company-navbar-value").val($(".company-navbar").val());
        getCompanies();
    });

    function getCompanies(loading = "y") {
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
                removeLoadingSkeletonCards();
            },
            success: function success(response) {
                verifyCompanies(response.data);

                if (loading == "y") loadingOnScreenRemove();
                else loadOnAny("#content", true);
            },
        });
    }

    function verifyCompanies(companies) {
        removeLoadingSkeletonCards();

        if (isEmpty(companies)) {
            htmlCompanyNotFound();
            return;
        }

        let hasCompanyApproved = false;
        $("#select_companies").empty();
        $(companies).each(function (index, company) {
            if (companyIsApproved(company)) {
                hasCompanyApproved = true;
                $("#select_companies").append(
                    `<option value="${company.id}"> ${company.name}</option>`
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
            url: "/api/apps/shopify?company=" + $(".company-navbar").val(),
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
                $(shopifyIntegrations).each(function (
                    index,
                    shopifyIntegration
                ) {
                    var data = shopifyIntegration;
                    $("#content").append(`
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                            <div class="card shadow card-edit" project="${
                                shopifyIntegration.id
                            }">

                            <svg
                            class="open-cfg" app="${data.id}"
                            data-img="${
                                !data.project_photo
                                    ? "/build/global/img/produto.svg"
                                    : data.project_photo
                            }"
                            data-name="${data.project_name}"
                            data-token="${data.token}"
                            data-skip="${shopifyIntegration.skip_to_cart}"
                            style="position:absolute; top:8px; right:8px; cursor:pointer"
                            width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M30.5519 15.2167C30.5519 23.4694 23.8618 30.1596 15.6091 30.1596C7.35639 30.1596 0.66626 23.4694 0.66626 15.2167C0.66626 6.96405 7.35639 0.273926 15.6091 0.273926C23.8618 0.273926 30.5519 6.96405 30.5519 15.2167Z" fill="white"/>
                                <g clip-path="url(#clip0_0_1)">
                                <path d="M15.609 18.7327C17.5508 18.7327 19.1249 17.1586 19.1249 15.2168C19.1249 13.275 17.5508 11.7008 15.609 11.7008C13.6672 11.7008 12.093 13.275 12.093 15.2168C12.093 17.1586 13.6672 18.7327 15.609 18.7327Z" stroke="#70707E" stroke-width="1.2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M23.8715 13.8291L22.4335 13.457C22.2697 12.8311 22.0199 12.2309 21.691 11.6738L22.4546 10.377C22.5336 10.2427 22.5659 10.086 22.5462 9.93137C22.5265 9.77678 22.456 9.6331 22.3459 9.52291L21.3464 8.5235C21.2362 8.4133 21.0926 8.34283 20.938 8.32316C20.7834 8.30348 20.6266 8.33572 20.4924 8.4148L19.1964 9.17659C18.6381 8.84679 18.0366 8.59631 17.4092 8.43238L17.0277 6.95421C16.9887 6.80326 16.9007 6.66954 16.7774 6.57407C16.6541 6.4786 16.5027 6.42681 16.3467 6.42682H14.933C14.7772 6.42687 14.6258 6.4787 14.5026 6.57416C14.3794 6.66962 14.2914 6.80331 14.2524 6.95421L13.8715 8.4274C13.2399 8.59063 12.6342 8.84153 12.0722 9.17278L10.7476 8.39282C10.6133 8.31374 10.4566 8.28151 10.302 8.30118C10.1474 8.32086 10.0037 8.39133 9.8935 8.50153L8.8938 9.50123C8.7836 9.61142 8.71313 9.7551 8.69346 9.90969C8.67378 10.0643 8.70602 10.221 8.7851 10.3553L9.56505 11.6797C9.23991 12.2318 8.99226 12.826 8.82905 13.4455L7.34649 13.8291C7.19553 13.8681 7.06181 13.9561 6.96634 14.0794C6.87088 14.2026 6.81908 14.3541 6.81909 14.51V15.9237C6.81914 16.0796 6.87097 16.231 6.96643 16.3542C7.06189 16.4774 7.19558 16.5654 7.34649 16.6043L8.81733 16.9852C8.98126 17.6243 9.23488 18.2368 9.57062 18.8047L8.80883 20.1007C8.72975 20.235 8.69751 20.3917 8.71719 20.5463C8.73686 20.7009 8.80733 20.8446 8.91753 20.9548L9.91724 21.9545C10.0274 22.0647 10.1711 22.1351 10.3257 22.1548C10.4803 22.1745 10.637 22.1422 10.7713 22.0632L12.0681 21.2993C12.6352 21.6342 13.2468 21.8872 13.8847 22.0509L14.2524 23.4792C14.2914 23.6301 14.3794 23.7638 14.5026 23.8593C14.6258 23.9547 14.7772 24.0066 14.933 24.0066H16.3467C16.5027 24.0066 16.6541 23.9548 16.7774 23.8594C16.9007 23.7639 16.9887 23.6302 17.0277 23.4792L17.3989 22.0435C18.0321 21.8789 18.6391 21.6261 19.202 21.2926L20.4704 22.0394C20.6047 22.1185 20.7614 22.1507 20.916 22.1311C21.0706 22.1114 21.2143 22.0409 21.3245 21.9307L22.3242 20.931C22.4344 20.8208 22.5048 20.6772 22.5245 20.5226C22.5442 20.368 22.512 20.2112 22.4329 20.0769L21.686 18.8086C22.0251 18.2365 22.2807 17.619 22.4452 16.9747L23.8718 16.6055C24.0228 16.5664 24.1566 16.4783 24.252 16.3548C24.3474 16.2314 24.3991 16.0797 24.3989 15.9237V14.51C24.3989 14.3541 24.3471 14.2026 24.2516 14.0794C24.1562 13.9561 24.0224 13.8681 23.8715 13.8291V13.8291Z" stroke="#70707E" stroke-width="1.2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_0_1">
                                <rect width="19.3378" height="19.3378" fill="white" transform="translate(5.94019 5.54785)"/>
                                </clipPath>
                                </defs>
                                <title>Configurações da Integração</title>
                            </svg>
                                <img class="card-img-top img-fluid w-full" style="height: 297px;" onerror="this.src = '/build/global/img/produto.svg'" src="${
                                    !shopifyIntegration.project_photo
                                        ? "/build/global/img/produto.svg"
                                        : shopifyIntegration.project_photo
                                }"  alt="Photo Project"/>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="card-title">${
                                                shopifyIntegration.project_name
                                            }</h4>
                                            <p class="card-text sm">Criado em ${
                                                shopifyIntegration.created_at
                                            }</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    $(".open-cfg").on("click", openCfg);
                });
            },
        });
    }

    function htmlCompanyNotFound() {
        $(
            "#btn-integration-model, #button-information, #no-integration-found"
        ).hide();
        $("#empty-companies-error").show();
    }

    function htmlAllCompanyNotApproved() {
        $(
            "#btn-integration-model, #button-information, #no-integration-found"
        ).hide();
        $("#companies-not-approved-getnet").show();
    }

    function htmlIntegrationShopifyNotFound() {
        removeLoadingSkeletonCards();
        $("#empty-companies-error, #companies-not-approved-getnet").hide();
        $(
            "#btn-integration-model, #button-information, #no-integration-found, #integration-actions"
        ).show();
        $("#button-information")
            .show()
            .addClass("d-flex")
            .css("display", "flex");
        $(".modal-title").html("Adicionar nova integração com Shopify");
        $("#bt_integration").addClass("btn-save");
        $("#bt_integration").text("Realizar integração");
    }

    function htmlHasIntegrationShopify() {
        removeLoadingSkeletonCards();
        $("#no-integration-found").hide();
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
        if (
            $("#token").val() == "" ||
            $("#url_store").val() == "" ||
            $("#company").val() == ""
        ) {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }

        saveIntegration();
    });

    function saveIntegration() {
        let form_data = new FormData(
            document.getElementById("form_add_integration")
        );

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
                loadOnAny("#content", true);
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny("#content", true);
                loadingOnScreenRemove();
                alertCustom("success", response.message);
                getCompanies();
            },
        });
    }

    let projectId;
    let token;

    function openCfg() {
        projectId = $(this).attr("app");
        var img = $(this).attr("data-img");
        var name = $(this).attr("data-name");
        var skip = $(this).attr("data-skip");
        token = $(this).attr("data-token");

        $("#modal_edit").modal("show");

        function imageFound() {}

        function imageNotFound() {
            img = "/build/global/img/produto.png";
            $("#project-img").attr("src", img);
        }

        var tester = new Image();
        tester.onload = imageFound;
        tester.onerror = imageNotFound;
        tester.src = img;

        $("#project-img").attr("src", img);
        img = null;
        $("#project-name").html(name);
        $("#project-token").val(token);

        $("#skiptocart-input")
            .prop("checked", skip == 1 ? true : false)
            .val(skip);
    }

    let syncAction = "";

    $("#token-change").click(function () {
        if ($(this).html() == "Cancelar") {
            $("#project-token").val(token);
            $("#project-token").attr("disabled", true);
            $(this).html("Alterar");
            $("#bt-update-keys").hide();
            $("#bt-close").show();
            return;
        }
        $("#project-token").attr("disabled", false);
        $("#project-token").val("");
        $("#project-token").focus();
        $(this).html("Cancelar");

        $("#bt-close").hide();
        $("#bt-update-keys").show();
    });

    $("#skiptocart-input").on("change", function () {
        if ($("#skiptocart-input").prop("checked") == true) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }

        var input = $(this);

        $.ajax({
            method: "POST",
            url: "/api/apps/shopify/skiptocart",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                project_id: projectId,
                skip_to_cart: parseInt(input.val()),
            },
            error: function (response) {
                errorAjaxResponse(response);
                changeSwitchSkipToCart(input);
                switchSkipToCartOpacity(input, "1", false);
            },
            success: function (response) {
                alertCustom("success", response.message);
                switchSkipToCartOpacity(input, "1", false);
            },
        });
    });

    $("#bt-update-keys").on("click", function () {
        if (!$("#project-token").val()) {
            alertCustom("error", "Insira um token válido!");

            return;
        }

        $.ajax({
            method: "POST",
            url: "/api/apps/shopify/updatetoken",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                project_id: projectId,
                token: $("#project-token").val(),
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                btnTokenClick = "enable click";
                $("#shopify-token").prop("disabled", true);
                $(".btn-edit-token").text("Alterar").removeClass("bg-grey-700");
                alertCustom("success", response.message);
            },
        });

        $("#bt-close").trigger("click");
    });

    $("#bt-delete-integration").on("click", function () {
        if (confirm("Deseja deletar a integração?")) {
            deleteIntegration(projectId);
        }
    });

    /**
     * Deletes a Shopify integration by its ID.
     * @param {number} id - The ID of the integration to delete.
     */
    function deleteIntegration(id) {
        $.ajax({
            method: "DELETE",
            url: "/api/apps/shopify/delete",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                id: id,
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                alertCustom("success", response.message);
                $("#modal_edit").modal("hide");
                getCompanies();
            },
        });
    }

    $("#bt-close").trigger("click");

    $(".sync-tracking").click(function () {
        syncAction = "tracking";
        toggle_confirm(
            "Rastreios",
            "A sincronização pode demorar algumas horas."
        );
    });

    $(".sync-products").click(function () {
        syncAction = "products";
        toggle_confirm(
            "Produtos",
            "A sincronização pode demorar algumas horas."
        );
    });

    $(".sync-template").click(function () {
        syncAction = "template";
        toggle_confirm(
            "Template",
            "Antes de sincronizar um novo tema em sua loja, tenha em mente que as configurações feitas antes serão atualizadas, <strong> podendo alterar o funcionamento de sua loja.</strong>"
        );
    });

    $("#bt-close-confirm").on("click", function () {
        $("#modal_edit").modal("show");
    });

    function toggle_confirm(name, desc) {
        $("#modal_edit").modal("hide");
        $("#modal-confirm").modal("show");

        function fill() {
            $("#sync-name").html(name);
            if (desc) {
                $("#sync-desc").html(
                    '<div style="padding:2px 0">' + desc + "</div>"
                );
            } else {
                $("#sync-desc").html("");
            }
        }

        if ($("#bts-confirm").is(":visible")) {
            // $("#bts-confirm").fadeOut('fast',null, function () {
            //     fill()
            //     $("#bts-confirm").slideDown();
            // });
            fill();
        } else {
            fill();
            //$("#bts-confirm").show()
            //$("#bts-confirm").slideDown();
        }
    }

    $("#bt-cancel").on("click", function () {
        $("#bts-confirm").slideUp();
    });

    $("#bt-confirm").on("click", function () {
        switch (syncAction) {
            case "products":
                $.ajax({
                    method: "POST",
                    url: "/api/apps/shopify/synchronize/products",
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    data: {
                        project_id: projectId,
                    },
                    error: function (response) {
                        errorAjaxResponse(response);
                    },
                    success: function (response) {
                        alertCustom("success", response.message);
                    },
                });
                break;
            case "template":
                $.ajax({
                    method: "POST",
                    url: "/api/apps/shopify/synchronize/templates",
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    data: {
                        project_id: projectId,
                    },
                    error: function (response) {
                        errorAjaxResponse(response);
                    },
                    success: function (response) {
                        alertCustom("success", response.message);
                    },
                });
                break;
            case "tracking":
                $.ajax({
                    method: "POST",
                    url: "/api/apps/shopify/synchronize/trackings",
                    dataType: "json",
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    data: {
                        project_id: projectId,
                    },
                    error: function (response) {
                        errorAjaxResponse(response);
                    },
                    success: function (response) {
                        alertCustom("success", response.message);
                    },
                });
                break;
            default:
                break;
        }
        $("#modal-confirm").modal("hide");

        $("#bt-cancel").trigger("click");
        $("#bt-close").trigger("click");
    });

    $("#bt-shopify-verify-permissions").on("click", function (event) {
        event.preventDefault();
        $("#bt-close").trigger("click");
        loadingOnScreen();

        $.ajax({
            method: "POST",
            url: "/api/apps/shopify/verifypermissions",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                project_id: projectId,
            },
            error: function (response) {
                loadOnAny("#content", true);
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadOnAny("#content", true);
                loadingOnScreenRemove();
                alertCustom("success", response.message);
            },
        });
    });
});
