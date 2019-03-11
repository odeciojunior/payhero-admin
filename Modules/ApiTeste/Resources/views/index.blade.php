@extends('apiteste::layouts.master')

@section('content')

    <div id="div_login">
        <h1>Login</h1>

        <label>Login</label>
        <input type="text" id="email" placeholder="login">
        <br>
        <label>Senha</label>
        <input type="text" id="password" placeholder="senha">
        <br>
        <button id="logar">Sign up</button>
    </div>

    <div id="layout" style="display:none">
        <h1> Barra superior </h1>
        <button id="dados_usuario">Dados do usuario</button><br>
        <button id="qtd_notificacoes">Quantidade de notificaçẽos</button><br>
        <button id="notificacoes">Notificacoes</button><br>
    </div>

    <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <script>

        $(document).ready(function(){

            jQuery.support.cors = true;

            var access_token = null;

            $("#logar").on("click", function(){

                $.ajax({
                    method: "POST",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/login",
                    cache: false,
                    headers: {
                        'Accept': 'application/json'
                    },
                    data: { 
                        email: $('#email').val(),
                        password: $('#password').val() 
                    },
                    error: function(){
                        alert('erro');
                    },
                    success: function(response){
                        if(!response.status){
                            access_token = response.data.access_token;
                            $("#div_login").hide();
                            $("#layout").show();
                        }
                        else{
                            alert(response.message);
                        }
                    }
                });
            });

            $("#dados_usuario").on("click", function(){

                $.ajax({
                    method: "GET",
                    url: "http://cloudfoxapi.tk/api/user",
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + access_token
                    },
                    error: function(){
                        alert('erro');
                    },
                    success: function(response){
                        alert(response.toSource());
                    }
                });

            });

        });

    </script>
@stop
