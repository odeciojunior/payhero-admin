$(() => {

    index();

    function index() {
        $.ajax({
            url: '/api/projects',
            error: () => {
                alertCustom('error', 'Erro ao exibir projetos')
            },
            success: (response) => {
                console.log(response)
                if (response.length) {
                    $.each(response, (key, project) => {
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
            }
        });
    }


});
