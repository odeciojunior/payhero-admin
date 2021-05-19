$(document).ready(function(){

    $('.delete-product').on('click', function () {
        var producId = $(this).attr('product');
        var productName = $('#nome').val();

        $('#form-delete-product').attr('action', '/products/'+producId);
        $('#model-delete-title').text('Excluir o produto ' + productName + '?');
    });

});

