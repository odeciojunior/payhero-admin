@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar projeto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/projetos">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/projetos/editarprojeto">
            @csrf
            <input type="hidden" value="{!! $projeto->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input value="{!! $projeto->nome != '' ? $projeto->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="descricao">Descrição</label>
                                <input value="{!! $projeto->descricao != '' ? $projeto->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
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
