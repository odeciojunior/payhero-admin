$(document).ready(function () {

    index();
    function index() {
        $.ajax({
            method: "GET",
            url: "/api/apps/reportana/",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $('#content').html("");
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
        $('#url_api').val('');
        $(':checkbox').prop('checked', true).val(1);
        $('.select-pad').prop("selectedIndex", 0).change();
    }

    //draw the integration cards
    function renderIntegration(data) {
        $('#content').append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=` + data.id + ` style='cursor:pointer;'>
                                    <img class="card-img-top img-fluid w-full" src=` + data.project_photo + ` onerror="this.onerror=null;this.src='/modules/global/img/produto.png';" alt="` + data.project_name + `"/>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <h4 class="card-title">` + data.project_name + `</h4>
                                                <p class="card-text sm">Criado em ` + data.created_at + `</p>
                                            </div>
                                            <div class='col-md-2'>
                                                <a role='button' title='Excluir' class='delete-integration pointer float-right mt-35' project=` + data.id + `>
                                                    <i class='material-icons gradient'>delete_outline</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
    }

    //create
    $('#btn-add-integration').on('click', function () {
        $(".modal-title").html('Adicionar nova Integração com Reportana');
        $("#bt_integration").addClass('btn-save');
        $("#bt_integration").removeClass('btn-update');
        $("#bt_integration").text('Adicionar integração');
        $("#modal_add_integracao").modal('show');
        $("#form_update_integration").hide();
        $("#form_add_integration").show();
        clearForm();
    });

    //edit
    $(document).on('click', '.card-edit', function () {
        $(".modal-title").html('Editar Integração com Reportana');
        $("#bt_integration").addClass('btn-update');
        $("#bt_integration").removeClass('btn-save');
        $("#bt_integration").text('Atualizar');
        $("#form_update_integration").show();
        $("#form_add_integration").hide();
        $("#modal_add_integracao").modal('show');

        $.ajax({
            method: "GET",
            url: "/api/apps/reportana/" + $(this).attr('project'),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#select_projects_edit").val(response.data.project_id);
                $('#integration_id').val(response.data.id);
                $("#url_api_edit").val(response.data.url_api);

                $("#boleto_generated_edit").val(response.data.boleto_generated);
                $("#boleto_generated_edit").prop('checked', $("#boleto_generated_edit").val() == '1');

                $("#boleto_paid_edit").val(response.data.boleto_paid);
                $("#boleto_paid_edit").prop('checked', $("#boleto_paid_edit").val() == '1');

                $("#credit_card_refused_edit").val(response.data.credit_card_refused);
                $("#credit_card_refused_edit").prop('checked', $("#credit_card_refused_edit").val() == '1');

                $("#credit_card_paid_edit").val(response.data.credit_card_paid);
                $("#credit_card_paid_edit").prop('checked', $("#credit_card_paid_edit").val() == '1');

                $("#abandoned_cart_edit").val(response.data.abandoned_cart);
                $("#abandoned_cart_edit").prop('checked', $("#abandoned_cart_edit").val() == '1');
            }
        });
    });

    //store
    $(document).on('click', '.btn-save', function () {
        if ($('#url_api').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        var form_data = new FormData(document.getElementById('form_add_integration'));

        $.ajax({
            method: "POST",
            url: "/api/apps/reportana",
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
        if ($('#url_api_edit').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }
        var integrationId = $('#integration_id').val();
        var form_data = new FormData(document.getElementById('form_update_integration'));

        $.ajax({
            method: "POST",
            url: "/api/apps/reportana/" + integrationId,
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
            url: "/api/apps/reportana/" + project,
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
