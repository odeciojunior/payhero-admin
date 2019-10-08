$(document).ready(function () {

    verifyInviteRegister();
    function verifyInviteRegister() {
        let inviteCode = $(window.location.pathname.split('/')).get(-1);

        $.ajax({
            method: "GET",
            url: `/api/invitations/verifyinvite/${inviteCode}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: (response) => {
                if (response.responseJSON.data === 'invalido') {
                    $('head').append('<link rel="stylesheet" href="/modules/global/css/page-error.css" type="text/css" />');
                    $("#register-body").html('').append(createHtmlLinkInvalid());

                } else {
                    errorAjaxResponse(response);
                }
            },
            success: (response) => {
                $("#link-invalid").html('');
                console.log(response);
            }
        });
    }

    function createHtmlLinkInvalid() {
        return '<div id="link-invalid" class="page-holder">' +
            '        <div class="content-error d-flex text-center">' +
            '            <img class="svgorange" src="/modules/global/img/error.png">' +
            '            <h1 class="big"> Ops! Link de convite inválido</h1>' +
            '            <p style="font-size:12px">Parece que esse link é inválido. </p>' +
            '        </div>' +
            '    </div>';

    }

    $("#progress-bar-register").css('width', '33%');
    var accessToken = '';
    // MASCARA CNPJ/CPF
    var options = {
        onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
            $('#brasil_company_document').mask(mask, options);
        }
    };

    //mascara cpf
    $('#brasil_company_document').mask('000.000.000-000', options);

    // mascara cep
    $("#brasil_zip_code").mask("99.999-999");

    // mascara number
    $("#brasil_number").mask("0#");
    $("#eua_number").mask("0#");

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

    $("#btnBrasil").on("click", function () {
        $("#country").val('brasil');
    });

    $("#btnUSA").on("click", function () {
        $("#country").val('usa');
    });

    // botão prosseguir
    $("#btn-go").click(function () {
        loadingOnScreen();
        if (currentPage == 'user') {
            basicDataComplete();
        } else if (currentPage == 'company') {
            companyComplete();
        }
    });

    function companyComplete() {

        if (!validateCompanyData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        $.ajax({
            method: "POST",
            url: "/api/companies",
            dataType: "json",
            headers: {
                'Authorization': 'Bearer ' + accessToken,
                'Accept': 'application/json',
            },
            data: {
                country: $('#country').val(),
                fantasy_name: $('#country').val() == 'brasil' ? $('#brasil_fantasy_name').val() : $('#eua_fantasy_name').val(),
                company_document: $('#country').val() == 'brasil' ? $('#brasil_company_document').val() : $('#eua_company_document').val(),
                zip_code: $('#country').val() == 'brasil' ? $('#brasil_zip_code').val() : $('#eua_zip_code').val(),
                state: $('#country').val() == 'brasil' ? $('#brasil_state').val() : $('#eua_state').val(),
                city: $('#country').val() == 'brasil' ? $('#brasil_city').val() : $('#eua_city').val(),
                neighborhood: $('#country').val() == 'brasil' ? $('#brasil_neighborhood').val() : $('#eua_neighborhood').val(),
                street: $('#country').val() == 'brasil' ? $('#brasil_street').val() : $('#eua_street').val(),
                number: $('#country').val() == 'brasil' ? $('#brasil_number').val() : $('#eua_number').val()
            },
            error: function error(response) {
                loadingOnScreenRemove();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                loadingOnScreenRemove();
                // $("#company_id").val(response.data.id);
                alertCustom('success', 'Empresa cadastrada com sucesso');
                $(".div2").hide();
                $(".div3").show();
                $("#jump").click();
            }

        });
    }

    function basicDataComplete() {

        if (!validateBasicData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }

        let url = new URL(window.location.href).pathname;
        let parameter = url.split("/")[2];

        $.ajax({
            method: "POST",
            url: "/api/register",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: $('#firstname').val() + ' ' + $('#lastname').val(),
                email: $('#email').val(),
                cellphone: $('#phone').val(),
                password: $('#password').val(),
                parameter: parameter,
            },
            error: function (_error) {
                function error(_x) {
                    return _error.apply(this, arguments);
                }

                error.toString = function () {
                    return _error.toString();
                };

                return error;
            }(function (response) {
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
                loadingOnScreenRemove();
            }),
            success: function success(response) {
                loadingOnScreenRemove();
                if (response.success == 'true') {
                    accessToken = response.access_token;
                    currentPage = 'company';
                    $(".div1").hide();
                    $(".div2").show();
                    alertCustom('success', 'Cadastro realizado com sucesso');
                    $("#progress-bar-register").css('width', '66%');
                    $("#jump").show();
                } else {
                    alertCustom('error', 'revise os dados informados');
                }
            }
        });
    }

    function validateCompanyData() {

        $("#brasilFantasyNameError").css('display', 'none');
        $("#brasilCompanyDocumentError").css('display', 'none');
        $("#euaFantasyNameError").css('display', 'none');
        $("#euaCompanyDocumentError").css('display', 'none');

        var isDataValid = true;

        if ($('#country').val() == 'brasil') {
            if ($("#brasil_fantasy_name").val().length < 3) {
                $("#brasilFantasyNameError").show();
                isDataValid = false;
            }
            if ($("#brasil_company_document").val().length < 3) {
                $("#brasilCompanyDocumentError").show();
                isDataValid = false;
            }
        } else {
            if ($("#eua_fantasy_name").val().length < 3) {
                $("#euaFantasyNameError").show();
                isDataValid = false;
            }
            if ($("#eua_company_document").val().length < 3) {
                $("#euaCompanyDocumentError").show();
                isDataValid = false;
            }
        }

        return isDataValid;
    }

    function validateBasicData() {

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

        var emailFilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var illegalChars = /[\(\)\<\>\,\;\:\\\/\"\[\]]/;
        if (!emailFilter.test($("#email").val()) || $("#email").val().match(illegalChars) || $("#email").val().indexOf(" ") !== -1) {
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

    function validatePassword() {

        if ($("#password").val().replace(/[^0-9]/g, '').length < 1) {
            return false;
        }
        if ($("#password").val().length < 8) {
            return false;
        }
        if ($("#password").val().replace(/[^a-zA-Z]/g, '').length < 1) {
            return false;
        }
        return true;
    }

    $("#jump").on("click", function () {
        $("#progress-bar-register").css('width', '99%');
        $(".div2").hide();
        $(".div3").hide();
        $(".div4").hide();
        $(".div5").hide();
        $(".div6").show();
        $(this).hide();
        $("#btn-go").hide();
        $.ajax({
            method: "GET",
            url: "/api/register/welcome/",
            headers: {
                'Authorization': 'Bearer ' + accessToken,
                'Accept': 'application/json',
            },
            error: function error() {
            },
            success: function success() {
            }

        });
        setTimeout(registerComplete, 10000);
    });

    ///  radio button escolhe tipo de projeto
    $("#btnBrasil").click(function () {
        $("#eua-form").hide();
        $("#brasil-form").show();
    });

    $("#btnUSA").click(function () {
        //$("#brasil-form").hide();
        //$("#eua-form").show();
    });

    function registerComplete() {

        location.href = "/dashboard";
    }

    function alertCustom(type, message) {

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
