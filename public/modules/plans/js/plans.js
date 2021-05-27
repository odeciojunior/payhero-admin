$(function () {
    var statusPlan = {
        0: "danger",
        1: "success",
    }
    var projectId = $(window.location.pathname.split('/')).get(-1);
    var form_register_plan = $("#form-register-plan").html();
    var form_update_plan = $("#form-update-plan").html();

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
    function create() {
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
                    $("#btn-modal").addClass('btn-save-plan');
                    $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar')
                    $("#modal_add_plan").modal('show');
                    $("#form-update-plan").hide();
                    $("#form-register-plan").show();

                    $('.products_amount').mask('0#');

                    if($('#currency_type_project').val() == 1) {
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
                        var new_product = $('#products').clone();
                        // var opt = new_div.find('option:selected');
                        // opt.remove();
                        // var select = new_div.find('select');
                        var input = new_div.find('.products_amount');

                        input.addClass('products_amount');
                        div_products = new_div;
                        $('#products').after('<div class="card container">' + new_div.html() + '</div>');
                        $('.products_cost').maskMoney({thousands: '.', decimal: ',', allowZero: true});
                        $('.products_amount').mask('0#');

                        if($('#currency_type_project').val() == 1) {
                            $('.card.container .select_currency_create').prop('selectedIndex', 0);
                        } else {
                            $('.card.container .select_currency_create').prop('selectedIndex', 1);
                        }
                        bindModalKeys();
                    });

                    /**
                     * Save new Plan
                     */
                    $(".btn-save-plan").unbind('click');
                    $(".btn-save-plan").on('click', function () {
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
                        loadingOnScreen();
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
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);
                            },
                            success: function success(response) {
                                loadingOnScreenRemove();
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

                            loadingOnScreenRemove();
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
                                    if(formCartShopify.length) {
                                        if(value.shopify_id){
                                            let inputs = `<input type="hidden" name="product_id_${index+1}" value="${value.shopify_id}">
                                                          <input type="hidden" name="variant_id_${index+1}" value="${value.variant_id}">
                                                          <input type="hidden" name="product_amount_${index+1}" value="${value.amount}">`;
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
                $(document).on('click','.remove-custom-product',function(){
                    $(this).parent('.card').remove();
                });
                $(".edit-plan").on('click', function () {
                    loadOnModal('#modal-add-body');
                    $("#modal-add-body").html("");
                    var plan = $(this).attr('plan');
                    $("#modal-title-plan").html('<span class="ml-15">Editar Plano</span>');
                    
                    $.ajax({
                        method: "GET",
                        url: '/api/project/' + projectId + '/plans/' + plan,
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error() {
                            errorAjaxResponse(response);

                            loadingOnScreenRemove()
                        }, success: function success(response) {
                            $("#form-update-plan").html('');
                            $("#form-update-plan").html(form_update_plan);

                            $("#btn-modal").removeClass('btn-update-config-custom');
                            $("#nav-custom-tab").on('click', function () {
                                console.log('custom');
                                $("#btn-modal").removeClass('btn-update-plan');
                                $("#btn-modal").addClass('btn-update-config-custom');
                            });
                            $("#nav-geral-tab").on('click', function () {
                                $("#btn-modal").removeClass('btn-update-config-custom');
                                $("#btn-modal").addClass('btn-update-plan');
                            });
        

                            $('#plan_id').val(response.data.id);
                            $('#plan-name_edit').val(response.data.name);
                            $('#plan-price_edit').val(response.data.price);
                            $('#plan-description_edit').val(response.data.description);
                            //$('#plan-price_edit').mask('#.###,#0', {reverse: true});

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
                                    $('#custom_products').append(`
                                        <div class='card container '>
                                            <div class="row">
                                                <div class="col-sm-12 col-md-12 col-lg-12">
                                                    <h4 class="bold">Produto: ${value.product_name} </h4>
                                                </div>                                                
                                                <div class="col-sm-12" id="area-custom-products-${index}"></div>
                                                <div class="col-sm-12 col-md-12 col-lg-12">
                                                    <button type="button" id="add_custom_product-${index}" class="btn btn-primary col-12">Adicionar item requerido</button>
                                                </div>
                                            </div>
                                            <hr class='mb-30 display-lg-none display-xlg-none'>
                                        </div>
                                    `);

                                    $.each(value.custom_configs, function (indexC,valueC) {
                                        
                                        $(`#area-custom-products-${index}`).append(`
                                            <div class="row">
                                                <input type="hidden" name="products[]" value="${value.product_id}">
                                                <div class="form-group col-4">
                                                    <label>Tipo:</label>
                                                    <select name="type[${value.product_id}][]" name="type" class="form-control select-pad">
                                                        <option value="image" ${valueC.type=='image'?'selected':''}>Imagem</option>    
                                                        <option value="file" ${valueC.type=='file'?'selected':''}>Arquivo</option>
                                                        <option value="text" ${valueC.type=='text'?'selected':''}>Texto</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-8">
                                                    <label>Título:</label>
                                                    <input name="label[${value.product_id}][]" class="form-control input-pad" type="text" 
                                                    placeholder="Ex. Verifique a qualidade da imagem antes de enviar" value="${valueC.label}">
                                                </div>
                                                <div class="form-group col-sm-12 offset-md-4 col-md-4 offset-lg-4 col-lg-4">
                                                    <button type="button" class="remove-custom-product btn btn-outline btnDelete form-control d-flex justify-content-around align-items-center align-self-center flex-row"><b>Remover </b><span class="o-bin-1"></span></button>
                                                </div>
                                            </div>
                                        `);
                                    });

                                    $(document).on('click',`#add_custom_product-${index}`,function(){
                                        console.log('add '+index);
                                        $('#area-custom-products-'+index).append(`
                                            <div class="row">
                                                <input type="hidden" name="products[]" value="${value.product_id}">
                                                <div class="form-group col-4">
                                                    <label>Tipo:</label>
                                                    <select name="type[${value.product_id}][]" name="type" class="form-control select-pad">
                                                        <option value="image">Imagem</option>    
                                                        <option value="file">Arquivo</option>
                                                        <option value="text">Texto</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-8">
                                                    <label>Título:</label>
                                                    <input value="" name="label[${value.product_id}][]" class="form-control input-pad" type="text" placeholder="Ex. Verifique a qualidade da imagem antes de enviar">
                                                </div>
                                                <div class="form-group col-sm-12 offset-md-4 col-md-4 offset-lg-4 col-lg-4">
                                                    <button type="button" class="remove-custom-product btn btn-outline btnDelete form-control d-flex justify-content-around align-items-center align-self-center flex-row"><b>Remover </b><span class="o-bin-1"></span></button>
                                                </div>
                                            </div>
                                        `);
                                    });
                                });
                                $.each(response.data.products, function (index, value) {
                                    $('#select_currency_' + index).val(value.currency);
                                });
                                // $('.products_cost').bind('keyup', calcularTotal)
                                bindModalKeys();
                                card_div_edit = $('.products_row_edit').find('#products_div_edit').first().clone();
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
                                            }
                                        });

                                    });
                                }
                            });

                            $("#modal_add_plan").modal('show');
                            $("#form-register-plan").hide();
                            $("#form-update-plan").show();

                            $("#btn-modal").removeClass('btn-save-plan');
                            $("#btn-modal").addClass('btn-update-plan');
                            $("#btn-modal").text('Atualizar');
                            $("#btn-modal").show();
                            $('.products_amount').mask('0#');

                            $('#products .card.container .row').each(function( index ) {
                                findElementsEdit(this);
                            });

                            loadingOnScreenRemove()

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

                            /**
                             * Update Plan
                             */
                            $(".btn-update-plan").unbind('click');
                            $(".btn-update-plan").on('click', function () {
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
                                loadingOnScreen();
                                $.ajax({
                                    method: "POST",
                                    // url: "/api/plans/" + plan,
                                    url: '/api/project/' + projectId + '/plans/' + plan,
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
                                        loadingOnScreenRemove();
                                        errorAjaxResponse(response);

                                        index(pageCurrent);
                                    }),
                                    success: function success(data) {
                                        loadingOnScreenRemove();
                                        alertCustom("success", "Plano atualizado com sucesso");
                                        index(pageCurrent);
                                    }
                                });
                            });

                            /**
                             * save config custom product
                             */
                            $(".btn-update-config-custom").unbind('click');
                            $(".btn-update-config-custom").on('click', function (){
                                
                                var formData = new FormData(document.getElementById('form-update-plan-tab-2'));
                                formData.append("plan", plan);
                                console.log(formData);
  
                                loadingOnScreen();

                                $.ajax({
                                    method: "POST",                                    
                                    url: '/api/plans/config-custom-product',
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
                                        loadingOnScreenRemove();
                                        errorAjaxResponse(response);

                                        index(pageCurrent);
                                    }),
                                    success: function success(data) {
                                        loadingOnScreenRemove();
                                        alertCustom("success", "Configurações do Plano atualizado com sucesso");
                                        index(pageCurrent);
                                    }
                                });

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
                        loadingOnScreen();
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
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);

                            }),
                            success: function success(response) {
                                loadingOnScreenRemove();
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
        loadingOnScreen();

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
                    let elemId = this.$element.attr('id');

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

        $('#modal_config_cost_plan').modal('show');

        $.ajax({
            method: "GET",
            url: '/api/projects/' + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error() {
                errorAjaxResponse(response);

                loadingOnScreenRemove()
            }, success: function success(response) {
                if(response.data.shopify_id == null) {
                    $('#tab_update_cost_block').prop('disabled', true);
                } else {
                    $('#tab_update_cost_block').prop('disabled', false);
                }
                var indexCurrency = (response.data.cost_currency_type == 'BRL') ? 0 : 1;
                $('#cost_currency_type').prop('selectedIndex', indexCurrency);
                $('#update_cost_shopify').prop('selectedIndex',response.data.update_cost_shopify);
                var prefixCurrency = (response.data.cost_currency_type == 'USD') ? 'US$' : 'R$';
                $('#cost_plan').maskMoney({thousands: ',', decimal: '.', allowZero: true, prefix: prefixCurrency});
            },
        });
    });

    $(document).on('click', '.bt-update-cost-block', function (event) {

        loadingOnScreen();
        console.log($('#add_cost_on_plans').val());
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
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            }),
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", "Configuração atualizada com sucesso");
            }
        });

    });

    $(document).on('click', '.bt-update-cost-configs', function (event) {
        loadingOnScreen();
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
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            }),
            success: function success(data) {
                loadingOnScreenRemove();
                var prefixCurrency = ($('#cost_currency_type').val() == 'USD') ? 'US$' : 'R$';
                $('#cost_plan').maskMoney({thousands: ',', decimal: '.', allowZero: true, prefix: prefixCurrency});
                $('#currency_type_project').val(($('#cost_currency_type').val() == 'USD') ? 2 : 1);
                alertCustom("success", "Configuração atualizada com sucesso");
                // index(pageCurrent);
            }
        });

    });

    $(document).on('change', '#cost_currency_type', function (event) {
        $('#div_update_cost_shopify').show();
    });
})
;
