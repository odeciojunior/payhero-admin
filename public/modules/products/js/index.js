$(document).ready(function () {

    $("#procurar").on("click", function () {
        window.location.href = "/products?nome=" + $('#nome').val();
    });

    $(".product-image").on("click", function () {
        window.location.href = $(this).attr('data-link');
    });
});
