$(document).ready(function () {

    $("#pop-antecipacao").click(function () {
        if($("#antecipa-popover").css('display') == 'none'){
            $("#antecipa-popover").fadeIn(200);
        }
        else{
            $("#antecipa-popover").fadeOut(100);
        }
    });

    $("#transfers_company_select").on("change",function(){
        $("#extract_company_select").val($(this).val());
        updateBalances();
    });

    $("#extract_company_select").on("change",function(){
        $("#transfers_company_select").val($(this).val());
        updateBalances();
    });

    updateBalances();

    function updateBalances(){
            $(".price").append("<span class='loading'>" +
            "<span class='loaderSpan' >" +
            "</span>" +
            "</span>");


        $.ajax({
            url : "/finances/getbalances/" + $("#transfers_company_select").val(),
            type : "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            },
            success : function(response) {
                $('.saldoPendente').html('<span class="currency">R$</span><span class="pending-balance">0,00</span>')
                $('.removeSpan').remove();
                $('.disponivelAntecipar').append('<span class="currency removeSpan">R$</span><span class="antecipable-balance removeSpan">0,00</span>')
                $('.saldoDisponivel').html('<span class="currency">R$</span><span class="available-balance">0,00 <i class="material-icons ml-5" style="color: #44a44b;">arrow_forward</i></span>')
                $('.saltoTotal').html('<span class="currency">R$</span><span class="total-balance">0,00</span>')
                $('.totalConta').html('<span class="currency">R$</span><span class="total-balance">0,00</span>')
                $(".currency").html(response.currency);
                $(".available-balance").html(response.available_balance);
                $(".antecipable-balance").html(response.antecipable_balance);
                $(".pending-balance").html(response.pending_balance);
                $(".total-balance").html(response.total_balance);
                $(".loading").remove();
            }
        });

    }

});


