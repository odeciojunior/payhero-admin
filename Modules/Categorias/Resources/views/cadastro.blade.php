@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar nova categoria</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/usuarios">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/categorias/cadastrarcategoria">
            @csrf
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <label for="nome">Nome</label>
                                <input name="nome" type="text" class="form-control" id="nome" placeholder="Nome">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-12">
                                <label for="descricao">Descrição</label>
                                <input name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
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

