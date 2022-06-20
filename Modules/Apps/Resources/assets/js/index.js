function updateAfterChangeCompany(){
    loadOnAny('.page-content');
    window.updateUsedApps();
}

$(document).ready(function () {

    loadingOnScreen();

    getProjects();

    function getProjects() {
        $.ajax({
            method: "GET",
            url: '/api/projects?select=true&status=active&company='+ sessionStorage.getItem('company_default'),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove()
                errorAjaxResponse(response);
                if (verifyAccountFrozen()) {
                    $('.add-btn').removeAttr('href');
                }
            },
            success: function success(response) {
                if (verifyAccountFrozen()) {
                    $('.add-btn').removeAttr('href');
                }
                $("#project-not-empty").show();

                if (response.data.length) {
                    $("#project-empty").hide();
                    window.updateUsedApps();
                } else {
                    loadingOnScreenRemove();
                }
            }
        });
    }

    window.updateUsedApps = function () {
        $.ajax({
            method: 'GET',
            url: '/api/apps',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.page-content',true);
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (response.hotzappIntegrations > 0) {
                    $('#hotzapp-bt').addClass('added');
                    $('#hotzapp-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#hotzapp-bt').removeClass('added');
                    $('#hotzapp-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.shopifyIntegrations > 0) {
                    $('#shopify-bt').addClass('added');
                    $('#shopify-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#shopify-bt').removeClass('added');
                    $('#shopify-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.notazzIntegrations > 0) {
                    $('#notazz-bt').addClass('added');
                    $('#notazz-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#notazz-bt').removeClass('added');
                    $('#notazz-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.convertaxIntegrations > 0) {
                    $('#convertax-bt').addClass('added');
                    $('#convertax-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#convertax-bt').removeClass('added');
                    $('#convertax-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.activecampaignIntegrations > 0) {
                    $('#activecampaign-bt').addClass('added');
                    $('#activecampaign-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#activecampaign-bt').removeClass('added');
                    $('#activecampaign-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.digitalmanagerIntegrations > 0) {
                    $('#digitalmanager-bt').addClass('added');
                    $('#digitalmanager-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#digitalmanager-bt').removeClass('added');
                    $('#digitalmanager-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.whatsapp2Integrations > 0) {
                    $('#whatsapp2-bt').addClass('added');
                    $('#whatsapp2-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#whatsapp2-bt').removeClass('added');
                    $('#whatsapp2-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.reportanaIntegrations > 0) {
                    $('#reportana-bt').addClass('added');
                    $('#reportana-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#reportana-bt').removeClass('added');
                    $('#reportana-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.unicodropIntegrations > 0) {
                    $('#unicodrop-bt').addClass('added');
                    $('#unicodrop-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#unicodrop-bt').removeClass('added');
                    $('#unicodrop-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.smartfunnelIntegrations > 0) {
                    $('#smartfunnel-bt').addClass('added');
                    $('#smartfunnel-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#smartfunnel-bt').removeClass('added');
                    $('#smartfunnel-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.woocommerceIntegrations > 0) {
                    $('#woocom-bt').addClass('added');
                    $('#woocom-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#woocom-bt').removeClass('added');
                    $('#woocom-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.astronmembersIntegrations > 0) {
                    $('#astronmembers-bt').addClass('added');
                    $('#astronmembers-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#astronmembers-bt').removeClass('added');
                    $('#astronmembers-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.notificacoesinteligentesIntegrations > 0) {
                    $('#notificacoesinteligentes-bt').addClass('added');
                    $('#notificacoesinteligentes-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#notificacoesinteligentes-bt').removeClass('added');
                    $('#notificacoesinteligentes-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.hotbilletIntegrations > 0) {
                    $('#hotbillet-bt').addClass('added');
                    $('#hotbillet-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#hotbillet-bt').removeClass('added');
                    $('#hotbillet-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }

                if (response.melhorenvioIntegrations > 0) {
                    $('#menv-bt').addClass('added');
                    $('#menv-icon').removeClass('o-add-1').addClass('o-checkmark-1');
                }
                else{
                    $('#menv-bt').removeClass('added');
                    $('#menv-icon').removeClass('o-checkmark-1').addClass('o-add-1');
                }
                loadOnAny('.page-content',true);
                loadingOnScreenRemove();
            }
        });
    }

    $('.app-integration').on('click', function () {
        if (verifyAccountFrozen() == false) {
            window.location.href = $(this).data('url');
        }
    });
});
