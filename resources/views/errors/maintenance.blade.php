<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manutenção | CloudFox App </title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">


</head>

<body>

<div class="page-holder">

    <div class="content-error d-flex text-center">
        <img style="width:100px; margin-bottom: 20px;" src="{!! asset('modules/global/img/tools.svg') !!}">
        <h1 class="big">Voltaremos em breve!</h1>
        <p style="font-size:12px; max-width: 400px;">Desculpe pelo transtorno, nós estamos realizando uma manutenção no momento e voltaremos em breve.</p>
        <p><img style="width:100px; filter: brightness(0);" src="http://dev.cloudfox.com.br/modules/global/adminremark/assets/images/logo-oficial.svg" alt="CloudFox"></p>
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

    .big {
        font-size: 45px;
        color: black;
        font-weight: 700;
    }

    .content-error {
        color: black;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
}
</style>

</html>

