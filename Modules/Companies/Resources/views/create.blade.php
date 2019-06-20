@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Cadastrar nova empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.index')}}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <div class="row" style="margin-bottom: 30px">
                    <div class="col-3">
                        <label for="country">Company country</label>
                        <select id="country" class="form-control">
                            <option value="usa">United States</option>
                            <option value="brazil">Brasil</option>
                        </select>
                    </div>
                </div>
                <div id="store_form" style="width:100%">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/companies/js/create.js') }}"></script>
    @endpush


@endsection

