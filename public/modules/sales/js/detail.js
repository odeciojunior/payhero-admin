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

                $(".btn_refund_transaction").unbind('click');
                $(".btn_refund_transaction").on('click', function () {
                    var sale = $(this).attr('sale');
                    $('#modal-refund-transaction').modal('show');
                    $('#modal_detalhes').modal('hide');

                    $(".btn-confirm-refund-transaction").unbind('click');
                    $(".btn-confirm-refund-transaction").on('click', function () {
                        refundedClick(sale);
                    })
                });
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
            case 8:
                status.append("<span class='ml-2 badge badge-danger'>Estorno Parcial</span>");
                break;
            case 20:
                status.append("<span class='ml-2 badge badge-antifraude'>Revisão Antifraude</span>");
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

        //Detalhes do shopify
        if (sale.has_shopify_integration) {
            if (sale.shopify_order || sale.status == 20) {
                // $('#shopify-order').text('TEM ORDER');
                $('#resendShopfyOrder').addClass('d-none');
                $('#resendShopfyOrder').removeClass('d-block');
                $('#resendeShopifyOrderButton').attr('sale', '');
            } else {/*
                $('#shopify-order').text('Ordem não foi gerada no shopify!');
                $('#div_details_shopify').html('<button class="btn btn-secondary btn-sm btn_new_order_shopify" sale=' + sale.id + '>Gerar ordem Shopify</button>');*/
                $('#resendShopfyOrder').removeClass('d-none');
                $('#resendShopfyOrder').addClass('d-block');
                $('#resendeShopifyOrderButton').attr('sale', sale.id);
                $(".btn_new_order_shopify").unbind('click');
                $(".btn_new_order_shopify").on('click', function () {
                    var sale = $(this).attr('sale');
                    console.log(sale)
                    $('#modal-new-order-shopify').modal('show');
                    $('#modal_detalhes').modal('hide');
                    $(".btn-confirm-new-order-shopify").unbind('click');
                    $(".btn-confirm-new-order-shopify").on('click', function () {
                        newOrderClick(sale);
                    })
                });
            }
            $('#details-shopify').show();
        } else {
            $('#resendShopfyOrder').addClass('d-none');
            $('#resendShopfyOrder').removeClass('d-block');
            $('#resendeShopifyOrderButton').attr('sale', '')
        }
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

        if ((sale.payment_method == 1 || sale.payment_method == 3) && sale.status == 1) {
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
            if (value.sale_status == 1 || value.sale_status == 4) {
                let data = `<tr>
                                <td>
                                    <img src='${value.photo}'  width='35px;' style='border-radius:6px;'><br>
                                    <span class='small ellipsis'>${value.name}</span>
                                </td>
                                <td>
                                    <span class="small font-weight-bold">${value.tracking_code}</span>
                                </td>
                                <td>
                                    <span class='tracking-status-span small'>${value.tracking_status_enum}</span>
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
        $('#delivery-neighborhood').text('Bairro: ' + delivery.neighborhood);
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

    //Estornar venda
    function refundedClick(sale) {
        console.log(currentPage)
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/sales/refund/' + sale,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                atualizar(currentPage);
            },
            success: (response) => {
                console.log(response);
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                atualizar(currentPage);
            }
        });
    }

    //Gera ordem shopify
    function newOrderClick(sale) {
        console.log(currentPage)
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/sales/newordershopify/' + sale,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                atualizar(currentPage);
            },
            success: (response) => {
                console.log(response);
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                atualizar(currentPage);
            }
        });
    }

});
