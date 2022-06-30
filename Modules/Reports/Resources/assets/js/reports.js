$(function () {
    loadingOnScreen();
    getInfo();
    verifyUserHasStore()

    $('.sirius-select1').each(function () {
        $(this).siriusSelect();
    });
    $('.sirius-select1').on('click', function() {
        $('.sirius-select1 .sirius-select-text').toggleClass('on');
    });

    let resumeUrl = '/api/reports/resume';

    if(sessionStorage.info) {
        let info = JSON.parse(sessionStorage.getItem('info'));
        $('input[name=daterange]').val(info.calendar);

    }

    function getCashback() {
        let cashHtml = '';
        $('#card-cashback .onPreLoad *' ).remove();
        $("#block-cash").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl + "/cashbacks?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },

            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { chart, count, total, variation } = response.data;

                if( count > 0 ) {
                    cashHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='cashback' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                ${total}
                            </h4>
                            <em class="${variation.color} visible">
                                <svg width="19" height="19" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.849471 0.404734L7.11918 0.245869C7.50392 0.23612 7.80791 0.540111 7.79816 0.924852L7.63929 7.19456C7.62955 7.5793 7.30975 7.8991 6.92501 7.90884C6.54027 7.91859 6.23628 7.6146 6.24603 7.22986L6.36228 2.64198L1.52072 7.48353C1.24178 7.76248 0.800693 7.77365 0.535534 7.5085C0.270375 7.24334 0.281551 6.80225 0.560497 6.52331L5.40205 1.68175L0.814167 1.798C0.429427 1.80775 0.125436 1.50376 0.135185 1.11902C0.144933 0.73428 0.46473 0.414483 0.849471 0.404734Z" fill="#1BE4A8"/>
                                </svg>
                                ${variation.value}
                            </em>
                        </div>
                        <div class="new-graph-cashback graph"></div>
                    `;
                    $("#block-cash").html(cashHtml);
                    $('.new-graph-cashback').width($("#block-cash").width() + 4);

                    $('.new-graph-cashback').html(`<canvas id="graph-cashback"></canvas>`);
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphCashback(series, labels);

                    $(window).on("resize", function() {
                        $('.new-graph-cashback').width($("#block-cash").width() + 4);
                    });

                } else {
                    cashHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='cashback' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">
                            ${emptyGraph}
                            <p class="noone-data">Não há dados suficientes</p>
                        </div>
                    `;
                    $("#block-cash").html(cashHtml);
                }
            }
        });


    }

    function getPending() {
        let pendHtml = '';
        $('#card-pending .onPreLoad *' ).remove();
        $("#block-pending").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl+ "/pendings?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { chart, total, variation } = response.data;

                if( total !== "0,00" ) {
                    pendHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='cashback' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                ${total}
                            </h4>
                            <em class="${variation.color} visible">
                                <svg width="19" height="19" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.849471 0.404734L7.11918 0.245869C7.50392 0.23612 7.80791 0.540111 7.79816 0.924852L7.63929 7.19456C7.62955 7.5793 7.30975 7.8991 6.92501 7.90884C6.54027 7.91859 6.23628 7.6146 6.24603 7.22986L6.36228 2.64198L1.52072 7.48353C1.24178 7.76248 0.800693 7.77365 0.535534 7.5085C0.270375 7.24334 0.281551 6.80225 0.560497 6.52331L5.40205 1.68175L0.814167 1.798C0.429427 1.80775 0.125436 1.50376 0.135185 1.11902C0.144933 0.73428 0.46473 0.414483 0.849471 0.404734Z" fill="#1BE4A8"/>
                                </svg>
                                ${variation.value}
                            </em>
                        </div>
                        <div class="new-graph-pending graph"></div>
                    `;
                    $("#block-pending").html(pendHtml);
                    $('.new-graph-pending').width($('#block-pending').width() + 4);
                    $('.new-graph-pending').html('<canvas id=graph-pending></canvas>')
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphPending(series,labels);

                    $(window).on("resize", function() {
                        $('.new-graph-pending').width($('#block-pending').width() + 4);
                    });

                } else {
                    pendHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='pending' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">
                            ${emptyGraph}
                            <p class="noone-data">Não há dados suficientes</p>
                        </div>
                    `;
                    $("#block-pending").html(pendHtml);
                }
            }
        });
    }

    function getCommission() {
        let comissionhtml = '';
        $('#card-comission .onPreLoad *' ).remove();
        $("#block-comission").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl + "/commissions?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { chart, total, variation } = response.data;

                if( total !== 'R$ 0,00' ) {
                    comissionhtml = `
                        <div class="container d-flex value-price">
                            <h4 id='comission' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                ${removeMoneyCurrency(total)}
                            </h4>
                            <em class="${variation.color} visible" style="display: none;">
                                <svg width="19" height="19" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.849471 0.404734L7.11918 0.245869C7.50392 0.23612 7.80791 0.540111 7.79816 0.924852L7.63929 7.19456C7.62955 7.5793 7.30975 7.8991 6.92501 7.90884C6.54027 7.91859 6.23628 7.6146 6.24603 7.22986L6.36228 2.64198L1.52072 7.48353C1.24178 7.76248 0.800693 7.77365 0.535534 7.5085C0.270375 7.24334 0.281551 6.80225 0.560497 6.52331L5.40205 1.68175L0.814167 1.798C0.429427 1.80775 0.125436 1.50376 0.135185 1.11902C0.144933 0.73428 0.46473 0.414483 0.849471 0.404734Z" fill="#1BE4A8"/>
                                </svg>
                                ${variation.value}
                            </em>
                        </div>
                        <div class="new-graph graph"></div>
                    `;
                    $("#block-comission").html(comissionhtml);
                    $('.new-graph').width($("#block-comission").width() + 4);
                    $('.new-graph').html('<canvas id=comission-graph></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    graphComission(series, labels);

                    $(window).on("resize", function() {
                        $('.new-graph').width($('#block-comission').width() + 4);
                    });

                } else {
                    comissionhtml = `
                        <div class="container d-flex value-price">
                            <h4 id='comission' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">
                            ${emptyGraph}
                            <p class="noone-data">Não há dados suficientes</p>
                        </div>
                    `;
                    $("#block-comission").html(comissionhtml);
                }
            }
        });
    }

    function getSales() {
        let salesHtml = '';
        $('#card-sales .onPreLoad *' ).remove();
        $("#block-sales").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl + "/sales?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { chart, total, variation } = response.data;

                if( total !== 0 ) {
                    salesHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='sales' class=" font-size-24 bold">
                                ${total}
                            </h4>
                            <em class="${variation.color} visible">
                                <svg width="19" height="19" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.849471 0.404734L7.11918 0.245869C7.50392 0.23612 7.80791 0.540111 7.79816 0.924852L7.63929 7.19456C7.62955 7.5793 7.30975 7.8991 6.92501 7.90884C6.54027 7.91859 6.23628 7.6146 6.24603 7.22986L6.36228 2.64198L1.52072 7.48353C1.24178 7.76248 0.800693 7.77365 0.535534 7.5085C0.270375 7.24334 0.281551 6.80225 0.560497 6.52331L5.40205 1.68175L0.814167 1.798C0.429427 1.80775 0.125436 1.50376 0.135185 1.11902C0.144933 0.73428 0.46473 0.414483 0.849471 0.404734Z" fill="#1BE4A8"/>
                                </svg>
                                ${variation.value}
                            </em>
                        </div>
                        <div class="new-graph-sell graph"></div>
                    `;
                    $("#block-sales").html(salesHtml);
                    $('.new-graph-sell').width($('#block-sales').width() + 4);
                    $('.new-graph-sell').html('<canvas id=graph-sell></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphSell(series, labels);

                    $(window).on("resize", function() {
                        $('.new-graph-sell').width($('#block-sales').width() + 4);
                    });

                }else {
                    salesHtml = `
                        <div class="container d-flex value-price" style="visibility: hidden; height: 30px;">
                            <h4 id='sales' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">
                            ${emptyGraph}
                            <p class="noone-data">Não há dados suficientes</p>
                        </div>
                    `;
                    $("#block-sales").html(salesHtml);
                }
            }
        });

    }

    function getProducts() {
        let lista = '';
        $('#card-products .onPreLoad *' ).remove();
        $("#block-products").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl+ "/products?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { total, products } = response.data;
                let spaceTotal = $('#block-products').width();

                if(total) {
                    $("#block-products").prepend(`
                        <footer class="footer-products scroll-212">
                            <ul class="list-products container"></ul>
                        </footer>
                    `);

                    $.each(products, function (i, product) {
                        let { color, amount, image, name } = product;

                        if(amount) {
                            lista = `
                                <li>
                                    <div class="box-list-products">
                                        <figure
                                            data-container="body"
                                            data-viewport=".container"
                                            data-placement="top"
                                            data-toggle="tooltip"
                                            title="${name}">
                                            <img class="photo" src="${image}" width="16px" height="16px" />
                                        </figure>
                                        <div class="bars ${color}" style="width:${(( 100 * amount ) / spaceTotal).toFixed(1)}%">
                                            <span>${(( 100 * amount ) / spaceTotal) > 6.1 ? amount : ''}</span>
                                        </div>
                                        <span style="color: #636363;">${(( 100 * amount ) / spaceTotal) > 6.1 ? '' : amount}</span>
                                    </div>
                                </li>
                            `;

                            $("#block-products .list-products").append(lista);
                            $('[data-toggle="tooltip"]').tooltip({
                                container: '.list-products'
                            });
                            $('.photo').on('error', function() {
                                $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg');
                            });

                            if(products.length < 4) {
                                lista = `<li>${noListProducts}</li>`;
                                $("#block-products .list-products").append(lista);
                            }
                        }
                    });
                }else {
                    lista = `
                        <div class="container d-flex value-price" style="visibility: hidden; height: 10px;">
                            <h4 id='products' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="empty-products">
                            ${emptyProducts}
                            <p class="noone">Nenhum produto vendido</p>
                        </div>
                    `;
                    $("#block-products").html(lista);
                }
                $('#card-products .ske-load').remove();
            }
        });
    }

    function getCoupons() {
        let cuponsHtml = '';
        $('#card-coupons .onPreLoad *').remove();
        $("#block-coupons").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl + "/coupons?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { coupons, total } = response.data;

                if( total != 0 ) {
                    cuponsHtml = `
                        <div class="container d-flex value-price" style="visibility: hidden; height: 15px;">
                            <h4 id="qtd-dispute" class="font-size-24 bold">0</h4>
                        </div>
                        <div class="container d-flex justify-content-between box-donut">
                            <div class="new-graph-pie graph"></div>
                            <div class="data-pie"><ul></ul></div>
                        </div>
                    `;
                    $("#block-coupons").html(cuponsHtml);
                    $('.new-graph-pie').html('<div class=graph-pie></div>');
                    let arr = [];
                    let seriesArr = [];

                    $.each(coupons, function (i, coupon) {
                        arr.push(coupon);
                    });

                    for(let i = 0; i < arr.length; i++) {
                        if(arr[i].amount != undefined) {
                            seriesArr.push(arr[i].amount);
                                $('.data-pie ul').append(
                                    `
                                        <li>
                                            <div class="donut-pie ${arr[i].color}">
                                                <figure>
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke-width="3"/>
                                                    </svg>
                                                </figure>
                                                <div>${arr[i].coupon}</div>
                                            </div>
                                            <div class="grey bold">${arr[i].amount}</div>
                                        </li>
                                    `
                                );

                        }
                    }
                        new Chartist.Pie('.graph-pie',
                        { series: seriesArr },
                        {
                            donut: true,
                            donutWidth: 20,
                            donutSolid: true,
                            startAngle: 270,
                            showLabel: false,
                            chartPadding: 0,
                            labelOffset: 0,
                        });

                } else {
                    cuponsHtml = `
                        <div class="container d-flex value-price" style="visibility: hidden; height: 15px;">
                            <h4 id="qtd-dispute" class="font-size-24 bold">0</h4>
                        </div>
                        <div class="d-flex align-items justify-around">
                            <div class="no-coupon">${emptyCoupons}</div>
                            <div class="msg-coupon">Nenhum cupom utilizado</div>
                        </div>

                    `;
                    $("#block-coupons").html(cuponsHtml);
                }
                $('#card-coupons .ske-load').hide();
            }
        });
    }

    function getTypePayments() {
        let paymentsHtml = '';
        $('#card-typepayments .onPreLoad *' ).remove();
        $("#block-payments").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl + "/type-payments?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let { boleto, credit_card, pix, total } = response.data;

                paymentsHtml = `
                    <div id="payment-type-items" class="custom-table pb-0 pt-0">
                        <div class="row container-payment" id="type-payment">
                            <div class="container">
                                <div class="data-holder b-bottom">
                                    <div class="box-payment-option">
                                        <div class="col-payment grey box-image-payment ico-pay">
                                            <div class="box-ico">
                                                <span class="box-ico-report">
                                                    <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"/>
                                                    </svg>
                                                </span>
                                            </div>Cartão
                                        </div>

                                        <div class="box-payment-option option">
                                            <div class="col-payment grey percentage-card" id='percent-credit-card'>${credit_card.percentage}</div>
                                            <div class="col-payment col-graph bar-payment">
                                                <div class="bar blue" style="width: ${credit_card.percentage};">-</div>
                                            </div>
                                            <div class="col-payment end"><span class="money-td green bold grey" id='credit-card-value'>R$ ${credit_card.value}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="data-holder b-bottom">
                                    <div class="box-payment-option">
                                        <div class="col-payment grey box-image-payment ico-pay">
                                            <div class="box-ico">
                                                <span class="box-ico-report">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="38.867"
                                                        height="40.868"
                                                        viewBox="0 0 38.867 40.868"
                                                        style="width: 24px;"
                                                    >
                                                        <g id="Grupo_61" data-name="Grupo 61" transform="translate(-2948.5 213.743)">
                                                            <g id="g992" transform="translate(2956.673 -190.882)">
                                                                <path id="path994" d="M-73.541-25.595a5.528,5.528,0,0,1-3.933-1.629l-5.68-5.68a1.079,1.079,0,0,0-1.492,0l-5.7,5.7a5.529,5.529,0,0,1-3.934,1.628H-95.4l7.193,7.194a5.753,5.753,0,0,0,8.136,0l7.214-7.214Z" transform="translate(95.4 34.202)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                            <g id="g996" transform="translate(2956.673 -212.243)">
                                                                <path id="path998" d="M-3.765-29.869A5.528,5.528,0,0,1,.169-28.24l5.7,5.7a1.056,1.056,0,0,0,1.493,0l5.68-5.68a5.529,5.529,0,0,1,3.934-1.629h.684l-7.214-7.214a5.753,5.753,0,0,0-8.136,0l-7.193,7.193Z" transform="translate(4.884 37.747)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                            <g id="g1000" transform="translate(2949 -201.753)">
                                                                <path id="path1002" d="M-121.731-14.725l-4.36-4.359a.83.83,0,0,1-.31.063h-1.982a3.917,3.917,0,0,0-2.752,1.14l-5.68,5.68a2.718,2.718,0,0,1-1.927.8,2.719,2.719,0,0,1-1.928-.8l-5.7-5.7a3.917,3.917,0,0,0-2.752-1.14h-2.437a.827.827,0,0,1-.293-.059l-4.377,4.377a5.753,5.753,0,0,0,0,8.136l4.377,4.377a.828.828,0,0,1,.293-.059h2.437a3.917,3.917,0,0,0,2.752-1.14l5.7-5.7a2.792,2.792,0,0,1,3.856,0l5.68,5.679a3.917,3.917,0,0,0,2.752,1.14h1.982a.83.83,0,0,1,.31.062l4.359-4.359a5.753,5.753,0,0,0,0-8.136" transform="translate(157.913 19.102)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </div> Pix
                                        </div>
                                        <div class="box-payment-option option">
                                            <div class="col-payment grey percentage-card" id='percent-values-pix'>
                                                ${pix.percentage}
                                            </div>
                                            <div class="col-payment col-graph bar-payment">
                                                <div class="bar pink" style="width: ${pix.percentage};">-</div>
                                            </div>
                                            <div class="col-payment end">
                                                <span class="money-td green grey bold" id='pix-value'>R$ ${pix.value}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="data-holder b-bottom">
                                    <div class="box-payment-option">
                                        <div class="col-payment grey box-image-payment ico-pay">
                                            <div class="box-ico">
                                                <span class="box-ico-report">
                                                    <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_386_407)">
                                                            <rect x="-161.098" y="-1313.01" width="646" height="1962" rx="12" fill="white"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4016 2.27981H2.40165C2.07013 2.27981 1.75218 2.41555 1.51776 2.65717C1.28333 2.89878 1.15163 3.22648 1.15163 3.56817V13.875C1.15172 14.2167 1.28346 14.5443 1.51787 14.7858C1.75229 15.0274 2.07019 15.1631 2.40165 15.1631H17.4019C17.7334 15.1631 18.0514 15.0273 18.2858 14.7857C18.5202 14.5441 18.6519 14.2164 18.6519 13.8747V3.56817C18.6519 3.39895 18.6196 3.23139 18.5567 3.07506C18.4939 2.91873 18.4018 2.77668 18.2857 2.65704C18.1696 2.5374 18.0317 2.44251 17.88 2.37779C17.7283 2.31306 17.5658 2.27977 17.4016 2.27981ZM2.40165 0.991455C1.7386 0.991455 1.10271 1.26293 0.633857 1.74616C0.165008 2.22939 -0.0983887 2.88479 -0.0983887 3.56817L-0.0983887 13.875C-0.0983887 14.5584 0.165008 15.2138 0.633857 15.6971C1.10271 16.1803 1.7386 16.4518 2.40165 16.4518H17.4019C17.7302 16.4518 18.0553 16.3851 18.3586 16.2556C18.6619 16.1261 18.9376 15.9363 19.1697 15.6971C19.4019 15.4578 19.586 15.1737 19.7116 14.8611C19.8373 14.5485 19.9019 14.2134 19.9019 13.875V3.56817C19.9019 3.22979 19.8373 2.89473 19.7116 2.58211C19.586 2.26948 19.4019 1.98543 19.1697 1.74616C18.9376 1.50689 18.6619 1.31709 18.3586 1.1876C18.0553 1.0581 17.7302 0.991455 17.4019 0.991455H2.40165Z" fill="#636363"/>
                                                            <path d="M4.34595 4.99976H6.27182V12.9399H4.34595V4.99976ZM7.23492 4.99976H8.19803V12.9399H7.23492V4.99976ZM14.9387 4.99976H15.9018V12.9399H14.9387V4.99976ZM11.087 4.99976H13.977V12.9399H11.087V4.99976ZM9.16113 4.99976H10.1242V12.9399H9.16113V4.99976Z" fill="#636363"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_386_407">
                                                                <rect width="20.082" height="15.46" fill="white" transform="translate(0 0.991486)"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </div> Boleto
                                        </div>
                                        <div class="box-payment-option option">
                                            <div class="col-payment grey percentage-card" id='percent-values-boleto'>
                                                ${boleto.percentage}
                                            </div>
                                            <div class="col-payment col-graph bar-payment">
                                                <div class="bar purple" style="width: ${boleto.percentage};">-</div>
                                            </div>
                                            <div class="col-payment end">
                                                <span class="money-td green bold grey" id='boleto-value'>R$ ${boleto.value}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // $('#payment-type-items .bar').addClass('visible');
                if( total !== "0,00" ) {
                    $("#block-payments").html(paymentsHtml);

                } else {
                    paymentsHtml = `
                        <div class="container d-flex value-price" style="visibility: hidden; height: 30px">
                            <h4 id='sales' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">
                            ${emptyGraph}
                            <p class="noone-data">Não há dados suficientes</p>
                        </div>

                    `;
                    $("#block-payments").html(paymentsHtml);
                }
                // $('#type-payment').addClass('visible');
                // $('#card-typepayments .ske-load').hide();
            }
        });
    }

    function getRegions() {
        let regionsHtml = '';
        $('#card-regions .onPreLoad *').remove();
        $("#block-regions").html(skeLoad);

        return $.ajax({
            method: "GET",
            url: resumeUrl + "/regions?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                regionsHtml = `
                    <footer class="container footer-regions">
                        <dl class="states">
                            <dt>SP</dt>
                            <dt>MG</dt>
                            <dt>RS</dt>
                            <dt>PR</dt>
                        </dl>
                        <section class="new-graph-regions graph">
                        </section>
                        <section class="info-regions">
                            <ul class="conversion-colors"></ul>
                        </section>
                        <section class="info-regions">
                            <ul class="regions-legend">
                                <li class="access"><span></span>Acessos</li>
                                <li class="conversion"><span></span>Conversões</li>
                            </ul>
                        </section>
                    </footer>
                `;                              
                
                if(response.data.length > 0){
                    let { region, access, conversion } = response.data;
                    let regions         = [...region];
                    let accessArr       = [...access];
                    let conversionArr   = [...conversion];
                    
                    $("#block-regions").html(regionsHtml);
                    $('.new-graph-regions').html('<canvas id="regionsChart" height="140" width="159"></canvas>');

                    conversion.map(v => $(".conversion-colors").append(`<li>${v}%</li>`));
                    graphRegions(regions, conversionArr, accessArr);

                } else {
                    regionsHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='sales' class="font-size-24 bold grey" style="visibility: hidden; height: 15px;">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">
                            ${emptyGraph}
                            <p class="noone-data">Não há dados suficientes</p>
                        </div>
                    `;
                    $("#block-regions").html(regionsHtml);
                }
            }
        });
    }


    // show/hide modal de exportar relatórios
    $(".lk-export").on('click', function(e) {
        e.preventDefault();
        $('.inner-reports').addClass('focus');
        $('.line-reports').addClass('d-flex');
    });

    $('.reports-remove').on('click', function (e) {
        e.preventDefault();
        $('.inner-reports').removeClass('focus');
        $('.line-reports').removeClass('d-flex');
    });

    $.ajax({
        method: "GET",
        url: "/api/projects?select=true",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            loadingOnScreenRemove();
            $("#modal-content").hide();
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (!isEmpty(response.data)) {

                $("#project-empty").hide();
                $("#project-not-empty").show();
                $("#export-excel").show();

                $.each(response.data, function (i, project) {
                    $("#select_projects").append(
                        $("<option>", {
                            value: project.id,
                            text: project.name,
                        })
                    );
                });
                if(sessionStorage.info) {
                    $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
                    $("#select_projects").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
                }
                resume();
            } else {
                $("#export-excel").hide();
                $("#project-not-empty").hide();
                $("#project-empty").show();
            }

            loadingOnScreenRemove();
        },
    });

    $("#select_projects").on("change", function () {
        $("#card-origin .ske-load").show();
        $('.origin-report').hide();
        $.ajaxQ.abortAll();
        updateStorage({company: $(this).val(), companyName: $(this).find('option:selected').text()})
        resume();
        $(".data-pie ul li").remove();
    });

    $("#origin").on("change", function () {
        $("#card-origin .ske-load").show();
        $('.origin-report').hide();

        $("#origin").val($(this).val());
        updateSalesByOrigin();
    });

    function resume() {
        getCommission()
        getPending()
        getCashback()
        getSales()
        getTypePayments()
        getProducts()
        getCoupons()
        getRegions()
        updateSalesByOrigin()
    }

    function updateReports() {
        $('#payment-type-items .bar').css('width','100%');
        $('#payment-type-items .bar').removeClass('blue');
        $('#payment-type-items .bar').removeClass('pink');
        $('#payment-type-items .bar').removeClass('purple');

        $("#revenue-generated, #qtd-aproved, #qtd-boletos, #qtd-recusadas, #qtd-chargeback, #qtd-pending, #qtd-canceled, #percent-boleto-convert,#percent-credit-card-convert, #percent-desktop, #percent-mobile, #qtd-cartao-convert, #qtd-boleto-convert, #ticket-medio"
        ).html("<span>" + "<span class='loaderSpan' >" + "</span>" + "</span>");
        loadOnTable("#origins-table-itens", ".table-vendas-itens");

        if($('.ske-load').is(':hidden')) {
            $('.ske-load').show();
            $('.no-graph').remove();
            $('.graph *').remove();
            $('.value-price *').removeClass('visible');
            $("#type-payment").removeClass('visible');
            $('.list-products li').remove();
            $(".origin-report").hide();
        }

        resume();
    }

    function updateSalesByOrigin() {
        var link =
            arguments.length > 0 && arguments[0] !== undefined
                ? arguments[0]
                : null;

        // loadOnTable("#origins-table", ".table-vendas");

        link = `${resumeUrl}/origins?date_range=${$("input[name='daterange']").val()}&origin=${$("#origin").val()}&project_id=${$("#select_projects option:selected").val()}`;

        $.ajax({
            url: link,
            type: "GET",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let td = `
                    <td>
                        ${noWithdrawal}
                    </td>
                    <td>
                        <p class='no-data-origin'>
                            <strong>Sem dados, por enquanto...</strong>
                            Ainda faltam dados suficientes a comparação, continue rodando!
                        </p>
                    </td>
                    `

                if (response.data == '') {
                    $("#card-origin .ske-load").hide();
                    $("#origins-table").html(td);
                    $("#pagination").html("");
                    $("#pagination-origins").hide();
                    $(".origin-report").show();
                } else {
                    var table_data = "";

                    $.each(response.data, function (index, data) {
                        table_data += "<tr>";
                            table_data += "<td>" + data.origin + "</td>";
                            table_data += "<td>" + data.sales_amount + "</td>";
                            table_data += "<td style='text-align: right;'>" + data.value + "</td>";
                        table_data += "</tr>";
                    });

                    $("#origins-table").html("");
                    $("#origins-table").append(table_data);
                    $("#card-origin .ske-load").hide();
                    $(".table-vendas").addClass("table-striped");

                    pagination(response, "origins", updateSalesByOrigin);
                    $(".origin-report").show();
                }

            },
        });
    }

    function updateGraph(chartData) {
        var scoreChart = function scoreChart(
            id,
            labelList,
            series1List,
            series2List,
            series3List
        ) {
            var scoreChart = new Chartist.Line(
                "#" + id,
                {
                    labels: labelList,
                    series: [series1List, series2List, series3List],
                },
                {
                    lineSmooth: Chartist.Interpolation.simple({
                        divisor: 2,
                    }),
                    fullWidth: !0,
                    chartPadding: {
                        right: 30,
                        left: 40,
                    },
                    series: {
                        "credit-card-data": {
                            showArea: !0,
                        },
                        "boleto-data": {
                            showArea: !0,
                        },
                        "pix-data": {
                            showArea: !0,
                        },
                    },
                    axisX: {
                        showGrid: !1,
                    },
                    axisY: {
                        labelInterpolationFnc: function labelInterpolationFnc(
                            value
                        ) {
                            value = value * 100;
                            // value = Math.round(value,1);
                            var str = value.toString();
                            str = str.replace(".", "");
                            let complete = 3 - str.length;
                            if (complete == 1) {
                                str = "0" + str;
                            } else if (complete == 2) {
                                str = "00" + str;
                            }
                            str = str.replace(/([0-9]{2})$/g, ",$1");
                            if (str.length > 6) {
                                str = str.replace(
                                    /([0-9]{3}),([0-9]{2}$)/g,
                                    ".$1,$2"
                                );
                            }
                            return chartData.currency + str;
                            return value / 1e3 + "K";
                        },
                        scaleMinSpace: 40,
                    },
                    plugins: [
                        Chartist.plugins.tooltip({
                            position: "bottom",
                        }),
                        Chartist.plugins.legend(),
                    ],
                    low: 0,
                    height: 300,
                }
            );
            scoreChart
                .on("created", function (data) {
                    var defs =
                            data.svg.querySelector("defs") ||
                            data.svg.elem("defs"),
                        filter =
                            (data.svg.width(),
                            data.svg.height(),
                            defs.elem(
                                "filter",
                                {
                                    x: 0,
                                    y: "-10%",
                                    id: "shadow" + id,
                                },
                                "",
                                !0
                            ));
                    return (
                        filter.elem("feGaussianBlur", {
                            in: "SourceAlpha",
                            stdDeviation: "800",
                            result: "offsetBlur",
                        }),
                        filter.elem("feOffset", {
                            dx: "0",
                            dy: "800",
                        }),
                        filter.elem("feBlend", {
                            in: "SourceGraphic",
                            mode: "multiply",
                        }),
                        defs
                    );
                })
                .on("draw", function (data) {
                    "line" === data.type
                        ? data.element.attr({
                                filter: "url(#shadow" + id + ")",
                            })
                        : "point" === data.type &&
                            new Chartist.Svg(
                                data.element._node.parentNode
                            ).elem("line", {
                                x1: data.x,
                                y1: data.y,
                                x2: data.x + 0.01,
                                y2: data.y,
                                class: "ct-point-content",
                            }),
                        ("line" !== data.type && "area" != data.type) ||
                            data.element.animate({
                                d: {
                                    begin: 1e3 * data.index,
                                    dur: 1e3,
                                    from: data.path
                                        .clone()
                                        .scale(1, 0)
                                        .translate(
                                            0,
                                            data.chartRect.height()
                                        )
                                        .stringify(),
                                    to: data.path.clone().stringify(),
                                    easing:
                                        Chartist.Svg.Easing.easeOutQuint,
                                },
                            });
                });
        },
        labelList = chartData.label_list,
        creditCardSalesData = {
            name: "Cartão de crédito",
            data: chartData.boleto_data,
        },
        boletoSalesData = {
            name: "Boleto",
            data: chartData.credit_card_data,
        },
        pixSalesData = {
            name: "PIX",
            data: chartData.pix_data,
        };
        (createChart = function createChart(button) {
            scoreChart(
                "scoreLineToDay",
                labelList,
                creditCardSalesData,
                boletoSalesData,
                pixSalesData
            );
        }),
        createChart(),
        $(".chart-action li a").on("click", function () {
            createChart($(this));
        });
    }

    var startDate = moment().subtract(30, "days").format("DD/MM/YYYY");
    var endDate = moment().format("DD/MM/YYYY");
    // $('input[name="daterange"]').dateRangePicker(
    //     {
    //         startDate: moment().subtract(30, "days"),
    //         endDate: moment(),
    //         opens: "left",
    //         maxDate: moment().endOf("day"),
    //         alwaysShowCalendar: true,
    //         showCustomRangeLabel: "Customizado",
    //         autoUpdateInput: true,
    //         locale: {
    //             locale: "pt-br",
    //             format: "DD/MM/YYYY",
    //             applyLabel: "Aplicar",
    //             cancelLabel: "Limpar",
    //             fromLabel: "De",
    //             toLabel: "Até",
    //             customRangeLabel: "Customizado",
    //             weekLabel: "W",
    //             daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
    //             monthNames: [
    //                 "Janeiro",
    //                 "Fevereiro",
    //                 "Março",
    //                 "Abril",
    //                 "Maio",
    //                 "Junho",
    //                 "Julho",
    //                 "Agosto",
    //                 "Setembro",
    //                 "Outubro",
    //                 "Novembro",
    //                 "Dezembro",
    //             ],
    //             firstDay: 0,
    //         },
    //         ranges: {
    //             Hoje: [moment(), moment()],
    //             Ontem: [
    //                 moment().subtract(1, "days"),
    //                 moment().subtract(1, "days"),
    //             ],
    //             "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
    //             "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
    //             "Este mês": [
    //                 moment().startOf("month"),
    //                 moment().endOf("month"),
    //             ],
    //             "Mês passado": [
    //                 moment().subtract(1, "month").startOf("month"),
    //                 moment().subtract(1, "month").endOf("month"),
    //             ],
    //         },
    //     },
    //     function (start, end) {
    //         startDate = start.format("YYYY-MM-DD");
    //         endDate = end.format("YYYY-MM-DD");
    //         $(".data-pie ul li").remove();
    //         updateReports();
    //     }
    // );
    $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
    $('input[name="daterange"]').dateRangePicker({
        setValue: function (s) {
            if (s) {
                let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                $(this).html(s).data('value', normalize);
                $('input[name="daterange"]').attr('value', normalize);
                $('input[name="daterange"]').val(normalize);
            } else {
                $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
                $('input[name="daterange"]').val(`${startDate}-${endDate}`);
            }
        }
    })
    .on('datepicker-change', function () {
        $("#card-origin .ske-load").show();
        $('.origin-report').hide();
        $.ajaxQ.abortAll();
        updateStorage({calendar: $(this).val()});
        resume();
    })
    .on('datepicker-open', function () {
        $('.filter-badge-input').removeClass('show');
    })
    .on('datepicker-close', function () {
        $(this).removeClass('focused');
        if ($(this).data('value')) {
            $(this).addClass('active');
        }
    });


    function newGraphSell(series, labels) {
        const titleTooltip = (tooltipItems) => {
            return '';
        }

        const legendMargin = {
            id: 'legendMargin',
            beforeInit(chart, legend, options) {
                const fitValue = chart.legend.fit;
                chart.legend.fit = function () {
                    fitValue.bind(chart.legend)();
                    return this.height += 20;
                }
            }
        };

        const ctx = document.getElementById('graph-sell').getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 150);
        gradient.addColorStop(0, 'rgba(76, 152,242, 0.23)');
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            const myChart = new Chart(ctx, {
                plugins: [legendMargin],
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Legenda',
                            data: series,
                            color:'#636363',
                            backgroundColor: gradient,
                            borderColor: "#2E85EC",
                            borderWidth: 4,
                            fill: true,
                            borderRadius: 4,
                            barThickness: 30,
                        }
                    ]
                },
                options: {
                    tension: 0.5,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {display: false},
                        title: {display: false},
                    },
                    responsive: true,
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                    pointBackgroundColor:"#2E85EC",
                    radius: 0.1,
                    interaction: {
                      intersect: false,
                      mode: "index",
                      borderRadius: 4,
                      usePointStyle: true,
                      yAlign: 'bottom',
                      padding: 10,
                      titleSpacing: 10,
                      callbacks: {
                          label: function (tooltipItem) {
                              return tooltipItem.raw + ' vendas';
                          },
                          labelPointStyle: function (context) {
                              return {
                                  pointStyle: 'rect',
                                  borderRadius: 4,
                                  rotatio: 0,
                              }
                          }
                      }
                    }
                  },
            });
    }

    function newGraphCashback(series, labels) {
        const titleTooltip = (tooltipItems) => {
            return '';
        }

        const legendMargin = {
            id: 'legendMargin',
            beforeInit(chart, legend, options) {
                const fitValue = chart.legend.fit;
                chart.legend.fit = function () {
                    fitValue.bind(chart.legend)();
                    return this.height += 20;
                }
            }
        };

        const ctx = document.getElementById('graph-cashback').getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 100);

        gradient.addColorStop(0, 'rgba(54,216,119,0.23)');
        gradient.addColorStop(1, 'rgba(255,255,255,0)');

            const myChart = new Chart(ctx, {
                plugins: [legendMargin],
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Legenda',
                            data: series,
                            color:'#636363',
                            backgroundColor: gradient,
                            borderColor: "#1BE4A8",
                            borderWidth: 4,
                            fill: true,
                            borderRadius: 4,
                            barThickness: 30,
                        }
                    ]
                },
                options: {
                    tension: 0.5,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {display: false},
                        title: {display: false},
                    },
                    responsive: true,
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                    pointBackgroundColor:"#1BE4A8",
                    radius: 0.1,
                    interaction: {
                      intersect: false,
                      mode: "index",
                      borderRadius: 4,
                      usePointStyle: true,
                      yAlign: 'bottom',
                      padding: 10,
                      titleSpacing: 10,
                      callbacks: {
                        label: function (tooltipItem) {
                            return convertToReal(tooltipItem);
                        },
                          labelPointStyle: function (context) {
                              return {
                                  pointStyle: 'rect',
                                  borderRadius: 4,
                                  rotatio: 0,
                              }
                          }
                      }
                    }
                  },
            });
    }
    function newGraphPending(series, labels) {
        const titleTooltip = (tooltipItems) => {
            return '';
        }

        const legendMargin = {
            id: 'legendMargin',
            beforeInit(chart, legend, options) {
                const fitValue = chart.legend.fit;
                chart.legend.fit = function () {
                    fitValue.bind(chart.legend)();
                    return this.height += 20;
                }
            }
        };

        const ctx = document.getElementById('graph-pending').getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 130);
        gradient.addColorStop(0, 'rgba(255,121,0, 0.23)');
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            const myChart = new Chart(ctx, {
                plugins: [legendMargin],
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Legenda',
                            data: series,
                            color:'#636363',
                            backgroundColor: gradient,
                            borderColor: "#FF7900",
                            borderWidth: 4,
                            fill: true,
                            borderRadius: 4,
                            barThickness: 30,
                        }
                    ]
                },
                options: {
                    tension: 0.5,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {display: false},
                        title: {display: false},
                    },
                    responsive: true,
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                    pointBackgroundColor:"#FF7900",
                    radius: 0.1,
                    interaction: {
                        intersect: false,
                        mode: "index",
                        borderRadius: 4,
                        usePointStyle: true,
                        yAlign: 'bottom',
                        padding: 10,
                        titleSpacing: 10,
                        callbacks: {
                            label: function (tooltipItem) {
                                return convertToReal(tooltipItem);
                            },
                            labelPointStyle: function (context) {
                                return {
                                    pointStyle: 'rect',
                                    borderRadius: 4,
                                    rotatio: 0,
                                }
                            }
                        }
                      }
                  },
            });

    }

    function graphComission(series, labels) {
       const titleTooltip = (tooltipItems) => {
            return '';
        }

        const legendMargin = {
            id: 'legendMargin',
            beforeInit(chart, legend, options) {
                const fitValue = chart.legend.fit;
                chart.legend.fit = function () {
                    fitValue.bind(chart.legend)();
                    return this.height += 20;
                }
            }
        };

        const ctx = document.getElementById('comission-graph').getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 150);
        gradient.addColorStop(0, 'rgba(76, 152,242, 0.2)');
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            const myChart = new Chart(ctx, {
                plugins: [legendMargin],
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Legenda',
                            data: series,
                            color:'#636363',
                            backgroundColor: gradient,
                            borderColor: "#2E85EC",
                            borderWidth: 4,
                            fill: true,
                            borderRadius: 4,
                            barThickness: 30,
                        }
                    ]
                },
                options: {
                    tension: 0.5,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {display: false},
                        title: {display: false},
                    },
                    responsive: true,
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                        },
                    },
                    pointBackgroundColor:"#2E85EC",
                    radius: 0.1,
                    interaction: {
                      intersect: false,
                      mode: "index",
                      borderRadius: 4,
                      usePointStyle: true,
                      yAlign: 'bottom',
                      padding: 10,
                      titleSpacing: 10,
                      callbacks: {
                          label: function (tooltipItem) {
                              return convertToReal(tooltipItem);
                          },
                          labelPointStyle: function (context) {
                              return {
                                  pointStyle: 'rect',
                                  borderRadius: 4,
                                  rotatio: 0,
                              }
                          }
                      }
                    }
                  },
            });
    }


    function graphRegions(labels, conversion, access) {

        const ctx = document.getElementById('regionsChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: '',
                        data: conversion,
                        color:'#636363',
                        backgroundColor: [
                            'rgba(46, 133, 236, 1)',
                            'rgba(102, 95, 232, 1)',
                            'rgba(244, 63, 94, 1)',
                            'rgba(255, 121, 0, 1)',
                        ],
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.9
                    },
                    {
                        label: '',
                        data: access,
                        color:'#636363',
                        backgroundColor: [
                            'rgba(46, 133, 236, .2)',
                            'rgba(102, 95, 232, .2)',
                            'rgba(244, 63, 94, .2)',
                            'rgba(255, 121, 0, .2)',
                        ],
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.9 
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {display: false},
                    title: {display: false},
                },

                responsive: true,
                scales: {
                    x: {
                        display: false,
                    },
                    y: {
                        stacked: true,
                        grid: {
                            color: '#ECE9F1',
                            drawBorder: false,
                            display: false
                        },
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            padding: -4,
                            mirror: true,
                            stepSize: 0,
                            font: {
                                family: 'Muli',
                                size: 12,
                            },
                            // color: "#ff0000",
                            callback: function(value, index){
                                return this.getLabelForValue(value);
                            }
                        }
                    }
                },
            }
        });
        
            

    }

    function convertToReal(tooltipItem) {
        let tooltipValue = tooltipItem.raw;
            tooltipValue = tooltipValue + '';
            tooltipValue = parseInt(tooltipValue.replace(/[\D]+/g, ''));
            tooltipValue = tooltipValue + '';
            tooltipValue = tooltipValue.replace(/([0-9]{2})$/g, ",$1");

            if (tooltipValue.length > 6) {
                tooltipValue = tooltipValue.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
            }

            return 'R$ ' + tooltipValue;
    }

    function getInfo() {
        $('.box-link').on('click', function(e) {
            let calendar = $('input[name=daterange]').val();
            let company = $('#select_projects').val();
            let companyName = $('#select_projects').find('option:selected').text();

            let obj = {
                calendar,
                company,
                companyName
            }
            sessionStorage.setItem('info', JSON.stringify(obj));
        });
    }

    function updateStorage(v){
        var existing = sessionStorage.getItem('info');
        existing = existing ? JSON.parse(existing) : {};
        Object.keys(v).forEach(function(val, key){
            existing[val] = v[val];
       })
        sessionStorage.setItem('info', JSON.stringify(existing));
    }

    function verifyUserHasStore() {
        $.ajax({
            method: 'GET',
            url: '/api/core/verify-account/' + $('meta[name="user-id"]').attr('content'),
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                if (response.data.account.status !== 'approved') {
                    $("#box-projects").hide();
                }
            },
        });
    }

    // abort all ajax
    $.ajaxQ = (function(){
        var id = 0, Q = {};

        $(document).ajaxSend(function(e, jqx){
          jqx._id = ++id;
          Q[jqx._id] = jqx;
        });
        $(document).ajaxComplete(function(e, jqx){
          delete Q[jqx._id];
        });

        return {
          abortAll: function(){
            var r = [];
            $.each(Q, function(i, jqx){
              r.push(jqx._id);
              jqx.abort();
            });
            return r;
          }
        };

      })();

});

let skeLoad = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text"></div>
        </div>
    </div>
`;

let emptyGraph = `
<svg width="393" height="106" viewBox="0 0 393 106" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M340.875 70.41C361.5 73.8549 375.714 94.3495 393 97.7104V106H3.05176e-05V55.9893C19.2434 55.9893 21.8113 71.2636 30.867 67.0589C39.9227 62.8542 49.4351 33.9447 60 35.4737C70.565 37.0027 76.3631 -3.56824 98.625 0.254224C120.887 4.07668 137.271 47.7352 155.005 29.0051C172.739 10.2751 176.509 20.1579 195.375 16.3355C214.241 12.513 222.506 58.3041 240.375 56.8408C258.244 55.3775 263.014 93.4635 281.25 89.931C303.348 85.6503 301.424 83.2526 311.25 77.7C321.076 72.1474 329.882 68.5739 340.875 70.41Z" fill="url(#paint0_linear_1640_460)" fill-opacity="0.5"/>
<defs>
<linearGradient id="paint0_linear_1640_460" x1="196.5" y1="0" x2="196.5" y2="150.34" gradientUnits="userSpaceOnUse">
<stop stop-color="#E8EDFA"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let emptyCoupons = `
<svg width="132" height="126" viewBox="0 0 132 126" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M123.632 78.3097C120.484 90.0588 113.922 100.611 104.777 108.63C95.6322 116.65 84.3144 121.779 72.2549 123.366C60.1954 124.954 47.9359 122.93 37.0268 117.55C26.1176 112.17 17.0487 103.676 10.9669 93.1423L32.2712 80.8423C35.9202 87.1627 41.3616 92.2591 47.9071 95.4869C54.4526 98.7148 61.8082 99.9293 69.0439 98.9767C76.2796 98.0241 83.0703 94.9472 88.5574 90.1352C94.0444 85.3232 97.9813 78.9922 99.8702 71.9428L123.632 78.3097Z" fill="#F4F6FB" fill-opacity="0.8"/>
<path d="M64.2275 0.892341C77.7658 0.892339 90.9257 5.35954 101.666 13.6011C112.407 21.8427 120.128 33.398 123.632 46.475L99.8702 52.8419C97.7678 44.9958 93.1352 38.0625 86.6908 33.1176C80.2464 28.1727 72.3505 25.4923 64.2275 25.4923L64.2275 0.892341Z" fill="#F4F6FB" fill-opacity="0.25"/>
<path d="M123.632 46.475C126.426 56.9026 126.426 67.8821 123.632 78.3097L99.8702 71.9428C101.547 65.6862 101.547 59.0985 99.8702 52.8419L123.632 46.475Z" fill="#E8EAEB"/>
<path d="M10.9669 93.1423C5.56919 83.7932 2.72751 73.1878 2.72751 62.3923C2.72751 51.5968 5.5692 40.9915 10.9669 31.6423C16.3647 22.2932 24.1283 14.5295 33.4775 9.13177C42.8267 3.73402 53.432 0.892339 64.2275 0.892341L64.2275 25.4923C57.7502 25.4923 51.387 27.1974 45.7775 30.436C40.168 33.6747 35.5098 38.3328 32.2712 43.9423C29.0325 49.5518 27.3275 55.915 27.3275 62.3923C27.3275 68.8696 29.0325 75.2328 32.2712 80.8423L10.9669 93.1423Z" fill="#F4F6FB" fill-opacity="0.6"/>
</svg>
`;

let emptyProducts = `
<svg width="267" height="130" viewBox="0 0 267 130" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="40" y="69" width="187" height="25" rx="4" fill="url(#paint0_linear_2696_524)"/>
<rect x="40" width="227" height="25" rx="4" fill="url(#paint1_linear_2696_524)"/>
<rect x="40" y="104" width="121" height="24" rx="4" fill="url(#paint2_linear_2696_524)"/>
<rect x="40" y="35" width="227" height="24" rx="4" fill="url(#paint3_linear_2696_524)"/>
<path opacity="0.6" d="M2.46885 70.3035C2.7865 70.1718 3.12481 70.0798 3.47756 70.0338L3.54224 70.5296C3.69184 70.5101 3.84461 70.5 4 70.5L4.5 70.5L4.5 70L5.5 70L5.5 70.5L6.5 70.5L6.5 70L7.5 70L7.5 70.5L8.5 70.5L8.5 70L9.5 70L9.5 70.5L10.5 70.5L10.5 70L11.5 70L11.5 70.5L12.5 70.5L12.5 70L13.5 70L13.5 70.5L14.5 70.5L14.5 70L15.5 70L15.5 70.5L16.5 70.5L16.5 70L17.5 70L17.5 70.5L18.5 70.5L18.5 70L19.5 70L19.5 70.5L20 70.5C20.1554 70.5 20.3082 70.5101 20.4578 70.5296L20.5224 70.0338C20.8752 70.0798 21.2135 70.1718 21.5312 70.3035L21.3396 70.7654C21.6239 70.8833 21.8895 71.0377 22.1305 71.2229L22.4351 70.8264C22.7122 71.0393 22.9607 71.2878 23.1736 71.5649L22.7771 71.8695C22.9623 72.1105 23.1167 72.3761 23.2346 72.6604L23.6965 72.4688C23.8282 72.7865 23.9202 73.1248 23.9662 73.4776L23.4704 73.5422C23.4899 73.6918 23.5 73.8446 23.5 74L23.5 74.523L24 74.523L24 75.569L23.5 75.569L23.5 76.615L24 76.615L24 77.661L23.5 77.661L23.5 78.707L24 78.707L24 79.753L23.5 79.753L23.5 80.799L24 80.799L24 81.845L23.5 81.845L23.5 82.891L24 82.891L24 83.937L23.5 83.937L23.5 84.983L24 84.983L24 86.029L23.5 86.029L23.5 87.075L24 87.075L24 88.121L23.5 88.121L23.5 89.167L24 89.167L24 90.2129L23.5 90.2129L23.5 90.7359C23.5 90.8913 23.4899 91.0441 23.4704 91.1937L23.9662 91.2584C23.9202 91.6111 23.8282 91.9494 23.6965 92.2671L23.2346 92.0756C23.1167 92.3598 22.9623 92.6254 22.7771 92.8664L23.1736 93.1711C22.9607 93.4482 22.7122 93.6966 22.4351 93.9095L22.1305 93.5131C21.8895 93.6983 21.6239 93.8527 21.3396 93.9706L21.5312 94.4324C21.2135 94.5641 20.8752 94.6561 20.5224 94.7021L20.4578 94.2063C20.3082 94.2258 20.1554 94.2359 20 94.2359L19.5 94.2359L19.5 94.7359L18.5 94.7359L18.5 94.2359L17.5 94.2359L17.5 94.7359L16.5 94.7359L16.5 94.2359L15.5 94.2359L15.5 94.7359L14.5 94.7359L14.5 94.2359L13.5 94.2359L13.5 94.7359L12.5 94.7359L12.5 94.2359L11.5 94.2359L11.5 94.7359L10.5 94.7359L10.5 94.2359L9.5 94.2359L9.5 94.7359L8.5 94.7359L8.5 94.2359L7.5 94.2359L7.5 94.7359L6.5 94.7359L6.5 94.2359L5.5 94.2359L5.5 94.7359L4.5 94.7359L4.5 94.2359L4 94.2359C3.84461 94.2359 3.69183 94.2258 3.54224 94.2063L3.47756 94.7021C3.12481 94.6561 2.7865 94.5641 2.46885 94.4324L2.66037 93.9706C2.37611 93.8527 2.11053 93.6983 1.86953 93.5131L1.56486 93.9095C1.28776 93.6966 1.03934 93.4482 0.826402 93.1711L1.22286 92.8664C1.03766 92.6254 0.883266 92.3598 0.765384 92.0756L0.303521 92.2671C0.171794 91.9494 0.0798216 91.6111 0.0338088 91.2584L0.529609 91.1937C0.510095 91.0441 0.499999 90.8913 0.499999 90.7359L0.499999 90.2129L-8.57249e-07 90.2129L-8.12887e-07 89.1669L0.499999 89.1669L0.499999 88.121L-7.68526e-07 88.121L-7.24164e-07 87.075L0.499999 87.075L0.499999 86.029L-6.79802e-07 86.029L-6.35441e-07 84.983L0.499999 84.983L0.499999 83.937L-5.91079e-07 83.937L-5.46717e-07 82.891L0.499999 82.891L0.499999 81.845L-5.02356e-07 81.845L-4.57994e-07 80.799L0.5 80.799L0.5 79.753L-4.13632e-07 79.753L-3.69271e-07 78.707L0.5 78.707L0.5 77.661L-3.24909e-07 77.661L-2.80548e-07 76.615L0.5 76.615L0.5 75.569L-2.36186e-07 75.569L-1.91824e-07 74.523L0.5 74.523L0.5 74C0.5 73.8446 0.510095 73.6918 0.52961 73.5422L0.0338095 73.4776C0.0798224 73.1248 0.171795 72.7865 0.303522 72.4688L0.765385 72.6604C0.883266 72.3761 1.03766 72.1105 1.22286 71.8695L0.826403 71.5649C1.03934 71.2878 1.28776 71.0393 1.56486 70.8264L1.86953 71.2229C2.11053 71.0377 2.37611 70.8833 2.66038 70.7654L2.46885 70.3035Z" stroke="#CCCCCC" stroke-dasharray="1 1"/>
<path opacity="0.6" d="M2.46885 0.303521C2.7865 0.171794 3.12481 0.0798215 3.47756 0.0338082L3.54224 0.529608C3.69184 0.510094 3.84461 0.499999 4 0.499999L4.5 0.499999L4.5 -8.7851e-07L5.5 -8.33458e-07L5.5 0.499999L6.5 0.499999L6.5 -7.88406e-07L7.5 -7.43354e-07L7.5 0.499999L8.5 0.499999L8.5 -6.98302e-07L9.5 -6.53251e-07L9.5 0.499999L10.5 0.499999L10.5 -6.08199e-07L11.5 -5.63147e-07L11.5 0.499999L12.5 0.499999L12.5 -5.18095e-07L13.5 -4.73044e-07L13.5 0.5L14.5 0.5L14.5 -4.27992e-07L15.5 -3.8294e-07L15.5 0.5L16.5 0.5L16.5 -3.37888e-07L17.5 -2.92837e-07L17.5 0.5L18.5 0.5L18.5 -2.47785e-07L19.5 -2.02733e-07L19.5 0.5L20 0.5C20.1554 0.5 20.3082 0.510095 20.4578 0.529609L20.5224 0.033809C20.8752 0.0798224 21.2135 0.171795 21.5312 0.303522L21.3396 0.765385C21.6239 0.883267 21.8895 1.03766 22.1305 1.22286L22.4351 0.826402C22.7122 1.03934 22.9607 1.28776 23.1736 1.56486L22.7771 1.86953C22.9623 2.11053 23.1167 2.37611 23.2346 2.66038L23.6965 2.46885C23.8282 2.7865 23.9202 3.12481 23.9662 3.47756L23.4704 3.54224C23.4899 3.69183 23.5 3.84461 23.5 4L23.5 4.523L24 4.523L24 5.569L23.5 5.569L23.5 6.61499L24 6.61499L24 7.66099L23.5 7.66099L23.5 8.70699L24 8.70699L24 9.75298L23.5 9.75298L23.5 10.799L24 10.799L24 11.845L23.5 11.845L23.5 12.891L24 12.891L24 13.937L23.5 13.937L23.5 14.983L24 14.983L24 16.029L23.5 16.029L23.5 17.075L24 17.075L24 18.121L23.5 18.121L23.5 19.167L24 19.167L24 20.2129L23.5 20.2129L23.5 20.7359C23.5 20.8913 23.4899 21.0441 23.4704 21.1937L23.9662 21.2584C23.9202 21.6111 23.8282 21.9494 23.6965 22.2671L23.2346 22.0756C23.1167 22.3598 22.9623 22.6254 22.7771 22.8664L23.1736 23.1711C22.9607 23.4482 22.7122 23.6966 22.4351 23.9095L22.1305 23.5131C21.8895 23.6983 21.6239 23.8527 21.3396 23.9706L21.5312 24.4324C21.2135 24.5641 20.8752 24.6561 20.5224 24.7021L20.4578 24.2063C20.3082 24.2258 20.1554 24.2359 20 24.2359L19.5 24.2359L19.5 24.7359L18.5 24.7359L18.5 24.2359L17.5 24.2359L17.5 24.7359L16.5 24.7359L16.5 24.2359L15.5 24.2359L15.5 24.7359L14.5 24.7359L14.5 24.2359L13.5 24.2359L13.5 24.7359L12.5 24.7359L12.5 24.2359L11.5 24.2359L11.5 24.7359L10.5 24.7359L10.5 24.2359L9.5 24.2359L9.5 24.7359L8.5 24.7359L8.5 24.2359L7.5 24.2359L7.5 24.7359L6.5 24.7359L6.5 24.2359L5.5 24.2359L5.5 24.7359L4.5 24.7359L4.5 24.2359L4 24.2359C3.84461 24.2359 3.69183 24.2258 3.54224 24.2063L3.47756 24.7021C3.12481 24.6561 2.7865 24.5641 2.46885 24.4324L2.66037 23.9706C2.37611 23.8527 2.11053 23.6983 1.86953 23.5131L1.56486 23.9095C1.28776 23.6966 1.03934 23.4482 0.826402 23.1711L1.22286 22.8664C1.03766 22.6254 0.883266 22.3598 0.765384 22.0756L0.303521 22.2671C0.171794 21.9494 0.0798216 21.6111 0.0338088 21.2584L0.529609 21.1937C0.510095 21.0441 0.499999 20.8913 0.499999 20.7359L0.499999 20.2129L-8.57249e-07 20.2129L-8.12887e-07 19.1669L0.499999 19.1669L0.499999 18.121L-7.68526e-07 18.121L-7.24164e-07 17.075L0.499999 17.075L0.499999 16.029L-6.79802e-07 16.029L-6.35441e-07 14.983L0.499999 14.983L0.499999 13.937L-5.91079e-07 13.937L-5.46717e-07 12.891L0.499999 12.891L0.499999 11.845L-5.02356e-07 11.845L-4.57994e-07 10.799L0.5 10.799L0.5 9.75298L-4.13632e-07 9.75298L-3.69271e-07 8.70698L0.5 8.70698L0.5 7.66098L-3.24909e-07 7.66098L-2.80548e-07 6.61499L0.5 6.61499L0.5 5.56899L-2.36186e-07 5.56899L-1.91824e-07 4.52299L0.5 4.52299L0.5 4C0.5 3.84461 0.510095 3.69183 0.52961 3.54223L0.0338095 3.47756C0.0798224 3.12481 0.171795 2.7865 0.303522 2.46885L0.765385 2.66037C0.883266 2.3761 1.03766 2.11052 1.22286 1.86952L0.826403 1.56486C1.03934 1.28776 1.28776 1.03934 1.56486 0.8264L1.86953 1.22286C2.11053 1.03766 2.37611 0.883266 2.66038 0.765384L2.46885 0.303521Z" stroke="#CCCCCC" stroke-dasharray="1 1"/>
<path opacity="0.6" d="M2.46885 105.304C2.7865 105.172 3.12481 105.08 3.47756 105.034L3.54224 105.53C3.69184 105.51 3.84461 105.5 4 105.5L4.5 105.5L4.5 105L5.5 105L5.5 105.5L6.5 105.5L6.5 105L7.5 105L7.5 105.5L8.5 105.5L8.5 105L9.5 105L9.5 105.5L10.5 105.5L10.5 105L11.5 105L11.5 105.5L12.5 105.5L12.5 105L13.5 105L13.5 105.5L14.5 105.5L14.5 105L15.5 105L15.5 105.5L16.5 105.5L16.5 105L17.5 105L17.5 105.5L18.5 105.5L18.5 105L19.5 105L19.5 105.5L20 105.5C20.1554 105.5 20.3082 105.51 20.4578 105.53L20.5224 105.034C20.8752 105.08 21.2135 105.172 21.5312 105.304L21.3396 105.765C21.6239 105.883 21.8895 106.038 22.1305 106.223L22.4351 105.826C22.7122 106.039 22.9607 106.288 23.1736 106.565L22.7771 106.87C22.9623 107.111 23.1167 107.376 23.2346 107.66L23.6965 107.469C23.8282 107.787 23.9202 108.125 23.9662 108.478L23.4704 108.542C23.4899 108.692 23.5 108.845 23.5 109L23.5 109.523L24 109.523L24 110.569L23.5 110.569L23.5 111.615L24 111.615L24 112.661L23.5 112.661L23.5 113.707L24 113.707L24 114.753L23.5 114.753L23.5 115.799L24 115.799L24 116.845L23.5 116.845L23.5 117.891L24 117.891L24 118.937L23.5 118.937L23.5 119.983L24 119.983L24 121.029L23.5 121.029L23.5 122.075L24 122.075L24 123.121L23.5 123.121L23.5 124.167L24 124.167L24 125.213L23.5 125.213L23.5 125.736C23.5 125.891 23.4899 126.044 23.4704 126.194L23.9662 126.258C23.9202 126.611 23.8282 126.949 23.6965 127.267L23.2346 127.076C23.1167 127.36 22.9623 127.625 22.7771 127.866L23.1736 128.171C22.9607 128.448 22.7122 128.697 22.4351 128.91L22.1305 128.513C21.8895 128.698 21.6239 128.853 21.3396 128.971L21.5312 129.432C21.2135 129.564 20.8752 129.656 20.5224 129.702L20.4578 129.206C20.3082 129.226 20.1554 129.236 20 129.236L19.5 129.236L19.5 129.736L18.5 129.736L18.5 129.236L17.5 129.236L17.5 129.736L16.5 129.736L16.5 129.236L15.5 129.236L15.5 129.736L14.5 129.736L14.5 129.236L13.5 129.236L13.5 129.736L12.5 129.736L12.5 129.236L11.5 129.236L11.5 129.736L10.5 129.736L10.5 129.236L9.5 129.236L9.5 129.736L8.5 129.736L8.5 129.236L7.5 129.236L7.5 129.736L6.5 129.736L6.5 129.236L5.5 129.236L5.5 129.736L4.5 129.736L4.5 129.236L4 129.236C3.84461 129.236 3.69183 129.226 3.54224 129.206L3.47756 129.702C3.12481 129.656 2.7865 129.564 2.46885 129.432L2.66037 128.971C2.37611 128.853 2.11053 128.698 1.86953 128.513L1.56486 128.91C1.28776 128.697 1.03934 128.448 0.826402 128.171L1.22286 127.866C1.03766 127.625 0.883266 127.36 0.765384 127.076L0.303521 127.267C0.171794 126.949 0.0798216 126.611 0.0338088 126.258L0.529609 126.194C0.510095 126.044 0.499999 125.891 0.499999 125.736L0.499999 125.213L-8.57249e-07 125.213L-8.12887e-07 124.167L0.499999 124.167L0.499999 123.121L-7.68526e-07 123.121L-7.24164e-07 122.075L0.499999 122.075L0.499999 121.029L-6.79802e-07 121.029L-6.35441e-07 119.983L0.499999 119.983L0.499999 118.937L-5.91079e-07 118.937L-5.46717e-07 117.891L0.499999 117.891L0.499999 116.845L-5.02356e-07 116.845L-4.57994e-07 115.799L0.5 115.799L0.5 114.753L-4.13632e-07 114.753L-3.69271e-07 113.707L0.5 113.707L0.5 112.661L-3.24909e-07 112.661L-2.80548e-07 111.615L0.5 111.615L0.5 110.569L-2.36186e-07 110.569L-1.91824e-07 109.523L0.5 109.523L0.5 109C0.5 108.845 0.510095 108.692 0.52961 108.542L0.0338095 108.478C0.0798224 108.125 0.171795 107.787 0.303522 107.469L0.765385 107.66C0.883266 107.376 1.03766 107.111 1.22286 106.87L0.826403 106.565C1.03934 106.288 1.28776 106.039 1.56486 105.826L1.86953 106.223C2.11053 106.038 2.37611 105.883 2.66038 105.765L2.46885 105.304Z" stroke="#CCCCCC" stroke-dasharray="1 1"/>
<path opacity="0.6" d="M2.46885 35.3035C2.7865 35.1718 3.12481 35.0798 3.47756 35.0338L3.54224 35.5296C3.69184 35.5101 3.84461 35.5 4 35.5L4.5 35.5L4.5 35L5.5 35L5.5 35.5L6.5 35.5L6.5 35L7.5 35L7.5 35.5L8.5 35.5L8.5 35L9.5 35L9.5 35.5L10.5 35.5L10.5 35L11.5 35L11.5 35.5L12.5 35.5L12.5 35L13.5 35L13.5 35.5L14.5 35.5L14.5 35L15.5 35L15.5 35.5L16.5 35.5L16.5 35L17.5 35L17.5 35.5L18.5 35.5L18.5 35L19.5 35L19.5 35.5L20 35.5C20.1554 35.5 20.3082 35.5101 20.4578 35.5296L20.5224 35.0338C20.8752 35.0798 21.2135 35.1718 21.5312 35.3035L21.3396 35.7654C21.6239 35.8833 21.8895 36.0377 22.1305 36.2229L22.4351 35.8264C22.7122 36.0393 22.9607 36.2878 23.1736 36.5649L22.7771 36.8695C22.9623 37.1105 23.1167 37.3761 23.2346 37.6604L23.6965 37.4688C23.8282 37.7865 23.9202 38.1248 23.9662 38.4776L23.4704 38.5422C23.4899 38.6918 23.5 38.8446 23.5 39L23.5 39.523L24 39.523L24 40.569L23.5 40.569L23.5 41.615L24 41.615L24 42.661L23.5 42.661L23.5 43.707L24 43.707L24 44.753L23.5 44.753L23.5 45.799L24 45.799L24 46.845L23.5 46.845L23.5 47.891L24 47.891L24 48.937L23.5 48.937L23.5 49.983L24 49.983L24 51.029L23.5 51.029L23.5 52.075L24 52.075L24 53.121L23.5 53.121L23.5 54.167L24 54.167L24 55.2129L23.5 55.2129L23.5 55.7359C23.5 55.8913 23.4899 56.0441 23.4704 56.1937L23.9662 56.2584C23.9202 56.6111 23.8282 56.9494 23.6965 57.2671L23.2346 57.0756C23.1167 57.3598 22.9623 57.6254 22.7771 57.8664L23.1736 58.1711C22.9607 58.4482 22.7122 58.6966 22.4351 58.9095L22.1305 58.5131C21.8895 58.6983 21.6239 58.8527 21.3396 58.9706L21.5312 59.4324C21.2135 59.5641 20.8752 59.6561 20.5224 59.7021L20.4578 59.2063C20.3082 59.2258 20.1554 59.2359 20 59.2359L19.5 59.2359L19.5 59.7359L18.5 59.7359L18.5 59.2359L17.5 59.2359L17.5 59.7359L16.5 59.7359L16.5 59.2359L15.5 59.2359L15.5 59.7359L14.5 59.7359L14.5 59.2359L13.5 59.2359L13.5 59.7359L12.5 59.7359L12.5 59.2359L11.5 59.2359L11.5 59.7359L10.5 59.7359L10.5 59.2359L9.5 59.2359L9.5 59.7359L8.5 59.7359L8.5 59.2359L7.5 59.2359L7.5 59.7359L6.5 59.7359L6.5 59.2359L5.5 59.2359L5.5 59.7359L4.5 59.7359L4.5 59.2359L4 59.2359C3.84461 59.2359 3.69183 59.2258 3.54224 59.2063L3.47756 59.7021C3.12481 59.6561 2.7865 59.5641 2.46885 59.4324L2.66037 58.9706C2.37611 58.8527 2.11053 58.6983 1.86953 58.5131L1.56486 58.9095C1.28776 58.6966 1.03934 58.4482 0.826402 58.1711L1.22286 57.8664C1.03766 57.6254 0.883266 57.3598 0.765384 57.0756L0.303521 57.2671C0.171794 56.9494 0.0798216 56.6111 0.0338088 56.2584L0.529609 56.1937C0.510095 56.0441 0.499999 55.8913 0.499999 55.7359L0.499999 55.2129L-8.57249e-07 55.2129L-8.12887e-07 54.1669L0.499999 54.1669L0.499999 53.121L-7.68526e-07 53.121L-7.24164e-07 52.075L0.499999 52.075L0.499999 51.029L-6.79802e-07 51.029L-6.35441e-07 49.983L0.499999 49.983L0.499999 48.937L-5.91079e-07 48.937L-5.46717e-07 47.891L0.499999 47.891L0.499999 46.845L-5.02356e-07 46.845L-4.57994e-07 45.799L0.5 45.799L0.5 44.753L-4.13632e-07 44.753L-3.69271e-07 43.707L0.5 43.707L0.5 42.661L-3.24909e-07 42.661L-2.80548e-07 41.615L0.5 41.615L0.5 40.569L-2.36186e-07 40.569L-1.91824e-07 39.523L0.5 39.523L0.5 39C0.5 38.8446 0.510095 38.6918 0.52961 38.5422L0.0338095 38.4776C0.0798224 38.1248 0.171795 37.7865 0.303522 37.4688L0.765385 37.6604C0.883266 37.3761 1.03766 37.1105 1.22286 36.8695L0.826403 36.5649C1.03934 36.2878 1.28776 36.0393 1.56486 35.8264L1.86953 36.2229C2.11053 36.0377 2.37611 35.8833 2.66038 35.7654L2.46885 35.3035Z" stroke="#CCCCCC" stroke-dasharray="1 1"/>
<defs>
<linearGradient id="paint0_linear_2696_524" x1="133.5" y1="69" x2="227" y2="69" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint1_linear_2696_524" x1="153.5" y1="-3.9596e-06" x2="267" y2="-1.05574e-05" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint2_linear_2696_524" x1="54.5" y1="128" x2="148" y2="128" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint3_linear_2696_524" x1="67.2025" y1="59" x2="242.612" y2="58.9999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let noListProducts = `
<svg width="200" height="90" viewBox="0 0 275 122" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="48" y="94" width="187" height="25" rx="4" fill="url(#paint0_linear_2696_543)"/>
<rect x="48" y="5" width="227" height="25" rx="4" fill="url(#paint1_linear_2696_543)"/>
<rect x="48" y="50" width="227" height="24" rx="4" fill="url(#paint2_linear_2696_543)"/>
<path opacity="0.6" d="M29 89C31.2091 89 33 90.7909 33 93L33 117C33 119.209 31.2091 121 29 121L5 121C2.79086 121 0.999999 119.209 0.999999 117L1 93C1 90.7909 2.79086 89 5 89L29 89Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 1C31.2091 1 33 2.79086 33 5L33 29C33 31.2091 31.2091 33 29 33L5 33C2.79086 33 0.999999 31.2091 0.999999 29L1 5C1 2.79086 2.79086 0.999999 5 0.999999L29 1Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 45C31.2091 45 33 46.7909 33 49L33 73C33 75.2091 31.2091 77 29 77L5 77C2.79086 77 0.999999 75.2091 0.999999 73L1 49C1 46.7909 2.79086 45 5 45L29 45Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<defs>
<linearGradient id="paint0_linear_2696_543" x1="141.5" y1="94" x2="235" y2="94" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint1_linear_2696_543" x1="161.5" y1="5" x2="275" y2="4.99999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint2_linear_2696_543" x1="75.2025" y1="74" x2="250.612" y2="73.9999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

let noWithdrawal = `
<svg width="111" height="138" viewBox="0 0 111 138" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M55.5 132C86.1518 132 111 107.152 111 76.5C111 45.8482 86.1518 21 55.5 21C24.8482 21 0 45.8482 0 76.5C0 107.152 24.8482 132 55.5 132Z" fill="#FAFAFA"/>
<path d="M87.32 52.8199H23.68C21.6365 52.8199 19.98 54.4765 19.98 56.5199V134.22C19.98 136.263 21.6365 137.92 23.68 137.92H87.32C89.3634 137.92 91.02 136.263 91.02 134.22V56.5199C91.02 54.4765 89.3634 52.8199 87.32 52.8199Z" fill="white"/>
<path d="M48.0999 63.9199H28.8599C27.6338 63.9199 26.6399 64.9138 26.6399 66.1399C26.6399 67.3659 27.6338 68.3599 28.8599 68.3599H48.0999C49.326 68.3599 50.3199 67.3659 50.3199 66.1399C50.3199 64.9138 49.326 63.9199 48.0999 63.9199Z" fill="#B4DAFF"/>
<path d="M61.4199 73.5397H28.8599C27.6338 73.5397 26.6399 74.5336 26.6399 75.7597C26.6399 76.9857 27.6338 77.9797 28.8599 77.9797H61.4199C62.646 77.9797 63.6399 76.9857 63.6399 75.7597C63.6399 74.5336 62.646 73.5397 61.4199 73.5397Z" fill="#DFEAFB"/>
<path d="M48.0999 83.8999H28.8599C27.6338 83.8999 26.6399 84.8938 26.6399 86.1199C26.6399 87.346 27.6338 88.3399 28.8599 88.3399H48.0999C49.326 88.3399 50.3199 87.346 50.3199 86.1199C50.3199 84.8938 49.326 83.8999 48.0999 83.8999Z" fill="#B4DAFF"/>
<path d="M61.4199 93.5199H28.8599C27.6338 93.5199 26.6399 94.5138 26.6399 95.7399C26.6399 96.966 27.6338 97.9599 28.8599 97.9599H61.4199C62.646 97.9599 63.6399 96.966 63.6399 95.7399C63.6399 94.5138 62.646 93.5199 61.4199 93.5199Z" fill="#DFEAFB"/>
<path d="M48.0999 103.88H28.8599C27.6338 103.88 26.6399 104.874 26.6399 106.1C26.6399 107.326 27.6338 108.32 28.8599 108.32H48.0999C49.326 108.32 50.3199 107.326 50.3199 106.1C50.3199 104.874 49.326 103.88 48.0999 103.88Z" fill="#B4DAFF"/>
<path d="M61.4199 113.5H28.8599C27.6338 113.5 26.6399 114.494 26.6399 115.72C26.6399 116.946 27.6338 117.94 28.8599 117.94H61.4199C62.646 117.94 63.6399 116.946 63.6399 115.72C63.6399 114.494 62.646 113.5 61.4199 113.5Z" fill="#DFEAFB"/>
<g filter="url(#filter0_d_1640_468)">
<path d="M87.32 15.08H23.68C21.6365 15.08 19.98 16.7365 19.98 18.78V40.98C19.98 43.0235 21.6365 44.68 23.68 44.68H87.32C89.3634 44.68 91.02 43.0235 91.02 40.98V18.78C91.02 16.7365 89.3634 15.08 87.32 15.08Z" fill="#1485FD"/>
</g>
<path d="M48.0999 23.2201H28.8599C27.6338 23.2201 26.6399 24.214 26.6399 25.4401C26.6399 26.6661 27.6338 27.6601 28.8599 27.6601H48.0999C49.326 27.6601 50.3199 26.6661 50.3199 25.4401C50.3199 24.214 49.326 23.2201 48.0999 23.2201Z" fill="#B4DAFF"/>
<path d="M61.4199 32.8401H28.8599C27.6338 32.8401 26.6399 33.834 26.6399 35.0601C26.6399 36.2862 27.6338 37.2801 28.8599 37.2801H61.4199C62.646 37.2801 63.6399 36.2862 63.6399 35.0601C63.6399 33.834 62.646 32.8401 61.4199 32.8401Z" fill="white"/>
<defs>
<filter id="filter0_d_1640_468" x="1.78146" y="0.521187" width="107.437" height="65.997" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
<feFlood flood-opacity="0" result="BackgroundImageFix"/>
<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
<feOffset dy="3.6397"/>
<feGaussianBlur stdDeviation="9.09926"/>
<feComposite in2="hardAlpha" operator="out"/>
<feColorMatrix type="matrix" values="0 0 0 0 0.180392 0 0 0 0 0.521569 0 0 0 0 0.92549 0 0 0 0.17 0"/>
<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1640_468"/>
<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1640_468" result="shape"/>
</filter>
</defs>
</svg>
`;
