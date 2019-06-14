var nome = $('#nome');
var email = $('#email');
var telefone = $('#telefone');
var cpf = $('#cpf');
var card_number = $('#card-number');
var card_name = $('#card-name');
var card_cpf = $('#card-cpf');
var card_expiration = $('#card-expiration');
var card_cvv = $('#card-cvv');

function validarCheckout(forma_pagamento){

    $('input').removeClass('is-invalid');

    var validacao = true;

    var nome_tamanho = nome.val().length;
    if(nome_tamanho < 5 || nome.val().indexOf(" ") == -1){
        nome.addClass('is-invalid');
        validacao = false;
    }

    var emailFilter=/^.+@.+\..{2,}$/;
    var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
    if(!(emailFilter.test(email.val()))||email.val().match(illegalChars)){        
        email.addClass('is-invalid');
        validacao = false;
    }

    var str_telefone = telefone.val().replace(/[^0-9]/g, '');
    if(str_telefone.length < 10){
        telefone.addClass('is-invalid');
        validacao = false;
    }

    var str_cpf = cpf.val().replace(/[^0-9]/g, '');
    if(! verificarCPF(str_cpf) ) {
        cpf.addClass('is-invalid');
        validacao = false;
    }

    if(forma_pagamento == 'cartao'){

        var str_cartao = card_number.val().replace(/[^0-9]/g, '');
        if(str_cartao.length < 16){
            card_number.addClass('is-invalid');
            validacao = false;
        }

        if(card_name.val().length < 5){
            card_name.addClass('is-invalid');
            validacao = false;
        }

        var str_card_cpf = card_cpf.val().replace(/[^0-9]/g, '');    
        if(! verificarCPF(str_card_cpf) ) {
            card_cpf.addClass('is-invalid');
            validacao = false;
        }
        if(card_expiration.val().length < 5){
            card_expiration.addClass('is-invalid');
            validacao = false;
        }
        if(card_cvv.val().length < 3){
            card_cvv.addClass('is-invalid');
            validacao = false;
        }
    }

    return validacao;
}

function verificarCPF(cpf){

    if( cpf.length == 11 ) {

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
        if ( (v[0] != cpf[9]) || (v[1] != cpf[10]) ) {

            return false;
        }

        return true;
    }
    else {
        return false;
    }
}

$(document).ready(function(){

    $('#nome').on('blur', function(){
        nome.removeClass('input-error');
        nome.removeClass('is-invalid');
        var nome_tamanho = nome.val().length;
        if(nome_tamanho < 5 || nome.val().indexOf(" ") == -1){
            nome.addClass('input-error');
        }
    });
    $('#cpf').on('blur', function(){
        cpf.removeClass('input-error');
        cpf.removeClass('is-invalid');
        var str_cpf = cpf.val().replace(/[^0-9]/g, '');
        if(! verificarCPF(str_cpf) ) {
            cpf.addClass('input-error');
        }
    });
    $('#email').on('blur', function(){
        email.removeClass('input-error');
        email.removeClass('is-invalid');
        var emailFilter=/^.+@.+\..{2,}$/;
        var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
        if(!(emailFilter.test(email.val()))||email.val().match(illegalChars)){        
            email.addClass('input-error');
        }
    });
    $('#telefone').on('blur', function(){
        telefone.removeClass('input-error');
        telefone.removeClass('is-invalid');
        var str_telefone = telefone.val().replace(/[^0-9]/g, '');
        if(str_telefone.length < 10){
            telefone.addClass('input-error');
        }
    });

    $('#card-number').on('blur',function(){
        card_number.removeClass('is-invalid');
        card_number.removeClass('input-error');
        var str_cartao = card_number.val().replace(/[^0-9]/g, '');
        if(str_cartao.length < 16){
            card_number.addClass('input-error');
        }
    });
    $('#card-number').on('focus',function(){
        validarDadosPessoais();
    });
    $('#card-name').on('blur', function(){
        card_name.removeClass('is-invalid');
        card_name.removeClass('input-error');
        if(card_name.val().length < 5){
            card_name.addClass('input-error');
        }
    });
    $('#card-name').on('focus',function(){
        validarDadosPessoais();
    });
    $('#card-cpf').on('blur',function(){
        card_cpf.removeClass('is-invalid');
        card_cpf.removeClass('input-error');
        var str_card_cpf = card_cpf.val().replace(/[^0-9]/g, '');    
        if(! verificarCPF(str_card_cpf) ) {
            card_cpf.addClass('input-error');
        }
    });
    $('#card-cpf').on('focus',function(){
        validarDadosPessoais();
    });
    $('#card-expiration').on('blur', function(){
        card_expiration.removeClass('is-invalid');
        card_expiration.removeClass('input-error');
        if(card_expiration.val().length < 5){
            card_expiration.addClass('input-error');
        }
    });
    $('#card-expiration').on('focus',function(){
        validarDadosPessoais();
    });
    $('#card-cvv').on('blur', function(){
        card_cvv.removeClass('is-invalid');
        card_cvv.removeClass('input-error');        
        if(card_cvv.val().length < 3){
            card_cvv.addClass('input-error');
            validacao = false;
        }
    });
    $('#card-cvv').on('focus',function(){
        validarDadosPessoais();
    });

    $('#selecao_frete').on('click', function(){
        validarDadosPessoais();
    });
    $('.link').on('click', function(){
        validarDadosPessoais();
    });

    $('#editar_dados_pessoais').on('click', function(e){
        e.preventDefault();
        $('#resumo_dados_pessoais').hide();
        $('#dados_pessoais').show();
    });

    $('#editar_dados_entrega').on('click', function(e){
        e.preventDefault();
        $('#resumo_dados_entrega').hide();
        $('#dados_entrega').show();
    });

});

function validarDadosPessoais(){

    var validacao = true;

    var nome_tamanho = nome.val().length;
    if(nome_tamanho < 5 || nome.val().indexOf(" ") == -1){
        nome.addClass('input-error');
        validacao = false;
    }

    var emailFilter=/^.+@.+\..{2,}$/;
    var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
    if(!(emailFilter.test(email.val()))||email.val().match(illegalChars)){        
        email.addClass('input-error');
        validacao = false;
    }

    var str_telefone = telefone.val().replace(/[^0-9]/g, '');
    if(str_telefone.length < 10){
        telefone.addClass('input-error');
        validacao = false;
    }

    var str_cpf = cpf.val().replace(/[^0-9]/g, '');
    if(! verificarCPF(str_cpf) ) {
        cpf.addClass('input-error');
        validacao = false;
    }

    if(validacao){
        $('#resumo_nome').html($('#nome').val());
        $('#resumo_cpf').html($('#cpf').val());
        $('#resumo_telefone').html($('#telefone').val());
        $('#resumo_email').html($('#email').val());

        $('#dados_pessoais').hide();
        $('#resumo_dados_pessoais').show();
    }


}


