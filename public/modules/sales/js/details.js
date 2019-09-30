$(document).ready(function () {
    $("#sales_tab").css("min-width", $(window).width() / 2);

    $("#client_tab").css("min-width", $("#sales_tab").width());
    $("#products_tab").css("min-width", $("#sales_tab").width());

    $("#btn-edit-trackingcode").on('click', function () {
        $('.tracking-code').hide();
        $('.input-value-trackingcode').show();
        $('.btn-save-tracking').show();
        $('.btn-cancel-tracking').show();
    });

    $('.btn-cancel-tracking').on('click', function () {
        $('.tracking-code').show();
        $('.input-value-trackingcode').val('').hide();
        $('.btn-save-tracking').hide();
        $('.btn-cancel-tracking').hide();
    });

    $('.btn-save-tracking').on('click', function () {
        let trackingCode = $(".input-value-trackingcode").val();
        let referenceCode = $(this).attr('data-code');
        ajaxUpdateTracking(trackingCode, referenceCode);
    });

    function ajaxUpdateTracking(tracking, reference) {
        var delivery = currentDeliveryCode;
        var sale = currentSaleCode;

        $.ajax({
            method: 'POST',
            url: '/api/sales/update/trackingcode',
            data: {
                sale: sale,
                delivery: delivery,
                trackingCode: tracking,
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom("error", response.message)
                }
                $(".btn-cancel-tracking").click();
            },
            success: function (response) {
                $(".btn-cancel-tracking").click();
                $(".tracking-code-value").html(tracking);
                $('#btn-sent-tracking-user[data-code=' + reference + ']').show('slow');
                alertCustom('success', response.message);
            }
        });
    }

    $('#btn-sent-tracking-user').on('click', function () {
        let sale = currentSaleCode;

        $.ajax({
            method: 'POST',
            url: '/api/sales/update/trackingcode/' + sale,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom("error", response.message)
                }
            },
            success: function (response) {
                alertCustom('success', response.message);
            }
        });
    });
});
