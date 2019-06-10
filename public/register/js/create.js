$(document).ready(function () {

// dados obrigatorios
    var password = $("#password");
    var email = $('#email');

// contagem de divs do registro
    var cont = 1;
    var back = 0;
    var contProgress = 0;

//////////// função do botão prosseguir////////////////////
    $(".btn-go").on('click', function () {
        // verifica se na primeira div os dados email e password foram preenchidos
        contProgressRegister();

        if (contProgress >= 260) {
            $("#cadastroUsuario").show();
            $("#jump").show();
        }

        // controla visibilidade do botão prosseguir e voltar
        veirfyDivButtonVisible();

        // se for a ultima div
        finalyDiv();

        console.log(contProgress);
        console.log(cont);
    });

//////////// função do botão pular////////////////////
    $("#jump").on('click', function () {
        console.log(password);
        console.log(email);

        // verifica se na primeira div os dados email e password foram preenchidos
        contProgressRegister();

        veirfyDivButtonVisible();

        // se for a ultima div
        finalyDiv();

    });
//////////// função do botão voltar  ////////////////////
    $(".back").on('click', function () {
        // if (contProgress === 260) {
        //     $(".btn-go").css('display', 'block');
        //     $("#jump").css('display', 'block');
        // }

        if (contProgress <= 0) {
            contProgress = 0;
        } else {

            contProgress -= 260;
        }

        if (contProgress < 260) {
            $("#cadastroUsuario").css('display', 'none');
            $("#jump").css('display', 'none');
        } else {
            $(".btn-go").css('display', 'block');
            $("#jump").css('display', 'block');
        }

        /// diminui o barra de progresso
        progressBar(contProgress);

        if (back > 0) {
            $(".div" + cont).hide();
            cont--;
            $(".div" + back).show();
            back--;

        }
    });
////////////

///// verifica os botões prosseguir e pular como invisivel se estiver na div3 ou visivel em outras divs
    function veirfyDivButtonVisible() {
        if (contProgress === 520) {
            $(".btn-go").css('display', 'none');
            $("#jump").css('display', 'none');
        } else {
            $(".btn-go").css('display', 'block');
            $("#jump").css('display', 'block');
        }

    }

////////// verifica a contagem da barra de progresso do registro //////////
    function contProgressRegister() {
        if (password.val() && email.val()) {
            if (contProgress >= 1300) {
                contProgress = 1300;
            } else {
                contProgress += 260;
            }

            // aumenta a barra de progresso do registro
            progressBar(contProgress);

            console.log(cont);
            if (cont < 6) {
                $(".div" + cont).hide();
                cont++;
                $(".div" + cont).show();
                back++;

            }
        }
    }

///// Funcão da ultima div para deixar invisivel  //////////
    function finalyDiv() {
        if (contProgress >= 1300) {
            $("#cadastroUsuario").css('display', 'none');
            $("#jump").css('display', 'none');
            $(".progress").css('display', 'none');
            $(".wrap-footer").css('display', 'none');
            $(".toptitle").html('Parabéns cadastro finalizado com sucesso!')

        }
    }

////////Barra Progresso Cadastro //////////////////
    var bar = $('.progress-bar');

    function progressBar(value) {
        console.log(value);
        if (value < 1300 || value > 0) {
            console.log(value);
            bar.width(value);
        }
    }

/////////////////////////////////////////

    $("#project-default").click(function () {
        $('#select-projeto').hide();
        $('#standard-project').show();
        cont++;
    })

    $("#project-shopify").click(function () {
        $('#select-projeto').hide();
        $('#shopify-project').show();
        cont += 2;
    })

});


