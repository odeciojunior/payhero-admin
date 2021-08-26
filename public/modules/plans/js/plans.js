$(function () {
    var statusPlan = {
        0: "danger",
        1: "success",
    }
    var projectId = $(window.location.pathname.split('/')).get(-1);
    var form_register_plan = $("#form-register-plan").html();
    var form_update_plan = $("#form-update-plan").html();

    var switch_modal = 'Create';

    var card_div_edit;
    var pageCurrent;
    // var card_div_create = $("#form-register-plan").find('.card-products').first().clone();

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
        $('#name').val('');
        $('#price').val('');
        $('#description').val('');
        $("#form-register-plan").html('');
        $("#form-register-plan").html(form_register_plan);
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

    function createNew()
    {
        var modalID = $('#modal_add_plan');
        
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
                $("#modal-content").hide();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                modalID.modal('show');
                
                var data = '<div class="row">';
                response.data.forEach(function(product) {
                    console.log(product.status_enum);
                    data += '<div class="col-sm-6">';
                        data += '<div class="box-product ' + (product.status_enum == 1 ? 'review' : '') + ' d-flex justify-content-between align-items-center">';
                            data += '<div class="d-flex align-items-center">';
                                data += '<img class="mr-15" src="' + product.photo + '" alt="Image Product">';
                                data += '<div>';
                                    data += '<h1 class="title">' + product.name_substr + '</h1>';
                                    data += '<p class="description">' + product.description + '</p>';
                                data += '</div>';
                            data += '</div>';
                            if (product.status_enum == 2) {
                                data += '<div class="check"></div>';
                            }
                        data += '</div>';
                    data += '</div>';
                });
                data + '</div>';

                modalID.find('#load-products').html(data);
            }
        });
    }

    function create() {
        switch_modal = 'Create';
        $.ajax({
            method: "POST",
            url: "/api/products/userproducts",
            data: {project: projectId},
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
                if (Object.keys(response.data).length === 0) {
                    var route = '/products/create';
                    $('#modal-title-plan-error').text("Oooppsssss!");
                    $('#modal-add-plan-body-error').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Você não cadastrou nenhum produto</strong></h3>' + '<h5 align="center">Deseja cadastrar um produto? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                    $('#modal-footer-plan-error').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    $('#modal-error-plan').modal('show');
                } else {
                    $("#product_1").html('');
                    $(response.data).each(function (index, data) {
                        $("#product_1").append(`<option value="${data.id}" ${data.type_enum == 2 && data.status_enum != 2 ? 'disabled' : ''} data-cost="${data.cost}">${data.name}</option>`);
                    });
                    $("#modal-title-plan").html('<span class="ml-15">Adicionar Plano</span>');

                    $("#btn-modal-plan").addClass('btn-save-plan');
                    $("#btn-modal-plan").removeClass('btn-update-config-custom');
                    $("#btn-modal-plan").removeClass('btn-update-plan');


                    $("#btn-modal-plan").html('<i class="material-icons btn-fix"> save </i>Salvar')
                    $("#modal_add_plan").modal('show');
                    $("#form-update-plan").hide();
                    $("#form-register-plan").show();

                    $('.products_amount').mask('0#');

                    if ($('#currency_type_project').val() == 1) {
                        $('#select_currency').prop('selectedIndex', 0);
                    } else {
                        $('#select_currency').prop('selectedIndex', 1);
                    }

                    $(document).on('click', '.btnDelete', function (event) {
                        event.preventDefault();
                        $(this).parent().parent().remove();
                        //remove o card container que fica sobrando
                        $('.card.container').each(function () {
                            if ($.trim($(this).html()) == '') {
                                $(this).remove();
                            }
                        })
                    });

                    //product
                    //$('#price').mask('#.###,#0', {reverse: true});
                    var qtd_products = '1';

                    var div_products = $('#products_div_' + qtd_products).parent().clone();

                    /**
                     * Add new product in array
                     */
                    $('#add_product_plan').on('click', function () {

                        qtd_products++;

                        var new_div = div_products.clone();
                        // var opt = new_div.find('option:selected');
                        // opt.remove();
                        // var select = new_div.find('select');
                        var input = new_div.find('.products_amount');

                        input.addClass('products_amount');
                        div_products = new_div;
                        $('#products').after('<div class="card container">' + new_div.html() + '</div>');
                        $('.products_cost').maskMoney({thousands: '.', decimal: ',', allowZero: true});
                        $('.products_amount').mask('0#');

                        if ($('#currency_type_project').val() == 1) {
                            $('.card.container .select_currency_create').prop('selectedIndex', 0);
                        } else {
                            $('.card.container .select_currency_create').prop('selectedIndex', 1);
                        }
                        bindModalKeys();
                    });

                    /**
                     * Save new Plan
                     */
                    $(".btn-save-plan").off('click');
                    $(".btn-save-plan").on('click', function () {

                        console.log('save create plan');
                        if (switch_modal != 'Create') return false;
                        var hasNoValue;
                        $('.products_amount_create').each(function () {
                            if ($(this).val() == '' || $(this).val() == 0) {
                                hasNoValue = true;
                            }
                        });
                        if (hasNoValue) {
                            alertCustom('error', 'Dados informados inválidos');
                            return false;
                        }

                        var formData = new FormData(document.getElementById('form-register-plan'));
                        formData.append("project_id", projectId);
                        $.ajax({
                            method: "POST",
                            url: '/api/project/' + projectId + '/plans',
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            cache: false,
                            error: function error(response) {
                                clearFields();
                                errorAjaxResponse(response);
                            },
                            success: function success(response) {
                                index();
                                clearFields();
                                bindModalKeys();
                                alertCustom("success", "Plano Adicionado!");
                            }
                        });
                    });
                }
            }
        });
    }

    /**
     * Add new Plan
     */
    $("#add-plan").on('click', function () {
        $('#modal_add_plan').attr('data-backdrop', 'static');
        create();
        $('.btn-close-add-plan').on('click', function () {
            clearFields();
            $('#modal_add_plan').removeAttr('data-backdrop');
        });
        bindModalKeys();
    });

    $("#btn-search-plan").on('click', function () {
        index();
    });

    /**
     * Update Table Plan
     */
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
                            data += '<td id=""     class=""                                 style="vertical-align: middle;">' + value.name + '</td>';
                            data += '<td id=""     class=""                                 style="vertical-align: middle;">' + value.description + '</td>';
                            data += '<td id="link" class="display-sm-none display-m-none copy_link" title="Copiar Link" style="vertical-align: middle;cursor:pointer;" link="' + value.code + '">' + value.code + '</td>';
                            data += '<td id=""     class="display-lg-none display-xlg-none" style="vertical-align: middle;"><a class="pointer" onclick="copyToClipboard(\'#link\')"> <span class="material-icons icon-copy-1"> content_copy </span> </a></td>';
                            data += '<td id=""     class=""                                 style="vertical-align: middle;">' + value.price + '</td>';
                            data += '<td id=""     class=""                                                                ><span class="badge badge-' + statusPlan[value.status] + '">' + value.status_translated + '</span></td>';
                            data += "<td style='text-align:center' class='mg-responsive'>"
                            data += "<a title='Visualizar' class='mg-responsive pointer details-plan'    plan='" + value.id + "'  role='button'><span class='o-eye-1'></span></a>"
                            data += "<a title='Editar' class='mg-responsive pointer edit-plan'       plan='" + value.id + "'  role='button'data-toggle='modal' data-target='#modal-content' ><span class='o-edit-1'></span></a>"
                            data += "<a title='Excluir' class='mg-responsive pointer delete-plan'     plan='" + value.id + "'  role='button'data-toggle='modal' data-target='#modal-delete'  ><span class='o-bin-1'></span></a>";
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

                /**
                 * Details Plan
                 */
                $(".details-plan").unbind('click');
                $('.details-plan').on('click', function () {
                    var plan = $(this).attr('plan');
                    // var data = {planId: plan, project: projectId};

                    $.ajax({
                        method: "GET",
                        url: '/api/project/' + projectId + '/plans/' + plan,
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function (_error3) {
                            function error(_x3) {
                                return _error3.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error3.toString();
                            };

                            return error;
                        }(function (response) {
                            errorAjaxResponse(response);
                        }), success: function success(response) {
                            if (response.message == 'error') {
                                alertCustom('error', 'Ocorreu um erro ao tentar buscar dados plano!');
                            } else {
                                $("#modal-title-details").html('Detalhes do Plano <br>');
                                $('#plan_name_details').text(response.data.name);
                                $('#plan_description_details').text(response.data.description);
                                $('#plan_code_edit_details').text(response.data.code);
                                $('#plan_price_edit_details').text(response.data.price);
                                $('#plan_status_edit_details').html('<span class="badge badge-' + statusPlan[response.data.status] + '">' + response.data.status_translated + '</span>');
                                $("#products_plan_details").html('');
                                $('#form-cart-shopify input').remove();
                                let formCartShopify = $('#form-cart-shopify');
                                $.each(response.data.products, function (index, value) {
                                    data = '';
                                    data += '<tr>';
                                    data += '<td style="vertical-align: middle;">' + value.product_name + '</td>';
                                    data += '<td style="vertical-align: middle;">' + value.amount + '</td>';
                                    data += '</tr>';
                                    $("#products_plan_details").append(data);
                                    if (formCartShopify.length) {
                                        if (value.shopify_id) {
                                            let inputs = `<input type="hidden" name="product_id_${index + 1}" value="${value.shopify_id}">
                                                          <input type="hidden" name="variant_id_${index + 1}" value="${value.variant_id}">
                                                          <input type="hidden" name="product_amount_${index + 1}" value="${value.amount}">`;
                                            formCartShopify.append(inputs)
                                                .show();
                                        } else {
                                            formCartShopify.hide();
                                        }
                                    }
                                });
                                $("#modal_details_plan").modal('show');
                            }
                        }
                    });
                });

                /**
                 * Edit Plan
                 */
                $(".edit-plan").unbind('click');
                $(".edit-plan").on('click', function () {
                    switch_modal = 'Edit';
                    $("#tab_plans-panel").loading({message: '...', start: true});

                    loadOnModal('#modal-add-body');
                    $("#modal-add-body").html("");
                    var plan = $(this).attr('plan');
                    $("#modal-title-plan").html('<span class="ml-15">Editar Plano</span>');
                    $(".btn-save-plan").off('click');

                    $.ajax({
                        method: "GET",
                        url: '/api/project/' + projectId + '/plans/' + plan,
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error() {
                            $("#tab_plans-panel").loading('stop');
                            errorAjaxResponse(response);
                        }, success: function success(response) {
                            $("#tab_plans-panel").loading('stop');

                            $("#form-update-plan").html('');
                            $("#form-update-plan").html(form_update_plan);

                            $('#plan_id').val(response.data.id);
                            $('#plan-name_edit').val(response.data.name);
                            $('#plan-price_edit').val(response.data.price);
                            $('#plan-description_edit').val(response.data.description);
                            //$('#plan-price_edit').mask('#.###,#0', {reverse: true});

                            $('.products_row_edit').html('');
                            $('.products_row_custom').html('');

                            var allow_change_in_block = false;
                            var idxProducts = [];

                            if (response.data.products != undefined) {
                                $.each(response.data.products, function (index, value) {
                                    let productCost = value.product_cost.split(' ')
                                    var product_total = productCost[1] * value.amount;
                                    $('.products_row_edit').append(`
                                        <div class='card container '>
                                            <div id="products_div_edit" class="row">
                                                <div class="form-group col-sm-9 col-md-9 col-lg-9">
                                                    <label>Produtos do plano:</label>
                                                    <select id="product_${index}" name="products[]" class="form-control products_edit plan_product">
                                                        <option value= ` + value.product_id + ` selected> ` + value.product_name + ` </option>
                                                     </select>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Quantidade:</label>
                                                    <input value="` + value.amount + `" id="product_amount_${index}" class="form-control products_amount products_amount_edit" type="text" data-mask='0#' name="product_amounts[]" placeholder="quantidade">
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Custo (<b>Un</b>):</label>
                                                    <input id="product_cost_${index}" class="form-control products_cost products_cost_update products_cost_edit" type="text" data-mask='0#' name="product_cost[]" placeholder="custo unitario" value="${productCost[1]}">
                                                </div>
                                                <div class="form-group col-sm-5 col-md-5 col-lg-5">
                                                    <label>Custo Total:</label>
                                                    <input value="${product_total}" id="product_total_${index}" class="form-control products_total products_total_edit" type="text" data-mask='0#' name="product_total[]" placeholder="Custo Total" readonly>
                                                </div>

                                                 <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Moeda:</label>
                                                    <select id='select_currency_${index}' class='form-control select_currency select_currency_edit' name='currency[]'>
                                                        <option value='BRL' selected >BRL</option>
                                                        <option value='USD' >USD</option>
                                                    </select>
                                                </div>

                                                <div class='form-group col-sm-12 offset-md-4 col-md-4 offset-lg-4 col-lg-4'>
                                                   <!--<label class="display-xsm-none">Remover:</label>-->

                                                   <button class='btn btn-outline btnDelete form-control d-flex justify-content-around align-items-center align-self-center flex-row'>
                                                        <b>Remover </b>
                                                        <span class="o-bin-1"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <hr class='mb-30 display-lg-none display-xlg-none'>
                                        </div>
                                    `);
                                });
                                $.each(response.data.products, function (index, value) {
                                    $('#select_currency_' + index).val(value.currency);
                                });
                                // $('.products_cost').bind('keyup', calcularTotal)
                                bindModalKeys();
                                card_div_edit = $('.products_row_edit').find('#products_div_edit').first().clone();

                                //implementation custom products
                                $.each(response.data.products, function (index, value) {
                                    if (value.shopify_id > 0) {
                                        allow_change_in_block = true;
                                    }
                                    idxProducts[value.id] = value.id * 10;

                                    $('.products_row_custom').append(`
                                        <div class='card container mb-3'>
                                            <div class="row mb-3">                                                
                                                <div class="col-sm-10">
                                                    <div class="img-preview mb-2">
                                                        <img src="${value.photo}" onerror="this.src='/modules/global/img/produto.svg'" width="45px" height="45px" class="float-left">
                                                    </div>
                                                    <h4 class="bold pl-10 mt-1">Produto: ${value.product_name} </h4>
                                                </div>
                                                <div class="col-sm-2" align="right">
                                                    <div class="switch-holder d-inline">
                                                        <label class="switch mt-15">
                                                            <input type="checkbox" class="active_custom" productId="${value.id}" name="is_custom[${value.id}]" value="true" ${value.is_custom ? 'checked' : ''}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-3 pr-0">
                                                    <div class="form-group">
                                                        <label for="type">Tipo</label>
                                                        <div>
                                                            <input type="hidden" id="type-custom-${value.id}" value="">
                                                            <button type="button" class="btn btn-outline-secondary p-2 type-${value.id}" typeCustom="Text" title="Solicitar personalização tipo Texto">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M3.9 1.8C2.7402 1.8 1.8 2.7402 1.8 3.9V7.5C1.8 7.99705 1.39705 8.4 0.9 8.4C0.402948 8.4 0 7.99705 0 7.5V3.9C0 1.74608 1.74608 0 3.9 0H7.5C7.99705 0 8.4 0.402948 8.4 0.9C8.4 1.39705 7.99705 1.8 7.5 1.8H3.9ZM3.9 22.2C2.7402 22.2 1.8 21.2598 1.8 20.1V16.5C1.8 16.003 1.39705 15.6 0.9 15.6C0.402948 15.6 0 16.003 0 16.5V20.1C0 22.2539 1.74608 24 3.9 24H7.5C7.99705 24 8.4 23.597 8.4 23.1C8.4 22.603 7.99705 22.2 7.5 22.2H3.9ZM22.2 3.9C22.2 2.7402 21.2598 1.8 20.1 1.8H16.5C16.003 1.8 15.6 1.39705 15.6 0.9C15.6 0.402948 16.003 0 16.5 0H20.1C22.2539 0 24 1.74608 24 3.9V7.5C24 7.99705 23.597 8.4 23.1 8.4C22.603 8.4 22.2 7.99705 22.2 7.5V3.9ZM20.1 22.2C21.2598 22.2 22.2 21.2598 22.2 20.1V16.5C22.2 16.003 22.603 15.6 23.1 15.6C23.597 15.6 24 16.003 24 16.5V20.1C24 22.2539 22.2539 24 20.1 24H16.5C16.003 24 15.6 23.597 15.6 23.1C15.6 22.603 16.003 22.2 16.5 22.2H20.1ZM6.9 4.8C6.40295 4.8 6 5.20295 6 5.7V7.2C6 7.69705 6.40295 8.1 6.9 8.1C7.39705 8.1 7.8 7.69705 7.8 7.2V6.6H11.1V17.4H9.3C8.80295 17.4 8.4 17.803 8.4 18.3C8.4 18.797 8.80295 19.2 9.3 19.2H14.7C15.197 19.2 15.6 18.797 15.6 18.3C15.6 17.803 15.197 17.4 14.7 17.4H12.9V6.6H16.2V7.2C16.2 7.69705 16.603 8.1 17.1 8.1C17.597 8.1 18 7.69705 18 7.2V5.7C18 5.20295 17.597 4.8 17.1 4.8H6.9Z" fill="#636363"/>
                                                                </svg>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary p-2 type-${value.id}" typeCustom="Image"  title="Solicitar personalização tipo Imagem">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M16.6645 4C18.5055 4 19.9979 5.4924 19.9979 7.33333C19.9979 9.17427 18.5055 10.6667 16.6645 10.6667C14.8236 10.6667 13.3312 9.17427 13.3312 7.33333C13.3312 5.4924 14.8236 4 16.6645 4ZM14.9979 7.33333C14.9979 8.2538 15.7441 9 16.6645 9C17.585 9 18.3312 8.2538 18.3312 7.33333C18.3312 6.41287 17.585 5.66667 16.6645 5.66667C15.7441 5.66667 14.9979 6.41287 14.9979 7.33333ZM0 3.16667C0 1.41777 1.41777 0 3.16667 0H20.8333C22.5823 0 24 1.41777 24 3.16667V20.8333C24 21.6251 23.7094 22.3491 23.229 22.9043C23.1932 22.9687 23.1482 23.0294 23.094 23.0845C23.0365 23.1429 22.9726 23.1911 22.9044 23.2289C22.3492 23.7093 21.6252 24 20.8333 24H3.16667C2.37332 24 1.64812 23.7083 1.09249 23.2262C1.02613 23.1888 0.963833 23.1415 0.90772 23.0845C0.855053 23.0311 0.811093 22.9722 0.77582 22.9099C0.29256 22.3539 0 21.6278 0 20.8333V3.16667ZM22.3333 20.8333V3.16667C22.3333 2.33824 21.6617 1.66667 20.8333 1.66667H3.16667C2.33824 1.66667 1.66667 2.33824 1.66667 3.16667V20.8333C1.66667 20.9377 1.67732 21.0395 1.69759 21.1378L10.2465 12.7234C11.2194 11.7657 12.7807 11.7657 13.7537 12.7233L22.3027 21.1365C22.3228 21.0386 22.3333 20.9372 22.3333 20.8333ZM3.16667 22.3333H20.8333C20.9299 22.3333 21.0243 22.3242 21.1157 22.3068L12.5847 13.9112C12.2603 13.592 11.7399 13.592 11.4156 13.9112L2.8856 22.3071C2.97667 22.3243 3.0706 22.3333 3.16667 22.3333Z" fill="#636363"/>
                                                                </svg>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary p-2 type-${value.id}" typeCustom="File"  title="Solicitar personalização tipo Arquivo">
                                                                <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.77024 2.7431C12.1117 0.399509 15.9106 0.399509 18.2538 2.74266C20.5369 5.02572 20.5954 8.69093 18.4294 11.0449L18.2413 11.2422L9.44124 20.0404L9.40474 20.0707C7.94346 21.3875 5.68946 21.3427 4.28208 19.9353C2.96306 18.6163 2.84095 16.5536 3.91574 15.0969C3.93908 15.0516 3.96732 15.0078 4.00054 14.9667L4.0541 14.907L4.14101 14.8193L4.28208 14.6714L4.28501 14.6743L11.7207 7.21998C11.9866 6.95336 12.4032 6.9286 12.6971 7.14607L12.7814 7.21857C13.048 7.48449 13.0727 7.90112 12.8553 8.19502L12.7828 8.27923L5.1882 15.8923C4.47056 16.7679 4.52044 18.0622 5.33784 18.8796C6.1669 19.7087 7.48655 19.7481 8.36234 18.998L17.195 10.1676C18.9505 8.40992 18.9505 5.56068 17.1931 3.80332C15.4907 2.10087 12.7635 2.04767 10.9971 3.64371L10.8292 3.80332L10.8166 3.81763L1.28033 13.354C0.987435 13.6468 0.512565 13.6468 0.219665 13.354C-0.0465948 13.0877 -0.0708047 12.671 0.147055 12.3774L0.219665 12.2933L9.76854 2.74266L9.77024 2.7431Z" fill="#636363"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                <div class="col-sm-7">
                                                    <div class="form-group">
                                                        <label for="title">Nome da personalização</label>
                                                        <input type="text" class="form-control input-pad" id="pro-title-${value.id}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button type="button" class="btn btn-plus" id="add-list-custom-product-${value.id}">                                                    
                                                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M18 9.5H1M9.5 1V18V1Z" stroke="#41DC8F" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="col-12">
                                                    <small>ATENÇÃO: Seja claro e objetivo. O campo de “nome” aparecerá como a descrição do arquivo ou texto que seu cliente preencherá na página de obrigado.</small>
                                                </div>
                                            </div>
                                            <div class="row my-5">
                                                <div class="col-12">
                                                    <h5 class="bold mb-0">Personalizações adicionadas</h5>
                                                </div>
                                            </div>
                                            <hr class="mb-4p">
                                            <div class="row">                                         
                                                <div class="col-sm-12" id="list-custom-products-${value.id}"></div>
                                            </div>
                                            <hr class='mb-30 display-lg-none display-xlg-none'>
                                        </div>
                                    `);

                                    $(`.type-${value.id}`).off('click');
                                    $(`.type-${value.id}`).on('click', function () {
                                        // $(document).on('click',`.type-${value.id}`,function(){
                                        $(`.type-${value.id}`).removeClass('btn-active');
                                        $(this).addClass('btn-active');
                                        $(`#type-custom-${value.id}`).val($(this).attr('typeCustom'));
                                    });

                                    $.each(value.custom_configs, function (indexC, valueC) {
                                        idxProducts[value.id]++;
                                        var inputType = getIconTypeCustomProduct(valueC.type);

                                        $(`#list-custom-products-${value.id}`).append(`
                                            <div class="row">
                                                <input type="hidden" name="productsPlan[]" value="${value.id}">
                                                <div class="col-1">
                                                    <input type="hidden" name="type[${value.id}][]" class="form-control input-pad" value="${valueC.type}">
                                                    <button type="button" id="btn-type-${idxProducts[value.id]}" class="btn btn-outline-secondary p-2 fc-${value.id} border-light-gray">
                                                        ${inputType}
                                                    </button>
                                                </div>
                                                <div class="col-9">
                                                    <input type="text" name="label[${value.id}][]" class="form-control input-pad fc-${value.id} edit-input"  
                                                    placeholder="Nome para personalização" value="${valueC.label}" index="${idxProducts[value.id]}">
                                                </div>
                                                <div class="form-group col-2">
                                                    <button type="button" id="btn-trash-${idxProducts[value.id]}"
                                                    class="remove-custom-product btn btn-outline btnDelete fc-${value.id} 
                                                    d-flex justify-content-around align-items-center align-self-center flex-row"><span class="o-bin-1"></span></button>
                                                </div>
                                            </div>                                            
                                        `);
                                    });

                                    $(`#add-list-custom-product-${value.id}`).off('click');
                                    $(`#add-list-custom-product-${value.id}`).on('click', function () {
                                        //$(document).on('click',`#add-list-custom-product-${value.id}`,function(){
                                        idxProducts[value.id]++;
                                        var proType = $(`#type-custom-${value.id}`).val();
                                        var proTitle = $(`#pro-title-${value.id}`).val();
                                        if (proTitle != '' && proType != '') {
                                            var inputType = getIconTypeCustomProduct(proType);

                                            $(`#list-custom-products-${value.id}`).append(`
                                                <div class="row">
                                                    <input type="hidden" name="productsPlan[]" value="${value.id}">
                                                    <div class="col-1">
                                                        <input type="hidden" name="type[${value.id}][]" class="form-control input-pad fc-${value.id}" value="${proType}">
                                                        <button type="button" class="btn btn-outline-secondary p-2 border-light-gray" id="btn-type-${idxProducts[value.id]}">
                                                            ${inputType}
                                                        </button>
                                                    </div>
                                                    <div class="col-9">
                                                        <input type="text" name="label[${value.id}][]" class="form-control input-pad fc-${value.id} edit-input"  
                                                        placeholder="Nome para personalização" value="${proTitle}" index="${idxProducts[value.id]}">
                                                    </div>
                                                    <div class="form-group col-2">
                                                        <button type="button" id="btn-trash-${idxProducts[value.id]}"
                                                        class="remove-custom-product btn btn-outline btnDelete d-flex fc-${value.id} 
                                                        justify-content-around align-items-center align-self-center flex-row"><span class="o-bin-1"></span></button>
                                                    </div>
                                                </div>
                                            `);

                                            $(`#pro-title-${value.id}`).val('');
                                        }
                                    });

                                    enableDisabledCustomProduct(value.is_custom, value.id);

                                });

                                if (allow_change_in_block) {
                                    $('#custom_products_checkbox').html('');
                                    $('#custom_products_checkbox').append(`
                                    <div class="col-md-12">
                                        <div class="switch-holder d-inline">
                                            <label class="switch">
                                                <input type="checkbox" class="allow_change_in_block" name="allow_change_in_block" value="true">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <span>Aplicar personalização nas outras variantes deste produto</span>
                                    </div>
                                    `);
                                }

                                $('.active_custom').off('change');
                                $('.active_custom').on('change', function () {
                                    var productId = $(this).attr('productId');
                                    enableDisabledCustomProduct(this.checked, productId);
                                });

                                $('.edit-input').off('click');
                                $('.edit-input').on('click', function () {
                                    //$(document).on('click', '.edit-input', function () {
                                    console.log('editando');
                                    var index = $(this).attr('index');
                                    $(`#btn-type-${index}`).addClass('btn-edit');
                                    $(`#btn-trash-${index}`).removeClass('btnDelete').attr('index', index);
                                    $(`#btn-trash-${index}`).addClass('btn-edit-row').html(`                                        
                                        <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 2L8.92308 14L2 7.33333" stroke="white" stroke-width="2.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    `);
                                });

                                $(document).on('click', '.btn-edit-row', function () {
                                    console.log('ok');
                                    var index = $(this).attr('index');
                                    $(`#btn-type-${index}`).removeClass('btn-edit');
                                    $(`#btn-trash-${index}`).addClass('btnDelete');
                                    $(`#btn-trash-${index}`).removeClass('btn-edit-row')
                                        .html(`<span class="o-bin-1"></span>`);
                                });
                            } else {
                                $('.products_row_edit').append(`
                                    <div id="products_div_edit" class='card' >
                                        <div  class="row">
                                            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                                <label>Produtos do plano:</label>
                                                <select id="product_${index}" name="products[]" class="form-control products_edit">
                                                 </select>
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Quantidade:</label>
                                                <input value="1" id="product_amount_${index}" class="form-control products_amount" type="text" data-mask='0#' name="product_amounts[]" placeholder="quantidade">
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Custo (<b>Un</b>):</label>
                                                <input id="product_cost_${index}" class="form-control products_cost products_cost_update" type="text" data-mask='0#' name="product_cost[]" placeholder="custo unitario" value="${value.product_cost}">
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Custo Total:</label>
                                                <input value="" id="product_total_${index}" class="form-control products_total" type="text" data-mask='0#' name="product_total[]" placeholder="Custo Total" readonly>
                                            </div>

                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Moeda:</label>
                                                <select id='select_currency' class='form-control select_currency' name='currency[]'>
                                                    <option value='BRL' selected >BRL</option>
                                                    <option value='USD' >USD</option>
                                                </select>
                                            </div>
                                             <div class='form-group col-sm-12 offset-md-4 col-md-4 offset-lg-4 col-lg-4'>
                                                 <button class='btn btn-outline btn-danger btnDelete form-control'>
                                                    <b>Remover </b><i class='icon wb-trash' aria-hidden='true'></i></button>
                                                 </button>
                                            </div>
                                            <hr class='mb-30 display-lg-none display-xlg-none'>
                                        </div>
                                    </div>
                                `);
                                $.each(response.data.products, function (index, value) {
                                    $('#select_currency_' + index).val(value.currency);
                                });
                                bindModalKeys();
                                // $('.products_cost').bind('keyup', calcularTotal)
                                $.ajax({
                                    method: "POST",
                                    url: "/api/products/userproducts",
                                    data: {project: projectId},
                                    dataType: "json",
                                    headers: {
                                        'Authorization': $('meta[name="access-token"]').attr('content'),
                                        'Accept': 'application/json',
                                    },
                                    error: function error() {
                                        $("#modal-content").hide();
                                        errorAjaxResponse(response);

                                    },
                                    success: function success(response) {
                                        $("#products_edit").html('');
                                        $(response.data).each(function (index, data) {
                                            $("#products_edit").append(`<option value="${data.id}" ${data.type_enum == 2 && data.status_enum != 2 ? 'disabled' : ''} data-cost="${data.cost}">${data.name}</option>`);
                                        });
                                    }
                                });
                            }
                            $('.products_cost').maskMoney({thousands: ',', decimal: '.', allowZero: true});

                            $.ajax({
                                method: "POST",
                                url: "/api/products/userproducts",
                                data: {project: projectId},
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: function error() {
                                    $("#modal-content").hide();
                                    errorAjaxResponse(response);

                                },
                                success: function success(response) {
                                    $(".products_edit").each(function () {
                                        var selectProduct = $(this);
                                        $(response.data).each(function (index, data) {
                                            if (data.id != selectProduct.val()) {
                                                selectProduct.append(`<option value="${data.id}" ${data.type_enum == 2 && data.status_enum != 2 ? 'disabled' : ''} data-cost="${data.cost}">${data.name}</option>`);

                                                card_div_edit = $('.products_row_edit').find('#products_div_edit').first().clone();
                                            }
                                        });

                                    });
                                }
                            });

                            $("#modal_add_plan").modal('show');
                            $("#form-register-plan").hide();
                            $("#form-update-plan").show();

                            $("#btn-modal-plan").removeClass('btn-save-plan');
                            $("#btn-modal-plan").removeClass('btn-update-config-custom');
                            $("#btn-modal-plan").addClass('btn-update-plan');
                            $("#btn-modal-plan").html('Salvar');
                            $("#btn-modal-plan").show();

                            $("#nav-custom-tab").on('click', function () {
                                $("#btn-modal-plan").removeClass('btn-update-plan');
                                $("#btn-modal-plan").addClass('btn-update-config-custom');
                                switch_modal = 'Custom';
                            });
                            $("#nav-geral-tab").on('click', function () {
                                $("#btn-modal-plan").removeClass('btn-update-config-custom');
                                $("#btn-modal-plan").addClass('btn-update-plan');
                                switch_modal = 'Edit';
                            });

                            $('.products_amount').mask('0#');

                            $('#products .card.container .row').each(function (index) {
                                findElementsEdit(this);
                            });

                            $(document).on('click', '.btnDelete', function (event) {
                                event.preventDefault();
                                $(this).parent().parent().remove();
                                //remove o card container que fica sobrando
                                $('.card.container').each(function () {
                                    if ($.trim($(this).html()) == '') {
                                        $(this).remove();
                                    }
                                })
                            });

                            //product
                            //$('#plan-price').mask('#.###,#0', {reverse: true});
                            var qtd_products = '1';

                            $('.add_product_plan_edit').on('click', function () {

                                qtd_products++;
                                var div_products = card_div_edit;

                                var new_div = div_products.clone();
                                var input = new_div.find('.products_amount');

                                input.addClass('products_amount');

                                div_products = new_div;

                                $('.products_row_edit').append('<div class="card container"><div class="row">' + new_div.html() + '</div></div>');
                                $('.products_cost').maskMoney({thousands: ',', decimal: '.', allowZero: true});

                                $('.products_amount').mask('0#');
                                bindModalKeys();
                            });

                        }
                    });
                });

                /**
                 * Delete Plan
                 */
                $('.delete-plan').on('click', function (event) {
                    event.preventDefault();
                    var plan = $(this).attr('plan');
                    $("#modal-delete-plan").modal('show');
                    $("#btn-delete-plan").unbind('click');
                    $("#btn-delete-plan").on('click', function () {
                        $("#modal-delete-plan").modal('hide');
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
            }
        });

        //Copia link do plano
        $("#table-plans").on("click", ".copy_link", function () {
            var temp = $("<input>");
            $("#table-plans").append(temp);
            temp.val($(this).attr('link')).select();
            document.execCommand("copy");
            temp.remove();
            alertCustom('success', 'Link copiado!');
        });
    }

    /* $('.products_cost, .products_amount, .products_amount_create').change(function () {
         calcularTotal(this)
     })

     function calcularTotal(element) {
         let quantidade = $(element).parent().parent().find('.products_amount_create').val()
         if (quantidade == undefined) {
             quantidade = $(element).parent().parent().find('.products_amount').val()
         }
         let valor = $(element).parent().parent().find('.products_cost').maskMoney('unmasked')[0];

         let moeda = $(element).parent().parent().find('[name="currency[]"]').val()
         let custoTotal = defineMoeda(quantidade, valor, moeda, element)
     }

     function defineMoeda(quantidade, valor, moeda, element) {
         let total = quantidade * valor;
         let valorFormatado;
         switch (moeda) {
             case 'USD':
                 valorFormatado = total.toLocaleString('pt-BR', {style: 'currency', currency: 'USD'});
                 valor = valor.toLocaleString('pt-BR', {style: 'currency', currency: 'USD'});
                 $(element).parent().parent().find('.products_cost, .products_cost_update').maskMoney({prefix: 'US$'});
                 break;
             case 'BRL':
                 valorFormatado = total.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                 valor = valor.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                 $(element).parent().parent().find('.products_cost, .products_cost_update').maskMoney({prefix: 'R$'});
                 break;
             default:
                 valorFormatado = total.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                 valor = valor.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                 $(element).parent().parent().find('.products_cost, .products_cost_update').maskMoney({prefix: 'R$'});
                 break;
         }

         $(element).parent().parent().find('.products_total').val(valorFormatado)
         $(element).parent().parent().find('.products_cost').val(valor)
         return valorFormatado;
     }

     $('.products_cost, .products_amount_create').keyup(function () {
         let quantidade = $(this).parent().parent().find('.products_amount_create').val()
         let valor = $(this).parent().parent().find('.products_cost').val()
         $(this).parent().parent().find('.products_total').val(parseFloat(quantidade * valor))
     })*/

    ////////////////////       NOVA LOGICA         ////////////////////////

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