<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sirius - Transportadoras Suportadas</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ mix('build/global/img/logos/2021/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ mix('build/global/img/logos/2021/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ mix('build/global/img/logos/2021/favicon/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ mix('build/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Muli:400,600,700,800" rel="stylesheet">

    <!-- Styles -->
    <style>

        body {
            margin: 0;
            box-sizing: border-box;
            background-color: #F4F4F4;
            font-family: 'Muli', sans-serif;
            font-size: 14px;
            color: #404040;
        }

        .header {
            display: flex;
            align-items:center;
            margin-bottom: 40px;
        }

        .header img {
            margin-right: 15px;
        }

        .header h1 {
            margin: 0;
        }

        .container {
            display: inline-block;
            padding: 40px;
        }

        .logo {
            height: 26px;
        }

        .row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            color: #000000;

        }

        .row img {
            width: 50px;
            margin-right: 15px;
        }

        .row a {
            margin-left: 15px;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img class="logo" src="{{mix('/build/global/img/logos/2021/svg/sirius-logo.svg')}}" alt="Sirius Logo">
        <h1>Transportadoras compatíveis</h1>
    </div>

    @php
        $trackingmoreService = new \Modules\Core\Services\TrackingmoreService();
        $result = $trackingmoreService->getAllCarriers();
    @endphp
    @foreach($result->data as $carrier)
        <div class="row">
            <img src="{{$carrier->picture}}" alt="Carrier Logo">
            <b>{{$carrier->name}}</b>
            <a href="{{$carrier->homepage}}" target="_blank">{{$carrier->homepage}}</a>
        </div>
    @endforeach
</div>
</body>
</html>

