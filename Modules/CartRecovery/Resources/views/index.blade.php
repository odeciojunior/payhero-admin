@extends("layouts.master")

@section('content')

  @push('css')
      <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
  @endpush

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Carrinhos abandonados</h1>
    </div>

    <div class="page-content container-fluid">

      <div class="panel pt-10 p-10" style="min-height: 300px">
          <table class="table table-hover table-vendas">
            <thead class="text-center">
                <td><b>Data</b></td>
                <td><b>Cliente</b></td>
                <td><b>Email</b></td>
                <td><b>Sms</b></td>
                <td><b>Status</b></td>
                <td><b>Valor</b></td>
                <td></td>
                <td><b>Link</b></td>
                <td><b>Detalhes</b></td>
            </thead>
            <tbody id="table_data">
            </tbody>
          </table>
      </div>
      <div class="row">
        <div class="col-12">
          <ul id="pagination" class="pagination-sm m-30" style="margin-top:10px;position:relative;float:right">
              {{-- js carrega... --}}
          </ul>
        </div>
      </div>
  </div>

  @push('scripts')
      <script src="{!! asset('modules/cartrecovery/js/cartrecovery.js') !!}"></script>
  @endpush

@endsection

