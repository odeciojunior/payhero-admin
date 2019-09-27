@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Cadastrar nova empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.index')}}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div class="page-content container">
            <form id='create_form' method="post" action="{{route('companies.store')}}">
                @csrf
                @method('POST')
                <div class="card shadow p-30" data-plugin="matchHeight">
                    <div class="form-group col-3">
                        <label for="country">País da empresa</label>
                        <select id="country" name='country' class="form-control select-pad">
                            <option value="brasil">Brasil</option>
                            <option value="usa" disabled>United States</option>
                        </select>
                    </div>
                    <div id="store_form" style="width:100%">
                    </div>
                    <div class="form-group col-xl-4">
                        <button class="form-control btn btn-success" type='submit'>
                            Proximo <i class='icon wb-chevron-right-mini' aria-hidden='true'></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/companies/js/create.js') }}"></script>
    @endpush

@endsection

