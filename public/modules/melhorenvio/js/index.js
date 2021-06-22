$(() => {

    index();

    function index(){

        loadingOnScreen();

        $.ajax({
            url: "/api/apps/melhorenvio",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: resp => {
                if(resp.data.length) {

                    $('#content').html('');

                    for (let integration of resp.data){
                        let data =`<div class="col-sm-6 col-md-4 col-lg-3 integration-container">
                                       <div class="card shadow">
                                           <svg class="card-img-top" width="255" height="255" data-jdenticon-value="${integration.name}"></svg>
                                           ${ !integration.completed
                                                ? `<div class="btn-authorize" data-id="${integration.id}"><b>INTEGRAÇÃO NÃO AUTORIZADA.</b> <br> Clique para autorizar </div>`
                                                : ''
                                            }
                                           <div class="card-body">
                                               <div class='row'>
                                                   <div class='col-md-10'>
                                                       <h4 class="card-title">${integration.name}</h4>
                                                       <span class="card-text">Criado em ${integration.created_at}</span>
                                                   </div>
                                                   <div class='col-md-2'>
                                                       <a role="button" title="Excluir" class="btn-delete" data-id="${integration.id}">
                                                           <span class='o-bin-1 pointer'></span>
                                                       </a>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>`;

                        $("#content").append(data);
                    }

                    $('#no-integration-found').hide();

                    jdenticon.configure({
                        hues: [213],
                        lightness: {
                            color: [0.11, 0.53],
                            grayscale: [0.11, 0.54]
                        },
                        saturation: {
                            color: 0.92,
                            grayscale: 1.00
                        },
                    });
                    jdenticon();
                }
                else {
                    $('#no-integration-found').show();
                }
                loadingOnScreenRemove();
            },
            error: resp => {
                errorAjaxResponse(resp)
                loadingOnScreenRemove();
            }
        });
    }

    $(document).on('click', '.btn-authorize', function (){
        let id = $(this).data('id');
        $.ajax({
            url: "/api/apps/melhorenvio/continue/" + id,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: resp => {
                window.location.href = resp.url;
            },
            error: resp => {
                errorAjaxResponse(resp)
            }
        });
    });

    $(document).on('click', '.btn-delete', function (){
        let id = $(this).data('id');
        let parent = $(this).closest('.integration-container');
        $.ajax({
            method: 'POST',
            url: "/api/apps/melhorenvio/" + id,
            data: {
              _method: 'DELETE'
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: resp => {

                parent.remove();
                if($('.integration-container').length === 0){
                    $('#no-integration-found').show();
                }

                alertCustom('success', resp.message);
            },
            error: resp => {
                errorAjaxResponse(resp)
            }
        });
    })

    $('#btn-save').on('click', function () {

        let name = $('#name').val();
        let client_id = $('#client-id').val();
        let client_secret = $('#client-secret').val();

        if (name && client_id && client_secret) {

            loadingOnScreen();

            $.ajax({
                method: "POST",
                url: "/api/apps/melhorenvio",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                data: {
                    name,
                    client_id,
                    client_secret,
                },
                success: resp => {
                    window.location.href = resp.url;
                },
                error: resp => {
                    loadingOnScreenRemove()
                    errorAjaxResponse(resp)
                }
            });

        }
    })

})
