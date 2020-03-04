$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    let countCompanies;
    getProjectData();
    getCompanyData();

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
                if (response.data.status_url_affiliates) {
                    $('.div-project').show();
                    $('.project-header').html(`Afiliação no projeto ${response.data.name}`);
                    $('.project-image').prop('src', `${response.data.photo ? response.data.photo : '/modules/global/img/projeto.png'}`);
                    $('#created_by').html(`Produtor: ${response.data.user_name}`);
                    $('.text-about-project').html(response.data.description);
                    if(response.data.url_page != '' && response.data.url_page != null) {
                        $('.url_page').html(` <strong >URL da página principal: <a href='${response.data.url_page}' target='_blank'>${response.data.url_page}</a></strong>`);
                    } else {
                        $('.url_page').html('');
                    }
                    $('.created_at').html(` <strong >Criado em: ${response.data.created_at}</strong>`);
                    if (!response.data.producer) {
                        if (response.data.affiliatedMessage != '') {
                            $('.div-button').html(`<div class="alert alert-info">${response.data.affiliatedMessage}</div>`);
                            $('.div-button').toggleClass('text-center');
                        } else {
                            if (response.data.automatic_affiliation) {
                                $('.div-button').html('<button id="btn-affiliation" class="btn btn-primary" data-type="affiliate">Confimar Afiliação</button>');
                            } else {
                                $('.div-button').html('<button id="btn-affiliation" class="btn btn-primary" data-type="affiliate_request">Solicitar Afiliação</button>');
                            }
                        }
                    }
                    if (response.data.percentage_affiliates != '') {
                        $('.percentage-affiliate').html(`<strong>Porcentagem de afiliado: <span class='green-gradient'>${response.data.percentage_affiliates}%</span></strong>`);
                    }
                    console.log(response.data.cookie_duration);
                    if (response.data.cookie_duration != '') {
                        if (response.data.cookie_duration == 0) {
                            $('.cookie_duration').html(` <strong>Duração do cookie: <span class='green-gradient'>Eterno</span></strong>`);
                        }  else if (response.data.cookie_duration >= 1) {
                            $('.cookie_duration').html(` <strong>Duração do cookie: <span class='green-gradient'>${response.data.cookie_duration} dias</span></strong>`);
                        }
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
                    let dataRelease = '<strong>Dias para liberar dinheiro<br>';
                    dataRelease += `Boleto: <span class='green-gradient'>${response.data.billet_release_days} dias</span><br>`;
                    dataRelease += `Cartão de crédito: <span class='green-gradient'>${response.data.credit_release_days} dias</span><br>`;
                    dataRelease += `Cartão de débito: <span class='green-gradient'>${response.data.debit_release_days} dias</span>`;
                    dataRelease += '</strong>';
                    $('.release_days').html(dataRelease);
                } else {
                    // $('.div-disabled-url-affiliates').show();
                    swal({
                        title: 'Esse projeto não está disponível para afiliação',
                        type: 'warning',
                        confirmButtonColor: "#EC6421",
                        confirmButtonClass: "btn btn-warning",
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.replace('/dashboard');
                    })
                }
            }
        });
    }

    function getCompanyData() {
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
                countCompanies = response.data.length;
                for (let company of response.data) {
                    $('#companies').append(`<option value='${company.id}'>
                                                ${(company.company_document_status == 'pending' ? company.name + ' (documentos pendentes)' : company.name)}
                                            </option>`);
                }
            }
        });

    }
    $(document).on('click', '#btn-affiliation', function () {
        if (countCompanies == 0) {
            $("#modal-not-companies").modal('show');
        } else {
            $('#modal_store_affiliate').modal('show');
        }
    });

    $(document).on('click', '#btn-store-affiliation', function () {
        loadingOnScreen();
        let type = $('#btn-affiliation').data('type');
        $.ajax({
            method: "POST",
            url: "/api/affiliates",
            dataType: "json",
            data: {
                project_id: projectId,
                type: type,
                company_id: $('#companies').val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                $('#modal_store_affiliate').modal('hide');
                loadingOnScreenRemove();
                // errorAjaxResponse(response);
            },
            success: (response) => {
                $('#modal_store_affiliate').modal('hide');
                loadingOnScreenRemove();
                getProjectData();
                alertCustom('success', response.message);
                if (response.type == 'affiliate') {
                    window.location = "/projects";
                } else {
                    setTimeout(function(){ window.location = "/dashboard" }, 4000);
                }
            }
        });
    });

});
