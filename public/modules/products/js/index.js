$(document).ready(function () {

    $("#procurar").on("click", function () {
        window.location.href = "/products?nome=" + $('#nome').val();
    });

    $('.delete-product').on('click', function () {
        var producId = $(this).attr('product');
        var productName = $('#nome').val();

        $('#form-delete-product').attr('action', '/products/'+producId);
        $('#model-delete-title').text('Excluir o produto ' + productName + '?');
    });

    $(".product-image").on("click", function(){
        window.location.href = $(this).attr('data-link');
    });

});



