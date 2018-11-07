@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar domínio</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/dominios">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/dominios/editardominio">
            @csrf
            <input type="hidden" value="{!! $dominio->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <label for="dominio">Domínio</label>
                                <input value="{!! $dominio->dominio != '' ? $dominio->dominio : '' !!}" name="dominio" type="text" class="form-control" id="dominio" placeholder="Domínio">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="layout">Layout</label>
                                <select name="layout" type="text" class="form-control" id="layout" required>
                                    @foreach($layouts as $layout)
                                        <option value="{{ $layout['id'] }}" {!! ($dominio->layout == $layout['id']) ? 'selected' : '' !!}>{{ $layout['descricao'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="empresa">Empresa</label>
                                <select name="empresa" type="text" class="form-control" id="empresa" required>
                                    <option value="" selected>Selecione</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa['id'] }}" {!! ($dominio->empresa == $empresa['id']) ? 'selected' : '' !!}>{{ $empresa['nome'] }}</option>
                                    @endforeach
                                </select>
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

