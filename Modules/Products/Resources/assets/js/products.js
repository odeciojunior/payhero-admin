getCompaniesAndProjects().done( function (data){
    $('.site-navbar .sirius-select-container').addClass('disabled');
});

loadingOnScreen();

$(document).ready(function () {
    let code = window.location.href.split('/')[4];
    let typeEnum;

    getDataProducts();

    function getDataProducts() {

        dropifyOptions = {
            messages: {
                'default': 'Arraste e solte uma imagem ou ',
                'replace': 'Arraste e solte uma imagem ou selecione um arquivo',
                'remove': 'Remover',
                'error': ''
            },
            error: {
                'fileSize': 'O tamanho máximo do arquivo deve ser {{ value }}.',
                'minWidth': 'A imagem deve ter largura maior que 651px.',
                'maxWidth': 'A imagem deve ter largura menor que 651px.',
                'minHeight': 'A imagem deve ter altura maior que 651px.',
                'maxHeight': 'A imagem deve ter altura menor que 651px.',
                'fileExtension': 'A imagem deve ser algum dos formatos permitidos. ({{ value }}).'
            },
            tpl: {
                message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">selecione um arquivo</span></p></div>',
                clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
            },
            imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg']
        };

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

                $('#product_photo').dropify(dropifyOptions);
            },
            success: function (response) {
                if (!isEmpty(response.data.product)) {
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
                     * Se nao for produto shopify o botao converter nao aparece
                     *
                    */
                    if (!response.data.product.shopify_variant_id) {
                        $(".converte-product").parent().hide();
                    }
                    $('.converte-product').attr('product', response.data.product.id);
                    $('.converte-product').attr('productname', response.data.product.name);

                    /**
                     * Se for produto shopify o botao delete nao aparece
                     *
                    */
                    if (response.data.product.shopify_variant_id) {
                        $(".delete-product").parent().hide();
                    }
                    $('.delete-product').attr('product', response.data.product.id);
                    $('.delete-product').attr('productname', response.data.product.name);


                    /**
                     * Image
                     */
                    $("#product_photo").attr('src', response.data.product.photo);

                    $("img").on("error", function () {
                        $(this).attr("src", "https://cloudfox-files.s3.amazonaws.com/produto.svg");
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

                    if (response.data.product.photo != '') {
                        dropifyOptions.defaultFile = response.data.product.photo;
                    }
                    $('#product_photo').dropify(dropifyOptions);
                    if (response.data.product.digital_product_url != '') {
                        $(".btn-view-product-url").attr('link', response.data.product.digital_product_url);
                        $(".btn-view-product-url").show();
                    }
                    $('.div-expiration-time').show();

                    $('#url_expiration_time').val(response.data.product.url_expiration_time);

                    removeImageButton = false;
                    $("#my-form").submit(function (event) {
                        event.preventDefault();

                        let myForm = document.getElementById('my-form');

                        let formData = new FormData(myForm);

                        if (response.data.product.type_enum == 1) {
                            formData.append('type_enum', 'physical');
                        } else {
                            formData.append('type_enum', 'digital');
                        }

                        if (verify(response.data.product.type_enum)) {
                            loadOnAny('.page', false);
                            $.ajax({
                                method: 'POST',
                                url: "/api/products/" + response.data.product.id + '?product_photo_remove=' + removeImageButton,
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

                                },
                                success: function (response) {
                                    loadOnAny('.page', true);
                                    alertCustom('success', response.message);
                                    $(".btn-view-product-url").attr('link', response.digital_product_url);
                                    window.location = "/products";
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
    function verify(type_enum) {
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

        if (type_enum == 1) {

        }
        if (type_enum == 2) {
            let expiration_time = $.trim($('#url_expiration_time').val());
            if (parseInt(expiration_time) >= 168) {
                alertCustom('error', 'Expiração do link deve ser menor que 1 semana (168 HORAS)');
                ver = false;
            }

            if (expiration_time === '') {

                alertCustom('error', 'Preencha o campo Tempo de expiração da url');
                ver = false;
                $("#url_expiration_time").focus();
            }
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

    $(".converte-product").on('click', function (event) {
        event.preventDefault();

        var product = $('.converte-product').attr('product');
        $("#bt_converter").unbind('click');
        $("#bt_converter").on('click', function () {
            $("#close-modal-converte").click();
            loadingOnScreen();

            $.ajax({
                method: 'POST',
                url: '/api/products/update-product-type/' + product,
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

    $('#product_photo').on('dropify.afterClear', function (event, element) {
        removeImageButton = true;
    });

    /* Produto Fisico */
    alterarCaixinha('#height', 'caixinha-altura');
    alterarCaixinha('#width', 'caixinha-largura');
    alterarCaixinha('#length', 'caixinha-comprimento');
    alterarCaixinha('#weight', 'caixinha-peso');

    function alterarCaixinha(input, newValue) {
        $(input).on('focus', function () { $('#caixinha-img')[0].src = $('#caixinha-img')[0].src.replace('caixinha', newValue) });
        $(input).on('focusout', function () { $('#caixinha-img')[0].src = $('#caixinha-img')[0].src.replace(newValue, 'caixinha') });
    }

    /* Upload Digital Product Input */
    if ($('#digital_product_url')[0] != undefined) {
        $('#digital_product_url')[0].addEventListener("change", function () {
            productName = this.value.split('\\')[2] || '';
            $('#file_return')[0].innerHTML = productName.length > 25
                ? productName.substring(0, 21) + productName.substring(productName.length - 4)
                : productName;
        });
    }
});
