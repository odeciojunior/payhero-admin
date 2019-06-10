$(function () {

    //keyUp para verificar preenchimento senha e email
    $("#password").keyup(function () {
        $("#passwordError").hide();
    });

    $("#email").keyup(function () {
        $("#emailError").hide();

    });



    /////////////////

    var div1Visible = $(".div1").is(':visible');
    if (div1Visible) {
        $(".btn-go").css('display', 'block');
        $("#jump").css('display', 'none');
        $("#btnBack").css('display', 'none');

    }

    // dados obrigatorios
    var password = $("#password");
    var email = $("#email");
    var name = $('#firstname');
    var phone = $("#phone");

    // contagem das divs do registro
    var contDiv = 1;
    var contBack = 0;
    var contProgress = 0;

    ///// botão prosseguir
    $("#btn-go").click(function () {
        contProgressRegister();

        buttonsVisible();

    });

    function buttonsVisible() {

        if (contProgress == 640) {
            $("#btn-go").css('display', 'none');
        }

        if (contProgress == 320) {
            $("#jump").css('display', 'block');
        }

        /// ESTA NA ULTIMA DIV
        if (contProgress == 1280) {
            $("#jump").css('display', 'none');
            $(".progress").css('display', 'none');
            $(".wrap-footer").css('display', 'none');
            $(".toptitle").html('Parabéns cadastro finalizado com sucesso!')
        }
    }

    /// button back
    $(".back").click(function () {
        if (contProgress <= 0) {
            contProgress = 0;
        } else {
            contProgress -= 320;
        }

        buttonsVisible();

        /// diminui o barra de progresso
        progressBar(contProgress);

        if (contBack > 0) {
            $(".div" + contDiv).hide();
            contDiv--;
            $(".div" + contBack).show();
            contBack--;

        }

        if (contProgress === 0) {
            $("#btn-go").css('display', 'block');
            $("#jump").css('display', 'none');
            $("#btnBack").css('display', 'none');

        }

        if (contProgress === 320) {
            $("#btnBack").css('display', 'block');
            $("#btn-go").css('display', 'block');
        }

        if (contProgress === 640) {
            $("#jump").css('display', 'block');

            $(".div5").css('display', 'none');
        }
        if (contProgress === 960) {
            $(".div5").css('display', 'none');
        }

        console.log('volta primeira pagina: ' + contProgress);
    });

    $("#jump").click(function () {
        if (contProgress == 640) {
            console.log('contDiv' + contDiv + '.....' + contProgress);
            $(".div" + contDiv).hide();
            contDiv += 3;
            $(".div" + contDiv).show();
            contBack += 2;

            contProgress += 640;
            progressBar(contProgress);

            buttonsVisible();

        }
        if (contProgress == 320) {
            $(".div" + contDiv).hide();
            contDiv++;
            $(".div" + contDiv).show();
            contBack++;

            contProgress += 320;
            progressBar(contProgress);

            buttonsVisible();
        }

    });

    //
    function contProgressRegister() {
        console.log(password.val());
        if (!password.val()) {
            $("#passwordError").show();

        }
        console.log(email.val());
        if (!email.val()) {
            $("#emailError").show();

        }
        console.log(name.val());
        if (!name.val()) {
            $("#nameError").show();
        }

        console.log(phone.val());
        if (!phone.val()) {
            $("#phoneError").show();
        }

        if (password.val() && email.val() && name.val() && email.val() && phone.val()) {
            console.log(contProgress);
            contProgress += 320;
            progressBar(contProgress);

            if (contDiv < 6) {
                $(".div" + contDiv).hide();
                contDiv++;
                $(".div" + contDiv).show();
                contBack++;
            }
        }

        if (contProgress === 640) {
            $("#btnBack").css('display', 'block');
        } else if (contProgress === 320) {
            $("#btnBack").css('display', 'block');
            $("#btn-go").css('display', 'block');
        }

        finalyDiv();

    }

    function finalyDiv() {

        if (contProgress >= 1280) {
            $(".div5").hide();
            $(".div6").show();
            $("#cadastroUsuario").css('display', 'none');
            $("#jump").css('display', 'none');
            $(".progress").css('display', 'none');
            $(".wrap-footer").css('display', 'none');
            $(".toptitle").html('Parabéns cadastro finalizado com sucesso!')

        }
    }

    // barra de progresso cadastro
    var bar = $("#progress-bar-register");
    function progressBar(value) {
        console.log('progress bar: ' + value);
        bar.width(value);

    }

    ///  radio button escolhe tipo de projeto
    $("#btnBrasil").click(function () {
        $("#eua-form").hide();
        $("#brasil-form").show();

    });

    $("#btnUSA").click(function () {
        $("#brasil-form").hide();
        $("#eua-form").show();
    });

    $("#project-default").click(function () {
        contProgress += 320;
        progressBar(contProgress);
        $('.div' + contDiv).hide();
        contDiv++;
        $("#standard-project").show();
        contBack++;

        $("#btn-go").css('display', 'block');
        $("#jump").css('display', 'none');
        $("#btnBack").css('display', 'block');

    });

    $("#project-shopify").click(function () {
        contProgress += 320;
        progressBar(contProgress);
        $('.div' + contDiv).hide();
        contDiv++;
        $("#shopify-project").show();
        contBack++;

        $("#btn-go").css('display', 'block');
        $("#jump").css('display', 'none');
        $("#btnBack").css('display', 'block');

    });

});





