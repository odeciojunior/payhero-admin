$(() => {
    // COMPORTAMENTOS DA JANELA

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
        let temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr('link')).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Link copiado!');
    });

    $(document).on("click", '#boleto-digitable-line .copy_link', function () {
        let temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr('digitable-line')).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Linha Digitável copiado!');
    });

    //Codigo de rastreio
    $(document).on('click', '.btn-edit-trackingcode', function () {
        let trackingInput = $(this).parent().parent().find('#tracking_code');
        let btnEdit = $(this);
        let btnNotify = btnEdit.parent().find('.btn-notify-trackingcode');
        let btnSave = btnEdit.parent().find('.btn-save-trackingcode');
        let btnClose = $(this).parent().find('.btn-close-tracking');
        btnEdit.hide();
        btnNotify.hide();
        btnSave.show('fast');
        btnClose.show('fast');
        trackingInput.css({
            borderColor: '',
            backgroundColor: ''
        }).prop('readonly', false);
    });

    //Botão para ocultar campos rastreio
    $(document).on('click', '.btn-close-tracking', function () {
        let trackingInput = $(this).parent().parent().find('#tracking_code');
        let btnEdit = $(this).parent().find('.btn-edit-trackingcode');
        let btnNotify = btnEdit.parent().find('.btn-notify-trackingcode');
        let btnSave = $(this).parent().find('.btn-save-trackingcode');

        $(this).hide();
        btnSave.hide();
        trackingInput.css({
            borderColor: 'transparent',
            backgroundColor: 'transparent'
        }).prop('readonly', true);
        btnEdit.show('fast');
        if (trackingInput.val() !== '') {
            btnNotify.show('fast');
        }
    });

    // FIM - COMPORTAMENTOS DA JANELA

    // MODAL DETALHES DA VENDA
    $(document).on('click', '.detalhes_venda', function () {
        let sale = $(this).attr('venda');

        loadOnAny('#modal-saleDetails');
        $('#modal_detalhes').modal('show');

        $.ajax({
            method: "GET",
            url: '/api/sales/' + sale,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                getSale(response.data);
            }
        });
    });

    function getSale(sale) {

        renderSale(sale);

        getClient(sale.client_id);

        getProducts(sale.id);

        getDelivery(sale.delivery_id);

        getCheckout(sale.checkout_id);

        getNotazz(sale.invoices);
    }

    function renderSale(sale) {
        //Dados da venda
        $('#sale-code').text(sale.id);
        $('#payment-type').text('Pagamento via ' + (sale.payment_method === 2 ? 'Boleto' : 'Cartão ' + sale.flag) + ' em ' + sale.start_date + ' às ' + sale.hours);
        if (sale.release_date != '') {
            $('#release-date').text('Data de liberação: ' + sale.release_date);
        } else {
            $('#release-date').text('');
        }

        //Status
        let status = $('.modal-body #status');
        status.html('');
        status.append('<img style="width: 50px;" src="/modules/global/img/cartoes/' + sale.flag + '.png">');

        switch (sale.status) {
            case 1:
                status.append("<span class='ml-2 badge badge-success'>Aprovada</span>");
                break;
            case 2:
                status.append("<span class='ml-2 badge badge-pendente'>Pendente</span>");
                break;
            case 3:
                status.append("<span class='ml-2 badge badge-danger'>Recusada</span>");
                break;
            case 4:
                status.append("<span class='ml-2 badge badge-danger'>Chargeback</span>");
                break;
            case 6:
                status.append("<span class='ml-2 badge badge-primary'>Em análise</span>");
                break;
            case 7:
                status.append("<span class='ml-2 badge badge-danger'>Estornado</span>");
                break;
            default:
                status.append("<span class='ml-2 badge badge-primary'>" + sale.status + "</span>");
                break;
        }

        //Valores
        $("#subtotal-value").html("R$ " + sale.subTotal);
        $("#shipment-value").html("R$ " + sale.shipment_value);

        $('#iof-label, #iof-value, #cambio-label, #cambio-value').hide();
        if (sale.dolar_quotation) {
            $('#iof-value span').text('R$ ' + sale.iof);
            $('#cambio-label span').text('Câmbio (1 $ = R$ ' + sale.dolar_quotation + '): ');
            $('#cambio-value span').text('US$ ' + sale.taxa);
            $('#iof-label, #iof-value, #cambio-label, #cambio-value').show();
        }

        $("#taxas-installment-free-label, #taxa-installment-value").hide();
        if (sale.installment_tax_value !== '0,00') {
            $("#taxa-installment-value").html('R$ ' + sale.installment_tax_value);
            $("#taxas-installment-free-label").show();
            $("#taxa-installment-value").show();
        }

        $("#desconto-value").html("R$ " + sale.discount);
        $("#total-value").html("R$ " + sale.total);

        $('#taxas-label').text(sale.percentage_rate ? 'Taxas (' + sale.percentage_rate + '% + ' + sale.transaction_rate + '): ' : 'Taxas');
        $('#taxareal-value').text(sale.taxaReal ? sale.taxaReal : '');

        $('#convertax-label, #convertax-value').hide();
        if (sale.convertax_value !== '0,00') {
            $('#convertax-value').text(sale.convertax_value ? sale.convertax_value : '');
            $('#convertax-label, #convertax-value').show();
        }

        $('#comission-value').text(sale.comission ? sale.comission : '');

        //Detalhes da venda
        if (sale.payment_method === 1) {
            $('#details-card #card-flag').text('Bandeira: ' + sale.flag);
            $('#details-card #card-installments').text('Quantidade de parcelas: ' + sale.installments_amount);
            $('#details-card').show();
            $('#details-boleto').hide();
        }

        if (sale.payment_method === 2) {
            $('#details-boleto #boleto-link a').attr('link', sale.boleto_link);
            $('#details-boleto #boleto-digitable-line a').attr('digitable-line', sale.boleto_digitable_line);
            $('#details-boleto #boleto-due').text('Vencimento: ' + sale.boleto_due_date);
            $('#details-card').hide();
            $('#details-boleto').show();
        }

        $('#checkout-attempts').hide();
        if (sale.payment_method === 1) {
            $('#checkout-attempts').text('Quantidade de tentativas: ' + sale.attempts).show();
        }

        if (sale.payment_method == 1 && sale.status == 1) {
            $('#div_refund_transaction').html('<button class="btn btn-secondary btn-sm btn_refund_transaction" sale=' + sale.id + '>Estornar transação</button>');
        } else {
            $('#div_refund_transaction').html('');
        }
    }

    function getNotazz(invoices) {
        if (!isEmpty(invoices)) {

            let lastInvoice = invoices[invoices.length - 1];

            $.ajax({
                method: "GET",
                url: '/api/apps/notazz/invoice/' + lastInvoice,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    renderNotazz(response.data);
                }
            });

        }
    }

    function renderNotazz(invoice) {

        if (!isEmpty(invoice)) {
            //exist

            $('#data-notazz-invoices').empty();

            if (invoice.date_pending) {

                let data = `<tr>
                                <td>
                                    ${invoice.date_pending}
                                </td>
                                <td>
                                    Pendente de envio
                                </td>
                                <td>
                                    0
                                </td>
                                <td>
                                    Sucesso
                                </td>
                                <td>
                                    
                                </td>
                            </tr>`;
                $('#data-notazz-invoices').append(data);
            }

            if (invoice.date_sent) {

                let return_message = (invoice.return_message == null) ? 'Sucesso' : invoice.return_message;

                let status = (invoice.return_message) ? 'Erro ao enviar para Notazz' : 'Enviado para Notazz';
                let link = (invoice.pdf) ? "<a href='" + invoice.pdf + "' class='copy_link' style='cursor:pointer;' target='_blank'><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a>" : '';
                let data = `<tr>
                                <td>
                                    ${invoice.date_sent}
                                </td>
                                <td>
                                    ${status}
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    ${return_message}
                                </td>
                                <td>
                                    ${link}
                                </td>
                            </tr>`;
                $('#data-notazz-invoices').append(data);
            }

            if (invoice.date_error) {
                let status = (invoice.return_message) ? 'Erro ao enviar para Notazz' : 'Enviado para Notazz';

                let data = `<tr>
                                <td>
                                    ${invoice.date_error}
                                </td>
                                <td>
                                    ${status}
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    ${invoice.return_message}
                                </td>
                                <td>
                                    
                                </td>
                            </tr>`;
                $('#data-notazz-invoices').append(data);
            }

            if (invoice.date_rejected) {

                let postback_message = (invoice.postback_message == null) ? 'Rejeitado' : invoice.postback_message;

                let data = `<tr>
                                <td>
                                    ${invoice.date_sent}
                                </td>
                                <td>
                                    Nota fiscal rejeitada
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    ${postback_message}
                                </td>
                                <td>
                                    
                                </td>
                            </tr>`;
                $('#data-notazz-invoices').append(data);
            }

            if (invoice.date_canceled) {

                let link = (invoice.pdf) ? "<a href='" + invoice.pdf + "' class='copy_link' style='cursor:pointer;' target='_blank'><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a>" : '';
                let data = `<tr>
                                <td>
                                    ${invoice.date_sent}
                                </td>
                                <td>
                                    Nota fical cancelada
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    Sucesso
                                </td>
                                <td>
                                    ${link}
                                </td>
                            </tr>`;
                $('#data-notazz-invoices').append(data);
            }

            if (invoice.return_message) {
                $('#div_notazz_schedule').html('Próxima tentativa de envio em ' + invoice.schedule);
            }

            $('#div_notazz_invoice').show();
        } else {
            //not exist
            $('#div_notazz_invoice').hide();
        }
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

    function getProducts(sale) {
        $.ajax({
            method: "GET",
            url: '/api/products/saleproducts/' + sale,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderProducts(response.data, sale);
            }
        });
    }

    function renderProducts(products, sale) {
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
                                    <span class='small' style='display: inline-block; width: 60px;white-space: nowrap;overflow: hidden !important;text-overflow: ellipsis;'>${value.name}</span>
                                </td>
                                <td>
                                    <input class='form-control' id='tracking_code' name='tracking_code' value='${value.tracking_code}' readonly style="border-color: transparent; background-color: transparent;"/>
                                </td>
                                <td>
                                    <span class='tracking-status-span small'>${value.tracking_status_enum}</span>
                                </td>
                                <td class="text-center" style="padding: 0 !important;">
                                    <a class='pointer btn-save-trackingcode' title='Salvar alterações' sale='${sale}' 
                                    product-code='${value.id}' style='display:none;'><i class="material-icons gradient" style="font-size:17px;">save</i></a>
                                    <a class='pointer btn-edit-trackingcode' title='Editar Código de rastreio' product-code='${value.id}'><i class='icon wb-edit' aria-hidden='true' style='color:#f1556f;'></i></a>
                                    <a class='pointer btn-notify-trackingcode' title='Enviar e-mail com codigo de rastreio para o cliente' tracking="${value.tracking_id}"
                                    style='margin-left: 10px; ${value.tracking_code ? '' : 'display:none;'}'><i class='icon wb-envelope' aria-hidden='true' style='color:#f1556f;'></i></a>
                                    <a class='pointer btn-close-tracking' title='Fechar' style='display:none;'><i class='material-icons gradient'>close</i></a>
                                </td>
                            </tr>`;
                $('#div_tracking_code').css('display', 'block');
                $('#data-tracking-products').append(data);
            } else {
                $('#div_tracking_code').css('display', 'none');
            }
        });
    }

    function getDelivery(deliveryId) {
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

    function renderDelivery(delivery) {
        $('.btn-save-trackingcode').attr('delivery', delivery.id);
        $('#delivery-address').text('Endereço: ' + delivery.street + ', ' + delivery.number);
        $('#delivery-zipcode').text('CEP: ' + delivery.zip_code);
        $('#delivery-city').text('Cidade: ' + delivery.city + '/' + delivery.state);
    }

    function getCheckout(checkoutId) {
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

    function renderCheckout(checkout) {
        $('#checkout-ip').text('IP: ' + checkout.ip);
        $('#checkout-operational-system').text('Dispositivo: ' + checkout.operational_system);
        $('#checkout-browser').text('Navegador: ' + checkout.browser);
        $('#checkout-src').text('SRC: ' + checkout.src);
        $('#checkout-source').text('UTM Source: ' + checkout.source);
        $('#checkout-medium').text('UTM Medium: ' + checkout.utm_medium);
        $('#checkout-campaign').text('UTM Campaign: ' + checkout.utm_campaign);
        $('#checkout-term').text('UTM Term: ' + checkout.utm_term);
        $('#checkout-content').text('UTM Content: ' + checkout.utm_content);

        //remove o loader depois de tudo carregado
        loadOnAny('#modal-saleDetails', true);
    }

    // FIM - MODAL DETALHES DA VENDA

    //Sallet Código de Rastreio
    $(document).on('click', '.btn-save-trackingcode', function () {
        let btnSave = $(this);
        let trackingInput = $(this).parent().parent().find('#tracking_code');
        let tracking_code = trackingInput.val();
        let productId = $(this).attr('product-code');
        let saleId = $(this).attr('sale');
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
                let trackingStatusSPan = trackingInput.parents().next('td').find('.tracking-status-span');
                let btnEdit = btnSave.parent().find('.btn-edit-trackingcode');
                let btnNotify = btnSave.parent().find('.btn-notify-trackingcode');
                let btnClose = btnSave.parent().find('.btn-close-tracking');

                trackingStatusSPan.html(response.data.tracking_status);
                trackingInput.val(response.data.tracking_code);
                trackingInput.css({
                    borderColor: 'transparent',
                    backgroundColor: 'transparent'
                }).prop('readonly', true);
                btnSave.hide();
                btnEdit.show('fast');
                btnNotify.show('fast');
                btnClose.hide();
                alertCustom('success', response.message);
            }
        });
    });

    //enviar e-mail com o codigo de rastreio
    $(document).on('click', '.btn-notify-trackingcode', function () {
        let tracking_id = $(this).attr('tracking');
        $.ajax({
            method: "POST",
            url: '/api/tracking/notify/' + tracking_id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: () => {
                alertCustom('success', 'Notificação enviada com sucesso');
            }
        });
    });

    //Estornar venda
    $(document).on('click', '.btn_refund_transaction', function () {
        var sale = $(this).attr('sale');
        $('#modal-refund-transaction').modal('show');
        $('#modal_detalhes').modal('hide');

        $(document).on('click', '.btn-confirm-refund-transaction', function () {
            $.ajax({
                method: "POST",
                url: '/api/sales/refund/' + sale,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $.getScript('/modules/sales/js/index.js?v=2', function () {
                        atualizar();
                    });
                    alertCustom('success', response.message);
                }
            });
        });
    });

});
