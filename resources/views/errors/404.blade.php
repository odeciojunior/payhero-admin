<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ops! Não encontramos essa página! | CloudFox App </title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">


</head>

<body>

    <div class="page-holder">

        <div class="content-error d-flex text-center">
            <img class="svgorange" src="{!! asset('modules/global/img/error.png') !!}">
            <h1 class="big"> Ops! Nada por aqui</h1>
            <p>Erro | 404</p> 
            <p style="font-size:12px">Parece que essa página que você procura não existe. </p>
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

