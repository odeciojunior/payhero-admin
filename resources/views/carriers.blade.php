<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <title>CloudFox - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="cloudfox">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/img/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/site.min.css') }}">

    <style>
        body {
            padding-top: 0;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .logo {
            float: left;
            width: 95px;
            margin-right: 20px;
            filter: saturate(30.38) hue-rotate(200deg) brightness(0.3098) contrast(0.5)
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery/jquery.min.js') }}"></script>

</head>
<body>
<div class="container py-60">
<img src="{{asset('/modules/global/img/logo.png')}}" class="logo">
<h1 class="my-4">Transportadoras Suportadas</h1>
<table class="table table-responsive">
    <thead class="thead-dark">
        <tr>
            <th>Logo</th>
            <th>Nome</th>
            <th>Homepage</th>
        </tr>
    </thead>
    <tbody>
        @php
            $trackingmoreService = new \Modules\Core\Services\TrackingmoreService();

            $result = $trackingmoreService->getAllCarriers();
        @endphp

        @foreach($result->data as $carrier)
            <tr>
                <td>
                    <img width='50px' src='{{$carrier->picture}}'>
                </td>
                <td>{{$carrier->name}}</td>
                <td>
                    <a href="{{$carrier->homepage}}" target="_blank">{{$carrier->homepage}}</a>
                </td>
            </tr>
    @endforeach
    </tbody>
</table>
</div>
</body>

</html>

