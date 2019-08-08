$(document).ready(function () {

    updateValues();
    loading('#cardPendente','#loaderCard');
    loading('#cardAntecipavel','#loaderCard');
    loading('#cardDisponivel','#loaderCard');
    loading('#cardTotal','#loaderCard');

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
            error: function () {
                //
            },
            success: function (data) {

                $(".moeda").html(data.currency);
                $("#pending_money").html(data.pending_balance);
                $("#antecipation_money").html(data.antecipable_balance);
                $("#available_money").html(data.available_balance);
                $("#total_money").html(data.total_balance);
            }
        });

    }
});
