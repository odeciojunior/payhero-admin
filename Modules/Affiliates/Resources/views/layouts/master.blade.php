<!DOCTYPE html>
<html lang="en">

<head>
    <title>Nexus Pay - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
          content="nexuspay">
    <meta name="msapplication-TileColor"
          content="#603cba">
    <meta name="theme-color"
          content="#ffffff">
    @if (getenv('APP_ENV') === 'production')
        <meta http-equiv="Content-Security-Policy"
              content="upgrade-insecure-requests">
    @endif

    <!-- access token used for api ajax requests -->
    <meta name="access-token"
          content="Bearer {{ auth()->check() && auth()->user()->status != 3? auth()->user()->createToken('Laravel Password Grant Client')->accessToken: '' }}">
    <meta name="current-url"
          content="{{ env('APP_URL') }}">
    <meta name="user-id"
          content="{{ hashids_encode(auth()->user()->id) }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ mix('build/global/img/apple-touch-icon.png') }}">
    <link rel="icon"
          type="image/png"
          sizes="32x32"
          href="{{ mix('build/global/img/favicon-32x32.png') }}">
    <link rel="icon"
          type="image/png"
          sizes="16x16"
          href="{{ mix('build/global/img/favicon-16x16.png') }}">
    <link rel="mask-icon"
          href="{{ mix('build/global/img/safari-pinned-tab.svg') }}"
          color="#5bbad5">
    <!-- CSS -->
    <link rel="stylesheet"
          href="{{ mix('build/layouts/affiliates/master.min.css') }}">
    @stack('css')

    @if (env('APP_ENV', 'production') == 'production')
        <script src="{{ mix('build/layouts/master/sentry-bundle.min.js') }}"></script>
        <script>
            Sentry.init({
                        dsn: {{ getenv('SENTRY_LARAVEL_DSN') }});
        </script>
    @endif

    <script src="{{ mix('build/layouts/affiliates/master2.min.js') }}"></script>
    <script>
        Breakpoints();
    </script>
    <style>
        body {
            background: linear-gradient(90deg, #fafafa 36px, transparent 1%) center, linear-gradient(#fafafa 36px, transparent 1%) center, #e5e5e5;
            background-size: 40px 40px;
        }
    </style>
</head>

<body>
    {{-- loading --}}
    <div id='loadingOnScreen'
         style='height:100%; width:100%; position:absolute'>
    </div>
    @yield('content')

    <!-- JS -->
    <script src="{{ mix('build/layouts/affiliates/master.min.js') }}"></script>
    <script>
        verifyDocumentPending();
    </script>
    @stack('scripts')

</body>

</html>
