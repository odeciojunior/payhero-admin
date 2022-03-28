$(document).ready(function(){

    $('#finalizar_compra_cartao').on('click', function(){

        if(!validarCheckout('cartao')){
            mensagemDadosInvalidos();
            return false;
        }

        var card = {};
        card.card_holder_name = $("#card-name").val();
        card.card_expiration_date = $("#card-expiration").val();
        card.card_number = $("#card-number").val();
        card.card_cvv = $("#card-cvv").val();

        var cardValidations = pagarme.validate({card: card});

        if(!cardValidations.card.card_number)
            alert('Oops, número de cartão incorreto');

        pagarme.client.connect({ encryption_key: encryption_key })
        .then(
            client => client.security.encrypt(card)
        )
        .then( function(card_hash){

            var form_data = new FormData(document.getElementById('formulario_pagamento'));
            form_data.append('card_hash',card_hash);

            $.ajax({
                method: "POST",
                url: "/checkout/pagamentocartao",
                processData: false,
                contentType: false,
                cache: false,
                data: form_data,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                beforeSend: () => {
                    $('.ajax-loader').css("visibility", "visible");
                    // efetuandoPagamento();
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success:(response) => {
                    if(response.sucesso){
                        window.location.replace("/ferramentas/sms");
                    }
                    if(response.erro){
                        swal({
                            position: 'top-end',
                            type: 'error',
                            toast: 'true',
                            title: response.mensagem,
                            showConfirmButton: false,
                            timer: 8000
                        });
                    }
                },
                complete: function(){
                    $('.ajax-loader').css("visibility", "hidden");
                }
            });

        });

        return false;
    });

    $('#finalizar_compra_boleto').on('click', function(e){

        if(!validarCheckout('boleto')){
            mensagemDadosInvalidos();
            return false;
        }

        e.preventDefault();

        var form_data = new FormData(document.getElementById('formulario_pagamento'));

        $.ajax({
            method: "POST",
            url: "/checkout/pagamentoboleto",
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            beforeSend: function(){
                $('.ajax-loader').css("visibility", "visible");
                // efetuandoPagamento();
            },
            error: (response) => {
                errorAjaxResponse(response)
            },
            success: () => {
                window.location.replace("/ferramentas/sms");
            },
            complete: function(){
                $('.ajax-loader').css("visibility", "hidden");
            }
        });

    });

    function mensagemDadosInvalidos(){

        swal({
            position: 'top-end',
            type: 'error',
            toast: 'true',
            title: 'Verifique os dados informados',
            showConfirmButton: false,
            timer: 6000
        });
    }

});

