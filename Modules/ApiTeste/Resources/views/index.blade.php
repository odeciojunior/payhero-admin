@extends('apiteste::layouts.master')

@section('content')
    <h1>Login</h1>

    <label>Login</label>
    <input type="text" id="login" placeholder="login">
    <br>
    <label>Senha</label>
    <input type="text" id="senha" placeholder="senha">
    <br>
    <button id="logar">Sign up</button>

    <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <script>

        $(document).ready(function(){

        });

    </script>
@stop
