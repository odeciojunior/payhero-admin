$(document).ready(function () {

    updateForm();

    $("#country").on("change", function () {
        updateForm();
    });

    function updateForm() {

        $("#store_form").html('');

        $.ajax({
            method: "POST",
            url: "/companies/getcompanyform/",
            dataType: "json",
            data: {
                country: $("#country").val()
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#store_form").html(response);

                var options = {
                    onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
                        var masks = ['000.000.000-000', '00.000.000/0000-00'];
                        var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
                        $('#brazil_company_document').mask(mask, options);
                    }
                };

                $('#brazil_company_document').mask('000.000.000-000', options);
            }
        });
    }

    $("#create_form").on("submit", function (event) {
        event.preventDefault();

        $.ajax({
            method: "POST",
            url: "/api/companies",
            dataType: "json",
            data: $("#create_form").serialize(),
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom('success', response.message);
                window.location.replace('/companies/' + response.idEncoded + '/edit ');
            }
        });
    });
});
