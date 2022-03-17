<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ops! Acesso não permitido! | CloudFox App </title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">


</head>

<body>

    <div class="page-holder">

        <div class="content-error d-flex text-center">
            <img class="svgorange" src="{!! mix('build/global/img/svg/dog_off.svg') !!}">
            <h1 class="big"> Conteúdo indisponível</h1>
            <p>Você não tem permissão de acesso aqui! Solicite permissão ao<br/>
titular para visualizar e gerenciar esse conteúdo. </p>
            <a href="javascript:history.back()" class="btn btn-info btn-lg">
                <img class="svgorange" src="{!! mix('build/global/img/svg/arrow_left.svg') !!}">&nbsp;&nbsp;Retornar para a página anterior
            </a>
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
    .content-error p{
        font-size:16px;
        color: #A2A3B4;
    }

    .big {
        font-size: 45px;
        color: #7A7C93;
        font-weight: 700;
    }

    .btn-info, .btn-info:hover{
        background: #2E85EC;
    }

</style>

</html>

