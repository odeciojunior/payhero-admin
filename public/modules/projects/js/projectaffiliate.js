$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-2);

    // COMPORTAMENTOS DA TELA
    $('#tab-info').click(() => {
        show();
    });

    $("#tab_configuration").click(function () {
        $("#image-logo-email").imgAreaSelect({remove: true});
        $("#previewimage").imgAreaSelect({remove: true});
    });

    $('.toggler').on('click', function () {

        let target = $(this).data('target');

        if ($(target).hasClass('show')) {
            $(this).find('.showMore').html('add');
        } else {
            $(this).find('.showMore').html('remove');
        }
    });

    // FIM - COMPORTAMENTOS DA TELA

    show();

    //carrega detalhes do projeto
    function show() {

        loadOnAny('#tab_info_geral .card', false, {
            styles: {
                container: {
                    minHeight: '250px'
                }
            }
        });

        $.ajax({
            url: '/api/projects/' + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: (response) => {

                let project = response.data;
                $('.page-title, .title-pad').text(project.name);
                $('#show-photo').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.png');
                $('#created_at').text('Afiliado em ' + project.affiliate_date);
                if (project.visibility === 'public') {
                    $('#show-visibility').text('Público').addClass('badge-primary');
                } else {
                    $('#show-visibility').text('Privado').addClass('badge-danger');
                }
                if (project.status == '1') {
                    $('#show-status').text('Ativo').addClass('badge-primary');
                } else {
                    $('#show-status').text('Inativo').addClass('badge-danger');
                }
                $('#show-description').text(project.description);

                loadOnAny('#tab_info_geral .card', true);
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadOnAny('#tab_info_geral .card', true);
            }
        });
    }

});