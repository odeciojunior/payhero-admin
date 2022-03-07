$(document).ready(function () {

    var projectId = $(window.location.pathname.split('/')).get(-1);

    $.ajax({
        method: "POST",
        url: "/api/apps/woocommerce/keys/get?projectId="+projectId,
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

            $('#consumer_k').attr('placeholder', r.consumer_k+'...')
            $('#consumer_s').attr('placeholder', r.consumer_s+'...')


        }
    });

    $('#bt-modal-woocommerce-apikeys').click(function () {

        var consumer_key = $('#consumer_k').val()
        var consumer_secret = $('#consumer_s').val()

        if(!consumer_key || !consumer_secret){
            alertCustom('error', 'Informe os novos valores das chaves de acesso!');
            return false;
        }

        $.ajax({
            method: "POST",
            data: {"consumer_key":consumer_key, "consumer_secret":consumer_secret},
            url: "/api/apps/woocommerce/keys/update?projectId="+projectId,
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


                if(r.status == true){
                    alertCustom('success', 'Chaves de acesso atualizadas com sucesso!');

                }else{
                    alertCustom('error', 'Erro ao atualizar as chaves!');

                }

            }
        });
    })



    $('#bt-modal-sync-woocommerce').click(function () {



        var opt_prod = $('#opt_prod').is(':checked')
        var opt_track = $('#opt_track').is(':checked')
        var opt_webhooks = $('#opt_webhooks').is(':checked')



        if(!opt_prod && !opt_track && !opt_webhooks){
            alertCustom('error', 'Selecione uma ou mais categorias de dados para sincronizar!');
            $('#close-modal').click()

            return false;
        }




        $("#_content").hide();
        $("#_loading").show();


        $.ajax({
            method: "POST",
            data: {"opt_prod":opt_prod, "opt_track":opt_track, "opt_webhooks":opt_webhooks},
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
