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
            data: {
                country: $("#country").val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                $("#store_form").html(response);

                var options = {
                    onKeyPress: function (identificatioNumber, e, field, options) {
                        var masks = ['000.000.000-000', '00.000.000/0000-00'];
                        var mask = (identificatioNumber.length > 14) ? masks[1] : masks[0];
                        $('#brazil_company_document').mask(mask, options);
                    }
                };

                $('#brazil_company_document').mask('000.000.000-000', options);
            },
        });
    }

    $("#create_form").on("submit", function (event) {
        event.preventDefault();

        $.ajax({
            method: "POST",
            url: "/companies",
            data: $("#create_form").serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                alertCustom('success', response.message);
                window.location.replace(response.redirect);
            },
        });

    })

});
