$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    getProjectData();

    function getProjectData() {
        loadOnAny('.page-content');
        $.ajax({
            method: "GET",
            url: "/api/affiliates/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadOnAny('.page-content', true);
                $('.page-content').show();
                $('.project-header').html(response.data.name);
                $('.project-image').prop('src', response.data.photo);
                $('#created_by').html(`Produtor: ${response.data.user_name}`);
                $('.text-about-project').html(response.data.description);
                $('.url_page').html(` <strong >URL da página principal: <a href='${response.data.url_page}' target='_blank'>${response.data.url_page}</a></strong>`);
                $('.created_at').html(` <strong >Criado em: ${response.data.created_at}</strong>`);
                if (response.data.automatic_affiliation) {
                    $('.div-button').html('<button id="btn-store-affiliation" class="btn btn-primary" data-type="confirm">Confimar Afiliação</button>');
                } else {
                    $('.div-button').html('<button id="btn-store-affiliation" class="btn btn-primary" data-type="request">Solicitar Afiliação</button>');
                }
                if (response.data.percentage_affiliates != '') {
                    $('.percentage-affiliate').html(`<strong >Porcentagem de afiliado: <span class='green-gradient'>${response.data.percentage_affiliates}%</span></strong>`);
                }
                if (response.data.terms_affiliates != '') {
                    $('.text-terms').html(response.data.terms_affiliates);
                } else {
                    $('.text-terms').html('<strong >Não possui termos de afiliação.</strong>');
                }
                if (response.data.contact != '') {
                    $('.contact').html(`<strong>E-mail: ${response.data.contact}</strong>`);
                }
                if (response.data.support_phone != '') {
                    $('.support_phone').html(`<strong>Telefone: ${response.data.support_phone}</strong>`);
                }
            }
        });
    }

    $(document).on('click', '#btn-store-affiliation', function () {
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/affiliates",
            dataType: "json",
            data: {
                project_id: projectId,
                type: $(this).data('type'),
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
