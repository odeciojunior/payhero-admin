@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar usuário</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/usuarios">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/usuarios/editarusuario">
            @csrf
            <input type="hidden" value="{!! $user->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel pt-30 p-30" data-plugin="matchHeight">
                    <div style="width:100%">
                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input name="name" value="{!! $user->name != '' ? $user->name : '' !!}" type="text" class="form-control" id="nome" placeholder="Nome">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="funcao">Função</label>
                                <select name="role" class="form-control" id="funcao" required>
                                    @foreach($roles as $role)
                                        <option value="{!! $role['id'] !!}">{!! $role['name'] !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Senha" disabled>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="email">Email</label>
                                <input value="{!! $user->email != '' ? $user->email : '' !!}" name="email" type="text" class="form-control" id="email" placeholder="Email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="data_nascimento">Data de nascimento</label>
                                <input value="{!! $user->data_nascimento != '' ? $user->data_nascimento : '' !!}" name="data_nascimento" type="date" class="form-control" id="data_nascimento">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="cpf">CPF</label>
                                <input value="{!! $user->cpf != '' ? $user->cpf : '' !!}" name="cpf" type="text" class="form-control" id="cpf" placeholder="CPF">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="celular">Celular</label>
                                <input value="{!! $user->celular != '' ? $user->celular : '' !!}" name="celular" type="text" class="form-control" id="celular" placeholder="Celular">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="telefone1">Telefone 1</label>
                                <input value="{!! $user->telefone1 != '' ? $user->telefone1 : '' !!}" name="telefone1" type="text" class="form-control" id="telefone1" placeholder="Telefone 1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="telefone2">Telefone 2</label>
                                <input value="{!! $user->telefone2 != '' ? $user->telefone2 : '' !!}" name="telefone2" type="text" class="form-control" id="telefone2" placeholder="Telefone 2">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="cep">CEP</label>
                                <input value="{!! $user->cep != '' ? $user->cep : '' !!}" name="cep" type="text" class="form-control" id="cep" placeholder="CEP">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="pais">País</label>
                                <input value="{!! $user->pais != '' ? $user->pais : '' !!}" name="pais" type="text" class="form-control" id="pais" placeholder="País">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="estado">Estado</label>
                                <input value="{!! $user->estado != '' ? $user->estado : '' !!}" name="estado" type="text" class="form-control" id="estado" placeholder="Estado">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="cidade">Cidade</label>
                                <input value="{!! $user->cidade != '' ? $user->cidade : '' !!}" name="cidade" type="text" class="form-control" id="cidade" placeholder="Cidade">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="bairro">Bairro</label>
                                <input value="{!! $user->bairro != '' ? $user->bairro : '' !!}" name="bairro" type="text" class="form-control" id="bairro" placeholder="Bairro">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="rua">Rua</label>
                                <input value="{!! $user->logradouro != '' ? $user->logradouro : '' !!}" name="logradouro" type="text" class="form-control" id="rua" placeholder="Rua">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="numero">Número</label>
                                <input value="{!! $user->numero != '' ? $user->numero : '' !!}" name="numero" type="text" class="form-control" id="numero" placeholder="Número">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="complemento">Complemento</label>
                                <input value="{!! $user->complemento != '' ? $user->complemento : '' !!}" name="complemento" type="text" class="form-control" id="complemento" placeholder="Complemento">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="referencia">Referência</label>
                                <input value="{!! $user->referencia != '' ? $user->referencia : '' !!}" name="referencia" type="text" class="form-control" id="referencia" placeholder="Referência">
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
