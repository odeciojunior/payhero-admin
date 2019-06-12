$(document).ready(function () {

    var currentPage = 'user';

    //keyUp para verificar preenchimento senha e email
    $("#password").keyup(function () {
        $("#passwordError").hide();
    });

    $("#email").keyup(function () {
        $("#emailError").hide();
    });

    // replica texto na criação do projeto standard
    $("#project_name_standard").keydown(function () {
        $("#name_preview_standard").text($("#project_name_standard").val());
    });

    $("#project_desc_standard").keydown(function () {
        $("#description_preview_standard").text($("#project_desc_standard").val());
    });

    ///// replica texto na criação do projeto standard
    $("#project_name_shopify").keydown(function () {
        $("#name_preview_shopify").text($("#project_name_shopify").val());
    });

    $("#project_desc_shopify").keydown(function () {
        $("#description_preview_shopify").text($("#project_desc_shopify").val());
    });

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
    // $(".back").click(function () {
    //     if (contProgress <= 0) {
    //         contProgress = 0;
    //     } else {
    //         contProgress -= 320;
    //     }

    //     buttonsVisible();

    //     /// diminui o barra de progresso
    //     progressBar(contProgress);

    //     if (contBack > 0) {
    //         $(".div" + contDiv).hide();
    //         contDiv--;
    //         $(".div" + contBack).show();
    //         contBack--;
    //     }

    //     if (contProgress === 0) {
    //         $("#btn-go").css('display', 'block');
    //         $("#jump").css('display', 'none');
    //         $("#btnBack").css('display', 'none');
    //     }

    //     if (contProgress === 320) {
    //         $("#btnBack").css('display', 'block');
    //         $("#btn-go").css('display', 'block');
    //     }

    //     if (contProgress === 640) {
    //         $("#jump").css('display', 'block');

    //         $(".div5").css('display', 'none');
    //     }
    //     if (contProgress === 960) {
    //         $(".div5").css('display', 'none');
    //     }

    //     // console.log('volta primeira pagina: ' + contProgress);
    // });

    $("#jump").click(function () {

        alert('to aqui');

        if (contProgress == 640) {
            // console.log('contDiv' + contDiv + '.....' + contProgress);
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

    function contProgressRegister() {

        if (!$("#password").val()) {
            $("#passwordError").show();
        }
        if (!$("#email").val()) {
            $("#emailError").show();
        }
        if (!$("#name").val()) {
            $("#nameError").show();
        }
        if (!$("#phone").val()) {
            $("#phoneError").show();
        }
        if (!$("#password").val() || !$("#email").val() || !$("#name").val() || !$("#email").val() || !$("#phone").val()) {
            return false;
        }

        let firstName = $('#firstname').val();
        let lastName = $('#lastname').val();
        let email = $('#email').val();
        let phone = $('#phone').val();
        let password = $('#password').val();
        let invite = $('#invite').val();

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: firstName + ' ' + lastName,
                email: email,
                celphone: phone,
                password: password,
                invite: invite
            },
            error: function () {
                alert('yes');
            },
            success: function (data) {
                alert('noo');
            }

        });
    }

    function finalyDiv() {

        if (contProgress >= 1280) {
            $(".div5").hide();
            $(".div6").show();
            $("#cadastroUsuario").css('display', 'none');
            $("#jump").css('display', 'none');
            $(".progress").css('display', 'none');
            $(".wrap-footer").css('display', 'none');
            $(".toptitle").html('Parabéns cadastro finalizado com sucesso!');
        }
    }

    // barra de progresso cadastro
    var bar = $("#progress-bar-register");
    function progressBar(value) {
        // console.log('progress bar: ' + value);
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

    //ajax save user
    function saveUser() {
        let firstName = $('#firstname').val();
        let lastName = $('#lastname').val();
        let email = $('#email').val();
        let phone = $('#phone').val();
        let password = $('#password').val();
        let invite = $('#invite').val();

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: firstName + ' ' + lastName,
                email: email,
                celphone: phone,
                password: password,
                // invite: invite
            },
            error: function () {
                //
            },
            success: function (data) {

            }

        });

    }

    //
    function saveEmpresaBr() {
        let country = $('#options').val();
        let fantasyname = $('#fantasyname').val();
        let zip_code = $('#zip_code').val();
        let street = $('#logradouro').val();
        let number = $('#numero').val();
        let neighborhood = $('#bairro').val();
        let state = $('#estado').val();
        let city = $('#cidade').val();

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                country: country,
                fantasyname: fantasyname,
                zip_code: zip_code,
                street: street,
                number: number,
                neighborhood: neighborhood,
                state: state,
                city: city,
            },
            error: function () {
                //
            },
            success: function (data) {

            }

        });
    }

    function saveEmpresaUSA() {

    }

    function saveProjectStandard() {
        let name = $('#project_name_standard').val();
        let description = $('#project_desc_standard').val();
        let photo = $('#file-upload').val();

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                photo: photo,
                name: name,
                description: description,

            },
            error: function () {
                //
            },
            success: function (data) {

            }

        });
    }

    function saveProjectShopify() {
        let name = $('#project_name_shopify').val();
        let description = $('#project_desc_standard').val();
        let photo = $('#file-upload').val();

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                photo: photo,
                name: name,
                description: description,

            },
            error: function () {
                //
            },
            success: function (data) {

            }

        });
    }

    ///////////
    if (window.File && window.FileList && window.FileReader) {

        $("#file-upload").on("change", function (e) {
            let files = e.target.files;
            let filesLength = files.length;
            for (let i = 0; i < filesLength; i++) {
                let f = files[i];
                if (/\.(jpe?g|png)$/i.test(f.name)) {
                    let fileReader = new FileReader();
                    fileReader.onload = (function (e) {
                        let file = e.target;
                        $("#image_standard").attr('src', e.target.result);
                    });

                    fileReader.readAsDataURL(f);
                }

            }
        });

    } else {
        alert("Your Browser doesn't support to File API");
    }


   /*
    if (window.File && window.FileList && window.FileReader) {
        $("#file-upload-shopify").on("change", function (e) {
            alert('aki');
            let files = e.target.files;
            let filesLength = files.length;
            for (let i = 0; i < filesLength; i++) {
                let f = files[i];
                if (/\.(jpe?g|png)$/i.test(f.name)) {
                    let fileReader = new FileReader();
                    fileReader.onload = (function (e) {
                        let file = e.target;
                        $("#image-shopify").attr('src', e.target.result);
                    });

                    fileReader.readAsDataURL(f);
                    // verificaImagemPerfil();
                }

            }
        });
    } else {
        alert("Your Browser doesn't support to File API");
    }*/

});





