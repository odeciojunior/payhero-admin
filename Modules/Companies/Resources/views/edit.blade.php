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

        @if (\Session::has('error'))
            <div class="alert alert-danger">
                <ul>
                    <li>{!! \Session::get('error') !!}</li>
                </ul>
            </div>
        @endif

        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">        
                <form method="post" action="/empresas/editarempresa">
                    @csrf
                    <input type="hidden" value="{!! $company->id !!}" name="id">
                    <div style="width:100%">
                        <h4>Dados gerais</h4>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="cnpj">CNPJ</label>
                                <input value="{!! $company->cnpj != '' ? $company->cnpj : '' !!}" name="cnpj" type="text" class="form-control" id="cnpj" placeholder="CNPJ/CPF" data-mask="0#">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="situacao">Situacao</label>
                                <select name="situacao" class="form-control" id="situacao" required>
                                    <option value="ativo" {!! ($company->situacao == 'ativo') ? 'selected' : '' !!} >Ativo</option>
                                    <option value="inativo" {!! ($company->situacao == 'inativo') ? 'selected' : '' !!}>Inativo</option>
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-12">
                                <label for="nome">Nome fantasia</label>
                                <input value="{!! $company->nome_fantasia != '' ? $company->nome_fantasia : '' !!}" name="nome_fantasia" type="text" class="form-control" id="nome" placeholder="Nome fantasia">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="cep">CEP</label>
                                <input value="{!! $company->cep != '' ? $company->cep : '' !!}" name="cep" type="text" class="form-control" id="cep" placeholder="CEP" data-mask="0#">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="estado">Estado</label>
                                <input value="{!! $company->uf != '' ? $company->uf : '' !!}" name="uf" type="text" class="form-control" id="estado" placeholder="estado">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="municipio">Município</label>
                                <input value="{!! $company->municipio != '' ? $company->municipio : '' !!}" name="municipio" type="text" class="form-control" id="municipio" placeholder="Município">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="bairro">Bairro</label>
                                <input value="{!! $company->bairro != '' ? $company->bairro : '' !!}" name="bairro" type="text" class="form-control" id="bairro" placeholder="Bairro">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="logradouro">Rua</label>
                                <input value="{!! $company->logradouro != '' ? $company->logradouro : '' !!}" name="logradouro" type="text" class="form-control" id="logradouro" placeholder="Rua">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="numero">Número</label>
                                <input value="{!! $company->numero != '' ? $company->numero : '' !!}" name="numero" type="text" class="form-control" id="numero" placeholder="Número" data-mask="0#">
                            </div>
 
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="complemento">Complemento</label>
                                <input value="{!! $company->complemento != '' ? $company->complemento : '' !!}" name="complemento" type="text" class="form-control" id="complemento" placeholder="Complemento">
                            </div>

                        </div>
                        <h4 style="margin-bottom: 15px">Dados bancários</h4>

                        @if ($company->recipient_id == '')
                            <div class="alert alert-danger">
                                <ul>
                                    <li>Dados bancários inválidos!</li>
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <ul>
                                    <li>Dados bancários cadastrados com sucesso!</li>
                                </ul>
                            </div>
                        @endif
                                    
                        <div class="row">
                            <div class="form-group col-xl-12">
                                <label for="banco">Banco</label>
                                <select id="banco" name="banco" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach($banks as $bank)
                                        <option value="{!! $bank['codigo'] !!}" {!! $company->banco == $bank['codigo'] ? 'selected' : '' !!}>{!! $bank['codigo'] . ' - ' .$bank['nome'] !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-9">
                                <label for="agencia">Agência</label>
                                <input name="agencia" value="{!! $company->agencia !!}" type="text" class="form-control" id="agencia" placeholder="Agência" data-mask="0#">
                            </div>
                            <div class="form-group col-xl-3">
                                <label for="agencia_digito">Dígito</label>
                                <input name="agencia_digito" value="{!! $company->agencia_digito !!}" type="text" class="form-control" id="agencia_digito" placeholder="Dígito" data-mask="0#">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-9">
                                <label for="conta">Conta</label>
                                <input name="conta" value="{!! $company->conta !!}" type="text" class="form-control" id="conta" placeholder="Conta" data-mask="0#">
                            </div>
                            <div class="form-group col-xl-3">
                                <label for="conta_digito">Dígito</label>
                                <input name="conta_digito" value="{!! $company->conta_digito !!}" type="text" class="form-control" id="agencia_digito" placeholder="Dígito" data-mask="0#">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Salvar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <script>

    $(document).ready( function(){

    });

  </script>


@endsection

