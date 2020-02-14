let companyStatus = {
    pending: 'badge badge-primary',
    analyzing: 'badge badge-pending',
    approved: 'badge badge-success',
    refused: 'badge badge-danger',
};

let companyStatusTranslated = {
    pending: 'Pendente',
    analyzing: 'Em análise',
    approved: 'Aprovado',
    refused: 'Recusado',
};

let companyTypeDocument = {
    bank_document_status: 'Comprovante de extrato bancário',
    address_document_status: 'Comprovante de endereço',
    contract_document_status: 'Comprovante de contrato social'
};

var initForm = null;
var htmlTable = null;
$(document).ready(function () {
    //On Ready
    //Clear all content of form
    let redirectBackLink = $("#redirect_back_link");
    let companyUpdateForm = $("#company_update_form");
    let companyBankUpdateForm = $("#company_bank_update_form");
    let companyBankUpdateRoutingForm = $("#company_bank_routing_number_form");
    initLinks();

    //Functions
    function initLinks() {
        let encodedId = extractIdFromPathName();
        let origin = $(location).attr('origin');
        let path = origin + '/companies';
        let apiPath = origin + '/api/companies';
        redirectBackLink.attr('href', path);
        companyUpdateForm.attr('action', (apiPath + "/" + encodedId));
        companyBankUpdateForm.attr('action', (apiPath + "/" + encodedId));
        companyBankUpdateRoutingForm.attr('action', (apiPath + "/" + encodedId));
    }

    function htmlModifyAlerts(company) {
        if (companyStatusTranslated[company.bank_document_status] === 'Aprovado') {
            $("#details-document-person-juridic-bank-document").hide();
        } else {
            $("#details-document-person-juridic-bank-document").show();
        }
        if (companyStatusTranslated[company.address_document_status] === 'Aprovado') {
            $(".info-complemented").attr('disabled', 'disabled');
            $("#details-document-person-juridic-address").hide();
        } else {
            $(".info-complemented").removeAttr('disabled');
            $("#details-document-person-juridic-address").show();
        }
        if (companyStatusTranslated[company.contract_document_status] === 'Aprovado') {
            $("#company_document").attr('disabled', 'disabled');
            $("#details-document-person-juridic-contract").hide();
        } else {
            $("#company_document").removeAttr('disabled');
            $("#details-document-person-juridic-contract").show();
        }

        if (companyStatusTranslated[company.bank_document_status] === 'Pendente' || companyStatusTranslated[company.bank_document_status] === 'Recusado') {
            $("#text-alert-documents").show();
        } else if (companyStatusTranslated[company.address_document_status] === 'Pendente' || companyStatusTranslated[company.address_document_status] === 'Recusado') {
            $("#text-alert-documents").show();
        } else if (companyStatusTranslated[company.contract_document_status] === 'Pendente' || companyStatusTranslated[company.contract_document_status] === 'Recusado') {
            $("#text-alert-documents").show();
        } else {
            $("#text-alert-documents").hide();
        }
    }

    initForm = function () {
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
                let banks = response.banks;

                $('#fantasy_name').val(company.fantasy_name);
                $('#company_document').val(company.company_document);
                $('#business_website').val(company.business_website);
                $('#support_email').val(company.support_email);
                $('#support_telephone').val(company.support_telephone);
                $('#zip_code').val(company.zip_code);
                $('#street').val(company.street);
                $('#number').val(company.number);
                $('#neighborhood').val(company.neighborhood);
                $('#complement').val(company.complement);
                $('#state').val(company.state);
                $('#city').val(company.city);
                $('#country').val(company.country);

                if (company.country === 'usa') {
                    $('#rounting_number').val(company.bank).trigger('input');
                    $('#account_routing_number').val(company.account);
                    //$('#swift-code-info').show();
                    $('#company_bank_update_form').hide();
                    $('#company_bank_routing_number_form').show();
                } else {
                    if (company.country === 'brazil' || company.country === 'portugal') {
                        for (let bank of banks) {
                            $('#bank').append(`<option value="${bank.code}" ${bank.code === company.bank ? 'selected' : ''}>${bank.code} - ${bank.name}</option>`)
                        }
                    } else {
                        $('.div-bank').html('');
                        $('.div-bank').append(`<label for="bank">Banco</label><input id="bank" name="bank"  value="${company.bank}" class="input-pad" placeholder="Nome do banco">`);
                    }
                    $('#agency').val(company.agency);
                    $('#agency_digit').val(company.agency_digit);
                    $('#account').val(company.account);
                    $('#account_digit').val(company.account_digit);

                    $("#company_id").val(company.id_code);

                }
                htmlModifyAlerts(company);

                $("#td-bank-status").html('').append(`
                    <span class='badge ${companyStatus[company.bank_document_status]}'>
                        ${companyStatusTranslated[company.bank_document_status]}
                    </span>
                `);

                $("#td-address-status").html('').append(`
                    <span class='badge ${companyStatus[company.address_document_status]}'>
                        ${companyStatusTranslated[company.address_document_status]}
                    </span>
                    `);
                $("#td-contract-status").html('').append(`
                    <span class='badge ${companyStatus[company.contract_document_status]}'>
                        ${companyStatusTranslated[company.contract_document_status]}
                    </span>
                    `);
                verifyDocuments(company);
                // getRefusedDocuments(response.company.refusedDocuments);
                verifyCompanyAddress(company);

                //só mostra o campo estado se o país for Brasil e Estados Unidos
                if (company.country == 'brazil' || company.country == 'usa') {
                    $(".div-state").show();
                }
                //verifica país da empresa e coloca mascara
                changeMaskByCompanyCountry(company);
                loadLabelsByCountry(company);
                openDocument();

                $(".details-document-person-juridic").on('click', function () {
                    $("#document-type").val('');
                    $("#modal-title-document-person-juridic").html('');

                    $("#document-type").val($(this).data('document'));
                    $("#modal-title-document-person-juridic").html(`${companyTypeDocument[$(this).data('document')]}`);

                    $("#modal-document-person-juridic").modal('show');

                    Dropzone.forElement('#dropzoneDocumentsJuridicPerson').removeAllFiles(true);

                    getDocuments(encodedId);

                });
            }
        });
    };

    initForm();

    //Couting number
    $('#rounting_number').on('input', function () {

        let value = $(this).val();

        if (value.length === 9 && !isNaN(parseInt(value))) {
            $.ajax({
                url: 'https://www.routingnumbers.info/api/data.json?rn=' + $(this).val(),
                success: response => {
                    if (!isEmpty(response.customer_name)) {
                        $('#bank_routing_number').val(response.customer_name);
                    } else {
                        $('#bank_routing_number').val('Digite um routing number válido...');
                    }
                },
                error: response => {
                    $('#bank_routing_number').val('Digite um routing number válido...');
                    alertCustom('error', 'Erro ao buscar routing number');
                }
            });
        } else {
            $('#bank_routing_number').val('Digite um routing number válido...');
        }
    });

    //Config Submit
    companyUpdateForm.on("submit", function (event) {
        event.preventDefault();
        $("#company_document").remove('disabled');
        $(".info-complemented").removeAttr('disabled');

        $("#country").removeAttr('disabled');
        let form_data = new FormData(document.getElementById('company_update_form'));
        $('#country').attr('disabled', true);

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
                // $("#company_document").attr('disabled', 'disabled');
            },
            success: function success(response) {
                alertCustom('success', response.message);
                loadingOnScreenRemove();
                // $("#company_document").attr('disabled', 'disabled');
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
                initForm();
            }
        });
    });
    companyBankUpdateRoutingForm.on("submit", function (event) {
        event.preventDefault();
        let form_data = new FormData(document.getElementById('company_bank_routing_number_form'));
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: companyBankUpdateRoutingForm.attr('action'),
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
                initForm();
            }
        });
    });

    function openDocument() {
        $(".document-url").on("click", function (e) {
            e.preventDefault();
            let documentUrl = $(this).attr('href');
            loadingOnScreen();
            $.ajax({
                method: "POST",
                url: '/api/companies/opendocument',
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
    }

    //vefica se os documentos da empresa estão aprovados e desabilita todos os inputs
    function verifyDocuments(company) {
        if (company.address_document_status == 3 && company.bank_document_status == 3 && company.contract_document_status == 3) {
            $(".form-basic-informations :input[type=text]").attr("disabled", true);
            $(".dz-hidden-input").prop("disabled", true);
            $('#dropzoneDocuments').css({
                'cursor': 'not-allowed',
            });
            $('.text-dropzone').css({
                'cursor': 'not-allowed',
            });
        }
    }

    function verifyCompanyAddress(company) {
        if (company.zip_code == '' || company.street == '' || company.number == '' || company.neighborhood == '' || company.state == '' || company.city == '' || company.country == '') {
            $('#row_dropzone_documents').hide();
            $('#div_address_pending').show();
        } else {
            $('#row_dropzone_documents').show();
            $('#div_address_pending').hide();
        }
    }
    function changeMaskByCompanyCountry(company) {
        if (company.country == 'brazil') {
            $('#zip_code').mask('00000-000');
            $("#support_telephone").mask("+55 (00) 0000-00009");
            $('#company_document').mask('00.000.000/0000-00');
            zipCode();
        } else {
            $('#support_telephone').mask('+0#');
        }
    }
    function loadLabelsByCountry(company) {
        let companyDocumentName = {
            brazil: 'CNPJ',
            portugal: 'NIPC',
            usa: 'EIN',
            germany: 'NIF',
            spain: 'CIF',
            france: 'SIRET',
            italy: 'Partita IVA'
        };
        $('.label-document').text(companyDocumentName[company.country]);
        $('#company_document').attr('placeholder', companyDocumentName[company.country]);
    }
    function getDocuments(encodedId) {
        loadOnTable('#table-body-document-person-juridic', '#table-document-person-juridic');
        $.ajax({
            method: 'POST',
            url: `/api/companies/${encodedId}/getdocuments`,
            data: {
                document_type: $("#document-type").val()
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();

            },
            success: function success(response) {
                $("#loaderLine").remove();
                htmlTable(response.data);
                $("#company-id").val(encodedId);
                $("#modal-document-person-fisic").modal('show');
            },
        });
    }

    function zipCode() {
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
    }

    htmlTable = function (dataTable) {
        $("#loaderLine").remove();

        let dados = '';
        $("#table-body-document-person-juridic").html('');
        if (dataTable.length == 0) {
            $("#table-body-document-person-juridic").append('<tr><td class="text-center" colspan="4">Nenhum documento enviado</td></tr>');
        } else {
            $("#table-body-document-person-juridic").html('');
            $.each(dataTable, function (index, value) {
                dados = `
                    <tr>
                        <td class='text-center'>${value.date}</td>
                        <td class='text-center' style='cursor: pointer;'>
                            <span class='badge ${companyStatus[value.status]}'>
                                ${companyStatusTranslated[value.status]}
                            </span>
                        </td>`;

                if (value.refused_reason != '' && value.refused_reason != null) {
                    dados += `<td class='text-center' style='color: red;'>${value.refused_reason}</td>`;
                } else {
                    dados += `<td class='text-center' style='color:red;'></td>`;
                }

                dados += `
                    <td class='text-center'>
                        <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'><i class='material-icons gradient'>remove_red_eye</i></a>
                    </td>
                    </tr>
                `;
                $("#table-body-document-person-juridic").append(dados);

            });
        }

    }

});

Dropzone.autoDiscover = false;

const myDropzone = new Dropzone('#dropzoneDocumentsJuridicPerson', {
    headers: {
        'Authorization': $('meta[name="access-token"]').attr('content'),
        'Accept': 'application/json',
    },
    paramName: "file",
    maxFilesize: 2,
    url: '/api/companies/uploaddocuments',
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    previewsContainer: ".dropzone-previews",
    success: function (file, response) {
        alertCustom('success', response.message);

        if (file.previewElement) {
            return file.previewElement.classList.add('dz-success');
        }

    }, error: function (file, response) {
        if (response == 'Max filesize') {
            errorAjaxResponse('O documento é muito grande. Tamanho maximo: 2mb.');
        } else if (response == 'upload files of this type') {
            errorAjaxResponse('O documento deve estar em um dos seguintes formatos: jpeg, jpg, png.');
        } else {
            errorAjaxResponse(response);
        }

        myDropzone.removeFile(file);

    }, complete: function () {
        loadOnTable('#table-body-document-person-juridic', '#table-document-person-juridic');
        let codeId = extractIdFromPathName();

        $.ajax({
            method: 'POST',
            url: `/api/companies/${codeId}/getdocuments`,
            data: {
                document_type: document.querySelector('#document-type').value
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();
            }, success: function (response) {
                htmlTable(response.data);
                initForm();
            }
        });
    }
});


