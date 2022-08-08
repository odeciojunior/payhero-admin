@php($gitInfo = \Modules\Core\Services\FoxUtils::gitInfo())
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>QA Utilities</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
          crossorigin="anonymous">
</head>

<body>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Branch atual: <b>{{ $gitInfo->branch }}</b></h3>
                <h5>Últimos commits:</h5>
                <ul class="list-group">
                    @foreach ($gitInfo->commits as $commit)
                        <li class="list-group-item">
                            @if ($commit->is_merge)
                                <span class="badge bg-primary float-end">Merge</span>
                            @endif
                            <div><b>Hash: </b> {{ $commit->hash }}</div>
                            <div><b>Comentário: </b> {{ $commit->comment }}</div>
                            <div><b>Data: </b> {{ $commit->date }}</div>
                            <div><b>Autor: </b> {{ $commit->author }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Cache</h3>
                <code>php artisan optimize:clear</code>
                <div>
                    @if (\Illuminate\Support\Facades\Artisan::call('optimize:clear') === 0)
                        Cache do laravel limpo com sucesso!
                    @else
                        <b>Oh no!</b> Não foi possível limpar o cache!
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>

</html>
