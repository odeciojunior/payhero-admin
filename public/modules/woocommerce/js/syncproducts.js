$(document).ready(function () {
    //let allCompanyNotApproved = false;
    
    // $.ajax({
    //     method: "GET",
    //     url: "/api/companies?select=true",
    //     dataType: "json",
    //     headers: {
    //         'Authorization': $('meta[name="access-token"]').attr('content'),
    //         'Accept': 'application/json',
    //     },
    //     error: function error(response) {
    //         $("#modal-content").hide();
    //         errorAjaxResponse(response);
    //     },
    //     success: function success(response) {
    //         create(response.data);

    //         htmlAlertWooCommerce();
    //         loadingOnScreenRemove();
    //     }
    // });

    $('#bt-modal-sync-woocommerce').click(function () {
        
        $('#close-modal').click()

        alertCustom('success', 'A sincronização está em andamento!');

    })

    function fun() {
        
    }

});
