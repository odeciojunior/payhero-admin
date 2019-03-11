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

                $.ajax({
                    method: "POST",
                    url: "http://cloudfoxapi.tk/api/login",
                    headers: {
                        'Accept': 'application/json'
                    },
                    data: { 
                        email: $('#email').val(),
                        password: $('#password').val() 
                    },
                    error: function(a,b,c){
                        alert('Ocorreu algum erro');
                    },
                    success: function(data){
                        alert(data.toSource());
                    }
                });
            });

        });

    </script>
@stop
