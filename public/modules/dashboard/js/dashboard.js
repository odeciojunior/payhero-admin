$(document).ready(function () {

    getProjects();

    function updateChart() {
        $('#scoreLineToMonth').html('')
        loadingOnChart('#chart-loading');

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-chart-data`,
            dataType: "json",
            data: {
                company: $('#company').val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnChartRemove('#chart-loading');
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                getChart(response)
                loadingOnChartRemove('#chart-loading');
            }
        });
    }

    function getChart(chartData) {

        let haveData = 0;

        chartData.value_data.forEach(function(elem, index){
            if(elem) haveData += parseInt(elem)
        });

        if (haveData > 0) {
            var scoreChart = function scoreChart(id, labelList, series1List) {
                    var scoreChart = new Chartist.Line("#" + id, {
                        labels: labelList,
                        series: [series1List],
                    }, {
                        lineSmooth: Chartist.Interpolation.simple({
                            divisor: 2
                        }),
                        showPoint: false,
                        showLine: false,
                        showArea: true,
                        fullWidth: true,
                        chartPadding: {
                            right: 50,
                            left: 20,
                            top: 30,
                            button: 20
                        },
                        axisX: {
                            showGrid: false,
                            labelInterpolationFnc: function (value) {
                                return value;
                            }
                        },
                        axisY: {
                            labelInterpolationFnc: function labelInterpolationFnc(value) {
                               let str = parseInt(value)

                                if (str > 0) {
                                    str = str / 1e3 + "K"
                                } else {
                                    str = "0.00"
                                }

                                return str;
                            },
                            scaleMinSpace: 40,
                        },
                        low: 0,
                        height: 260,
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

                    $('#not-empty-sale').show()
                },
                labelList = chartData.label_list,
                totalSalesData = {value: chartData.value_data}
            createChart = function createChart() {
                scoreChart("scoreLineToMonth", labelList, totalSalesData);
            };

            $('#empty-sale').fadeOut()
            createChart();
        } else {
            $('#empty-sale').fadeIn()
            $('#scoreLineToMonth').html('')
        }

    }

    $("#company").on("change", function () {
        updateValues();
        updateChart();
    });
    let userAccepted = true;

    function getDataDashboard() {
        $.ajax({
            method: "GET",
            url: `/api/dashboard${window.location.search}`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'appliation/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                if (!isEmpty(data.companies)) {
                    for (let i = 0; i < data.companies.length; i++) {
                        if (data.companies[i].company_type == '1') {
                            $('#company').append('<option value="' + data.companies[i].id_code + '">Pessoa física</option>')
                        } else {
                            $('#company').append('<option value="' + data.companies[i].id_code + '">' + data.companies[i].fantasy_name + '</option>')
                        }
                    }


                    $(".content-error").hide();
                    $('#company-select').show();

                    updateValues();
                    updateChart();
                    updatePerformance();
                    updateAccountHealth();
                } else {
                    $(".content-error").show();
                    $('#company-select, .page-content').hide();
                    loadingOnScreenRemove();
                }
            }
        });
    }

    function updateValues() {

        loadOnAnyEllipsis('.text-money, .update-text, .text-circle', false, {
            styles: {
                container: {
                    minHeight: '30px',
                    width: '30px',
                    height: 'auto',
                    margin: 'auto'
                },
                loader: {
                    width: '30px',
                    height: '30px',
                    borderWidth: '6px'
                },

            }
        });

        loadingOnChart('#chart-loading');

        $('.circle strong').addClass('loaded')

        $.ajax({
            method: "POST",
            url: "/api/dashboard/getvalues",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {company: $('#company').val()},
            error: function error(response) {
                loadOnAnyEllipsis('.text-money, .update-text, .text-circle', true)
                loadingOnScreenRemove();

                errorAjaxResponse(response);
            },
            success: function success(data) {

                $(".moeda").html(data.currency);
                $("#pending_money").html(data.pending_balance);
                $("#available_money").html(data.available_balance);
                $("#total_money").html(data.total_balance);
                $("#today_money").html(data.today_balance);

                $('#total_sales_approved').text(data.total_sales_approved);
                $('#total_sales_chargeback').text(data.total_sales_chargeback);

                let title = "Valor incluindo o saldo bloqueado de R$ " + data.blocked_balance_total;

                $('#info-total-balance').attr('title', title).tooltip({placement: 'bottom'});

                //--updateTrackings(data.trackings);
                //--updateChargeback(data.chargeback_tax);
                //--updateTickets(data.tickets);

                loadOnAnyEllipsis('.text-money, .update-text, .text-circle', true)
                //loadingOnScreenRemove();
            }
        });
    }



    // function updateTrackings(trackings) {
    //     $('#average_post_time').html(trackings.average_post_time + ' dia' + (trackings.average_post_time === 1 ? '' : 's'));
    //     $('#oldest_sale').html(trackings.oldest_sale + ' dia' + (trackings.oldest_sale === 1 ? '' : 's'));
    //     $('#problem').html(trackings.problem + ' <small>(' + trackings.problem_percentage + '%)</small>');
    //     $('#unknown').html(trackings.unknown + ' <small>(' + trackings.unknown_percentage + '%)</small>');
    // }
    //
    // function updateTickets(data) {
    //     $('#open-tickets').text(data.open || 0);
    //     $('#closed-tickets').text(data.closed || 0);
    //     $('#mediation-tickets').text(data.mediation || 0);
    //     $('#total-tickets').text(data.total);
    // }
    //
    // function updateNews(data) {
    //
    //     $('#carouselNews .carousel-inner').html('');
    //     $('#carouselNews .carousel-indicators').html('');
    //
    //     if (!isEmpty(data)) {
    //
    //         for (let i = 0; i < data.length; i++) {
    //
    //             let active = i === 0 ? 'active' : '';
    //
    //             let slide = `<div class="carousel-item ${active}">
    //                              <div class="card shadow news-background">
    //                                  <div class="card-body p-md-60 d-flex flex-column justify-content-center" style="height: 354px">
    //                                      <h1 class="news-title">${data[i].title}</h1>
    //                                      <div class="news-content">${data[i].content}</div>
    //                                  </div>
    //                              </div>
    //                          </div>`;
    //
    //             let indicator = `<li data-target="#carouselNews" data-slide-to="${i}" class="${active}"></li>`;
    //
    //             $('#carouselNews .carousel-inner').append(slide);
    //             $('#carouselNews .carousel-indicators').append(indicator);
    //         }
    //
    //         if (data.length === 1) {
    //             $('#carouselNews .carousel-indicators').hide();
    //             $('#carouselNews .carousel-control-prev, #carouselNews .carousel-control-next').hide();
    //         } else {
    //             $('#carouselNews .carousel-indicators').show();
    //             $('#carouselNews .carousel-control-prev, #carouselNews .carousel-control-next').show();
    //         }
    //
    //         $('#news-col').show();
    //     } else {
    //         $('#news-col').hide();
    //     }
    // }
    //
    // function updateReleases(data) {
    //     $('#releases-div').html('');
    //
    //     if (!isEmpty(data)) {
    //         $.each(data, function (index, value) {
    //             let item = `<div class="d-flex align-items-center my-15">
    //                             <div class="release-progress" id="${index}">
    //                                 <strong>${value.progress}%</strong>
    //                             </div>
    //                             <span class="ml-2">${value.release}</span>
    //                         </div>`;
    //             $('#releases-div').append(item);
    //
    //             updateReleasesProgress(index, value.progress);
    //         });
    //
    //         $('#releases-col').show();
    //     } else {
    //         $('#releases-col').hide();
    //     }
    // }

    // function verifyPendingData() {
    //     $.ajax({
    //         method: "GET",
    //         url: "/api/dashboard/verifypendingdata",
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         data: {company: $('#company').val()},
    //         error: function error(response) {
    //             errorAjaxResponse(response);
    //         },
    //         success: function success(response) {
    //             let companies = response.companies;
    //             if ((!isEmpty(companies)) || response.pending_user_data) {
    //                 if (response.pending_user_data) {
    //                     $('.tr-pending-profile').show();
    //                 } else {
    //                     $('.tr-pending-profile').hide();
    //                 }
    //                 for (let company of companies) {
    //                     $('.table-pending-data-body').append(`
    //                             <tr>
    //                                 <td style='width:2px;' class='text-center'>
    //                                 <span class="status status-lg status-away"></span>
    //                                 </td>
    //                                 <td class='text-left'>
    //                                     Empresas > ${company.fantasy_name}
    //                                 </td>
    //                                 <td class='text-center'>
    //                                     <a class='btn' style='color:darkorange;' href='/companies/${company.id_code}/edit?type=${company.type}' target='_blank'><b><i class="fa fa-pencil-square-o mr-2" aria-hidden="true"></i>Atualizar</b></a>
    //                                 </td>
    //                             </tr>
    //                         `);
    //                 }
    //                 if (!userAccepted) {
    //                     $('#modal-peding-data').modal('hide');
    //                 } else {
    //                     $('#modal-peding-data').modal('show');
    //                 }
    //             }
    //         }
    //     });
    // }
    // function updateReleasesProgress(id, value) {
    //
    //     let circle = $('#' + id);
    //
    //     let color = '';
    //     switch (true) {
    //         case value <= 33:
    //             color = '#FFA040';
    //             break;
    //         case value > 33 && value <= 66:
    //             color = '#FF6F00';
    //             break;
    //         default:
    //             color = '#C43E00';
    //             break;
    //     }
    //
    //     circle.circleProgress({
    //         size: 55,
    //         startAngle: -Math.PI / 2,
    //         thickness: 6,
    //         value: value / 100,
    //         fill: {
    //             color: color,
    //         }
    //     });
    // }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });

    function getProjects() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: '/api/projects?select=true',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();

                    getDataDashboard();

                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }

                loadingOnScreenRemove();
            }
        });
    }
});
