$(document).ready(function(){

    updateUsedApps();

    function updateUsedApps() {
        $.ajax({
            method: 'GET',
            url: '/api/apps',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                if(response.hotzappIntegrations > 0){
                    $('#hotzapp-bt').addClass('added');
                    $('#hotzapp-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.shopifyIntegrations > 0){
                    $('#shopify-bt').addClass('added');
                    $('#shopify-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.notazzIntegrations > 0){
                    $('#notazz-bt').addClass('added');
                    $('#notazz-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.convertaxIntegrations > 0){
                    $('#convertax-bt').addClass('added');
                    $('#convertax-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.activecampaignIntegrations > 0){
                    $('#activecampaign-bt').addClass('added');
                    $('#activecampaign-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.digitalmanagerIntegrations > 0){
                    $('#digitalmanager-bt').addClass('added');
                    $('#digitalmanager-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.whatsapp2Integrations > 0){
                    $('#whatsapp2-bt').addClass('added');
                    $('#whatsapp2-icon').removeClass('wb-plus').addClass('wb-check');
                }
                if(response.hotsacIntegrations > 0){
                    $('#hotsac-bt').addClass('added');
                    $('#hotsac-icon').removeClass('wb-plus').addClass('wb-check');
                }
            }
        });
    }


});

