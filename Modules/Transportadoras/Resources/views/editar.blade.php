@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar transportadora</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{ route('transportadoras') }}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/transportadoras/editartransportadora">
            @csrf
            <input type="hidden" value="{!! $transportadora->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="name">Nome</label>
                                <input value="{!! $transportadora->name != '' ? $transportadora->name : '' !!}"  name="name" type="text" class="form-control" id="name" placeholder="Nome">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="site">Site</label>
                                <input value="{!! $transportadora->site != '' ? $transportadora->site : '' !!}"  name="site" type="text" class="form-control" id="site" placeholder="Site">
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

