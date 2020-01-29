$(function () {

    function projectionsExport(fileFormat) {

        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/reports/projectionsexport',
            xhrFields: {
                responseType: 'blob'
            },
            data: { format: fileFormat, company: $('#select_companies').val() },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response, textStatus, request) {
                loadingOnScreenRemove();
                downloadFile(response, request);
            }
        });
    }

    $("#bt_get_csv").on("click", function () {
        projectionsExport('csv');
    });

    $("#bt_get_xls").on("click", function () {
        projectionsExport('xls');
    });

    function downloadFile(response, request) {
        let type = request.getResponseHeader("Content-Type");
        // Get file name
        let contentDisposition = request.getResponseHeader("Content-Disposition");
        let fileName = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
        fileName = fileName ? fileName[0].replace("filename=", "") : '';

        var a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);
        a.href = window.URL.createObjectURL(new Blob([response], {type: type}));
        a.setAttribute("download", fileName);
        a.click();
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }

    $.ajax({
        method: "GET",
        url: "/api/companies/?select=true",
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
                $("#project-empty").show();
            } else {
                $(response.data).each(function (index, data) {
                    $("#select_companies").append("<option value='" + data.id + "'>" + data.name + "</option>");
                });

                $("#reports-content").show();

                updateReports();
            }
        }
    });

    $("#select_companies").on('change', function () {
        updateReports();
    });

    function updateReports() {

        $('#projection-total, #projection-billet, #projection-card').html("<span class='loaderSpan' >" + "</span>");
        loadOnTable('#body-table-transaction-itens');

        $.ajax({
            url: '/api/reports/projections',
            type: 'GET',
            data: {
                company: $("#select_companies").val(),
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

                $("#projection-total").html(response.chartData.currency + ' ' + response.totalValue);
                $("#projection-billet").html(response.chartData.currency + ' ' + response.valueBillet);
                $("#projection-card").html(response.chartData.currency + ' ' + response.valueCard);

                var table_data_itens = '';
                $.each(response.transactions, function (index, data) {
                    table_data_itens += '<tr>';
                    table_data_itens += '<td>' + data.date + "</td>";
                    table_data_itens += '<td>' + response.chartData.currency + ' ' +data.value + "</td>";
                    table_data_itens += '</tr>';
                });
                $('#body-table-transaction-itens').html("");
                $("#body-table-transaction-itens").append(table_data_itens);

                updateGraph(response.chartData);
            }
        });
    }


    function updateGraph(chartData) {

        var scoreChart = function scoreChart(id, labelList, series1List) {
                var scoreChart = new Chartist.Line("#" + id, {
                    labels: labelList, series: [series1List]
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
            transactionValueData = {
                name: "Saldo a liberar", data: chartData.transaction_data
            };
        createChart = function createChart(button) {
            scoreChart("scoreLineToDay", labelList, transactionValueData);
        }, createChart(), $(".chart-action li a").on("click", function () {
            createChart($(this));
        });
    }

});
