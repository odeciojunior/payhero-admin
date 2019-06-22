$(document).ready(function () {

    updateForm();

    $("#country").on("change", function () {
        updateForm();
    });

    function updateForm() {

        $("#store_form").html('');

        $.ajax({
            method: "GET",
            url: "/empresas/getformcadastrarempresa/" + $("#country").val(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $('.loading').css("visibility", "hidden");
            },
            success: function (data) {
                $('.loading').css("visibility", "hidden");
                $("#store_form").html(data);

                $("#routing_number").on("blur", function () {

                    $.ajax({
                        method: "GET",
                        url: "https://www.routingnumbers.info/api/data.json?rn=" + $("#routing_number").val(),
                        success: function (data) {
                            if (data.message == 'OK') {
                                $("#bank").val(data.customer_name);
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                });
            },
        });
    }

});
