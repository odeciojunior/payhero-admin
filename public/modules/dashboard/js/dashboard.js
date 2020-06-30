$(document).ready(function () {
    console.log();
    getDataDashboard();
    verifyPendingData();

    $("#company").on("change", function () {
        updateValues();
    });
    function verifyPendingData() {
        $.ajax({
            method: "GET",
            url: "/api/dashboard/verifypendingdata",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {company: $('#company').val()},
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let companies = response.companies;
                if ((!isEmpty(companies)) || response.pending_user_data) {
                    if (response.pending_user_data) {
                        $('.tr-pending-profile').show();
                    } else {
                        $('.tr-pending-profile').hide();
                    }
                    for (let company of companies) {
                        $('.table-pending-data-body').append(`
                                <tr>
                                    <td style='width:2px;' class='text-center'>
                                    <span class="status status-lg status-away"></span>
                                    </td>
                                    <td class='text-left'>
                                        Empresas > ${company.fantasy_name}
                                    </td>
                                    <td class='text-center'>
                                        <a class='btn' style='color:darkorange;' href='/companies/${company.id_code}/edit?type=${company.type}' target='_blank'><b><i class="fa fa-pencil-square-o mr-2" aria-hidden="true"></i>Atualizar</b></a>
                                    </td>
                                </tr>
                            `);
                    }
                    $('#modal-peding-data').modal('show');
                }
            }
        });
    }
    function getDataDashboard() {
        loadOnAny('.page-content');
        $.ajax({
            method: "GET",
            url: `/api/dashboard${window.location.search}`,
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
                        if (data.companies[i].company_type == '1') {
                            $('#company').append('<option value="' + data.companies[i].id_code + '">Pessoa física</option>')
                        } else {
                            $('#company').append('<option value="' + data.companies[i].id_code + '">' + data.companies[i].fantasy_name + '</option>')
                        }
                    }

                    updateValues();

                    $(".content-error").hide();
                    $('#company-select').show();
                } else {
                    loadOnAny('.page-content', true);
                    $(".content-error").show();
                    $('#company-select, .page-content').hide();
                }

                if (!data.userTerm) {
                    $('#modal-user-term').modal('show');
                }

                $("#accepted-terms").unbind('click');
                $("#accepted-terms").on('click', function () {
                    $.ajax({
                        method: "POST",
                        url: "/api/terms",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        data: {},
                        error: function error(response) {
                            errorAjaxResponse(response);
                        },
                        success: function success(data) {
                            $('#modal-user-term').modal('hide');
                        }
                    });
                });
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

                updateTrackings(data.trackings);
                updateChargeback(data.chargeback_tax);
                updateTickets(data.tickets);
                updateNews(data.news);
                updateReleases(data.releases);
                loadOnAny('.page-content', true);
            }
        });
    }

    function updateChargeback(value) {
        $('.circle').circleProgress({
            size: 125,
            startAngle: -Math.PI / 2,
            value: value / 100,
            fill: {
                gradient: ["#F76B1C", "#FA6161"]
            }
        });

        $('.circle strong').addClass('loaded')
            .text(parseFloat(value).toFixed(2) + '%');
    }

    function updateTrackings(trackings) {
        let last10Days = trackings.last_10_days;
        let last30Days = trackings.last_30_days;
        let total = trackings.total;

        $('#tracking-10-days').html(last10Days > 4 ? last10Days + '%' : '')
            .addClass(last10Days <= 66.66 ? last10Days <= 33.33 ? 'bg-danger' : 'bg-warning' : '')
            .animate({width: last10Days + "%"});

        $('#tracking-30-days').html(last30Days > 4 ? last30Days + '%' : '')
            .addClass(last30Days <= 66.66 ? last30Days <= 33.33 ? 'bg-danger' : 'bg-warning' : '')
            .animate({width: last30Days + "%"});

        $('#tracking-total').html(total > 4 ? total + '%' : '')
            .addClass(total <= 66.66 ? total <= 33.33 ? 'bg-danger' : 'bg-warning' : '')
            .animate({width: total + "%"});

        if (total > 0) {
            if (last10Days < 40 || last30Days < 90) {
                let icon = $('#alert-trackings');
                icon.show();
                setInterval(function () {
                    if (icon.hasClass('text-danger')) {
                        icon.removeClass('text-danger');
                    } else {
                        icon.addClass('text-danger');
                    }
                }, 1000);
            }
        }
    }

    function updateTickets(data) {
        $('#open-tickets').text(data.open || 0);
        $('#closed-tickets').text(data.closed || 0);
        $('#mediation-tickets').text(data.mediation || 0);
        $('#total-tickets').text(data.total);
    }

    function updateNews(data) {

        $('#carouselNews .carousel-inner').html('');
        $('#carouselNews .carousel-indicators').html('');

        if (!isEmpty(data)) {

            for (let i = 0; i < data.length; i++) {

                let active = i === 0 ? 'active' : '';

                let slide = `<div class="carousel-item ${active}">
                                 <div class="card shadow news-background">
                                     <div class="card-body p-md-60 d-flex flex-column justify-content-center" style="height: 354px">
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

            $('#news-col').show();
        } else {
            $('#news-col').hide();
        }
    }

    function updateReleases(data) {

        $('#releases-div').html('');

        if (!isEmpty(data)) {
            $.each(data, function (index, value) {
                let item = `<div class="d-flex align-items-center my-15">
                                <div class="release-progress" id="${index}">
                                    <strong>${value.progress}%</strong>
                                </div>
                                <span class="ml-2">${value.release}</span>
                            </div>`;
                $('#releases-div').append(item);

                updateReleasesProgress(index, value.progress);
            });

            $('#releases-col').show();
        } else {
            $('#releases-col').hide();
        }
    }

    function updateReleasesProgress(id, value) {

        let circle = $('#' + id);

        let color = '';
        switch (true) {
            case value <= 33:
                color = '#FFA040';
                break;
            case value > 33 && value <= 66:
                color = '#FF6F00';
                break;
            default:
                color = '#C43E00';
                break;
        }

        circle.circleProgress({
            size: 55,
            startAngle: -Math.PI / 2,
            thickness: 6,
            value: value / 100,
            fill: {
                color: color,
            }
        });
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });
});
