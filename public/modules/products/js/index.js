$(document).ready(function () {
    /**
     * Helper Functions
     */

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    getTypeProducts();

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
                    console.log(response.data);
                    $("#type-products").append($('<option>', {
                        value: '0',
                        text: 'Meus Produtos'
                    }));

                    $("#type-products").append($('<option>', {
                        value: '1',
                        text: 'Produtos Shopify'
                    }));

                    if ($("#type-products").val() === "1") {
                        $("#select-projects").html('');

                        $.each(response.data, function (index, value) {
                            if (value.shopify) {
                                $("#select-projects").append($('<option>', {
                                    value: value.id,
                                    text: value.name
                                }));
                            }
                        });
                        $("#is-projects").show();

                        $("#select-projects").find('option:eq(0)').prop('selected', true);
                    } else {
                        $("#is-projects").hide();

                    }

                    $("#type-products").on('change', function () {
                        if ($("#type-products").val() === "1") {
                            $("#select-projects").html('');
                            if (response.data.length == 0) {

                                $("#select-projects").hide();
                                $(".product-is-empty-cla").hide();

                            } else {
                                $.each(response.data, function (index, value) {
                                    if (value.shopify) {
                                        $("#select-projects").append($('<option>', {
                                            value: value.id,
                                            text: value.name
                                        }));
                                    }

                                });
                                $("#is-projects").show();
                                $("#select-projects").find('option:eq(0)').prop('selected', true);
                                $(".product-is-empty-cla").hide();

                            }

                        } else {
                            $("#is-projects").hide();
                        }

                        if ($("#type-products").val() === "0") {
                            $(".product-is-empty-cla").show();

                        }
                    });

                    updateProducts();

                    $('#type-products').on('change', function () {
                        if ($(this).val() == 1) {
                            $("#div-create").hide();
                        } else if ($(this).val() == 0) {
                            $("#div-create").show();
                        }
                        updateProducts();
                    });

                    $('#select-projects').on('change', function () {
                        updateProducts();
                    });

                }
            }
        );
    }

    function updateProducts(link = null) {
        if (link == null) {
            link = '/api/products?shopify=' + $("#type-products").val() + '&project=' + $('#select-projects').val();
        } else {
            link = '/api/products' + link + '&shopify=' + $("#type-products").val() + '&project=' + $('#select-projects').val();
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

                    pagination(response, 'products', updateProducts)

                } else {
                    $("#data-table-products").html('');
                    $("#pagination-products").html('');

                    $(".products-is-empty").show();

                }

            }
        });
    }

})
;
