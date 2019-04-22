@extends('notificacoes::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('notificacoes.name') !!}
    </p>
@stop
