function updateAfterChangeCompany(){
    $("#select_projects").find('option').remove();
    let companies = JSON.parse(sessionStorage.getItem('companies'));
    $.each(companies, function (c, company) {
        if( sessionStorage.getItem('company_default') == company.id){
            $.each(company.projects, function (i, project) {
                $("#select_projects").append($('<option>', {
                    value: project.id,
                    text: project.name
                }));
            });
        }
    });
    window.updateReports();
}

$(function () {

    getProjects();

    function getProjects(){
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true&company="+ sessionStorage.getItem('company_default'),
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

                    window.updateReports();
                } else {
                    $("#export-excel").hide();
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }

                loadingOnScreenRemove();
            },
        });
    }

    $("#select_projects").on("change", function () {
        window.updateReports();
    });

    $("#origin").on("change", function () {
        $("#origin").val($(this).val());
        updateSalesByOrigin();
    });

    var current_currency = "";

    window.updateReports = function() {
        var date_range = $("#date_range_requests").val();

        $(
            "#revenue-generated, #qtd-aproved, #qtd-boletos, #qtd-recusadas, #qtd-chargeback, #qtd-reembolso, #qtd-pending, #qtd-canceled, #percent-credit-card, #percent-values-boleto,#credit-card-value,#boleto-value, #percent-boleto-convert#percent-credit-card-convert, #percent-desktop, #percent-mobile, #qtd-cartao-convert, #qtd-boleto-convert, #ticket-medio"
        ).html("<span>" + "<span class='loaderSpan' >" + "</span>" + "</span>");
        loadOnTable("#origins-table-itens", ".table-vendas-itens");

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

                if(response.totalPaidValueAproved=='R$ 0,00' || response.totalPaidValueAproved ==false || !response.totalPaidValueAproved){
                    response.totalPaidValueAproved='R$ <span class="font-size-30 bold">0,00</span>'
                }else{
                    let split=response.totalPaidValueAproved.split(/\s/g);
                    response.totalPaidValueAproved=split[0]+' <span class="font-size-30 bold">'+split[1]+'</span>';
                }

                $("#revenue-generated").html(response.totalPaidValueAproved);
                $("#qtd-aproved").html(response.contAproved);
                $("#qtd-boletos").html(response.contBoleto);
                $("#qtd-pix").html(response.contPix);
                $("#qtd-recusadas").html(response.contRecused);
                $("#qtd-reembolso").html(response.contRefunded);
                $("#qtd-chargeback").html(response.contChargeBack);
                $("#qtd-dispute").html(response.contInDispute);
                $("#qtd-pending").html(response.contPending);
                $("#qtd-canceled").html(response.contCanceled);
                $("#percent-credit-card").html(`
                    ${parseFloat(response.totalPercentCartao).toFixed(1)} %
                `);
                $("#percent-values-boleto").html(`
                    ${parseFloat(response.totalPercentPaidBoleto).toFixed(1)} %
                `);
                $("#percent-values-pix").html(`
                    ${parseFloat(response.totalPercentPaidPix).toFixed(1)} %
                `);
                $("#credit-card-value").html(response.totalValueCreditCard);
                $("#boleto-value").html(response.totalValueBoleto);
                $("#pix-value").html(response.totalValuePix);
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
                        "<td colspan='3' class='text-center'><img style='height:90px' src='" +
                            $("#origins-table-itens").attr("img-empty") +
                            "'><br> Nenhuma venda encontrada</div>"
                    );
                    $("#pagination").html("");
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
            window.updateReports();
        }
    );
});
