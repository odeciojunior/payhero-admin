@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar categoria</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/categorias">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/categorias/editarcategoria">
            @csrf
            <input type="hidden" value="{!! $categoria->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <label for="nome">Nome</label>
                                <input value="{!! $categoria->nome != '' ? $categoria->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-12">
                                <label for="descricao">Descrição</label>
                                <input value="{!! $categoria->descricao != '' ? $categoria->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
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

