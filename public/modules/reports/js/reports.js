$(function () {
    
    
    loadingOnScreen();
    newFinanceGraph();
    distributionGraph();
    newSellGraph();
    distributionGraphSeller();

    function getCashback() {
        const currentRequest = $.ajax({
            type: 'GET',
            url: "/api/reports/resume/cashbacks?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        });

        return $.ajax({
            method: "GET",
            url: "/api/reports/resume/cashbacks?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function() {
                currentRequest.abort();
                $('#card-cashback .ske-load').show();
                $('.new-graph-cashback').next('.no-graph').remove();
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, status) {
                if(status !== ''){
                    if(response.data != ''){
                        let value = response.data.replace("R$", "");
                        $("#cashback").html("<span class='currency'>R$ </span>" + value).addClass('visible');
    
                        if(response.data !== '0,00') {
                            $('.new-graph-cashback').html('<div class=graph-cashback></div>');
                            $(".new-graph-cashback").next('.no-graph').remove();
                            newGraphCashback();
                            
                        } else {
                            $(".new-graph-cashback").next('.no-graph').remove();
                            $('.new-graph-cashback').after('<div class=no-graph>Não há dados suficientes</div>');
                            $("#cashback").html("<span class='currency'>R$ </span>" + '0,00').addClass('visible');
                        }                    
                    } else {
                        $("#cashback").html("<span class='currency'>R$ </span>" + '0,00').addClass('visible');
                        $('.new-graph-cashback').next('.no-graph').remove();
                        $('.new-graph-cashback').after('<div class=no-graph>Não há dados suficientes</div>');
                    }
                    $('#card-cashback .ske-load').hide();
                }
            }
        });
    }

    function getPending() {
        const currentRequest = $.ajax({
            type: 'GET',
            url: "/api/reports/resume/pendings?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        });

        return $.ajax({
            method: "GET",
            url: "/api/reports/resume/pendings?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function() {
                currentRequest.abort();
                $('#card-pending .ske-load').show();
                $('.new-graph-pending').next('.no-graph').remove();
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, status) {
                if(status !== '') {
                    if(response.data != ''){
                        let value = response.data.replace("R$", " ");
                        $("#pending").html("<span class='currency'>R$ </span>" + value).addClass('visible');
                        
                        if(response.data !== '0,00') {
                            $('.new-graph-pending').html('<div class=graph-pending></div>');
                            $(".new-graph-pending").next('.no-graph').remove();
                            newGraphPending();
                        } else {
                            $('.new-graph-pending').after('<div class=no-graph>Não há dados suficientes</div>');
                            $("#pending").html("<span class='currency'>R$ </span>" + '0,00').addClass('visible');
                        }
                    } else {
                        $("#pending").html("<span class='currency'>R$ </span>" + '0,00').addClass('visible');
                        $('.new-graph-pending').next('.no-graph').remove();
                        $('.new-graph-pending').after('<div class=no-graph>Não há dados suficientes</div>');
                    }
                    $('#card-pending .ske-load').hide();
                }
            }
        });
    }

    function getCommission() {
        const currentRequest = $.ajax({
            type: 'GET',
            url: "/api/reports/resume/commissions?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        });

        return $.ajax({
            method: "GET",
            url: "/api/reports/resume/commissions?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function() {
                currentRequest.abort();
                $('#card-comission .ske-load').show();
                $(".new-graph").next('.no-graph').remove();
            },
            error: function error(response) {
                errorAjaxResponse(response);
                $('#card-comission .ske-load').hide();
            },
            success: function success(response, status) {
                if(status !== '') {
                    if(response.data != ''){
                        let value = response.data.replace("R$", "");
                        
                        $("#comission").html("<span class='currency'>R$ </span>" + value).addClass('visible');
                        
                        if(response.data !== '0,00') {
                            $('.new-graph').html('<div class=graph-comission></div>');
                            $(".new-graph").next('.no-graph').remove();
                            newGraph();
                        } else {
                            $('.new-graph').after('<div class=no-graph>Não há dados suficientes</div>');
                            $("#comission").html("<span class='currency'>R$ </span>" + '0,00').addClass('visible');
                        }
                    } else {
                        $("#comission").html("<span class='currency'>R$ </span>" + '0,00').addClass('visible');
                        $('.new-graph').next('.no-graph').remove();
                        $('.new-graph').after('<div class=no-graph>Não há dados suficientes</div>');
                    }
                    $('#card-comission .ske-load').hide();
                }
            }
        });
    }

    function getSales() {
        const currentRequest = $.ajax({
            type: 'GET',
            url: "/api/reports/resume/sales?date_range=" + $("input[name='daterange']").val(),
        });

        return $.ajax({
            method: "GET",
            url: "/api/reports/resume/sales?date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function() {
                currentRequest.abort();
                $('#card-sales .ske-load').show();
                $(".new-graph-sell").next('.no-graph').remove();
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, status) {
                if(status !== ''){
                    $("#sales").addClass('visible');
                    if(response.data != '0'){
                        $("#sales").html(response.data);
                        $('.new-graph-sell').html('<div class=graph-sell></div>');
                        $(".new-graph-sell").next('.no-graph').remove();
                        newGraphSell();
                        
                    } else {
                        $("#sales").html('0');
                        $(".new-graph-sell").next('.no-graph').remove();
                        $('.new-graph-sell').after('<div class=no-graph>Não há dados suficientes</div>');
                    }
                    $('#card-sales .ske-load').hide();
                }
            }
        });
    }

    function getProducts() {
        const currentRequest = $.ajax({
            type: 'GET',
            url: "/api/reports/resume/products?date_range=" + $("input[name='daterange']").val(),
        });

       return $.ajax({
            method: "GET",
            url: "/api/reports/resume/products?date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function() {
                currentRequest.abort();
                $('#card-products .ske-load').show();
                $("#card-products .value-price").next('.no-graph').remove();
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, status) {
                if(status !== ''){
                    $('#card-products .ske-load').show();
                    $("#qtd").addClass('visible');
                    $("#card-products .value-price").next('.no-graph').remove();
                    $('.list-products li').remove();

                    if(response.data != ''){
                        $.each(response.data, function (i, product) {
                            if(product.total != 0) {
                                if(product.amount) {
                                    $(".list-products").append(
                                        $("<li class='" + ( (i > 3 && i < 8) ? 'line': '' ) + "'>"+
                                            "<div class='box-list-products'>"+
                                            "<figure data-container='body' data-viewport='.container' data-placement='top' data-toggle='tooltip' title='" + product.name +"'><img src='"+ product.image +"' width='24px' height='24px' /></figure>"+
                                            "<div class='bars " +product.color+ "' style='width:"+ product.percentage +"'>"+
                                            "<span>" + product.amount + "</span></div></div></li>"
                                        )
                                    );
                                    $(".list-products, .footer-products").addClass('visible');
                                    $('#card-products .value-price').addClass('invisible');
                                    $('[data-toggle="tooltip"]').tooltip();
                                }
                            } else {
                                $('#card-products .value-price').removeClass('invisible');
                                $("#qtd").html(0);
                                $(".footer-products").removeClass('visible');
                            }
                        });
                    } else {
                        $('#card-products .value-price').removeClass('invisible');
                        $("#qtd").html('0');
                        
                        $("#card-products .value-price").next('.no-graph').remove();
                        $("#card-products .value-price").after('<div class=no-graph>Não há dados suficientes</div>');
                        $('.no-graph').css('height','111px');
                        $(".footer-products").removeClass('visible');
                    }
                    $('#card-products .ske-load').hide();
                }
            }
        });
    }

    function getCoupons() {
        const currentRequest = $.ajax({
            type: 'GET',
            url: "/api/reports/resume/coupons?date_range=" + $("input[name='daterange']").val(),
        });

        return $.ajax({
            method: "GET",
            url: "/api/reports/resume/coupons?date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            beforeSend: function() {
                currentRequest.abort();
                $('#card-coupons .ske-load').show();
                $(".new-graph-pie").next('.no-graph').remove();
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response, status) {
                if(status !== ''){
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
                            $("#qtd-dispute").html('0').addClass('visible');
                            $('#card-coupons .value-price').removeClass('invisible');
                            
                            $('.box-donut').css('height','0');
                            $(".box-donut").next('.no-graph').remove();
                            $('.box-donut').after('<div class=no-graph>Não há dados suficientes</div>');
                        }
                    } else {
                        $("#qtd-dispute").html('0').addClass('visible');
                        $('#card-coupons .value-price').removeClass('invisible');
                        
                        $('.box-donut').css('height','0');
                        $(".box-donut").next('.no-graph').remove();
                        $('.box-donut').after('<div class=no-graph>Não há dados suficientes</div>');
                    }
                    $('#card-coupons .ske-load').hide();
                }
            }
        });
    }

    function getTypePayments() {
        return $.ajax({
            method: "GET",
            url: "/api/reports/resume/type-payments?date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
                $('#card-typepayments .ske-load').hide();
                
            },
            success: function success(response, status) {
                $('#payment-type-items .bar').addClass('visible');
                
                if(status !== '') {
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
                } else {
                    console.log('else error');
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

                updateReports();
            } else {
                $("#export-excel").hide();
                $("#project-not-empty").hide();
                $("#project-empty").show();
            }

            loadingOnScreenRemove();
        },
    });

    $("#select_projects").on("change", function () {
        updateReports();
    });

    $("#origin").on("change", function () {
        $("#origin").val($(this).val());
        updateSalesByOrigin();
    });

    function resume() {
       
        $.when(
            getCommission(),
            getTypePayments(),
            getProducts(),
            getPending(),
            getCashback(),
            getSales(),
            getCoupons()
        ).then((comission,payments,products,pending,cashback,sales,coupons) => {
            // console.log('comission ', comission);
            // console.log('payments ', payments);
            // console.log('products ', products[1]);
            // console.log('pending ', pending[1]);
            // console.log('cashback ', cashback[1]);
            // console.log('sales ', sales[1]);
            // console.log('coupons ', coupons);

            if(comission[1] == "success") {
                $('#card-comission .ske-load').hide();
            }
            if(payments[1] == "success") {
                $('#card-typepayments .ske-load').hide();
            }
            if(products[1] == "success") {
                $('#card-products .ske-load').hide();
            }
            if(pending[1] == "success") {
                $('#card-pending .ske-load').hide();
            }
            if(cashback[1] == "success") {
                $('#card-cashback .ske-load').hide();
            }
            if(sales[1] == "success") {
                $('#card-sales .ske-load').hide();
            }
            if(coupons[1] == "success") {
                $('#card-coupons .ske-load').hide();
            }
        });        
    }

    var current_currency = "";

    function updateReports() {
        var date_range = $("#date_range_requests").val();
        
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
        }

        $.ajax({
            url: "/api/reports",
            type: "GET",
            data: {
                project: $("#select_projects").val(),
                endDate: endDate,
                startDate: startDate,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                current_currency = response.currency;

                if(response.totalPaidValueAproved='R$ 0,00' || response.totalPaidValueAproved ==false || !response.totalPaidValueAproved){
                    response.totalPaidValueAproved='R$ <span class="grey font-size-24 bold">0,00</span>'
                }else{
                    let split=response.totalPaidValueAproved.split(/\s/g);
                    response.totalPaidValueAproved=split[0]+' <span class="font-size-30 bold">'+split[1]+'</span>';
                }

                $("#revenue-generated").html(response.totalPaidValueAproved);
                $("#qtd-aproved").html(response.contAproved);
                $("#qtd-boletos").html(response.contBoleto);
                $("#qtd-pix").html(response.contPix);
                $("#qtd-recusadas").html(response.contRecused);
                // $("#qtd-reembolso").html(response.contRefunded);
                $("#qtd-chargeback").html(response.contChargeBack);
                // $("#qtd-dispute").html(response.contInDispute);
                $("#qtd-pending").html(response.contPending);
                $("#qtd-canceled").html(response.contCanceled);
                
                $("#percent-boleto-convert").html(`
                    <span class="money-td"> ${parseFloat(response.convercaoBoleto).toFixed(1)} % </span>
                `);
                $("#percent-credit-card-convert").html(`
                    <span class="money-td"> ${parseFloat(response.convercaoCreditCard).toFixed(1)} % </span>
                `);
                $("#percent-pix-convert").html(`
                    <span class="money-td"> ${parseFloat(response.convercaoPix).toFixed(1)} % </span>
                `);
                $("#percent-desktop").html(`
                    ${parseFloat(response.conversaoDesktop).toFixed(1)} %
                `);
                $("#percent-mobile").html(`
                    ${parseFloat(response.conversaoMobile).toFixed(1)} %
                `);
                $("#qtd-cartao-convert").html(response.cartaoConvert);
                $("#qtd-boleto-convert").html(response.boletoConvert);
                $("#qtd-pix-convert").html(response.pixConvert);
                $("#ticket-medio").html(
                    response.currency + " " + response.ticketMedio
                );

                $('#conversion-items').asScrollable();
                $('#payment-type-items').asScrollable();

                var table_data_itens = "";
                if (!isEmpty(response.plans)) {
                    $.each(response.plans, function (index, data) {
                        table_data_itens += "<tr>";
                        table_data_itens +=
                            "<td><img src=" +
                            data.photo +
                            ' width="50px;" style="border-radius:6px;"></td>';
                        table_data_itens += "<td>" + data.name + "</td>";
                        table_data_itens +=
                            "<td> x " + data.quantidade + "</td>";
                        table_data_itens += "</tr>";
                    });
                } else {
                    table_data_itens +=
                        "<tr class='text-center'><td colspan='3' style='vertical-align: middle'><img style='height:90px' src='" +
                        $("#origins-table-itens").attr("img-empty") +
                        "'>Nenhuma venda encontrada</td></tr>";
                }

                $("#origins-table-itens").html("");
                $("#origins-table-itens").append(table_data_itens);
                var flag = false;
                $.each(response.chartData.boleto_data,function(index,value){
                    if (value!=false) {
                        flag=true;
                    }
                });
                $.each(response.chartData.credit_card_data,function(index,value){
                    if (value!=false) {
                        flag=true;
                    }
                });
                $.each(response.chartData.pix_data,function(index,value){
                    if (value!=false) {
                        flag=true;
                    }
                });
                if (flag==true) {
                    $('#empty-graph>').hide();
                    $('#scoreLineToDay').show();
                    $('#scoreLineToWeek').show();
                    $('#scoreLineToMonth').show();
                    updateGraph(response.chartData);
                }else{
                    $('#empty-graph>').show();
                    $('#scoreLineToDay').hide();
                    $('#scoreLineToWeek').hide();
                    $('#scoreLineToMonth').hide();
                }
                updateSalesByOrigin();

                resume();
                
            },
        });
    }

    function updateSalesByOrigin() {
        var link =
            arguments.length > 0 && arguments[0] !== undefined
                ? arguments[0]
                : null;

        loadOnTable("#origins-table", ".table-vendas");

        if (link == null) {
            link =
                "/api/reports/getsalesbyorigin?" +
                "project_id=" +
                $("#select_projects").val() +
                "&start_date=" +
                startDate +
                "&end_date=" +
                endDate +
                "&origin=" +
                $("#origin").val();
        } else {
            link =
                "/api/reports/getsalesbyorigin" +
                link +
                "&project_id=" +
                $("#select_projects").val() +
                "&start_date=" +
                startDate +
                "&end_date=" +
                endDate +
                "&origin=" +
                $("#origin").val();
        }

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
                if (response.data.length == 0) {
                    $("#origins-table").html(
                        "<td><img src='" + $("#origins-table-itens").attr("img-empty") + "'></td><td> <p class='no-data-origin'><strong>Sem dados, por enquanto...</strong>Ainda faltam dados suficientes a comparação, continue rodando!</p></td>"
                    );
                    $("#pagination").html("");
                    $("#pagination-origins").hide();
                } else {
                    var table_data = "";

                    $.each(response.data, function (index, data) {
                        table_data += "<tr>";
                        table_data += "<td>" + data.origin + "</td>";
                        table_data += "<td>" + data.sales_amount + "</td>";
                        table_data +=
                            "<td>" +
                            current_currency +
                            " " +
                            data.balance +
                            "</td>";
                        table_data += "</tr>";
                    });

                    $("#origins-table").html("");
                    $("#origins-table").append(table_data);
                    $(".table-vendas").addClass("table-striped");

                    pagination(response, "origins", updateSalesByOrigin);
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

    var startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    var endDate = moment().format("YYYY-MM-DD");
    $('input[name="daterange"]').daterangepicker(
        {
            startDate: moment().subtract(30, "days"),
            endDate: moment(),
            opens: "left",
            maxDate: moment().endOf("day"),
            alwaysShowCalendar: true,
            showCustomRangeLabel: "Customizado",
            autoUpdateInput: true,
            locale: {
                locale: "pt-br",
                format: "DD/MM/YYYY",
                applyLabel: "Aplicar",
                cancelLabel: "Limpar",
                fromLabel: "De",
                toLabel: "Até",
                customRangeLabel: "Customizado",
                weekLabel: "W",
                daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
                monthNames: [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro",
                ],
                firstDay: 0,
            },
            ranges: {
                Hoje: [moment(), moment()],
                Ontem: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este mês": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Mês passado": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
        },
        function (start, end) {
            startDate = start.format("YYYY-MM-DD");
            endDate = end.format("YYYY-MM-DD");
            updateReports();
        }
    );

    // new graphs
    function newGraph() {
        new Chartist.Line('.graph-comission', {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            series: [
              [120, 90, 480, 370, 65, 60, 550, 55, 5],
            ]
          }, {
            fullWidth: true,
            showArea: true,
            chartPadding: 0,
            axisX: {
              showLabel: false,
              offset: 0,
              showGrid: false
            },
            axisY: {
              showLabel: false,
              offset: 0,
              showGrid: false
            }
          });
    }
    function newGraphSell() {
        new Chartist.Line('.graph-sell', {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            series: [
              [12, 9, 7, 8, 5],
            ]
          }, {
            fullWidth: true,
            showArea: true,
            chartPadding: 0,
            axisX: {
              showLabel: false,
              offset: 0,
              showGrid: false
            },
            axisY: {
              showLabel: false,
              offset: 0,
              showGrid: false
            }
          });
    }

    function newGraphCashback() {
        new Chartist.Line('.graph-cashback', {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            series: [[12, 9, 7, 8, 5],]
          }, {
            fullWidth: true,
            showArea: true,
            chartPadding: 0,
            axisX: {
              showLabel: false,
              offset: 0,
              showGrid: false,
            },
            axisY: {
              showLabel: false,
              showGrid: false,
              offset: 0
            }
          });
    }
    function newGraphPending() {
        new Chartist.Line('.graph-pending', {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            series: [[12, 9, 7, 8, 5],]
          }, {
            fullWidth: true,
            showArea: true,
            chartPadding: 0,
            axisX: {
              showLabel: false,
              offset: 0,
              showGrid: false,
            },
            axisY: {
              showLabel: false,
              showGrid: false,
              offset: 0
            }
          });
          
    }
    
    function newFinanceGraph() {
        new Chartist.Line('.new-finance-graph', {
            labels: [1, 5, 10, 15, 20, 30 ],
            series: [[1, 5, 10, 15, 20, 30]]
        }, {
            chartPadding: {
                top: 40,
                right: 0,
                left: -3,
                bottom: 20
            },
            axisX: {
                labelOffset: {
                    x: -15,
                    y: 15
                },
                showGrid: false,
            },
            axisY: {
                labelOffset: {
                    x: 0,
                    y: 0
                },
                offset: 55,
                labelInterpolationFnc: function(value) {
                    return 'R$ ' + value + 'K'
                },
                scaleMinSpace: 40
            },
            fullWidth: true,
            low: 0,
            height: 289,
            showArea: true
        });
    }

    function distributionGraph() {
        new Chartist.Pie('.distribution-graph', {
            series: [30, 15, 80, 70]
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

    function newSellGraph() {
        new Chartist.Line('.new-sell-graph', {
            labels: [1, 5, 10, 15, 20, 30 ],
            series: [[1, 5, 10, 15, 20, 30]]
        }, {
            chartPadding: {
                top: 40,
                right: 0,
                left: -3,
                bottom: 20
            },
            axisX: {
                labelOffset: {
                    x: -15,
                    y: 15
                },
                showGrid: false,
            },
            axisY: {
                labelOffset: {
                    x: 0,
                    y: 0
                },
                offset: 55,
                labelInterpolationFnc: function(value) {
                    return 'R$ ' + value + 'K'
                },
                scaleMinSpace: 40
            },
            fullWidth: true,
            low: 0,
            height: 289,
            showArea: true
        });
    }


});
