$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    // COMPORTAMENTOS DA TELA
    $('#tab_configuration').click(() => {
        show();
    });

    // FIM - COMPORTAMENTOS DA TELA

    show();

    //carrega detalhes do projeto
    function show() {
        loadOnAny('#tab_configuration .card', false,{
            styles: {
                container: {
                    minHeight: '250px'
                }
            }
        });

        $.ajax({
            url: '/api/apps/activecampaign/' + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: (response) => {
                let project = response.data;
                $('.page-title, .title-pad').text(project.project_name);
                $('#show-photo').attr('src', project.project_photo ? project.project_photo : '/modules/global/img/projeto.png');
                $('#created_at').text('Criado em ' + project.created_at);

                $('#show-description').text(project.project_description);
                $('#api_url').val(project.api_url);
                $('#api_key').val(project.api_key);
                $('#integration_id').val(project.id);

                loadOnAny('#tab_configuration .card', true);
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadOnAny('#tab_configuration .card', true);
            }
        });
    }


    // update
    $(document).on('click', '#bt_integration', function () {
        if ($('#api_url').val() == '' || $('#api_key').val() == '') {
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
                show();
                alertCustom('success', response.message);
            }
        });
    });

//     function renderProjectConfig(data) {

//         let {project, companies, userProject, shopifyIntegrations} = data;

//         $('#update-project #previewimage').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.png');
//         $('#update-project #name').val(project.name);
//         $('#update-project #description').text(project.description);
//         if (project.visibility === 'public') {
//             $('#update-project #visibility').prop('selectedIndex', 0).change();
//         } else {
//             $('#update-project #visibility').prop('selectedIndex', 1).change();
//         }
//         $('#update-project #image-logo-email').attr('src', project.logo ? project.logo : '/modules/global/img/projeto.png');


//         if (project.shopify_id) {
//             $('#update-project #shopify-configs').show();
//             if (shopifyIntegrations[0].status !== 1) {
//                 $('#bt-change-shopify-integration')
//                     .attr('integration-status', shopifyIntegrations[0].status)
//                     .show();
//                 $('#bt-change-shopify-integration span').html(shopifyIntegrations[0].status === 2 ? 'Desfazer integração com shopify' : 'Integrar com shopify');
//             } else if (shopifyIntegrations[0].status === 1) {
//                 $('#shopify-integration-pending').show();
//             }
//             if (shopifyIntegrations[0].status !== 3) {
//                 $('#bt-shopify-sincronization-product, #bt-shopify-sincronization-template')
//                     .attr('integration-status', shopifyIntegrations[0].status)
//                     .show();
//             }
//         }
//     }

//     //carrega a tela de edicao do proejto
//     function updateConfiguracoes() {
//         loadOnAny('#tab_configuration_project .card');
//         $.ajax({
//             method: "GET",
//             url: "/api/projects/" + projectId + '/edit',
//             dataType: "json",
//             headers: {
//                 'Authorization': $('meta[name="access-token"]').attr('content'),
//                 'Accept': 'application/json',
//             }, error: function (response) {
//                 loadOnAny('#tab_configuration_project .card', true);
//                 errorAjaxResponse(response);

//             }, success: function (data) {
//                 renderProjectConfig(data);
//                 loadOnAny('#tab_configuration_project .card', true);
//             }
//         });
//     }

//     //atualiza as configuracoes do projeto
//     $("#bt-update-project").on('click', function (event) {
//         event.preventDefault();
//         loadingOnScreen();

//         let formData = new FormData(document.getElementById("update-project"));

//         if (!verify) {
//             $.ajax({
//                 method: "POST",
//                 url: "/api/projects/" + projectId,
//                 processData: false,
//                 contentType: false,
//                 cache: false,
//                 dataType: "json",
//                 headers: {
//                     'Authorization': $('meta[name="access-token"]').attr('content'),
//                     'Accept': 'application/json',
//                 },
//                 data: formData,
//                 error: function (response) {
//                     loadingOnScreenRemove();
//                     errorAjaxResponse(response);

//                 }, success: function (response) {
//                     alertCustom('success', response.message);

//                     $("#image-logo-email").imgAreaSelect({remove: true});
//                     $("#previewimage").imgAreaSelect({remove: true});
//                     updateConfiguracoes();
//                     loadingOnScreenRemove();
//                 }
//             });
//         } else {
//             $("#error-juros").css('display', 'block');
//             loadingOnScreenRemove();
//         }

//     });

});



// $(document).ready(function () {

//     index();
//     function index() {
//         $.ajax({
//             method: "GET",
//             url: "/api/apps/activecampaign/",
//             dataType: "json",
//             headers: {
//                 'Authorization': $('meta[name="access-token"]').attr('content'),
//                 'Accept': 'application/json',
//             },
//             error: (response) => {
//                 errorAjaxResponse(response);
//             },
//             success: (response) => {
//                 if (isEmpty(response.projects)) {
//                     $('#project-empty').show();
//                     $('#integration-actions').hide();
//                 } else {
//                     $('.select-pad').html("");
//                     let projects = response.projects;
//                     for (let i = 0; i < projects.length; i++) {
//                         $('.select-pad').append('<option value="' + projects[i].id + '">' + projects[i].name + '</option>');
//                     }
//                     if (isEmpty(response.integrations)) {
//                         $("#no-integration-found").show();
//                     } else {
//                         $('#content').html("");
//                         let integrations = response.integrations;
//                         for (let i = 0; i < integrations.length; i++) {
//                             renderIntegration(integrations[i]);
//                         }
//                         $("#no-integration-found").hide();
//                     }
//                     $('#project-empty').hide();
//                     $('#integration-actions').show();
//                 }
//             }
//         });
//     }

    //draw the integration cards
    // function renderIntegration(data) {
    //     $('#content').append(`
    //                         <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
    //                             <div class="card shadow card-edit" project=` + data.id + ` style='cursor:pointer;'>
    //                                 <a href="/apps/activecampaign/${data.id}" class="activecampaign-link">
    //                                     <img class="card-img-top img-fluid w-full" src=` + data.project_photo + ` onerror="this.onerror=null;this.src='/modules/global/img/produto.png';" alt="` + data.project_name + `"/>
    //                                 </a>
    //                                 <div class="card-body">
    //                                     <div class='row'>
    //                                         <div class='col-md-10'>
    //                                             <a href="/apps/activecampaign/${data.id}" class="activecampaign-link">
    //                                                 <h4 class="card-title">` + data.project_name + `</h4>
    //                                                 <p class="card-text sm">Criado em ` + data.created_at + `</p>
    //                                             </a>
    //                                         </div>
    //                                         <div class='col-md-2'>
    //                                             <div role='button' title='Excluir' class='delete-integration pointer float-right mt-35' project=` + data.id + `>
    //                                                 <i class='material-icons gradient'>delete_outline</i>
    //                                             </div>
    //                                         </div>
    //                                     </div>
    //                                 </div>
    //                             </div>
    //                         </div>
    //                     `);
    // }

    //edit
    // $(document).on('click', '.card-edit', function () {

        // $(".modal-title").html('Editar nova Integração com ActiveCampaign');
        // $("#bt_integration").addClass('btn-update');
        // $("#bt_integration").removeClass('btn-save');
        // $("#bt_integration").text('Atualizar');
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


    //update
    // $(document).on('click', '.btn-update', function () {
    //     if ($('#link_edit').val() == '') {
    //         alertCustom('error', 'Dados informados inválidos');
    //         return false;
    //     }
    //     var integrationId = $('#integration_id').val();
    //     var form_data = new FormData(document.getElementById('form_update_integration'));

    //     $.ajax({
    //         method: "POST",
    //         url: "/api/apps/activecampaign/" + integrationId,
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         processData: false,
    //         contentType: false,
    //         cache: false,
    //         data: form_data,
    //         error: (response) => {
    //             errorAjaxResponse(response);
    //         },
    //         success: function success(response) {
    //             index();
    //             alertCustom('success', response.message);
    //         }
    //     });
    // });

// });
