@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/global/jquery-daterangepicker/daterangepicker.min.css?v='.uniqid()) }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=03') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v='.uniqid()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/table.css?v='.uniqid()) }}">
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

            @include('companies::empty')

        </div>

        @include('finances::components.export-modal')

        @include('finances::components.new-withdrawal-modal')

        @include('finances::components.details')

        @include('sales::details')

        <link rel="stylesheet" href="{{asset('modules/finances/css/jPages.css?v=2125')}}">

        @push('scripts')
            <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
            <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
            <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
            <script src="{{ asset('modules/finances/js/statement-index.js?v='. uniqid()) }}"></script>
            <script src="{{ asset('modules/finances/js/balances.js?v='. uniqid()) }}"></script>
            <script src="{{ asset('modules/finances/js/withdrawals-table.js?v='. uniqid()) }}"></script>
            {{-- <script src="{{ asset('modules/finances/js/withdrawal-custom.js?v='. uniqid()) }}"></script> --}}
            {{-- <script src="{{ asset('modules/finances/js/withdrawal-default.js?v='. uniqid()) }}"></script> --}}
            <script src="{{ asset('modules/finances/js/withdrawal-handler.js?v='. uniqid()) }}"></script>
            <script src="{{ asset('modules/finances/js/statement.js?v='. uniqid()) }}"></script>
            <script src="{{ asset('modules/global/jquery-daterangepicker/daterangepicker.min.js?v=' . uniqid()) }}"></script>
        @endpush
    </div>

@endsection
