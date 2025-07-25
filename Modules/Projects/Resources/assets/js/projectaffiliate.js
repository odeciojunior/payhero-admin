$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-2);
    let affiliateId = $(window.location.pathname.split("/")).get(-1);

    // COMPORTAMENTOS DA TELA
    $("#tab-info").click(() => {
        show();
    });

    $(".tab_settings_affiliate-panel").click(function () {
        updateConfiguracoes();
    });

    $(".toggler").on("click", function () {
        let target = $(this).data("target");

        if ($(target).hasClass("show")) {
            $(this).find(".showMore").html("add");
        } else {
            $(this).find(".showMore").html("remove");
        }
    });

    // FIM - COMPORTAMENTOS DA TELA

    getCompaniesAndProjects().done( function (data){
        $('.site-navbar .sirius-select-container').addClass('disabled');
    });

    show();

    //carrega detalhes do loja
    function show() {
        $(".page").addClass("low-opacity");

        $.ajax({
            url: "/api/projects/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (response) => {
                let project = response.data;
                $(".title-pad").text(project.name);
                $("#show-photo").attr("src", project.photo ? project.photo : "/build/global/img/projeto.svg");
                $("#created_at").text("Afiliado em " + project.affiliate_date);
                if (project.visibility === "public") {
                    $("#show-visibility")
                        .text("Público")
                        .addClass("text-white details-text md p-2 pr-4 pl-4 badge-pill badge-primary");
                } else {
                    $("#show-visibility")
                        .text("Privado")
                        .addClass("text-white details-text md p-2 pr-4 pl-4 badge-pill badge-danger");
                }

                if (project.status_affiliate == "1") {
                    $("#show-status").text("Ativo").addClass("badge-success");
                } else {
                    $("#show-status").text("Desativado").addClass("badge-danger");
                }

                $("#show-description").text(project.description);
                if (project.cookie_duration == 0) {
                    $("#show-cookie-duration").text("Eterno");
                } else if (project.cookie_duration > 0) {
                    $("#show-cookie-duration").text(project.cookie_duration + " dias");
                }
                $("#show-producer").text(project.producer);
                $("#show-commission").text(project.commission_affiliate + "%");
                $("#show-billet-release").text(project.billet_release_days + " dias");

                $(".page-content").show();
                $(".page").removeClass("low-opacity");
                loadingOnScreenRemove();
            },
            error: (response) => {
                errorAjaxResponse(response);
                $(".page-content").show();
                $(".page").removeClass("low-opacity");
            },
        });
    }

    //abre o modal de cancelar afiliação
    $("#bt-cancel-affiliation").on("click", function (event) {
        event.preventDefault();

        $("#modal-cancel-affiliation").modal("show");
        $("#modal-cancel-affiliation .btn-cancel-affiliation").on("click", function () {
            $("#modal-cancel-affiliation").modal("hide");
            loadingOnScreen();
            $.ajax({
                method: "DELETE",
                url: "/api/affiliates/" + affiliateId,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: function (response) {
                    errorAjaxResponse(response);
                    loadingOnScreenRemove();
                },
                success: function (data) {
                    loadingOnScreenRemove();
                    window.location = "/projects";
                },
            });
        });
    });

    //carrega a tela de edicao do loja
    function updateConfiguracoes() {
        loadOnAny("#tab_setiings_affiliate .card");
        $.ajax({
            method: "GET",
            url: "/api/affiliates/" + affiliateId + "/edit",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) {
                loadOnAny("#tab_setiings_affiliate .card", true);
                errorAjaxResponse(response);
            },
            success: function (data) {
                $("#update-project #previewimage").attr(
                    "src",
                    data.data.project_photo ? data.data.project_photo : "/build/global/img/projeto.svg"
                );
                $("#update-project #image-logo-email").attr(
                    "src",
                    data.data.project_logo ? data.data.project_logo : "/build/global/img/projeto.svg"
                );
                $("#update-project #contact").val(data.data.suport_contact);
                $("#update-project #suport_phone").val(data.data.suport_phone);
                loadOnAny("#tab_setiings_affiliate .card", true);
            },
        });
    }

    //atualiza as configuracoes do loja
    $("#bt-update-project").on("click", function (event) {
        event.preventDefault();
        loadingOnScreen();
        let formData = new FormData(document.getElementById("update-project"));
        $.ajax({
            method: "POST",
            url: "/api/affiliates/updateconfigaffiliate/" + affiliateId,
            processData: false,
            contentType: false,
            cache: false,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: formData,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                alertCustom("success", response.message);
                show();
                loadingOnScreenRemove();
            },
        });
    });
});
