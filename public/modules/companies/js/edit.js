$(document).ready(function () {

    var options = {
        onKeyPress: function (identificatioNumber, e, field, options) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            var mask = (identificatioNumber.length > 14) ? masks[1] : masks[0];
            $('#cnpj').mask(mask, options);
        }
    };

    //mascara cnpj
    $('#cnpj').mask('000.000.000-000', options);

    $("#company_update_form").on("submit", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('company_update_form'));
        loadingOnScreen()
        $.ajax({
            method: "POST",
            url: $('#company_update_form').attr('action'),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove()
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                alertCustom('success', response.message);
                loadingOnScreenRemove()
            }
        });

    });

    $("#company_bank_update_form").on("submit", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('company_bank_update_form'));
        loadingOnScreen()
        $.ajax({
            method: "POST",
            url: $('#company_bank_update_form').attr('action'),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove()
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                loadingOnScreenRemove()
                alertCustom('success', response.message);
            }
        });

    });

    $(document).on("blur", '#routing_number', function () {
        $.ajax({
            method: "GET",
            url: "https://www.routingnumbers.info/api/data.json?rn=" + $("#routing_number").val(),
            success: function (data) {
                if (data.message == 'OK') {
                    $("#bank").val(data.customer_name);
                } else {
                    alertCustom('error', data.message);
                    $('#routing_number').focus();
                }
            }
        });
    });

    $("#brazil_zip_code").on("input", function () {

        var zip_code = $('#brazil_zip_code').val().replace(/[^0-9]/g, '');

        if (zip_code.length != 8)
            return false;

        $.ajax({
            url: "https://viacep.com.br/ws/" + zip_code + "/json/",
            type: "GET",
            cache: false,
            async: false,
            success: function (response) {
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

});

Dropzone.options.dropzoneDocuments = {
    paramName: "file",
    maxFilesize: 2, // MB
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    //uploadMultiple: true,
    accept: function (file, done) {
        var dropz = this;

        swal({
            title: 'Qual é o tipo do documento?',
            type: 'warning',
            input: 'select',
            inputPlaceholder: 'Selecione o documento',
            inputOptions: {
                '1': 'Extrato bancário',
                '2': 'Comprovante de residência',
                '3': 'Contrato social',
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

        if (response.data.bank_document_translate.status == 3) {

            $('#td_bank_status').html('<span class="badge badge-aprovado">' +response.data.bank_document_translate.message+ '</span>');
        } else {
            $('#td_bank_status').html('<span class="badge badge-pendente">' +response.data.bank_document_translate.message+ '</span>');
        }

        if (response.data.address_document_translate.status == 3) {

            $('#td_address_status').html('<span class="badge badge-aprovado">' +response.data.address_document_translate.message+ '</span>');
        } else {
            $('#td_address_status').html('<span class="badge badge-pendente">' +response.data.address_document_translate.message+ '</span>');
        }

        if (response.data.contract_document_translate.status == 3) {

            $('#td_contract_status').html('<span class="badge badge-aprovado">' +response.data.contract_document_translate.message+ '</span>');
        } else {
            $('#td_contract_status').html('<span class="badge badge-pendente">' +response.data.contract_document_translate.message+ '</span>');
        }

        //<span class="badge badge-pendente"> {{ $company->bank_document_translate }} </span>

        // $('#td_bank_status').html(response.bank_document_translate);
        // $('#td_address_status').html(response.address_document_translate);
        // $('#td_contract_status').html(response.contract_document_translate);
        // done();

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
            title: response,
            showConfirmButton: false,
            timer: 6000
        });

        this.removeFile(file)
    }

};
