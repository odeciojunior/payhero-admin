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
            url: "https://app.cloudfox.net/api/dashboard",
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
                if (data.companies.length) {
                    for (let i = 0; i < data.companies.length; i++) {
                        $('#company').append('<option value="' + data.companies[i].id_code + '">' + data.companies[i].fantasy_name + '</option>')
                    }
                    $(".moeda").html(data.values.currency);
                    $("#pending_money").html(data.values.pending_balance);
                    $("#antecipation_money").html(data.values.antecipable_balance);
                    $("#available_money").html(data.values.available_balance);
                    $("#total_money").html(data.values.total_balance);
                    $(".content-error").hide();
                } else {
                    $(".content-error").show();
                }
                loadOnAny('.page-content', true);
            }
        });
    }

    function updateValues() {

        $.ajax({
            method: "POST",
            url: $('meta[name="current-url"]').attr('content') + "/api/dashboard/getvalues",
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
                $("#antecipation_money").html(data.antecipable_balance);
                $("#available_money").html(data.available_balance);
                $("#total_money").html(data.total_balance);
            }
        });
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });
});
