$(() => {

    $(document).on('click', '.detalhes-black-antifraud', function () {
        let sale = $(this).attr('sale').replace('#', '');
        loadOnAny('#modal-sale-details-blackantifraud');
        $('#modal-detalhes-black-antifraud').modal('show');

        $.ajax({
            method: "GET",
            url: '/api/salesblacklistantifraud/' + sale,
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
                console.log(response);
                getSale(response.data);
            }
        });

        function getSale(sale) {
            renderSale(sale);
            getProducts(sale);

        }

    });

    function getProducts(sale) {
        $.ajax({
            method: "GET",
            url: '/api/products/saleproducts/' + sale.id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                console.log(response);
                renderProducts(response.data, sale);
            }
        });
    }

    function renderSale(sale) {
        $('#payment-type').text('Pagamento via ' + (sale.payment_method === 2 ? 'Boleto' : 'Cartao ' + sale.flag) + ' em ' + sale.start_date);
    }

    function renderProducts(products, sale) {
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

        loadOnAny('#modal-sale-details-blackantifraud', true);

    }
});
