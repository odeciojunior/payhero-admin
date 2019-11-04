$(() => {

    index();

    function index() {
        loadOnAny('#data-table-projects');
        $.ajax({
            url: '/api/projects',
            data: {
                'status': 'active'
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('#data-table-projects', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (response.data.length) {
                    $.each(response.data, (key, project) => {
                        let data = `<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                                        <div class="card">
                                            ${project.shopify_id != null ? '<div class="ribbon"><span>Shopify <a class="ribbon-shopify-default"></a></span></div>' : ''}
                                            <img class="card-img-top" src="${project.photo ? project.photo : '/modules/global/img/projeto.png'}" alt="${project.name}">
                                            <div class="card-body">
                                                <h5 class="card-title">${project.name}</h5>
                                                <p class="card-text sm">Criado em ${project.created_at}</p>
                                                <a href="/projects/${project.id}" class="stretched-link"></a>
                                            </div>
                                        </div>
                                    </div>`;
                        $('#data-table-projects').append(data);
                        $('#btn-add-project').show();
                    });
                } else {
                    $('#data-table-projects').hide();
                    $('.content-error').show();
                }
                loadOnAny('#data-table-projects', true);
            }
        });
    }

});
