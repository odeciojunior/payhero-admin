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
