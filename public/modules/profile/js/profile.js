$(document).ready(function () {

    $('[data-toggle="tooltip"]').tooltip();

    let user = '';

    let maskOptions = {
        onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
            $('#document').mask(mask, maskOptions);
        }
    };

    $('#document').mask('000.000.000-000', maskOptions);

    // Verificar número de celular
    $("#btn_verify_cellphone").on("click", function () {
        event.preventDefault();
        loadingOnScreen();
        let cellphone = $("#cellphone").val();
        $.ajax({
            method: "POST",
            url: '/api/profile/verifycellphone',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                cellphone: cellphone
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                $("#progress-bar-register").css('width', '66%');
                $("#jump").show();
            }
        });
    });

    // Verificar email
    $("#btn_verify_email").on("click", function () {
        event.preventDefault();
        loadingOnScreen();
        let email = $("#email").val();
        $.ajax({
            method: "POST",
            url: '/api/profile/verifyemail',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                email: email
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                $("#progress-bar-register").css('width', '66%');
                $("#jump").show();
            }
        });
    });

    $(".notification_switch").on("click", function () {
        let object = this;
        if (object.getAttribute("checked")) {
            object.removeAttribute("checked");
            object.value = 0;
        } else {
            object.setAttribute("checked", "checked");
            object.value = 1;
        }

        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/profile/updatenotification',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                column: object.name,
                value: object.value
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });

    getDataProfile();
    function getDataProfile() {
        $.ajax({
            url: "/api/profile",
            type: "GET",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            cache: false,
            async: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                $('#email').val(response.data.email);
                $('#name').val(response.data.name);
                $('#document').val(response.data.document);
                $('#cellphone').val(response.data.cellphone);
                $('#date_birth').val(response.data.date_birth);
                $('#zip_code').val(response.data.zip_code);
                $('#street').val(response.data.street);
                $('#number').val(response.data.number);
                $('#neighborhood').val(response.data.neighborhood);
                $('#complement').val(response.data.complement);
                $('#city').val(response.data.city);
                $('#state').val(response.data.state);
                $('#previewimage').attr("src", response.data.photo ? response.data.photo : '/modules/global/img/user-default.png');
                $("#previewimage").on("error", function () {
                    $(this).attr('src', '/modules/global/img/user-default.png');
                });
                var valuecss = '';

                if (response.data.personal_document_status === 1) {
                    valuecss = 'primary';
                } else if (response.data.personal_document_status === 2) {
                    valuecss = 'pendente';
                } else if (response.data.personal_document_status === 3) {
                    valuecss = 'success';
                } else {
                    valuecss = 'danger';
                }

                // if (response.data.new_affiliation) {
                //     $("#new_affiliation_switch").attr("checked", "checked");
                // }
                // if (response.data.new_affiliation_request) {
                //     $("#new_affiliation_request_switch").attr("checked", "checked");
                // }
                // if (response.data.approved_affiliation) {
                //     $("#approved_affiliation_switch").attr("checked", "checked");
                // }
                if (response.data.boleto_compensated) {
                    $("#boleto_compensated_switch").attr("checked", "checked");
                }
                if (response.data.sale_approved) {
                    $("#sale_approved_switch").attr("checked", "checked");
                }
                if (response.data.notazz) {
                    $("#notazz_switch").attr("checked", "checked");
                }
                // if (response.data.withdrawal_approved) {
                //     $("#withdrawal_approved_switch").attr("checked", "checked");
                // }
                if (response.data.released_balance) {
                    $("#released_balance_switch").attr("checked", "checked");
                }
                if (response.data.domain_approved) {
                    $("#domain_approved_switch").attr("checked", "checked");
                }
                if (response.data.shopify) {
                    $("#shopify_switch").attr("checked", "checked");
                }
                // if (response.data.user_shopify_integration_store) {
                //     $("#user_shopify_integration_store_switch").attr("checked", "checked");
                // }
                if (response.data.billet_generated) {
                    $("#billet_generated_switch").attr("checked", "checked");
                }
                if (response.data.credit_card_in_proccess) {
                    $("#credit_card_in_proccess_switch").attr("checked", "checked");
                }

                // Verificação de telefone

                if (response.data.cellphone_verified) {
                    cellphoneVerified();
                } else {
                    cellphoneNotVerified();
                }

                // Verificação de email

                if (response.data.email_verified) {
                    emailVerified();
                } else {
                    emailNotVerified();
                }

                var linha = '<span class="badge badge-' + valuecss + '" id="personal_document_badge">' + response.data.personal_document_translate + '</span>';
                $("#td_personal_status").append(linha);

                if (response.data.address_document_status === 1) {
                    valuecss = 'primary';
                } else if (response.data.address_document_status === 2) {
                    valuecss = 'pendente';
                } else if (response.data.address_document_status === 3) {
                    valuecss = 'success';
                } else {
                    valuecss = 'danger';
                }

                linha = '<span class="badge badge-' + valuecss + '" id="address_document_badge">' + response.data.address_document_translate + '</span>';
                $("#td_address_status").append(linha);
                user = response.data.id_code;
            }
        });
    }

    function cellphoneVerified() {
        $("#message_not_verified_cellphone").css("display", "none");
        $("#input_group_cellphone").css("border-color", "forestgreen");
        $("#cellphone").css("border-color", "forestgreen");
        $("#input_group_cellphone").append().html("<i class='fas fa-check' data-toggle='tooltip' data-placement='left' title='Celular verificado!' style='color:forestgreen;'></i>");
    }

    function cellphoneNotVerified() {
        $("#message_not_verified_cellphone").css("display", "");
        $("#input_group_cellphone").css("border-color", "red");
        $("#cellphone").css("border-color", "red");
        $("#input_group_cellphone").append().html("<i class='fas fa-times' data-toggle='tooltip' data-placement='left' title='Celular não verificado!' style='color:red;'></i>");
    }

    function emailVerified() {
        $("#message_not_verified_email").css("display", "none");
        $("#input_group_email").css("border-color", "forestgreen");
        $("#email").css("border-color", "forestgreen");
        $("#input_group_email").append().html("<i class='fas fa-check' data-toggle='tooltip' data-placement='left' title='Email verificado!' style='color:forestgreen;'></i>");
    }

    function emailNotVerified() {
        $("#message_not_verified_email").css("display", "");
        $("#input_group_email").css("border-color", "red");
        $("#email").css("border-color", "red");
        $("#input_group_email").append().html("<i class='fas fa-times' data-toggle='tooltip' data-placement='left' title='Email não verificado!' style='color:red;'></i>");
    }

    $("#profile_update_form").on("submit", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('profile_update_form'));
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/profile/uploaddocuments',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                $("#progress-bar-register").css('width', '66%');
                $("#jump").show();
                getDataProfile();
            }
        });
    });

    $("#match_cellphone_verifycode_form").on("submit", function (event) {
        event.preventDefault();
        let verify_code = $("#cellphone_verify_code").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/profile/matchcellphoneverifycode',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                verifyCode: verify_code
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                $('#modal_verify_cellphone').modal('hide');
                $('#cellphone_verify_code').val('');
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                $("#progress-bar-register").css('width', '66%');
                $("#jump").show();
                cellphoneVerified();
            }
        });
    });

    $("#match_email_verifycode_form").on("submit", function (event) {
        event.preventDefault();
        let verify_code = $("#email_verify_code").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/profile/matchemailverifycode',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                verifyCode: verify_code
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                $('#modal_verify_email').modal('hide');
                $('#email_verify_code').val('');
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                $("#progress-bar-register").css('width', '66%');
                $("#jump").show();
                emailVerified();
            }
        });
    });

    var p = $("#previewimage");
    $("#profile_photo").on("change", function () {

        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("profile_photo").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr('src', oFREvent.target.result).fadeIn();

            p.on('load', function () {
                var img = document.getElementById('previewimage');
                var x1, x2, y1, y2;

                if (img.naturalWidth > img.naturalHeight) {
                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                    x2 = x1 + (y2 - y1);
                } else {
                    if (img.naturalWidth < img.naturalHeight) {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                        y2 = y1 + (x2 - x1);
                    } else {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    }
                }

                $('input[name="photo_x1"]').val(x1);
                $('input[name="photo_y1"]').val(y1);
                $('input[name="photo_w"]').val(x2 - x1);
                $('input[name="photo_h"]').val(y2 - y1);

                $('#previewimage').imgAreaSelect({
                    x1: x1, y1: y1, x2: x2, y2: y2,
                    aspectRatio: '1:1',
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    onSelectEnd: function onSelectEnd(img, selection) {
                        $('input[name="photo_x1"]').val(selection.x1);
                        $('input[name="photo_y1"]').val(selection.y1);
                        $('input[name="photo_w"]').val(selection.width);
                        $('input[name="photo_h"]').val(selection.height);
                    }
                });
            });
        };
    });

    $("#previewimage").on("click", function () {
        $("#profile_photo").click();
    });

    $("#new_password").on("input", function () {

        if ($("#new_password").val().length > 5 && $("#new_password_confirm").val().length > 5 && $("#new_password").val() == $("#new_password_confirm").val()) {
            $("#password_update").attr("disabled", false);
        } else {
            $("#password_update").attr("disabled", true);
        }
    });

    $("#new_password_confirm").on("input", function () {

        if ($("#new_password").val().length > 5 && $("#new_password_confirm").val().length > 5 && $("#new_password").val() == $("#new_password_confirm").val()) {
            $("#password_update").attr("disabled", false);
        } else {
            $("#password_update").attr("disabled", true);
        }
    });

    $("#password_update").on('click', function () {

        if ($("#new_password").val().length > 5 && $("#new_password_confirm").val().length > 5 && $("#new_password").val() == $("#new_password_confirm").val()) {

            $.ajax({
                method: "POST",
                url: "/api/profile/changepassword",
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {
                    new_password: $("#new_password").val(),
                    new_password_confirm: $("#new_password_confirm").val()
                },
                error: function error(data) {
                    swal({
                        position: 'bottom',
                        type: 'error',
                        toast: 'true',
                        title: 'Não foi possivel alterar a senha!',
                        showConfirmButton: false,
                        timer: 6000
                    });
                },
                success: function success(data) {

                    swal({
                        position: 'bottom',
                        type: 'success',
                        toast: 'true',
                        title: 'Senha alterada com sucesso !',
                        showConfirmButton: false,
                        timer: 6000
                    });

                    $('#new_password').val('');
                    $('#new_password_confirm').val('');
                }
            });
        }
    });

    $("#nav_documents").on("click", function () {
        $("#tab_documentos").click();
        $("#previewimage").imgAreaSelect({remove: true});
    });

    $("#nav_users").on("click", function () {
        $("#tab_user").click();
        $("#previewimage").imgAreaSelect({remove: true});
    });

    $("#zip_code").on("input", function () {

        var cep = $('#zip_code').val().replace(/[^0-9]/g, '');

        if (cep.length != 8) return false;

        $.ajax({
            url: "https://viacep.com.br/ws/" + cep + "/json/",
            type: "GET",
            cache: false,
            async: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
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

    $("#nav_taxs").on('click', function () {
        getTax();
    });

    function getTax() {
        $.ajax({
            method: "GET",
            url: `/api/profile/${user}/tax`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                setValuesHtml(response.data);
            }
        });
    }

    function setValuesHtml(data) {
        $("#credit-card-tax").val(data.credit_card_tax + '%');
        $("#boleto-tax").val(data.boleto_tax + '%');
        $("#credit-card-release").val('plan-' + data.credit_card_release_money);
        $("#boleto-release").val(data.boleto_release_money).attr('disabled', 'disabled');
        $("#transaction-tax").html(data.transaction_rate).attr('disabled', 'disabled');
        $("#installment-tax").html(data.installment_tax).attr('disabled', 'disabled');
    }

    $("#update_taxes").on("click", function () {

        $.ajax({
            method: "POST",
            url: '/api/profile/updatetaxes',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {plan: $("#credit-card-release").val()},
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom('success', response.message);
                $("#credit-card-tax").val(response.data.new_tax_value);
            }
        });
    });

});

Dropzone.options.dropzoneDocuments = {
    headers: {
        'Authorization': $('meta[name="access-token"]').attr('content'),
        'Accept': 'application/json',
    },
    paramName: "file",
    maxFilesize: 2,
    url: '/api/profile/uploaddocuments',
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    accept: function accept(file, done) {
        var dropz = this;

        swal({
            title: 'Qual é o tipo do documento?',
            type: 'warning',
            input: 'select',
            inputPlaceholder: 'Selecione o documento',
            inputOptions: {
                '1': 'Documento de identidade',
                '2': 'Comprovante de residência'
            },
            showCancelButton: true,
            confirmButtonColor: '#3085D6',
            cancelButtonColor: '#DD3333',
            confirmButtonText: 'Enviar'
        }).then(function (data) {
            if (data.value) {
                //ok
                $('#document_type').val(data.value);
                done();
            } else {
                //cancel
                dropz.removeFile(file);
            }
        }).catch(function (reason) {
            //close
            dropz.removeFile(file);
        });
    },
    success: function success(file, response) {
        //update table
        if (response.personal_document_translate === 'Em análise') {
            $('#personal_document_badge').removeAttr('class').attr('class', 'badge badge-pendente').text(response.personal_document_translate);
        }
        if (response.address_document_translate === 'Em análise') {

            $('#address_document_badge').removeAttr('class').attr('class', 'badge badge-pendente').text(response.address_document_translate);
        }

        swal({
            position: 'bottom',
            type: 'success',
            toast: 'true',
            title: response.message,
            showConfirmButton: false,
            timer: 6000
        });
    },
    error: function error(file, response) {

        if (response.search('Max filesize') > 0) {
            response = 'O documento é muito grande. Tamanho maximo: 2mb.';
        } else if (response.search('upload files of this type') > 0) {
            response = 'O documento deve estar em um dos seguintes formatos: jpeg, jpg, png.';
        }

        swal({
            position: 'bottom',
            type: 'error',
            toast: 'true',
            title: response,
            showConfirmButton: false,
            timer: 6000
        });

        this.removeFile(file);
    }

};


