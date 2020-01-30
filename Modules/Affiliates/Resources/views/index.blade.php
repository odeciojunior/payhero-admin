@extends('affiliates::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('affiliates.name') !!}
    </p>

    @push('scripts')
        <script src="{{asset('modules/affiliates/js/index.js') }}"></script>
    @endpush
@endsection
