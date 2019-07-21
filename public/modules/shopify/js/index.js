$(document).ready(function () {

    $("#bt_add_integration").on("click", function () {

        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        $('.loading').css("visibility", "visible");

        var form_data = new FormData(document.getElementById('form_add_integration'));

        $.ajax({
            method: "POST",
            url: "/apps/shopify",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                $('.loading').css("visibility", "hidden");
                alertCustom('error', response.responseJSON.message);//'Ocorreu algum erro'
            },
            success: function (response) {
                $('.loading').css("visibility", "hidden");
                alertCustom('success', response.message);
            },
        });

    });

});
