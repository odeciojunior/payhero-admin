$(window).on('load', function () {

    $(document).on("change", "#transfers_company_select, #transfers_company_select_mobile", function () {
        let value = $(this);
        let option = value.find('option:selected').val();
        let text = value.find('option:selected').text();

        $(".transfers_company_select .sirius-select-text, .transfers_company_select_mobile .sirius-select-text").text(text)
        $('#custom-input-addon').val('');
        updateBalances(option);
        if (value.children("option:selected").attr('country') != 'brazil') {
            $("#col_transferred_value").show();
        } else {
            $("#col_transferred_value").hide();
        }
    });

});

window.updateBalances = function(company_code = '') {

    loadOnAny(".number", false, {
        styles: {
            container: {
                minHeight: "32px",
                height: "auto",
            },
            loader: {
                width: "20px",
                height: "20px",
                borderWidth: "4px",
            },
        },
    });

    loadOnTable('#withdrawals-table-data', '#withdrawalsTable');

    let companyCode = company_code ? company_code :  $("#transfers_company_select option:selected").val();
    $.ajax({
        url: "/api/finances/getbalances",
        type: "GET",
        data: {
               company: companyCode,
               gateway_id: gatewayCode
        },
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },

        error: (response) => {
            loadOnAny(".number", true);
            errorAjaxResponse(response);
        },
        success: (response) => {
            loadOnAny(".number", true);
            $('.removeSpan').remove();

            $(".available-balance").html(removeMoneyCurrency(response.available_balance));
            $('.available-balance-mobile').html(removeMoneyCurrency(response.available_balance));
            $(".pending-balance").html(removeMoneyCurrency(response.pending_balance));
            $(".total-balance").html(removeMoneyCurrency(response.total_balance));
            $(".blocked-balance").html(removeMoneyCurrency(response.blocked_balance));
            $(".debt-balance").html(removeMoneyCurrency(response.pending_debt_balance));

            if(onlyNumbers(response.pending_debt_balance) != "000"){
                $("#balance-resumes > .col-md-4").removeClass('col-md-4').addClass('col-md-3');
                $("#card-debt-balance").show();
            }

            $(".loading").remove();

            $("#div-available-money, #div-available-money_m").off('click');
            $("#div-available-money, #div-available-money_m").on("click", function () {
                if(response.available_balance.charAt(0) == '-' || onlyNumbers(response.available_balance) == "000") {
                    return;
                }
                $("#custom-input-addon").val(removeMoneyCurrency(response.available_balance));
            });

            loadWithdrawalsTable();
        }
    });

}
