$(() => {
    let projectId = $(window.location.pathname.split("/")).get(-1);

    $("#verify_phone_open, #verified_phone_open").on("click", () => {
        // 1 - Verifica se o input não é vazio ou inválido via regex
        if (
            $("#support_phone").val() &&
            testinput(
                /^\([1-9]{2}\)\s(?:[2-8]|9[1-9])[0-9]{3}\-[0-9]{4}$/,
                $("#support_phone").val()
            )
        ) {
            $("#support_phone").removeClass("warning-input");

            // Abre o modal
            $("#modal_verify_phone").modal("show");
        } else {
            // Caso contrario alerta ao usuário destacando o input
            $("#support_phone").addClass("warning-input");
        }
    });

    $("#modal_verify_phone").on("shown.bs.modal", function () {

        $('#modal_verify_content').show();
        $('#modal_verified_content').hide();

        $("#phone_modal").empty();
        $("#phone_modal").append($("#support_phone").val());

        // 2 - Envia o código;
        sendCode();
    });

    $("#verify_phone").on("click", (e) => {
        e.preventDefault();

        verifyPhone();
    });

    $("#resend_code").on("click", function () {
        if ($(this).hasClass("disabled")) {
            return;
        } else {
            sendCode();
        }
    });

    function verifyPhone() {

        loadOnAny("#modal_verify_content", false, {
            styles: {
                container: {
                    minHeight: "350px",
                    minWidth: "600px",
                },
            },
        });

        let config_id = $("#checkout_editor #checkout_editor_id").val();
        var confirmationCode = getCodeInput();
        $.ajax({
            method: "POST",
            url: "/api/checkouteditor/verifysupportphone",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                id: config_id,
                verification_code: confirmationCode,
            },
            success: function (response) {
                alertCustom("success", response.message);
                
                $('#verify_phone_open').hide();
                $('#verified_phone_open').show();

                $('#modal_verify_content').hide();                

                loadOnAny("#modal_verified_content", true);
                
            },
            error: function (response) {
                startTimer();
                $(".code-input").addClass("warning-input");
                $(".verify-error").show("fast", "linear");
                errorAjaxResponse(response);
                loadOnAny("#modal_verify_content", true);
                
            },
        });
    }

    function sendCode() {
        startTimer();
        // Ajax para verificar telefone
        let config_id = $("#checkout_editor #checkout_editor_id").val();
        let support_phone = $("#checkout_editor #support_phone").masked();
        $.ajax({
            method: "POST",
            url: "/api/checkouteditor/sendsupportphoneverification",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                id: config_id,
                support_phone: support_phone,
            },
            success: function (response) {
                alertCustom("success", response.message);
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
        });
    }

    function startTimer() {
        $("#resend_code").addClass("disabled");
        $("#timer").show("fast", "linear");
        display = document.querySelector("#timer");
        var timer = 59,
            seconds;
        var timeCounter = setInterval(function () {
            seconds = timer < 10 ? "0" + timer : timer;
            display.textContent = `Aguarde (${seconds})`;
            if (--timer < 0) {
                clearInterval(timeCounter);
                // Ao zerar o contador libera o reenvio do código.
                $("#resend_code").removeClass("disabled");
                $("#timer").hide("fast", "linear");
            }
        }, 1000);
    }

    function testinput(re, str) {
        if (re.test(str)) {
            return true;
        } else {
            return false;
        }
    }

    function getCodeInput() {
        const code = [...document.getElementsByName("verify-phone-code")]
            .filter(({ name }) => name)
            .map(({ value }) => value)
            .join("");

        return code;
    }
});
