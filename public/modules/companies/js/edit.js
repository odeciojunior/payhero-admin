$(document).ready(function () {
    $("#company_update_form").on("submit", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('company_update_form'));

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
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                alertCustom('success', response.message);
            }
        });

    });

    $("#company_bank_update_form").on("submit", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('company_bank_update_form'));

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
                if (response.status == '422') {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                }
            },
            success: function (response) {
                alertCustom('success', response.message);
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
        $('#td_bank_status').html(response.bank_document_translate);
        $('#td_address_status').html(response.address_document_translate);
        $('#td_contract_status').html(response.contract_document_translate);
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
