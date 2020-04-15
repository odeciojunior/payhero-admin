$(() => {

    $(document).on('click', '.detalhes-black-antifraud', function () {
        let sale = $(this).attr('sale').replace('#', '');
        loadOnAny('#modal-sale-details-blackantifraud');
        $('#modal-detalhes-black-antifraud').modal('show');

        $.ajax({
            method: "GET",
            url: '/api/antifraud/' + sale,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadOnAny('#modal-sale-details-blackantifraud', true);
            },
            success: (response) => {
                getSale(response.data);
            }
        });

        function getSale(sale) {
            renderSale(sale);
            getProducts(sale.id);
            getClient(sale.customer_id);
            getDelivery(sale.delivery_id);
            getCheckout(sale.checkout_id);

        }

    });
    function renderSale(sale) {
        $('#sale-code').text(sale.id);
        $('#payment-type').text(`Pagamento via ${(sale.payment_method === 2 ? 'Boleto' : 'Cartao ' + sale.flag)} em ${sale.start_date} `);

        let status = $(".modal-body-detalhes-black-antifraud #status");
        status.html('').append(`<img style='width: 50px;' src='/modules/global/img/cartoes/${sale.flag}.png' alt='Cartao utilizado'>`);

    }

    function getProducts(saleId) {
        $.ajax({
            method: "GET",
            url: '/api/products/saleproducts/' + saleId,
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
        $("#table-product-black-antifraud").html('');
        let div = '';
        let photo = '/modules/global/img/produto.img';
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
            $("#table-product-black-antifraud").html(div);
        });

    }

    function getClient(customer) {
        $.ajax({
            method: "GET",
            url: '/api/customers/' + customer,
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

    function renderClient(customer) {
        $('#customer-name').text('Nome: ' + customer.name);
        $('#customer-telephone').val(customer.telephone)
            .attr('customer', customer.code)
            .mask('+0#');
        $('#customer-email').val(customer.email)
            .attr('customer', customer.code);
        $('#customer-document').text('CPF: ' + customer.document);
    }

    function getDelivery(delivery) {
        $.ajax({
            method: "GET",
            url: '/api/delivery/' + delivery,
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
        let deliveryAddress = 'Endereço: ' + delivery.street + ', ' + delivery.number;
        if (!isEmpty(delivery.complement)) {
            deliveryAddress += ', ' + delivery.complement;
        }
        $('#delivery-address').text(deliveryAddress);
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
        console.log(checkout);
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
        loadOnAny('#modal-sale-details-blackantifraud', true);
    }

});
