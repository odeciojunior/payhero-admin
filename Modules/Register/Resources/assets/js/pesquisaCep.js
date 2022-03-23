function clearZipCodeForm() {
    //Limpa valores do formulário de cep.
    document.getElementById('brasil_street').value = "";
    document.getElementById('brasil_neighborhood').value = "";
    document.getElementById('brasil_city').value = "";
    document.getElementById('brasil_state').value = "";
}

function myCallback(data) {
    if (!("erro" in data)) {
        //Atualiza os campos com os valores.
        document.getElementById('brasil_street').value = data.logradouro;
        document.getElementById('brasil_neighborhood').value = data.bairro;
        document.getElementById('brasil_city').value = data.localidade;
        document.getElementById('brasil_state').value = data.uf;
    } //end if.
    else {
            //CEP não Encontrado.
            clearZipCodeForm();
            // alert("CEP não encontrado.");
        }
}

$("#brasil_zip_code").on("blur", function () {

    //Nova variável "cep" somente com dígitos.
    var cep = $(this).val().replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
    if (cep != "") {

        //Expressão regular para validar o CEP.
        var validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if (validacep.test(cep)) {

            //Preenche os campos com "..." enquanto consulta webservice.
            document.getElementById('brasil_street').value = "...";
            document.getElementById('brasil_neighborhood').value = "...";
            document.getElementById('brasil_city').value = "...";
            document.getElementById('brasil_state').value = "...";

            //Cria um elemento javascript.
            var script = document.createElement('script');

            //Sincroniza com o callback.
            script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=myCallback';

            //Insere script no documento e carrega o conteúdo.
            document.body.appendChild(script);
        } //end if.
        else {
                //cep é inválido.
                clearZipCodeForm();
                alert("Formato de CEP inválido.");
            }
    } //end if.
    else {
            //cep sem valor, limpa formulário.
            clearZipCodeForm();
        }
});
