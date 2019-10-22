$(function () {
    var statusPlan = {
        0: "danger",
        1: "success",
    }
    var projectId = $(window.location.pathname.split('/')).get(-1);
    var form_register_plan = $("#form-register-plan").html();
    var form_update_plan = $("#form-update-plan").html();
    $('#tab_plans').on('click', function () {
        index();
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
                    $('#modal-add-plan-body-error').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Você não cadastrou nenhum produto</strong></h3>' + '<h5 align="center">Deseja cadastrar uma produto? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                    $('#modal-footer-plan-error').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    $('#modal-error-plan').modal('show');
                } else {
                    $("#product_1").html('');
                    $(response.data).each(function (index, data) {
                        $("#product_1").append("<option value='" + data.id + "'>" + data.name + "</option>");
                    });
                    $("#modal-title-plan").html('<span class="ml-15">Adicionar Plano</span>');
                    $("#btn-modal").addClass('btn-save-plan');
                    $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar')
                    $("#modal_add_plan").modal('show');
                    $("#form-update-plan").hide();
                    $("#form-register-plan").show();

                    $('.products_amount').mask('0#');

                    $(document).on('click', '.btnDelete', function (event) {
                        event.preventDefault();
                        $(this).parent().parent().remove();
                        //remove o card container que fica sobrando
                        $('.card.container').each(function () {
                            if ($.trim($(this).html()) == '') {
                                console.log('oi')
                                $(this).remove();
                            }
                        })
                    });

                    //product
                    $('#price').mask('#.###,#0', {reverse: true});
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

                        $('.products_amount').mask('0#');
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
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);
                            },
                            success: function success(response) {
                                loadingOnScreenRemove();
                                index();
                                clearFields();
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
    });

    $("#btn-search-plan").on('click', function () {
        index();
    });

    /**
     * Update Table Plan
     */
    function index() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

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

                if (isEmpty(response.data)) {
                    $("#data-table-plan").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {

                    $("#data-table-plan").html('');
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td id=""     class=""                                 style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td id=""     class=""                                 style="vertical-align: middle;">' + value.description + '</td>';
                        data += '<td id="link" class="display-sm-none display-m-none copy_link" title="Copiar Link" style="vertical-align: middle;cursor:pointer;" link="' + value.code + '">' + value.code + '</td>';
                        data += '<td id=""     class="display-lg-none display-xlg-none" style="vertical-align: middle;"><a class="material-icons pointer gradient" onclick="copyToClipboard(\'#link\')"> file_copy</a></td>';
                        data += '<td id=""     class=""                                 style="vertical-align: middle;">' + value.price + '</td>';
                        data += '<td id=""     class=""                                                                ><span class="badge badge-' + statusPlan[value.status] + '">' + value.status_translated + '</span></td>';
                        data += "<td style='text-align:center' class='mg-responsive'>"
                        data += "<a title='Visualizar' class='mg-responsive pointer details-plan'    plan='" + value.id + "'  role='button'                                                 ><i class='material-icons gradient'>remove_red_eye</i></a>"
                        data += "<a title='Editar' class='mg-responsive pointer edit-plan'       plan='" + value.id + "'  role='button'data-toggle='modal' data-target='#modal-content' ><i class='material-icons gradient'>edit</i></a>"
                        data += "<a title='Excluir' class='mg-responsive pointer delete-plan'     plan='" + value.id + "'  role='button'data-toggle='modal' data-target='#modal-delete'  ><i class='material-icons gradient'>delete_outline</i></a>";
                        data += "</td>";
                        data += '</tr>';
                        $("#data-table-plan").append(data);
                        $('#table-plans').addClass('table-striped');
                    });

                    pagination(response, 'plans', index);
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
                                $.each(response.data.products, function (index, value) {
                                    data = '';
                                    data += '<tr>';
                                    data += '<td style="vertical-align: middle;">' + value.product_name + '</td>';
                                    data += '<td style="vertical-align: middle;">' + value.amount + '</td>';
                                    data += '</tr>';
                                    $("#products_plan_details").append(data);
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

                            $('#plan_id').val(response.data.id);
                            $('#plan-name_edit').val(response.data.name);
                            $('#plan-price_edit').val(response.data.price.replace(/[^0-9]/g, ''));
                            $('#plan-description_edit').val(response.data.description);
                            $('#plan-price_edit').mask('#.###,#0', {reverse: true});

                            if (response.data.products != undefined) {
                                $.each(response.data.products, function (index, value) {
                                    $('.products_row_edit').append(`
                                        <div class='card container '>
                                            <div id="products_div_edit" class="row">
                                                <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                                    <label>Produtos do plano:</label>
                                                    <select id="product_1" name="products[]" class="form-control products_edit">
                                                        <option value= ` + value.product_id + ` selected> ` + value.product_name + ` </option>
                                                     </select>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                    <label>Quantidade:</label>
                                                    <input value="` + value.amount + `" id="product_amount_1" class="form-control products_amount" type="text" data-mask='0#' name="product_amounts[]" placeholder="quantidade">
                                                </div>
                                                <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                    <label>Custo (<b>Un</b>):</label>
                                                    <input value="" id="product_cost_1" class="form-control products_cost" type="text" data-mask='0#' name="product_cost[]" placeholder="custo unitario">
                                                </div>
                                                <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                    <label>Custo Total:</label>
                                                    <input value="" id="product_total_1" class="form-control products_total" type="text" data-mask='0#' name="product_total[]" placeholder="Custo Total" readonly>
                                                </div>
                                             
                                                 <div class="switch-holder col-sm-4 col-md-3 col-lg3">
                                                    <label for="token" class='mb-10'>Dólar:</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="status" class='check shipping-status' value='0'>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                                
                                                <div class='form-group col-sm-12 offset-md-4 col-md-4 offset-lg-4 col-lg-4'>
                                                   <!--<label class="display-xsm-none">Remover:</label>-->
                                                   <button class='btn btn-outline btn-danger btnDelete form-control'>
                                                        <b>Remover </b><i class='icon wb-trash' aria-hidden='true'></i></button>
                                                    </button>
                                                </div>
                                            </div>
                                            <hr class='mb-30 display-lg-none display-xlg-none'>
                                        </div>
                                    `);
                                });
                                $('.products_cost').bind('keyup', calcularTotal)
                            } else {
                                $('.products_row_edit').append(`
                                    <div id="products_div_edit" class='card' > 
                                        <div  class="row">
                                            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                                <label>Produtos do plano:</label>
                                                <select id="product_1" name="products[]" class="form-control products_edit">
                                                 </select>
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Quantidade:</label>
                                                <input value="1" id="product_amount_1" class="form-control products_amount" type="text" data-mask='0#' name="product_amounts[]" placeholder="quantidade">
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Custo (<b>Un</b>):</label>
                                                <input value="" id="product_cost_1" class="form-control products_cost" type="text" data-mask='0#' name="product_cost[]" placeholder="custo unitario">
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3 col-lg-3">
                                                <label>Custo Total:</label>
                                                <input value="" id="product_total_1" class="form-control products_total" type="text" data-mask='0#' name="product_total[]" placeholder="Custo Total" readonly>
                                            </div>
                                         
                                             <div class="switch-holder col-sm-4 col-md-3 col-lg3">
                                                <label for="token" class='mb-10'>Dólar:</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" name="status" class='check shipping-status' value='0'>
                                                    <span class="slider round"></span>
                                                </label>
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
                                $('.products_cost').bind('keyup', calcularTotal)
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
                                            $("#products_edit").append("<option value='" + data.id + "'>" + data.name + "</option>");
                                        });
                                    }
                                });
                            }
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
                                                selectProduct.append("<option value='" + data.id + "' >" + data.name + "</option>");
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

                            loadingOnScreenRemove()

                            $(document).on('click', '.btnDelete', function (event) {
                                event.preventDefault();
                                $(this).parent().parent().remove();
                                //remove o card container que fica sobrando
                                $('.card.container').each(function () {
                                    if ($.trim($(this).html()) == '') {
                                        console.log('oi')
                                        $(this).remove();
                                    }
                                })
                            });

                            //product
                            $('#plan-price').mask('#.###,#0', {reverse: true});
                            var qtd_products = '1';

                            $('.add_product_plan_edit').on('click', function () {
                                qtd_products++;
                                var div_products = $('.products_row_edit').find('#products_div_edit').first().clone();

                                var new_div = div_products.clone();
                                var input = new_div.find('.products_amount');

                                input.addClass('products_amount');

                                div_products = new_div;

                                $('.products_row_edit').append('<div class="card container"><div class="row">' + new_div.html() + '</div></div>');

                                $('.products_amount').mask('0#');
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
                                var formData = new FormData(document.getElementById('form-update-plan'));
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

                                        index();
                                    }),
                                    success: function success(data) {
                                        loadingOnScreenRemove();
                                        alertCustom("success", "Plano atualizado com sucesso");
                                        index();
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

    function calcularTotal() {
        $('.products_cost, .products_amount_create').keyup(function () {
            let quantidade = $(this).parent().parent().find('.products_amount_create').val()
            if (quantidade == undefined) {
                quantidade = $(this).parent().parent().find('.products_amount').val()
            }
            let valor = $(this).parent().parent().find('.products_cost').val()
            $(this).parent().parent().find('.products_total').val(parseFloat(quantidade * valor))
        })
    }

    $('.products_cost, .products_amount_create').keyup(function () {
        let quantidade = $(this).parent().parent().find('.products_amount_create').val()
        let valor = $(this).parent().parent().find('.products_cost').val()
        $(this).parent().parent().find('.products_total').val(parseFloat(quantidade * valor))
    })
})
;
