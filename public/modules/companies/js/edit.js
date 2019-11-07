$(document).ready(function () {
    //On Ready
    //Clear all content of form
    let redirectBackLink = $("#redirect_back_link");
    let companyUpdateForm = $("#company_update_form");
    let companyBankUpdateForm = $("#company_bank_update_form");
    let dropzoneDocuments = $("#dropzoneDocuments");
    initLinks();
    initForm();

    $(document).on("blur", '#routing_number', function () {
        $.ajax({
            method: "GET",
            url: "https://www.routingnumbers.info/api/data.json?rn=" + $("#routing_number").val(),
            success: function success(data) {
                if (data.message === 'OK') {
                    $("#bank").val(data.customer_name);
                } else {
                    alertCustom('error', data.message);
                    $('#routing_number').focus();
                }
            }
        });
    });
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
////Functions
    function initLinks() {
        let encodedId = extractIdFromPathName();
        let origin = $(location).attr('origin');
        let path = origin + '/companies';
        let apiPath = origin + '/api/companies';
        redirectBackLink.attr('href', path);
        companyUpdateForm.attr('action', (apiPath + "/" + encodedId));
        companyBankUpdateForm.attr('action', (apiPath + "/" + encodedId));
        dropzoneDocuments.attr('action', (apiPath + '/uploaddocuments'));
    }
    function initForm() {
        //Get CompanyId from path
        let encodedId = extractIdFromPathName();
        //Get Company data from laravel api
        $.ajax({
            method: "GET",
            url: "/api/companies/" + encodedId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let company = response.company;
                let lists = {bank: response.banks};
                let functions = {bank: selectItemsFunction};
                fillAllFormInputsWithModel('company_update_form', company);
                fillAllFormInputsWithModel('company_bank_update_form', company, lists, functions);
                $("#company_id").attr('value', company.id_code);

                $("#td_bank_status").append("<span class='badge badge-" + getStatusBadge(company.bank_document_status) + "'>" + company.bank_document_translate + "</span>");
                $("#td_address_status").append("<span class='badge badge-" + getStatusBadge(company.address_document_status) + "'>" + company.address_document_translate + "</span>");
                $("#td_contract_status").append("<span class='badge badge-" + getStatusBadge(company.contract_document_status) + "'>" + company.contract_document_translate + "</span>");
                configSubmits();

                //mascara cnpj
                var optionsCompanyDocument = {
                    onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {

                        var masks = ['000.000.000-000', '00.000.000/0000-00'];
                        var mask = (identificatioNumber.length > 14) ? masks[1] : masks[0];
                        $('#company_document').mask(mask, options);
                    }
                };
                $('#company_document').val().replace(/\D/g, '').length > 14 ? $('#company_document').mask('00.000.000/0000-00', optionsCompanyDocument) : $('#company_document').mask('000.000.000-000', optionsCompanyDocument);

                $("#support_telephone").mask("(00) 0000-00009");
            }
        });
    }
    //Config Submit
    function configSubmits() {
        companyUpdateForm.on("submit", function (event) {
            event.preventDefault();
            let form_data = new FormData(document.getElementById('company_update_form'));
            loadingOnScreen();
            $.ajax({
                method: "POST",
                url: companyUpdateForm.attr('action'),
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
                    alertCustom('success', response.message);
                    loadingOnScreenRemove();
                }
            });
        });
        companyBankUpdateForm.on("submit", function (event) {
            event.preventDefault();
            let form_data = new FormData(document.getElementById('company_bank_update_form'));
            loadingOnScreen();
            $.ajax({
                method: "POST",
                url: companyBankUpdateForm.attr('action'),
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
                    alertCustom('success', response.message);
                }
            });
        });
    }
    function getStatusBadge(bankDocumentStatus) {
        let badge = null;
        switch (bankDocumentStatus) {
            case 1://'pending':
                badge = 'primary';
                break;
            case 2://'analyzing':
                badge = 'pendente';
                break;
            case 3://'approved':
                badge = 'success';
                break;
            case 4://'refused':
                badge = 'danger';
                break;
            default:
                badge = '';
                break;
        }

        return badge;
    }
    function selectItemsFunction(item) {
        return {value: item.code, text: (item.code + ' - ' + item.name)};
    }
});
Dropzone.options.dropzoneDocuments = {
    headers: {
        'Authorization': $('meta[name="access-token"]').attr('content'),
        'Accept': 'application/json',
    },
    paramName: "file",
    maxFilesize: 2, // MB
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    //uploadMultiple: true,
    accept: function accept(file, done) {
        var dropz = this;
        swal({
            title: 'Qual é o tipo do documento?',
            type: 'warning',
            input: 'select',
            inputPlaceholder: 'Selecione o documento',
            inputOptions: {
                '1': 'Extrato bancário',
                '2': 'Comprovante de residência',
                '3': 'Contrato social'
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
        if (response.data.bank_document_translate.status === 3) {
            $('#td_bank_status').html('<span class="badge badge-aprovado">' + response.data.bank_document_translate.message + '</span>');
        } else if (response.data.bank_document_translate.status === 2) {
            $('#td_bank_status').html('<span class="badge badge-pendente">' + response.data.bank_document_translate.message + '</span>');
        }
        if (response.data.address_document_translate.status === 3) {
            $('#td_address_status').html('<span class="badge badge-aprovado">' + response.data.address_document_translate.message + '</span>');
        } else if (response.data.address_document_translate.status === 2) {
            $('#td_address_status').html('<span class="badge badge-pendente">' + response.data.address_document_translate.message + '</span>');
        }
        if (response.data.contract_document_translate.status === 3) {
            $('#td_contract_status').html('<span class="badge badge-aprovado">' + response.data.contract_document_translate.message + '</span>');
        } else if (response.data.contract_document_translate.status === 2) {
            $('#td_contract_status').html('<span class="badge badge-pendente">' + response.data.contract_document_translate.message + '</span>');
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
