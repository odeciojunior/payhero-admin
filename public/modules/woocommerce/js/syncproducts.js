$(document).ready(function () {
    //let allCompanyNotApproved = false;
    
    
    
    
    $('#bt-modal-sync-woocommerce').click(function () {
        
        var projectId = $(window.location.pathname.split('/')).get(-1);
        
        var opt_prod = $('#opt_prod').is(':checked')
        var opt_track = $('#opt_track').is(':checked')

        
        
        if(!opt_prod && !opt_track){
            alertCustom('error', 'Selecione uma ou mais categorias de dados para sincronizar!');
            $('#close-modal').click()

            return false;
        }

        


        $("#_content").hide();
        $("#_loading").show();


        $.ajax({
            method: "POST",
            data: {"opt_prod":opt_prod, "opt_track":opt_track},
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
                
                
                $('#close-modal').click()
                
                setTimeout(function(){
                    $("#_loading").hide();
                    $("#_content").show();
                },200);

                if(r.status == true){
                    alertCustom('success', 'Sincronização de dados foi iniciada!');

                }else{
                    alertCustom('error', 'Já existe uma sincronização de dados em andamento!');

                }
                
            }
        });


        


    })

    function fun() {
        
    }

});
