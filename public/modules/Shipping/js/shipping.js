$(function () {

    atualizarFrete();

    function atualizarFrete() {
        $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");

        $.ajax({
            method: "GET",
            url: '',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                //
            },
            success: function (response) {
                $("#dados-tabela-frete").html('');
                $.each(response.data, function (index, value) {

                });
            }
        });
    }

});
