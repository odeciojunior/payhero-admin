$(document).ready(function () {

    getDataDashboard();

    $("#company").on("change", function () {
        updateValues();
    });

    function getDataDashboard() {
        loadOnAny('.page-content');
        $.ajax({
            method: "GET",
            url: "/api/dashboard",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            },
            success: function success(data) {
                if (!isEmpty(data.companies)) {
                    for (let i = 0; i < data.companies.length; i++) {
                        $('#company').append('<option value="' + data.companies[i].id_code + '">' + data.companies[i].fantasy_name + '</option>')
                    }

                    updateValues();

                    $(".content-error").hide();
                    $('#company-select').show();
                } else {
                    $(".content-error").show();
                    $('#company-select, .page-content').hide();
                }
            }
        });
    }

    function updateValues() {

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
                loadOnAny('.page-content', true);
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

                updateProgressBar(data.chargeback_tax);
                updateNews(data.news);
                loadOnAny('.page-content', true);
            }
        });
    }

    function updateProgressBar(value) {
        $('.circle').circleProgress({
            size: 176,
            startAngle: -Math.PI / 2,
            value: value / 100,
            fill: {
                gradient: ["#f76b1c", "#fa6161"]
            }
        });

        $('.circle strong').text(parseFloat(value).toFixed(2) + '%');
    }

    function updateNews(data) {

        $('#carouselNews .carousel-inner').html('');
        $('#carouselNews .carousel-indicators').html('');

        if (!isEmpty(data)) {

            for (let i = 0; i < data.length; i++) {

                let active = i === 0 ? 'active' : '';

                let slide = `<div class="carousel-item ${active}">
                                 <div class="card shadow news-background">
                                     <div class="card-body p-md-60 d-flex flex-column justify-content-center" style="height: 370px">
                                         <h1 class="news-title">${data[i].title}</h1>
                                         <div class="news-content">${data[i].content}</div>
                                     </div>
                                 </div>
                             </div>`;

                let indicator = `<li data-target="#carouselNews" data-slide-to="${i}" class="${active}"></li>`;

                $('#carouselNews .carousel-inner').append(slide);
                $('#carouselNews .carousel-indicators').append(indicator);
            }

            if (data.length === 1) {
                $('#carouselNews .carousel-indicators').hide();
                $('#carouselNews .carousel-control-prev, #carouselNews .carousel-control-next').hide();
            } else {
                $('#carouselNews .carousel-indicators').show();
                $('#carouselNews .carousel-control-prev, #carouselNews .carousel-control-next').show();
            }

            $('#news-row').show();
        } else {
            $('#news-row').hide();
        }
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });
});
