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
                if (response.data == 'email') {
                    $('#email').addClass('d-none');
                    $('#email').val(response.email);
                    $('<input type="text" name="emailscreen" id="emailscreen" value="' + response.email + '" required class="disabled" disabled>').insertAfter('#email');
                } else {
                    $('#email').removeClass('d-none');
                    $('#email').val('');
                    $('#emailscreen').addClass('d-none');

                }
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

    $("#progress-bar-register").css('width', '16%');
    var accessToken = '';
    // MASCARA CNPJ/CPF
    var options = {
        onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
            $('#brasil_company_document').mask(mask, options);
        }
    };

    // mascara numero telefone
    $("#phone").mask("(00) 0000-00009");
    $("#support_telephone").mask("(00) 0000-00009");

    // mascara cpf do usuario
    $('#document').mask('000.000.000-00');

    // mascara para data de nascimento
    // $('#date_birth').mask('00/00/0000');

    // mascara para cep
    $('#zip_code').mask('99.999-999');
    $('#zip_code_company').mask('99.999-999');

    //mascara cnpj
    $('#company_document').mask('00.000.000/0000-00');

    //mascara numero
    $('#number').mask('0#');
    $('#agency').mask('0#');
    $('#agency_digit').mask('0#');
    $('#account').mask('0#');
    $('#account_digit').mask('0#');
    $('#number_company').mask('0#');

    //select de bancos
    $('#bank').select2();

    var currentPage = 'basic data';

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
    $("#btn-go").click(function (e) {
        e.preventDefault();
        loadingOnScreen();
        switch (currentPage) {
            case "basic data":
                basicDataComplete();
                break;
            case "password":
                passwordComplete();
                break;
            case "residential data":
                residentialDataComplete();
                break;
            case "company data":
                companyComplete();
                break;
            case "residential data company":
                residentialDataCompanyComplete();
                break;
            case "bank data juridical person":
                bankJuridicalPersonComplete();
                break;
            case "bank data physical person":
                bankPhysicalPersonComplete();
                break;
        }
    });

    function companyComplete() {

        if (!validateCompanyData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();
        currentPage = 'residential data company';

        $('.div4').hide();
        $('.div7').show();
        $("#btn-go").show();
        $('#alert-row').hide();
        $("#progress-bar-register").css('width', '80%');

    }
    $('#btn-physical-person').on('click', function (e) {
        e.preventDefault();
        $('.div4').hide();
        $('.div5').show();
        $("#btn-go").show();
        $('#alert-row').hide();
        currentPage = 'bank data physical person';
        $("#progress-bar-register").css('width', '80%');
    })

    function residentialDataComplete() {
        if (!validateResidentialData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();
        currentPage = 'company data';
        $(".div3").hide();
        $(".div4").show();
        $("#btn-go").hide();
        $('#alert-row').show();
        $("#progress-bar-register").css('width', '64%');
    }
    function passwordComplete() {

        if (!validatePassword()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();
        currentPage = 'residential data';
        $(".div2").hide();
        $(".div3").show();
        $("#progress-bar-register").css('width', '48%');

    }
    function basicDataComplete() {

        if (!validateBasicData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();

        currentPage = 'password';
        $(".div1").hide();
        $(".div2").show();
        $("#progress-bar-register").css('width', '32%');
    }
    function bankPhysicalPersonComplete() {
        if (!validateBankData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();
        let url = new URL(window.location.href).pathname;
        let parameter = url.split("/")[2];
        $("#progress-bar-register").css('width', '99%');
        $(".div5").hide();
        $(".div6").show();
        $("#btn-go").hide();
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
                document: $('#document').val().replace(/[^0-9]/g, ''),
                date_birth: $('#date_birth').val(),

                password: $('#password').val(),
                parameter: parameter,

                zip_code: $('#zip_code').val(),
                street: $('#street').val(),
                number: $('#number').val(),
                neighborhood: $('#neighborhood').val(),
                complement: $('#complement').val(),
                city: $('#city').val(),
                state: $('#state').val(),

                company_type: 1,

                bank: $('#bank').val(),
                agency: $('#agency').val(),
                agency_digit: $('#agency_digit').val(),
                account: $('#account').val(),
                account_digit: $('#account_digit').val(),

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
                $(".div5").show();
                $(".div6").hide();
                $("#btn-go").show();
                loadingOnScreenRemove();
            }),
            success: function success(response) {
                loadingOnScreenRemove();
                if (response.success == 'true') {
                    accessToken = response.access_token;
                    $.ajax({
                        method: "GET",
                        url: "/api/register/welcome/",
                        headers: {
                            'Authorization': 'Bearer ' + accessToken,
                            'Accept': 'application/json',
                        },
                        error: function error(response) {
                        },
                        success: function success(response) {
                        }
                    });
                    setTimeout(registerComplete, 10000);

                } else {
                    $(".div5").show();
                    $(".div6").hide();
                    $("#btn-go").show();
                    loadingOnScreenRemove();
                    alertCustom('error', response.message);
                }
            }
        });
    }
    function bankJuridicalPersonComplete() {
        if (!validateBankData()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();
        let url = new URL(window.location.href).pathname;
        let parameter = url.split("/")[2];
        $("#progress-bar-register").css('width', '99%');
        $(".div5").hide();
        $(".div6").show();
        $("#btn-go").hide();

        $.ajax({
            method: "POST",
            url: "/api/register",
            dataType: "json",
            headers: {
                'Authorization': 'Bearer ' + accessToken,
                'Accept': 'application/json',
            },
            data: {
                name: $('#firstname').val() + ' ' + $('#lastname').val(),
                email: $('#email').val(),
                cellphone: $('#phone').val(),
                document: $('#document').val().replace(/[^0-9]/g, ''),
                date_birth: $('#date_birth').val(),

                password: $('#password').val(),
                parameter: parameter,

                zip_code: $('#zip_code').val(),
                street: $('#street').val(),
                number: $('#number').val(),
                neighborhood: $('#neighborhood').val(),
                complement: $('#complement').val(),
                city: $('#city').val(),
                state: $('#state').val(),

                company_document: $("#company_document").val().replace(/[^0-9]/g, ''),
                fantasy_name: $("#fantasy_name").val(),
                support_email: $("#support_email").val(),
                support_telephone: $("#support_telephone").val(),
                company_type: 2,

                street_company: $('#street_company').val(),
                number_company: $('#number_company').val(),
                neighborhood_company: $('#neighborhood_company').val(),
                complement_company: $('#complement_company').val(),
                state_company: $('#state_company').val(),
                city_company: $('#city_company').val(),

                bank: $('#bank').val(),
                agency: $('#agency').val(),
                agency_digit: $('#agency_digit').val(),
                account: $('#account').val(),
                account_digit: $('#account_digit').val(),
            },
            error: function error(response) {
                $(".div5").show();
                $(".div6").hide();
                $("#btn-go").show();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                accessToken = response.access_token;
                $.ajax({
                    method: "GET",
                    url: "/api/register/welcome/",
                    headers: {
                        'Authorization': 'Bearer ' + accessToken,
                        'Accept': 'application/json',
                    },
                    error: function error(response) {
                    },
                    success: function success(response) {
                    }
                });
                setTimeout(registerComplete, 10000);
            }

        });
    }
    function residentialDataCompanyComplete() {
        if (!validateResidentialDataCompany()) {
            alertCustom('error', 'Revise os dados informados');
            loadingOnScreenRemove();
            return false;
        }
        loadingOnScreenRemove();
        currentPage = 'bank data juridical person';
        loadingOnScreenRemove();
        $('.div7').hide();
        $('.div5').show();
        $("#btn-go").show();
        $("#progress-bar-register").css('width', '91%');

    }

    function validateCompanyData() {

        $("#companyDocumentError").css('display', 'none');
        $("#fantasyNameError").css('display', 'none');
        $("#supportEmailError").css('display', 'none');
        $("#supportTelephoneError").css('display', 'none');

        var isDataValid = true;

        if ($("#company_document").val().replace(/[^0-9]/g, '').length < 14) {
            $("#companyDocumentError").show();
            $('#companydocumentExistError').hide();
            isDataValid = false;
        } else {
            let result = verifyEqualCNPJ($("#company_document").val());
            if (result) {
                $("#companyDocumentError").hide();
                $('#companydocumentExistError').show();
                isDataValid = false;
            } else {
                $("#companyDocumentError").hide();
                $('#companydocumentExistError').hide();
            }
        }

        if ($("#fantasy_name").val().length < 3) {
            $("#fantasyNameError").show();
            isDataValid = false;
        }

        var emailFilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var illegalChars = /[\(\)\<\>\,\;\:\\\/\"\[\]]/;
        if (!emailFilter.test($("#support_email").val()) || $("#support_email").val().match(illegalChars) || $("#support_email").val().indexOf(" ") !== -1) {
            $("#supportEmailError").show();
            isDataValid = false;
        }

        if ($("#support_telephone").val().replace(/[^0-9]/g, '').length < 10) {
            $("#supportTelephoneError").show();
            isDataValid = false;
        }

        return isDataValid;
    }
    function validateResidentialData() {
        var isDataValid = true;

        $("#zipCodeError").css('display', 'none');
        $("#streetError").css('display', 'none');
        $("#numberError").css('display', 'none');
        $("#neighborhoodError").css('display', 'none');
        $("#complementError").css('display', 'none');
        $("#cityError").css('display', 'none');
        $("#stateError").css('display', 'none');

        if ($("#zip_code").val().replace(/[^0-9]/g, '').length < 8) {
            $("#zipCodeError").show();
            isDataValid = false;
        }
        if ($("#street").val().length < 3) {
            $("#streetError").show();
            isDataValid = false;
        }
        if ($("#number").val().length < 1) {
            $("#numberError").show();
            isDataValid = false;
        }
        if ($("#neighborhood").val().length < 3) {
            $("#neighborhoodError").show();
            isDataValid = false;
        }
        if ($("#city").val().length < 2) {
            $("#cityError").show();
            isDataValid = false;
        }
        if ($("#state").val().length < 1) {
            $("#stateError").show();
            isDataValid = false;
        }
        return isDataValid;

    }
    function validateBasicData() {

        var isDataValid = true;

        $("#nameError").css('display', 'none');
        $("#lastNameError").css('display', 'none');
        $("#emailError").css('display', 'none');
        $("#phoneError").css('display', 'none');
        $("#documentError").css('display', 'none');
        $("#documentExistError").css('display', 'none');
        $("#documentInvalidError").css('display', 'none');
        $("#dateBirthError").css('display', 'none');

        if ($("#firstname").val().length < 3) {
            $("#nameError").show();
            isDataValid = false;
        }
        if ($("#lastname").val().length < 3) {
            $("#lastNameError").show();
            isDataValid = false;
        }
        if ($("#phone").val().replace(/[^0-9]/g, '').length < 10) {
            $("#phoneError").show();
            isDataValid = false;
        }

        var str_cpf = $("#document").val().replace(/[^0-9]/g, '');

        if ($("#document").val().replace(/[^0-9]/g, '').length < 1) {
            $("#documentError").show();
            $("#documentInvalidError").hide();
            $('#documentExistError').hide();
            isDataValid = false;
        } else if (!verifyCPF(str_cpf)) {
            $("#documentInvalidError").show();
            $('#documentError').hide();
            $('#documentExistError').hide();
            isDataValid = false;
        } else {
            let result = verifyEqualCPF($("#document").val());
            if (result) {
                $("#documentError").hide();
                $("#documentInvalidError").hide();
                $('#documentExistError').show();
                isDataValid = false;
            } else {
                $("#documentError").hide();
                $("#documentInvalidError").hide();
                $('#documentExistError').hide();
            }
        }
        if ($("#date_birth").val().replace(/[^0-9]/g, '').length < 8) {
            $("#dateBirthError").show();
            isDataValid = false;
        }

        return isDataValid;
    }

    function validatePassword() {
        var isDataValid = true;

        var emailFilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var illegalChars = /[\(\)\<\>\,\;\:\\\/\"\[\]]/;
        if (!emailFilter.test($("#email").val()) || $("#email").val().match(illegalChars) || $("#email").val().indexOf(" ") !== -1) {
            $("#emailError").show();
            $("#emailExistError").hide();
            isDataValid = false;
        } else {
            let result = verifyEqualEmail($("#email").val());
            if (result) {
                $("#emailError").hide();
                $("#emailExistError").show();
                isDataValid = false;
            } else {
                $("#emailError").hide();
                $("#emailExistError").hide();
            }

        }

        if ($("#password").val().replace(/[^0-9]/g, '').length < 1) {
            $("#passwordError").show();
            isDataValid = false;

        }
        if ($("#password").val().length < 8) {
            $("#passwordError").show();
            isDataValid = false;

        }
        if ($("#password").val().replace(/[^a-zA-Z]/g, '').length < 1) {
            $("#passwordError").show();
            isDataValid = false;

        }
        return isDataValid;
    }
    function validateBankData() {
        $("#bankError").css('display', 'none');
        $("#agencyError").css('display', 'none');
        $("#accountError").css('display', 'none');

        var isDataValid = true;

        if ($("#bank").val() == '') {
            $("#bankError").show();
            isDataValid = false;
        }

        if ($("#agency").val().length < 1) {
            $("#agencyError").show();
            isDataValid = false;
        }

        if ($("#account").val().length < 1) {
            $("#accountError").show();
            isDataValid = false;
        }

        return isDataValid;
    }
    function validateResidentialDataCompany() {
        var isDataValid = true;

        $("#zipCodeCompanyError").css('display', 'none');
        $("#streetCompanyError").css('display', 'none');
        $("#numberCompanyError").css('display', 'none');
        $("#neighborhoodCompanyError").css('display', 'none');
        $("#stateCompanyError").css('display', 'none');
        $("#cityCompanyError").css('display', 'none');

        if ($("#zip_code_company").val().replace(/[^0-9]/g, '').length < 8) {
            $("#zipCodeCompanyError").show();
            isDataValid = false;
        }
        if ($("#street_company").val().length < 3) {
            $("#streetCompanyError").show();
            isDataValid = false;
        }
        if ($("#number_company").val().length < 1) {
            $("#numberCompanyError").show();
            isDataValid = false;
        }
        if ($("#neighborhood_company").val().length < 3) {
            $("#neighborhoodCompanyError").show();
            isDataValid = false;
        }
        if ($("#city_company").val().length < 2) {
            $("#cityCompanyError").show();
            isDataValid = false;
        }
        if ($("#state_company").val().length < 1) {
            $("#stateCompanyError").show();
            isDataValid = false;
        }
        return isDataValid;

    }
    //abilita os inputs de pessoa jurídica
    $('#btn-juridical-person').on('click', function (e) {
        e.preventDefault();
        $('#div-juridical-person').slideDown('fast');
        $('#text-main').hide();
        $('#text-company').show();
        $('#btn-physical-person').hide();
        $(this).hide();
        $("#btn-go").show();
        $('#alert-row').hide();
    });

    //verifica CEP
    $("#zip_code").on("input", function () {
        var zip_code = $('#zip_code').val().replace(/[^0-9]/g, '');
        if (zip_code.length !== 8) return false;
        $.ajax({
            url: "https://viacep.com.br/ws/" + zip_code + "/json/",
            type: "GET",
            cache: false,
            async: false,
            success: function success(response) {
                if (response.localidade) {
                    $("#city").val(unescape(response.localidade));
                }
                if (response.bairro) {
                    $("#neighborhood").val(unescape(response.bairro));
                }
                if (response.uf) {
                    $("#state").val(unescape(response.uf));
                }
                if (response.logradouro) {
                    $("#street").val(unescape(response.logradouro));
                }
            }
        });
    });
    //verifica CEP Empresa
    $("#zip_code_company").on("input", function () {
        var zip_code = $('#zip_code_company').val().replace(/[^0-9]/g, '');
        if (zip_code.length !== 8) return false;
        $.ajax({
            url: "https://viacep.com.br/ws/" + zip_code + "/json/",
            type: "GET",
            cache: false,
            async: false,
            success: function success(response) {
                if (response.localidade) {
                    $("#city_company").val(unescape(response.localidade));
                }
                if (response.bairro) {
                    $("#neighborhood_company").val(unescape(response.bairro));
                }
                if (response.uf) {
                    $("#state_company").val(unescape(response.uf));
                }
                if (response.logradouro) {
                    $("#street_company").val(unescape(response.logradouro));
                }
            }
        });
    });

    function registerComplete() {

        location.href = "/dashboard";
    }

    function verifyEqualCPF(cpf) {
        var result = '';

        $.ajax({
            method: "POST",
            url: "/api/register/verifycpf",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {document: cpf},
            async: false,
            error: function error(response) {
            },
            success: function success(response) {
                if (response.cpf_exist == 'true') {
                    result = true;
                } else {
                    result = false;
                }
            }
        });
        return result;
    }
    function verifyEqualCNPJ(cnpj) {
        var result = '';

        $.ajax({
            method: "POST",
            url: "/api/register/verifycnpj",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {company_document: cnpj},
            async: false,
            error: function error(response) {
            },
            success: function success(response) {
                if (response.cnpj_exist == 'true') {
                    result = true;
                } else {
                    result = false;
                }
            }
        });
        return result;
    }
    function verifyEqualEmail(email) {
        var result = '';

        $.ajax({
            method: "POST",
            url: "/api/register/verifyemail",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {email: email},
            async: false,
            error: function error(response) {
            },
            success: function success(response) {
                if (response.email_exist == 'true') {
                    result = true;
                } else {
                    result = false;
                }
            }
        });
        return result;
    }
    $.ajax({
        method: "GET",
        url: "/api/register/getbanks/",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function error(response) {
        },
        success: function success(response) {
            $.each(response.banks, function (index, value) {
                $("#bank").append("<option value='" + value.code + "'>" + value.name + "</option>")
            });
        }
    });
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
    function verifyCPF(cpf) {
        if (cpf.length == 11) {

            var v = [];

            //Calcula o primeiro dígito de verificação.
            v[0] = 1 * cpf[0] + 2 * cpf[1] + 3 * cpf[2];
            v[0] += 4 * cpf[3] + 5 * cpf[4] + 6 * cpf[5];
            v[0] += 7 * cpf[6] + 8 * cpf[7] + 9 * cpf[8];
            v[0] = v[0] % 11;
            v[0] = v[0] % 10;

            //Calcula o segundo dígito de verificação.
            v[1] = 1 * cpf[1] + 2 * cpf[2] + 3 * cpf[3];
            v[1] += 4 * cpf[4] + 5 * cpf[5] + 6 * cpf[6];
            v[1] += 7 * cpf[7] + 8 * cpf[8] + 9 * v[0];
            v[1] = v[1] % 11;
            v[1] = v[1] % 10;

            //Retorna Verdadeiro se os dígitos de verificação são os esperados.
            if ((v[0] != cpf[9]) || (v[1] != cpf[10])) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    $('#form-register input').on('keypress', function (e) {
        if (e.keyCode == 13) {
            return false;
        }
    });
});
