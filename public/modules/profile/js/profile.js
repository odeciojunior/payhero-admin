var documentType = '';
var bagder = '';
var badgeArray = {
    'pending': 'badge-primary',
    'analyzing': 'badge-pending',
    'approved': 'badge-success',
    'refused': 'badge-danger',
};

var statusArray = {
    'pending': 'Pendente',
    'analyzing': 'Em análise',
    'approved': 'Aprovado',
    'refused': 'Recusado',
};
var getDataProfile = '';
$(document).ready(function () {

    $('[data-toggle="tooltip"]').tooltip();
    var user = '';

    getDataProfile = function () {
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
                /**
                 * Dados Pessoais
                 */

                $('#name').val(response.data.name);
                $('#email').val(response.data.email);
                $('#document').val(response.data.document);
                $('#cellphone').val(response.data.cellphone);
                $('#date_birth').val(response.data.date_birth);

                /**
                 * Imagem Perfil
                 */
                $('#previewimage').attr("src", response.data.photo ? response.data.photo : '/modules/global/img/user-default.png');
                $("#previewimage").on("error", function () {
                    $(this).attr('src', '/modules/global/img/user-default.png');
                });

                /**
                 * Dados Residenciais
                 */
                if (statusArray[response.data.address_document_translate] === 'Aprovado') {
                    $('.dados-residenciais').attr('disabled', 'disabled');

                } else {
                    $("#text-alert-documents").show();
                    $('.dados-residenciais').removeAttr('disabled');
                }
                $('#zip_code').val(response.data.zip_code);
                $('#street').val(response.data.street);
                $('#number').val(response.data.number);
                $('#neighborhood').val(response.data.neighborhood);
                $('#complement').val(response.data.complement);
                $('#city').val(response.data.city);
                $('#state').val(response.data.state);

                //seleciona a opcao do select de acordo com o país do usuário
                $("#country").find('option').each(function () {
                    if (response.data.country == $(this).val()) {
                        $(this).attr('selected', true);
                    }
                });
                //só mostra o campo estado se o país for Brasil e Estados Unidos
                if (response.data.country == 'brazil' || response.data.country == 'usa') {
                    $(".div-state").show();
                }
                /**
                 * Notificações
                 */
                if (response.data.boleto_compensated) {
                    $("#boleto_compensated_switch").attr("checked", "checked");
                }
                if (response.data.sale_approved) {
                    $("#sale_approved_switch").attr("checked", "checked");
                }
                if (response.data.notazz) {
                    $("#notazz_switch").attr("checked", "checked");
                }

                if (response.data.released_balance) {
                    $("#released_balance_switch").attr("checked", "checked");
                }
                if (response.data.domain_approved) {
                    $("#domain_approved_switch").attr("checked", "checked");
                }
                if (response.data.shopify) {
                    $("#shopify_switch").attr("checked", "checked");
                }

                if (response.data.billet_generated) {
                    $("#billet_generated_switch").attr("checked", "checked");
                }
                if (response.data.credit_card_in_proccess) {
                    $("#credit_card_in_proccess_switch").attr("checked", "checked");
                }

                if (response.data.affiliation) {
                    $("#affiliation_switch").attr("checked", "checked");
                }

                // Verificação de telefone

                if (response.data.cellphone_verified) {
                    cellphoneVerified();
                } else {
                    cellphoneNotVerified();
                }

                if (response.data.role.name !== 'account_owner') {
                    $("#nav_notifications").hide();
                    $("#nav_taxs").hide();
                } else {
                    $("#nav_notifications").show();
                    $("#nav_taxs").show();
                }

                // Verificação de email

                if (response.data.email_verified) {
                    emailVerified();
                } else {
                    emailNotVerified();
                }

                /**
                 * Documentos
                 */

                if (response.data.personal_document_translate === 'pending' || response.data.personal_document_translate === 'refused') {
                    $("#text-alert-documents-cpf").show();
                } else {
                    $("#text-alert-documents-cpf").hide();
                }

                if (response.data.personal_document_translate === 'approved' || response.data.personal_document_translate === 'analyzing') {
                    $('#document').attr('disabled', 'disabled');
                    $("#personal-document-id").hide();
                }

                if (response.data.address_document_translate == 'pending' || response.data.address_document_translate == 'refused') {
                    $("#text-alert-documents-cpf").show();
                    $("#address-document-id").show();
                }

                if (response.data.address_document_translate == 'approved' || response.data.address_document_translate == 'analyzing') {
                    $("#address-document-id").hide();
                }

                $("#td_personal_status").html('').append(`<span class='badge ${badgeArray[response.data.personal_document_translate]}'>${statusArray[response.data.personal_document_translate]}</span>`);

                $("#td_address_status").html('').append(`<span class='badge ${badgeArray[response.data.address_document_translate]}'>${statusArray[response.data.address_document_translate]}</span>`);
                user = response.data.id_code;

                verifyDocuments(response.data);
                verifyUserAddress(response.data);
                changeMaskByUserCountry(response.data);
                loadLabelsByCountry(response.data);
            }
        });
    }
    getDataProfile();

    // Verificar número de celular
    $("#btn_verify_cellphone").on("click", function () {
        event.preventDefault();
        loadingOnScreen();
        var cellphone = $("#cellphone").val();
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
        var email = $("#email").val();
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
        var object = this;
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
        $('.dados-residenciais').removeAttr('disabled');
        $('#document').removeAttr('disabled');

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
                loadingOnScreenRemove();
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
        var verify_code = $("#cellphone_verify_code").val();
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
        var verify_code = $("#email_verify_code").val();
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
        $("#debit-card-tax").val(data.debit_card_tax + '%');
        $("#boleto-tax").val(data.boleto_tax + '%');
        $("#credit-card-release").val('plan-' + data.credit_card_release_money);
        $("#debit-card-release").val(data.debit_card_release_money);
        $("#transaction-tax-abroad").html(data.abroad_transfer_tax + '%.');

        if (data.antecipation_enabled_flag) {
            // $('.title-antecipation-tax').show();
            // $('.form-antecipation-tax').show();
            $('.info-antecipation-tax').show();
            $('#label-antecipation-tax').text(data.antecipation_tax + '%.');
            // $("#antecipation-tax").val(data.antecipation_tax + '%');
        } else {
            $('.title-antecipation-tax').hide();
            $('.form-antecipation-tax').hide();
        }

        $("#boleto-release").val('plan-' + data.boleto_release_money);
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
            data: {
                credit_card_plan: $("#credit-card-release").val(),
                boleto_plan: $("#boleto-release").val()
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom('success', response.message);
                $("#credit-card-tax").val(response.data.new_card_tax_value);
                $("#debit-card-tax").val(response.data.new_card_tax_value);
                $("#boleto-tax").val(response.data.new_boleto_tax_value);
                $("#debit-card-release").val($("#credit-card-release option:selected").html());
            }
        });
    });

    $(".document-url").on("click", function (e) {
        e.preventDefault();
        var documentUrl = $(this).attr('href');
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/profile/opendocument',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {document_url: documentUrl},
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                window.open(response.data, '_blank');
            }
        });
    });

    $('#country').on('change', function () {
        var documentName = {
            brazil: 'CPF',
            portugal: 'NIF (Número de Identificação Fiscal)',
            usa: 'SSN (Social Security Number)',
            germany: 'STEUERNUMMER',
            spain: 'DNI (Documento Nacional de Identidade)',
            france: 'CI',
            italy: 'Codice Fiscale',
            chile: 'RUT (Rol Único Tributario)'
        };
        if ($(this).val() == 'brazil' || $(this).val() == 'usa') {
            $(".div-state").show();
        } else {
            $(".div-state").hide();
        }
        if ($(this).val() == 'brazil') {
            $('#zip_code').mask('00000-000');
            $('#cellphone').mask('+55 (00) 00000-0000');
            $('#document').mask('000.000.000-00');
            zipCode();
        } else {
            $('#cellphone').mask('+0#');
            $('#document').unmask();
            $('#zip_code').unmask();
        }
        $('.label-document').text(documentName[$(this).val()]);
        $('#document').attr('placeholder', documentName[$(this).val()]);
    });

    //vefica se os documentos do usuário estão aprovados e desabilita todos os inputs
    function verifyDocuments(user) {
        if (user.address_document_status == 3 && user.personal_document_status == 3) {
            $(".dz-hidden-input").prop("disabled", true);
            $('#dropzoneDocuments').css({
                'cursor': 'not-allowed',
            });
            $('.text-dropzone').css({
                'cursor': 'not-allowed',
            });
        }
    }

    function verifyUserAddress(user) {
        if (user.zip_code == null || user.street == null || user.number == null || user.neighborhood == null || user.city == null) {
            $('#row_dropzone_documents').hide();
            $('#div_address_pending').show();
        } else {
            $('#row_dropzone_documents').show();
            $('#div_address_pending').hide();
        }
    }

    function changeMaskByUserCountry(user) {
        if (user.country == 'brazil') {
            $('#zip_code').mask('00000-000');
            $('#cellphone').mask('+55 (00) 00000-0000');
            $('#document').mask('000.000.000-00');
            zipCode();
        } else {
            $('#cellphone').mask('+0#');
        }
    }

    function loadLabelsByCountry(user) {
        var documentName = {
            brazil: 'CPF',
            portugal: 'NIF (Número de Identificação Fiscal)',
            usa: 'SSN (Social Security Number)',
            germany: 'STEUERNUMMER',
            spain: 'DNI (Documento Nacional de Identidade)',
            france: 'CI',
            italy: 'Codice Fiscale',
            chile: 'RUT (Rol Único Tributario)'
        };
        $('.label-document').text(documentName[user.country]);
        $('#document').attr('placeholder', documentName[user.country]);
    }

    function htmlTableDocuments(data) {
        var dados = '';
        var verifyReason = false;
        if (data.length == 0) {
            $("#profile-documents-modal").append('<tr><td class="text-center" colspan="4">Nenhum documento enviado</td></tr>');
        } else {
            $("#document-refused-motived").html('');
            $.each(data, function (index, value) {
                dados = `<tr>
                        <td class='text-center'>${value.date}</td>
                        <td class='text-center' style='cursor: pointer;'>
                            <span class='badge ${badgeArray[value.status]}'>
                                    ${statusArray[value.status]}</td>
                               </span>
                        </td>`;

                if (value.refused_reason != '' && value.refused_reason != null) {
                    dados += `
                                <td class='text-center' style='color:red;'>${value.refused_reason}</td>
                             `;

                } else {
                    dados += `
                                <td class='text-center' style='color:red;'></td>
                             `;
                }
                dados += `<td class='text-center'>
                            <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'><i class='material-icons gradient'>remove_red_eye</i></a>
                        </td>

                    </tr>`;
                $("#profile-documents-modal").append(dados);

            });
        }

    }

    function getDocumentsProfile(document_type) {
        $.ajax({
            url: "/api/profile/getdocuments",
            type: "POST",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                'document_type': document_type
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();

            },
            success: function success(response) {
                htmlTableDocuments(response.data);
                $("#loaderLine").remove();

            }
        });
    }

    function zipCode() {
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
    }

    $(".details-document").on('click', function () {
        $("#profile-documents-modal").html('');
        $("#document-refused-motived").css('display', 'none');
        loadOnTable('#profile-documents-modal', '#table-documents');

        documentType = $(this).data('document');
        getDocumentsProfile(documentType);

        Dropzone.forElement('#dropzoneDocuments').removeAllFiles(true);

        $("#document_type").val($(this).data('document'));

        if ($(this).data('document') == 'personal_document') {
            $("#modal-title-documents").html('Documento Pessoal');
            $("#modal-title-documents-info").html(`<br><small class="" style="line-height: 1.5;">'
                                                                            <br><b>Documentos aceitos (Desde que contenham o CPF):</b><br>
                                                                            <ul>
                                                                            <li class='text-left'><b>RG (Carteira de Identidade )</b></li>
                                                                            <li class='text-left'><b>CNH (Carteira Nacional de Habilitação)</b></li>
                                                                            <li class='text-left'><b>Carteira Funcional</b></li>
                                                                            <li class='text-left'><b>CPTS (Carteira de Trabalho e Previdência Social)</b></li>
                                                                            <li class='text-left'><b>Passaporte</b></li>
                                                                            </ul>


                                                                        </small>`);
        } else {
            $("#modal-title-documents").html('Documento Compravante de Residência');
            $("#modal-title-documents-info").html(`<br><small class="" style="line-height: 1.5;">
                <br><b>Comp. de Residência aceitos:</b><br>
                <ul>
                <li class='text-left'><b>Água</b></li>
                <li class='text-left'><b>Energia</b></li>
                <li class='text-left'><b>Gás Encanado</b></li>
                <li class='text-left'><b>Internet</b></li>
                <li class='text-left'><b>Telefone Fixo ou Móvel</b></li>
                <li class='text-left'><b>Contrato de Locação em nome do usuário ou dos Pais</b></li>
                </ul>
                <b>Se nome de terceiro, anexar junto declaração de endereço do titular da conta e RG do titular da conta.</b>
                </small>`);
        }

        $("#modal-details-document").modal('show');
    });

});

Dropzone.autoDiscover = false;

const myDropzone = new Dropzone('#dropzoneDocuments', {
    headers: {
        'Authorization': $('meta[name="access-token"]').attr('content'),
        'Accept': 'application/json',
    },
    paramName: "file",
    maxFilesize: 2,
    url: '/api/profile/uploaddocuments',
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    previewsContainer: ".dropzone-previews",
    success: function success(file, response) {
        alertCustom('success', response.message);

        if (file.previewElement) {
            return file.previewElement.classList.add('dz-success');
        }

    }, error: function (file, response) {

        if (response.search('Max filesize') > 0) {
            response = 'O documento é muito grande. Tamanho maximo: 2mb.';
        } else if (response.search('upload files of this type') > 0) {
            response = 'O documento deve estar em um dos seguintes formatos: jpeg, jpg, png.';
        }

        errorAjaxResponse(response);
        myDropzone.removeFile(file);
    }, complete: function () {
        loadOnTable('#profile-documents-modal', '#table-documents');

        $.ajax({
            url: "/api/profile/getdocuments",
            type: "POST",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                'document_type': documentType
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();
            },
            success: function success(response) {
                $("#loaderLine").remove();

                var dados = '';
                if (response.data.length == 0) {
                    $("#profile-documents-modal").append('<span>Nenhum documento enviado</span>');
                } else {
                    $("#document-refused-motived").html('');
                    $.each(response.data, function (index, value) {
                        dados = `<tr>
                        <td class='text-center'>${value.date}</td>
                        <td class='text-center' style='cursor: pointer;'>
                            <span class='badge ${badgeArray[value.status]}'>
                                    ${statusArray[value.status]}</td>
                               </span>
                        </td>`;

                        if (value.refused_reason != '' && value.refused_reason != null) {
                            dados += `
                                <td class='text-center' style='color:red;'>${value.refused_reason}</td>
                             `;

                        } else {
                            dados += `
                                <td class='text-center' style='color:red;'></td>
                             `;
                        }
                        dados += `<td class='text-center'>
                            <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'><i class='material-icons gradient'>remove_red_eye</i></a>
                        </td>

                    </tr>`;
                        $("#profile-documents-modal").append(dados);

                    });
                }

                getDataProfile();

            }
        });

        ajaxVerifyDocumentPending();
    }

});

