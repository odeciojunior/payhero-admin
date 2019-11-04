$(document).ready(function () {
    console.log("oi");
    index();
    function index() {
        $.ajax({
            method: "GET",
            url: "/api/collaborators",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (isEmpty(response.data)) {
                    $("#content-error").css('display', 'block');
                } else {
                    $("#content-error").hide();
                    $("#card-table-collaborators").css('display', 'block');
                    // $("#card-invitation-data").css('display', 'block');

                    // $("#text-info").css('display', 'block');
                    $("#card-table-collaborators").css('display', 'block');
                    $("#table-body-collaborators").html('');

                    $.each(response.data, function (index, value) {

                        data = '';
                        data += '<tr>';
                        data += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled></button></td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.email + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.created_at + '</td>';
                        data += '</tr>';
                        $("#table-body-collaborators").append(data);
                    });
                }
            }
        });
    }

});
