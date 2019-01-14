$(document).ready( function(){

    $('#etapa_pagamento').html('2');

    atualizarValorTotal();

    function atualizarValorTotal(){

        valor_total = valor_compra;
        $('.valor_total').html(valor_total);

        $.ajax({
            method: "POST",
            url: "/checkout/getparcelas",
            data: { valor_total: valor_total },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function() {
                // erro
            },
            success: function(data) {

                var installments = data.installments;

                var options = '';

                $.each(installments, function(i, parcela) {
                    options += "<option value='"+parcela.installment+"'>"+parcela.installment+' x R$ '+parcela.installment_amount + ' ( R$ '+parcela.amount + ')'+"</option>";
                });

                $('#select_parcelas_desktop').html(options);
                $('#select_parcelas_mobile').html(options);
            }
        });

    }

});

