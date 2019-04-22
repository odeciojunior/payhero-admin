$(document).ready(function(){

    $("#notifications_button").on("click", function(){

        $.ajax({
            method: "POST",
            url: "/notificacoes/markasread",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function() {
                // erro
            },
            success: function(data) {
                //
            }
        });

        $("#qtd_notificacoes").html('0');

    });

});
