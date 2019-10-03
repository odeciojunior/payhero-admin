$(document).ready(function () {

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    // Comportamentos da tela
    $("#type-products").on('change', function () {

        if ($(this).val() === "1") {
            $('#is-projects').show();
            $('#btn-filtro').parent().removeClass('offset-md-3');
            $('#div-create').hide();
        } else {
            $('#is-projects').hide();
            $('#btn-filtro').parent().addClass('offset-md-3');
            $('#div-create').show();
        }
    });

    $('#btn-filtro').on('click', function () {
        updateProducts();
    });

    getTypeProducts();

    updateProducts();

    function getTypeProducts() {
        $.ajax({
            method: 'GET',
            url: '/api/projects?select=true',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                $.each(response.data, function (index, value) {
                    if (value.shopify) {
                        $("#select-projects").append($('<option>', {
                            value: value.id,
                            text: value.name
                        }));
                    }
                });
            }
        });
    }

    function updateProducts(link = null) {

        loadOnAny('.page-content');

        if (link == null) {
            link = '/api/products?shopify=' + $("#type-products").val() + '&project=' + $('#select-projects').val() + '&name=' + $('#name').val();
        } else {
            link = '/api/products' + link + '&shopify=' + $("#type-products").val() + '&project=' + $('#select-projects').val() + '&name=' + $('#name').val();
        }

        $.ajax({
            method: 'GET',
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (!isEmpty(response.data)) {
                    $(".products-is-empty").hide();
                    $("#data-table-products").html('');

                    $.each(response.data, function (index, value) {
                        dados = '';
                        dados += '<div class="col-xl-3 col-md-6 d-flex align-items-stretch">';
                        dados += '<div class="card shadow" style="cursor:pointer;width:100%;">';
                        dados += '<img style="min-height: 200px;" class="card-img-top product-image" src="' + value.image + '"  ' +
                            'data-link=" + value.link + " alt="Imagem do produto" data-code="' + value.id + '">' +
                            '<div class="card-body">' +
                            '<div class="row align-items-end justify-content-between">' +
                            '<div class="col-10">' +
                            '<h5 class="card-title">' + value.name + '</h5>' +
                            '<h5 class="">' + value.description + '</h5>' +
                            '<p class="card-text sm">Criado em  ' + value.created_at + '</p>' +
                            '<p class="card-text sm">ID: ' + value.id_view + '</p>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';

                        $("#data-table-products").append(dados);
                    });

                    $(".product-image").on("click", function () {
                        window.location.href = "/products/" + $(this).data('code') + "/edit";
                    });

                    $("img").on("error", function () {
                        $(this).attr("src", "https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/product-default.png");
                    });

                    pagination(response, 'products', updateProducts);

                    $(".products-is-empty").hide();
                } else {
                    $("#data-table-products, #pagination-products").html('');
                    $(".products-is-empty").show();
                }
                loadOnAny('.page-content', true);
            }
        });
    }
});
