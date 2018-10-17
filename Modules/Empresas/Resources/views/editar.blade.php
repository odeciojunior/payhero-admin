@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/empresas">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/empresas/editarempresa">
            @csrf
            <input type="hidden" value="{!! $empresa->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="cnpj">CNPJ</label>
                                <input value="{!! $empresa->cnpj != '' ? $empresa->cnpj : '' !!}" name="cnpj" type="text" class="form-control" id="cnpj" placeholder="CNPJ">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="situacao">Situacao</label>
                                <select name="situacao" class="form-control" id="situacao" required>
                                    <option value="ativo" value="{!! ($empresa->situacao != '' && $empresa->situacao == 'ativo') ? 'selected' : '' !!}" >Ativo</option>
                                    <option value="inativo" value="{!! ($empresa->situacao != '' && $empresa->situacao == 'inativo') ? 'selected' : '' !!}">Inativo</option>
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome fantasia</label>
                                <input value="{!! $empresa->nome != '' ? $empresa->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome fantasia">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="email">Email</label>
                                <input value="{!! $empresa->email != '' ? $empresa->email : '' !!}" name="email" type="text" class="form-control" id="email" placeholder="Email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="cep">CEP</label>
                                <input value="{!! $empresa->cep != '' ? $empresa->cep : '' !!}" name="cep" type="text" class="form-control" id="cep" placeholder="CEP">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="estado">Estado</label>
                                <input value="{!! $empresa->uf != '' ? $empresa->uf : '' !!}" name="uf" type="text" class="form-control" id="estado" placeholder="estado">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="municipio">Município</label>
                                <input value="{!! $empresa->municipio != '' ? $empresa->municipio : '' !!}" name="municipio" type="text" class="form-control" id="municipio" placeholder="Município">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="bairro">Bairro</label>
                                <input value="{!! $empresa->bairro != '' ? $empresa->bairro : '' !!}" name="bairro" type="text" class="form-control" id="bairro" placeholder="Bairro">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="logradouro">Rua</label>
                                <input value="{!! $empresa->logradouro != '' ? $empresa->logradouro : '' !!}" name="logradouro" type="text" class="form-control" id="logradouro" placeholder="Rua">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="numero">Número</label>
                                <input value="{!! $empresa->numero != '' ? $empresa->numero : '' !!}" name="numero" type="text" class="form-control" id="numero" placeholder="Número">
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="complemento">Complemento</label>
                                <input value="{!! $empresa->complemento != '' ? $empresa->complemento : '' !!}" name="complemento" type="text" class="form-control" id="complemento" placeholder="Complemento">
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

    });

  </script>


@endsection

