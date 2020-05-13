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
                        let data = `<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 name_project" data-id="${project.id}">
                                        <div class="card">
                                            ${project.shopify_id != null && !project.affiliated ? '<div class="ribbon"><span>Shopify <a class="ribbon-shopify-default"></a></span></div>' : ''}
                                            ${project.affiliated ? '<div class="ribbon-left"><span>Afiliado</span></div>' : ''}
                                            <img class="card-img-top" src="${project.photo ? project.photo : '/modules/global/img/projeto.png'}" alt="${project.name}">
                                            <div class="card-body">
                                                <h5 class="card-title">${project.name}</h5>
                                                <p class="card-text sm">Criado em ${project.created_at}</p>
                                                <a href="/projects/${project.id}${project.affiliated ? '/' + project.affiliate_id : ''}" class="stretched-link"></a>
                                            </div>
                                        </div>
                                    </div>`;
                        $('#data-table-projects').append(data);
                        $('#btn-add-project').show();
                    });

                    Sortable.create(document.getElementById('data-table-projects'), {
                        onEnd: function(evt) {
                            var orderProjects = [];
                            var listCompanies = $('#data-table-projects');
                            $(listCompanies).find(".name_project").each(function(index, tr) {
                                orderProjects.push($(tr).data('id'));
                            });

                            $.ajax({
                                method: "POST",
                                url: "/api/projects/updateorder",
                                dataType: "json",
                                data: { order: orderProjects },
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: function (response) {
                                    loadingOnScreenRemove();
                                    errorAjaxResponse(response);
                                },
                                success: function success(data) {
                                    loadingOnScreenRemove();
                                    alertCustom("success", data.message);
                                }
                            });
                        }
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
