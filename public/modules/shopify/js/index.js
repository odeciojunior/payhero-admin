$(document).ready(function () {

    $("#bt_adicionar_integracao").on("click", function () {

        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
            alertPersonalizado('error', 'Dados informados inválidos');
            return false;
        }
        $('.loading').css("visibility", "visible");

        var form_data = new FormData(document.getElementById('form_add_integracao'));

        $.ajax({
            method: "POST",
            url: "/apps/shopify/adicionarintegracao",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                $('.loading').css("visibility", "hidden");
                alertPersonalizado('error', response.responseJSON.message);//'Ocorreu algum erro'
            },
            success: function (response) {
                $('.loading').css("visibility", "hidden");
                alertPersonalizado('success', response.message);
                window.location.reload(true);
            },
        });

    });

    function alertPersonalizado(tipo, mensagem) {

        swal({
            position: 'bottom',
            type: tipo,
            toast: 'true',
            title: mensagem,
            showConfirmButton: false,
            timer: 6000
        });
    }

});
