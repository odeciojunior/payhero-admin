$(document).ready(function () {

    index();
    function index() {
        $.ajax({
            method: "GET",
            url: "/api/apps/activecampaign/",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (isEmpty(response.projects)) {
                    $('#project-empty').show();
                    $('#integration-actions').hide();
                } else {
                    $('.select-pad').html("");
                    let projects = response.projects;
                    for (let i = 0; i < projects.length; i++) {
                        $('.select-pad').append('<option value="' + projects[i].id + '">' + projects[i].name + '</option>');
                    }
                    if (isEmpty(response.integrations)) {
                        $("#no-integration-found").show();
                        $('#content').html("");
                    } else {
                        $('#content').html("");
                        let integrations = response.integrations;
                        for (let i = 0; i < integrations.length; i++) {
                            renderIntegration(integrations[i]);
                        }
                        $("#no-integration-found").hide();
                    }
                    $('#project-empty').hide();
                    $('#integration-actions').show();
                }
            }
        });
    }

    //checkbox
    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    //reset the intergation modal
    function clearForm() {
        $(':text').val('')
        $(':checkbox').prop('checked', true).val(1);
        $('.select-pad').prop("selectedIndex", 0).change();
    }

    //draw the integration cards
    function renderIntegration(data) {
        $('#content').append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=` + data.id + ` style='cursor:pointer;'>
                                    <a href="/apps/activecampaign/${data.id}" class="activecampaign-link">
                                        <img class="card-img-top img-fluid w-full" src=` + data.project_photo + ` onerror="this.onerror=null;this.src='/modules/global/img/produto.png';" alt="` + data.project_name + `"/>
                                    </a>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <a href="/apps/activecampaign/${data.id}" class="activecampaign-link">
                                                    <h4 class="card-title">` + data.project_name + `</h4>
                                                    <p class="card-text sm">Criado em ` + data.created_at + `</p>
                                                </a>
                                            </div>
                                            <div class='col-md-2'>
                                                <div role='button' title='Excluir' class='delete-integration pointer float-right mt-35' project=` + data.id + `>
                                                    <i class='material-icons gradient'>delete_outline</i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
    }

    //create
    $('#btn-add-integration').on('click', function () {
        $(".modal-title").html('Adicionar nova Integração com ActiveCampaign');
        $("#bt_integration_add").addClass('btn-save');
        $("#bt_integration_add").removeClass('btn-update');
        $("#bt_integration_add").text('Adicionar integração');
        $("#modal_add_integracao").modal('show');
        $("#form_update_integration").hide();
        $("#form_add_integration").show();
        clearForm();
    });

    //edit
    // $(document).on('click', '.card-edit', function () {
        // $('.activecampaign-link').click();
        // $('.activecampaign-link-'+$(this).attr('project')).click();

// <a href="/projects/${project.id}" class="stretched-link"></a>

        // $(".modal-title").html('Editar nova Integração com ActiveCampaign');
        // $("#bt_integration_add").addClass('btn-update');
        // $("#bt_integration_add").removeClass('btn-save');
        // $("#bt_integration_add").text('Atualizar');
        // $("#form_update_integration").show();
        // $("#form_add_integration").hide();
        // $("#modal_add_integracao").modal('show');

        // $.ajax({
        //     method: "GET",
        //     url: "/api/apps/activecampaign/" + $(this).attr('project'),
        //     dataType: "json",
        //     headers: {
        //         'Authorization': $('meta[name="access-token"]').attr('content'),
        //         'Accept': 'application/json',
        //     },
        //     error: (response) => {
        //         errorAjaxResponse(response);
        //     },
        //     success: (response) => {
        //         $("#select_projects_edit").val(response.data.project_id);
        //         $('#integration_id').val(response.data.id);
        //         $("#link_edit").val(response.data.link);
        //     }
        // });
    // });

    //store
    $(document).on('click', '.btn-save', function () {
        if ($('#link').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        var form_data = new FormData(document.getElementById('form_add_integration'));

        $.ajax({
            method: "POST",
            url: "/api/apps/activecampaign",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                index();
                alertCustom('success', response.message);
            }
        });
    });

    //update
    $(document).on('click', '.btn-update', function () {
        if ($('#link_edit').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        var integrationId = $('#integration_id').val();
        var form_data = new FormData(document.getElementById('form_update_integration'));

        $.ajax({
            method: "POST",
            url: "/api/apps/activecampaign/" + integrationId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
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
            }
        });
    });

    //destroy
    $(document).on('click', '.delete-integration', function (e) {
        e.stopPropagation();
        var project = $(this).attr('project');
        var card = $(this).parent().parent().parent().parent().parent();
        card.find('.card-edit').unbind('click');
        $.ajax({
            method: "DELETE",
            url: "/api/apps/activecampaign/" + project,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                index();
                alertCustom("success", response.message);
            }
        });
    });
});
