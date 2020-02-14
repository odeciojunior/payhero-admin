$(document).ready(function () {
    function getTickets() {
        $.ajax({
            method: "GET",
            url: "/api/companies/usercompanies",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
            },
            success: (response) => {

            }
        });
    }
});
