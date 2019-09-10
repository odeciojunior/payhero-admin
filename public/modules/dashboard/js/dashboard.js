$(document).ready(function () {

    updateValues();
    loading('#cardPendente', '#loaderCard');
    loading('#cardAntecipavel', '#loaderCard');
    loading('#cardDisponivel', '#loaderCard');
    loading('#cardTotal', '#loaderCard');

    $("#company").on("change", function () {
        updateValues();
    });

    function updateValues() {

        $.ajax({
            method: "POST",
            url: "/dashboard/getvalues",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {company: $('#company').val()},
            error: function error() {
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));
                }
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
});
