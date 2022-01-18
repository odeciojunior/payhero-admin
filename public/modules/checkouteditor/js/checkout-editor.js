$(document).ready( function () {
    // ----------------------- Funções de Botão ----------------------------
    $("#default_finish_color").on("change", function () {
        if ($(this).is(":checked")) {
            $(":root").css("--finish-button-color", "#23D07D");
            $("#color_buy_button").prop("disabled", true);
            $("#color_buy_button").css("opacity", "0.3");
        } else {
            $(":root").css("--finish-button-color",$("#color_buy_button").val());
            $("#color_buy_button").prop("disabled", false);
            $("#color_buy_button").css("opacity", "1");
        }
    });

    $("#checkout-type").on("click", ".btn", function () {
        $(this).addClass("btn-active").siblings().removeClass("btn-active");
    });

    $("#color_primary").on("input", function () {
        $(":root").css("--primary-color", $(this).val());
    });

    $("#color_secondary").on("input", function () {
        $(":root").css("--secondary-color", $(this).val());
    });

    $("#color_buy_button").on("input", function () {
        $(":root").css("--finish-button-color", $(this).val());
    });

    $("#download_template_banner").on("click", function (e) {
        e.preventDefault();
    });

    $(".accept-payment-type").on("change", function () {
        const form = document.querySelector("#checkout_editor");
        const checkboxes = form.querySelectorAll(".accept-payment-type");
        const checkboxLength = checkboxes.length;
        var oneChecked = false;

        for (let i = 0; i < checkboxLength; i++) {
            if (checkboxes[i].checked) oneChecked = true;
        }

        if (!oneChecked) {
            $(this).prop("checked", true);
        }
    });

    $(".accept-payment-method").on("change", function () {
        const form = document.querySelector("#checkout_editor");
        const checkboxes = form.querySelectorAll(".accept-payment-method");
        const checkboxLength = checkboxes.length;
        var oneChecked = false;

        for (let i = 0; i < checkboxLength; i++) {
            if (checkboxes[i].checked) oneChecked = true;
        }

        if (!oneChecked) {
            $(this).prop("checked", true);
        } else {
            if ($(this).is(":checked")) {
                $($(this).attr("data-preview")).show("slow", "swing");
                $("." + $(this).attr("data-target")).slideDown("slow", "swing");
            } else {
                $($(this).attr("data-preview")).hide("slow", "swing");
                $("." + $(this).attr("data-target")).slideUp("slow", "swing");
            }
        }
    });

    $("input[name=checkout_banner_type]").on("click", function () {
        var bannerType = $(this).val();

        if (bannerType === "0") {
            $(".preview-banner").addClass("retangle-banner");
            $(".preview-banner").removeClass("wide-banner");
        } else {
            $(".preview-banner").removeClass("retangle-banner");
            $(".preview-banner").addClass("wide-banner");
        }
    });

    $("input[name=checkout_type_enum]").on("click", function () {
        var checkoutType = $(this).val();

        if (checkoutType === "1") {
            $(".visual-content-left").addClass("three-steps");
            $(".visual-content-left").removeClass("unique");
            $(".visual-content-mobile").addClass("three-steps");
            $(".visual-content-mobile").removeClass("unique");
            $(".steps-lines").slideDown("slow", "swing");
            $("#finish_button_preview_desktop_visual").slideDown(
                "slow",
                "swing"
            );
            $("#finish_button_preview_mobile_visual").slideDown(
                "slow",
                "swing"
            );
        } else {
            $(".visual-content-left").removeClass("three-steps");
            $(".visual-content-left").addClass("unique");
            $(".visual-content-mobile").removeClass("three-steps");
            $(".visual-content-mobile").addClass("unique");
            $(".steps-lines").slideUp("slow", "swing");
            $("#finish_button_preview_desktop_visual").slideUp("slow", "swing");
            $("#finish_button_preview_mobile_visual").slideUp("slow", "swing");
        }
    });

    $(".add-tag").on("click", function (e) {
        e.preventDefault();
        var input = $(this).attr("data-input");
        $(input).val($(input).val() + " " + $(this).attr("data-tag") + " ");
        $(input).focus();
    });

    // ----------------- Função de Collapse --------------------
    $(".switch-checkout")
        .off()
        .on("click", function () {
            let checked = $(this).prop("checked");
            if (checked) {
                $("." + $(this).attr("data-target")).slideDown("slow", "swing");
                $($(this).attr("data-preview")).slideDown("slow", "swing");
            } else {
                $("." + $(this).attr("data-target")).slideUp("slow", "swing");
                $($(this).attr("data-preview")).slideUp("slow", "swing");
            }
        });

    $(".switch-checkout-accordion")
        .off()
        .on("click", function () {
            let checked = $(this).prop("checked");
            if (checked) {
                $("." + $(this).attr("data-target")).slideDown("slow", "swing");
                $("." + $(this).attr("data-toggle")).slideUp("slow", "swing");

                var primaryColor = $("#color_primary").val();
                var secondaryColor = $("#color_secondary").val();
                var finishButtonColor = "#23D07D";

                if (!$("#default_finish_color").is(":checked")) {
                    var finishButtonColor = $("#color_buy_button").val();
                }

                $(":root").css("--primary-color", primaryColor);
                $(":root").css("--secondary-color", secondaryColor);

                $(":root").css("--finish-button-color", finishButtonColor);
            } else {
                $("." + $(this).attr("data-target")).slideUp("slow", "swing");
                $("." + $(this).attr("data-toggle")).slideDown("slow", "swing");

                var primaryColor = $(
                    'label[for="' +
                        $("input[name=theme_enum]:checked").attr("id") +
                        '"]'
                )
                    .children(".theme-primary-color")
                    .css("background-color");
                var secondaryColor = $(
                    'label[for="' +
                        $("input[name=theme_enum]:checked").attr("id") +
                        '"]'
                )
                    .children(".theme-secondary-color")
                    .css("background-color");

                $(":root").css("--primary-color", primaryColor);
                $(":root").css("--secondary-color", secondaryColor);
                $(":root").css("--finish-button-color", primaryColor);
            }
        });

    // ----------------- Função Colors --------------------
    $(".theme-radio").on("click", function () {
        var primaryColor = $('label[for="' + $(this).attr("id") + '"]')
            .children(".theme-primary-color")
            .css("background-color");
        var secondaryColor = $('label[for="' + $(this).attr("id") + '"]')
            .children(".theme-secondary-color")
            .css("background-color");

        $(":root").css("--primary-color", primaryColor);
        $(":root").css("--secondary-color", secondaryColor);
        $(":root").css("--finish-button-color", primaryColor);
    });

    (function hideToogleAccordions() {
        if ($(".switch-checkout-accordion").is("checked")) {
            $(
                "." + $(".switch-checkout-accordion").attr("data-toggle")
            ).slideUp("slow", "swing");
        } else {
            $(
                "." + $(".switch-checkout-accordion").attr("data-target")
            ).slideUp("slow", "swing");
        }
    })();

    $("#whatsapp_phone").mask("(00) 00000-0000");

    $("#download_template_banner").on("click", (e) => {
        e.preventDefault();
        window.open($(this).attr("data-href"), "_blank");
    });

    $("#post_purchase_message_title").on("input", function () {
        $(".shop-message-preview-title").empty();
        $(".shop-message-preview-title").append($(this).val());
    });

    // Enable all tooltips
    $('[data-toggle="tooltip"]').tooltip();

    $("input[name=number]").on("input", () => {
        $(this).attr(
            "value",
            $(this)
                .val()
                .replace(/[^0-9.]/g, "")
                .replace(/(\..*)\./g, "$1")
        );
    });

    $(".preview-type").on("change", function () {
        if ($(this).add(":checked")) {
            $("#" + $(this).attr("data-toggle")).fadeOut("slow", "swing");
            $("#" + $(this).attr("data-target")).fadeIn("slow", "swing");
        }
    });

    $(".switch-labeled").on("input", function () {
        if ($(this).is(":checked")) {
            $("#" + $(this).attr("data-label")).addClass("active");
        } else {
            $("#" + $(this).attr("data-label")).removeClass("active");
        }
    });

    var drEventLogo = $("#checkout_logo").dropify({
        messages: {
            default: "",
            replace: "",
            remove: "Remover",
            error: "",
        },
        error: {
            fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
            minWidth: "",
            maxWidth: "A imagem deve ter largura menor que 300px.",
            minHeight: "",
            maxHeight: "A imagem deve ter altura menor que 300px.",
            fileExtension:
                "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
        },
        tpl: {
            message:
                '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Clique ou arraste e solte aqui</span></p></div>',
            clearButton:
                '<button type="button" class="dropify-clear o-bin-1"></button>',
        },
        imgFileExtensions: ["png", "jpg", "jpeg", "svg"],
    });

    drEventLogo.on("dropify.fileReady", function (event, element) {
        var files = event.target.files;
        var done = function (url) {
            $("#logo_preview_mobile").attr("src", url);
            $("#logo_preview_desktop").attr("src", url);
        };
        if (files && files.length > 0) {
            file = files[0];

            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        }
    });

    var drEventBanner = $("#checkout_banner").dropify({
        messages: {
            default: "",
            replace: "",
            remove: "Remover",
            error: "",
        },
        error: {
            fileSize: "O tamanho máximo do arquivo deve ser {{ value }}.",
            minWidth: "A imagem deve ter largura maior que 651px.",
            maxWidth: "A imagem deve ter largura menor que 651px.",
            minHeight: "A imagem deve ter altura maior que 651px.",
            maxHeight: "A imagem deve ter altura menor que 651px.",
            fileExtension:
                "A imagem deve ser algum dos formatos permitidos. ({{ value }}).",
        },
        tpl: {
            message:
                '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Faça upload do seu banner</span></p></div>',
            clearButton:
                '<button type="button" class="dropify-clear o-bin-1"></button>',
        },
        imgFileExtensions: ["png", "jpg", "jpeg"],
    });

    var bs_modal = $("#modal_banner");
    var image = document.getElementById("cropped_image");
    var cropper, reader, file;

    drEventBanner.on("dropify.fileReady", function (event, element) {
        var files = event.target.files;
        var done = function (url) {
            image.src = url;
            bs_modal.modal("show");
        };

        if (files && files.length > 0) {
            file = files[0];

            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        }
    });

    drEventBanner.on("dropify.beforeClear", function (event, element) {
        var imgPreviewDesktop = document.getElementById(
            "preview_banner_img_desktop"
        );
        var imgPreviewMobile = document.getElementById(
            "preview_banner_img_mobile"
        );

        imgPreviewDesktop.src = "";
        imgPreviewMobile.src = "";

        $("#checkout_banner_hidden").val("");
    });

    //  ----------------- Crop Modal ----------------------

    var $dataZoom = $("#dataZoom");
    bs_modal
        .on("shown.bs.modal", function () {
            cropper = new Cropper(image, {
                highlight: false,
                movable: false,
                viewMode: 3,
                aspectRatio: 960 / 210,
                zoom: function (e) {
                    var ratio = Math.round(e.ratio * 1000) / 10;
                    $dataZoom.text(ratio);
                },
            });
        })
        .on("hidden.bs.modal", function () {
            cropper.destroy();
            cropper = null;
        });

    $("#zoom-in").on("click", () => {
        cropper.zoom(0.1);
    });

    $("#zoom-out").on("click", () => {
        cropper.zoom(-0.1);
    });

    var lastNum;
    $("#zoom-slide").on("input change", () => {
        if (lastNum < $("#zoom-slide").val()) {
            cropper.zoom(0.1);
        } else {
            cropper.zoom(-0.1);
        }
        lastNum = $("#zoom-slide").val();
    });

    $("#crop-reset").on("click", () => {
        cropper.reset();
        $("#zoom-slide").val(0);
    });

    $(".img-profile input")
        .on("click", function (e) {
            e.stopPropagation();
        })
        .on("change", function () {
            let file = this.files[0];
            let reader = new FileReader();
            reader.onload = function (e) {
                let img = $(".img-profile")
                    .addClass("cropping")
                    .find("img")
                    .attr("src", e.target.result);
                cropper = new Cropper(img[0], {
                    aspectRatio: 1,
                    minContainerWidth: 150,
                    minContainerHeight: 150,
                });
                $("#btn-crop-cancel, #btn-crop").show();
            };
            reader.readAsDataURL(file);
        });

    $("#button-crop").on("click", function () {
        if (cropper) {
            var canvas = cropper.getCroppedCanvas();
            var src = canvas.toDataURL();

            var imgPreviewDesktop = document.getElementById(
                "preview_banner_img_desktop"
            );
            var imgPreviewMobile = document.getElementById(
                "preview_banner_img_mobile"
            );

            imgPreviewDesktop.src = src;
            imgPreviewMobile.src = src;

            replacePreview("checkout_banner", src, "Image.jpg");

            $("#checkout_banner_hidden").prop("type", "hidden");
            $("#checkout_banner_hidden").val(src);
            $("#checkout_banner_hidden").prop("type", "file");

            cropper.getCroppedCanvas().toBlob((blob) => {
                let dt = new DataTransfer();
                let file = new File(
                    [blob],
                    "banner." + blob.type.split("/")[1]
                );
                dt.items.add(file);
                document.querySelector("#upload-banner input").files = dt.files;

                let reader = new FileReader();
                reader.onload = function (e) {
                    // $('.img-profile')
                    //     .find('img')
                    //     .attr('src', e.target.result)
                    //     .data('src', e.target.result);
                    // cropper.destroy();
                    // $('#btn-crop-cancel, #btn-crop').hide();
                };
                reader.readAsDataURL(file);
            });
        }

        bs_modal.modal("hide");
    });

    $("#button-cancel-crop").on("click", function () {
        $("#checkout_banner").parent().find(".dropify-clear").trigger("click");
    });

    $("#checkout_editor input[type=checkbox]").on("change", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $("#checkout_editor").on("change", function () {
        $("#save_changes").fadeIn("slow", "swing");
    });

    $('#checkout_banner_enabled').on('change', function(){
        if($(this).is(':checked')){
            $("#banner_type").fadeIn("slow", "swing");
            $('#logo_preview_desktop_div').addClass('has-banner');
        }else{
            $("#banner_type").fadeOut("slow", "swing");
            $('#logo_preview_desktop_div').removeClass('has-banner');
        }
    });

    $('[data-toggle="tooltip"]').tooltip()

    
    $('#installments_limit').on('change', function() {
        var installmentsLimit = parseInt($("#installments_limit option:selected").val());
        var interestFreeInstallments = parseInt($("#interest_free_installments option:selected").val());
        var preselectedInstallment = parseInt($("#preselected_installment option:selected").val());

        $("#interest_free_installments option").remove();
        $("#preselected_installment option").remove();

        if(installmentsLimit < interestFreeInstallments ) {
            interestFreeInstallments =  installmentsLimit;
        }

        for(var installments = 1; installments < installmentsLimit+1; installments++) {
            $("#interest_free_installments").append(
                `<option 
                    value="${installments}" ${installments == interestFreeInstallments ? 'selected' : ''}>
                    ${installments}x 
                </option>`);
        }

        if(installmentsLimit < preselectedInstallment ) {
            preselectedInstallment =  installmentsLimit;
        }

        for(var installments = 1; installments < installmentsLimit+1; installments++) {
            $("#preselected_installment").append(
                `<option 
                    value="${installments}" ${installments == preselectedInstallment ? 'selected' : ''}>
                    ${installments}x 
                </option>`);
        }
    });


    $('#quantity-selector-tooltip').mouseover(function(){
        $('.tooltip-content').fadeIn();
    });

    $('#quantity-selector-tooltip').mouseout(function(){
        $('.tooltip-content').fadeOut();
    });

});

function replacePreview(name, src, fname = "") {
    let input = $('input[id="' + name + '"]');
    let wrapper = input.closest(".dropify-wrapper");
    let preview = wrapper.find(".dropify-preview");
    let filename = wrapper.find(".dropify-filename-inner");
    let render = wrapper.find(".dropify-render").html("");

    input.val("").attr("title", fname);
    wrapper.removeClass("has-error").addClass("has-preview");
    filename.html(fname);

    render.append(
        $('<img style="width: 100%; border-radius: 8px; object-fit: cover;" />')
            .attr("src", src)
            .css("height", input.attr("height"))
    );
    preview.fadeIn();
}

const inputElements = [...document.querySelectorAll("input.code-input")];

inputElements.forEach((ele, index) => {
    ele.addEventListener("keydown", (e) => {
        if (e.keyCode === 8 && e.target.value === "")
            inputElements[Math.max(0, index - 1)].focus();
    });
    ele.addEventListener("input", (e) => {
        const [first, ...rest] = e.target.value;
        e.target.value = first ?? "";
        if (index !== inputElements.length - 1 && first !== undefined) {
            inputElements[index + 1].focus();
            inputElements[index + 1].value = rest.join("");
            inputElements[index + 1].dispatchEvent(new Event("input"));
        }
    });
});
