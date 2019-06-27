$(document).ready(function () {

    $("#pop-antecipacao").click(function () {
        if($("#antecipa-popover").css('display') == 'none'){
            $("#antecipa-popover").fadeIn(200);
        }
        else{
            $("#antecipa-popover").fadeOut(100);
        }
    });
});