$(function () {

    $.ajax({
        method: "GET",
        url: "/api/projects/?select=true",
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            $("#modal-content").hide();
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (Object.keys(response.data).length === 0) {
                $("#no-projects").show();
            } else {
                $(response.data).each(function (index, data) {
                    $("#select_projects").append("<option value='" + data.id + "'>" + data.name + "</option>");
                });

                $("#reports-content").show();

                updateReports();
            }
        }
    });

    $("#select_projects").on('change', function () {
        updateReports();
    });

    $("#origin").on('change', function () {
        $('#origin').val($(this).val());
        updateSalesByOrigin();
    });

    var current_currency = '';

    function updateReports() {
        var date_range = $('#date_range_requests').val();

        $('#revenue-generated, #qtd-aproved, #qtd-boletos, #qtd-recusadas, #qtd-reembolso, #qtd-pending, #qtd-canceled' + '#percent-credit-card, #percent-values-boleto, #credit-card-value, #boleto-value, #percent-boleto-convert' + '#percent-credit-card-convert, #percent-desktop, #percent-mobile, #qtd-cartao-convert, #qtd-boleto-convert, #ticket-medio, #qtd-canceled').html("<span class='loading'>" + "<span class='loaderSpan' >" + "</span>" + "</span>");
        loadOnTable('#origins-table-itens', '.table-vendas-itens');

        $.ajax({
            url: '/api/reports',
            type: 'GET',
            data: {
                project: $("#select_projects").val(),
                endDate: endDate,
                startDate: startDate
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                current_currency = response.currency;
                $("#revenue-generated").html(response.currency + ' ' + response.totalPaidValueAproved);
                $("#qtd-aproved").html(response.contAproved);
                $("#qtd-boletos").html(response.contBoleto);
                $("#qtd-recusadas").html(response.contRecused);
                $("#qtd-reembolso").html(response.contChargeBack);
                $("#qtd-pending").html(response.contPending);
                $("#qtd-canceled").html(response.contCanceled);
                $("#percent-credit-card").html(response.totalPercentCartao + '%');
                $("#percent-values-boleto").html(response.totalPercentPaidBoleto + '%');
                $("#credit-card-value").html(response.currency + ' ' + response.totalValueCreditCard);
                $("#boleto-value").html(response.currency + ' ' + response.totalValueBoleto);
                $("#percent-boleto-convert").html(response.convercaoBoleto + '%');
                $("#percent-credit-card-convert").html(response.convercaoCreditCard + '%');
                $("#percent-desktop").html(response.conversaoDesktop + '%');
                $("#percent-mobile").html(response.conversaoMobile + '%');
                $("#qtd-cartao-convert").html(response.cartaoConvert);
                $("#qtd-boleto-convert").html(response.boletoConvert);
                $("#ticket-medio").html(response.currency + ' ' + response.ticketMedio);

                var table_data_itens = '';
                $.each(response.plans, function (index, data) {
                    table_data_itens += '<tr>';
                    table_data_itens += '<td><img src=' + data.photo + ' width="50px;" style="border-radius:6px;"></td>';
                    table_data_itens += '<td>' + data.name + "</td>";
                    table_data_itens += '<td> x ' + data.quantidade + "</td>";
                    table_data_itens += '</tr>';
                });
                $('#origins-table-itens').html("");
                $("#origins-table-itens").append(table_data_itens);

                updateGraph(response.chartData);
                updateSalesByOrigin();
            }
        });
    }

    function updateSalesByOrigin() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#origins-table', '.table-vendas');

        if (link == null) {
            link = '/api/reports/getsalesbyorigin?' + 'project_id=' + $("#select_projects").val() + '&start_date=' + startDate + '&end_date=' + endDate + '&origin=' + $("#origin").val();
        } else {
            link = '/api/reports/getsalesbyorigin' + link + '&project_id=' + $("#select_projects").val() + '&start_date=' + startDate + '&end_date=' + endDate + '&origin=' + $("#origin").val();
        }

        $.ajax({
            url: link,
            type: 'GET',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                if (response.data == '') {
                    $('#origins-table').html("<td colspan='3' class='text-center'> Nenhuma venda encontrada</div>");
                    $("#pagination").html("");
                } else {
                    var table_data = '';

                    $.each(response.data, function (index, data) {
                        table_data += '<tr>';
                        table_data += '<td>' + data.origin + "</td>";
                        table_data += '<td>' + data.sales_amount + "</td>";
                        table_data += '<td>' + current_currency + ' ' + data.balance + "</td>";
                        table_data += '</tr>';
                    });

                    $('#origins-table').html("");
                    $("#origins-table").append(table_data);
                    $('.table-vendas').addClass('table-striped');

                    pagination(response, "origins", updateSalesByOrigin);
                }
            }
        });
    }

    function updateGraph(chartData) {

        var scoreChart = function scoreChart(id, labelList, series1List, series2List) {
                var scoreChart = new Chartist.Line("#" + id, {
                    labels: labelList, series: [series1List, series2List]
                }, {
                    lineSmooth: Chartist.Interpolation.simple({
                        divisor: 2
                    }),
                    fullWidth: !0,
                    chartPadding: {
                        right: 30
                    },
                    series: {
                        "credit-card-data": {
                            showArea: !0
                        },
                        "boleto-data": {
                            showArea: !0
                        }
                    },
                    axisX: {
                        showGrid: !1
                    },
                    axisY: {
                        labelInterpolationFnc: function labelInterpolationFnc(value) {
                            return chartData.currency + value;
                            return value / 1e3 + "K";
                        },
                        scaleMinSpace: 40
                    },
                    plugins: [Chartist.plugins.tooltip({
                        position: 'bottom'
                    }), Chartist.plugins.legend()],
                    low: 0,
                    height: 300
                });
                scoreChart.on("created", function (data) {
                    var defs = data.svg.querySelector("defs") || data.svg.elem("defs"),
                        filter = (data.svg.width(), data.svg.height(), defs.elem("filter", {
                            x: 0, y: "-10%", id: "shadow" + id
                        }, "", !0));
                    return filter.elem("feGaussianBlur", {
                        in: "SourceAlpha", stdDeviation: "800", result: "offsetBlur"
                    }), filter.elem("feOffset", {
                        dx: "0", dy: "800"
                    }), filter.elem("feBlend", {
                        in: "SourceGraphic", mode: "multiply"
                    }), defs;
                }).on("draw", function (data) {
                    "line" === data.type ? data.element.attr({
                        filter: "url(#shadow" + id + ")"
                    }) : "point" === data.type && new Chartist.Svg(data.element._node.parentNode).elem("line", {
                        x1: data.x, y1: data.y, x2: data.x + .01, y2: data.y, class: "ct-point-content"
                    }), "line" !== data.type && "area" != data.type || data.element.animate({
                        d: {
                            begin: 1e3 * data.index,
                            dur: 1e3,
                            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                            to: data.path.clone().stringify(),
                            easing: Chartist.Svg.Easing.easeOutQuint
                        }
                    });
                });
            },
            labelList = chartData.label_list,
            creditCardSalesData = {
                name: "Cartão de crédito", data: chartData.boleto_data
            },
            boletoSalesData = {
                name: "Boleto", data: chartData.credit_card_data
            };
        createChart = function createChart(button) {
            scoreChart("scoreLineToDay", labelList, creditCardSalesData, boletoSalesData);
        }, createChart(), $(".chart-action li a").on("click", function () {
            createChart($(this));
        });
    }

    var startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
    var endDate = moment().format('YYYY-MM-DD');
    $('input[name="daterange"]').daterangepicker({
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        opens: 'left',
        maxDate: moment().endOf("day"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: 'Customizado',
        autoUpdateInput: true,
        locale: {
            locale: 'pt-br',
            format: 'DD/MM/YYYY',
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: 'De',
            toLabel: 'Até',
            customRangeLabel: 'Customizado',
            weekLabel: 'W',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 0
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
        updateReports();
    });

});
