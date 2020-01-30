$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    getProjectData();

    function getProjectData() {
        loadOnAny('.container');

        $.ajax({
            method: "GET",
            url: "/api/affiliates/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('.container', true);
                // loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadOnAny('.container', true);
                $('.project-header').html(response.data.name);
                $('.project-image').prop('src', response.data.photo);
                $('#created_by').html(`Criado por: ${response.data.user_name}`);
                $('.text-about-project').html(response.data.description);
                // loadingOnScreenRemove();
                // alertCustom('success', response.message);
            }
        });
    }
    $('#btn-affiliation-request').on('click', function () {
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/affiliates",
            dataType: "json",
            data: {
                project_id: projectId,
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });
});
