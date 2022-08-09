$(() => {
    index();

    function index() {
        loadingOnScreen();

        $.ajax({
            url: "/api/apps/melhorenvio",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (resp) => {
                if (resp.data.length) {
                    $("#content").html("");

                    for (let integration of resp.data) {
                        let data = `<div class="col-sm-6 col-md-4 col-lg-3" id="integration-${integration.id}">
                                       <div class="card shadow">
                                           <img class="card-img-top img-fluid w-full" src="/build/global/img/melhorenvio-mono.png" alt=""/>
                                           ${
                                               !integration.completed
                                                   ? `<div class="btn-authorize" data-id="${integration.id}"><b>INTEGRAÇÃO NÃO AUTORIZADA.</b> <br> Clique para autorizar </div>`
                                                   : ""
                                           }
                                           <div class="card-body">
                                               <div class='row'>
                                                   <div class='col-md-10'>
                                                       <h4 class="card-title">${integration.name}</h4>
                                                       <span class="card-text">Criado em ${
                                                           integration.created_at
                                                       }</span>
                                                   </div>
                                                   <div class='col-md-2'>
                                                       <a role="button" title="Excluir" class="btn-delete" data-id="${
                                                           integration.id
                                                       }">
                                                           <span class='o-bin-1 pointer'></span>
                                                       </a>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>`;

                        $("#content").append(data);
                    }

                    $("#no-integration-found").hide();
                } else {
                    $("#no-integration-found").show();
                }
                loadingOnScreenRemove();
            },
            error: (resp) => {
                errorAjaxResponse(resp);
                loadingOnScreenRemove();
            },
        });
    }

    $(document).on("click", ".btn-authorize", function () {
        let id = $(this).data("id");
        $.ajax({
            url: "/api/apps/melhorenvio/continue/" + id,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (resp) => {
                window.location.href = resp.url;
            },
            error: (resp) => {
                errorAjaxResponse(resp);
            },
        });
    });

    $(document).on("click", ".btn-delete", function () {
        let id = $(this).data("id");
        $("#btn-delete-confirm").data("id", id);
        $("#modal-delete-integration").modal("show");
    });

    $("#btn-delete-confirm").on("click", function () {
        let id = $(this).data("id");
        $.ajax({
            method: "POST",
            url: "/api/apps/melhorenvio/" + id,
            data: {
                _method: "DELETE",
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (resp) => {
                $("#integration-" + id).remove();
                if ($("#content").length === 0) {
                    $("#no-integration-found").show();
                }

                alertCustom("success", resp.message);
            },
            error: (resp) => {
                errorAjaxResponse(resp);
            },
        });
    });

    $("#btn-save").on("click", function () {
        let name = $("#name").val();

        if (name) {
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
                },
                success: (resp) => {
                    window.location.href = resp.url;
                },
                error: (resp) => {
                    loadingOnScreenRemove();
                    errorAjaxResponse(resp);
                },
            });
        }
    });
});
