$(document).ready(function () {
    let projectId = $(window.location.pathname.split("/")).get(-1);
    let previewImageReview = $("#previewimagereview");
    let photoReview = $("#photoReview");

    $(".tab_reviews").on("click", function () {
        $.ajax({
            method: "GET",
            url: "/api/projectreviewsconfig/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: function success(response) {
                let project = response.data;
                localStorage.setItem("reviews_config_icon_type", project.reviews_config_icon_type);
                localStorage.setItem("reviews_config_icon_color", project.reviews_config_icon_color);
            },
        });

        previewImageReview.imgAreaSelect({ remove: true });
        loadReviews();
        $(this).off();
    });

    let p = previewImageReview;
    //$('#photoReview').unbind('change');
    photoReview.off().on("change", function () {
        let imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photoReview").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr("src", oFREvent.target.result).fadeIn();

            p.on("load", function () {
                let img = document.getElementById("previewimagereview");
                let x1, x2, y1, y2;

                if (img.naturalWidth > img.naturalHeight) {
                    y1 = Math.floor((img.naturalHeight / 100) * 10);
                    y2 = img.naturalHeight - Math.floor((img.naturalHeight / 100) * 10);
                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                    x2 = x1 + (y2 - y1);
                } else {
                    if (img.naturalWidth < img.naturalHeight) {
                        x1 = Math.floor((img.naturalWidth / 100) * 10);
                        x2 = img.naturalWidth - Math.floor((img.naturalWidth / 100) * 10);
                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                        y2 = y1 + (x2 - x1);
                    } else {
                        x1 = Math.floor((img.naturalWidth / 100) * 10);
                        x2 = img.naturalWidth - Math.floor((img.naturalWidth / 100) * 10);
                        y1 = Math.floor((img.naturalHeight / 100) * 10);
                        y2 = img.naturalHeight - Math.floor((img.naturalHeight / 100) * 10);
                    }
                }

                $("#photo_x1").val(x1);
                $("#photo_y1").val(y1);
                $("#photo_w").val(x2 - x1);
                $("#photo_h").val(y2 - y1);

                previewImageReview.imgAreaSelect({
                    x1: x1,
                    y1: y1,
                    x2: x2,
                    y2: y2,
                    aspectRatio: "1:1",
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    parent: $("#modal_content_review_form"),
                    onSelectEnd: function (img, selection) {
                        $("#photo_x1").val(selection.x1);
                        $("#photo_y1").val(selection.y1);
                        $("#photo_w").val(selection.width);
                        $("#photo_h").val(selection.height);
                    },
                });
            });
        };
    });

    previewImageReview.on("click", function () {
        photoReview.click();
    });

    var initStarsPlugin = function (el, score, readOnly = true) {
        var icon = localStorage.getItem("reviews_config_icon_type") || "star";
        var starHalf = icon === "star" ? `fa fa-${icon}-half-o` : `fa fa-${icon}`;
        var $el = $(el);
        $el.off();
        $el.html("");
        $el.css({ color: localStorage.getItem("reviews_config_icon_color") });
        $el.raty({
            half: true,
            readOnly: readOnly,
            starType: "i",
            score: score,
            starHalf: starHalf,
            starOff: `fa fa-${icon}-o`,
            starOn: `fa fa-${icon}`,
            scoreName: "stars",
        });
    };

    function loadReviews() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var url = "/api/projectreviews";

        if (link != null) {
            url += link;
        }

        loadOnTable("#data-table-reviews", "#table-reviews");
        $("#pagination-review").children().attr("disabled", "disabled");

        $("#tab_project_reviews").find(".no-gutters").css("display", "none");
        $("#table-reviews").find("thead").css("display", "none");

        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            data: { project_id: projectId },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let tableReviews = $("#table-reviews");
                tableReviews.addClass("table-striped");

                let dataTable = $("#data-table-reviews");
                dataTable.html("");

                if (response.data == "") {
                    $("#pagination-container-reviews").removeClass("d-flex").addClass("d-none")
                    dataTable.html(`
                        <tr class='text-center'>
                            <td colspan='11' style='height: 70px;vertical-align: middle'>
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum review configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro review para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-review' data-toggle="modal" data-target="#modal_review">Adicionar review</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    let data = "";
                    $("#tab_project_reviews").find(".no-gutters").css("display", "flex");
                    $("#table-reviews").find("thead").css("display", "contents");
                    $("#pagination-container-reviews").removeClass("d-none").addClass("d-flex")


                    $("#count-project-reviews").html(response.meta.total);

                    $.each(response.data, function (index, value) {
                        data = `
                        <tr>
                            <td class="ellipsis-text">
                                <img src="${value.photo || "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/user-default.png"}" class="img-fluid rounded-circle mr-2" width="35" height="35">

                                <span class="fullInformation" data-toggle="tooltip" data-placement="top" title="${value.name}">
                                    ${value.name}
                                </span>

                            </td>

                            <td class="ellipsis-text">${value.description.substring(0, 50)}...</td>

                            <td>
                                <div id="stars-${value.id}" data-score="${value.stars}"></div>
                            </td>

                            <td class='text-center'>${value.active_flag
                                ? `<span class="badge badge-success">Ativo</span>`
                                : `<span class="badge badge-danger">Desativado</span>`
                            }</td>
                            <td style='text-align:center'>
                                <div class='d-flex justify-content-end align-items-center'>
                                    <a role='button' title='Visualizar' class='mg-responsive details-review pointer' data-review="${value.id
                            }" data-target='#modal-detail-review' data-toggle='modal'><span class=""><img src='/build/global/img/icon-eye.svg'/></span></a>
                                    <a role='button' title='Editar' class='pointer edit-review mg-responsive' data-review="${value.id
                            }"><span class=''><img src='/build/global/img/pencil-icon.svg'/></span></a>
                                    <a role='button' title='Excluir' class='pointer delete-review mg-responsive' data-review="${value.id
                            }" data-toggle="modal" data-target="#modal-delete-review"><span class=''><img src='/build/global/img/icon-trash-tale.svg'/></span></a>
                                </div>
                            </td>
                        </tr>
                        `;
                        dataTable.append(data);
                        initStarsPlugin("#stars-" + value.id);
                    });
                    $(".fullInformation").tooltip();
                    $(".div-config").show();
                    pagination(response, "review", loadReviews);
                }
            },
        });
    }

    $(document).on("click", ".add-review", function () {
        $("#modal_review .modal-title").html("Novo review");
        $(".bt-review-save").show();
        $(".bt-review-update").hide();

        var form = $("#form_review");
        form.trigger("reset");
        form.find("#name").val("");
        form.find("#description_review").val("");
        form.find("#review_stars").html("");
        form.find("#previewimagereview").attr("src", "/build/global/img/projeto.svg");
        form.find("#review_apply_on_plans").val("").trigger("change");

        initStarsPlugin("#review_stars", 5, false);
        setTimeout(() => {
            previewImageReview.imgAreaSelect({ remove: true });
        }, 50);
    });

    $(document).on("click", ".bt-review-save", function () {
        var btReviewSave = $(".bt-review-save");
        btReviewSave.attr("disabled", "disabled");

        var form_data = new FormData(document.getElementById("form_review"));
        form_data.append("project_id", projectId);

        $.ajax({
            method: "POST",
            url: "/api/projectreviews",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            dataType: "json",
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal_review").modal("hide");

                loadReviews();
                alertCustom("success", response.message);
                $("#review_apply_on_plans").val(null).trigger("change");
            },
            complete: function () {
                previewImageReview.imgAreaSelect({ remove: true });
                btReviewSave.attr("disabled", false);
            },
        });
    });

    $(document).on("click", ".edit-review", function (event) {
        event.preventDefault();
        let reviewId = $(this).data("review");
        $("#modal_review .modal-title").html("Editar review");
        $(".bt-review-save").hide();
        $(".bt-review-update").show();

        var form = $("#form_review");
        form.trigger("reset");
        form.find("#name").val("");
        form.find("#description_review").val("");
        form.find("#review_stars").html("");
        form.find(".review-id").val(reviewId);
        form.find("#previewimagereview").attr("src", "/build/global/img/projeto.svg");
        previewImageReview.imgAreaSelect({ remove: true });

        $.ajax({
            method: "GET",
            url: "/api/projectreviews/" + reviewId + "/edit",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) { },
            success: function (response) {
                let review = response.data;
                form.trigger("reset");
                form.find("[name=name]").val(review.name);
                form.find("[name=description]").val(review.description);
                form.find("[name=active_flag]").val(review.active_flag);
                form.find("#previewimagereview").attr("src", review.photo || "/build/global/img/projeto.svg");

                // Seleciona a opção do select de acordo com o que vem do banco
                form.find("#review_apply_on_plans").html("");
                let applyOnPlans = [];
                for (let plan of review.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    form.find("#review_apply_on_plans").append(
                        `<option value="${plan.id}">${plan.name + (plan.description ? " - " + plan.description : "")
                        }</option>`
                    );
                }
                form.find("#review_apply_on_plans").val(applyOnPlans).trigger("change");

                initStarsPlugin("#review_stars", review.stars, false);

                $("#modal_review").modal("show");
                setTimeout(() => {
                    form.find("#previewimagereview").imgAreaSelect({ remove: true });
                }, 300);
                // END
            },
        });
    });

    $(document).on("click", ".delete-review", function (event) {
        event.preventDefault();
        let reviewId = $(this).data("review");
        $(".btn-delete-review").unbind("click");
        $(".btn-delete-review").on("click", function () {
            $.ajax({
                method: "DELETE",
                url: "/api/projectreviews/" + reviewId,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: (function (_error3) {
                    function error() {
                        return _error3.apply(this, arguments);
                    }

                    error.toString = function () {
                        return _error3.toString();
                    };

                    return error;
                })(function (response) {
                    errorAjaxResponse(response);
                }),
                success: function (response) {
                    loadReviews();
                    alertCustom("success", response.message);
                },
            });
        });
    });

    $(document).on("click", ".bt-review-update", function (event) {
        event.preventDefault();

        var btReviewUpdate = $(".bt-review-update");
        btReviewUpdate.attr("disabled", "disabled");

        var form_data = new FormData(document.getElementById("form_review"));
        form_data.append("_method", "PUT");

        let reviewId = $("#form_review .review-id").val();
        $.ajax({
            method: "POST",
            url: "/api/projectreviews/" + reviewId,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal_review").modal("hide");

                loadReviews();
                alertCustom("success", response.message);
            },
            complete: function () {
                previewImageReview.imgAreaSelect({ remove: true });
                btReviewUpdate.attr("disabled", false);
            },
        });
    });

    $(document).on("click", ".details-review", function (event) {
        event.preventDefault();
        let reviewId = $(this).data("review");
        $.ajax({
            method: "GET",
            url: "/api/projectreviews/" + reviewId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let review = response.data;

                $(".review-photo").attr(
                    "src",
                    review.photo || "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/user-default.png"
                );
                $(".review-name").html(review.name);
                $(".review-description").html(review.description);
                $(".review-status").html(
                    `${review.active_flag
                        ? `<span class="badge badge-success">Ativo</span>`
                        : `<span class="badge badge-danger">Desativado</span>`
                    }`
                );

                initStarsPlugin(".preview-stars", review.stars);

                var reviewApplyPlans = $(".review-apply-plans");
                reviewApplyPlans.html("");
                for (let applyPlan of review.apply_on_plans) {
                    reviewApplyPlans.append(`<span>${applyPlan.name}</span><br>`);
                }
            },
        });
    });

    // Search plan
    $("#review_apply_on_plans").select2({
        placeholder: "Nome do plano",
        multiple: true,
        dropdownParent: $("#modal_review"),
        language: {
            noResults: function () {
                return "Nenhum review encontrado";
            },
            searching: function () {
                return "Procurando...";
            },
            loadingMore: function () {
                return "Carregando mais reviews...";
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: "plan",
                    search: params.term,
                    project_id: projectId,
                    page: params.page || 1,
                };
            },
            method: "GET",
            url: "/api/plans/user-plans",
            delay: 300,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processResults: function (res) {
                let elemId = this.$element.attr("id");
                if (elemId === "review_apply_on_plans" && res.meta.current_page === 1) {
                    let allObject = {
                        id: "all",
                        name: "Qualquer plano",
                        description: "",
                    };
                    res.data.unshift(allObject);
                }

                return {
                    results: $.map(res.data, function (obj) {
                        return { id: obj.id, text: obj.name + (obj.description ? " - " + obj.description : "") };
                    }),
                    pagination: {
                        more: res.meta.current_page !== res.meta.last_page,
                    },
                };
            },
        },
    });

    $("#review_apply_on_plans").on("select2:select", function () {
        let selectPlan = $(this);
        if (
            (selectPlan.val().length > 1 && selectPlan.val().includes("all")) ||
            (selectPlan.val().includes("all") && selectPlan.val() !== "all")
        ) {
            selectPlan.val("all").trigger("change");
        }
    });

    $(document).on("click", "#config-review", function (event) {
        event.preventDefault();

        $.ajax({
            method: "GET",
            url: "/api/projectreviewsconfig/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let reviewConfig = response.data;
                let formConfigReview = $("#form_config_review");

                formConfigReview.find("[name=reviews_config_icon_color]").val(reviewConfig.reviews_config_icon_color);
                formConfigReview
                    .find("[name=reviews_config_icon_type][value=" + reviewConfig.reviews_config_icon_type + "]")
                    .prop("checked", true);
                formConfigReview
                    .find("[name=reviews_config_icon_type]")
                    .parent(".radio-custom")
                    .find("i")
                    .css({ color: reviewConfig.reviews_config_icon_color });

                let colorOptions = formConfigReview.find(".color-options > div");
                colorOptions.removeClass("active");
                formConfigReview
                    .find(".color-options")
                    .find('[data-color="' + String(reviewConfig.reviews_config_icon_color).toLowerCase() + '"]')
                    .addClass("active");
                colorOptions.off().on("click", function () {
                    let color = $(this).data("color");
                    formConfigReview.find("[name=reviews_config_icon_color]").val(color);
                    formConfigReview
                        .find("[name=reviews_config_icon_type]")
                        .parent(".radio-custom")
                        .find("i")
                        .css({ color: color });
                    colorOptions.removeClass("active");
                    $(this).addClass("active");
                });

                $("#modal_config_review").modal("show");
            },
        });
    });

    $(document).on("click", ".bt-review-config-update", function (event) {
        event.preventDefault();

        let form_data = new FormData(document.getElementById("form_config_review"));

        $.ajax({
            method: "POST",
            url: "/api/projectreviewsconfig/" + projectId,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                localStorage.setItem("reviews_config_icon_type", form_data.get("reviews_config_icon_type"));
                localStorage.setItem("reviews_config_icon_color", form_data.get("reviews_config_icon_color"));
                loadReviews();

                alertCustom("success", response.message);
                $("#modal_config_review").modal("hide");
            },
        });
    });

    $("#modal_review").on("hidden.bs.modal", function () {
        previewImageReview.imgAreaSelect({ remove: true });
        previewImageReview.trigger("fileselect", [1, ""]);
    });
});
