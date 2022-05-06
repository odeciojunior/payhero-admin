$(function () {
    loadingOnScreen();

    
    distributionGraphSeller();
    getInfo();

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
                    $('.new-graph-cashback').width($("#block-cash").width() + 6);

                    $('.new-graph-cashback').html(`<canvas id="graph-cashback"></canvas>`);
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphCashback(series, labels);
                    
                    $(window).on("resize", function() {
                        $('.new-graph-cashback').width($("#block-cash").width() + 6);
                    });
                    
                } else {
                    cashHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='cashback' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
                    $('.new-graph-pending').width($('#block-pending').width() + 8);
                    $('.new-graph-pending').html('<canvas id=graph-pending></canvas>')
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphPending(series,labels);

                    $(window).on("resize", function() {
                        $('.new-graph-pending').width($('#block-pending').width() + 8);
                    });
                    
                } else {
                    pendHtml = `
                        <div class="container d-flex value-price">                            
                            <h4 id='pending' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
                    $('.new-graph').width($("#block-comission").width() + 8);
                    $('.new-graph').html('<canvas id=comission-graph></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    graphComission(series, labels);

                    $(window).on("resize", function() {
                        $('.new-graph').width($('#block-comission').width() + 8);
                    });

                } else {
                    comissionhtml = `
                        <div class="container d-flex value-price">                            
                            <h4 id='comission' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
                    $('.new-graph-sell').width($('#block-sales').width() + 8);
                    $('.new-graph-sell').html('<canvas id=graph-sell></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphSell(series, labels);

                    $(window).on("resize", function() {
                        $('.new-graph-sell').width($('#block-sales').width() + 8);
                    });

                }else {
                    salesHtml = `
                        <div class="container d-flex value-price">                            
                            <h4 id='sales' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
                                            <img class="photo" src="${image}" width="24px" height="24px" />
                                        </figure>
                                        <div class="bars ${color}" style="width:${(( 100 * amount ) / total).toFixed(1)}%">
                                            <span>${(( 100 * amount ) / total) > 9 ? amount : ''}</span>
                                        </div>
                                        <span style="color: #636363;">${(( 100 * amount ) / total) > 9 ? '' : amount}</span>
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
                        }
                    });
                }else {
                    lista = `
                        <div class="container d-flex value-price">                            
                            <h4 id='products' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
                        <div class="container d-flex value-price" style="visibility: hidden;">
                            <h4 id="qtd-dispute" class="font-size-24 bold">0</h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
                                        <div class="col-payment grey box-image-payment">
                                            <div class="box-ico">
                                                <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"/>
                                                </svg>
                                            </div>Cartão
                                        </div>
                                        
                                        <div class="box-payment-option option">
                                            <div class="col-payment grey" id='percent-credit-card'>${credit_card.percentage}</div>
                                            <div class="col-payment col-graph">
                                                <div class="bar blue" style="width: ${credit_card.percentage};">-</div>
                                            </div>
                                            <div class="col-payment end"><span class="money-td green bold grey" id='credit-card-value'>${credit_card.value}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="data-holder b-bottom">
                                    <div class="box-payment-option">
                                        <div class="col-payment grey box-image-payment">
                                            <div class="box-ico">
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
                                            </div> Pix
                                        </div>
                                        <div class="box-payment-option option">
                                            <div class="col-payment grey" id='percent-values-pix'>
                                                ${pix.percentage}
                                            </div>
                                            <div class="col-payment col-graph">
                                                <div class="bar pink" style="width: ${pix.percentage};">-</div>
                                            </div>
                                            <div class="col-payment end">
                                                <span class="money-td green grey bold" id='pix-value'>${pix.value}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="data-holder b-bottom">
                                    <div class="box-payment-option">
                                        <div class="col-payment grey box-image-payment">
                                            <div class="box-ico">
                                            <span class="">
                                                <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                            <div class="col-payment grey" id='percent-values-boleto'>
                                                ${boleto.percentage}
                                            </div>
                                            <div class="col-payment col-graph">
                                                <div class="bar purple" style="width: ${boleto.percentage};">-</div>
                                            </div>
                                            <div class="col-payment end">
                                                <span class="money-td green bold grey" id='boleto-value'>${boleto.value}</span>
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
                        <div class="container d-flex value-price" style="visibility: hidden;">
                            <h4 id='sales' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">${emptyGraph}</div>
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
        $('#card-regions .onPreLoad *' ).remove();
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
                // console.log(response.data);

                regionsHtml = `
                    <footer class="container footer-regions">
                        <section class="new-graph-regions graph">
                        </section>
                        <section class="info-regions">
                            <ul class="conversion-colors">
                                <!-- <li class="blue">60%</li>
                                <li class="purple">42%</li>
                                <li class="pink">48%</li>
                                <li class="orange">35%</li> -->
                            </ul>
                        </section>
                        <section class="info-regions">
                            <ul class="regions-legend">
                                <!-- <li class="access"><span></span>Acessos</li>
                                <li class="conversion"><span></span>Conversões</li> -->
                            </ul>
                        </section>
                    </footer>
                `;

                $("#block-regions").html(regionsHtml);

                // if(response.data != ''){
                //     $('.new-graph-regions').html('<canvas id=regionsChart></canvas>').addClass('visible');
                //     $(".new-graph-regions").next('.no-graph').remove();
                //     graphRegions();

                //     let percentage = `<li class='blue'>60%</li>`;
                //     let legend = `<li class='conversion'><span></span>Conversões</li>`;
                //     $('.conversion-colors').append(percentage);
                //     $('.regions-legend').append(legend);


                // } else {
                //     $('.info-regions li').remove();
                //     $('#regionsChart').remove();
                //     $(".new-graph-regions").next('.no-graph').remove();
                //     $('.new-graph-regions').after('<div class=no-graph>Não há dados suficientes</div>');
                //     $('.new-graph-regions').removeClass('visible');
                // }
                // $('#card-regions .ske-load').hide();
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

                // updateReports();
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
        // updateReports();
        updateStorage({company: $(this).val()})
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

        // if (link == null) {
        //     link =
        //         "/api/reports/getsalesbyorigin?" +
        //         "project_id=" +
        //         $("#select_projects").val() +
        //         "&start_date=" +
        //         startDate +
        //         "&end_date=" +
        //         endDate +
        //         "&origin=" +
        //         $("#origin").val();

        // } else {
        //     link =
        //         "/api/reports/getsalesbyorigin" +
        //         link +
        //         "&project_id=" +
        //         $("#select_projects").val() +
        //         "&start_date=" +
        //         startDate +
        //         "&end_date=" +
        //         endDate +
        //         "&origin=" +
        //         $("#origin").val();
        // }

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
                        <img src=${$("#origins-table-itens").attr("img-empty")}></td>
                    <td>
                        <p class='no-data-origin'>
                            <strong>Sem dados, por enquanto...</strong>
                            Ainda faltam dados suficientes a comparação, continue rodando!
                        </p>
                    </td>`;


                if (response.data == '') {
                    $("#origins-table").html(td);
                    $("#pagination").html("");
                    $("#pagination-origins").hide();
                } else {
                    var table_data = "";

                    $.each(response.data, function (index, data) {
                        table_data += "<tr>";
                        table_data += "<td>" + data.origin + "</td>";
                        table_data += "<td>" + data.sales_amount + "</td>";
                        table_data +=
                            "<td>" + data.balance + "</td>";
                        table_data += "</tr>";
                    });

                    $("#origins-table").html("");
                    $("#origins-table").append(table_data);
                    $(".table-vendas").addClass("table-striped");

                    pagination(response, "origins", updateSalesByOrigin);
                }
                $("#card-origin .ske-load").hide();
                $(".origin-report").show();
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
        var gradient = ctx.createLinearGradient(0, 0, 0, 450);
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
        var gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(54,216,119, 0.23)');
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
        var gradient = ctx.createLinearGradient(0, 0, 0, 450);
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

    function distributionGraphSeller() {
        new Chartist.Pie('.distribution-graph-seller', {
            series: [10, 20, 30, 15, 80, 70]
          }, {
            donut: true,
            donutWidth: 30,
            donutSolid: true,
            startAngle: 270,
            showLabel: false,
            chartPadding: 0,
            labelOffset: 0,
            height: 123
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
        var gradient = ctx.createLinearGradient(0, 0, 0, 450);
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


    function graphRegions() {

        const ctx = document.getElementById('regionsChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['SP', 'MG', 'RS', 'PR'],
                    datasets: [
                        {
                            label: '',
                            data: [60,22,48,35],
                            color:'#636363',
                            backgroundColor: [
                                'rgba(46, 133, 236, 1)',
                                'rgba(102, 95, 232, 1)',
                                'rgba(244, 63, 94, 1)',
                                'rgba(255, 121, 0, 1)',
                            ],
                            borderRadius: 4,
                            barThickness: 30,
                        },
                        {
                            label: '',
                            data: [100,42,58,45],
                            color:'#636363',
                            backgroundColor: [
                                'rgba(46, 133, 236, .2)',
                                'rgba(102, 95, 232, .2)',
                                'rgba(244, 63, 94, .2)',
                                'rgba(255, 121, 0, .2)',
                            ],
                            borderRadius: 4,
                            barThickness: 30,
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
                                padding: 0,
                                  stepSize: 100,
                                font: {
                                    family: 'Muli',
                                    size: 12,
                                },
                                color: "#636363",
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

            let obj = {
                calendar,
                company
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
<path d="M340.875 70.41C361.5 73.8549 375.714 94.3495 393 97.7104V106H3.05176e-05V55.9893C19.2434 55.9893 21.8113 71.2636 30.867 67.0589C39.9227 62.8542 49.4351 33.9447 60 35.4737C70.565 37.0027 76.3631 -3.56824 98.625 0.254224C120.887 4.07668 137.271 47.7352 155.005 29.0051C172.739 10.2751 176.509 20.1579 195.375 16.3355C214.241 12.513 222.506 58.3041 240.375 56.8408C258.244 55.3775 263.014 93.4635 281.25 89.931C303.348 85.6503 301.424 83.2526 311.25 77.7C321.076 72.1474 329.882 68.5739 340.875 70.41Z" fill="url(#paint0_linear_1640_459)" fill-opacity="0.5"/>
<path d="M104.728 49V37.72H105.768L113 47.32H112.632V37.72H113.864V49H112.84L105.608 39.4H105.96V49H104.728ZM119.593 49.144C118.879 49.144 118.255 48.9787 117.721 48.648C117.188 48.3067 116.772 47.8267 116.473 47.208C116.185 46.5893 116.041 45.848 116.041 44.984C116.041 44.1093 116.185 43.3627 116.473 42.744C116.772 42.1253 117.188 41.6507 117.721 41.32C118.255 40.9893 118.879 40.824 119.593 40.824C120.319 40.824 120.943 41.0107 121.465 41.384C121.999 41.7467 122.351 42.2427 122.521 42.872H122.329L122.505 40.968H123.753C123.721 41.2773 123.689 41.592 123.657 41.912C123.636 42.2213 123.625 42.5253 123.625 42.824V49H122.329V47.112H122.505C122.335 47.7413 121.983 48.2373 121.449 48.6C120.916 48.9627 120.297 49.144 119.593 49.144ZM119.849 48.088C120.617 48.088 121.225 47.8267 121.673 47.304C122.121 46.7707 122.345 45.9973 122.345 44.984C122.345 43.96 122.121 43.1867 121.673 42.664C121.225 42.1307 120.617 41.864 119.849 41.864C119.092 41.864 118.484 42.1307 118.025 42.664C117.577 43.1867 117.353 43.96 117.353 44.984C117.353 45.9973 117.577 46.7707 118.025 47.304C118.484 47.8267 119.092 48.088 119.849 48.088ZM117.833 39.56L117.097 39.512C117.151 38.8293 117.332 38.3067 117.641 37.944C117.951 37.5707 118.34 37.384 118.809 37.384C119.044 37.384 119.257 37.4373 119.449 37.544C119.652 37.6507 119.881 37.816 120.137 38.04C120.372 38.2533 120.559 38.4027 120.697 38.488C120.847 38.5627 120.991 38.6 121.129 38.6C121.673 38.6 122.004 38.1787 122.121 37.336L122.857 37.384C122.804 38.0667 122.623 38.5947 122.313 38.968C122.015 39.3307 121.631 39.512 121.161 39.512C120.937 39.512 120.724 39.4587 120.521 39.352C120.319 39.2453 120.084 39.0747 119.817 38.84C119.593 38.6373 119.407 38.4987 119.257 38.424C119.119 38.3387 118.975 38.296 118.825 38.296C118.548 38.296 118.324 38.408 118.153 38.632C117.993 38.8453 117.887 39.1547 117.833 39.56ZM129.427 49.144C128.659 49.144 127.987 48.9787 127.411 48.648C126.846 48.3067 126.409 47.8267 126.099 47.208C125.79 46.5787 125.635 45.8373 125.635 44.984C125.635 44.12 125.79 43.3787 126.099 42.76C126.409 42.1413 126.846 41.6667 127.411 41.336C127.987 40.9947 128.659 40.824 129.427 40.824C130.206 40.824 130.878 40.9947 131.443 41.336C132.019 41.6667 132.462 42.1413 132.771 42.76C133.091 43.3787 133.251 44.12 133.251 44.984C133.251 45.8373 133.091 46.5787 132.771 47.208C132.462 47.8267 132.019 48.3067 131.443 48.648C130.878 48.9787 130.206 49.144 129.427 49.144ZM129.427 48.088C130.195 48.088 130.803 47.8267 131.251 47.304C131.699 46.7707 131.923 45.9973 131.923 44.984C131.923 43.96 131.694 43.1867 131.235 42.664C130.787 42.1307 130.185 41.864 129.427 41.864C128.67 41.864 128.067 42.1307 127.619 42.664C127.171 43.1867 126.947 43.96 126.947 44.984C126.947 45.9973 127.171 46.7707 127.619 47.304C128.067 47.8267 128.67 48.088 129.427 48.088ZM139.444 49V37.224H140.74V42.744H140.532C140.756 42.1147 141.129 41.64 141.652 41.32C142.185 40.9893 142.798 40.824 143.492 40.824C144.452 40.824 145.166 41.0853 145.636 41.608C146.105 42.12 146.34 42.8987 146.34 43.944V49H145.044V44.024C145.044 43.2773 144.894 42.7333 144.596 42.392C144.308 42.0507 143.838 41.88 143.188 41.88C142.441 41.88 141.844 42.1093 141.396 42.568C140.958 43.0267 140.74 43.6293 140.74 44.376V49H139.444ZM151.875 49.144C151.16 49.144 150.536 48.9787 150.003 48.648C149.469 48.3067 149.053 47.8267 148.755 47.208C148.467 46.5893 148.323 45.848 148.323 44.984C148.323 44.1093 148.467 43.3627 148.755 42.744C149.053 42.1253 149.469 41.6507 150.003 41.32C150.536 40.9893 151.16 40.824 151.875 40.824C152.6 40.824 153.224 41.0107 153.747 41.384C154.28 41.7467 154.632 42.2427 154.803 42.872H154.611L154.787 40.968H156.035C156.003 41.2773 155.971 41.592 155.939 41.912C155.917 42.2213 155.907 42.5253 155.907 42.824V49H154.611V47.112H154.787C154.616 47.7413 154.264 48.2373 153.731 48.6C153.197 48.9627 152.579 49.144 151.875 49.144ZM152.131 48.088C152.899 48.088 153.507 47.8267 153.955 47.304C154.403 46.7707 154.627 45.9973 154.627 44.984C154.627 43.96 154.403 43.1867 153.955 42.664C153.507 42.1307 152.899 41.864 152.131 41.864C151.373 41.864 150.765 42.1307 150.307 42.664C149.859 43.1867 149.635 43.96 149.635 44.984C149.635 45.9973 149.859 46.7707 150.307 47.304C150.765 47.8267 151.373 48.088 152.131 48.088ZM151.795 40.184L153.443 36.952H154.835L152.723 40.184H151.795ZM165.624 49.144C164.92 49.144 164.302 48.9787 163.768 48.648C163.235 48.3067 162.819 47.8267 162.52 47.208C162.232 46.5893 162.088 45.848 162.088 44.984C162.088 44.1093 162.232 43.3627 162.52 42.744C162.819 42.1253 163.235 41.6507 163.768 41.32C164.302 40.9893 164.92 40.824 165.624 40.824C166.35 40.824 166.974 41.0053 167.496 41.368C168.03 41.7307 168.387 42.2213 168.568 42.84H168.376V37.224H169.672V49H168.392V47.08H168.568C168.398 47.72 168.046 48.2267 167.512 48.6C166.979 48.9627 166.35 49.144 165.624 49.144ZM165.896 48.088C166.654 48.088 167.262 47.8267 167.72 47.304C168.179 46.7707 168.408 45.9973 168.408 44.984C168.408 43.96 168.179 43.1867 167.72 42.664C167.262 42.1307 166.654 41.864 165.896 41.864C165.139 41.864 164.531 42.1307 164.072 42.664C163.624 43.1867 163.4 43.96 163.4 44.984C163.4 45.9973 163.624 46.7707 164.072 47.304C164.531 47.8267 165.139 48.088 165.896 48.088ZM175.234 49.144C174.519 49.144 173.895 48.9787 173.362 48.648C172.829 48.3067 172.413 47.8267 172.114 47.208C171.826 46.5893 171.682 45.848 171.682 44.984C171.682 44.1093 171.826 43.3627 172.114 42.744C172.413 42.1253 172.829 41.6507 173.362 41.32C173.895 40.9893 174.519 40.824 175.234 40.824C175.959 40.824 176.583 41.0107 177.106 41.384C177.639 41.7467 177.991 42.2427 178.162 42.872H177.97L178.146 40.968H179.394C179.362 41.2773 179.33 41.592 179.298 41.912C179.277 42.2213 179.266 42.5253 179.266 42.824V49H177.97V47.112H178.146C177.975 47.7413 177.623 48.2373 177.09 48.6C176.557 48.9627 175.938 49.144 175.234 49.144ZM175.49 48.088C176.258 48.088 176.866 47.8267 177.314 47.304C177.762 46.7707 177.986 45.9973 177.986 44.984C177.986 43.96 177.762 43.1867 177.314 42.664C176.866 42.1307 176.258 41.864 175.49 41.864C174.733 41.864 174.125 42.1307 173.666 42.664C173.218 43.1867 172.994 43.96 172.994 44.984C172.994 45.9973 173.218 46.7707 173.666 47.304C174.125 47.8267 174.733 48.088 175.49 48.088ZM184.812 49.144C184.108 49.144 183.489 48.9787 182.956 48.648C182.422 48.3067 182.006 47.8267 181.708 47.208C181.42 46.5893 181.276 45.848 181.276 44.984C181.276 44.1093 181.42 43.3627 181.708 42.744C182.006 42.1253 182.422 41.6507 182.956 41.32C183.489 40.9893 184.108 40.824 184.812 40.824C185.537 40.824 186.161 41.0053 186.684 41.368C187.217 41.7307 187.574 42.2213 187.756 42.84H187.564V37.224H188.86V49H187.58V47.08H187.756C187.585 47.72 187.233 48.2267 186.7 48.6C186.166 48.9627 185.537 49.144 184.812 49.144ZM185.084 48.088C185.841 48.088 186.449 47.8267 186.908 47.304C187.366 46.7707 187.596 45.9973 187.596 44.984C187.596 43.96 187.366 43.1867 186.908 42.664C186.449 42.1307 185.841 41.864 185.084 41.864C184.326 41.864 183.718 42.1307 183.26 42.664C182.812 43.1867 182.588 43.96 182.588 44.984C182.588 45.9973 182.812 46.7707 183.26 47.304C183.718 47.8267 184.326 48.088 185.084 48.088ZM194.662 49.144C193.894 49.144 193.222 48.9787 192.646 48.648C192.08 48.3067 191.643 47.8267 191.334 47.208C191.024 46.5787 190.87 45.8373 190.87 44.984C190.87 44.12 191.024 43.3787 191.334 42.76C191.643 42.1413 192.08 41.6667 192.646 41.336C193.222 40.9947 193.894 40.824 194.662 40.824C195.44 40.824 196.112 40.9947 196.678 41.336C197.254 41.6667 197.696 42.1413 198.006 42.76C198.326 43.3787 198.486 44.12 198.486 44.984C198.486 45.8373 198.326 46.5787 198.006 47.208C197.696 47.8267 197.254 48.3067 196.678 48.648C196.112 48.9787 195.44 49.144 194.662 49.144ZM194.662 48.088C195.43 48.088 196.038 47.8267 196.486 47.304C196.934 46.7707 197.158 45.9973 197.158 44.984C197.158 43.96 196.928 43.1867 196.47 42.664C196.022 42.1307 195.419 41.864 194.662 41.864C193.904 41.864 193.302 42.1307 192.854 42.664C192.406 43.1867 192.182 43.96 192.182 44.984C192.182 45.9973 192.406 46.7707 192.854 47.304C193.302 47.8267 193.904 48.088 194.662 48.088ZM203.178 49.144C202.538 49.144 201.941 49.0587 201.386 48.888C200.832 48.7067 200.373 48.456 200.01 48.136L200.458 47.224C200.853 47.5333 201.28 47.7627 201.738 47.912C202.208 48.0613 202.693 48.136 203.194 48.136C203.834 48.136 204.314 48.024 204.634 47.8C204.965 47.5653 205.13 47.2453 205.13 46.84C205.13 46.5307 205.024 46.2853 204.81 46.104C204.608 45.912 204.272 45.7627 203.802 45.656L202.314 45.352C201.632 45.2027 201.12 44.952 200.778 44.6C200.437 44.2373 200.266 43.7787 200.266 43.224C200.266 42.7547 200.389 42.344 200.634 41.992C200.88 41.6293 201.237 41.3467 201.706 41.144C202.176 40.9307 202.725 40.824 203.354 40.824C203.941 40.824 204.48 40.9147 204.97 41.096C205.472 41.2667 205.888 41.5173 206.218 41.848L205.754 42.744C205.434 42.4347 205.066 42.2053 204.65 42.056C204.245 41.896 203.824 41.816 203.386 41.816C202.757 41.816 202.282 41.944 201.962 42.2C201.653 42.4453 201.498 42.7707 201.498 43.176C201.498 43.4853 201.594 43.7413 201.786 43.944C201.989 44.136 202.298 44.28 202.714 44.376L204.202 44.68C204.928 44.84 205.466 45.0907 205.818 45.432C206.181 45.7627 206.362 46.2107 206.362 46.776C206.362 47.256 206.229 47.6773 205.962 48.04C205.696 48.392 205.322 48.664 204.842 48.856C204.373 49.048 203.818 49.144 203.178 49.144ZM215.288 49.144C214.648 49.144 214.05 49.0587 213.496 48.888C212.941 48.7067 212.482 48.456 212.12 48.136L212.568 47.224C212.962 47.5333 213.389 47.7627 213.848 47.912C214.317 48.0613 214.802 48.136 215.304 48.136C215.944 48.136 216.424 48.024 216.744 47.8C217.074 47.5653 217.24 47.2453 217.24 46.84C217.24 46.5307 217.133 46.2853 216.92 46.104C216.717 45.912 216.381 45.7627 215.912 45.656L214.424 45.352C213.741 45.2027 213.229 44.952 212.888 44.6C212.546 44.2373 212.376 43.7787 212.376 43.224C212.376 42.7547 212.498 42.344 212.744 41.992C212.989 41.6293 213.346 41.3467 213.816 41.144C214.285 40.9307 214.834 40.824 215.464 40.824C216.05 40.824 216.589 40.9147 217.08 41.096C217.581 41.2667 217.997 41.5173 218.328 41.848L217.864 42.744C217.544 42.4347 217.176 42.2053 216.76 42.056C216.354 41.896 215.933 41.816 215.496 41.816C214.866 41.816 214.392 41.944 214.072 42.2C213.762 42.4453 213.608 42.7707 213.608 43.176C213.608 43.4853 213.704 43.7413 213.896 43.944C214.098 44.136 214.408 44.28 214.824 44.376L216.312 44.68C217.037 44.84 217.576 45.0907 217.928 45.432C218.29 45.7627 218.472 46.2107 218.472 46.776C218.472 47.256 218.338 47.6773 218.072 48.04C217.805 48.392 217.432 48.664 216.952 48.856C216.482 49.048 215.928 49.144 215.288 49.144ZM223.385 49.144C222.436 49.144 221.716 48.8827 221.225 48.36C220.745 47.8373 220.505 47.0373 220.505 45.96V40.968H221.801V45.928C221.801 46.664 221.95 47.208 222.249 47.56C222.548 47.9013 223.012 48.072 223.641 48.072C224.345 48.072 224.91 47.8427 225.337 47.384C225.764 46.9253 225.977 46.312 225.977 45.544V40.968H227.273V49H226.009V47.192H226.217C225.993 47.8107 225.63 48.2907 225.129 48.632C224.638 48.9733 224.057 49.144 223.385 49.144ZM230.203 49V41.976H228.635V40.968H230.555L230.203 41.304V39.96C230.203 39.0533 230.427 38.36 230.875 37.88C231.334 37.4 231.985 37.16 232.827 37.16C233.03 37.16 233.243 37.1813 233.467 37.224C233.702 37.256 233.899 37.304 234.059 37.368V38.44C233.931 38.3867 233.771 38.344 233.579 38.312C233.398 38.2693 233.211 38.248 233.019 38.248C232.518 38.248 232.139 38.3973 231.883 38.696C231.627 38.9947 231.499 39.4533 231.499 40.072V41.24L231.291 40.968H236.443V49H235.147V41.976H231.499V49H230.203ZM235.003 39.144V37.688H236.603V39.144H235.003ZM242.351 49.144C241.562 49.144 240.874 48.9733 240.287 48.632C239.711 48.2907 239.263 47.8053 238.943 47.176C238.634 46.5467 238.479 45.8 238.479 44.936C238.479 44.0613 238.639 43.32 238.959 42.712C239.279 42.0933 239.727 41.624 240.303 41.304C240.89 40.984 241.572 40.824 242.351 40.824C242.863 40.824 243.359 40.9147 243.839 41.096C244.33 41.2773 244.735 41.5333 245.055 41.864L244.591 42.792C244.271 42.4827 243.919 42.2533 243.535 42.104C243.162 41.9547 242.794 41.88 242.431 41.88C241.599 41.88 240.954 42.1413 240.495 42.664C240.036 43.1867 239.807 43.9493 239.807 44.952C239.807 45.944 240.036 46.712 240.495 47.256C240.954 47.8 241.599 48.072 242.431 48.072C242.783 48.072 243.146 48.0027 243.519 47.864C243.903 47.7253 244.26 47.496 244.591 47.176L245.055 48.088C244.724 48.4293 244.314 48.6907 243.823 48.872C243.332 49.0533 242.842 49.144 242.351 49.144ZM246.897 49V40.968H248.193V49H246.897ZM246.737 39.144V37.688H248.337V39.144H246.737ZM254.277 49.144C253.019 49.144 252.027 48.7813 251.301 48.056C250.576 47.32 250.213 46.3013 250.213 45C250.213 44.1573 250.373 43.4267 250.693 42.808C251.013 42.1787 251.461 41.6933 252.037 41.352C252.613 41 253.275 40.824 254.021 40.824C254.757 40.824 255.376 40.9787 255.877 41.288C256.379 41.5973 256.763 42.04 257.029 42.616C257.296 43.1813 257.429 43.8533 257.429 44.632V45.112H251.205V44.296H256.581L256.309 44.504C256.309 43.6507 256.117 42.984 255.733 42.504C255.349 42.024 254.779 41.784 254.021 41.784C253.221 41.784 252.597 42.0667 252.149 42.632C251.701 43.1867 251.477 43.9387 251.477 44.888V45.032C251.477 46.0347 251.723 46.7973 252.213 47.32C252.715 47.832 253.413 48.088 254.309 48.088C254.789 48.088 255.237 48.0187 255.653 47.88C256.08 47.7307 256.485 47.4907 256.869 47.16L257.317 48.072C256.965 48.4133 256.517 48.68 255.973 48.872C255.44 49.0533 254.875 49.144 254.277 49.144ZM259.412 49V42.824C259.412 42.5253 259.396 42.2213 259.364 41.912C259.343 41.592 259.316 41.2773 259.284 40.968H260.532L260.692 42.728H260.5C260.735 42.1093 261.108 41.64 261.62 41.32C262.143 40.9893 262.746 40.824 263.428 40.824C264.378 40.824 265.092 41.08 265.572 41.592C266.063 42.0933 266.308 42.888 266.308 43.976V49H265.012V44.056C265.012 43.2987 264.858 42.7493 264.548 42.408C264.25 42.056 263.78 41.88 263.14 41.88C262.394 41.88 261.802 42.1093 261.364 42.568C260.927 43.0267 260.708 43.64 260.708 44.408V49H259.412ZM271.635 49.144C270.846 49.144 270.243 48.92 269.827 48.472C269.411 48.0133 269.203 47.3253 269.203 46.408V41.976H267.635V40.968H269.203V38.776L270.499 38.408V40.968H272.803V41.976H270.499V46.264C270.499 46.904 270.606 47.3627 270.819 47.64C271.043 47.9067 271.374 48.04 271.811 48.04C272.014 48.04 272.195 48.024 272.355 47.992C272.515 47.9493 272.659 47.9013 272.787 47.848V48.936C272.638 49 272.457 49.048 272.243 49.08C272.041 49.1227 271.838 49.144 271.635 49.144ZM277.871 49.144C276.612 49.144 275.62 48.7813 274.895 48.056C274.17 47.32 273.807 46.3013 273.807 45C273.807 44.1573 273.967 43.4267 274.287 42.808C274.607 42.1787 275.055 41.6933 275.631 41.352C276.207 41 276.868 40.824 277.615 40.824C278.351 40.824 278.97 40.9787 279.471 41.288C279.972 41.5973 280.356 42.04 280.623 42.616C280.89 43.1813 281.023 43.8533 281.023 44.632V45.112H274.799V44.296H280.175L279.903 44.504C279.903 43.6507 279.711 42.984 279.327 42.504C278.943 42.024 278.372 41.784 277.615 41.784C276.815 41.784 276.191 42.0667 275.743 42.632C275.295 43.1867 275.071 43.9387 275.071 44.888V45.032C275.071 46.0347 275.316 46.7973 275.807 47.32C276.308 47.832 277.007 48.088 277.903 48.088C278.383 48.088 278.831 48.0187 279.247 47.88C279.674 47.7307 280.079 47.4907 280.463 47.16L280.911 48.072C280.559 48.4133 280.111 48.68 279.567 48.872C279.034 49.0533 278.468 49.144 277.871 49.144ZM285.678 49.144C285.038 49.144 284.441 49.0587 283.886 48.888C283.332 48.7067 282.873 48.456 282.51 48.136L282.958 47.224C283.353 47.5333 283.78 47.7627 284.238 47.912C284.708 48.0613 285.193 48.136 285.694 48.136C286.334 48.136 286.814 48.024 287.134 47.8C287.465 47.5653 287.63 47.2453 287.63 46.84C287.63 46.5307 287.524 46.2853 287.31 46.104C287.108 45.912 286.772 45.7627 286.302 45.656L284.814 45.352C284.132 45.2027 283.62 44.952 283.278 44.6C282.937 44.2373 282.766 43.7787 282.766 43.224C282.766 42.7547 282.889 42.344 283.134 41.992C283.38 41.6293 283.737 41.3467 284.206 41.144C284.676 40.9307 285.225 40.824 285.854 40.824C286.441 40.824 286.98 40.9147 287.47 41.096C287.972 41.2667 288.388 41.5173 288.718 41.848L288.254 42.744C287.934 42.4347 287.566 42.2053 287.15 42.056C286.745 41.896 286.324 41.816 285.886 41.816C285.257 41.816 284.782 41.944 284.462 42.2C284.153 42.4453 283.998 42.7707 283.998 43.176C283.998 43.4853 284.094 43.7413 284.286 43.944C284.489 44.136 284.798 44.28 285.214 44.376L286.702 44.68C287.428 44.84 287.966 45.0907 288.318 45.432C288.681 45.7627 288.862 46.2107 288.862 46.776C288.862 47.256 288.729 47.6773 288.462 48.04C288.196 48.392 287.822 48.664 287.342 48.856C286.873 49.048 286.318 49.144 285.678 49.144Z" fill="#757575"/>
<defs>
<linearGradient id="paint0_linear_1640_459" x1="196.5" y1="0" x2="196.5" y2="150.34" gradientUnits="userSpaceOnUse">
<stop stop-color="#E8EDFA"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;