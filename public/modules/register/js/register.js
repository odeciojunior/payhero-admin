$(document).ready(function () {

    // MASCARA CNPJ/CPF
    var options = {
        onKeyPress: function (identificatioNumber, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = (identificatioNumber.length > 14) ? masks[1] : masks[0];
            $('#identificatioNumber').mask(mask, options);
        }
    };

    //mascara cpf
    $('#identificatioNumber').mask('000.000.000-000', options);

    // mascara cep
    $("#zip_code").mask("99.999-999");

    // mascara numero telefone
    $("#phone").mask("(00) 0000-00009");

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

    $("#btnBrasil").on("click", function(){
        $("#country").val('brasil');
    });

    $("#btnUSA").on("click", function(){
        $("#country").val('usa');
    });

    // contagem das divs do registro
    var contDiv = 1;
    var contBack = 0;
    var contProgress = 0;

    ///// botão prosseguir
    $("#btn-go").click(function () {

        nextStep();

        buttonsVisible();
    });

    function buttonsVisible() {

        if (contProgress == 640) {
            $("#btn-go").css('display', 'none');
        }

        /// ESTA NA ULTIMA DIV
        if (contProgress == 1280) {
            $("#jump").css('display', 'none');
            $(".progress").css('display', 'none');
            $(".wrap-footer").css('display', 'none');
            $(".toptitle").html('Parabéns cadastro finalizado com sucesso!')
        }
    }

    function nextStep() {

        if(currentPage == 'user'){
            basicDataComplete();
        }
        else if(currentPage == 'company'){
            companyComplete();
        }
    }

    function companyComplete(){

        $(".div2").hide();
        $(".div3").show();

        return true;

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                country: $('#country').val(),
                fantasyname: ($('#country').val() == 'brasil') ? $('#fantasyname').val() : '',
                zip_code: ($('#country').val() == 'brasil') ? $('#zip_code').val() : '',
                street: ($('#country').val() == 'brasil') ? $('#logradouro').val() : '',
                number: ($('#country').val() == 'brasil') ? $('#numero').val() : '',
                neighborhood: ($('#country').val() == 'brasil') ? $('#bairro').val() : '',
                state: ($('#country').val() == 'brasil') ? $('#estado').val() : '',
                city: ($('#country').val() == 'brasil') ? $('#cidade').val() : '',
            },
            error: function () {
                //
            },
            success: function (data) {

            }

        });

    }

    function basicDataComplete(){

        if(!validateBasicData()){
            return false;
        }

        $.ajax({
            method: "POST",
            url: "/register/",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: $('#firstname').val() + ' ' + $('#lastname').val(),
                email: $('#email').val(),
                celphone: $('#phone').val(),
                password: $('#password').val(),
                invite: $('#invite').val()
            },
            error: function ( response) {
                //
            },
            success: function ( response ) {
                if(response.success == 'true'){
                    currentPage = 'company';
                    $(".div1").hide();
                    $(".div2").show();
                    $("#jump").show();
                    alertCustom('success','Cadastro realizado com sucesso');
                }
                else{
                    alertCustom('error','revise os dados informados');
                }
            }
        });

    }

    function validateBasicData(){

        var isDataValid = true;

        $("#passwordError").css('display', 'none');
        $("#emailError").css('display', 'none');
        $("#nameError").css('display', 'none');
        $("#lastNameError").css('display', 'none');
        $("#phoneError").css('display', 'none');
        $("#passwordError").css('display', 'none');

        if (!validatePassword()) {
            $("#passwordError").show();
            isDataValid = false;
        }
        if ($("#email").val().length < 3) {
            $("#emailError").show();
            isDataValid = false;
        }
        if ($("#firstname").val().length < 3) {
            $("#nameError").show();
            isDataValid = false;
        }
        if ($("#lastname").val().length < 3) {
            $("#lastNameError").show();
            isDataValid = false;
        }
        if ($("#phone").val().length < 14) {
            $("#phoneError").show();
            isDataValid = false;
        }

        return isDataValid;
    }

    function validatePassword(){

        if($("#password").val().replace(/[^0-9]/g,'').length < 1){
            return false;
        }
        if($("#password").val().length < 8){
            return false;
        }
        if($("#password").val().replace(/[^a-zA-Z]/g,'').length < 1){
            alert($("#password").val().replace(/[^a-zA-Z]/g,'').length < 1);
            return false;
        }
        return true;
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

    $("#jump").on("click", function(){
        $(".div2").hide();
        $(".div3").hide();
        $(".div4").hide();
        $(".div5").hide();
        $(".div6").show();
        $(this).hide();
        setTimeout(registerComplete, 10000);
    });

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
        $('.div3').hide();
        $("#standard-project").show();
    });

    $("#project-shopify").click(function () {
        $('.div3').hide();
        $("#shopify-project").show();
    });

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

    function registerComplete(){

        location.href="/dashboard";
    }

    function alertCustom(type, message){

        swal({
            position: 'top-right',
            type: type,
            toast: 'true',
            title: message,
            showConfirmButton: false,
            timer: 6000
        });
    }

});





