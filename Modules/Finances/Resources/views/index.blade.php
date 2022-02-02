@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{asset('modules/finances/css/jPages.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('/modules/global/css/table.css?v='. versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v=' . versionsFile()) }}">
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


        @push('scripts')
            <script src="{{ asset('modules/global/js-extra/moment.min.js?v=' . versionsFile()) }}"></script>
            <script src="{{ asset('modules/global/js/daterangepicker.min.js?v=' . versionsFile()) }}"></script>
            <script src="{{ asset('modules/finances/js/jPages.min.js?v=' . versionsFile()) }}"></script>
            <script src="{{ asset('modules/finances/js/statement-index.js?v=' . versionsFile()) }}"></script>
            <script src="{{ asset('modules/finances/js/balances.js?v=' . versionsFile()) }}"></script>
            <script src="{{ asset('modules/finances/js/withdrawals-table.js?v=' . versionsFile()) }}"></script>
            {{-- <script src="{{ asset('modules/finances/js/withdrawal-custom.js?v=' . versionsFile()) }}"></script> --}}
            {{-- <script src="{{ asset('modules/finances/js/withdrawal-default.js?v=' . versionsFile()) }}"></script> --}}
            <script src="{{ asset('modules/finances/js/withdrawal-handler.js?v=' . versionsFile()) }}"></script>
            <script src="{{ asset('modules/finances/js/statement.js?v=1' . versionsFile()) }}"></script>
        @endpush
    </div>

@endsection
