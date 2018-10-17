@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar nova empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/usuarios">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/empresas/cadastrarempresa">
            @csrf
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">
                        <div class="row">
                            <input type="hidden" name="">
                            <input type="hidden" name="">
                            <input type="hidden" name="">
                            <input type="hidden" name="">
                            <div class="form-group col-xl-6">
                                <label for="cnpj">CNPJ</label>
                                <input name="cnpj" type="text" class="form-control" id="cnpj" placeholder="CNPJ">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="situacao">Situacao</label>
                                <select name="situacao" class="form-control" id="situacao" required>
                                    <option value="ativo" selected>Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome fantasia</label>
                                <input name="nome" type="text" class="form-control" id="nome" placeholder="Nome fantasia">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="email">Email</label>
                                <input name="email" type="text" class="form-control" id="email" placeholder="Email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="cep">CEP</label>
                                <input name="cep" type="text" class="form-control" id="cep" placeholder="CEP">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="estado">Estado</label>
                                <input name="uf" type="text" class="form-control" id="estado" placeholder="estado">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="municipio">Município</label>
                                <input name="municipio" type="text" class="form-control" id="municipio" placeholder="Município">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="bairro">Bairro</label>
                                <input name="bairro" type="text" class="form-control" id="bairro" placeholder="Bairro">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="logradouro">Rua</label>
                                <input name="logradouro" type="text" class="form-control" id="logradouro" placeholder="Rua">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="numero">Número</label>
                                <input name="numero" type="text" class="form-control" id="numero" placeholder="Número">
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="complemento">Complemento</label>
                                <input name="complemento" type="text" class="form-control" id="complemento" placeholder="Complemento">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

  <script>

    $(document).ready( function(){

        $('#cnpj').on('input',function(){

            if($(this).val().replace(/[^0-9]/g,'').length ==  14){

                jQuery.support.cors = true;

                $.ajax({
                    type: 'GET',
                    url: 'https://www.receitaws.com.br/v1/cnpj/'+$(this).val(),
                    crossDomain: true,
                    dataType: 'jsonp',
                    success: function(data) {
                        alert(data.toSource());
                        
                    },
                });
        
            }

        });

    });

  </script>


@endsection

