$(document).ready(function(){

    initForm();

    function initForm() {
        let encodedId = extractIdFromPathName();

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

                alert(response.toSource());
            }
        });
    }


});



