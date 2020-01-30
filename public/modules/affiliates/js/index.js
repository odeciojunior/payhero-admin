$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    console.log('oi');
    $.ajax({
        method: "GET",
        url: "/api/affiliates/" + projectId,
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
