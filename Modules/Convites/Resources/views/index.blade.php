@extends("layouts.master")

@section('content')

<div class="page">

    <div class="page-header">
        <h2 class="page-title">Convites</h2>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">
            <div class="col-xl-12">
                <div class="example-wrap">
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_convites_enviados"
                                aria-controls="tab_convites_enviados" role="tab">Convites enviados</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_enviar_convites"
                                aria-controls="tab_enviar_convites" role="tab">Enviar convite</a></li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">

                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <th>Email convidado</th>
                                        <th>Status</th>
                                        <th>Data cadastro</th>
                                        <th>Data expiração</th>
                                    </thead>
                                    <tbody>
                                        @foreach($convites as $convite)
                                            <tr>
                                                <td>{!! $convite['email_convidado'] !!}</td>
                                                <td>{!! $convite['status'] !!}</td>
                                                <td>{!! $convite['data_cadastro'] !!}</td>
                                                <td>{!! $convite['data_expiracao'] !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if(count($convites) == 0)
                                    <div style="width:100%; text-center">
                                        <h4> Nenhum convite enviado </h4>
                                    </div>
                                @endif

                            </div>
                            <div class="tab-pane" id="tab_enviar_convites" role="tabpanel">

                                <div style="padding: 30px">
                                    <form method="POST" action="/convites/enviarconvite">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <label for="email">Email para enviar o convite</label>
                                                <input name="email_convidado" type="text" class="form-control" id="email" placeholder="Email">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-12">
                                                <input type="submit" class="form-control btn btn-success" value="Enviar convite" style="width: 30%">
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready( function(){

    });

</script>


@endsection

