/*
$(function () {

    $("#btn-disponible-antecipation").on('click', function () {
        // loading("#balance-after-anticipation",'');
        $('#balance-after-anticipation').html("<span class='loaderSpan' >" + "</span>")
        let company = $("#transfers_company_select").val();
        $("#tax-value").html('');

        $.ajax({
            method: 'GET',
            url: '/api/anticipations/' + company,
            headers: {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content'),
            },
            error: function (response) {
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else if (response.status === 400) {
                    $("#balance-after-anticipation").html(response.responseJSON.data['valueAntecipable'] + ',00');
                    $("#tax-value").html(response.responseJSON.data['taxValue'] + ',00');
                    alertCustom("error", response.responseJSON.message)
                } else {
                    alertCustom("error", response.responseJSON.message)
                }
            },
            success: function (response) {
                $("#balance-after-anticipation").html(response.data['valueAntecipable']);
                $("#tax-value").html(response.data['taxValue']);
            }
        });

    });

    $("#btn-anticipation").unbind();
    $("#btn-anticipation").on('click', function () {
        loadingOnScreen();
        let company = $("#transfers_company_select").val();

        $.ajax({
            method: 'POST',
            url: '/api/anticipations',
            data: {company: company},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            error: function (response) {
                loadingOnScreenRemove();
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else if (response.status === 400) {
                    $("#balance-after-anticipation").html(response.responseJSON.data['valueAntecipable']);
                    $("#tax-value").html(response.responseJSON.data['taxValue']);
                    alertCustom("error", response.responseJSON.message)
                } else {
                    alertCustom("error", response.responseJSON.message)
                }
            },
            success: function (response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                $.getScript('modules/finances/js/index.js', function () {
                    updateBalances()
                });

            }
        })

    });

});
*/
