$(document).ready(function(){

    initForm();

    function initForm() {

        var encodedId = extractIdFromPathName();

        $.ajax({
            method: "GET",
            url: "/api/companies/" + encodedId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                $.each(response.banks, function(index, value){
                    $("#bank").append("<option value='" + value.code + "'>" + value.name + "</option>")
                });

                $("#bank").val(response.company.bank);
                $("#agency").val(response.company.agency);
                $("#agency_digit").val(response.company.agency_digit);
                $("#account").val(response.company.account);
                $("#account_digit").val(response.company.account_digit);
            }
        });
    }

    $("#update_bank_data").on("click", function (event) {
        event.preventDefault();
        let form_data = new FormData(document.getElementById('company_update_bank_form'));
        loadingOnScreen();

        var encodedId = extractIdFromPathName();

        $.ajax({
            method: "POST",
            url: "/api/companies/" + encodedId,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });


});

