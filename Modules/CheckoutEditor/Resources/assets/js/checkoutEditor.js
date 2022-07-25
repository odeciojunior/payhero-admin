$(document).ready( function () {
    $('#checkout_editor #companies').on("change", function () {
        $('.company-navbar').val( $('#checkout_editor #companies').val() ).change();
    });
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

        $('.accepted-payment-card-creditcard svg path').fill = $(this).val();
    });

    $("#color_secondary").on("input", function () {
        $(":root").css("--secondary-color", $(this).val());
    });

    $("#color_buy_button").on("input", function () {
        $(":root").css("--finish-button-color", $(this).val());
    });

    $("#download_template_banner").on("click", function (e) {
        e.preventDefault();
        window.location.href = $(this).attr('data-href');
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
            $(".logo-div.logo-menu-bar").addClass("retangle-banner");
            $(".preview-banner").removeClass("wide-banner");
        } else {
            $(".preview-banner").removeClass("retangle-banner");
            $(".logo-div.logo-menu-bar").removeClass("retangle-banner");
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
                $($(this).attr("data-enable")).removeClass('low-opacity')
            } else {
                $("." + $(this).attr("data-target")).slideUp("slow", "swing");
                $($(this).attr("data-preview")).slideUp("slow", "swing");
                $($(this).attr("data-enable")).addClass('low-opacity')
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

                $('.theme-ready-first-line').addClass('low-opacity');
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

                $('.theme-ready-first-line').removeClass('low-opacity');
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

    $(".code-input").on("input", () => {
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


    $("#checkout_editor input[type=checkbox]").on("change", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $("input[name='checkout_favicon_type'").on("change", function(){
        if($(this).val()=='1'){
            $("#upload_favicon").addClass('low-opacity');
        }else{
            $("#upload_favicon").removeClass('low-opacity');
        }
    })

    $('#checkout_banner_enabled').on('change', function(){
        if($(this).is(':checked')){
            $("#banner_type").fadeIn("slow", "swing");
            $('.logo-div').addClass('has-banner');
            $('.logo-preview-container').addClass('has-banner');
            $('.menu-bar-mobile').hide('slow');
            $('.purchase-menu-mobile').fadeIn('slow');
        }else{
            $("#banner_type").fadeOut("slow", "swing");
            $('.logo-div').removeClass('has-banner');
            $('.logo-preview-container').removeClass('has-banner');
            $('.menu-bar-mobile').show('slow');
            $('.purchase-menu-mobile').fadeOut('slow');
        }
    });

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

    $('#selector-tooltip').on({
        mouseenter: function () {
            $('#selector-tooltip-container').fadeIn();
        },
        mouseleave: function () {
            $('#selector-tooltip-container').fadeOut();
        }
    });

    $('#favicon-tooltip').on({
        mouseenter: function () {
            $('#favicon-tooltip-container').fadeIn();
        },
        mouseleave: function () {
            $('#favicon-tooltip-container').fadeOut();
        }
    });

    // ---------------- Functions Table - START ---------------------
    $('.selectable-notification').on('change', function(){
        const form = document.querySelector("#checkout_editor");
        const selectableCheckboxes = form.querySelectorAll(".selectable-notification:checked");

        if(selectableCheckboxes.length > 0 && selectableCheckboxes.length < 4) {
            $('#selectable-all-notification').addClass('dash-check');
            $('#selectable-all-notification').prop('checked', true);
        }

        if(selectableCheckboxes.length == 0) {
            $('#selectable-all-notification').prop('checked', false);
            $('#selectable-all-notification').removeClass('dash-check');
        }

        if (selectableCheckboxes.length == 4){
            $('#selectable-all-notification').removeClass('dash-check');
        }
    });

    $('#selectable-all-notification').on('click', function(){
        if($(this).is(':checked')){
            $('.selectable-notification').prop('checked', true);
        }else{
            $('.selectable-notification').prop('checked', false);
            $('#selectable-all-notification').removeClass('dash-check');
        }
    });
    // ---------------- Functions Table - END ---------------------

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
