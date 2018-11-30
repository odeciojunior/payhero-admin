@extends("layouts.master")

@section('content')

  {{--  <link rel='stylesheet' href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js">  --}}
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">

      <h3> Vendas </h3>

      <div class="panel pt-30 p-30 " data-plugin="matchHeight">

        {!! $dataTable->table() !!}

        <!-- Modal -->
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

      </div>
    </div>
  </div>
@endsection

