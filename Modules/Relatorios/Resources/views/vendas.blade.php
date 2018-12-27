@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">

      <h3> Vendas </h3>

      <div class="panel pt-30 p-30 " data-plugin="matchHeight">

        {!! $dataTable->table() !!}

        <!-- Modal detalhes da venda-->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_venda_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
              </div>
              <div id="modal_venda_body" class="modal-body">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

        <!-- Modal estornar venda-->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_estornar" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_estornar_titulo" class="modal-title" style="width: 100%; text-align:center">Estornar venda?</h4>
              </div>
              <div id="modal_estornar_body" class="modal-body">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success bt_estornar_venda">Confirmar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

      </div>
    </div>
  </div>
@endsection

