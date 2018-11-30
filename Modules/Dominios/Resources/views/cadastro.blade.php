{{--  @extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo domínio</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/dominios">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>  --}}
        <div style="text-align: center">
            <h4>Cadastro de domínio no projeto</h4>
        </div>
        <form id="cadastrar_dominio" method="post" action="/dominios/cadastrardominio">
            @csrf
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="dominio">Domínio</label>
                                <input name="dominio" type="text" class="form-control" id="dominio" placeholder="Domínio">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="layout">Layout</label>
                                <select name="layout" type="text" class="form-control" id="layout" required>
                                    <option value="" selected>Selecione</option>
                                    @foreach($layouts as $layout)
                                        <option value="{{ $layout['id'] }}">{{ $layout['descricao'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="empresa">Empresa</label>
                                <select name="empresa" type="text" class="form-control" id="empresa" required>
                                    <option value="" selected>Selecione</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa['id'] }}">{{ $empresa['nome_fantasia'] }}</option>
                                    @endforeach
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
