$(document).ready(function () {

    getDataDashboard();
    loading('#cardPendente', '#loaderCard');
    loading('#cardAntecipavel', '#loaderCard');
    loading('#cardDisponivel', '#loaderCard');
    loading('#cardTotal', '#loaderCard');

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
                loadOnAny('.page-content', true);
                if (!isEmpty(data.companies)) {
                    for (let i = 0; i < data.companies.length; i++) {
                        $('#company').append('<option value="' + data.companies[i].id_code + '">' + data.companies[i].fantasy_name + '</option>')
                    }
                    $(".moeda").html(data.values.currency);
                    $("#pending_money").html(data.values.pending_balance);
                    $("#antecipation_money").html(data.values.antecipable_balance);
                    $("#available_money").html(data.values.available_balance);
                    $("#total_money").html(data.values.total_balance);
                    $("#today_money").html(data.values.today_balance);

                    $('#total_sales_approved').text(data.values.total_sales_approved);
                    $('#total_sales_chargeback').text(data.values.total_sales_chargeback);
                    updateProgressBar(data.values.chargeback_tax);

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
            }
        });
    }

    function updateProgressBar(value){
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

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });
});
