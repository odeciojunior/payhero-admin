$(document).ready(function () {
    verify();
    updateForm();

    function updateForm() {
        // var options = {
        //     onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
        //         var masks = ['000.000.000-00', '00.000.000/0000-00'];
        //         var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
        //         $('#brazil_company_document').mask(mask, options);
        //     }
        // };
        var options = {
            onKeyPress: function (cpf, ev, el, op) {
                var masks = ['000.000.000-000', '00.000.000/0000-00'];
                $('#brazil_company_document').mask((cpf.length > 14) ? masks[1] : masks[0], op);
            }
        }
        $('#brazil_company_document').length > 11 ? $('#brazil_company_document').mask('00.000.000/0000-00', options) : $('#brazil_company_document').mask('000.000.000-00#', options);
    }
    function verify() {
        $.ajax({
            method: "GET",
            url: "/api/companies/verify",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (response.has_physical_company == 'true') {
                    $('#company_document_label').text('CNPJ');
                    $('.company_document_1').mask('00.000.000/0000-00');
                    $('#company_document').attr('placeholder', 'CNPJ');
                    $('#div1').show();
                    $('#div-company-document').show();
                } else {
                    $('.company_document_2').mask('00.000.000/0000-00');
                    $('#div2').show();
                }
            }
        });
    }
    $('.btn-next-div1').on('click', function (e) {
        e.preventDefault();
        let companyDocumentVal = $('.company_document_1').val().replace(/[^0-9]/g, '');
        let fantasyNameVal = $('.fantasy_name_1').val();

        if ($('.company_document_1').val() != '') {
            let result = verifyEqualCNPJ($('.company_document_1').val());
            if (result) {
                return false;
            }
        }
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/companies",
            dataType: "json",
            data: {
                company_document: companyDocumentVal,
                fantasy_name: fantasyNameVal,
                country: $('#country').val(),
                company_type: 2
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                window.location.replace('/companies/' + response.idEncoded + '/edit?type=2 ');
            }
        });
    });

    $('#btn-juridical-person').on('click', function (e) {
        e.preventDefault();
        $('#div-juridical-person').slideDown('fast');
        $('#text-main').hide();
        $('#text-company').show();
        $('#btn-physical-person').hide();
        $(this).hide();
    });
    $('.btn-next-div2').on('click', function (e) {
        e.preventDefault();
        let companyDocumentVal = $('.company_document_2').val().replace(/[^0-9]/g, '');
        let fantasyNameVal = $('.fantasy_name_2').val();

        if ($('.company_document_2').val() != '') {
            let result = verifyEqualCNPJ($('.company_document_2').val());
            if (result) {
                return false;
            }
        }
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/companies",
            dataType: "json",
            data: {
                company_document: companyDocumentVal,
                fantasy_name: fantasyNameVal,
                country: $('#country').val(),
                company_type: 2
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                window.location.replace('/companies/' + response.idEncoded + '/edit?type=2 ');
            }
        });
    });

    $('#btn-physical-person').on('click', function (e) {
        e.preventDefault();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/companies",
            dataType: "json",
            data: {country: $('#country').val(), company_type: 1},
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                window.location.replace('/companies/' + response.idEncoded + '/edit?type=1 ');
            }
        });
    });
    function verifyEqualCNPJ(cnpj) {
        var result = '';

        $.ajax({
            method: "POST",
            url: "/api/companies/verifycnpj",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {company_document: cnpj},
            async: false,
            error: function error(response) {
            },
            success: function success(response) {
                if (response.cnpj_exist == 'true') {
                    alertCustom('error', response.message);
                    result = true;
                } else {
                    result = false;
                }
            }
        });
        return result;
    }
    // $("#create_form").on("submit", function (event) {
    //     event.preventDefault();
    //
    //     $.ajax({
    //         method: "POST",
    //         url: "/api/companies",
    //         dataType: "json",
    //         data: $("#create_form").serialize(),
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         error: function (response) {
    //             errorAjaxResponse(response);
    //         },
    //         success: function success(response) {
    //             alertCustom('success', response.message);
    //             window.location.replace('/companies/' + response.idEncoded + '/edit ');
    //         }
    //     });
    // });
});
