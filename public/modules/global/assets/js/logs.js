var id_sessao_log = hashCode( ''+$.now() );
var plano = codigo_identificador;
var ip = '';
var user_agent = navigator.userAgent;
var hora_acesso = getHorarioAtual();

$(document).ready(function(){

    primeiroAcesso();
});

$(window).on("beforeunload", function() {
    saindoPagina();
});

$('#dados_pessoais input').on('blur', function(){

    if($('#nome').val() == '')
        return;
    if($('#email').val() == '')
        return;
    if($('#cpf').val() == '')
        return;
    if($('#telefone').val() == '')
        return;

    identificacaoCompleta();
});

$('#dados_entrega input').on('blur', function(){

    if($('#cep').val() == '')
        return;
    if($('#endereco').val() == '')
        return;
    if($('#numero-casa').val() == '')
        return;
    if($('#cidade').val() == '')
        return;
    if($('#estado').val() == '')
        return;

    entregaCompleta();
});

$('#termos_de_uso').on('click', function(){
    clickTermosUso();
});

$('#politica_privacidade').on('click', function(){
    clickPoliticaPrivacidade();
});


function getLogParametros(){

    var dados = {
        id_sessao_log       : id_sessao_log,
        plano               : plano,
        user_agent          : navigator.userAgent,
        ip                  : ip,
        hora_acesso         : hora_acesso,
        horario             : getHorarioAtual(),
        forward             : url_forward,
        referencia          : url_referencia,
        nome                : $('#nome').val(),
        email               : $('#email').val(),
        cpf                 : $('#cpf').val(),
        celular             : $('#telefone').val(),
        cep                 : $('#cep').val(),
        endereco            : $('#rua').val(),
        numero              : $('#numero').val(),
        bairro              : $('#bairro').val(),
        cidade              : $('#cidade').val(),
        estado              : $('#estado').val(),
        valor_frete         : $(".valor_frete").html(),
        valor_cupom         : $('.valor_desconto').html(),
        valor_total         : $('.valor_total').html(),
        numero_cartao       : $('#cartao').val() != '' ? 1 : 0,
        nome_cartao         : $('#nome-cartao').val(),
        cpf_cartao          : $('#cpf-cartao').val(),
        mes_cartao          : $('#validade').val() ? 1 : 0,
        ano_cartao          : $('#ano-validade').val() ? 1 : 0,
        codigo_cartao       : $('#codigo-seguranca').val() ? 1 : 0,
        parcelamento        : $('#parcelas').val(),
        erro                : '',
        coockies            : '',
    };

    return dados;
}

function getHorarioAtual(){

    var agora   = new Date();
    var dia     = agora.getDate();
    var mes     = agora.getMonth();
    var ano     = agora.getFullYear();
    var hora    = agora.getHours();
    var min     = agora.getMinutes();
    var seg     = agora.getSeconds();

    return hora + ':' + min + ':' + seg + ' - ' + dia + '/' + mes + '/' + ano;
}

function hashCode (str) {

    var hash = 0;

    if (str.length == 0) 
        return hash;

    for (i = 0; i < str.length; i++) {
        char = str.charCodeAt(i);
        hash = ((hash<<5)-hash)+char;
        hash = hash & hash;
    }

    return hash.toString() + (Math.floor(Math.random() * (99999 - 11111 + 1)) + 11111);
}

$('.dadospessoais input').on('blur', function(){

    if($('#nome').val() == '')
        return;
    if($('#email').val() == '')
        return;
    if($('#cpf').val() == '')
        return;
    if($('#telefone').val() == '')
        return;

    identificacaoCompleta();
});

$('.dadosentrega input').on('blur', function(){

    if($('#cep').val() == '')
        return;
    if($('#rua').val() == '')
        return;
    if($('#numero').val() == '')
        return;
    if($('#cidade').val() == '')
        return;
    if($('#estado').val() == '')
        return;

    entregaCompleta();
});

$('#termos_de_uso').on('click', function(){
    clickTermosUso();
});

$('#politica_privacidade').on('click', function(){
    clickPoliticaPrivacidade();
});

function primeiroAcesso(){

    var dados = '';

    $.get("https://api.ipify.org?format=json", function(response){

        ip = response.ip;

        dados = getLogParametros();

        dados.evento = 'primeiro acesso';
        dados.horario = getHorarioAtual();

        salvarLog(dados);

    });
}

function salvarCoockies(){

    dados = getLogParametros();

    dados.evento = 'salvar coockies';

    salvarLog(dados);
}

function identificacaoCompleta(){

    var dados = getLogParametros();

    dados.evento = 'identificação completa';

    salvarLog(dados);
}

function entregaCompleta(){

    dados = getLogParametros();

    dados.evento = 'entrega completa';

    salvarLog(dados);
}

function submetendoCumpom(){

    dados = getLogParametros();

    dados.evento = 'submit cupom';

    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function saindoPagina(){

    dados = getLogParametros();

    dados.evento = 'saindo da pagina';

    salvarLog(dados);
}

function efetuandoPagamento(){

    dados = getLogParametros();

    dados.evento = 'efetuando pagamento';
    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function clickTermosUso(){

    dados = getLogParametros();

    dados.evento = 'click termos de uso';
    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function clickPoliticaPrivacidade(){

    dados = getLogParametros();

    dados.evento = 'click política de privacidade';
    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function seguranca(){

    dados = getLogParametros();

    dados.evento = 'segurança';
    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function erro(erro){

    dados = getLogParametros();

    dados.evento = 'erro ao efetuar pagamento';
    dados.horario = getHorarioAtual();
    dados.erro = erro;

    salvarLog(dados);

}

function clickTermosUso(){

    dados = getLogParametros();

    dados.evento = 'click termos de uso';
    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function erroCep(){

    dados = getLogParametros();

    dados.evento = 'Cep não encontrado. ';
    dados.horario = getHorarioAtual();

    salvarLog(dados);
}

function salvarLog(informacoes){

    $.post('/logs/salvarlog', informacoes);
}

