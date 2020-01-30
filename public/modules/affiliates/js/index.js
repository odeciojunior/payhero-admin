$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    getProjectData();

    function getProjectData() {
        loadOnAny('.page');
        $.ajax({
            method: "GET",
            url: "/api/affiliates/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('.page', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadOnAny('.page', true);
                $('.page-content').show();
                $('.project-header').html(response.data.name);
                $('.project-image').prop('src', response.data.photo);
                $('#created_by').html(`Criado por: ${response.data.user_name}`);
                $('.percentage-affiliate').html(` <strong >Porcentagem de afiliado: <span class='green-gradient'>${response.data.percentage_affiliates}%</span></strong>`);
                $('.text-terms').html(response.data.terms_affiliates);
                $('.text-about-project').html(response.data.description);
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
