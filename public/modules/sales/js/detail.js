$(() => {
    // COMPORTAMENTOS DA JANELA

    //Codigo de rastreio
    $(document).on('click', '.btn-edit-trackingcode', function () {
        var trackingInput = $(this).parent().parent().find('#tracking_code');
        var trackingCodeSpan = trackingInput.parent().find('.tracking-code-span');
        var btnEdit = $(this);
        var btnSave = btnEdit.parent().find('.btn-save-trackingcode');
        var btnClose = $(this).parent().find('.btn-close-tracking');
        btnEdit.hide('fast');
        btnSave.show('fast');
        btnClose.show('fast');
        trackingCodeSpan.hide('fast');
        trackingInput.show('fast');
    });

    //Botão para ocultar campos rastreio
    $(document).on('click', '.btn-close-tracking', function () {
        var trackingInput = $(this).parent().parent().find('#tracking_code');
        var trackingCodeSpan = trackingInput.parent().find('.tracking-code-span');
        var btnEdit = $(this).parent().find('.btn-edit-trackingcode');
        var btnSave = $(this).parent().find('.btn-save-trackingcode');

        $(this).hide('fast');
        btnSave.hide('fast');
        trackingInput.hide('fast');
        btnEdit.show('fast');
        trackingCodeSpan.show('fast');
    });

    $('#discount_value').mask('00%', {reverse: true});

    $("#apply_discount").on("click", function () {
        if ($("#div_discount").is(":visible")) {
            $("#div_discount").hide();
            $("#discount_value").val("");
        } else {
            $("#div_discount").show();

            $("#discount_type").on('change', function () {
                if ($("#discount_type").val() == 'value') {
                    $("#discount_value").mask('#.###,#0', {reverse: true}).removeAttr('maxlength');
                    $("#label_discount_value").html("Valor (ex: 20,00)");
                } else {
                    $('#discount_value').mask('00%', {reverse: true});
                    $("#label_discount_value").html("Valor (ex: 20%)");
                }
            });
        }
    });

    $(document).on("click", '#boleto-link .copy_link', function () {
        var temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr('link')).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Link copiado!');
    });

    $(document).on("click", '#boleto-digitable-line .copy_link', function () {
        var temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr('digitable-line')).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Linha Digitável copiado!');
    });

    // FIM - COMPORTAMENTOS DA JANELA

    // MODAL DETALHES DA VENDA
    $(document).on('click', '.detalhes_venda', function () {
        // let btn_detalhe = $(this);
        // btn_detalhe.hide();
        // btn_detalhe.parent().append('<span class="loaderSpan"></span>');
        var venda = $(this).attr('venda');

        $('#modal_venda_titulo').html('Detalhes da venda ' + venda + '<br><hr>');
        $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

        $.ajax({
            method: "GET",
            url: '/api/sales/' + venda,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
                // btn_detalhe.parent().children('span').remove();
                // btn_detalhe.show();
            },
            success: (response) => {
                getSale(response);
            }
        });
    });

    function getSale(response){
        let sale = response.sale;

        renderSale(response);

        getClient(sale.client_id);

        getProducts(sale.code);

        getDelivery(sale.delivery_id);

        getCheckout(sale.checkout_id);

        // btn_detalhe.parent().children('span').remove();
        // btn_detalhe.show();
    }

    function renderSale(data) {
        //Dados da venda
        $('#sale-code').text(data.sale.code);
        $('#payment-type').text('Pagamento via ' + (data.sale.payment_method === 2 ? 'Boleto' : 'Cartão ' + data.sale.flag) + ' em ' + data.sale.start_date + ' às ' + data.sale.hours);
        var sale_status = data.sale.status;
        //Status
        let status = $('.modal-body #status');
        status.html('');
        status.append('<img style="width: 50px;" src="/modules/global/img/cartoes/' + data.sale.flag + '.png">');

        switch (data.sale.status) {
            case 1:
                status.append("<span class='badge badge-success'>Aprovada</span>");
                break;
            case 2:
                status.append("<span class='badge badge-pendente'>Pendente</span>");
                break;
            case 3:
                status.append("<span class='badge badge-danger'>Recusada</span>");
                break;
            case 4:
                status.append("<span class='badge badge-danger'>Estornada</span>");
                break;
            case 6:
                status.append("<span class='badge badge-primary'>Em análise</span>");
                break;
            default:
                status.append("<span class='badge badge-primary'>" + data.sale.status + "</span>");
                break;
        }

        //Valores
        $("#subtotal-value").html("R$ " + data.subTotal);
        $("#shipment-value").html("R$ " + data.shipment_value);

        $('#iof-label, #iof-value, #cambio-label, #cambio-value').hide();
        if (data.sale.dolar_quotation) {
            $('#iof-value span').text('R$ ' + data.sale.iof);
            $('#cambio-label span').text('Câmbio (1 $ = R$ ' + data.sale.dolar_quotation + '): ');
            $('#cambio-value span').text('US$ ' + data.taxa);
            $('#iof-label, #iof-value, #cambio-label, #cambio-value').show();
        }

        $("#desconto-value").html("R$ " + data.discount);
        $("#total-value").html("R$ " + data.total);

        $('#taxas-label').text('Taxas (' + data.transaction.percentage_rate + '% + ' + data.transaction.transaction_rate + '): ');
        $('#taxareal-value').text(data.taxaReal ? data.taxaReal : '');

        $('#convertax-label, #convertax-value').hide();
        if (data.convertax_value !== '0,00') {
            $('#convertax-value').text(data.convertax_value ? data.convertax_value : '');
            $('#convertax-label, #convertax-value').show();
        }

        $('#comission-value').text(data.comission ? data.comission : '');

        //Detalhes da venda
        if (data.sale.payment_method === 1) {
            $('#details-card #card-flag').text('Bandeira: ' + data.sale.flag);
            $('#details-card #card-installments').text('Quantidade de parcelas: ' + data.sale.installments_amount);
            $('#details-card').show();
        }

        if (data.sale.payment_method === 2) {
            $('#details-boleto #boleto-link a').attr('link', data.sale.boleto_link);
            $('#details-boleto #boleto-digitable-line a').attr('digitable-line', data.sale.boleto_digitable_line);
            $('#details-boleto #boleto-due').text('Vencimento: ' + data.sale.boleto_due_date);
            $('#details-boleto').show();
        }

        $('#checkout-attempts').hide();
        if (data.sale.payment_method === 1) {
            $('#checkout-attempts').text('Quantidade de tentativas: ' + data.sale.attempts).show();
        }

        $('.btn-save-trackingcode').attr('sale', data.sale.code);
    }

    function getClient(client) {
        $.ajax({
            method: "GET",
            url: '/api/client/' + client,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderClient(response.data);
            }
        });
    }

    function renderClient(client) {
        //Cliente
        $('#client-name').text('Nome: ' + client.name);
        $('#client-telephone').text('Telefone: ' + client.telephone);
        $('#client-whatsapp').attr('href', client.whatsapp_link);
        $('#client-email').text('Email: ' + client.email);
        $('#client-document').text('CPF: ' + client.document);
    }

    function getProducts(venda) {
        $.ajax({
            method: "GET",
            url: '/api/products/saleproducts/' + venda,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderProducts(response.data);
            }
        });
    }

    function renderProducts(products) {
        $("#table-product").html('');
        $('#data-tracking-products').html('');
        let div = '';
        let photo = '/modules/global/img/produto.png';
        $.each(products, function (index, value) {
            if (!value.photo) {
                value.photo = photo;
            }
            div += `<div class="row align-items-baseline justify-content-between mb-15">
                        <div class="col-lg-2">
                            <img src='${value.photo}' width='50px' style='border-radius: 6px;'>
                        </div>
                        <div class="col-lg-5">
                            <h4 class="table-title">${value.name}</h4>
                        </div>
                        <div class="col-lg-3 text-right">
                            <p class="sm-text text-muted">${value.amount}x</p>
                        </div>
                    </div>`;
            $("#table-product").html(div);

            //Tabela de produtos Tracking Code
            if (value.sale_status == 1) {
                let data = `<tr>
                                <td>
                                    <img src='${value.photo}'  width='35px;' style='border-radius:6px;'><br>
                                    <span class='small'>${value.name}</span>
                                </td>
                                <td>
                                    <span class='tracking-code-span small'>${value.tracking_code}</span>
                                    <input class='form-control' id='tracking_code' name='tracking_code' value='${value.tracking_code}' style='display:none;'/>
                                </td>
                                <td>
                                    <span class='tracking-status-span small'>${value.tracking_status_enum}</span>
                                </td>
                                <td>
                                    <a class='pointer btn-edit-trackingcode p-5' title='Editar Código de rastreio' product-code='${value.id}'><i class='icon wb-edit' aria-hidden='true' style='color:#f1556f;'></i></a>
                                    <a class='pointer btn-save-trackingcode p-3 mb-15' title='Salvar Código de rastreio' product-code='${value.id}' style='display:none;'><i class="material-icons gradient" style="font-size:17px;">save</i></a>
                                    <a class='pointer btn-close-tracking' title='Fechar' style='display:none;'><i class='material-icons gradient mt-5'>close</i></a>
                                </td>
                            </tr>`;
                $('#div_tracking_code').css('display', 'block');
                $('#data-tracking-products').append(data);
            }else{
                $('#div_tracking_code').css('display', 'none');
            }
        });
    }

    function getDelivery(deliveryId){
        $.ajax({
            method: "GET",
            url: '/api/delivery/' + deliveryId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderDelivery(response.data);
            }
        });
    }

    function renderDelivery(delivery){
        $('.btn-save-trackingcode').attr('delivery', delivery.id);
        $('#delivery-address').text('Endereço: ' + delivery.street + ', ' + delivery.number);
        $('#delivery-zipcode').text('CEP: ' + delivery.zip_code);
        $('#delivery-city').text('Cidade: ' + delivery.city + '/' + delivery.state);
    }

    function getCheckout(checkoutId){
        $.ajax({
            method: "GET",
            url: '/api/checkout/' + checkoutId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderCheckout(response.data);
            }
        });
    }

    function renderCheckout(checkout){
        $('#checkout-ip').text('IP: ' + checkout.ip);
        $('#checkout-operational-system').text('Dispositivo: ' + checkout.operational_system);
        $('#checkout-browser').text('Navegador: ' + checkout.browser);

        $('#checkout-src').text('SRC: ' + checkout.src);
        $('#checkout-source').text('UTM Source: ' + checkout.source);
        $('#checkout-medium').text('UTM Medium: ' + checkout.utm_medium);
        $('#checkout-campaign').text('UTM Campaign: ' + checkout.utm_campaign);
        $('#checkout-term').text('UTM Term: ' + checkout.utm_term);
        $('#checkout-content').text('UTM Content: ' + checkout.utm_content);

        //Abre o modal depois da ultima requisiçao
        $('#modal_detalhes').modal('show');
    }

    // FIM - MODAL DETALHES DA VENDA

    //Salvar Código de Restreio
    $(document).on('click', '.btn-save-trackingcode', function () {
        var btnSave = $(this);
        var trackingInput = $(this).parent().parent().find('#tracking_code');
        var tracking_code = trackingInput.val();
        var productId = $(this).attr('product-code');
        var saleId = $(this).attr('sale');
        if (tracking_code == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        $.ajax({
            method: "POST",
            url: '/api/tracking',
            data: {tracking_code: tracking_code, sale_id: saleId, product_id: productId},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                var trackingStatusSPan = trackingInput.parents().next('td').find('.tracking-status-span');
                var trackingCodeSpan = trackingInput.parent().find('.tracking-code-span');
                var btnEdit = btnSave.parent().find('.btn-edit-trackingcode');
                var btnClose = btnSave.parent().find('.btn-close-tracking');

                trackingCodeSpan.html(response.data.tracking_code);
                trackingStatusSPan.html(response.data.tracking_status);
                trackingInput.val(response.data.tracking_code);
                trackingInput.hide('fast');
                trackingCodeSpan.show('fast');
                btnSave.hide('fast');
                btnEdit.show('fast');
                btnClose.hide('fast');
                alertCustom('success', response.message);
            }
        });
    });
});
