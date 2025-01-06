$(document).ready(function() {
    $('.company-navbar').change(function() {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        loadOnAny('#page-webhooks', false);
        updateCompanyDefault().done(function(data1) {
            getCompaniesAndProjects().done(function(data2) {
                companiesAndProjects = data2;
                $('.company_name').val(companiesAndProjects.company_default_fullname);
                refreshWebhooks(1, false);
            });
        });
    });

    var companiesAndProjects = '';

    getCompaniesAndProjects().done(function(data) {
        companiesAndProjects = data;
        $('.company_name').val(companiesAndProjects.company_default_fullname);
        refreshWebhooks();
    });

    function refreshWebhooks(page = 1, reload = true) {
        $.ajax({
            method: 'GET',
            url: '/api/webhooks?resume=true&page=' + page + '&company_id=' + $('.company-navbar').val(),
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            beforeSend: function() {
                if (reload) {
                    loadOnAny('#page-webhooks', false);
                }
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    $('.page-header').find('.store-webhook').css('display', 'none');
                    $('#content-error').find('.store-webhook').css('display', 'block');
                    $('#content-error').css('display', 'block');
                    $('#content-script').css('display', 'none');
                    $('#card-table-webhook').css('display', 'none');
                    $('#card-webhook-data').css('display', 'none');
                } else {
                    $('.page-header').find('.store-webhook').css('display', 'block');
                    $('#content-error').find('.store-webhook').css('display', 'none');
                    $('#content-error').hide();
                    $('#content-script').show();
                    updateWebhookTableData(response);
                    pagination(response, 'webhooks');

                }
            },
            complete: function() {
                loadOnAny('#page-webhooks', true);
                loadingOnScreenRemove();
            },
        });
    }

    function updateWebhookTableData(response) {
        $('#content-script').css('display', 'block');
        $('#card-table-webhook').css('display', 'block');
        $('#card-webhook-data').css('display', 'block');
        $('#table-body-webhook').html('');

        $.each(response.data, function(index, value) {
            let dados = '';
            dados += '<tr>';
            dados += '<td class="ellipsis-text">';
            dados += value.description + '<br>';
            dados += '<span class="subdescription font-size-12">Criada em ' + value.register_date + '</span>';
            dados += '</td>';
            dados += '<td class="ellipsis-text">';
            dados += value.url + '<br>';
            dados += '<span class="subdescription font-size-12">' + value.company_name + '</span>';
            dados += '</td>';
            dados += '<td class="text-right">';
            dados +=
                '<button type="button" class="btn pointer edit-webhook" style="background-color:transparent;" webhook="' +
                value.id +
                '"' +
                ' title="Editar webhook"><span class=""><img src="/build/global/img/icon-eye.svg"></span></button>';
            dados +=
                '<button type="button" class="btn pointer delete-webhook" style="background-color:transparent;" webhook="' +
                value.id +
                '"' +
                ' title="Deletar webhook"><span class=""><img src="/build/global/img/icon-trash-tale.svg"></span></button>';
            dados += '</td>';
            dados += '</tr>';

            $('#table-body-webhook').append(dados);
        });
    }

    $('.store-webhook').on('click', function() {
        $('#modal-webhook').modal('show');
    });

    $('#btn-save-webhook').on('click', function() {
        let data = validate('store');
        data ? storeWebhook(data) : errorAjaxResponse('');
    });

    function storeWebhook(data) {
        $.ajax({
            method: 'POST',
            url: '/api/webhooks',
            data: {
                description: data.description,
                company_id: data.company_id,
                url: data.url,
            },
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            beforeSend: function() {
                loadOnAny('#modal-webhook #modal-loader', false);
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $('#modal-webhook').modal('hide');
                alertCustom('success', response.message);
                clearForm();
                refreshWebhooks();
            },
            complete: function() {
                loadOnAny('#modal-webhook #modal-loader', true);
            },
        });
    }

    $('#table-body-webhook').on('click', '.edit-webhook', function() {
        let webhookId = $(this).attr('webhook');
        $.ajax({
            method: 'GET',
            url: '/api/webhooks/' + webhookId,
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (!isEmpty(response.data)) {
                    let webhook = response.data;
                    $('#modal-edit-webhook #description_edit').val(webhook.description);
                    $('#modal-edit-webhook #url_edit').val(webhook.url);
                    $('#modal-edit-webhook #webhook_id').val(webhookId);
                    $('#modal-edit-webhook #x-signature-edit').val(webhook.signature);
                    $('#modal-edit-webhook').modal('show');
                } else {
                    alertCustom('error', 'Erro ao obter dados do webhook');
                }
            },
        });
    });

    $('#modal-edit-webhook').on('click', '#btn-edit-webhook', function() {
        let data = validate('update');
        data ? updateWebhook(data) : errorAjaxResponse('');
    });

    function updateWebhook(data) {
        let webhookId = $('#modal-edit-webhook #webhook_id').val();
        $.ajax({
            method: 'PUT',
            url: 'api/webhooks/' + webhookId,
            data: {
                description: data.description,
                company_id: data.company_id,
                url: data.url,
            },
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            beforeSend: function() {
                loadOnAny('#modal-edit-webhook #modal-loader', false);
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $('#modal-edit-webhook').modal('hide');
                alertCustom('success', response.message);
                clearForm();
                refreshWebhooks();
            },
            complete: function() {
                loadOnAny('#modal-edit-webhook #modal-loader', true);
            },
        });
    }

    function validate(event) {
        let description, company_id, url;

        if (event == 'store') {
            description = $('#modal-webhook #description').val();
            company_id = $('.company-navbar').val();
            url = $('#modal-webhook #url').val();
        } else if (event == 'update') {
            description = $('#modal-edit-webhook #description_edit').val();
            company_id = $('.company-navbar').val();
            url = $('#modal-edit-webhook #url_edit').val();
        }

        if (isEmpty(description)) {
            alertCustom('error', 'Digite uma descrição para seu webhook');
            return false;
        }

        if (isEmpty(company_id)) {
            alertCustom('error', 'Selecione uma empresa');
            return false;
        }

        if (isEmpty(url)) {
            alertCustom('error', 'Digite uma URL');
            return false;
        }

        return { description: description, company_id: company_id, url: url };
    }

    $('#table-body-webhook').on('click', '.delete-webhook', function() {
        $('#modal-delete-webhook #webhook_id').val($(this).attr('webhook'));
        $('#modal-delete-webhook').modal('show');
    });

    $('#modal-delete-webhook').on('click', '#btn-delete-webhook', function() {
        let webhookId = $('#modal-delete-webhook #webhook_id').val();
        $.ajax({
            method: 'DELETE',
            url: 'api/webhooks/' + webhookId,
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $('#modal-delete-webhook').modal('hide');
                alertCustom('success', response.message);
                clearForm();
                refreshWebhooks();
            },
        });
    });

    function pagination(response, model) {
        if (response.meta.last_page == 1) {
            $('#first_page').hide();
            $('#last_page').hide();
            $('#pagination-container-webhooks').removeClass('d-flex').addClass('d-none');

        } else {

            $('#pagination-' + model).html('');

            var first_page = '<button id=\'first_page\' class=\'btn nav-btn\'>1</button>';

            $('#pagination-' + model).append(first_page);

            if (response.meta.current_page == '1') {
                $('#first_page').attr('disabled', true).addClass('nav-btn').addClass('active');
            }

            $('#first_page').on('click', function() {
                refreshWebhooks(1);
            });

            for (x = 3; x > 0; x--) {
                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $('#pagination-' + model).append(
                    '<button id=\'page_' +
                    (response.meta.current_page - x) +
                    '\' class=\'btn nav-btn\'>' +
                    (response.meta.current_page - x) +
                    '</button>',
                );

                $('#page_' + (response.meta.current_page - x)).on('click', function() {
                    refreshWebhooks($(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page =
                    '<button id=\'current_page\' class=\'btn nav-btn active\'>' + response.meta.current_page + '</button>';

                $('#pagination-' + model).append(current_page);

                $('#current_page').attr('disabled', true).addClass('nav-btn').addClass('active');
            }
            for (x = 1; x < 4; x++) {
                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $('#pagination-' + model).append(
                    '<button id=\'page_' +
                    (response.meta.current_page + x) +
                    '\' class=\'btn nav-btn\'>' +
                    (response.meta.current_page + x) +
                    '</button>',
                );

                $('#page_' + (response.meta.current_page + x)).on('click', function() {
                    refreshWebhooks($(this).html());
                });
            }

            if (response.meta.last_page != '1') {
                var last_page = '<button id=\'last_page\' class=\'btn nav-btn\'>' + response.meta.last_page + '</button>';

                $('#pagination-' + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $('#last_page').attr('disabled', true).addClass('nav-btn').addClass('active');
                }

                $('#last_page').on('click', function() {
                    refreshWebhooks(response.meta.last_page);
                });
            }
            $('#pagination-container-webhooks').removeClass('d-none').addClass('d-flex');

        }
    }

    function clearForm() {
        $(
            '#modal-edit-webhook #webhook_id, #modal-delete-webhook #webhook_id, #description, #description_edit, #url, #url_edit',
        ).val('');
    }
});
