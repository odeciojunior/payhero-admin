$(document).ready(function () {

    $("#procurar").on("click", function () {
        window.location.href = "/products?nome=" + $('#nome').val();
    });

    $('.delete-product').on('click', function () {
        var productName = $('#nome').val();
        $('#model-delete-title').text('Excluir o produto ' + productName + '?');

    });

});
