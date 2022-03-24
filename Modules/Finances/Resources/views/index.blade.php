@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ mix('build/layouts/finances/index.min.css') }}">
@endpush

@section('content')

    <div class="page">

        <div class="page-header container">
            <div class="row">
                <div class="col-lg-6 mb-30">
                    <h1 class="page-title">Finanças</h1>
                </div>

                @include('finances::components.export-buttons')
            </div>
        </div>

        <div class="page-content container">

            @include('finances::components.export-alert')

            @include('finances::components.tabs')

            <div>
                <div id="tabs-view">
                    <div class="tab-content" id="nav-tabContent">

                        <div class="tab-pane fade show active" id="nav-transfers" role="tabpanel" aria-labelledby="nav-home-tab">

                            @include('finances::components.new-withdrawal')

                            @include('finances::components.balances-resume')

                            @include('finances::components.withdrawals')

                        </div>

                        <div class="tab-pane fade hide" id="nav-statement" role="tabpanel"  aria-labelledby="nav-profile-tab">

                            @include('finances::components.statement-filters')

                            @include('finances::components.statement')

                        </div>
                    </div>
                </div>
            </div>

            {{-- @include('companies::empty') --}}

            {{-- @push('css')
                <link rel="stylesheet" href="{!! mix('build/layouts/companies/empty.min.css') !!}">
            @endpush --}}
            <div class="content-error text-center" id="empty-companies-error" style="display:none">
                <img src="{!! mix('build/global/img/empty.svg') !!}" width="250px">
                <h1 class="big gray">Você ainda não tem nenhuma empresa!</h1>
                <p class="desc gray">Vamos cadastrar a primeira empresa? </p>
                <a href="" class="btn btn-primary redirect-to-accounts" data-url-value="/companies">Cadastrar empresa</a>
            </div>

        </div>

        @include('finances::components.export-modal')

        @include('finances::components.new-withdrawal-modal')

        @include('finances::components.details')

        @include('sales::details')


        @push('scripts')
            <script src="{{ mix('build/layouts/finances/index.min.js') }}"></script>
        @endpush
    </div>

@endsection
