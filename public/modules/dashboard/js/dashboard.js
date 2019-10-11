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
            method: "POST",
            url: "/api/dashboard/resume",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny('.page-content', true);
                if (!isEmpty(response.companies)) {

                    for (let i = 0; i < response.companies.length; i++) {
                        $('#company').append('<option value="' + response.companies[i].id_code + '">' + response.companies[i].fantasy_name + '</option>')
                    }
                    console.log($('#company').val());
                    let resumeData = response.data.filter(function (company) {
                        console.log(company.id_code);
                        return company.id_code == $('#company').val();
                    });
                    if (isEmpty(resumeData)) {
                        loadOnAny('.page-content', true);
                        errorAjaxResponse("Ocorreu um erro inesperado!");
                    } else {
                        let resume = resumeData[0];
                        console.log(resume);
                        $(".moeda").html(resume.currency);
                        $("#pending_money").html(resume.pending_balance);
                        $("#antecipation_money").html(resume.antecipable_balance);
                        $("#available_money").html(resume.available_balance);
                        $("#total_money").html(resume.total_balance);
                        $(".content-error").hide();
                        $('#company-select').show();
                    }
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
            url: "/api/dashboard/resume",
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
                let resume = response.data[0];
                $(".moeda").html(resume.currency);
                $("#pending_money").html(resume.pending_balance);
                $("#antecipation_money").html(resume.antecipable_balance);
                $("#available_money").html(resume.available_balance);
                $("#total_money").html(resume.total_balance);
            }
        });
    }

    $("#closeWelcome").click(function () {
        $("#cardWelcome").slideUp("600");
    });
});
