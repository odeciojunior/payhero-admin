<head>
    <meta name="access-token"
          content="Bearer {{ auth()->check()? auth()->user()->createToken('Laravel Password Grant Client', ['admin'])->accessToken: '' }}">
    <link rel="stylesheet"
          href="{{ mix('build/layouts/nuvemshop/finalize-integration.min.css') }}">
</head>

<body>

<div class="container">
    <span class="loader"></span>
</div>


<script src="{{ mix('build/layouts/nuvemshop/finalize-integration.min.js') }}"></script>

</body>





