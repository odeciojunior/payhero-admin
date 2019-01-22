{{--  @extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo cupom de desconto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/cuponsdesconto">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>  --}}

        <div style="text-align: center">
            <h4> Cadastrar cupom </h4>
        </div>

        <form id="cadastrar_cupom" method="post" action="/cuponsdesconto/cadastrarcupomdesconto">
            @csrf
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input name="nome" type="text" class="form-control" id="nome_cupom" placeholder="Nome">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="descricao">Descrição</label>
                                <input name="descricao" type="text" class="form-control" id="descricao_cupom" placeholder="Descrição">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="tipo">Tipo</label>
                                <select name="tipo" class="form-control" id="tipo_cupom" required>
                                    <option value="">Selecione</option>
                                    <option value="0">Porcentagem</option>
                                    <option value="1">Valor</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="valor">Valor</label>
                                <input name="valor" type="text" class="form-control" id="valor_cupom_cadastrar" placeholder="Valor" data-mask="0#">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="cod_cupom">Código</label>
                                <input name="cod_cupom" type="text" class="form-control" id="cod_cupom" placeholder="Código">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="status">Status</label>
                                <select name="status" class="form-control" id="status_cupom" required>
                                    <option value="">Selecione</option>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
    
                        {{--  <div class="row">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Salvar</button>
                            </div>
                        </div>  --}}

                    </div>
                </div>
            </div>
        </form>
    {{--  </div>

  <script>

    $(document).ready( function(){

    });

  </script>


@endsection
  --}}
