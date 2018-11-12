
$("#frete").hide();
$("[data-pag=pagamento]").prop("disabled", true);
$('#fechar_modal').on('click', function(){
    $("#modal_erros").css("display","none");
});
msgInput = {
    nome : "<h4>O nome não está correto. <br> Por favor digite seu nome e seu sobrenome </h4>",
    email : "<h4>O email não está correto. <br> Siga o padrão  fulano@gmail.com </h4>",
    cpf : "<h4>O Cpf não está correto. <br> Digite os 11 digitos do seu CPF  000.000.000-00 </h4>",
    celular : "<h4>O Celular não está correto. <br> Digite o seu Celular (00) 99900-0000 </h4>",
    cep : "<h4>O Cep não está correto. <br> Digite o seu Cep de oito digitos ex: 00000-000 </h4>",
    endereco : "<h4>O Endereço não está correto. <br> Digite o seu Endereço correto ex: Rua Tal </h4>",
    numero : "<h4>O Numero não está correto. <br> Digite o seu Numero correto ex: 000 </h4>",
    complemento : "",
    bairro : "<h4>O Bairro não está correto. <br> Digite o seu Bairro correto ex: Centro </h4>",
    cidade : "<h4>O Cidade não está correto. <br> Digite o seu Cidade correto ex: Ceidade </h4>",
    estado :  "<h4>O Estado não está correto. <br> Selecione um Estado </h4>",
    cartao : "<h4> Preencha todos os numero do Cartão de Crédito. </h4>",
    nomeCartao : "<h4>O nome do <b>Cartão de Credito<b> não está correto. <br> Por favor digite seu nome e seu sobrenome </h4>",
    cpfErro : "<h4>O Cpf não está correto. <br> Verifique seu Cpf. </h4>",
    cpfErroClinete : "<h4>Seu Cpf não está correto. <br> Verifique seu Cpf. </h4>",
    codidgoSeguranca : "<h4> O Codigo de Segurança não está correto. <br> Verifique o codigo de segurança do seu cartão. </h4>",
    mes : "<h4>Selecione o mês de vencimento do seu Cartão de Credito</h4>",
    ano : "<h4>Selecione o Ano de vencimento do seu Cartão de Credito</h4>",
}

$('#nome').keyup(function () { 
    this.value = this.value.replace(/[^A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]/g,'');
    var email = $("#email").val();
    var tamanho = email.length;
    if(tamanho >= 5){
    if($('#email').attr('data-check') == "true"){
            $(".esconderdaddos").removeClass("none");
        } 
    }
});



$('#nome').on('blur', function(e) { 
     
    var nome = limpa($("#nome").val());

    var tamanho = nome.length;

    if(tamanho <= 1){
        status = false;
        erroInput (msgInput.nome);
        //return false;
    }
    else if(tamanho < 5 ){
        $("#nome").val(nome);
        erroInput (msgInput.nome);
        status = false;
        
    }
      else if(nome.indexOf(" ") == -1){
          $("#nome").val(nome);
          erroInput (msgInput.nome);
          status = false;
      }
     else if(tamanho >= 5){
        $("#nome").val(nome);
        status = true;
    }
    else  {
         $("#nome").val(nome);
         erroInput (msgInput.nome);
        status = false;
    }
    var dados = { 'id' : $(this).attr("id"),
        'status' : status};
    valido(dados);

 });


$('#email').mask("A", {
    translation: {
        "A": { pattern: /[\w@\-.+]/, recursive: true }
    }
});



$("#email").on("keyup", function(e){
    var email = $("#email").val();
    var tamanho = email.length;
     if( (tamanho >= 8 ) && (email.indexOf(" ") == -1) && (email.indexOf("@") != -1) && (email.indexOf(".") != -1) ){
        if($('#nome').attr('data-check') == "true"){
            $(".esconderdaddos").removeClass("none");
        } 
    }
});

$('#email').on('blur', function(e) { 
   
    var email = $("#email").val();
    var tamanho = email.length;
    
    if(tamanho <= 1){
        status = false;
        erroInput(msgInput.email);
        //return false;
    }
    else if(tamanho < 8){
        erroInput(msgInput.email);
        
    }
    else if (email.indexOf("@") == -1){
        erroInput(msgInput.email);
        status = false;

    }
     else if( (tamanho >= 8 ) && (email.indexOf(" ") == -1) && (email.indexOf("@") != -1) && (email.indexOf(".") != -1) ){
         status = true;
     }
    else 
    {
        erroInput(msgInput.email);
        status = false;
    }
    var dados = { 'id' : $(this).attr("id"),
        'status' : status};
    valido(dados);

 });


// $("#cpf").keypress(function(){
//     $("#resposta").html(CPF.valida($(this).val()));
// });

// $("#input").blur(function(){
//      $("#resposta").html(CPF.valida($(this).val()));
// });

$("#cpf").on("keyup", function(e){
        var tamanho = $("#cpf").val().replace(/\.|\-/g, '').length;
        if(tamanho == 11 ){
            var cpf = $('#cpf-cartao').val().replace(/\.|\-/g, '');
                status = verificaCpf(cpf);
        if(status == "false"){

            erroInput(msgInput.cpfErroClinete);
        }else {
            if($('#telefone').attr('data-check') == "true"){
                $(".esconderentrega1").removeClass("none");
            }
        } 
    }
});

$("#cpf-cartao").on("keyup", function(e){
    var tamanho = $("#cpf-cartao").val().replace(/\.|\-/g, '').length;
        if(tamanho == 11 ){
            var cpf = $('#cpf-cartao').val().replace(/\.|\-/g, '');
            status = verificaCpf(cpf);
            if(status == "false"){

                erroInput(msgInput.cpfErro);
            }
            var dados = { 'id' : $(this).attr("id"),
                          'status' : status};
            valido(dados);
        }
});

 $("#cpf").mask("999.999.999-99");

 $('#cpf').on('blur', function(e) { 

    var tamanho = $("#cpf").val().replace(/\.|\-/g, '').length;
    if(tamanho == 11 ){
        
        var cpf = $('#cpf').val().replace(/\.|\-/g, '');
        status = verificaCpf(cpf);
        //status =  true;
        if(status == "false"){
            erroInput(msgInput.cpfErro);
        }
        var dados = { 'id' : $(this).attr("id"),
                      'status' : status};
        valido(dados); 

    }
    else if(tamanho == 0){
        erroInput (msgInput.cpf);
        status = false;
        //return false;
    }
    else {
        erroInput (msgInput.cpf);
        var dados = { 'id' : $(this).attr("id"),
                      'status' : false};
        valido(dados);
    }

});

$("#telefone").on("keyup", function(e){
    var tamanho = $('#telefone').val().replace(/()-/g, '');
    if(tamanho.length > 14){

     if($('#cpf').attr('data-check') == "true"){
            $(".esconderentrega1").removeClass("none");
        } 
    }
});

$("#telefone").mask("(00) 0 0000-0000");
$('#telefone').on('blur', function(e) {
    var tamanho = $('#telefone').val().replace(/()-/g, '');
    if(tamanho < 1){
        erroInput (msgInput.celular);
        status = false;
        //return false;
    }
    else if(tamanho.length > 0 && tamanho.length < 14){
        erroInput (msgInput.celular);
        status = false;
    }
    else {
        status = true;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);
});


$("#cep").mask('00000-000');

$("#cep").on("keyup", function(e){
    var tamanho = $("#cep").val().replace(/\.|\-/g, '').length;
    if(tamanho > 6){
            $(".esconderentrega2").removeClass("none");
    }
    if(tamanho == 8 ){

        var cep = $('#cep').val().replace(/\.|\-/g, '');
        //status = verificaCpf(cpf);
        status = buscaCep(cep);
        //alert("status:  " + status);
        var dados = { 'id' : $(this).attr("id"),
                      'status' : status };
        valido(dados);
    }
});

$('#cep').on('blur', function(e) {

    var tamanho = $("#cep").val().replace(/\.|\-/g, '').length;
    if(tamanho == 8 ){

        var cep = $('#cep').val().replace(/\.|\-/g, '');
       
        status = buscaCep(cep);
        //alert("status:  " + status);
        var dados = { 'id' : $(this).attr("id"),
                      'status' : status };
        valido(dados);
    }
    else{
        erroInput (msgInput.cep);
        freeCep();
        var dados = { 'id' : $(this).attr("id"),
                      'status' : false};
        valido(dados); 
        
    }
return false;

});


$('#endereco').on('blur', function(e) {
    var endereco = limpa($("#endereco").val());

    var tamanho = endereco.length;
    if(tamanho < 1){
        erroInput (msgInput.endereco);
        status = false;
        //return false;
    }
    else if(tamanho > 0 && tamanho < 5){
        erroInput (msgInput.endereco);
        status = false;
    }
    else {
        status = true;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});

$('#numero-casa').mask("000000000");

$("#numero-casa").on("keyup", function(e){
    var numero = limpa($("#numero-casa").val());

    var tamanho = numero.length;
    if(tamanho >= 1){
            $(".esconderinfopagamento1").removeClass("none");
    }
});

$('#numero-casa').on('blur', function(e) {
    var numero = limpa($("#numero-casa").val());

    var tamanho = numero.length;
    
    if(tamanho < 1){
        erroInput (msgInput.numero);
        status = false;
    }
    else {
        status = true;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});


$('#bairro').on('blur', function(e) {
    var bairro = limpa($("#bairro").val());

    var tamanho = bairro.length;
    if(tamanho < 1){
        erroInput (msgInput.bairro);
        status = false;
        //return false;
    }
    else if(tamanho > 0 && tamanho < 4){
        erroInput (msgInput.bairro);
        status = false;
    }
    else {
        status = true;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});


$('#cidade').on('blur', function(e) {
    var cidade = limpa($("#cidade").val());

    var tamanho = cidade.length;
    if(tamanho < 1){
        erroInput (msgInput.cidade);
        status = false;
        //return false;
    }
    else if(tamanho > 0 && tamanho < 4){
        erroInput (msgInput.cidade);
        status = false;
    }
    else {
        status = true;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});


$('#estado').on('change', function(e) {
    var estado = $("#estado").val();

    if(estado == "sel"){
        erroInput (msgInput.estado);
        status = false;
        //return false;
    }
   else {
        status = true;
       
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});

$('#pais').on('blur', function(e) {
    var pais = limpa($("#pais").val());

    var tamanho = pais.length;
    if(tamanho < 1){
        return false;
    }
    else if(tamanho > 0 && tamanho < 3){
        status = true;
    }
    else {
        status = false;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});



$("#pagar-cartao").on("click", function(){
    $(".esconderinfopagamento2").removeClass("none");
});
$("#pagar-boleto").on("click", function(){
    $(".esconderinfopagamento2").removeClass("none");
});

$("#cartao").mask('0000 0000 0000 0000', {reverse: false});

$("#cartao").on("keyup", function(e){
    var cartao = limpa($("#cartao").val());

    var tamanho = cartao.length;
    if(tamanho > 18 && tamanho < 20){

        if($('#nome-cartao').attr('data-check') == "true"){
         
            $(".esconderinfopagamento3").removeClass("none");
        } 
    }
});

$('#cartao').on('blur', function(e) { 
    var cartao = limpa($("#cartao").val());

    var tamanho = cartao.length;
    if(tamanho < 1){
        erroInput(msgInput.cartao);
        status = false;
        //return false;
    }
    else if(tamanho > 18 && tamanho < 20){
        status = true;
    }
    else {
        erroInput(msgInput.cartao);
        status = false;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});
$('#nome-cartao').keyup(function () { 
    this.value = this.value.replace(/[^A-Za-z ]/g,'');
  });

  $("#nome-cartao").on("keyup", function(e){
    var nome = limpa($("#nome-cartao").val());
    var tamanho = nome.length;
   
    if(tamanho >= 6){
 
     if($('#cartao').attr('data-check') == "true"){
        
            $(".esconderinfopagamento3").removeClass("none");
        } 
    }
});

  $('#nome-cartao').on('blur', function(e) { 

    var nome = limpa($("#nome-cartao").val());


    //var nome = $("#nome").val().replace(/\s{2,}/g, ' ');
    //$("#nome").val(nome);
    var tamanho = nome.length;
    
   //alert(tamanho);
    if(tamanho < 1){
        erroInput (msgInput.nomeCartao);
        status = false;
        //return false;
    }
    else if(tamanho < 6 ){
        erroInput (msgInput.nomeCartao);
        $("#nome-cartao").val(nome);
        status = false;
        
    }
     else if(nome.indexOf(" ") == -1){
        erroInput (msgInput.nomeCartao);
         $("#nome-cartao").val(nome);
         status = false;
     }
     else if(tamanho >= 6){
        $("#nome-cartao").val(nome);
        status = true;
    }
    else {
        erroInput (msgInput.nomeCartao);
        $("#nome-cartao").val(nome);
        status = false;
    }
    var dados = { 'id' : $(this).attr("id"),
        'status' : status};
    valido(dados);

 });


$("#cpf-cartao").mask("999.999.999-99");

$("#cpf-cartao").on("keyup", function(e){
    var tamanho = $("#cpf-cartao").val().replace(/\.|\-/g, '').length;
        if(tamanho == 11 ){
            var cpf = $('#cpf-cartao').val().replace(/\.|\-/g, '');
            status = verificaCpf(cpf);
            if(status == "false"){

                erroInput(msgInput.cpfErro);
            }
            var dados = { 'id' : $(this).attr("id"),
                          'status' : status};
            valido(dados);
        }
});

$('#cpf-cartao').on('blur', function(e) {
    

        var tamanho = $("#cpf-cartao").val().replace(/\.|\-/g, '').length;
        if(tamanho == 11 ){
            
            var cpf = $('#cpf-cartao').val().replace(/\.|\-/g, '');
            status = verificaCpf(cpf);
            
            if(status == "false"){

                erroInput(msgInput.cpfErro);
            }
        
        }
        else if(tamanho == 0){
            erroInput (msgInput.cpf);
            status = false;
            //return false;
        }
        else if(tamanho < 11 ){
            erroInput (msgInput.cfp);
            status = false;
            
        }
        var dados = { 'id' : $(this).attr("id"),
                          'status' : status};
            valido(dados); 
    
});
 
 $("#validade").on('change', function(e) {
    var mes = $("#validade").val();

    if(mes == "sel"){
        erroInput (msgInput.mes);
        status = false;
        //return false;
    }
   else {
        status = true;
       
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);
 });

 $("#ano-validade").on('change', function(e) {
    var ano = $("#ano-validades").val();

    if(ano == "sel"){
        erroInput (msgInput.ano);
        status = false;
        //return false;
    }
   else {
        status = true;
       
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);
 });


$("#codidgo-seguranca").mask('0000', {reverse: false});
$("#codidgo-seguranca").on('blur', function(e) {
    var codSeguranca = limpa($("#codidgo-seguranca").val());

    var tamanho = codSeguranca.length;
    if(tamanho < 1){
        return false;
    }
    else if(tamanho > 2 && tamanho < 5){
        status = true;
    }
    else {
        erroInput (msgInput.codidgoSeguranca);
        status = false;
    }
    var dados = {   'id' : $(this).attr("id"),
                    'status' : status };
    valido(dados);

});

$("body").on("click", "#pagar-cartao", function(e) {
    $("#cartao").attr("required", true);
    $("#nome-cartao").attr("required", true);
    $("#validade").attr("required", true);
    $("#ano-validade").attr("required", true);
    $("#codidgo-seguranca").attr("required", true);
    $("#parcelas").attr("required", true);

});




$("body").on("click", "#pagar-boleto", function(e) {
    
    desabilita();
});
$("body").on("click", "#pagar-loterica", function(e) {
    
    desabilita();
});

function desabilita(){
    $("#cartao").attr("required", false);
    $("#nome-cartao").attr("required", false);
    $("#validade").attr("required", false);
    $("#ano-validade").attr("required", false);
    $("#codidgo-seguranca").attr("required", false);
    $("#parcelas").attr("required", false);
}


/*$('#formulario_cupom').on('submit', function(e){
    e.preventDefault();

    var data = $('#formulario_cupom').serialize();

    $.post('/checkout/ativarcupom', data)
        .then(function (response) {

        if(response.success){
            alert(response.preco);
        }
        if(response.error){
            alert('Erro');
        }
    });
});*/


$('#formulario_cupom').on('submit', function(e){
    e.preventDefault();
    $("#load").css("display","flex");
    var data = $('#formulario_cupom').serialize();
    var cupom = $("#CupomdeDesconto").val();
    //alert($("#CupomdeDesconto").val());

    $.post('/checkout/ativarcupom', data)
        .then(function (response) {

        if(response.sucesso){
            $("#formulario_cupom").remove();

            $("#secess-cupom div").empty();
            $("#secess-cupom div").append(response.msg );
            $("#desconto").empty();
            $("#desconto").append(response.desconto);
            $("#valor-total").empty();
            $("#valor-total").append(response.precoTotal);
            $("#valor-total1").empty();
            $("#valor-total1").append(response.precoTotal);
            $("#cod-cupom").val(cupom);
            $("#secess-cupom").show();
            $("#load").hide();
            $('#valor-total').attr('valor-total', response.precoTotal);
            $('#cartao').keyup();

            $('#cupom').val(response.valor_cupom);

        }
        if(response.error){
            $("#load").hide(); 
            alert(response.msg);
        }

        submetendoCumpom();
    });

    
});

function load(status){
    if(status){
       $("#load").css("display","flex");
    }else{
       $("#load").hide();
    }
}

$('#fechar_modal').on('click', function(){
    $("#modal_erros").css("display","none");
});

function valido(dados){
    status = dados.status;
    if(status == 'true'){
        $("#"+dados.id).css({"border-color" : "#26bf26"});
        $("#"+dados.id).attr("data-check", "true");
    }
    if(status == 'false'){
        $("#"+dados.id).css({"border-color" : "red"});
        $("#"+dados.id).attr("data-check", "false");
    }
}

function limpa(str) {
    var string = str.split('  ').join(' ');
    if (string.indexOf('  ') != -1) return limpa(string);
    else return string;
}


function calcularFrete(cep){
    var data = {
        cep: cep,
        cupom: '',
        cod: $("#cod-plano").val()
    };

    data.cupom = $("#cod-cupom").val();

    $.post('/checkout/buscarcep', data)
        .then(function (response) {
        if(response.sucesso){

            $('#valor-total').attr('valor-total', response.total);

            $("[name=cep1]").val(response.frete);
            $("#valor-entrega").empty();
            $("#valor-entrega").append("R$ "+response.frete);
            $("#valor-total").empty();
            $("#valor-total").append("R$ " +response.total);
            $("#valor-total1").empty();
            $("#valor-total1").append("R$ " +response.total);
            $("#msg-frete").empty();
            $("#msg-frete").append(response.msg);
            $("[data-pag=pagamento]").prop("disabled", false);
            $("#bt_gerar_boleto").attr("data-check", "true");
            $("#frete").show();
        }
        if(response.error){
            $('#valor-total').attr('valor-total', response.total);

            $("[name=cep1]").val(response.frete);
            $("#valor-entrega").empty();
            $("#valor-entrega").append("R$ "+response.frete);
            $("#valor-total").empty();
            $("#valor-total").append("R$ " +response.total);
            $("#valor-total1").empty();
            $("#valor-total1").append("R$ " +response.total);
            $("#msg-frete").empty();
            $("#msg-frete").append(response.msg);
            $("[data-pag=pagamento]").prop("disabled", false);
            $("#bt_gerar_boleto").attr("data-check", "false");
            $("#frete").show();
        }
    });
}





function verificaCpf(cpf){

    if(cpf.length == 11){
        
        //load(true);
        var dadosCpf = { 'cpf' : cpf };
        $.ajax({
            type: 'POST', //Definimos o método HTTP usado
            async: false,
            dataType: 'json', //Definimos o tipo de retorno
            data: dadosCpf,
            url: "/checkout/verificacpf", //Definindo o arquivo onde serão buscados os dados
            success: function(response) {
                if(response.sucesso){
                    retorno = true;   
                } 
                if(response.error){
                    retorno = false;
                }
            }
        });
        //load(false);
        return retorno;


        // $.post('/checkout/verificacpf', data, {async : false} )
        // .then(function (response) {
        //     if(response.sucesso){
        //         alert('promeiro');
        //         retorno = true;   
        //     } 
        //     if(response.error){
        //         alert('segundo');
        //         retorno =  false;
        //     }
        //   });
          
    }
    
}


function buscaCep(cep){
        
        load(true);

        $.ajax({
            url : "https://viacep.com.br/ws/"+ cep +"/json/",
            type : "GET",
            cache: false,
            async: false,
            success : function(response) {
                if (!("erro" in response)) {
                    $("#cep").val(unescape(response.cep));
                    var dados = {   'id' : $("#cep").attr("id"),
                                    'status' : true };
                    valido(dados);

                    if(response.localidade){
                        $("#cidade").val(unescape(response.localidade));
                        var dados = {   'id' : $("#cidade").attr("id"),
                                        'status' : true };
                        valido(dados);
                    }

                    if(response.bairro){
                        $("#bairro").val(unescape(response.bairro));
                            var dados = {   'id' : $("#bairro").attr("id"),
                                            'status' : true };
                            valido(dados);
                    }

                    if(response.uf){
                        $("#estado").val(unescape(response.uf));
                            var dados = {   'id' : $("#estado").attr("id"),
                                            'status' : true };
                            valido(dados);
                    }
                    $("#pais").val("BR");
                        var dados = {   'id' : $("#pais").attr("id"),
                                        'status' : true };
                        valido(dados);

                    if(response.logradouro){
                        $("#endereco").val(unescape(response.logradouro));
                            var dados = {   'id' : $("#endereco").attr("id"),
                                            'status' : true };
                            valido(dados);
                    }

                    $('#numero-casa').focus();
                    calcularFrete(cep);
                    retorno = true;

                } 
                
                if(response.erro){
                    erroCep();
                    //$("#cep").val(unescape(response.cep));
                    var dados = {   'id' : $("#cep").attr("id"),
                                    'status' : true };
                    valido(dados);

                    retorno = false;
                    calcularFrete(cep);
                }
                load(false);

                return retorno;

            }
        });

}

function freeCep(){

                   //$('#cep').css({"border-color" : "red"});
                   $("#cidade").val("");
                    var dados = {   'id' : $("#cidade").attr("id"),
                                    'status' : false };
                    valido(dados);
                   $("#bairro").val("");
                    var dados = {   'id' : $("#bairro").attr("id"),
                                    'status' : false };
                    valido(dados);
                   $("#estado").val("");
                    var dados = {   'id' : $("#estado").attr("id"),
                                    'status' : false };
                    valido(dados);
                   $("#pais").val("");
                    var dados = {   'id' : $("#pais").attr("id"),
                                    'status' : false };
                    valido(dados);
                   $("#numero-casa").val("");
                    var dados = {   'id' : $("#numero-casa").attr("id"),
                                    'status' : false };
                    valido(dados);
                   $("#endereco").val("");
                    var dados = {   'id' : $("#endereco").attr("id"),
                                    'status' : false };
                    valido(dados);
                   //$(".gerar-boleto").removeAttr('id');
                   $('#cep').focus();

}


function validaForm(methodo){
    //var data = '';
    //data = $("#nome").data("check");
    //data = $('#nome').attr('data-check');

    retorno = true;
    if($('#nome').attr('data-check') != "true"){
        
        var dados = {   'id' : $("#nome").attr("id"),
                        'status' : false };
        valido(dados);
        erroInput(msgInput.nome );
        retorno =  false;
    }
    if($('#email').attr('data-check') != "true"){
        var dados = {   'id' : $("#email").attr("id"),
                        'status' : false };
        valido(dados);
        erroInput(msgInput.email ); 
        retorno =  false;
    }
    if($('#cpf').attr('data-check') != "true"){
        var dados = {   'id' : $("#cpf").attr("id"),
                        'status' : false };
        valido(dados); 
        erroInput(msgInput.cpf );
        retorno =  false;
    }
    if($('#telefone').attr('data-check') != "true"){
        var dados = {   'id' : $("#telefone").attr("id"),
                        'status' : false };
        valido(dados);
        erroInput(msgInput.celular );
        retorno =  false;
    }
    if($('#cep').attr('data-check') != "true"){
        var dados = {   'id' : $("#cep").attr("id"),
                        'status' : false };
        valido(dados);
        erroInput(msgInput.cep );
        retorno =  false;
    }
    if($('#endereco').attr('data-check') != "true"){
        var dados = {   'id' : $("#endereco").attr("id"),
                        'status' : false};
        valido(dados); 
        erroInput(msgInput.endereco );
        retorno =  false;
    }
    if($('#numero-casa').attr('data-check') != "true"){
        var dados = {   'id' : $("#numero-casa").attr("id"),
                        'status' : false};
        valido(dados); 
        erroInput(msgInput.numero );
        retorno =  false;
    }
    if($('#bairro').attr('data-check') != "true"){
        var dados = {   'id' : $("#bairro").attr("id"),
                        'status' : false};
        valido(dados);
        erroInput(msgInput.bairro );
        retorno =  false;
    }
    if($('#cidade').attr('data-check') != "true"){
        var dados = {   'id' : $("#cidade").attr("id"),
                        'status' : false};
        valido(dados); 
        erroInput(msgInput.cidade );
        retorno =  false;
    }
    if($('#estado').attr('data-check') != "true"){
        var dados = {   'id' : $("#estado").attr("id"),
                        'status' : false};
        valido(dados); 
        erroInput(msgInput.estado );
        retorno =  false;
    }
    // if($('#pais').attr('data-check') != "true"){
    //     var dados = {   'id' : $("#pais").attr("id"),
    //                     'status' : false};
    //     valido(dados); 
    //     retorno =  false;
    // }
    if(methodo == "cartao"){
        if($('#cartao').attr('data-check') != "true"){
            
            var dados = {   'id' : $("#pais").attr("id"),
                            'status' : false};
            valido(dados); 
            erroInput(msgInput.cartao );
            retorno =  false;
        }
        if($('#nome-cartao').attr('data-check') != "true"){
            erroInput(msgInput.nomeCartao);
            var dados = {   'id' : $("#nome-cartao").attr("id"),
                            'status' : false};
            valido(dados); 
            retorno =  false;
        }
        if($('#cpf-cartao').attr('data-check') != "true"){
            erroInput(msgInput.cpfErro );
            var dados = {   'id' : $("#cpf-cartao").attr("id"),
                            'status' : false};
            valido(dados); 
            retorno =  false;
        }
        if($('#validade').attr('data-check') != "true"){
            erroInput(msgInput.mes);
            var dados = {   'id' : $("#validade").attr("id"),
                            'status' : false};
            valido(dados); 
            retorno =  false;
        }
        if($('#ano-validade').attr('data-check') != "true"){
            erroInput(msgInput.ano );
            var dados = {   'id' : $("#ano-validade").attr("id"),
                            'status' : false};
            valido(dados); 
            retorno =  false;
        }
        if($('#codidgo-seguranca').attr('data-check') != "true"){
            var dados = {   'id' : $("#codidgo-seguranca").attr("id"),
                            'status' : false};
            valido(dados); 
            erroInput(msgInput.codidgoSeguranca );
            retorno =  false;
        }
                
    }

    return retorno;
}

function erroInput(msg){

        $("#titulo_modal").html("Campo Errado");
        $('#modal_erros_texto').html(msg);
        $("#modal_erros").css("display","flex");
}