$(document).ready(function () {
    let code = window.location.href.split('/')[4];
    let typeEnum;
    getDataProducts();
    function getDataProducts() {
        loadingOnScreen();

        $.ajax({
            method: 'GET',
            url: '/api/products/' + code + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (!isEmpty(response.data.product)) {

                    /**
                     * Se for produto shopify o botao delete nao aparece
                     *//*
                    if (!response.data.product.shopify_id) {
                        $(".delete-product").show();
                        $('.delete-product').attr('product', response.data.product.id);
                        $('.delete-product').attr('productname', response.data.product.name);

                    }*/

                    $(".delete-product").show();
                    $('.delete-product').attr('product', response.data.product.id);
                    $('.delete-product').attr('productname', response.data.product.name);

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
                        $(this).attr("src", "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/product-default.png");
                    });

                    $("#name").val(response.data.product.name);
                    $("#description").text(response.data.product.description);
                    if (!isEmpty(response.data.product.sku)) {
                        $('#sku input').val(response.data.product.sku);
                        $('#sku').show();
                    } else {
                        $('#sku').hide();
                    }
                    $("#cost").unmask().val(response.data.product.cost).mask('000.000.000.000.000,00', {reverse: true});
                    $("#price").unmask().val(response.data.product.price).mask('000.000.000.000.000,00', {reverse: true});
                    $("#height").unmask().val(response.data.product.height);
                    $("#length").unmask().val(response.data.product.length);
                    $("#width").unmask().val(response.data.product.width);
                    $("#weight").unmask().val(response.data.product.weight);

                    //select moeda
                    if (response.data.product.currency_type_enum == 1) {
                        $('#select-currency .select-currency-brl').attr('selected', true);
                    } else {
                        $('#select-currency .select-currency-usd').attr('selected', true);
                    }
                    typeEnum = response.data.product.type_enum;

                    //seleciona radio button product type
                    if (response.data.product.type_enum == 1) {
                        $('#physical').attr('checked', true);
                        $('#div_digital_product_upload').css('visibility', 'hidden');
                        $('#nav-logistic-tab').css('visibility', 'visible');
                        $("#div_digital_product_upload").addClass('d-none');
                        $('#digital_product_url').dropify();
                    } else {
                        $('#digital').attr('checked', true);
                        $('#div_digital_product_upload').css('visibility', 'visible');
                        $('#nav-logistic-tab').css('visibility', 'hidden');
                        $("#div_digital_product_upload").removeClass('d-none');
                        $('#digital_product_url').dropify({
                            messages: {
                                'default': 'Arraste e solte ou clique para adicionar um arquivo',
                                'replace': 'Arraste e solte ou clique para substituir',
                            },
                            defaultFile: response.data.product.digital_product_url,
                        });
                        if (response.data.product.digital_product_url != '') {
                            $(".btn-view-product-url").attr('link', response.data.product.digital_product_url);
                            $(".btn-view-product-url").show();
                        }
                        $('.div-expiration-time').show();
                    }

                    $('#url_expiration_time').val(response.data.product.url_expiration_time);

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
                        if ($('#digital').is(':checked') && $('#url_expiration_time').val() == '') {
                            alertCustom('error', 'Preencha o campo Tempo de expiração da url');
                            return false;
                        }
                        event.preventDefault();

                        let myForm = document.getElementById('my-form');

                        let formData = new FormData(myForm);

                        if ($('#physical').is(':checked')) {
                            formData.append('type_enum', 'physical');
                        } else {
                            formData.append('type_enum', 'digital');
                        }

                        if (verify()) {
                            loadOnAny('.page', false);
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
                                    loadOnAny('.page', true);

                                    errorAjaxResponse(response);

                                }, success: function (response) {
                                    loadOnAny('.page', true);
                                    alertCustom('success', response.message);
                                    // window.location = "/products";
                                }
                            });
                        }
                    });

                } else {
                    alertCustom('success', 'Ocorreu um erro, tente novamente mais tarde');
                    // window.location = "/products";
                }

                loadingOnScreenRemove();
            }
        });
    }

    /**
     * Helper verifica campos
     * @returns {boolean}
     */
    function verify() {
        let ver = true;
        if ($.trim($('#name').val()) === '') {
            $("#nav-basic-tab").click();
            $('#name').focus();
            alertCustom("error", "O campo Nome é obrigatório");
            ver = false;
        }
        if ($.trim($("#description").val()) === '') {
            $("#nav-basic-tab").click();
            $("#description").focus();
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

    $(".btn-view-product-url").on("click", function (event) {
        event.preventDefault();
        var url = $(this).attr('link');
        var expirationTime = $('#url_expiration_time').val();
        loadingOnScreen();
        $.ajax({
            method: 'POST',
            url: '/api/products/getsignedurl',
            dataType: "json",
            data: {digital_product_url: url, url_expiration_time: expirationTime},
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response)
            },
            success: function (response) {
                loadingOnScreenRemove();
                window.open(response.signed_url, '_blank');
            },
        });
    });

    $('#url_expiration_time').mask('0#');

    $("#physical").on("change", function () {
        $('#div_digital_product_upload').css('visibility', 'hidden');
        $('#nav-logistic-tab').css('visibility', 'visible');
        $("#div_digital_product_upload").addClass('d-none');
        $('.div-expiration-time').hide();
        $('#url_expiration_time').val('');
    });

    $("#digital").on("change", function () {
        if (typeEnum == 1) {
            $.ajax({
                method: 'POST',
                url: '/api/products/verifyproductinplan',
                dataType: "json",
                data: {product_id: code},
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    errorAjaxResponse(response)
                },
                success: function (response) {
                    if (response.product_in_plan) {
                        $('#modal-plan-already-created').modal('show');
                        $('#physical').click();
                    } else {
                        $('#div_digital_product_upload').css('visibility', 'visible');
                        $('#nav-logistic-tab').css('visibility', 'hidden');
                        $("#div_digital_product_upload").removeClass('d-none');
                        $('.div-expiration-time').show();
                    }
                },
            });
        } else {
            $('#div_digital_product_upload').css('visibility', 'visible');
            $('#nav-logistic-tab').css('visibility', 'hidden');
            $("#div_digital_product_upload").removeClass('d-none');
            $('.div-expiration-time').show();
        }
    });
    // $(".btn-close-modal-plan").on("change", function () {
    //
    // });

    // Produto Fisico
    $('#height').on('focus', function () { $('#caixinha-img')[0].src = 'http://dev.admin.net/modules/global/img/svg/caixinha-altura.svg' });
    $('#width').on('focus', function () { $('#caixinha-img')[0].src = 'http://dev.admin.net/modules/global/img/svg/caixinha-largura.svg' });
    $('#length').on('focus', function () { $('#caixinha-img')[0].src = 'http://dev.admin.net/modules/global/img/svg/caixinha-comprimento.svg' });
    $('#height, #width, #length').on('focusout', function () { $('#caixinha-img')[0].src = 'http://dev.admin.net/modules/global/img/svg/caixinha.svg' });
});
