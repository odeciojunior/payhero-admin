$(document).ready(function () {

    let userAccepted = true;

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

        chartData.value_data.forEach(function (elem, index) {
            if (elem) haveData += parseInt(elem)
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
                    localStorage.setItem('companies', JSON.stringify(data.companies));
                    for (let i = 0; i < data.companies.length; i++) {

                        if(localStorage.getItem('companySelected') == data.companies[i].id_code)
                            itemSel = 'selected="selected"'
                        else
                            itemSel = ''

                        if (data.companies[i].company_type == '1') {
                            $('#company').append('<option value="' + data.companies[i].id_code + '" '+itemSel+'>Pessoa física</option>')
                        } else {
                            $('#company').append('<option value="' + data.companies[i].id_code + '" '+itemSel+'>' + data.companies[i].fantasy_name + '</option>')
                        }
                    }
                    // if(localStorage.getItem('companySelected')){
                    //     $('#company').val(localStorage.getItem('companySelected')).change();
                    // }

                    $(".content-error").hide();
                    $('#company-select').addClass('d-sm-flex');//show();

                    updateValues();
                    updateChart();
                    updatePerformance();
                    updateAccountHealth();
                    setTimeout(verifyPixOnboarding, 1600);
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

                let $titleAvailableMoney = onlyNumbers(data.available_balance) > 0 ? 'Disponível' : 'Saldo Atual'
                $("#title_available_money").html($titleAvailableMoney)


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

    function showConfetti() {
        let startY = 605 / window.innerHeight;
        let count = 200;

        let defaults = {
            origin: {y: startY},
            startVelocity: 60,
            zIndex: 1700,
        };
        let fire = function (particleRatio, opts) {
            confetti({
                ...defaults,
                ...opts,
                particleCount: Math.floor(count * particleRatio)
            });
        }
        fire(0.25, {
            spread: 26,
        });
        fire(0.2, {
            spread: 60,
        });
        fire(0.35, {
            spread: 100,
            decay: 0.91,
            scalar: 0.8,
            startVelocity: 20
        });
        fire(0.1, {
            spread: 120,
            decay: 0.92,
            scalar: 1.2,
            startVelocity: 40
        });
        fire(0.1, {
            spread: 120,
            startVelocity: 40
        });
    }

    // function showConfetti() {
    //
    //     let velocity = window.innerWidth * 4 / 100;
    //
    //     let end = Date.now() + velocity * 20;
    //
    //     let common = {
    //         particleCount: 5,
    //         spread: velocity,
    //         zIndex: 1700,
    //         startVelocity: velocity,
    //     };
    //
    //     (function frame() {
    //         confetti({
    //             ...common,
    //             angle: 60,
    //             origin: {x: 0},
    //         });
    //         confetti({
    //             ...common,
    //             angle: 120,
    //             origin: {x: 1},
    //         });
    //         if (Date.now() < end) {
    //             requestAnimationFrame(frame);
    //         }
    //     }());
    // }

    function verifyAchievements() {
        $.ajax({
            method: "GET",
            url: '/api/dashboard/verify-achievements',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                if (!isEmpty(response.data)) {
                    response.data.forEach((data, index) => {
                        let modal_is_level_type = ''
                        let modal_is_achievement_type = ''

                        if (data.type === 0) {
                            modal_is_achievement_type = `
                                    <div id="title-achievement">Você alcançou uma nova <strong>conquista!</strong></div>
                                    <div id="name-title">${data.name}</div>
                                    <div id="description-achievement">${data.description}</div>
                                    <div id="storytelling">${data.storytelling}</div>
                            `
                        }

                        if (data.type === 1) {
                            modal_is_level_type = `
                                <div id="description">Você chegou ao <strong>${data.description}</strong></div>
                                <div id="name">${data.name}</div>
                                <div id="storytelling">${data.storytelling}</div>
                            `

                            if (!isEmpty(data.benefits)) {
                                modal_is_level_type += `
                                    <div id="benefits">
                                        <div id="benefits-title">Aqui está sua recompensa:</div>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span id="benefits-data"><span class="material-icons">done</span> ${data.benefits}</span>
                                        </div>
                                    </div>`
                            }
                        }

                        let modal = `
                            <div id="modal-achievement-data-${index}" class="modal fade modal-fade-in-scale-up show">
                                <div id="achievement-details" class="modal-dialog modal-simple achievement-details-style">
                                    <div class="modal-content">
                                        <div class="modal-header flex-wrap">
                                            <div class="w-p100">
                                                <img id="icon" src="${data.icon}" alt="Image">
                                            </div>
                                        </div>
                                        <div class="modal-body">

                                            ${modal_is_achievement_type}


                                            ${modal_is_level_type}

                                            <div id="reward-check-data-${index}"
                                                class="btn btn-primary"
                                                 data-dismiss="modal"
                                                 aria-label="close"
                                                 data-target="#modal-achievement"
                                                 data-achievement="${data.achievement}">Ok, legal!</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        $('#modal-achievement-container').append(modal)

                        $(`#modal-achievement-data-${index}`).on('shown.bs.modal', function () {
                            // $('body').addClass('blurred');
                            $(`#modal-achievement-data-${index}`).unbind("click");
                            showConfetti();
                        });

                        $(`#modal-achievement-data-${index}`).on('hidden.bs.modal', function () {
                            $('body').removeClass('blurred');

                            setTimeout(() => {
                                let totalAchievement = ($('[id*=modal-achievement-data-]').length) - 1

                                $(`#modal-achievement-data-${totalAchievement}`).modal('show')
                            }, 500)
                        });

                        $(`#reward-check-data-${index}`).click(() => {
                            let achievement = $(`#reward-check-data-${index}`).data('achievement')

                            $.ajax({
                                method: "PUT",
                                url: '/api/dashboard/update-achievements/' + achievement,
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: function error(response) {
                                    errorAjaxResponse(response);
                                    $(`#modal-achievement-data-${index}`).modal('hide')
                                },
                                success: function success() {
                                    $(`#modal-achievement-data-${index}`).modal('hide')
                                }
                            });

                            $(`#modal-achievement-data-${index}`).modal('hide')
                            setTimeout(() => {
                                $(`#modal-achievement-data-${index}`).remove()
                            }, 500)
                        })
                    })

                    let lastData = response.data.length;

                    if (lastData > 0) {
                        $(`[id*=modal-achievement-data-]:last`).modal('show')
                    }
                }
            }
        });
    }

    function verifyOnboarding() {
        $.ajax({
            method: "GET",
            url: '/api/dashboard/verify-onboarding',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: function success(response) {

                if (response.read === false) {
                    loadingOnChart('#loader-onboarding')
                    let modalOnboarding = $('#modal-content-onboarding')

                    modalOnboarding.slick({
                        slidesToShow: 1,
                        mobileFirst: true,
                        infinite: false,
                        arrows: false,
                        adaptiveHeight: true
                    })

                    $(window).on('resize', () => {
                        loadingOnChart('#loader-onboarding')
                        modalOnboarding.slick("refresh")

                        setTimeout(() => {
                            loadingOnChartRemove('#loader-onboarding')
                        }, 1000)
                    })


                    $('#modal-onboarding').on('shown.bs.modal', function () {
                        modalOnboarding.slick("refresh")
                        $('#user-name').html(response.name)
                        $(`#modal-onboarding`).unbind("click");
                    })
                        .modal('show');
                    setTimeout(() => {
                        loadingOnChartRemove('#loader-onboarding')
                    }, 1000)
                    $('#onboarding-next-presentation, #onboarding-next-gamification, #onboarding-next-account-health').click(() => {
                        modalOnboarding.slick("slickNext")
                    })

                    $('#onboarding-finish').click(() => {
                        $.ajax({
                            method: "PUT",
                            url: '/api/dashboard/update-onboarding/' + response.onboarding,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function error() {
                                $('#modal-onboarding').modal('hide')
                            },
                            success: function success() {
                                $('#modal-onboarding').modal('hide')
                                verifyAchievements()
                            }
                        });
                    });
                } else {
                    verifyAchievements()
                }
            }
        });
    }

    function verifyPixOnboarding() {
        $.ajax({
            method: "GET",
            url: '/api/dashboard/verify-pix-onboarding',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: function success(response) {

                if (response.read === false) {
                    loadingOnChart('#loader-onboarding')
                    let modalPixOnboarding = $('#modal-content-pix')

                    modalPixOnboarding.slick({
                        slidesToShow: 1,
                        mobileFirst: true,
                        infinite: false,
                        arrows: false,
                        adaptiveHeight: true
                    })

                    $(window).on('resize', () => {
                        loadingOnChart('#loader-onboarding')
                        modalPixOnboarding.slick("refresh")

                        setTimeout(() => {
                            loadingOnChartRemove('#loader-onboarding')
                        }, 1000)
                    })


                    $('#modal-pix-onboarding').on('shown.bs.modal', function () {
                        modalPixOnboarding.slick("refresh")
                        $('#user-name').html(response.name)
                        $('#modal-pix').unbind("click");
                    })
                        .modal('show');
                    setTimeout(() => {
                        loadingOnChartRemove('#loader-onboarding')
                    }, 1000)

                    $('.pix-onboarding-next').click(() => {
                        modalPixOnboarding.slick("slickNext")
                    })

                    $('.pix-onboarding-previous').click(() => {
                        modalPixOnboarding.slick("slickPrev")
                    })

                    $('.pix-onboarding-later').click(() => {
                        $.ajax({
                            method: "PUT",
                            url: '/api/dashboard/update-pix-onboarding/' + response.onboarding,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function error() {
                                $('#modal-pix-onboarding').modal('hide')
                                verifyAchievements();
                            },
                            success: function success() {
                                $('#modal-pix-onboarding').modal('hide')
                                verifyAchievements();
                            }
                        });
                    });

                    $('.pix-onboarding-finish').click(() => {
                        $.ajax({
                            method: "PUT",
                            url: '/api/dashboard/update-pix-onboarding/' + response.onboarding,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function error() {
                                $('#modal-pix-onboarding').modal('hide')
                            },
                            success: function success() {
                                $('#modal-pix-onboarding').modal('hide')
                                if (response.accounts_url.indexOf('http') == -1) {
                                    response.accounts_url = '//' + response.accounts_url
                                }
                                window.location.href = response.accounts_url
                            }
                        });
                    });
                } else {
                    verifyAchievements()
                }
            }
        });
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });

    $("#company").on("change", function () {
        updateValues();
        updateChart();
    });

    getProjects();
});
