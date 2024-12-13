$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-1);

    // COMPORTAMENTOS DA TELA
    $("#tab_configuration").click(() => {
        show();
    });

    // FIM - COMPORTAMENTOS DA TELA

    show();

    //carrega detalhes da loja
    function show() {
        loadOnAny("#tab_configuration .card", false, {
            styles: {
                container: {
                    minHeight: "250px",
                },
            },
        });

        $.ajax({
            url: "/api/apps/activecampaign/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (response) => {
                let project = response.data;
                $(".page-title, .title-pad").text(project.project_name);
                $("#show-photo").attr(
                    "src",
                    project.project_photo ? project.project_photo : "/build/global/img/projeto.svg"
                );
                $("#created_at").text("Criado em " + project.created_at);

                $("#show-description").text(project.project_description);
                $("#api_url").val(project.api_url);
                $("#api_key").val(project.api_key);
                $("#integration_id").val(project.id);

                loadOnAny("#tab_configuration .card", true);
                loadingOnScreenRemove();
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                loadOnAny("#tab_configuration .card", true);
                loadingOnScreenRemove();
            },
        });
    }

    // update
    $(document).on("click", "#bt_integration", function () {
        if ($("#api_url").val() == "" || $("#api_key").val() == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        let integrationId = $('#integration_id').val();
        let form_data = new FormData(document.getElementById("form_update_integration"));

        $.ajax({
            method: "POST",
            url: "/api/apps/activecampaign/" + integrationId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
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
                alertCustom("success", response.message);
            },
        });
    });
});
