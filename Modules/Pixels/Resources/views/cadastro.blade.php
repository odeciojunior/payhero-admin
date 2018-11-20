{{--  @extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo pixel</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/pixels">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>  --}}
        <div style="text-align: center">
            <h4>Adicionar pixel</h4>
        </div>
        <form id='cadastrar_pixel' method="post" action="/pixels/cadastrarpixel">
            @csrf
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <label for="nome">Nome</label>
                                <input name="nome" type="text" class="form-control" id="nome" placeholder="Nome">
                            </div>

                            <div class="form-group col-xl-12">
                                <label for="descricao">Código</label>
                                <input name="cod_pixel" type="text" class="form-control" id="cod_pixel" placeholder="Código">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-12">
                                <label for="plataforma">Plataforma</label>
                                <select name="plataforma" type="text" class="form-control" id="plataforma">
                                    <option value="" selected>Selecione</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="google">Google</option>
                                    <option value="taboola">Taboola</option>
                                    <option value="outbrain">Outbrain</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-12">
                                <label for="status">Status</label>
                                <select name="status" type="text" class="form-control" id="status">
                                    <option value="" selected>Selecione</option>
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
