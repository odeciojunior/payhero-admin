$(document).ready(function () {
    verify();

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
                country: $('#country_2').val(),
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

    let companyDocumentName = {
        brazil: 'CNPJ',
        portugal: 'NIPC',
        usa: 'EIN',
        germany: 'NIF',
        spain: 'CIF',
        france: 'SIRET',
        italy: 'Partita IVA',
        unitedkingdom: 'CNPJ'
    };

    $("#country").on('change', function () {

        $("#company_document_label").text(companyDocumentName[$(this).val()]);
        $('#company_document').attr('placeholder', companyDocumentName[$(this).val()]);

        if ($(this).val() == 'brazil') {
            $('#company_document').mask('00.000.000/0000-00');
            $('#fantasy_name').attr('disabled', true);
        } else {
            $('#company_document').unmask();
            $('#fantasy_name').attr('disabled', false);
        }

    });

    $("#country_2").on('change', function () {

        $("#company_document_2_label").text(companyDocumentName[$(this).val()]);
        $('#company_document_2').attr('placeholder', companyDocumentName[$(this).val()]);

        if ($(this).val() == 'brazil') {
            $('#company_document_2').mask('00.000.000/0000-00');
            $('#fantasy_name_2').attr('disabled', true);
        } else {
            $('#company_document_2').unmask();
            $('#fantasy_name_2').attr('disabled', false);
        }
    });
    $("#country").change();
    $("#country_2").change();

    $('#company_document_2').on('keyup', function (e) {
        let cnpj = this.value.replace(/[^0-9]/g,'');
        if(cnpj.length == 14 && $('#country_2').val() == 'brazil') {
            $.ajax({
                method: "POST",
                url: "/api/companies/consultcnpj",
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {cnpj: cnpj},
                async: false,
                error: function error(response) {
                    console.log(response);
                    $('#fantasy_name_2').attr('disabled', false);
                },
                success: function success(response) {
                    console.log(response);
                    if (response.name != '') {
                        $('#fantasy_name_2').val(response.name);
                        $('#fantasy_name_2').attr('disabled', true);
                    } else {
                        $('#fantasy_name_2').attr('disabled', false);
                    }
                }
            });
        }

    });

    $('#company_document').on('keyup', function (e) {
        let cnpj = this.value.replace(/[^0-9]/g,'');
        if(cnpj.length == 14 && $('#country').val() == 'brazil') {
            $.ajax({
                method: "POST",
                url: "/api/companies/consultcnpj",
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {cnpj: cnpj},
                async: false,
                error: function error(response) {
                    console.log(response);
                    $('#fantasy_name').attr('disabled', false);
                },
                success: function success(response) {
                    console.log(response);
                    if (response.name != '') {
                        $('#fantasy_name').val(response.name);
                        $('#fantasy_name').attr('disabled', true);
                    } else {
                        $('#fantasy_name').attr('disabled', false);
                    }
                }
            });
        }

    });

});
