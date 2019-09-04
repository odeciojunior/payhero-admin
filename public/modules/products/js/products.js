$(document).ready(function () {

    let code = window.location.href.split('/')[4];

    getDataProducts();
    function getDataProducts() {
        $.ajax({
            method: 'GET',
            url: '/api/products/' + code + '/edit',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.responseJSON.message);
                }
            },
            success: function (response) {
                if (!isEmpty(response.data.product)) {

                    /**
                     * Se for produto shopify o botao delete nao aparece
                     */
                    if (!response.data.product.shopify_id) {
                        $(".delete-product").show();
                        $('.delete-product').attr('product', response.data.product.id);
                        $('.delete-product').attr('productname', response.data.product.name);

                    }

                    /**
                     * Select com as categorias
                     */
                    $.each(response.data.categories, function (i, category) {
                        $("#select-categories").append($('<option>', {
                            value: category.id,
                            text: category.name
                        }));

                    });

                    $("#select-categories  option[value='" + response.data.product.category_id + "']").prop("selected", true);

                    /**
                     * Image
                     */
                    $("#previewimage").attr('src', response.data.product.photo);

                    $("img").on("error", function () {
                        $(this).attr("src", "https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/product-default.png");
                    });

                    $("#name").val(response.data.product.name);
                    $("#description").text(response.data.product.description);
                    $("#cost").unmask().val(response.data.product.cost).mask('000.000.000.000.000,00', {reverse: true});
                    $("#price").unmask().val(response.data.product.price).mask('000.000.000.000.000,00', {reverse: true});
                    $("#height").unmask().val(response.data.product.height);
                    $("#width").unmask().val(response.data.product.width);
                    $("#weight").unmask().val(response.data.product.weight);

                    $("#my-form").submit(function (event) {
                        event.preventDefault();
                        let myForm = document.getElementById('my-form');
                        let formData = new FormData(myForm);

                        if (verify) {
                            $.ajax({
                                method: 'POST',
                                url: "/api/products/" + response.data.product.id,
                                processData: false,
                                cache: false,
                                contentType: false,
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                },
                                data: formData,
                                dataType: "json",
                                error: function (response) {
                                    console.log(response);
                                    if (response.status === 422) {
                                        for (error in response.responseJSON.errors) {
                                            alertCustom('error', String(response.responseJSON.errors[error]));
                                        }
                                    } else {
                                        alertCustom('error', response.responseJSON.message);
                                    }
                                }, success: function (response) {
                                    alertCustom('success', response.message);
                                    window.location = "/products";
                                }
                            });
                        }
                    });

                } else {
                    alertCustom('success', 'Ocorreu um erro, tente novamente mais tarde');
                    window.location = "/products";
                }
            }
        });
    }

    /**
     * Verifica se existe algum objeto vazio
     * @param obj
     * @returns {boolean}
     */
    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    /**
     * Helper verifica campos
     * @returns {boolean}
     */
    function verify() {
        let ver = true;
        if ($('#name').val() == '') {
            alertCustom("error", "O campo Nome é obrigatório");
            ver = false;
        }
        if ($("#description") == '') {
            alertCustom("error", "O campo Descrição é obrigatório");
            ver = false;
        }
        return ver;
    }

    $('.money').mask('#.###,#0', {reverse: true});

    $("#shipping").on("change", function () {

        if ($(this).val() == 'proprio') {
            $("#div_carrier_id").hide();
        } else {
            $("#div_carrier_id").show();
        }
    });

    $('input[type=radio][name=format]').change(function () {
        if (this.value == '1') {
            $("#nav-logistic-tab").show();
            $("#div_next_step").show();
            $("#div_save_digital_product").hide();
            $("#div_digital_product_upload").css('visibility', 'hidden');
        } else if (this.value == '0') {
            $("#nav-logistic-tab").hide();
            $("#div_next_step").hide();
            $("#div_save_digital_product").show();
            $("#div_digital_product_upload").css('visibility', 'visible');
        }
    });

    $("#next_step").on("click", function () {
        $("#nav-logistic-tab").click();
        $("#previewimage").imgAreaSelect({remove: true});
    });

    $(".delete-product").on('click', function (event) {
        event.preventDefault();
        loadingOnScreen();

        var product = $('.delete-product').attr('product');
        $("#bt_excluir").unbind('click');
        $("#bt_excluir").on('click', function () {
            $("#close-modal-delete").click();
            $.ajax({
                method: 'DELETE',
                url: '/api/products/' + product,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (_error) {
                    function error(_x) {
                        return _error.apply(this, arguments);
                    }

                    error.toString = function () {
                        return _error.toString();
                    };

                    return error;
                }(function (response) {
                    loadingOnScreenRemove();

                    if (response.status === 422) {
                        for (error in response.responseJSON.errors) {
                            alertCustom('error', String(response.responseJSON.errors[error]));
                        }
                    } else {
                        alertCustom('error', response.responseJSON.message);

                    }

                }),
                success: function success(response) {
                    loadingOnScreenRemove();

                    alertCustom('success', response.message);
                    window.location = "/products";
                }
            });
        });
    });
});
