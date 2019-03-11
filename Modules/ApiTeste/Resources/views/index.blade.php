@extends('apiteste::layouts.master')

@section('content')
    <h1>Login</h1>

    <label>Login</label>
    <input type="text" id="email" placeholder="login">
    <br>
    <label>Senha</label>
    <input type="text" id="password" placeholder="senha">
    <br>
    <button id="logar">Sign up</button>

    <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <script>

        $(document).ready(function(){

            $("#logar").on("click", function(){

                jQuery.support.cors = true;

                $.ajax({
                    method: "POST",
                    dataType: 'text json',
                    url: "http://cloudfoxapi.tk/api/login",
                    cache: false,
                    {{--  headers: {
                        'Accept': 'application/json'
                    },  --}}
                    data: { 
                        email: $('#email').val(),
                        password: $('#password').val() 
                    },
                    error: function(){
                        alert('erro');
                    },
                    success: function(){
                        alert('success');
                    }
                });
            });

        });

    </script>
@stop
