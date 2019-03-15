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
        <h1>Dados necessários para o menu estático (top-bar e side-bar)</h1>
        <h3> Barra superior </h3>
        <button id="dados_usuario">Dados do usuario</button><br>
        <button id="qtd_notificacoes">Quantidade de notificaçẽos</button><br>
        <button id="notificacoes">Notificacoes</button><br>
        <h3>Menu lateral</h3>
        <button id="get_menulateral">Menu lateral</button><br>

        <hr>

        <h1>Dashboard</h1>
        <button id="get_saldos">Obter saldos</button><br>

        <h1>Vitrine</h1>
        <button id="get_vitrine">Obter projetos vitrine</button><br>

        <h1>Vendas</h1>
        <button id="get_vendas">Dados tabela de vendas</button><br>
        <button id="detalhes_venda">Detalhes de uma venda</button><br>
        <button id="get_carrinhos_abandonados">Dados tabela de carrinhos abandonados</button><br>

        <h1>Projetos</h1>
        <button id="get_projetos">Obter meus projetos</button><br>

        <h1>Produtos</h1>
        <button id="get_produtos">Obter meus produtos</button><br>

        <h1>Atendimento</h1>
        <button id="get_sms">Dados da tabela do histórico de sms</button><br>

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
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/user",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#qtd_notificacoes").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/notificacoes/qtdnotificacoes",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#notificacoes").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/notificacoes/",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_menulateral").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/layout/getmenulateral/",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_saldos").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/financas/getsaldos/",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_vitrine").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/vitrine/",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_vendas").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/vendas/",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_carrinhos_abandonados").on("click", function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/carrinhosabandonados/",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#detalhes_venda").on("click",function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/vendas/1108",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_projetos").on("click",function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/projetos",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_produtos").on("click",function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/produtos",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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

            $("#get_sms").on("click",function(){

                $.ajax({
                    method: "GET",
                    dataType: 'json',
                    url: "http://cloudfoxapi.tk/api/atendimento/sms",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
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
