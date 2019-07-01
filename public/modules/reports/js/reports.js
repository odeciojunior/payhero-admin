$(function () {
    var startDate = moment().subtract('days', 29).format('YYYY-MM-DD');
    var endDate = moment().format('YYYY-MM-DD');
    $('input[name="daterange"]').daterangepicker({
        startDate: moment().subtract('days', 29),
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
            daysOfWeek: [
                'Dom',
                'Seg',
                'Ter',
                'Qua',
                'Qui',
                'Sex',
                'Sab'
            ],
            monthNames: [
                'Janeiro',
                'Fevereiro',
                'Março',
                'Abril',
                'Maio',
                'Junho',
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro'
            ],
            firstDay: 0,
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
        console.log(startDate, endDate);
        updateReports();

    });/* function (start, end, label) {
        endDate = end.format('YYYY-MM-DD');
        startDate = start.format('YYYY-MM-DD');
        // console.log(endDate, startDate);
        updateReports();
    */


    /*$('#date_range_requests').on('change', function (e) {
        e.preventDefault();
        updateReports();
    });*/


    $("#project").on('change', function () {
        $('#project').val($(this).val());
        updateReports();

    });

    function updateReports() {
        var date_range = $('#date_range_requests').val();
        console.log(date_range);
        $.ajax({
            url: '/reports/getValues/' + $("#project").val(),
            type: 'GET',
            data: {
                project: $("#project").val(),
                endDate: endDate,
                startDate: startDate
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                alertCustom('error', 'Erro ao tentar buscar dados');
            },
            success: function (response) {
                $("#revenue-generated").html(response.currency + ' ' + response.totalPaidValueAproved);
                $("#qtd-aproved").html(response.contAproved);
                $("#qtd-boletos").html(response.contBoleto);
                $("#qtd-recusadas").html(response.contRecused);
                $("#qtd-reembolso").html(response.contChargeBack);
                $("#percent-credit-card").html(response.totalPercentCartao + ' %');
                $("#percent-values-boleto").html(response.totalPercentPaidBoleto + ' %');
                $("#credit-card-value").html(response.currency + ' ' + response.totalValueCreditCard);
                $("#boleto-value").html(response.currency + ' ' + response.totalValueBoleto);

                updateGraph(response.chartData);
            }
        })
    }

    updateReports();


    function updateGraph(chartData){

        var scoreChart=function(id, labelList, series1List, series2List) {
            var scoreChart=new Chartist.Line("#"+id, {
                labels: labelList, series: [series1List, series2List]
            }, 
            {
                lineSmooth:Chartist.Interpolation.simple( {
                    divisor: 2
                }), 
                fullWidth:!0, 
                chartPadding: {
                    right: 25
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
                    labelInterpolationFnc:function(value) {
                        return chartData.currency + value;
                        return value/1e3+"K"
                    }, 
                    scaleMinSpace:40
                }, 
                plugins:[
                    Chartist.plugins.tooltip({
                        position: 'bottom'
                    }),
                    Chartist.plugins.legend()
                ],
                low:0,
                height:300
            });
            scoreChart.on("created", function(data) {
                var defs=data.svg.querySelector("defs")||data.svg.elem("defs"), filter=(data.svg.width(), data.svg.height(), defs.elem("filter", {
                    x: 0, y: "-10%", id: "shadow"+id
                }, "", !0));
                return filter.elem("feGaussianBlur", {
                    in: "SourceAlpha", stdDeviation: "8", result: "offsetBlur"
                }), 
                filter.elem("feOffset", {
                    dx: "0", dy: "10"
                }), 
                filter.elem("feBlend", {
                    in: "SourceGraphic", mode: "multiply"
                }), 
                defs
            }
            ).on("draw", function(data) {
                "line"===data.type?data.element.attr( {
                    filter: "url(#shadow"+id+")"
                }
                ):"point"===data.type&&new Chartist.Svg(data.element._node.parentNode).elem("line", {
                    x1: data.x, y1: data.y, x2: data.x+.01, y2: data.y, class: "ct-point-content"
                }), 
                "line"!==data.type&&"area"!=data.type||data.element.animate( {
                    d: {
                        begin: 1e3*data.index, dur: 1e3, from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(), to: data.path.clone().stringify(), easing: Chartist.Svg.Easing.easeOutQuint
                    }
                })
            })
        },
        labelList= chartData.label_list, 
        creditCardSalesData= {
            name: "Cartão de crédito", data: chartData.boleto_data
        }, 
        boletoSalesData= {
            name: "Boleto", data: chartData.credit_card_data
        };
        createChart=function(button) {
                scoreChart("scoreLineToDay", labelList, creditCardSalesData, boletoSalesData);
        },
        createChart(), $(".chart-action li a").on("click", function() {
            createChart($(this))
        })

    }
});