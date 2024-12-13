$(document).ready(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#project-not-empty").hide();
        $("#project-empty").hide();
        loadingSkeletonCards($(".loading-container"));
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                companiesAndProjects = data2;
                getProjects();
            });
        });
    });

    let companiesAndProjects = "";

    loadingSkeletonCards($(".loading-container"));

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data;
        getProjects();
    });

    function getProjects() {
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true&status=active&company=" + $(".company-navbar").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
                if (verifyAccountFrozen()) {
                    $(".add-btn").removeAttr("href");
                }
            },
            success: function success(response) {
                if (verifyAccountFrozen()) {
                    $(".add-btn").removeAttr("href");
                }

                if (response.data.length) {
                    $("#project-empty").hide();
                    updateUsedApps();
                } else {
                    clearUsedApps();
                    removeLoadingSkeletonCards();
                    $("#project-not-empty").show();
                }
            },
        });
    }

    function updateUsedApps() {
        $.ajax({
            method: "GET",
            url: "/api/apps",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (response.hotzappIntegrations > 0) {
                    $("#hotzapp-bt").addClass("added");
                    $("#hotzapp-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#hotzapp-bt").removeClass("added");
                    $("#hotzapp-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.shopifyIntegrations > 0) {
                    $("#shopify-bt").addClass("added");
                    $("#shopify-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#shopify-bt").removeClass("added");
                    $("#shopify-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.notazzIntegrations > 0) {
                    $("#notazz-bt").addClass("added");
                    $("#notazz-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#notazz-bt").removeClass("added");
                    $("#notazz-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.convertaxIntegrations > 0) {
                    $("#convertax-bt").addClass("added");
                    $("#convertax-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#convertax-bt").removeClass("added");
                    $("#convertax-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.activecampaignIntegrations > 0) {
                    $("#activecampaign-bt").addClass("added");
                    $("#activecampaign-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#activecampaign-bt").removeClass("added");
                    $("#activecampaign-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.digitalmanagerIntegrations > 0) {
                    $("#digitalmanager-bt").addClass("added");
                    $("#digitalmanager-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#digitalmanager-bt").removeClass("added");
                    $("#digitalmanager-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.whatsapp2Integrations > 0) {
                    $("#whatsapp2-bt").addClass("added");
                    $("#whatsapp2-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#whatsapp2-bt").removeClass("added");
                    $("#whatsapp2-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.reportanaIntegrations > 0) {
                    $("#reportana-bt").addClass("added");
                    $("#reportana-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#reportana-bt").removeClass("added");
                    $("#reportana-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.unicodropIntegrations > 0) {
                    $("#unicodrop-bt").addClass("added");
                    $("#unicodrop-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#unicodrop-bt").removeClass("added");
                    $("#unicodrop-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.smartfunnelIntegrations > 0) {
                    $("#smartfunnel-bt").addClass("added");
                    $("#smartfunnel-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#smartfunnel-bt").removeClass("added");
                    $("#smartfunnel-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.woocommerceIntegrations > 0) {
                    $("#woocom-bt").addClass("added");
                    $("#woocom-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#woocom-bt").removeClass("added");
                    $("#woocom-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.astronmembersIntegrations > 0) {
                    $("#astronmembers-bt").addClass("added");
                    $("#astronmembers-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#astronmembers-bt").removeClass("added");
                    $("#astronmembers-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.geradorrastreioIntegrations > 0) {
                    $("#geradorrastreio-bt").addClass("added");
                    $("#geradorrastreio-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#geradorrastreio-bt").removeClass("added");
                    $("#geradorrastreio-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.notificacoesinteligentesIntegrations > 0) {
                    $("#notificacoesinteligentes-bt").addClass("added");
                    $("#notificacoesinteligentes-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#notificacoesinteligentes-bt").removeClass("added");
                    $("#notificacoesinteligentes-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.hotbilletIntegrations > 0) {
                    $("#hotbillet-bt").addClass("added");
                    $("#hotbillet-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#hotbillet-bt").removeClass("added");
                    $("#hotbillet-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.melhorenvioIntegrations > 0) {
                    $("#menv-bt").addClass("added");
                    $("#menv-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#menv-bt").removeClass("added");
                    $("#menv-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.utmifyIntegrations > 0) {
                    $("#utmify-bt").addClass("added");
                    $("#utmify-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#utmify-bt").removeClass("added");
                    $("#utmify-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.vegacheckoutIntegrations > 0) {
                    $("#vegacheckout-bt").addClass("added");
                    $("#vegacheckout-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#vegacheckout-bt").removeClass("added");
                    $("#vegacheckout-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.adooreicheckoutIntegrations > 0) {
                    $("#adooreicheckout-bt").addClass("added");
                    $("#adooreicheckout-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#adooreicheckout-bt").removeClass("added");
                    $("#adooreicheckout-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                if (response.nuvemshopIntegrations > 0) {
                    $("#nuvemshop-btn").addClass("added");
                    $("#nuvemshop-icon").removeClass("o-add-1").addClass("o-checkmark-1");
                } else {
                    $("#nuvemshop-btn").removeClass("added");
                    $("#nuvemshop-icon").removeClass("o-checkmark-1").addClass("o-add-1");
                }

                // removeLoadingSkeletonCards();

                // $("#project-not-empty").show();
            },
        }).done(function () {
            removeLoadingSkeletonCards();
            $("#project-not-empty").show();
        });
    }

    function clearUsedApps() {
        $("#hotzapp-bt").removeClass("added");
        $("#hotzapp-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#shopify-bt").removeClass("added");
        $("#shopify-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#notazz-bt").removeClass("added");
        $("#notazz-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#convertax-bt").removeClass("added");
        $("#convertax-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#activecampaign-bt").removeClass("added");
        $("#activecampaign-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#digitalmanager-bt").removeClass("added");
        $("#digitalmanager-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#whatsapp2-bt").removeClass("added");
        $("#whatsapp2-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#reportana-bt").removeClass("added");
        $("#reportana-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#unicodrop-bt").removeClass("added");
        $("#unicodrop-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#smartfunnel-bt").removeClass("added");
        $("#smartfunnel-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#woocom-bt").removeClass("added");
        $("#woocom-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#astronmembers-bt").removeClass("added");
        $("#astronmembers-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#geradorrastreio-bt").removeClass("added");
        $("#geradorrastreio-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#notificacoesinteligentes-bt").removeClass("added");
        $("#notificacoesinteligentes-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#hotbillet-bt").removeClass("added");
        $("#hotbillet-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#menv-bt").removeClass("added");
        $("#menv-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#vegacheckout-bt").removeClass("added");
        $("#vegacheckout-icon").removeClass("o-checkmark-1").addClass("o-add-1");

        $("#adooreicheckout-bt").removeClass("added");
        $("#adooreicheckout-icon").removeClass("o-checkmark-1").addClass("o-add-1");
    }

    $(".app-integration").on("click", function () {
        if (verifyAccountFrozen() == false) {
            window.location.href = $(this).data("url");
        }
    });
});
