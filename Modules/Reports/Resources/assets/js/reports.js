$(function () {
    loadingOnScreen();

    
    distributionGraphSeller();
    getInfo();

    sessionStorage.removeItem('info');

    let resumeUrl = '/api/reports/resume';

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
                    $('.new-graph-cashback').html('<canvas id="graph-cashback"></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphCashback(series, labels);
                } else {
                    cashHtml = `
                        <div class="container d-flex value-price">
                            <h4 id='cashback' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">
                            <span>Não há dados suficientes</span>
                            <img src="/build/global/img/reports/bg-no-graph.png" />
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
                    $('.new-graph-pending').html('<canvas id=graph-pending></canvas>')
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphPending(series,labels);
                    
                } else {
                    pendHtml = `
                        <div class="container d-flex value-price">                            
                            <h4 id='pending' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">
                            <span>Não há dados suficientes</span>
                            <img src="/build/global/img/reports/bg-no-graph.png" />
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
                    $('.new-graph').html('<canvas id=comission-graph></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    graphComission(series, labels);
                } else {
                    comissionhtml = `
                        <div class="container d-flex value-price">                            
                            <h4 id='comission' class="font-size-24 bold grey">
                                <span class="currency">R$ </span>
                                0,00
                            </h4>
                        </div>
                        <div class="no-graph">
                            <span>Não há dados suficientes</span>
                            <img src="/build/global/img/reports/bg-no-graph.png" />
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
                    $('.new-graph-sell').html('<canvas id=graph-sell></canvas>');
                    let labels = [...chart.labels];
                    let series = [...chart.values];
                    newGraphSell(series, labels);
                }else {
                    salesHtml = `
                        <div class="container d-flex value-price">                            
                            <h4 id='sales' class="font-size-24 bold grey">
                                0
                            </h4>
                        </div>
                        <div class="no-graph">
                            <span>Não há dados suficientes</span>
                            <img src="/build/global/img/reports/bg-no-graph.png" />
                        </div>
                    `;
                    $("#block-sales").html(salesHtml);
                }
            }
        });

    }

    function getProducts() {
        let lista = '';
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
                $('#card-products .ske-load').show();
                $("#qtd").addClass('visible');
                $("#card-products .value-price").next('.no-graph').remove();
                $('.list-products li').remove();

                var total = response.data.total;
                if(total) {
                    $.each(response.data.products, function (i, product) {
                        let { color, amount, percentage, image, name } = product;
                        if(amount) {
                            lista = `
                                <li class="${( i > 3 && i < 8 ) ? 'line': ''}">
                                    <div class="box-list-products">
                                        <figure 
                                            data-container="body" 
                                            data-viewport=".container" 
                                            data-placement="top" 
                                            data-toggle="tooltip" 
                                            title="${name}">
                                                <img class="photo" src="${image}" width="24px" height="24px" />
                                        </figure>
                                        <span style="color: #636363; padding-left: 0;">${(( 100 * amount ) / total) > 19 ? '' : amount}</span>
                                        <div class="bars ${color}" style="width:${(( 100 * amount ) / total).toFixed(1)}%">
                                            <span>${(( 100 * amount ) / total) > 19 ? amount : ''}</span>
                                        </div>
                                    </div>
                                </li>`
                            ;
                            $(".list-products").append(lista);
                            $(".list-products, .footer-products").addClass('visible');
                            $('#card-products .value-price').addClass('invisible');
                            $('[data-toggle="tooltip"]').tooltip({
                                container: '.list-products'
                            });
                            $('.photo').on('error', function() {
                                $(this).attr('src', 'https://cloudfox-files.s3.amazonaws.com/produto.svg');
                            });
                        }
                    });
                }else {
                    $('#card-products .value-price').removeClass('invisible');
                    $("#qtd").html(0);
                    $("#card-products .value-price").next('.no-graph').remove();
                    $("#card-products .value-price").after('<div class=no-graph>Não há dados suficientes</div>');
                    $('#card-products .no-graph').css('height','111px');
                    $(".footer-products").removeClass('visible');
                }
                $('#card-products .ske-load').hide();
            }
        });
    }

    function getCoupons() {
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
                if(response.data != ''){
                    if(response.data[0].total != 0){
                        $('.box-donut').css('height','190px');
                        $(".box-donut").next('.no-graph').remove();

                        $('#card-coupons .value-price').addClass('invisible');
                        $('.new-graph-pie').html('<div class=graph-pie></div>');
                        $(".new-graph-pie").next('.no-graph').remove();

                        let arr = [];
                        let seriesArr = [];

                        $.each(response.data, function (i, coupon) {
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
                        $(".data-pie ul li").remove();
                        $("#qtd-dispute").html('0').addClass('visible');
                        $('#card-coupons .value-price').removeClass('invisible');
                        $('.box-donut').css('height','0');
                        $(".box-donut").next('.no-graph').remove();
                        $('.box-donut').after('<div class=no-graph>Não há dados suficientes</div>');
                    }
                } else {
                    $(".data-pie ul li").remove();
                    $("#qtd-dispute").html('0').addClass('visible');
                    $('#card-coupons .value-price').removeClass('invisible');
                    $('.box-donut').css('height','0');
                    $(".box-donut").next('.no-graph').remove();
                    $('.box-donut').after('<div class=no-graph>Não há dados suficientes</div>');
                }
                $('#card-coupons .ske-load').hide();
            }
        });
    }

    function getTypePayments() {
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
                $('#card-typepayments .ske-load').hide();

            },
            success: function success(response) {
                $('#payment-type-items .bar').addClass('visible');
                if( response.data.total ) {
                    //credit_card
                    $("#credit-card-value").html(response.data.credit_card.value);
                    $("#percent-credit-card").html(response.data.credit_card.percentage);

                    if(response.data.credit_card.percentage){
                        $("#percent-credit-card").next('.col-payment').find('.bar').css('width', response.data.credit_card.percentage );
                        $("#percent-credit-card").next('.col-payment').find('.bar').addClass('blue');
                    }

                    // boleto
                    $("#boleto-value").html(response.data.boleto.value);
                    $("#percent-values-boleto").html(response.data.boleto.percentage);

                    if(response.data.boleto.percentage > '0'){
                        $("#percent-values-boleto").next('.col-payment').find('.bar').css('width', response.data.boleto.percentage );
                        $("#percent-values-boleto").next('.col-payment').find('.bar').addClass('pink');
                    }

                    // pix
                    $("#pix-value").html(response.data.pix.value);
                    $("#percent-values-pix").html(response.data.pix.percentage);

                    if(response.data.pix.percentage > '0'){
                        $("#percent-values-pix").next('.col-payment').find('.bar').css('width', response.data.pix.percentage );
                        $("#percent-values-pix").next('.col-payment').find('.bar').addClass('purple');
                    }
                    $('.bar').removeClass('visible');

                } else {
                    $('#percent-credit-card, #percent-values-boleto, #percent-values-pix ').html('0%');
                    $('#credit-card-value, #boleto-value, #pix-value').html('R$ 0,00');
                    $('#payment-type-items .bar').css('width', '100%');
                }
                $('#type-payment').addClass('visible');
                $('#card-typepayments .ske-load').hide();
            }
        });
    }

    function getRegions() {

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
                if(response.data != ''){
                    $('.new-graph-regions').html('<canvas id=regionsChart></canvas>').addClass('visible');
                    $(".new-graph-regions").next('.no-graph').remove();
                    graphRegions();

                    let percentage = `<li class='blue'>60%</li>`;
                    let legend = `<li class='conversion'><span></span>Conversões</li>`;
                    $('.conversion-colors').append(percentage);
                    $('.regions-legend').append(legend);


                } else {
                    $('.info-regions li').remove();
                    $('#regionsChart').remove();
                    $(".new-graph-regions").next('.no-graph').remove();
                    $('.new-graph-regions').after('<div class=no-graph>Não há dados suficientes</div>');
                    $('.new-graph-regions').removeClass('visible');
                }
                $('#card-regions .ske-load').hide();
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
            } else {
                $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
            }
        }
    })
    .on('datepicker-change', function () {
        updateReports();
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