$(document).ready(function () {

    $('.company-navbar').on("change", function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $(".performance-card > .performance-data").html("");
        $('.sirius-cashback > .card').html('');
        $('#cashback-container #cashback-container-money').text("");
        putSkeletonLoadingOnBalanceCards();
        window.putSkeletonLoadingOnPerformance();
        $(".sirius-cashback > .card").addClass("d-none");
        putSkeletonLoadingOnChart();
        $('#scoreLineToMonth').html('');


        window.putSkeletonLoadingOnAccountHealth();
        updateCompanyDefault().done(function(data1){
            getCompaniesAndProjects().done(function(data2){
                if(!isEmpty(data2.company_default_projects)){
                    if( $("#project-empty").css('display')!='none' ){
                        $("#project-empty").hide();
                        $("#project-not-empty").show();
                        window.getDataDashboard();
                    }
                    else{
                        window.updateValues();
                        window.updateChart();
                        window.updatePerformance();
                        window.updateAccountHealth('80px');
                    }
                }
                else{
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }
            });
        });
    });

    window.updateChart = function() {
        $('#scoreLineToMonth').html('')
        putSkeletonLoadingOnChart();

        $.ajax({
            method: "GET",
            url: `/api/dashboard/get-chart-data`,
            dataType: "json",
            data: {
                company: $('.company-navbar').val(),
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                removeSkeletonLoadingFromChart();
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                setTimeout(() => {
                    removeSkeletonLoadingFromChart();
                    getChart(response);
                }, 2000);
            },
        });
    }

    function getChart(chartData) {
        let haveData = 0;

        chartData.value_data.forEach(function (elem, index) {
            if (elem) haveData += parseInt(elem);
        });

        if (haveData > 0) {
            var scoreChart = function scoreChart(id, labelList, series1List) {
                    var scoreChart = new Chartist.Line(
                        "#" + id,
                        {
                            labels: labelList,
                            series: [series1List],
                        },
                        {
                            lineSmooth: Chartist.Interpolation.simple({
                                divisor: 2,
                            }),
                            showPoint: false,
                            showLine: false,
                            showArea: true,
                            fullWidth: true,
                            chartPadding: {
                                right: 50,
                                left: 20,
                                top: 30,
                                button: 20,
                            },
                            axisX: {
                                showGrid: false,
                                labelInterpolationFnc: function (value) {
                                    return value;
                                },
                            },
                            axisY: {
                                labelInterpolationFnc: function labelInterpolationFnc(value) {
                                    let str = parseInt(value);

                                    if (str > 0) {
                                        str = str / 1e3 + "K";
                                    } else {
                                        str = "0.00";
                                    }

                                    return str;
                                },
                                scaleMinSpace: 40,
                            },
                            low: 0,
                            height: 260,
                        }
                    );
                    scoreChart
                        .on("created", function (data) {
                            var defs = data.svg.querySelector("defs") || data.svg.elem("defs"),
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
                                  new Chartist.Svg(data.element._node.parentNode).elem("line", {
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
                                                .translate(0, data.chartRect.height())
                                                .stringify(),
                                            to: data.path.clone().stringify(),
                                            easing: Chartist.Svg.Easing.easeOutQuint,
                                        },
                                    });
                        });

                    $("#not-empty-sale").show();
                },
                labelList = chartData.label_list,
                totalSalesData = { value: chartData.value_data };
            createChart = function createChart() {
                scoreChart("scoreLineToMonth", labelList, totalSalesData);
            };

            $("#empty-sale").fadeOut();
            createChart();
        } else {
            $("#empty-sale").fadeIn();
            $("#scoreLineToMonth").html("");
        }
    }

    window.getDataDashboard = function() {
        $.ajax({
            method: "GET",
            url: `/api/dashboard${window.location.search}`,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "appliation/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(data) {
                if (!isEmpty(data.companies)) {
                    window.updateValues();
                    window.updateChart();
                    window.updatePerformance();
                    window.updateAccountHealth();
                    setTimeout(verifyPixOnboarding, 1600);
                } else {
                    $(".content-error").show();
                    $("#company-select, .page-content").hide();
                }
            },
        });
    }

    window.updateValues = function() {

        putSkeletonLoadingOnBalanceCards();
        $.ajax({
            method: "POST",
            url: "/api/dashboard/getvalues",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                company_id: $('.company-navbar').val()
            },
            error: function error(response) {
                removeSkeletonLoadingFromBalanceCards();
                removeSkeletonLoadingFromChart();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                $("#pending_money").html(data.pending_balance);
                $("#available_money").html(data.available_balance);
                $("#total_money").html(data.total_balance);
                $("#today_money").html(data.today_balance);

                $("#total_sales_approved").text(data.total_sales_approved);
                $("#total_sales_chargeback").text(data.total_sales_chargeback);

                let $titleAvailableMoney = onlyNumbers(data.available_balance) > 0 ? "Disponível" : "Saldo Atual";
                $("#title_available_money").html($titleAvailableMoney);

                let title = "Valor incluindo o saldo retido de R$ " + data.blocked_balance_total;

                $("#info-total-balance").attr("title", title).tooltip({ placement: "bottom" });

                removeSkeletonLoadingFromBalanceCards();
            }
        });
    }

    function getProjects() {

        window.putSkeletonLoadingOnPerformance();
        window.putSkeletonLoadingOnAccountHealth();
        putSkeletonLoadingOnBalanceCards();
        putSkeletonLoadingOnChart();

        $.ajax({
            method: "GET",
            url: '/api/projects?select=true&company='+ $('.company-navbar').val()+'&tokens=true',
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                if(!origin)
                    loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    window.getDataDashboard();
                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }
                if(!origin)
                    loadingOnScreenRemove();
            },
        });
    }

    function showConfetti() {
        let startY = 605 / window.innerHeight;
        let count = 200;

        let defaults = {
            origin: { y: startY },
            startVelocity: 60,
            zIndex: 1700,
        };
        let fire = function (particleRatio, opts) {
            confetti({
                ...defaults,
                ...opts,
                particleCount: Math.floor(count * particleRatio),
            });
        };
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
            startVelocity: 20,
        });
        fire(0.1, {
            spread: 120,
            decay: 0.92,
            scalar: 1.2,
            startVelocity: 40,
        });
        fire(0.1, {
            spread: 120,
            startVelocity: 40,
        });
    }

    function verifyAchievements() {
        $.ajax({
            method: "GET",
            url: "/api/dashboard/verify-achievements",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    response.data.forEach((data, index) => {
                        let modal_is_level_type = "";
                        let modal_is_achievement_type = "";

                        if (data.type === 0) {
                            modal_is_achievement_type = `
                                    <div id="title-achievement">Você alcançou uma nova <strong>conquista!</strong></div>
                                    <div id="name-title">${data.name}</div>
                                    <div id="description-achievement">${data.description}</div>
                                    <div id="storytelling">${data.storytelling}</div>
                            `;
                        }

                        if (data.type === 1) {
                            modal_is_level_type = `
                                <div id="description">Você chegou ao <strong>${data.description}</strong></div>
                                <div id="name">${data.name}</div>
                                <div id="storytelling">${data.storytelling}</div>
                            `;

                            if (!isEmpty(data.benefits)) {
                                modal_is_level_type += `
                                    <div id="benefits">
                                        <div id="benefits-title">Aqui está sua recompensa:</div>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span id="benefits-data"><span class="material-icons">done</span> ${data.benefits}</span>
                                        </div>
                                    </div>`;
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

                        $("#modal-achievement-container").append(modal);

                        $(`#modal-achievement-data-${index}`).on("shown.bs.modal", function () {
                            // $('body').addClass('blurred');
                            $(`#modal-achievement-data-${index}`).unbind("click");
                            showConfetti();
                        });

                        $(`#modal-achievement-data-${index}`).on("hidden.bs.modal", function () {
                            $("body").removeClass("blurred");

                            setTimeout(() => {
                                let totalAchievement = $("[id*=modal-achievement-data-]").length - 1;

                                $(`#modal-achievement-data-${totalAchievement}`).modal("show");
                            }, 500);
                        });

                        $(`#reward-check-data-${index}`).click(() => {
                            let achievement = $(`#reward-check-data-${index}`).data("achievement");

                            $.ajax({
                                method: "PUT",
                                url: "/api/dashboard/update-achievements/" + achievement,
                                dataType: "json",
                                headers: {
                                    Authorization: $('meta[name="access-token"]').attr("content"),
                                    Accept: "application/json",
                                },
                                error: function error(response) {
                                    errorAjaxResponse(response);
                                    $(`#modal-achievement-data-${index}`).modal("hide");
                                },
                                success: function success() {
                                    $(`#modal-achievement-data-${index}`).modal("hide");
                                },
                            });

                            $(`#modal-achievement-data-${index}`).modal("hide");
                            setTimeout(() => {
                                $(`#modal-achievement-data-${index}`).remove();
                            }, 500);
                        });
                    });

                    let lastData = response.data.length;

                    if (lastData > 0) {
                        $(`[id*=modal-achievement-data-]:last`).modal("show");
                    }
                }
            },
        });
    }

    function verifyPixOnboarding() {
        $.ajax({
            method: "GET",
            url: "/api/dashboard/verify-pix-onboarding",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: function success(response) {
                if (response.read === false) {
                    loadingOnChart("#loader-onboarding");
                    let modalPixOnboarding = $("#modal-content-pix");

                    modalPixOnboarding.slick({
                        slidesToShow: 1,
                        mobileFirst: true,
                        infinite: false,
                        arrows: false,
                        adaptiveHeight: true,
                    });

                    $(window).on("resize", () => {
                        loadingOnChart("#loader-onboarding");
                        modalPixOnboarding.slick("refresh");

                        setTimeout(() => {
                            loadingOnChartRemove("#loader-onboarding");
                        }, 1000);
                    });

                    $("#modal-pix-onboarding")
                        .on("shown.bs.modal", function () {
                            modalPixOnboarding.slick("refresh");
                            $("#user-name").html(response.name);
                            $("#modal-pix").unbind("click");
                        })
                        .modal("show");
                    setTimeout(() => {
                        loadingOnChartRemove("#loader-onboarding");
                    }, 1000);

                    $(".pix-onboarding-next").click(() => {
                        modalPixOnboarding.slick("slickNext");
                    });

                    $(".pix-onboarding-previous").click(() => {
                        modalPixOnboarding.slick("slickPrev");
                    });

                    $(".pix-onboarding-later").click(() => {
                        $.ajax({
                            method: "PUT",
                            url: "/api/dashboard/update-pix-onboarding/" + response.onboarding,
                            dataType: "json",
                            headers: {
                                Authorization: $('meta[name="access-token"]').attr("content"),
                                Accept: "application/json",
                            },
                            error: function error() {
                                $("#modal-pix-onboarding").modal("hide");
                                verifyAchievements();
                            },
                            success: function success() {
                                $("#modal-pix-onboarding").modal("hide");
                                verifyAchievements();
                            },
                        });
                    });

                    $(".pix-onboarding-finish").click(() => {
                        $.ajax({
                            method: "PUT",
                            url: "/api/dashboard/update-pix-onboarding/" + response.onboarding,
                            dataType: "json",
                            headers: {
                                Authorization: $('meta[name="access-token"]').attr("content"),
                                Accept: "application/json",
                            },
                            error: function error() {
                                $("#modal-pix-onboarding").modal("hide");
                            },
                            success: function success() {
                                $("#modal-pix-onboarding").modal("hide");
                                if (response.accounts_url.indexOf("http") == -1) {
                                    response.accounts_url = "//" + response.accounts_url;
                                }
                                window.location.href = response.accounts_url;
                            },
                        });
                    });
                } else {
                    verifyAchievements();
                }
            },
        });
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });

    getCompaniesAndProjects().done( function (data){
        getProjects();
    });

    function putSkeletonLoadingOnBalanceCards() {
        $(".balances-card > .balance-card-data").hide();
        $(".balances-card > .loading-title").removeClass("d-none");
        $(".balances-card > .loading-content").removeClass("d-none");
    }

    function removeSkeletonLoadingFromBalanceCards() {
        $(".balances-card > .loading-title").addClass("d-none");
        $(".balances-card > .loading-content").addClass("d-none");
        $(".balances-card > .balance-card-data").show();
    }

    function putSkeletonLoadingOnChart() {
        $(".chart-data").hide();
        $(".chart-card > .loading-title").removeClass("d-none");
        $(".chart-card > .loading-content").removeClass("d-none");
        $(".chart-card > .loading-content-inside").removeClass("d-none");
    }

    function removeSkeletonLoadingFromChart() {
        $(".chart-card > .loading-title").addClass("d-none");
        $(".chart-card > .loading-content").addClass("d-none");
        $(".chart-card > .loading-content-inside").addClass("d-none");
        $(".chart-data").show();
    }



});
