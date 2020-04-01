@extends('userterms::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('userterms.name') !!}
    </p>
@endsection
