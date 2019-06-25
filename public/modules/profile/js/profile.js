$(document).ready(function () {

    $("#profile_update_form").on("submit", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('profile_update_form'));

        $.ajax({
            method: "POST",
            url: $('#profile_update_form').attr('action'),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                $("#progress-bar-register").css('width', '66%');
                $("#jump").show();

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
                        ;
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
                    onSelectEnd: function (img, selection) {
                        $('input[name="photo_x1"]').val(selection.x1);
                        $('input[name="photo_y1"]').val(selection.y1);
                        $('input[name="photo_w"]').val(selection.width);
                        $('input[name="photo_h"]').val(selection.height);
                    }
                });
            })
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
                url: "/profile/changepassword",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    new_password: $("#new_password").val(),
                    new_password_confirm: $("#new_password_confirm").val()
                },
                error: function (data) {
                    swal({
                        position: 'bottom',
                        type: 'error',
                        toast: 'true',
                        title: 'Não foi possivel alterar a senha!',
                        showConfirmButton: false,
                        timer: 6000
                    });
                },
                success: function (data) {

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

    $("#zip_code").on("input", function(){

        var cep = $('#zip_code').val().replace(/[^0-9]/g, '');

        if(cep.length != 8)
            return false;

        $.ajax({
            url : "https://viacep.com.br/ws/"+ cep +"/json/", 
            type : "GET",
            cache: false,
            async: false,
            success : function(response) {

                if(response.localidade){
                    $("#city").val(unescape(response.localidade));
                }
                if(response.bairro){
                    $("#neighborhood").val(unescape(response.bairro));
                }
                if(response.uf){
                    $("#state").val(unescape(response.uf));
                }
                if(response.logradouro){
                    $("#street").val(unescape(response.logradouro));
                }

            }
        });

    });

});

Dropzone.options.dropzoneDocuments = {
    paramName: "file",
    maxFilesize: 10, // MB
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    accept: function (file, done) {
        var dropz = this;

        swal({
            title: 'Qual é o tipo do documento?',
            type: 'warning',
            input: 'select',
            inputPlaceholder: 'Selecione o documento',
            inputOptions: {
                '1': 'Documento de identidade',
                '2': 'Comprovante de residência',
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
                dropz.removeFile(file)
            }

        }).catch(function (reason) {
            //close
            dropz.removeFile(file)
        });
    },
    success: function (file, response) {
        //update table
        $('#td_personal_status').html(response.personal_document_translate);
        $('#td_address_status').html(response.address_document_translate);
        swal({
            position: 'bottom',
            type: 'success',
            toast: 'true',
            title: response.message,
            showConfirmButton: false,
            timer: 6000
        });
    },
    error: function (file, response) {

        swal({
            position: 'bottom',
            type: 'error',
            toast: 'true',
            title: response.message,
            showConfirmButton: false,
            timer: 6000
        });

        this.removeFile(file)
    }

};

