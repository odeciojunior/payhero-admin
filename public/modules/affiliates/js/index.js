$(document).ready(function () {
    console.log('oi');
    $.ajax({
        method: "GET",
        url: "/api/affiliates/" + '321',
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: (response) => {
            loadingOnScreenRemove();
            errorAjaxResponse(response);
        },
        success: (response) => {
            loadingOnScreenRemove();
            index();
            alertCustom('success', response.message);
        }
    });
});
