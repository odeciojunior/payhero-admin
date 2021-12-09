$(function () {
    var statusPlan = {
        0: "default",
        1: "success",
    }
    var projectId = $(window.location.pathname.split('/')).get(-1);

    var pageCurrent;

    var selected_products = [];
    var products_plan = [];
    var plan_id = '';
    var gateway_tax = 0;
    var allow_change_in_block = false;

    var selected_plans = [];

    $('.tab_plans').on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        index();
        $(this).off();

        $.ajax({
            async: false,
            method: "GET",
            url: "/api/projects/" + projectId + "/companie",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                console.log(response);
            },
            success: function success(response) {
                gateway_tax = response.data.gateway_tax;
            }
        });
    });

    /**
     *  Verifica se a array de objetos que retorna do ajax esta vazio
     * @returns {boolean}
     * @param data
     */

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    function getIconTypeCustomProduct(custom_type) {
        var input_type = '';
        switch (custom_type) {
            case 'Text':
                input_type = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.9 1.8C2.7402 1.8 1.8 2.7402 1.8 3.9V7.5C1.8 7.99705 1.39705 8.4 0.9 8.4C0.402948 8.4 0 7.99705 0 7.5V3.9C0 1.74608 1.74608 0 3.9 0H7.5C7.99705 0 8.4 0.402948 8.4 0.9C8.4 1.39705 7.99705 1.8 7.5 1.8H3.9ZM3.9 22.2C2.7402 22.2 1.8 21.2598 1.8 20.1V16.5C1.8 16.003 1.39705 15.6 0.9 15.6C0.402948 15.6 0 16.003 0 16.5V20.1C0 22.2539 1.74608 24 3.9 24H7.5C7.99705 24 8.4 23.597 8.4 23.1C8.4 22.603 7.99705 22.2 7.5 22.2H3.9ZM22.2 3.9C22.2 2.7402 21.2598 1.8 20.1 1.8H16.5C16.003 1.8 15.6 1.39705 15.6 0.9C15.6 0.402948 16.003 0 16.5 0H20.1C22.2539 0 24 1.74608 24 3.9V7.5C24 7.99705 23.597 8.4 23.1 8.4C22.603 8.4 22.2 7.99705 22.2 7.5V3.9ZM20.1 22.2C21.2598 22.2 22.2 21.2598 22.2 20.1V16.5C22.2 16.003 22.603 15.6 23.1 15.6C23.597 15.6 24 16.003 24 16.5V20.1C24 22.2539 22.2539 24 20.1 24H16.5C16.003 24 15.6 23.597 15.6 23.1C15.6 22.603 16.003 22.2 16.5 22.2H20.1ZM6.9 4.8C6.40295 4.8 6 5.20295 6 5.7V7.2C6 7.69705 6.40295 8.1 6.9 8.1C7.39705 8.1 7.8 7.69705 7.8 7.2V6.6H11.1V17.4H9.3C8.80295 17.4 8.4 17.803 8.4 18.3C8.4 18.797 8.80295 19.2 9.3 19.2H14.7C15.197 19.2 15.6 18.797 15.6 18.3C15.6 17.803 15.197 17.4 14.7 17.4H12.9V6.6H16.2V7.2C16.2 7.69705 16.603 8.1 17.1 8.1C17.597 8.1 18 7.69705 18 7.2V5.7C18 5.20295 17.597 4.8 17.1 4.8H6.9Z" fill="#636363"/>
                </svg>`;
                break;
            case 'Image':
                input_type = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.6645 4C18.5055 4 19.9979 5.4924 19.9979 7.33333C19.9979 9.17427 18.5055 10.6667 16.6645 10.6667C14.8236 10.6667 13.3312 9.17427 13.3312 7.33333C13.3312 5.4924 14.8236 4 16.6645 4ZM14.9979 7.33333C14.9979 8.2538 15.7441 9 16.6645 9C17.585 9 18.3312 8.2538 18.3312 7.33333C18.3312 6.41287 17.585 5.66667 16.6645 5.66667C15.7441 5.66667 14.9979 6.41287 14.9979 7.33333ZM0 3.16667C0 1.41777 1.41777 0 3.16667 0H20.8333C22.5823 0 24 1.41777 24 3.16667V20.8333C24 21.6251 23.7094 22.3491 23.229 22.9043C23.1932 22.9687 23.1482 23.0294 23.094 23.0845C23.0365 23.1429 22.9726 23.1911 22.9044 23.2289C22.3492 23.7093 21.6252 24 20.8333 24H3.16667C2.37332 24 1.64812 23.7083 1.09249 23.2262C1.02613 23.1888 0.963833 23.1415 0.90772 23.0845C0.855053 23.0311 0.811093 22.9722 0.77582 22.9099C0.29256 22.3539 0 21.6278 0 20.8333V3.16667ZM22.3333 20.8333V3.16667C22.3333 2.33824 21.6617 1.66667 20.8333 1.66667H3.16667C2.33824 1.66667 1.66667 2.33824 1.66667 3.16667V20.8333C1.66667 20.9377 1.67732 21.0395 1.69759 21.1378L10.2465 12.7234C11.2194 11.7657 12.7807 11.7657 13.7537 12.7233L22.3027 21.1365C22.3228 21.0386 22.3333 20.9372 22.3333 20.8333ZM3.16667 22.3333H20.8333C20.9299 22.3333 21.0243 22.3242 21.1157 22.3068L12.5847 13.9112C12.2603 13.592 11.7399 13.592 11.4156 13.9112L2.8856 22.3071C2.97667 22.3243 3.0706 22.3333 3.16667 22.3333Z" fill="#636363"/>
                </svg>`;
                break;
            case 'File':
                input_type = `<svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.77024 2.7431C12.1117 0.399509 15.9106 0.399509 18.2538 2.74266C20.5369 5.02572 20.5954 8.69093 18.4294 11.0449L18.2413 11.2422L9.44124 20.0404L9.40474 20.0707C7.94346 21.3875 5.68946 21.3427 4.28208 19.9353C2.96306 18.6163 2.84095 16.5536 3.91574 15.0969C3.93908 15.0516 3.96732 15.0078 4.00054 14.9667L4.0541 14.907L4.14101 14.8193L4.28208 14.6714L4.28501 14.6743L11.7207 7.21998C11.9866 6.95336 12.4032 6.9286 12.6971 7.14607L12.7814 7.21857C13.048 7.48449 13.0727 7.90112 12.8553 8.19502L12.7828 8.27923L5.1882 15.8923C4.47056 16.7679 4.52044 18.0622 5.33784 18.8796C6.1669 19.7087 7.48655 19.7481 8.36234 18.998L17.195 10.1676C18.9505 8.40992 18.9505 5.56068 17.1931 3.80332C15.4907 2.10087 12.7635 2.04767 10.9971 3.64371L10.8292 3.80332L10.8166 3.81763L1.28033 13.354C0.987435 13.6468 0.512565 13.6468 0.219665 13.354C-0.0465948 13.0877 -0.0708047 12.671 0.147055 12.3774L0.219665 12.2933L9.76854 2.74266L9.77024 2.7431Z" fill="#636363"/>
                </svg>`;
                break;
        }
        return input_type;
    }

    function enableDisabledCustomProduct(checked) {
        if (checked) {
            $('.active_custom').attr('checked', true).val(true);

            $('.btn-type').removeAttr('disabled');
            $('#custom-title').removeAttr('disabled');
            $('#add-list-custom-product').removeAttr('disabled');
            $('.added-custom-title').removeAttr('readonly');
        } else {
            $('.active_custom').removeAttr('checked').val(false);

            $('.btn-type').removeClass('btn-active');
            $('.btn-type').attr('disabled', 'disabled');
            $('#custom-title').attr('disabled', 'disabled');
            $('#add-list-custom-product').attr('disabled', 'disabled');
            $('.added-custom-title').attr('readonly', 'readonly');
        }
    }

    function searchProducts(product, modal, type) {
        var find_stage = type == 'create' ? '#stage1' : '#stage2';

        $(modal).find('.modal-body').css('height', 'auto');
        $(modal).find(find_stage).find('.box-review').html('').css('margin-bottom', '0px');

        $(modal).find(find_stage).find('.box-products').html('').css({'max-height': '276px', 'padding-right': '0px'});
        $(modal).find(find_stage).find('.box-products').find('.scrollbox').remove();

        $(modal).find(find_stage).find('.box-products').html(loadingProducts).promise().done(function() {
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
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    var append = '<div class="row" style="display: none;">';
                    if (response.data.length > 0) {
                        response.data.forEach(function(product) {
                            var index_product = selected_products.map(function(e) { return e.id; }).indexOf(product.id);
                            append += '<div class="col-sm-6">';
                                append += '<div ' + (product.name_short_flag ? 'data-toggle="tooltip" data-placement="top" title="' + product.name + '"' : '') + ' data-code="' + product.id + '" class="box-product ' + (index_product != -1 ? 'selected' : '') + ' ' + (product.status_enum == 1 ? 'review' : '') + ' d-flex justify-content-between align-items-center">';
                                    append += '<div class="d-flex align-items-center">';
                                        append += '<div class="background-photo">';
                                            append += '<img class="product-photo" src="' + product.photo + '">';
                                        append += '</div>';
                                        append += '<div>';
                                            append += '<h1 class="title">' + product.name_short + '</h1>';
                                            append += '<p class="description">' + product.description + '</p>';
                                        append += '</div>';
                                    append += '</div>';
                                    append += '<div class="check">';
                                        if (index_product != -1) {
                                            append += '<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">';
                                        }
                                    append += '</div>';
                                append += '</div>';
                            append += '</div>';
                        });
                    } else {
                        append += '<div class="col-sm-12">';
                            append += '<div class="text-center" style="height: 150px; margin-bottom: 25px; margin-top: 15px;"><img style="margin: 0 auto;" class="product-photo" src="/modules/global/img/search-product_not-found.svg" ></div>';
                            append += '<p class="m-0 text-center" style="font-size: 24px; line-height: 30px; color: #636363;">Nenhuma resultado encontrado.</p>';
                            append += '<p class="m-0 text-center" style="font-size: 16px; line-height: 20px; color: #9A9A9A;">Por aqui, nenhum produto com esse nome.</p>';
                        append += '</div>';
                    }
                    append + '</div>';

                    var curHeight = $(modal).find('.modal-body').height();
                    $(modal).find(find_stage).find('.box-products').append(append).promise().done(function() {
                        $('[data-toggle="tooltip"]').tooltip({
                            container: '.page',
                            template: '<div class="tooltip product-select" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        });

                        if (response.data.length > 6) {
                            scrollCustom(modal + ' ' + find_stage + ' .box-products');
                        } else {
                            $(modal + ' ' + find_stage + ' .box-products').off('wheel');
                        }

                        $(modal).find('.product-photo').on('error', function() {
                            $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg')
                        });

                        $(modal).find('.product-photo').on('load', function() {
                            $(modal).find('.ph-item').fadeOut(100, function(){ this.remove(); }).promise().done(function() {
                                $(modal).find(find_stage).find('.box-products').find('.row').css('display', 'flex').promise().done(function() {
                                    var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + 40;
                                    $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                                });
                            });
                        });
                    });
                }
            });
        });
    }

    function getProducts(modal, type) {
        $(modal).find('.modal-body').css('height', 'auto');

        var find_stage = type == 'create' ? '#stage1' : '#stage2';

        if (type == 'edit') {
            $(modal).find('.nav-tabs-horizontal').css('display', 'none');

            $(modal).find('.modal-footer').html(
                '<button id="btn-modal-plan-return" type="button" data-type="edit" class="btn btn-default btn-lg" role="button">Voltar</button>' +
                '<button id="btn-modal-plan-next" type="button" data-type="edit" class="btn btn-primary btn-lg">Continuar</button>'
            ).removeClass('justify-content-between');

            selected_products = [];
            products_plan.map(function(e) {
                console.log(e);
                selected_products.push({ id: e.product_id });
            });
        } else {
            $(modal).find('#btn-modal-plan-return').html('Voltar');
        }

        $(modal).find('.modal-body').css('height', 'auto');
        $(modal).find('.tab-pane').removeClass('show active');
        $(modal).find(find_stage).find('.box-products').html('').css({'max-height': '276px', 'padding-right': '0px'});
        $(modal).find(find_stage).find('#search-product').val('');
        $(modal).find(find_stage).find('.box-review').html('');

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            if (type == 'create') {
                $(modal).find('.modal-body').append(loadingCreateStage1);
            } else {
                $(modal).find('.modal-body').append(loadingEditStage2);
            }

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
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    var append = '<div class="row">';
                    if (response.data.length > 0) {
                        response.data.forEach(function(product) {
                            var index_product = selected_products.map(function(e) { return e.id; }).indexOf(product.id);
                            append += '<div class="col-sm-6">';
                                append += '<div ' + (product.name_short_flag ? 'data-toggle="tooltip" data-placement="top" title="' + product.name + '"' : '') + ' data-code="' + product.id + '" class="box-product ' + (index_product != -1 ? 'selected' : '') + ' ' + (product.status_enum == 1 ? 'review' : '') + ' d-flex justify-content-between align-items-center">';
                                    append += '<div class="d-flex align-items-center">';
                                        append += '<div class="background-photo">';
                                            append += '<img class="product-photo" src="' + product.photo + '">';
                                        append += '</div>';
                                        append += '<div>';
                                            append += '<h1 class="title">' + product.name_short + '</h1>';
                                            append += '<p class="description">' + product.description + '</p>';
                                        append += '</div>';
                                    append += '</div>';
                                    append += '<div class="check">';
                                        if (index_product != -1) {
                                            append += '<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">';
                                        }
                                    append += '</div>';
                                append += '</div>';
                            append += '</div>';
                        });
                    } else {
                        append += '<div class="col-sm-12">';
                            append += '<div class="text-center" style="height: 150px; margin-bottom: 25px; margin-top: 15px;"><img style="margin: 0 auto;" class="product-photo" src="/modules/global/img/search-product_not-found.svg" ></div>';
                            append += '<p class="m-0 text-center" style="font-size: 24px; line-height: 30px; color: #636363;">Nenhuma resultado encontrado.</p>';
                            append += '<p class="m-0 text-center" style="font-size: 16px; line-height: 20px; color: #9A9A9A;">Por aqui, nenhum produto com esse nome.</p>';
                        append += '</div>';
                    }
                    append + '</div>';

                    var curHeight = $(modal).find('.modal-body').height();
                    $(modal).find(find_stage).find('.box-products').html(append).promise().done(function() {
                        $('[data-toggle="tooltip"]').tooltip({
                            container: '.page',
                            template: '<div class="tooltip product-select" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        });

                        if (response.data.length > 6) {
                            scrollCustom(modal + ' ' + find_stage + ' .box-products');
                        } else {
                            $(modal + ' ' + find_stage + ' .box-products').off('wheel');
                        }

                        if (type == 'edit') {
                            $(modal).find('.box-breadcrumbs').find('.title span').html(' ' + products_plan.length + (products_plan.length > 1 ? ' produtos' : ' produto'));

                            var appendProductsPlan = '<div class="d-flex">';
                            products_plan.forEach(function(product) {
                                appendProductsPlan += '<div class="background-photo" data-toggle="tooltip" data-placement="top"  title="' + product.product_name + '">';
                                    appendProductsPlan += '<img class="product-photo" src="' + product.photo + '">';
                                appendProductsPlan += '</div>';
                            });
                            appendProductsPlan += '</div>';

                            $(modal).find('.box-photos-products').html(appendProductsPlan).promise().done(function() {
                                $('[data-toggle="tooltip"]').tooltip({
                                    container: '.page',
                                    template: '<div class="tooltip product-details" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                                });
                            });
                        }

                        $(modal).find('.product-photo').on('error', function() {
                            $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg');
                        });

                        $(modal).find('.product-photo').on('load', function() {
                            $(modal).find('.ph-item').fadeOut(100, function(){ this.remove(); }).promise().done(function() {
                                $(modal).find('#tab-general-data_panel').addClass('show active').promise().done(function() {
                                    $(modal).find(find_stage).addClass('show active').promise().done(function() {
                                        var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + 60;
                                        $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                                    });
                                });
                            });
                        });
                    });
                }
            });
        });
    }

    function getDetailsProducts(modal, type) {
        var find_stage = type == 'create' ? '#stage2' : '#stage3';

        $(modal).find('.modal-body').css('height', 'auto');
        $(modal).find(find_stage).find('.box-products').html('').css({'max-height': 'unset', 'padding-right': '0px'});
        $(modal).find(find_stage).find('.box-review').html('');
        $(modal).find('#btn-modal-plan-return').html('Voltar');
        if (type == 'create') {
            $(modal).find('#btn-modal-plan-next').html('Continuar');
        } else {
            $(modal).find('#btn-modal-plan-next').html('Finalizar');
        }

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            if (type == 'create') {
                $(modal).find('.modal-body').append(loadingCreateStage2);
            } else {
                $(modal).find('.modal-body').append(loadingEditStage3);
            }

            var append = '';
            append += '<div class="box-details">';
                append += '<div class="head d-flex">';
                    append += '<div>Produto</div>';
                    append += '<div>Quantidade<span style="color: #FF0000;">*</span></div>';
                    append += '<div>Custo (un)</div>';
                    append += '<div>Moeda</div>';
                append += '</div>';
                append += '<div class="body">';
                    append += '<div class="row">';
                        append += '<div class="col-sm-12">';
                            selected_products.forEach(function(product) {
                                var index_product = selected_products.map(function(p) { return p.id; }).indexOf(product.id);

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
                                        append += '<div class="product d-flex align-items-center" data-code="' + response.data.id + '">';
                                            append += '<div class="div-product d-flex align-items-center" ' + (response.data.name_short_flag ? 'data-toggle="tooltip" data-placement="top" title="' + response.data.name + '"' : '') + '>';
                                                append += '<div class="div-photo" data-type="' + type + '"><img class="product-photo" src="' + response.data.photo + '"></div>';
                                                append += '<h1 class="title">' + response.data.name_short + '</h1>';
                                            append += '</div>';
                                            append += '<div class="div-amount">';
                                                append += '<div class="d-flex align-items-center  justify-content-center ">';
                                                    append += '<div class="input-number">';
                                                        append += '<button class="btn-sub">';
                                                            append += '<img src="/modules/global/img/minus.svg">';
                                                        append += '</button>';
                                                        append += '<input type="number" class="form-control" name="amount" value="' + (selected_products[index_product].amount ?? 1) + '" min="1" max="99" step="1">';
                                                        append += '<button class="btn-add">';
                                                            append += '<img src="/modules/global/img/plus.svg">';
                                                        append += '</button>';
                                                    append += '</div>';
                                                append += '</div>';
                                            append += '</div>';
                                            append += '<div class="div-value"><input class="form-control form-control-lg" autocomplete="off" value="' + (selected_products[index_product].value ?? '') + '" type="text" name="value" placeholder="Valor un."></div>';
                                            append += '<div class="div-currency">';
                                                append += '<select class="form-control form-control-lg" type="text" name="currency_type_enum">';
                                                    append += '<option value="BRL" ' + (selected_products[index_product].currency_type_enum == 'BRL' ? 'selected' : '') + '>BRL (R$)</option>';
                                                    append += '<option value="USD" ' + (selected_products[index_product].currency_type_enum == 'USD' ? 'selected' : '') + '>USD ($)</option>';
                                                append += '</select>';
                                            append += '</div>';
                                        append += '</div>';
                                    }
                                });
                            });
                        append += '</div>';
                    append += '</div>';
                append += '</div>';
            append += '</div>';

            var curHeight = $(modal).find('.modal-body').height();
            $(modal).find(find_stage).find('.box-products').html(append).promise().done(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    container: '.page',
                    template: '<div class="tooltip product-details" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                });

                $(modal).find(find_stage).find(".product-photo").on("error", function () {
                    $(this).attr("src", "https://cloudfox-files.s3.amazonaws.com/produto.svg");
                });

                $('input[name="value"]').mask('#.##0,00', { reverse: true });

                if (selected_products.length > 4) {
                    $(modal).find(find_stage).find('.box-products').find('.body').css({'max-height': '238px', 'position': 'relative', 'overflow': 'hidden'});

                    scrollCustom(modal + ' ' + find_stage + ' .box-products .body');
                } else {
                    $(modal + ' ' + find_stage + ' .box-products .body').off('wheel');
                }

                if (selected_products.length > 1) {
                    $(modal).find(find_stage).find('.box-review').html(
                        `<div class="switch-holder d-flex">
                            <label class="switch">
                                <input type="checkbox" id="check-values" name="check-values" class="check" value="0">
                                <span class="slider round"></span>
                            </label>
                            <label for="product_amount_selector" style="margin: 0;">Todos os produtos têm o mesmo custo</label>
                        </div>`
                    );
                } else {
                    $(modal).find(find_stage).find('.box-review').html('');
                }

                if (type == 'edit') {
                    $(modal).find('.modal-footer').find('#btn-modal-plan-next').html('Finalizar');
                }

                $(modal).find('.product-photo').on('load', function() {
                    $(modal).find('.ph-item').fadeOut(100, function(){ this.remove(); }).promise().done(function() {
                        $(modal).find('#tab-general-data_panel').addClass('show active').promise().done(function() {
                            $(modal).find(find_stage).addClass('show active').promise().done( function() {
                                var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + 60;
                                $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                            });
                        });
                    });
                });
            });
        });
    }

    function getPlanInformations(modal) {
        $(modal).find('.modal-body').css('height', 'auto');
        $(modal).find('#btn-modal-plan-next').html('Finalizar');

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            $(modal).find('.modal-body').append(loadingCreateStage3);

            $('.box-products .form-control').each(function() {
                var product_ID = $(this).parents('.product').attr('data-code');

                var product_selected_index = selected_products.map(function(p) { return p.id; }).indexOf(product_ID);

                var name_input = $(this).attr('name');
                if (name_input == 'amount') selected_products[product_selected_index].amount = $(this).val();
                if (name_input == 'value') selected_products[product_selected_index].value = $(this).val();
                if (name_input == 'currency_type_enum') selected_products[product_selected_index].currency_type_enum = $(this).val();
            });

            setTimeout(function() {
                var curHeight = $(modal).find('.modal-body').height();
                $(modal).find('.ph-item').fadeOut(100, function(){ this.remove(); }).promise().done(function() {
                    $('input[name="price"]').mask('#.##0,00', {reverse: true});

                    $(modal).find('.costs-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(calculateCostsPlan()));
                    $(modal).find('.box-review').find('.tax').html(gateway_tax);

                    $(modal).find('#stage3').addClass('show active').promise().done( function() {
                        setTimeout(function() {
                            var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + 60;
                            $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                        }, 0);
                    });
                });
            }, 450);
        });
    }

    function getPlanData(modal, flag = false) {
        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('#tab-customizations').removeClass('show active').addClass('disabled');
        $(modal).find('#tab-general-data').addClass('show active');

        $(modal).find('.nav-tabs-horizontal').css('display', 'block');
        $(modal).find('#tab-general-data_panel').removeClass('show active');

        $(modal).find('.modal-title').html('Detalhes');
        $(modal).find('.modal-footer').html(
            '<button plan="' + plan_id + '" id="btn-modal-plan-delete" type="button" class="btn btn-default btn-lg px-0" style="box-shadow: none !important; color: #838383; align-items: center !important; display: flex; padding: 10px 32px; background: transparent; border: none;" role="button">' +
                '<img class="mr-10" src="/modules/global/img/icon-trash.svg" alt="Icon Trash" />' +
                '<span>Excluir plano</span>' +
            '</button>' +
            '<button id="btn-modal-plan-close" type="button" data-dismiss="modal" class="btn btn-primary btn-lg">Fechar</button>'
        ).removeClass('justify-content-end').addClass('justify-content-between');
        $(modal).find('#stage1').find('.products-edit').find('a').remove();

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            $(modal).find('.modal-body').append(loadingEditStage1);

            $.ajax({
                method: "GET",
                url: '/api/project/' + projectId + '/plans/' + plan_id,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    products_plan = response.data.products;

                    selected_products = [];
                    products_plan.map(function(e) {
                        selected_products.push({
                            id: e.product_id,
                            value: e.product_cost.replace('R$ ', '').replace(',', '').replace('.', ','),
                            amount: e.amount
                        });
                    });

                    var price = parseFloat(response.data.price.replace('R$', '')).toFixed(2);

                    var tax = (price * gateway_tax / 100).toFixed(2);
                    var costs = calculateCostsPlan();
                    var comission = (price - tax).toFixed(2);
                    var return_value = (comission - costs).toFixed(2);

                    var curHeight = $(modal).find('.modal-body').height();

                    var products = response.data.products;

                    var append = '<div class="row">';
                    products.forEach(function(product) {
                        append += '<div class="col-sm-6">';
                            append += '<div ' + (product.product_name_short_flag ? 'data-toggle="tooltip" data-placement="top" title="' + product.product_name + '"' : '') + ' class="box-product d-flex justify-content-between align-items-center" style="cursor: inherit;">';
                                append += '<div class="d-flex align-items-center">';
                                    append += '<div class="background-photo"><img class="product-photo" src="' + product.photo + '"></div>';
                                    append += '<div>';
                                        append += '<h1 class="title">' + product.product_name_short + '</h1>';
                                        append += '<p class="description">Qtd: ' + product.amount + '</p>';
                                    append += '</div>';
                                append += '</div>';
                            append += '</div>';
                        append += '</div>';
                    });

                    $(modal).find('#stage1').find('.box-products').html(append).promise().done(function() {
                        $(modal).find('#tab-customizations').removeClass('disabled');

                        $(modal).find('.modal-title').html('Detalhes de ' + response.data.name_short);
                        $(modal).find('.modal-title').attr('data-title', 'Detalhes de ' + response.data.name);

                        $(modal).find('#btn-edit-informations-plan').attr('data-code', response.data.id);

                        $(modal).find('#name').val(response.data.name_short);
                        $(modal).find('#name').attr('data-short', response.data.name_short).attr('data', response.data.name);

                        $(modal).find('#price').val(response.data.price);
                        $(modal).find('#price').attr('data', response.data.price);

                        $(modal).find('#description').val(response.data.description_short);
                        $(modal).find('#description').attr('data-short', response.data.description_short).attr('data', response.data.description);

                        if (products.length > 2) {
                            $(modal).find('#stage1').find('.products-edit').append('<a type="button" id="all-products" data-open="0">Ver todos os produtos <span class="fas fa-chevron-down"></span></a>');
                        } else {
                            $(modal).find('#stage1').find('.products-edit').find('a').remove();
                        }

                        $(modal).find('#stage1').find('.price-plan p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(price));
                        $(modal).find('#stage1').find('.costs-plan p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(costs));
                        $(modal).find('#stage1').find('.tax-plan p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(tax));
                        $(modal).find('#stage1').find('.comission-plan p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(comission));
                        $(modal).find('#stage1').find('.profit-plan p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(return_value));

                        $(modal).find('#stage1').find('.description-tax p span').html(gateway_tax);

                        $(modal).find('#stage1').find('.products-edit').find('.title').find('span').html(' ' + response.data.products.length + (response.data.products.length > 1 ? ' produtos' : ' produto'));

                        $(modal).find('#stage1').find(".product-photo").on("error", function () {
                            $(this).attr("src", "https://cloudfox-files.s3.amazonaws.com/produto.svg");
                        });

                        if (flag == true) {
                            getCustom(modal);
                        } else {
                            $(modal).find('.product-photo').on('load', function() {
                                $(modal).find('.ph-item').fadeOut(100, function() { this.remove(); }).promise().done(function() {
                                    $('[data-toggle="tooltip"]').tooltip({
                                        container: '.page',
                                        template: '<div class="tooltip product-select" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                                    });

                                    $(modal).find('#tab-general-data_panel').addClass('show active').promise().done(function() {
                                        $(modal).find('#stage1').addClass('show active').promise().done(function() {
                                            var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + (products.length > 2 ? 65 : 55);
                                            $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                                        });
                                    });
                                });
                            });
                        }
                    });
                }
            });
        });
    }

    function getCustom(modal) {
        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('.modal-footer').html(
            '<button id="btn-modal-plan-delete" type="button" class="btn btn-default btn-lg px-0" style="box-shadow: none !important; color: #838383; align-items: center !important; display: flex; padding: 10px 32px; background: transparent; border: none;" role="button">' +
                '<img class="mr-10" src="/modules/global/img/icon-trash.svg" alt="Icon Trash" />' +
                '<span>Excluir plano</span>' +
            '</button>' +
            '<button id="btn-modal-plan-close" type="button" data-dismiss="modal" class="btn btn-primary btn-lg">Fechar</button>'
        ).removeClass('justify-content-end').addClass('justify-content-between');

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            $(modal).find('.modal-body').append(loadingCustomStage1);

            var append = '';
            append += '<div class="row header">';
                append += '<div class="col-sm-6">';
                    append += '<h1 class="title">Produtos no plano</h1>';
                append += '</div>';
                append += '<div class="col-sm-6">';
                    append += '<h1 class="title">Tipo de customização</h1>';
                append += '</div>';
            append += '</div>';

            products_plan.forEach(function(product) {
                append += '<div class="row box-product body align-items-center" style="cursor: inherit;">';
                    append += '<div class="col-sm-6" ' + (product.product_name_short_flag ? 'data-toggle="tooltip" data-placement="top" title="' + product.product_name + '"' : '') + '>';
                        append += '<div class="product d-flex align-items-center">';
                            append += '<div class="background-photo">';
                                append += '<img class="product-photo" src="' + product.photo + '">';
                            append += '</div>';
                            append += '<div>';
                                append += '<h1 class="title">' + product.product_name_short + '</h1>';
                                append += '<p class="description m-0">Qtd: ' + product.amount + '</p>';
                            append += '</div>';
                        append += '</div>';
                    append += '</div>';
                    append += '<div class="col-sm-6">';
                        append += '<div class="d-flex ' + (product.is_custom ? 'justify-content-between' : 'justify-content-end') + ' align-items-center">';
                            if (product.is_custom) {
                                append += '<div class="d-flex">';
                                    if (product.custom_configs.map(function(e) { return e.type }).indexOf('Text') != -1) {
                                        append += '<div class="d-flex align-items-center">';
                                            append += '<div class="custom-type">';
                                                append += '<img src="/modules/global/img/icon-custom-product-text.svg">';
                                            append += '</div>';
                                        append += '</div>';
                                    }

                                    if (product.custom_configs.map(function(e) { return e.type }).indexOf('Image') != -1) {
                                        append += '<div class="d-flex align-items-center">';
                                            append += '<div class="custom-type">';
                                                append += '<img src="/modules/global/img/icon-custom-product-image.svg">';
                                            append += '</div>';
                                        append += '</div>';
                                    }

                                    if (product.custom_configs.map(function(e) { return e.type }).indexOf('File') != -1) {
                                        append += '<div class="d-flex align-items-center">';
                                            append += '<div class="custom-type">';
                                                append += '<img src="/modules/global/img/icon-custom-product-file.svg">';
                                            append += '</div>';
                                        append += '</div>';
                                    }
                                append += '</div>';
                            }

                            append += '<div class="d-flex align-items-center">';
                                append += '<a class="btn-customizations" data-product="' + product.id + '" type="button" style="cursor: pointer; ' + (product.is_custom ? 'margin-right: 14px;' : '') + '">' + (product.is_custom ? 'Editar' : 'Adicionar') + '</a>';
                                if (product.is_custom) {
                                    append += '<div class="switch-holder d-flex align-items-center">';
                                        append += '<label class="switch m-0">';
                                            append += '<input type="checkbox" id="update_cost_shopify" name="check-values" class="check" value="' + (product.is_custom ? 1 : 0) + '" checked>';
                                            append += '<span class="slider round"></span>';
                                        append += '</label>';
                                    append += '</div>';
                                }
                            append += '</div>';
                        append += '</div>';
                    append += '</div>';
                append += '</div>';
            });

            append += '</div>';

            $(modal).find('#tab-general-data').removeClass('show active');
            $(modal).find('#tab-customizations').addClass('show active');

            var curHeight = $(modal).find('.modal-body').height();
            $(modal).find('#stage1-customization').find('.box-products').html(append).promise().done(function() {
                $(modal).find('.product-photo').on('load', function() {
                    $(modal).find('.ph-item').fadeOut(100, function() { this.remove(); }).promise().done(function() {
                        $('[data-toggle="tooltip"]').tooltip({
                            container: '.page',
                            template: '<div class="tooltip product-select" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        });

                        $(modal).find('#tab-customizations_panel').addClass('show active').promise().done(function() {
                            $(modal).find('#stage1-customization').addClass('show active').promise().done(function() {
                                var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + (products_plan.length > 2 ? 65 : 55);
                                $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                            });
                        });
                    });
                });
            });
        });
    }

    function getProductCustom(modal, product_ID) {
        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('.modal-footer').html(
            '<button id="btn-modal-plan-return" type="button" data-type="custom" class="btn btn-default btn-lg" role="button">Voltar</button>' +
            '<button id="btn-modal-plan-next" type="button" data-type="custom" class="btn btn-primary btn-lg">Salvar e fechar</button>'
        ).removeClass('justify-content-between').addClass('justify-content-end');

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            $(modal).find('.modal-body').append(loadingCustomStage2);

            var curHeight = $(modal).find('.modal-body').height();

            var index_product_custom = '';
            index_product_custom = products_plan.map(function(p) { return p.id }).indexOf(product_ID);

            $(modal).find('#stage2-customization').find('.box-breadcrumbs').find('.title').html('Personalização de ' + products_plan[index_product_custom].product_name_short);
            $(modal).find('#stage2-customization').find('.background-photo').html('<img src="' + products_plan[index_product_custom].photo + '" class="product-photo">');
            $(modal).find('#stage2-customization').find('.name-product').html(products_plan[index_product_custom].product_name_short);
            $(modal).find('#stage2-customization').find('.qtd-product').html('Qtd: ' + products_plan[index_product_custom].amount);
            $(modal).find('#stage2-customization').find('.active_custom').attr('name', 'is_custom[' + product_ID + ']');
            $(modal).find('#stage2-customization').find('.active_custom').prop('checked', products_plan[index_product_custom].is_custom ? true : false).val(products_plan[index_product_custom].is_custom).change();
            $(modal).find('#add-list-custom-product').attr('product', product_ID);

            if (products_plan[index_product_custom].shopify_id > 0) {
                allow_change_in_block = true;
            }

            var append = '';
            if (products_plan[index_product_custom].custom_configs.length > 0) {
                products_plan[index_product_custom].custom_configs.forEach(function(custom) {
                    append += '<div class="row" style="margin-bottom: 20px;">';
                        append += '<input type="hidden" name="productsPlan[]" value="' + product_ID + '">';
                        append += '<div class="col-sm-12">';
                            append += '<div class="d-flex">';
                                append += '<div class="d-flex">';
                                    append += '<div style="margin-right: 8px;">';
                                        append += '<input type="hidden" name="type[' + product_ID + '][]" value="' + custom.type + '">';
                                        append += '<button style="width: 45px; height: 45px;" type="button" class="btn btn-outline-secondary btn-type-custom border-light-gray">';
                                            append += getIconTypeCustomProduct(custom.type);
                                        append += '</button>';
                                    append += '</div>';
                                append += '</div>';

                                append += '<div style="width: 100%; margin-right: 16px;">';
                                    append += '<input readonly type="text" name="label[' + product_ID + '][]" class="form-control input-pad edit-input added-custom-title" data-index="' + product_ID + '" placeholder="Nome para personalização" value="' + custom.label + '">';
                                append += '</div>';

                                append += '<div>';
                                    append += '<button style="width: 45px; height: 45px;" type="button" data-index="' + product_ID + '" class="btn btn-outline btn-delete btn-trash-custom d-flex justify-content-around align-items-center align-self-center flex-row">';
                                        append += '<span class="o-bin-1"></span>';
                                    append += '</button>';
                                append += '</div>';
                            append += '</div>';
                        append += '</div>';
                    append += '</div>';
                });
            } else {
                append += '<div class="row custom-empty"><div class="col-sm-12">Nenhuma personalização adicionada</div></div>';
            }

            if (allow_change_in_block) {
                $('.custom_products_checkbox').html(`
                <div class="d-flex align-items-center" style="line-heigth: 1;">
                    <div class="switch-holder">
                        <label class="switch">
                            <input type="checkbox" class="allow_change_in_block" name="allow_change_in_block" value="true">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <span>Aplicar personalização nas outras variantes deste produto</span>
                </div>
                `).css('margin-top', '20px');
            } else {
                $('.custom_products_checkbox').html('');
            }

            $(modal).find('#stage2-customization').find('.list-custom-products').html(append).promise().done(function() {
                $(modal).find('.product-photo').on('load', function() {
                    $(modal).find('.ph-item').fadeOut(100, function() { this.remove(); }).promise().done(function() {
                        $(modal).find('#tab-customizations_panel').addClass('show active').promise().done(function() {
                            $(modal).find('#stage2-customization').addClass('show active').promise().done(function() {
                                var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + (products_plan.length > 2 ? 65 : 55);
                                $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                            });
                        });
                    });
                });
            });
        });
    }

    function storePlan(modalID, type) {
        if (type == 'create') {
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
                    'name': $(modalID).find('#name').val(),
                    'price': $(modalID).find('#price').val(),
                    'description': $(modalID).find('#description').val()
                },
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    index();
                    $(modalID).find('#name').val('');
                    $(modalID).find('#price').val('');
                    $(modalID).find('#description').val('');

                    alertCustom('success', 'Plano adicionado com sucesso');

                    $(modalID).modal('hide');
                }
            });
        } else {
            $('.box-products .form-control').each(function() {
                var product_ID = $(this).parent().parent().data('code');

                var product_selected_index = selected_products.map(function(p) { return p.id; }).indexOf(product_ID);

                var name_input = $(this).attr('name');
                if (name_input == 'amount') {
                    var productID_ = $(this).parent().parent().parent().parent().attr('data-code');
                    var product_selected_index_ = selected_products.map(function(p) { return p.id; }).indexOf(productID_);

                    selected_products[product_selected_index_].amount = $(this).val();
                }
                if (name_input == 'value') selected_products[product_selected_index].value = $(this).val();
                if (name_input == 'currency_type_enum') selected_products[product_selected_index].currency_type_enum = $(this).val();
            });

            $.ajax({
                method: 'PUT',
                url: '/api/plans/' + plan_id + '/products',
                dataType: 'JSON',
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content')
                },
                data: {
                    'products': selected_products
                },
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    index();
                    alertCustom('success', 'Produtos do plano atualizados');

                    $(modalID).modal('hide');
                }
            });
        }
    }

    function updateCustomConfig() {
        var modal = '#modal_edit_plan';

        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('.modal-footer').find('.btn').prop('disabled', true);

        $(modal).find('.tab-pane').removeClass('show active').promise().done(function() {
            $(modal).find('.modal-body').append(loadingCustomStage2);

            var formDataCP = new FormData(document.getElementById('form-update-custom-config'));
            formDataCP.append('plan', plan_id);

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
                    $(modal).find('.modal-footer').find('.btn').prop('disabled', false);

                    getPlanData(modal, false);

                    alertCustom("error", "Erro ao atualizar configurações do plano");
                },
                success: function success(data) {
                    $(modal).find('.modal-footer').find('.btn').prop('disabled', false);

                    getPlanData(modal, true);

                    alertCustom("success", "Configurações do plano atualizado com sucesso");
                }
            });

        });
    }

    function  calculateCostsPlan() {
        var costs_plan = 0;

        for (var i = 0; i < selected_products.length; i++) {
            if (selected_products[i]['value']) {
                costs_plan += (selected_products[i]['value'].replace('.', '').replace(',', '.') * selected_products[i]['amount']);
            }
        }

        return costs_plan;
    }

    // Search products
    let timeoutID = null;
    $('body').on('keyup', '#search-product', function(e) {
        clearTimeout(timeoutID);

        var modal = '#' + $(this).parents('.modal').attr('id');
        var type = 'create';
        if (modal == '#modal_edit_plan') {
            type = 'edit';
        }

        var search_product = e.target.value;
        timeoutID = setTimeout(function() {
            searchProducts(search_product, modal, type);
        }, 800);
    });

    // Calculate details
    $('body').on('keyup', '.box-products #price', function() {
        if ($(this).val() != '') {
            var price = $(this).val().replace('.', '').replace(',', '.');

            var tax = (price * gateway_tax / 100).toFixed(2);
            var costs = calculateCostsPlan();
            var comission = (price - tax).toFixed(2);
            var return_value = (comission - costs).toFixed(2);

            $('.price-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(price));
            $('.tax-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(tax));
            $('.comission-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(comission));
            $('.profit-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(return_value));
        } else {
            var costs = calculateCostsPlan();
            var return_value = costs.toFixed(2);

            $('.price-plan').find('p').html('R$0,00');
            $('.tax-plan').find('p').html('R$0,00');
            $('.comission-plan').find('p').html('R$0,00');
            $('.profit-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format('-' + return_value));
        }
    });

    // Select products
    $('body').on('click', '.box-product', function() {
        var product_id = $(this).data('code');
        var tabID = $(this).parents('.tab-content').attr('id');
        var stageID = $(this).parents('.tab-pane').attr('id');

        if ((tabID == 'tabs-modal-edit-plans' && stageID == 'stage2') || (tabID == 'tabs-modal-create-plans' && stageID == 'stage1')) {
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
        }
    });

    // Remove products
    $('body').on('click', '.box-products .div-photo', function() {
        var product_id = $(this).parent().parent().data('code');
        var type = $(this).attr('data-type');
        var modal = '';
        var index_selected_products = selected_products.map(function(e) { return e.id; }).indexOf(product_id);

        selected_products.splice(index_selected_products, 1);

        if (type == 'create') {
            modal = '#modal_add_plan';
        } else {
            modal = '#modal_edit_plan';
        }

        getDetailsProducts(modal, type);
    });

    // All values products
    $('body').on('click', '#check-values', function() {
        var checkbox_value = $(this).val();
        if (checkbox_value == 0) {
            $(this).val(1);

            var first_value = $('.box-products').find('.product').first().find('.form-control[name="value"]').val();
            $('.box-products .form-control[name="value"]').val(first_value);
        } else if (checkbox_value == 1) {
            $(this).val(0);
        }
    });

    // Change values products
    $('body').on('keyup', '.form-control[name="value"]', function(e) {
        var same_cost = $('#check-values').val();
        if (same_cost == 1) {
            $('.box-products .form-control[name="value"]').val($(this).val());
        }
    });

    // Button next
    $('body').on('click', '#btn-modal-plan-next', function() {
        var modal = '#' + $(this).parents('.modal').attr('id');
        var type = $(this).attr('data-type');
        var tag = (type == 'create' ? '#tabs-modal-create-plans' : '#tabs-modal-edit-plans');
        var stage = $(modal).find(tag).find('.tab-pane.active').attr('id');

        if (type == 'custom') {
            updateCustomConfig(modal, true);
        } else {
            if (selected_products.length > 0) {
                if (type == 'create') {
                    if (stage == 'stage1') {
                        getDetailsProducts(modal, type);
                    } else if (stage == 'stage2') {
                        getPlanInformations(modal);
                    } else if (stage == 'stage3') {
                        storePlan(modal, type);
                    }
                }  else {
                    if (stage == 'stage1') {
                        getProducts(modal, type);
                    } else if (stage == 'stage2') {
                        getDetailsProducts(modal, type);
                    } else if (stage == 'stage3') {
                        storePlan(modal, type);
                    }
                }
            } else {
                alertCustom('error', 'Selecione um produto para prosseguir');
            }
        }
    });

    // Button return
    $('body').on('click', '#btn-modal-plan-return', function() {
        var modal = '#' + $(this).parents('.modal').attr('id');
        var type = $(this).attr('data-type');
        var tag = (type == 'create' ? '#tabs-modal-create-plans' : '#tabs-modal-edit-plans');
        var stage = $(modal).find(tag).find('.tab-pane.active').attr('id');

        if (type == 'create') {
            if (stage == 'stage1') {
                $(modal).modal('hide');
                $.when(modal).done(function() {
                    $(modal).find('.tab-pane').removeClass('show active');
                });
            } else if (stage == 'stage2') {
                getProducts(modal, type);
            } else if (stage == 'stage3') {
                getDetailsProducts(modal, type);
            }
        } else if (type == 'edit') {
            if (stage == 'stage2') {
                $(modal).find('.modal-body').css('height', 'auto');
                getPlanData(modal);
            } else if (stage == 'stage3') {
                getProducts(modal, type);
            }
        } else {
            getCustom(modal, false);
        }
    });

    // Add new Plan
    $("#add-plan").on('click', function () {
        selected_products = [];

        var modal = '#modal_add_plan';

        $(modal).find('.tab-pane').removeClass('show active');

        $(modal).attr('data-backdrop', 'static');
        $(modal).modal('show');

        getProducts(modal, 'create');
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
        products_plan = [];

        plan_id = $(this).attr('plan');

        var modal = '#modal_edit_plan';

        $(modal).attr('data-backdrop', 'static');
        $(modal).modal('show');

        getPlanData(modal);
    });

    // Edit Plan
    $('#table-plans').on('click', '.edit-plan', function () {
        products_plan = [];

        plan_id = $(this).attr('plan');

        var modal = '#modal_edit_plan';

        $(modal).attr('data-backdrop', 'static');
        $(modal).modal('show');

        getPlanData(modal);
    });

    // Edit informations plan
    $("body").on('click', '#btn-edit-informations-plan', function () {
        var parent = $(this).parent().parent().parent();
        var curHeight = parent.find('.informations-data').css('height', 'auto').height();

        if (!parent.find('.informations-data').hasClass('edit')) {
            setTimeout(function() {
                parent.find('.informations-data').addClass('edit');
                parent.find('.informations-data').find('.form-control').attr('readonly', false);
                parent.find('#price').val(function(index, value) {
                    return value.replace('R$', '');
                });
                parent.find('#price').mask('#.##0,00', { reverse: true });
                parent.find('.informations-data').append(
                    '<div class="buttons-update">' +
                        '<div class="d-flex mt-20" style="justify-content: flex-end !important;">' +
                            '<button type="button" class="btn btn-default btn-lg mr-10" id="btn-cancel-update-informations-plan">Cancelar</button>' +
                            '<button type="button" class="btn btn-primary btn-lg" id="btn-update-informations-plan">Atualizar</button>' +
                        '</div>' +
                    '</div>'
                ).promise().done(function() {
                    var autoHeight = parent.find('.informations-data').height();
                    parent.find('.informations-data').height(curHeight).animate({ height: autoHeight }, 300);
                });

                var name = parent.find('#name').attr('data');
                var price = parent.find('#price').val();
                var description = parent.find('#description').attr('data');

                parent.find('#name').val(name);
                parent.find('#price').val(price.replace('R$', ''));
                parent.find('#description').val(description);
            }, 0);
        }
    });

    // Cancel update informations plan
    $("body").on('click', '#btn-cancel-update-informations-plan', function () {
        var parents = $(this).parents('.informations-edit');
        var curHeight = parents.find('.informations-data').height();

        parents.find('.informations-data').removeClass('edit');
        parents.find('.form-control').attr('readonly', true);

        var name_short = parents.find('#name').attr('data-short');
        var price = parents.find('#price').attr('data');
        var description_short = parents.find('#description').attr('data-short');

        parents.find('#name').val(name_short);
        parents.find('#price').val(price);
        parents.find('#description').val(description_short);

        $(this).parents('.buttons-update').remove().promise().done(function() {
            var autoHeight = parents.find('.informations-data').css('height', 'auto').height();
            parents.find('.informations-data').height(curHeight).animate({ height: autoHeight }, 300);
        });
    });

    // Tab general data
    $('body').on('click', '#tab-general-data', function() {
        if (!$(this).hasClass('active')) {
            var modal = '#modal_edit_plan';
            getPlanData(modal);
        }
    });

    // Tab customizations
    $('body').on('click', '#tab-customizations', function() {
        if (!$(this).hasClass('active')) {
            var modal = '#modal_edit_plan';
            getCustom(modal, false);
        }
    });

    // Create/Edit customizations
    $('body').on('click', '.btn-customizations', function() {
        var product_ID = $(this).attr('data-product');
        var modal = '#modal_edit_plan';

        getProductCustom(modal, product_ID);
    });

    // Select type custom
    $('body').on('click', '.btn-type', function () {
        $('.btn-type').removeClass('btn-active');
        $(this).addClass('btn-active');
        $('#custom-type').val($(this).attr('typeCustom'));
    });

    // Add custom
    $('body').on('click', '#add-list-custom-product', function () {
        var custom_type = $('#custom-type').val();
        var custom_title = $('#custom-title').val();
        if (custom_title != '' && custom_type != '') {
            if ($('.list-custom-products').find('.custom-empty').length > 0) {
                $('.list-custom-products').html('');
            }

            $('#modal_edit_plan').find('.modal-body').css('height', 'auto');

            var input_type = getIconTypeCustomProduct(custom_type);

            var product_ID = $(this).attr('product');

            var append = '';
            append += '<div class="row" style="margin-bottom: 20px;">';
            append += '<input type="hidden" name="productsPlan[]" value="' + product_ID + '">';
                append += '<div class="col-sm-12">';
                    append += '<div class="d-flex">';
                        append += '<div class="d-flex">';
                            append += '<div style="margin-right: 8px;">';
                            append += '<input type="hidden" name="type[' + product_ID + '][]" value="' + custom_type + '">';
                                append += '<button style="width: 45px; height: 45px;" type="button" class="btn btn-outline-secondary btn-type-custom border-light-gray">';
                                    append += input_type;
                                append += '</button>';
                            append += '</div>';
                        append += '</div>';

                        append += '<div style="width: 100%; margin-right: 16px;">';
                            append += '<input readonly type="text" name="label[' + product_ID + '][]" class="form-control input-pad edit-input" placeholder="Nome para personalização" value="' + custom_title + '">';
                        append += '</div>';

                        append += '<div>';
                            append += '<button style="width: 45px; height: 45px;" type="button" class="btn btn-outline btn-delete btn-trash-custom d-flex justify-content-around align-items-center align-self-center flex-row">';
                                append += '<span class="o-bin-1"></span>';
                            append += '</button>';
                        append += '</div>';
                    append += '</div>';
                append += '</div>';
            append += '</div>';

            $('.list-custom-products').append(append);

            $('#custom-title').val('');
            $('#custom-type').val('');
            $('.btn-type').removeClass('btn-active');
        }
    });

    // Edit input custom
    $('body').on('click', '.edit-input', function () {
        $(this).prop('readonly', false);

        var parent = $(this).parent().parent().parent().parent();
        parent.find('.btn-type-custom').addClass('btn-edit');
        parent.find('.btn-trash-custom').removeClass('btn-delete').addClass('btn-edit-row').html(`
            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2L8.92308 14L2 7.33333" stroke="white" stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `);
    });

    // Save input custom
    $('body').on('click', '.btn-edit-row', function () {
        var parent = $(this).parent().parent().parent().parent();
        parent.find('.edit-input').prop('readonly', true);
        parent.find('.btn-type-custom').removeClass('btn-edit');
        parent.find('.btn-trash-custom').removeClass('btn-edit-row').addClass('btn-delete').html(`<span class="o-bin-1"></span>`);
    });

    // Delete custom
    $('body').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        $(this).parent().parent().parent().parent().remove();

        $('#modal_edit_plan').find('.modal-body').css('height', 'auto');

        if ($('.list-custom-products').find('.row').length == 0) {
            $('.list-custom-products').html('<div class="row custom-empty"><div class="col-sm-12">Nenhum personalização adicionada</div></div>');
        }
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

    // Delete Plan - btn modal
    $('body').on('click', '#btn-modal-plan-delete', function (event) {
        event.preventDefault();
        var plan = $(this).attr('plan');
        var modalID = $('#modal_delete_plan');

        $('#modal_edit_plan').modal('hide');
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
    })

    // Show all products
    $("body").on('click', '#all-products', function () {
        var open = $(this).attr('data-open');
        if (open == 1) {
            $(this).parent().find('.products-data').animate({height: '74px'}, 500);
            $(this).removeClass('open');
            $(this).html('Ver todos os produtos <span class="fas fa-chevron-down"></span>')

            $(this).attr('data-open', '0');
        } else {
            var autoHeight = $(this).parent().find('.products-data').find('.row').height();
            $(this).parent().find('.products-data').animate({height: autoHeight}, 500);
            $(this).addClass('open');
            $(this).html('Ver menos <span class="fas fa-chevron-up"></span>');

            $(this).attr('data-open', '1');
        }
    });


    // Update informations plan
    $("body").on('click', '#btn-update-informations-plan', function () {
        var modal = '#modal_edit_plan';

        var parents = $(this).parents('.informations-edit');
        var curHeight = parents.find('.informations-data').height();

        $.ajax({
            method: "PUT",
            url: '/api/plans/' + plan_id + '/informations',
            dataType: "json",
            data: {
                'name': $(modal).find('#name').val(),
                'price': $(modal).find('#price').val(),
                'description': $(modal).find('#description').val()
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadOnModalRemove('#modal_edit_plan');
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $(modal).find('.modal-title').html('Detalhes de ' + response.plan.name_short);

                $(modal).find('#name').val(response.plan.name_short);
                $(modal).find('#name').attr('data', response.plan.name).attr('data-short', response.plan.name_short);

                $(modal).find('#price').val(response.plan.price);
                $(modal).find('#price').attr('data', response.plan.price);

                $(modal).find('#description').val(response.plan.description_short);
                $(modal).find('#description').attr('data', response.plan.description).attr('data-short', response.plan.description_short);

                var price = response.plan.price.replace('R$', '').replace('.', '').replace(',', '.');

                var tax = (price * gateway_tax / 100).toFixed(2);
                var costs = calculateCostsPlan();
                var comission = (price - tax).toFixed(2);
                var return_value = (comission - costs).toFixed(2);

                $(modal).find('.price-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(price));
                $(modal).find('.tax-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(tax));
                $(modal).find('.comission-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(comission));
                $(modal).find('.profit-plan').find('p').html(new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(return_value));

                $(modal).find('.informations-data').removeClass('edit');
                $(modal).find('.informations-edit').find('.form-control').attr('readonly', true);
                $(modal).find('.buttons-update').remove().promise().done(function() {
                    var autoHeight = parents.find('.informations-data').css('height', 'auto').height();
                    parents.find('.informations-data').height(curHeight).animate({ height: autoHeight }, 300);
                });

                index();
                alertCustom('success', response.message);
            }
        });
    });

    // Edit products plan
    $("body").on('click', '#btn-edit-products-plan', function () {
        var modal = '#modal_edit_plan';

        getProducts(modal, 'edit');
    });

    $("#btn-search-plan").on('click', function () {
        index();
    });

    $('.active_custom').on('change', function () {
        enableDisabledCustomProduct(this.checked);
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
                                data += '<td id="" class="" style="vertical-align: middle; line-height: 1;">' + value.name_short + '<div style="color: #8B8B8B; line-height: 1;"><small>com ' + (value.products_length > 1 ? value.products_length + ' produtos' : value.products_length + ' produto') + '</small></div></td>';
                                data += '<td id="" class="" style="vertical-align: middle;">' + value.description + '</td>';
                                data += '<td id="" class="" style="vertical-align: middle;">' + value.price + '</td>';
                                data += '<td id="link" class="copy_link text-center" title="Copiar Link" style="vertical-align: middle; cursor:pointer;" link="' + value.code + '"><span class="display-sm-none display-m-none">Copiar </span><img src="/modules/global/img/icon-copy-c.svg"></td>';
                                data += '<td id="" class="text-center"><span class="badge badge-' + statusPlan[value.status] + '">' + value.status_translated + '</span></td>';
                                data += "<td class='mg-responsive text-center' style='line-height: 1;'>"
                                    data += "<div class='d-flex justify-content-end align-items-center'>";
                                        data += "<a title='Visualizar' class='mg-responsive pointer details-plan' plan='" + value.id + "' role='button'><span class='o-eye-1'></span></a>"
                                        data += "<a title='Editar' class='mg-responsive pointer edit-plan' plan='" + value.id + "' role='button'><span class='o-edit-1'></span></a>"
                                        data += "<a title='Excluir' class='mr-0 mg-responsive pointer delete-plan' plan='" + value.id + "' role='button'><span class='o-bin-1'></span></a>";
                                    data += "</div>";
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

    // Update table plans by 'enter' button
    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            index();
        }
    });

    // Tab general settings
    $('body').on('click', '#tab_configuration', function() {
        $('#modal_config_cost_plan').find('.modal-body').css('height', 'auto');
    });

    // Tab plan cost change
    $('body').on('click', '#tab_update_cost_block', function() {
        if (!$(this).hasClass('active')) {
            let modal = '#modal_config_cost_plan';
            getPlansConfig(modal);
        }
    });

    // Search plan
    let timeoutID_plan = null;
    $('body').on('keyup', '#search-plan', function(e) {
        clearTimeout(timeoutID_plan);

        var modal = '#modal_config_cost_plan';
        var search_plan = e.target.value;

        timeoutID_plan = setTimeout(function() {
            searchPlans(search_plan, modal);
        }, 800);
    });

    function searchPlans(plan, modal) {
        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('.box-plans').css({ 'max-height': '180px', 'padding-right': '0px' });
        $(modal).find('.box-plans').find('.scrollbox').remove();

        $(modal).find('.box-plans').html(loadingPlans).promise().done(function() {
            $.ajax({
                method: "POST",
                url: "/api/plans/search",
                data: {
                    project: projectId,
                    plan: plan
                },
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function error(response) {
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    var append = '<div class="row">';
                    if (response.data.length > 0) {
                        response.data.forEach(function(plan) {
                            var index_plan = selected_plans.map(function(e) { return e.id; }).indexOf(plan.id);
                            let select_all = $('#select-all').attr('data-selected');

                            append += '<div class="col-sm-6">';
                                append += '<div data-toggle="tooltip" data-placement="top" title="' + plan.name + '" data-code="' + plan.id + '" class="box-plan d-flex justify-content-between align-items-center ' + (select_all == 'true' ? 'selected' : '') + '">';
                                    append += '<div class="d-flex align-items-center">';
                                        append += '<div class="background-photo">';
                                            append += '<img class="product-photo" src="' + plan.photo + '">';
                                        append += '</div>';
                                        append += '<div>';
                                            append += '<h1 class="title">' + plan.name_short + '</h1>';
                                            append += '<p class="description">Custo: ' + plan.custo + '</p>';
                                        append += '</div>';
                                    append += '</div>';
                                    append += '<div class="check">';
                                        if (index_plan != -1 || select_all == 'true') {
                                            append += '<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">';
                                        }
                                    append += '</div>';
                                append += '</div>';
                            append += '</div>';
                        });
                    } else {
                        append += '<div class="col-sm-12">';
                            append += '<div class="text-center" style="height: 150px; margin-bottom: 25px; margin-top: 15px;"><img style="margin: 0 auto;" class="product-photo" src="/modules/global/img/search-product_not-found.svg" ></div>';
                            append += '<p class="m-0 text-center" style="font-size: 24px; line-height: 30px; color: #636363;">Nenhuma resultado encontrado.</p>';
                            append += '<p class="m-0 text-center" style="font-size: 16px; line-height: 20px; color: #9A9A9A;">Por aqui, nenhum plano com esse nome.</p>';
                        append += '</div>';
                    }
                    append + '</div>';

                    var curHeight = $(modal).find('.modal-body').height();
                    $(modal).find('#tab_update_cost_block-panel').find('.box-plans').html(append).promise().done(function() {
                        $('[data-toggle="tooltip"]').tooltip({
                            container: '.page',
                        });

                        if (response.data.length > 4) {
                            scrollCustom(modal + ' #tab_update_cost_block-panel .box-plans');
                        } else {
                            $(modal + ' #tab_update_cost_block-panel .box-plans').off('wheel');
                        }

                        $(modal).find('.ph-item').fadeOut(100, function() { this.remove(); }).promise().done(function() {
                            $(modal).find('.tab-content').fadeIn('fast').promise().done(function() {
                                var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + 65;
                                $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                            });
                        });
                    });
                }
            });
        });
    }

    //Select all plans
    $('body').on('click', '#select-all', function() {
        if (!$(this).hasClass('selected')) {
            $(this).attr('data-selected', true);
            $(this).addClass('selected');
            $(this).find('.check').html('<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">');

            $('#modal_config_cost_plan').find('.box-plan').addClass('selected');
            $('#modal_config_cost_plan').find('.box-plan').find('.check').html('<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">');
        } else {
            $(this).attr('data-selected', false);
            $(this).removeClass('selected');
            $(this).find('.check').html('');

            $('#modal_config_cost_plan').find('.box-plan').removeClass('selected');
            $('#modal_config_cost_plan').find('.box-plan').find('.check').html('');
        }
    });

    // Select plans
    $('body').on('click', '.box-plan', function() {
        var plan_id = $(this).data('code');

        if (!$(this).hasClass('selected')) {
            $(this).addClass('selected');
            $(this).find('.check').html('<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">');

            selected_plans.push({'id': plan_id});
        } else {
            $('#select-all').attr('data-selected', false)
            $('#select-all').removeClass('selected');
            $('#select-all').find('.check').html('');

            $(this).removeClass('selected');
            $(this).find('.check img').remove();

            var index_selected_plans = selected_plans.map(function(e) { return e.id; }).indexOf(plan_id);
            selected_plans.splice(index_selected_plans, 1);
        }
    });

    // Get Plans Config Cost
    function getPlansConfig(modal) {
        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('.tab-content').fadeOut('fast').promise().done(function() {
            $(modal).find('.modal-body').append(loadingPlansConfigCost).promise().done(function() {
                $.ajax({
                    method: "GET",
                    url: "/api/plans/user-plans",
                    data: {
                        project_id: projectId,
                        is_config: true
                    },
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    error: function error(response) {
                        errorAjaxResponse(response);
                    },
                    success: function success(response) {
                        var append = '<div class="row">';
                        if (response.data.length > 0) {
                            response.data.forEach(function(plan) {
                                var index_plan = selected_plans.map(function(e) { return e.id; }).indexOf(plan.id);
                                append += '<div class="col-sm-6">';
                                    append += '<div ' + (plan.name_short_flag ? 'data-toggle="tooltip" data-placement="top" title="' + plan.name + '"' : '') + ' data-code="' + plan.id + '" class="box-plan d-flex justify-content-between align-items-center">';
                                        append += '<div class="d-flex align-items-center">';
                                            append += '<div class="background-photo">';
                                                append += '<img class="product-photo" src="' + plan.photo + '">';
                                            append += '</div>';
                                            append += '<div>';
                                                append += '<h1 class="title">' + plan.name_short + '</h1>';
                                                append += '<p class="description">Custo: ' + plan.custo + '</p>';
                                            append += '</div>';
                                        append += '</div>';
                                        append += '<div class="check">';
                                            if (index_plan != -1) {
                                                append += '<img src="/modules/global/img/icon-product-selected.svg" alt="Icon Check">';
                                            }
                                        append += '</div>';
                                    append += '</div>';
                                append += '</div>';
                            });
                        } else {
                            append += '<div class="col-sm-12">';
                                append += '<div class="text-center" style="height: 150px; margin-bottom: 25px; margin-top: 15px;"><img style="margin: 0 auto;" class="product-photo" src="/modules/global/img/search-product_not-found.svg" ></div>';
                                append += '<p class="m-0 text-center" style="font-size: 24px; line-height: 30px; color: #636363;">Nenhuma resultado encontrado.</p>';
                                append += '<p class="m-0 text-center" style="font-size: 16px; line-height: 20px; color: #9A9A9A;">Por aqui, nenhum plano com esse nome.</p>';
                            append += '</div>';
                        }
                        append + '</div>';

                        var curHeight = $(modal).find('.modal-body').height();
                        $(modal).find('#tab_update_cost_block-panel').find('.box-plans').html(append).promise().done(function() {
                            $('[data-toggle="tooltip"]').tooltip({
                                container: '.page',
                            });

                            if (response.data.length > 4) {
                                scrollCustom(modal + ' #tab_update_cost_block-panel .box-plans');
                            } else {
                                $(modal + ' #tab_update_cost_block-panel .box-plans').off('wheel');
                            }

                            $(modal).find('.ph-item').fadeOut(100, function() { this.remove(); }).promise().done(function() {
                                $(modal).find('.tab-content').fadeIn('fast').promise().done(function() {
                                    var autoHeight = $(modal).find('.modal-body').css('height', 'auto').height() + 65;
                                    $(modal).find('.modal-body').height(curHeight).animate({ height: autoHeight }, 300);
                                });
                            });
                        });
                    }
                });
            });
        });
    }

    // Button custom config plan
    $(document).on('click', '#config-cost-plan', function () {
        selected_plans = [];

        var modal = '#modal_config_cost_plan';

        $(modal).find('#cost_plan').val('');
        $(modal).find('#search-plan').val('');
        $(modal).find('#select-all').removeClass('selected');
        $(modal).find('#select-all').find('.check').html('');

        $(modal).find('.modal-body').css('height', 'auto');

        $(modal).find('#tab_configuration').addClass('active');
        $(modal).find('#tab_update_cost_block').removeClass('show active');

        $(modal).find('#tab_configuration_cost-panel').addClass('show active');
        $(modal).find('#tab_update_cost_block-panel').removeClass('show active');

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
            },
            success: function success(response) {
                $('[data-toggle="tooltip"]').tooltip({
                    container: '.modal',
                });

                if (response.data.shopify_id == null) {
                    $('#tab_update_cost_block').prop('disabled', true);
                } else {
                    $('#tab_update_cost_block').prop('disabled', false);
                }
                const indexCurrency = (response.data.cost_currency_type == 'BRL') ? 0 : 1;

                $('#cost_currency_type').prop('selectedIndex', indexCurrency);
                $('#update_cost_shopify').prop('selectedIndex', response.data.update_cost_shopify);

                const prefixCurrency = (response.data.cost_currency_type == 'USD') ? 'US$' : 'R$';

                $('#cost_plan').maskMoney({thousands: '.', decimal: ',', allowZero: true, prefix: prefixCurrency});

                $('#modal_config_cost_plan').modal('show');
            },
        });
    });

    // Plan cost config update
    $(document).on('click', '.bt-update-cost-block', function (event) {
        let costCurrency = $('#cost_currency_type').val();
        let updateCostShopify = $('#update_cost_shopify').val();
        //let updateAllCurrency = $('#select-all').attr('data-selected');
        let cost = $('#cost_plan').val();

        $.ajax({
            method: "POST",
            url: '/api/plans/update-bulk-cost',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project: projectId,
                costCurrency: costCurrency,
                updateCostShopify: updateCostShopify,
                cost: cost,
                plans: selected_plans
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
                alertCustom('success', 'Configuração atualizada com sucesso');

                $('#modal_config_cost_plan').modal('hide');
            }
        });
    });

    function changeProductAmount(input) {
        let amount = parseInt(input.val());

        input.val(amount);
    }

    $('body').on('click', '.input-number button', function () {
        var input = $(this).parent().find('input');

        if ($(this).hasClass('btn-add')) {
            input[0].stepUp();
        } else if ($(this).hasClass('btn-sub')) {
            input[0].stepDown();
        }

        changeProductAmount(input);
    });

    $('body').on('keyup', '.input-number input', function () {
        let max = parseInt($(this).prop('max')) || 99;
        let min = parseInt($(this).prop('min')) || 1;
        let value = parseInt($(this).val() || 1);
        if (value <= min) $(this).val(min)
        if (value > max) $(this).val(max)
        changeProductAmount($(this));
    });

    $(document).on('click', '#update_cost_shopify', function (event) {
        alert('ok');
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
                $('#cost_plan').maskMoney({thousands: '.', decimal: ',', allowZero: true, prefix: prefixCurrency});
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
