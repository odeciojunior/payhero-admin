$(document).ready(function() {
    $('.company-navbar').change(function() {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $('#integration-actions').hide();
        $('#no-integration-found').hide();
        $('#project-empty').hide();
        loadOnAny('#content');
        updateCompanyDefault().done(function(data1) {
            getCompaniesAndProjects().done(function(data2) {
                companiesAndProjects = data2;
                index(false);
            });
        });
    });

    let companiesAndProjects = '';

    function index(loading = true) {
        if (loading) {
            loadingOnScreen();
        } else {
            loadOnAny('#content');
        }

        let hasProjects = true;
        // if (companiesAndProjects.company_default_projects) {
        //     $.each(companiesAndProjects.company_default_projects, function (i, project) {
        //         if (project.status == 1) hasProjects = true;
        //     });
        // }

        if (!hasProjects) {
            $('#integration-actions').hide();
            $('#no-integration-found').hide();
            $('#project-empty').show();
            loadingOnScreenRemove();
            loadOnAny('#content', true);
        } else {
            $.ajax({
                method: 'GET',
                url: '/api/apps/adooreicheckout',
                dataType: 'json',
                headers: {
                    Authorization: $('meta[name="access-token"]').attr('content'),
                    Accept: 'application/json',
                },
                error: (response) => {
                    loadOnAny('#content', true);
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $('#project_id, #select_projects_edit').html('');
                    fillSelectProject(companiesAndProjects, '#project_id, #select_projects_edit');
                    if (isEmpty(response.integrations)) {
                        $('#no-integration-found').show();
                    } else {
                        $('#content').html('');
                        let integrations = response.integrations[0];
                        renderIntegration(integrations);
                        $('#no-integration-found').hide();
                    }
                    $('#project-empty').hide();
                    $('#integration-actions').show();
                    if (loading) loadOnAny('#content', true);
                    loadingOnScreenRemove();
                },
            });
        }
    }

    getCompaniesAndProjects().done(function(data) {
        companiesAndProjects = data;
        index();
    });

    // Reset the integration modal
    function clearForm() {
        $('#token').val('');
        $('#webhook').val('');
    }

    // Draw the integration cards
    function renderIntegration(data) {
        $('#content').append(`
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 integration-card" id_code="${data.id_code}">
                <div class="d-flex align-items-center justify-content-center"   >
                    <img class="card-img-top img-fluid w-full" src="/build/global/img/adoorei.png"/>
                </div>
                <div class="card shadow card-edit" style='cursor:pointer;'>
                    <div class="card-body">
                        <div class='row'>
                            <div class='col-md-10'>
                                <p>Clique aqui consultar o token</p>
                                <p class="card-text sm">Criado em ${data.register_date}</p>
                            </div>
                            <div class='col-md-2'>
                                <a role='button' title='Excluir' class='delete-integration float-right mt-35' id_code="${data.id_code}">
                                    <img src="/build/global/img/icon-trash-new.svg" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Create
    $('#btn-add-integration').on('click', function() {
        $('.modal-title').html('Adicionar nova Integração com Adoorei Checkout');
        $('#bt_integration').addClass('btn-save');
        $('#bt_integration').removeClass('btn-update');
        $('#bt_integration').text('Adicionar integração');
        $('#bt_integration').show();
        $('#modal_add_integracao').modal('show');
        $('#form_update_integration').hide();
        $('#form_add_integration').show();
        clearForm();
    });

    // Edit
    $(document).on('click', '.card-edit', function() {
        $.ajax({
            method: 'GET',
            url: '/api/apps/adooreicheckout/',
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                const integration = response.integrations[0];
                const webhook = response.Webhooks[0];
                const xSignatureDiv = $('#x-signature-div');

                $('#token_edit').val(integration.access_token);
                $('#integration_id').val(integration.id_code);
                $('#webhook_edit').val(webhook.url ?? null);
                xSignatureDiv.hide();

                if (webhook.signature.length > 0) {
                    $('#x-signature-edit').val(webhook.signature);
                    xSignatureDiv.show();
                }

                $('.modal-title').html('Integração com Adoorei Checkout');
                $('#bt_integration').addClass('btn-update');
                $('#bt_integration').removeClass('btn-save');
                $('#bt_integration').text('Atualizar');
                $('#form_update_integration').show();
                $('#form_add_integration').hide();
                $('#modal_add_integracao').modal('show');
            },
        });
    });

    $('.btn-copy').on('click', function() {
        const button = $(this);
        const input = button.prev('input');
        copyTextToClipboard(input, 'Copiado com sucesso!');
    });

    // Store
    $(document).on('click', '.btn-save', function() {
        if ($('#token').val() === '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        let form_data = new FormData(document.getElementById('form_add_integration'));

        loadingOnScreen();
        let description = 'Adoorei_Checkout';
        let companyHash = $('.company-navbar').val();
        let platformEnum = 'ADOOREI_CHECKOUT';

        storeIntegration(description, companyHash, platformEnum)
            .then(function(response) {
                $.ajax({
                    method: 'POST',
                    url: '/api/apps/adooreicheckout',
                    dataType: 'json',
                    headers: {
                        Authorization: $('meta[name="access-token"]').attr('content'),
                        Accept: 'application/json',
                    },
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: form_data,
                    error: (response) => {
                        errorAjaxResponse(response);
                        loadingOnScreenRemove();
                    },
                    success: (response) => {
                        index();
                        loadingOnScreenRemove();
                        alertCustom('success', response.message);
                    },
                });
            })
            .catch(function(error) {
                loadingOnScreenRemove();
                console.error('Erro ao executar storeIntegration:', error);
            });
    });

    function storeIntegration(description, companyHash, platformEnum = null) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                method: 'POST',
                url: '/api/integrations',
                data: {
                    description: description,
                    company_id: companyHash,
                    platform_enum: platformEnum,
                },
                dataType: 'json',
                headers: {
                    Authorization: $('meta[name="access-token"]').attr('content'),
                    Accept: 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                    reject(response);
                },
                success: (response) => {
                    resolve(response);
                },
            });
        });
    }

    //update
    $(document).on('click', '.btn-update', function() {
        if ($('#webhook_edit').val() == '') {
            alertCustom('error', 'Webhook é obrigatório');
            return false;
        }
        let integrationId = $('#integration_id').val();
        let form_data = new FormData(
            document.getElementById('form_update_integration'),
        );

        $.ajax({
            method: 'POST',
            url: '/api/apps/adooreicheckout/' + integrationId,
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                index();
                alertCustom('success', response.message);
            },
        });
    });

    // Load delete modal
    $(document).on('click', '.delete-integration', function(e) {
        e.stopPropagation();
        let id = $(this).attr('id_code');
        $('#modal-delete-integration .btn-delete').attr('id_code', id);
        $('#modal-delete-integration').modal('show');
    });

    // Destroy
    $(document).on('click', '#modal-delete-integration .btn-delete', function(e) {
        e.stopPropagation();
        let id_code = $(this).attr('id_code');

        $.ajax({
            method: 'DELETE',
            url: '/api/apps/adooreicheckout/' + id_code,
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                // Remove the integration card from the screen
                $(`.integration-card[id_code="${id_code}"]`).remove();
                index();
                alertCustom('success', response.message);
            },
        });
    });
});
