$(document).ready(function () {
    //let allCompanyNotApproved = false;
    
    
    
    
    $('#bt-modal-sync-woocommerce').click(function () {
        
        var projectId = $(window.location.pathname.split('/')).get(-1);
        
        

        $.ajax({
            method: "POST",
            
            url: "/api/apps/woocommerce/synchronize/products?projectId="+projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                $("#modal-content").hide();
                errorAjaxResponse(response);
            },
            success: function success(r) {
                
                
                if(r.status == true){
                    alertCustom('success', r.msg);

                }else{
                    alertCustom('error', r.msg);

                }
                

                
                
            }
        });


        
        $('#close-modal').click()


    })

    function fun() {
        
    }

});
