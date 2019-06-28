@extends("layouts.master")

@section('content')

  @push('css')
      <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
  @endpush

  <!-- Page -->
  <div class="page">

    <div class="page-header container">
        <h1 class="page-title">Carrinhos abandonados</h1>
    </div>

    <div class="page-content container">

      <div class="card shadow" style="min-height: 300px">
          <table class="table table-striped">
            <thead>
                <tr>
                    <td class="table-title" >Data</td>
                    <td class="table-title">Cliente</td>
                    <td class="table-title">Email</td>
                    <td class="table-title">Sms</td>
                    <td class="table-title">Status</td>
                    <td class="table-title">Valor</td>
                    <td class="table-title">Link</td>
                    <td class="table-title"></td>
                    <td class="table-title">Detalhes</td>
                </tr>
            </thead>
            <tbody id="table_data">
            </tbody>
          </table>
      </div>
      <div class="row">
        <div class="col-12">
          <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
              {{-- js carrega... --}}
          </ul>
        </div>
      </div>
  </div>

  @push('scripts')
      <script src="{!! asset('modules/cartrecovery/js/cartrecovery.js') !!}"></script>
  @endpush

@endsection

