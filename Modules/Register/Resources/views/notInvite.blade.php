<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if(getenv('APP_ENV') === 'production')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    <title>Cadastro | CloudFox </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body id='register-body' style='padding-top:0px;background-color:white;'>

<div class="page-holder">
    <div class="content-error d-flex text-center">
        <img class="svgorange" src="{!! asset('modules/global/img/error.png') !!}">
        <h1 class="big"> Aviso! </h1>
        <h3>O limite de usuários da versão beta foi atingido, aguarde a versão oficial para juntar-se a nós!</h3>
    </div>
</div>

</body>
<style>
    .page-holder {
        font-family: 'Muli', sans-serif;
        color: black;
        width: 100%;
        min-height: 100vh;
        margin: 0;
        overflow: hidden;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
    }

    .logo {
        margin-bottom: 20px;
        width: 60px;
    }

    .content-error {
        color: black;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
    {{--  padding: 20px;  --}}

    }

    .big {
        font-size: 45px;
        color: black;
        font-weight: 700;
    }

    .btn.orange {
        background: transparent;
        background-color: transparent;
        border: 2px solid orangered;
        cursor: pointer;
        color: orangered !important;
        font-weight: 600;
        border-radius: 5px;
        align-self: center;
        padding: 10px 22px;
        transition: all 300ms linear;
    }

    .btn.orange:hover {
        background-color: orangered;
        border-color: orangered;
        color: white !important;
    }
</style>
</html>


