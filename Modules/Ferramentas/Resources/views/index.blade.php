@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Ferramentas</h1>
    </div>

    <div class="page-content container-fluid">

        <a href="{!! route('ferramentas.sms') !!}">
            <div class="card-columns">
                <div class="card">
                    <div class="card-block">
                        <h4 class="card-title">SMS</h4>
                        <div class="row">
                            <div class="col-10">
                                <p class="card-text">
                                  Serviço de envio de sms.
                                </p>
                            </div>
                            <div class="col-2">
                                <i class="icon wb-envelope" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>


    </div>
    {{--  <div class="page-content container-fluid">
      <div class="panel pt-30 p-30" data-plugin="matchHeight">  --}}

        <!-- Modal com detalhes do usuário -->
        {{--  <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
              </div>
              <div id="modal_detalhes_body" class="modal-body">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>  --}}
        <!-- End Modal -->

        <!-- Modal de confirmação da exclusão do domínio -->
        {{--  <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple">
              <div class="modal-content">
                <form id="form_excluir_dominio" method="GET" action="/deletardominio">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                    <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center">Excluir ?</h4>
                  </div>
                  <div id="modal_excluir_body" class="modal-body">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>  --}}
          <!-- End Modal -->

        {{--  </div>
    </div>--}}
  </div>


  <script>

    $(document).ready( function(){


    });

  </script>


@endsection

