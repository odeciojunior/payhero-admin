$(document).ready(function () {
    let code = window.location.href.split('/')[4];
    getDataProducts();
    function getDataProducts() {
        $.ajax({
            method: 'GET',
            url: '/api/products/' + code + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
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

                    var p = $("#previewimage");
                    $("#photo").on("change", function () {

                        var imageReader = new FileReader();
                        imageReader.readAsDataURL(document.getElementById("photo").files[0]);

                        imageReader.onload = function (oFREvent) {
                            p.attr('src', oFREvent.target.result).fadeIn();

                            p.on('load', function () {

                                var img = document.getElementById('previewimage');
                                var x1, x2, y1, y2;

                                if (img.naturalWidth > img.naturalHeight) {
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                    x2 = x1 + (y2 - y1);
                                } else {
                                    if (img.naturalWidth < img.naturalHeight) {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                        y2 = y1 + (x2 - x1);
                                    } else {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    }
                                }

                                $('input[name="photo_x1"]').val(x1);
                                $('input[name="photo_y1"]').val(y1);
                                $('input[name="photo_w"]').val(x2 - x1);
                                $('input[name="photo_h"]').val(y2 - y1);

                                $('#previewimage').imgAreaSelect({
                                    x1: x1, y1: y1, x2: x2, y2: y2,
                                    aspectRatio: '1:1',
                                    handles: true,
                                    imageHeight: this.naturalHeight,
                                    imageWidth: this.naturalWidth,
                                    onSelectEnd: function onSelectEnd(img, selection) {
                                        $('input[name="photo_x1"]').val(selection.x1);
                                        $('input[name="photo_y1"]').val(selection.y1);
                                        $('input[name="photo_w"]').val(selection.width);
                                        $('input[name="photo_h"]').val(selection.height);
                                    }
                                });

                            });
                        };
                    });

                    $("#previewimage").on("click", function () {
                        $("#photo").click();
                    });

                    $("#my-form").submit(function (event) {
                        if ($('#photo_w').val() == '0' || $('#photo_h').val() == '0') {
                            alertCustom('error', 'Selecione as dimensões da imagem');
                            return false;
                        }
                        event.preventDefault();

                        let myForm = document.getElementById('my-form');
                        let formData = new FormData(myForm);

                        if (verify) {
                            loadingOnScreen();
                            $.ajax({
                                method: 'POST',
                                url: "/api/products/" + response.data.product.id,
                                processData: false,
                                cache: false,
                                contentType: false,
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                data: formData,
                                error: function (response) {
                                    loadingOnScreenRemove();

                                    errorAjaxResponse(response);

                                }, success: function (response) {
                                    loadingOnScreenRemove();
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

        var product = $('.delete-product').attr('product');
        $("#bt_excluir").unbind('click');
        $("#bt_excluir").on('click', function () {
            $("#close-modal-delete").click();
            loadingOnScreen();

            $.ajax({
                method: 'DELETE',
                url: '/api/products/' + product,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
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
                    errorAjaxResponse(response);
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
