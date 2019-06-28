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
                $(".currency").html(response.currency);
                $(".available-balance").html(response.available_balance);
                $(".antecipable-balance").html(response.antecipable_balance);
                $(".pending-balance").html(response.pending_balance);
                $(".total-balance").html(response.total_balance);
            }
        });

    }

});


