$(function () {
    var statusPlan = {
        0: "danger",
        1: "success",
    }
    var projectId = $(window.location.pathname.split('/')).get(-1);

    var pageCurrent;

    var selected_products = [];
    var stage_add_plan = 1;

    $('.tab_plans').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        index();
        $(this).off();
    });

    /**
     *  Verifica se a array de objetos que retorna do ajax esta vazio
     * @returns {boolean}
     * @param data
     */

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    function clearFields() {
        selected_products = [];
        stage_add_plan = 1;

        var modalID = $('#modal_add_plan');

        var stage_products = modalID.find('.box-breadcrumbs .products');
        var stage_details = modalID.find('.box-breadcrumbs .details');
        var stage_informations = modalID.find('.box-breadcrumbs .informations');

        stage_products.find('img').attr('src', '/modules/global/img/icon-products-plans.svg');
        stage_details.find('img').attr('src', '/modules/global/img/icon-costs-plans.svg');
        stage_informations.find('img').attr('src', '/modules/global/img/icon-info-plans.svg');

        stage_products.removeClass('finalized').addClass('active');
        stage_details.removeClass('finalized');
        stage_informations.removeClass('finalized');
        
        modalID.find('.box-description').html('<p class="font-weight-bold" style="margin-bottom: 21px;">Selecione os produtos do novo plano</p><input class="form-control form-control-lg" type="text" id="search-product" placeholder="Pesquisa por nome">');
        $('.box-review').html('');

        $('#name').val('');
        $('#price').val('');
        $('#description').val('');
    }

    function getIconTypeCustomProduct(proType) {
        var inputType = '';
        switch (proType) {
            case 'Text':
                inputType = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.9 1.8C2.7402 1.8 1.8 2.7402 1.8 3.9V7.5C1.8 7.99705 1.39705 8.4 0.9 8.4C0.402948 8.4 0 7.99705 0 7.5V3.9C0 1.74608 1.74608 0 3.9 0H7.5C7.99705 0 8.4 0.402948 8.4 0.9C8.4 1.39705 7.99705 1.8 7.5 1.8H3.9ZM3.9 22.2C2.7402 22.2 1.8 21.2598 1.8 20.1V16.5C1.8 16.003 1.39705 15.6 0.9 15.6C0.402948 15.6 0 16.003 0 16.5V20.1C0 22.2539 1.74608 24 3.9 24H7.5C7.99705 24 8.4 23.597 8.4 23.1C8.4 22.603 7.99705 22.2 7.5 22.2H3.9ZM22.2 3.9C22.2 2.7402 21.2598 1.8 20.1 1.8H16.5C16.003 1.8 15.6 1.39705 15.6 0.9C15.6 0.402948 16.003 0 16.5 0H20.1C22.2539 0 24 1.74608 24 3.9V7.5C24 7.99705 23.597 8.4 23.1 8.4C22.603 8.4 22.2 7.99705 22.2 7.5V3.9ZM20.1 22.2C21.2598 22.2 22.2 21.2598 22.2 20.1V16.5C22.2 16.003 22.603 15.6 23.1 15.6C23.597 15.6 24 16.003 24 16.5V20.1C24 22.2539 22.2539 24 20.1 24H16.5C16.003 24 15.6 23.597 15.6 23.1C15.6 22.603 16.003 22.2 16.5 22.2H20.1ZM6.9 4.8C6.40295 4.8 6 5.20295 6 5.7V7.2C6 7.69705 6.40295 8.1 6.9 8.1C7.39705 8.1 7.8 7.69705 7.8 7.2V6.6H11.1V17.4H9.3C8.80295 17.4 8.4 17.803 8.4 18.3C8.4 18.797 8.80295 19.2 9.3 19.2H14.7C15.197 19.2 15.6 18.797 15.6 18.3C15.6 17.803 15.197 17.4 14.7 17.4H12.9V6.6H16.2V7.2C16.2 7.69705 16.603 8.1 17.1 8.1C17.597 8.1 18 7.69705 18 7.2V5.7C18 5.20295 17.597 4.8 17.1 4.8H6.9Z" fill="#636363"/>
            </svg>`;
                break;
            case 'Image':
                inputType = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6645 4C18.5055 4 19.9979 5.4924 19.9979 7.33333C19.9979 9.17427 18.5055 10.6667 16.6645 10.6667C14.8236 10.6667 13.3312 9.17427 13.3312 7.33333C13.3312 5.4924 14.8236 4 16.6645 4ZM14.9979 7.33333C14.9979 8.2538 15.7441 9 16.6645 9C17.585 9 18.3312 8.2538 18.3312 7.33333C18.3312 6.41287 17.585 5.66667 16.6645 5.66667C15.7441 5.66667 14.9979 6.41287 14.9979 7.33333ZM0 3.16667C0 1.41777 1.41777 0 3.16667 0H20.8333C22.5823 0 24 1.41777 24 3.16667V20.8333C24 21.6251 23.7094 22.3491 23.229 22.9043C23.1932 22.9687 23.1482 23.0294 23.094 23.0845C23.0365 23.1429 22.9726 23.1911 22.9044 23.2289C22.3492 23.7093 21.6252 24 20.8333 24H3.16667C2.37332 24 1.64812 23.7083 1.09249 23.2262C1.02613 23.1888 0.963833 23.1415 0.90772 23.0845C0.855053 23.0311 0.811093 22.9722 0.77582 22.9099C0.29256 22.3539 0 21.6278 0 20.8333V3.16667ZM22.3333 20.8333V3.16667C22.3333 2.33824 21.6617 1.66667 20.8333 1.66667H3.16667C2.33824 1.66667 1.66667 2.33824 1.66667 3.16667V20.8333C1.66667 20.9377 1.67732 21.0395 1.69759 21.1378L10.2465 12.7234C11.2194 11.7657 12.7807 11.7657 13.7537 12.7233L22.3027 21.1365C22.3228 21.0386 22.3333 20.9372 22.3333 20.8333ZM3.16667 22.3333H20.8333C20.9299 22.3333 21.0243 22.3242 21.1157 22.3068L12.5847 13.9112C12.2603 13.592 11.7399 13.592 11.4156 13.9112L2.8856 22.3071C2.97667 22.3243 3.0706 22.3333 3.16667 22.3333Z" fill="#636363"/>
            </svg>`;
                break;
            case 'File':
                inputType = `<svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.77024 2.7431C12.1117 0.399509 15.9106 0.399509 18.2538 2.74266C20.5369 5.02572 20.5954 8.69093 18.4294 11.0449L18.2413 11.2422L9.44124 20.0404L9.40474 20.0707C7.94346 21.3875 5.68946 21.3427 4.28208 19.9353C2.96306 18.6163 2.84095 16.5536 3.91574 15.0969C3.93908 15.0516 3.96732 15.0078 4.00054 14.9667L4.0541 14.907L4.14101 14.8193L4.28208 14.6714L4.28501 14.6743L11.7207 7.21998C11.9866 6.95336 12.4032 6.9286 12.6971 7.14607L12.7814 7.21857C13.048 7.48449 13.0727 7.90112 12.8553 8.19502L12.7828 8.27923L5.1882 15.8923C4.47056 16.7679 4.52044 18.0622 5.33784 18.8796C6.1669 19.7087 7.48655 19.7481 8.36234 18.998L17.195 10.1676C18.9505 8.40992 18.9505 5.56068 17.1931 3.80332C15.4907 2.10087 12.7635 2.04767 10.9971 3.64371L10.8292 3.80332L10.8166 3.81763L1.28033 13.354C0.987435 13.6468 0.512565 13.6468 0.219665 13.354C-0.0465948 13.0877 -0.0708047 12.671 0.147055 12.3774L0.219665 12.2933L9.76854 2.74266L9.77024 2.7431Z" fill="#636363"/>
            </svg>`;
                break;
        }
        return inputType;
    }

    function enableDisabledCustomProduct(checked, productId) {
        if (checked) {
            $(`.type-${productId}`).removeAttr('disabled');
            $(`#pro-title-${productId}`).removeAttr('disabled');
            $(`#add-list-custom-product-${productId}`).removeAttr('disabled');
            $(`.fc-${productId}`).removeAttr('readonly');
        } else {
            $(`.type-${productId}`).removeClass('btn-active');
            $(`.type-${productId}`).attr('disabled', "disabled");
            $(`#pro-title-${productId}`).attr('disabled', "disabled");
            $(`#add-list-custom-product-${productId}`).attr('disabled', "disabled");
            $(`.fc-${productId}`).attr('readonly', "readonly");
        }
    }

    function create() {
        var modalID = $('#modal_add_plan');
        modalID.attr('data-backdrop', 'static');
        
        $('#search-product').val('');
        $.ajax({
            method: "POST",
            url: "/api/products/userproducts",
            data: { project: projectId },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                modalID.modal('show');
                
                appendProducts(response.data, modalID);
                loadingOnScreenRemove();
            }
        });
    }

    function searchProducts(product) {
        var modalID = $('#modal_add_plan');
        modalID.find('#load-products').html('');
        
        $.ajax({
            method: "POST",
            url: "/api/products/search",
            data: {
                project: projectId,
                product: product
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                $("#modal-content").hide();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                appendProducts(response.data, modalID);
            }
        });
    }

    function appendProducts(products, modalID) {
        var data = '<div class="row">';
        products.forEach(function(product) {
            var index_product = selected_products.map(function(e) { return e.id; }).indexOf(product.id);
            data += '<div class="col-sm-6">';
                data += '<div data-code="' + product.id + '" class="box-product ' + (index_product != -1 ? 'selected' : '') + ' ' + (product.status_enum == 1 ? 'review' : '') + ' d-flex justify-content-between align-items-center">';
                    data += '<div class="d-flex align-items-center">';
                        data += '<img class="product-photo" src="' + product.photo + '" alt="Image Product">';
                        data += '<div>';
                            data += '<h1 class="title">' + product.name + '</h1>';
                            data += '<p class="description">' + product.description + '</p>';
                        data += '</div>';
                    data += '</div>';
                    data += '<div class="check">';
                    if (index_product != -1) {    
                        data += '<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">';
                    }
                    data += '</div>';
                data += '</div>';
            data += '</div>';
        });
        data + '</div>';

        modalID.find('#load-products').html(data);

        $(".product-photo").on("error", function () {
            $(this).attr("src", "https://cloudfox-files.s3.amazonaws.com/produto.svg");
        });
    }
    
    function appendProductsDetails(modalID) {
        modalID.find('.box-products').html('');

        if (selected_products.length > 0) {
            var append = '';
            append += '<div class="box-details">';
                append += '<div class="head d-flex">';
                    append += '<div>Produto</div>';
                    append += '<div>Quantidade<span style="color: #FF0000;">*</span></div>';
                    append += '<div>Custo (un)</div>';
                    append += '<div>Moeda</div>';
                append += '</div>';
                append += '<div class="body">';
                    selected_products.forEach(function(product) {
                        $.ajax({
                            async: false,
                            method: "GET",
                            url: "/api/product/" + product.id,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function error(response) {
                                alertCustom('error', 'Ocorreu um erro, por favor, refaça a operação');
                            },
                            success: function success(response) {
                                append += '<div class="product d-flex" data-code="' + response.data.id + '">';
                                    append += '<div class="div-product d-flex align-items-center">';
                                        append += '<div class="div-photo"><img class="product-photo" src="' + response.data.photo + '" alt="Image Product"></div>';
                                        append += '<h1 class="title">' + response.data.name + '</h1>';
                                    append += '</div>';
                                    append += '<div class="div-amount"><input class="form-control form-control-lg" type="number" min="1" value="1" name="amount" placeholder="Qtd."></div>';
                                    append += '<div class="div-value"><input class="form-control form-control-lg" autocomplete="off" type="text" name="value" placeholder="Valor un."></div>';
                                    append += '<div class="div-currency">';
                                        append += '<select class="form-control form-control-lg" type="text" name="currency_type_enum">';
                                            append += '<option value="BRL">BRL (R$)</option>';
                                            append += '<option value="USD">USD ($)</option>';
                                        append += '</select>';
                                    append += '</div>';
                                append += '</div>';
                            }
                        });
                    });
                append += '</div>';
            append += '</div>';

            modalID.find('.box-products').html(append);

            $('input[name="value"]').mask('#.##0,00', {reverse: true});

            $(".product-photo").on("error", function () {
                $(this).attr("src", "https://cloudfox-files.s3.amazonaws.com/produto.svg");
            });
        } else {
            alertCustom('error', 'Selecione um produto para prosseguir');
            
            modalID.find('.box-description').html('');
            
            var stage_products = modalID.find('.box-breadcrumbs .products');
            var stage_details = modalID.find('.box-breadcrumbs .details');

            stage_products.find('img').attr('src', '/modules/global/img/icon-products-plans.svg');
            stage_details.removeClass('active');
            stage_products.removeClass('finalized');
            stage_products.addClass('active');
            
            modalID.find('.box-description').html('<p style="margin-bottom: 21px; font-weight: bold;">Selecione os produtos do novo plano</p><input class="form-control form-control-lg" type="text" id="search-product" placeholder="Pesquisa por nome">');
            create();
            modalID.find('.box-review').html('');

            stage_add_plan--;
        }
    }

    function appendProductsInformations(modalID) {
        modalID.find('.box-products').html('');
        
        append = '';
        append += '<div class="row">';
            append += '<div class="col-sm-6 form-group">';
                append += '<label for="name">Nome</label>';
                append += '<input type="text" class="form-control form-control-lg" autocomplete="off" id="name" name="name" placeholder="Digite o nome do plano">';
            append += '</div>';

            append += '<div class="col-sm-6 form-group">';
                append += '<label for="price">Preço de venda</label>';
                append += '<input type="text" class="form-control form-control-lg" id="price" autocomplete="off" name="price" placeholder="R$ 99,90">';
            append += '</div>';
        append += '</div>';
        
        append += '<div class="row">';
            append += '<div class="col-sm-12 form-group">';
                append += '<label for="description">Descrição</label>';
                append += '<textarea class="form-control form-control-lg" id="description" autocomplete="off" name="description" placeholder="Adicione uma descrição curta ao seu plano" rows="3"></textarea>';
            append += '</div>';
        append += '</div>';

        modalID.find('.box-products').html(append);

        $('input[name="price"]').mask('#.##0,00', {reverse: true});
    }

    function storePlan(modalID) {
        $.ajax({
            method: 'POST',
            url: '/api/project/' + projectId + '/plans',
            dataType: 'JSON',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content')
            },
            data: {
                'project_id': projectId,
                'products': selected_products,
                'name': modalID.find('input[name="name"]').val(),
                'price': modalID.find('input[name="price"]').val(),
                'description': modalID.find('textarea[name="description"]').val()
            },
            error: function error(response) {
                clearFields();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                var stage_informations = modalID.find('.box-breadcrumbs .informations');
                stage_informations.find('img').attr('src', '/modules/global/img/icon-check.svg');
                stage_informations.removeClass('active');
                stage_informations.addClass('finalized');
                
                index();
                clearFields();
                bindModalKeys();
                alertCustom('success', 'Plano Adicionado!');

                $('#modal_add_plan').modal('hide');
            }
        });
    }

    // Search products
    $('#search-product').on('keyup', function() {
        var search_product = $(this).val();
        if (search_product != '') {
            searchProducts(search_product);
        }
    });

    // Calculate details
    $('.box-products').on('change', '#price', function() {
        var price = parseFloat($(this).val()).toFixed(2);

        var tax = (price * 5.9 / 100).toFixed(2);
        var comission = (price - tax).toFixed(2);
        var return_value = comission;

        $('.price-plan').find('p').html('R$'+price);
        $('.tax-plan').find('p').html('R$'+tax);
        $('.comission-plan').find('p').html('R$'+comission);
        $('.profit-plan').find('p').html('R$'+return_value);
    });
    
    // Select products
    $('.box-products').on('click', '.box-product', function() {
        var product_id = $(this).data('code');

        if (!$(this).hasClass('selected')) {
            $(this).addClass('selected');
            $(this).find('.check').append('<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">');
            selected_products.push({'id': product_id});
        } else {
            $(this).removeClass('selected');
            $(this).find('.check img').remove();
            var index_selected_products = selected_products.map(function(e) { return e.id; }).indexOf(product_id);
            selected_products.splice(index_selected_products, 1);
        }
    });

    // Remove products
    $('.box-products').on('click', '.div-photo', function() {
        var product_id = $(this).parent().parent().data('code');
        var modalID = $('#modal_add_plan');
        var index_selected_products = selected_products.map(function(e) { return e.id; }).indexOf(product_id);

        selected_products.splice(index_selected_products, 1);
        appendProductsDetails(modalID);
    });

    // Button next
    $('#btn-modal-plan-prosseguir').on('click', function() {
        if (selected_products.length > 0) {
            var modalID = $('#modal_add_plan');

            var stage_products = modalID.find('.box-breadcrumbs .products');
            var stage_details = modalID.find('.box-breadcrumbs .details');
            var stage_informations = modalID.find('.box-breadcrumbs .informations');

            if (stage_add_plan == 1) {
                stage_products.find('img').attr('src', '/modules/global/img/icon-check.svg');

                stage_products.removeClass('active');
                stage_products.addClass('finalized');
                stage_details.addClass('active');

                modalID.find('.box-description').html('<p class="font-weight-bold" style="margin-bottom: 4px;">Insira a quantidade e custos de cada produto</p><smalll>As configurações de custo e moeda são utilizadas na emissão de notas fiscais</small>');
                appendProductsDetails(modalID);
                modalID.find('.box-review').html('<div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="check-values"><label class="form-check-label" for="check-values">Todos os produtos têm o mesmo custo</label></div>');
            } else if (stage_add_plan == 2) {
                $('.box-products .form-control').each(function() {
                    var product_ID = $(this).parent().parent().data('code');

                    var product_selected_index = selected_products.map(function(p) { return p.id; }).indexOf(product_ID);
                    
                    var name_input = $(this).attr('name');
                    if (name_input == 'amount') selected_products[product_selected_index].amount = $(this).val();
                    if (name_input == 'value') selected_products[product_selected_index].value = $(this).val();
                    if (name_input == 'currency_type_enum') selected_products[product_selected_index].currency_type_enum = $(this).val();
                });
                
                stage_details.find('img').attr('src', '/modules/global/img/icon-check.svg');
                
                stage_details.removeClass('active');
                stage_details.addClass('finalized');
                stage_informations.addClass('active');

                modalID.find('.box-description').html('<p class="font-weight-bold" style="margin: 0;">Insira os dados do plano</p>');
                appendProductsInformations(modalID);
                modalID.find('.box-review').css('margin-top', '10px');
                modalID.find('.box-review').html(
                    '<div style="margin-right: -30px; margin-left: -30px; border-top: 1px solid #EBEBEB;"></div>' +
                    '<p class="font-weight-bold" style="margin-top: 25px;">Revisão geral</p>' +
                    '<div class="d-flex justify-content-between" style="margin-bottom: 24px;">' +
                        '<div class="price-plan">' +
                            '<small>Preço de venda</small>' +
                            '<p class="font-weight-bold m-0" style="line-height: 100%;">-</p>' +
                        '</div>' +
                        '<div class="costs-plan">' +
                            '<small>Seu custo</small>' +
                            '<p class="font-weight-bold m-0" style="line-height: 100%;">R$0,00</p>' +
                        '</div>' +
                        '<div class="tax-plan">' +
                            '<small>Taxas est.</small>' +
                            '<p class="font-weight-bold m-0" style="line-height: 100%;">-</p>' +
                        '</div>' +
                        '<div class="comission-plan">' +
                            '<small>Comissão aprox.</small>' +
                            '<p class="font-weight-bold m-0" style="line-height: 100%;">-</p>' +
                        '</div>' +
                        '<div class="profit-plan">' +
                            '<small>Lucro aprox.</small>' +
                            '<p class="font-weight-bold m-0" style="line-height: 100%; color: #41DC8F;">-</p>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-center">' +
                        '<p class="m-0" style="line-height: 14px; font-size: 11px;">Simulação considerando compras à vista com taxa de 5,9% (30D).</p>' +
                        '<p class="font-weight-bold m-0" style="line-height: 14px; font-size: 11px;">Valor estimado sujeito à mudanças de acordo com as condições de pagamento.</p>' +
                    '</div>'
                );

                modalID.find('#btn-modal-plan-prosseguir').html('Finalizar');
            } else if (stage_add_plan == 3) {
                storePlan(modalID);
            }

            stage_add_plan++;
        } else {
            alertCustom('error', 'Selecione um produto para prosseguir');
        }
    });

    // Button return
    $('#btn-modal-plan-voltar').on('click', function() {
        var modalID = $('#modal_add_plan');

        modalID.find('#btn-modal-plan-prosseguir').html('Prosseguir');
        
        var stage_products = modalID.find('.box-breadcrumbs .products');
        var stage_details = modalID.find('.box-breadcrumbs .details');
        var stage_informations = modalID.find('.box-breadcrumbs .informations');

        if (stage_add_plan == 2) {
            stage_products.find('img').attr('src', '/modules/global/img/icon-products-plans.svg');

            stage_details.removeClass('active');
            stage_products.removeClass('finalized');
            stage_products.addClass('active');
            
            modalID.find('.box-description').html('<p style="margin-bottom: 21px; font-weight: bold;">Selecione os produtos do novo plano</p><input class="form-control form-control-lg" type="text" id="search-product" placeholder="Pesquisa por nome">');
            create();
            modalID.find('.box-review').html('');
        } else if (stage_add_plan == 3) {
            stage_details.find('img').attr('src', '/modules/global/img/icon-costs-plans.svg');

            stage_informations.removeClass('active');
            stage_details.removeClass('finalized');
            stage_details.addClass('active');

            modalID.find('.box-description').html('<p style="margin-bottom: 4px; font-weight: bold;">Insira a quantidade e custos de cada produto</p><smalll>As configurações de custo e moeda são utilizadas na emissão de notas fiscais</small>');
            appendProductsDetails(modalID);
        }

        stage_add_plan--;
    });

    // Add new Plan
    $("#add-plan").on('click', function () {
        create();
        $('.btn-close-add-plan').on('click', function () {
            clearFields();
            $('#modal_add_plan').removeAttr('data-backdrop');
        });
        bindModalKeys();
    });

    // Copy link plan
    $("#table-plans").on("click", ".copy_link", function () {
        var temp = $("<input>");
        $("#table-plans").append(temp);
        temp.val($(this).attr('link')).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Link copiado!');
    });

    // Details Plan
    $('#table-plans').on('click', '.details-plan', function () {
        var modalID = $('#modal_details_plan');
        
        modalID.attr('data-backdrop', 'static');
        modalID.modal('show');
    });

    // Edit Plan
    $('#table-plans').on('click', '.edit-plan', function () {
        var modalID = $('#modal_edit_plan');
        
        modalID.attr('data-backdrop', 'static');
        modalID.modal('show');
    });

    // Delete Plan
    $('#table-plans').on('click', '.delete-plan', function (event) {
        event.preventDefault();
        var plan = $(this).attr('plan');
        var modalID = $('#modal_delete_plan');

        modalID.modal('show');
        $("#btn-delete-plan").unbind('click');
        $("#btn-delete-plan").on('click', function () {
            modalID.modal('hide');
            $.ajax({
                method: "DELETE",
                url: '/api/project/' + projectId + '/plans/' + plan,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (_error5) {
                    function error(_x5) {
                        return _error5.apply(this, arguments);
                    }

                    error.toString = function () {
                        return _error5.toString();
                    };

                    return error;
                }(function (response) {
                    errorAjaxResponse(response);

                }),
                success: function success(response) {
                    alertCustom('success', response.message);
                    index();
                }

            });
        });
    });

    $("#btn-search-plan").on('click', function () {
        index();
    });

    // Update Table Plan
    function index() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        pageCurrent = link;

        loadOnTable('#data-table-plan', '#table-plans');
        if (link == null) {
            link = '/api/project/' + projectId + '/plans';
        } else {
            link = '/api/project/' + projectId + '/plans' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            data: {
                plan: $("#plan-name").val()
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (_error2) {
                function error() {
                    return _error2.apply(this, arguments);
                }

                error.toString = function () {
                    return _error2.toString();
                };

                return error;
            }(function (response) {
                $("#data-table-plan").html('Erro ao encontrar dados');
                errorAjaxResponse(response);

            }),
            success: function success(response) {
                $('#pagination-plans').html('');
                if (isEmpty(response.data)) {
                    $("#data-table-plan").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                    $('#table-plans').addClass('table-striped');

                } else {
                    $("#data-table-plan").html('');
                    $('#count-plans').html(response.meta.total);

                    if (response.data[0].document_status == 'approved') {
                        $.each(response.data, function (index, value) {
                            data = '';
                            data += '<tr>';
                                data += '<td id="" class="" style="vertical-align: middle;">' + value.name + '</td>';
                                data += '<td id="" class="" style="vertical-align: middle;">' + value.description + '</td>';
                                data += '<td id="link" class="display-sm-none display-m-none copy_link" title="Copiar Link" style="vertical-align: middle;cursor:pointer;" link="' + value.code + '">' + value.code + '</td>';
                                data += '<td id="" class="display-lg-none display-xlg-none" style="vertical-align: middle;"><a class="pointer" onclick="copyToClipboard(\'#link\')"> <span class="material-icons icon-copy-1"> content_copy </span> </a></td>';
                                data += '<td id="" class="" style="vertical-align: middle;">' + value.price + '</td>';
                                data += '<td id="" class=""><span class="badge badge-' + statusPlan[value.status] + '">' + value.status_translated + '</span></td>';
                                data += "<td style='text-align:center' class='mg-responsive'>"
                                    data += "<a title='Visualizar' class='mg-responsive pointer details-plan' lan='" + value.id + "' role='button'><span class='o-eye-1'></span></a>"
                                    data += "<a title='Editar' class='mg-responsive pointer edit-plan' plan='" + value.id + "' role='button'><span class='o-edit-1'></span></a>"
                                    data += "<a title='Excluir' class='mg-responsive pointer delete-plan' plan='" + value.id + "' role='button'><span class='o-bin-1'></span></a>";
                                data += "</td>";
                            data += '</tr>';

                            $("#data-table-plan").append(data);
                            $('#table-plans').addClass('table-striped');
                            $('#currency_type_project').val(value.currency_project);
                        });

                        pagination(response, 'plans', index);
                    } else {
                        $("#data-table-plan").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Link de pagamento só ficará disponível quando seus documentos e da sua empresa estiverem aprovados</td></tr>");
                        $('#table-plans').addClass('table-striped');
                    }

                }                
            }
        });
    }

    function getElementsEdit(element) {
        let custoUnitario = $(element).parent().parent().find('.products_cost_edit')
        let custoTotal = $(element).parent().parent().find('.products_total_edit')
        let moeda = $(element).parent().parent().find('.select_currency_edit')
        let quantidade = $(element).parent().parent().find('.products_amount_edit')

        calculateTotal(custoUnitario, custoTotal, moeda, quantidade);
    }

    function getElementsCreate(element) {
        let custoUnitario = $(element).parent().parent().find('.products_cost_create')
        let custoTotal = $(element).parent().parent().find('.products_total_create')
        let moeda = $(element).parent().parent().find('.select_currency_create')
        let quantidade = $(element).parent().parent().find('.products_amount_create')

        calculateTotal(custoUnitario, custoTotal, moeda, quantidade);
    }

    function clickElementEdit(element) {
        $(element).parent().parent().find('.products_cost_edit').focus();
    }

    function clickElementCreate(element) {
        $(element).parent().parent().find('.products_cost_create').focus();
    }

    function calculateTotal(custoUnitario, custoTotal, moeda, quantidade) {
        let valorCusto = custoUnitario.maskMoney('unmasked')[0];
        let valorQuantidade = $(quantidade).val();
        let valorTotal = valorCusto * valorQuantidade

        setCurrency(custoUnitario, custoTotal, moeda, valorTotal)
    }

    function setCurrency(custoUnitario, custoTotal, moeda, valorTotal) {
        let formatedValue;
        let unitaryValue = custoUnitario.val();

        switch (moeda.val()) {
            case 'USD':
                formatedValue = valorTotal.toLocaleString('pt-BR', {style: 'currency', currency: 'USD'});
                unitaryValue = unitaryValue.toLocaleString('pt-BR', {style: 'currency', currency: 'USD'});
                custoUnitario.maskMoney({prefix: 'US$'});
                break;
            case 'BRL':
                formatedValue = valorTotal.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                unitaryValue = unitaryValue.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                custoUnitario.maskMoney({prefix: 'R$'});
                break;
            default:
                formatedValue = valorTotal.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                unitaryValue = unitaryValue.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                custoUnitario.maskMoney({prefix: 'R$'});
                break;
        }

        custoUnitario.val(unitaryValue)
        custoTotal.val(formatedValue)

    }

    function findElementsEdit(element) {
        let custoUnitario = $(element).find('.products_cost_edit')
        let custoTotal = $(element).find('.products_total_edit')
        let moeda = $(element).find('.select_currency_edit')
        let quantidade = $(element).find('.products_amount_edit')

        calculateTotal(custoUnitario, custoTotal, moeda, quantidade);
    }

    function bindModalKeys() {

        $(document).on('change', '.select_currency_create', function () {
            getElementsCreate(this)
            clickElementCreate(this)
        })
        $(document).on('keyup', '.products_cost_create, .products_total_create', function () {
            getElementsCreate(this)
        })
        $(document).on('change', '.select_currency_edit', function () {
            getElementsEdit(this)
            clickElementEdit(this)
        })

        $(document).on('keyup', '.products_cost_edit, .products_total_edit', function () {
            getElementsEdit(this)
        })

        //o fluxo do product amount deve começar diferente ... algo no html faz com que o seletor o trate um pouco diferente dos outros inputs
        $(document).on('change', '.products_amount_create', function () {
            getElementsCreate($(this).parent())
        })
        $(document).on('change', '.products_amount_edit', function () {
            getElementsEdit($(this).parent())
        })

        //quando um novo registro for inserido na tela e for necessario a edição, esta funçao sera necessaria para vincular o bindModalkeys() aos campos da modal
        $(document).on('click', '.edit-plan', function () {
            bindModalKeys();
        })

        $(document).on('change', '.plan_product_create', function () {
            inputCost = $(this).parent().parent().parent().find('.products_cost');
            inputCost.val($(this).find(':selected').data('cost'));
            getElementsCreate($(this).parent())
        })

        $(document).on('change', '.products_edit', function () {
            inputCost = $(this).parent().parent().parent().find('.products_cost');
            inputCost.val($(this).find(':selected').data('cost'));
            getElementsEdit($(this).parent())
        })

        $('.products_cost_create, .products_cost_edit').maskMoney({thousands: ',', decimal: '.', allowZero: true});
        $('#plan-price_edit, #price').maskMoney({thousands: ',', decimal: '.', allowZero: true, prefix: 'R$'});
        if ($('.products_cost_create, .products_cost_edit').val() == undefined || $('.products_cost_create, .products_cost_edit').val() == null) {
            $('.products_cost_create, .products_cost_edit').maskMoney('mask', 0.00);
        }
    }

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            index();
        }
    });


    $(document).on('click', '#config-cost-plan', function (event) {
        event.preventDefault();

        $('#add_cost_on_plans').select2({
            placeholder: 'Nome do plano',
            multiple: false,
            dropdownParent: $('#modal_config_cost_plan'),
            language: {
                noResults: function () {
                    return 'Nenhum plano encontrado';
                },
                searching: function () {
                    return 'Procurando...';
                },
                loadingMore: function () {
                    return 'Carregando mais planos...';
                },
            },
            ajax: {
                data: function (params) {
                    return {
                        list: 'plan',
                        search: params.term,
                        project_id: projectId,
                        page: params.page || 1
                    };
                },
                method: "GET",
                url: "/api/plans/user-plans",
                delay: 300,
                dataType: 'json',
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                processResults: function (res) {
                    return {
                        results: $.map(res.data, function (obj) {
                            return {id: obj.id, text: obj.name + (obj.description ? ' - ' + obj.description : '')};
                        }),
                        pagination: {
                            'more': res.meta.current_page !== res.meta.last_page
                        }
                    };
                },
            }
        });

        $.ajax({
            method: "GET",
            url: `/api/projects/${projectId}`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error() {
                errorAjaxResponse(response);
            }, success: function success(response) {
                if (response.data.shopify_id == null) {
                    $('#tab_update_cost_block').prop('disabled', true);
                } else {
                    $('#tab_update_cost_block').prop('disabled', false);
                }
                const indexCurrency = (response.data.cost_currency_type == 'BRL') ? 0 : 1;
                $('#cost_currency_type').prop('selectedIndex', indexCurrency);
                $('#update_cost_shopify').prop('selectedIndex', response.data.update_cost_shopify);
                const prefixCurrency = (response.data.cost_currency_type == 'USD') ? 'US$' : 'R$';
                $('#cost_plan').maskMoney({thousands: ',', decimal: '.', allowZero: true, prefix: prefixCurrency});

                $('#modal_config_cost_plan').modal('show');
            },
        });
    });

    $(document).on('click', '.bt-update-cost-block', function (event) {

        $.ajax({
            method: "POST",
            url: '/api/plans/update-bulk-cost',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                plan: $('#add_cost_on_plans').val(),
                cost: $('#cost_plan').val(),
            },
            error: function (_error4) {
                function error(_x4) {
                    return _error4.apply(this, arguments);
                }

                error.toString = function () {
                    return _error4.toString();
                };

                return error;
            }(function (response) {
                errorAjaxResponse(response);
            }),
            success: function success(data) {
                alertCustom("success", "Configuração atualizada com sucesso");
            }
        });

    });

    $(document).on('click', '.bt-update-cost-configs', function (event) {
        $.ajax({
            method: "POST",
            url: '/api/plans/update-config-cost',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project: projectId,
                costCurrency: $('#cost_currency_type').val(),
                updateCostShopify: $('#update_cost_shopify').val(),
                updateAllCurrency: $('#update_all_currency_cost').val(),
            },
            error: function (_error4) {
                function error(_x4) {
                    return _error4.apply(this, arguments);
                }

                error.toString = function () {
                    return _error4.toString();
                };

                return error;
            }(function (response) {
                errorAjaxResponse(response);
            }),
            success: function success(data) {
                let prefixCurrency = ($('#cost_currency_type').val() == 'USD') ? 'US$' : 'R$';
                $('#cost_plan').maskMoney({thousands: ',', decimal: '.', allowZero: true, prefix: prefixCurrency});
                $('#currency_type_project').val(($('#cost_currency_type').val() == 'USD') ? 2 : 1);
                alertCustom("success", "Configuração atualizada com sucesso");
                $("#modal_config_cost_plan").modal('hide');
            }
        });

    });

    $(document).on('change', '#cost_currency_type', function (event) {
        $('#div_update_cost_shopify').show();
    });

    /**
     * Update Plan
     */

    $(document).on('click', '.btn-update-plan', function () {
        console.log('update plan');
        $("#tab_plans-panel").loading({message: '...', start: true});

        var hasNoValue;
        $('.products_amount').each(function () {
            if ($(this).val() == '' || $(this).val() == 0) {
                hasNoValue = true;
            }
        });

        if (hasNoValue) {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        var formData = new FormData(document.getElementById('form-update-plan-tab-1'));
        formData.append("project_id", projectId);
        $.ajax({
            method: "POST",
            // url: "/api/plans/" + plan,
            url: '/api/project/' + projectId + '/plans/' + $('#plan_id').val(),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (_error4) {
                function error(_x4) {
                    return _error4.apply(this, arguments);
                }

                error.toString = function () {
                    return _error4.toString();
                };

                return error;
            }(function (response) {
                $("#tab_plans-panel").loading('stop');
                errorAjaxResponse(response);

                index(pageCurrent);
            }),
            success: function success(data) {
                $("#tab_plans-panel").loading('stop');
                alertCustom("success", "Plano atualizado com sucesso");
                index(pageCurrent);
            }
        });
    });

    /**
     * Update custom Config
     */
    $(document).on('click', '.btn-update-config-custom', function () {
        console.log('update custom config');

        $("#tab_plans-panel").loading({message: '...', start: true});
        var formDataCP = new FormData(document.getElementById('form-update-plan-tab-2'));
        formDataCP.append('plan', $('#plan_id').val());

        $.ajax({
            method: "POST",
            url: '/api/plans/config-custom-product',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formDataCP,
            processData: false,
            contentType: false,
            cache: false,
            error: function (_error4) {
                function error(_x4) {
                    return _error4.apply(this, arguments);
                }

                error.toString = function () {
                    return _error4.toString();
                };

                return error;
            }(function (response) {
                $("#tab_plans-panel").loading('stop');
                errorAjaxResponse(response);
            }),
            success: function success(data) {
                $("#tab_plans-panel").loading('stop');
                alertCustom("success", "Configurações do Plano atualizado com sucesso");
            }
        });

    });

});