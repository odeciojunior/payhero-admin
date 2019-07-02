@extends("layouts.master")

@section('content')

<div class="page">

    <div class="page-header container">
        <button id="enviar_convite" type="button" class="btn btn-floating btn-danger" style="position: relative; float: right" data-target='#modal_convite' data-toggle='modal'><i class="icon wb-plus" aria-hidden="true"></i></button>
        <h2 class="page-title">Convites</h2>
        @if(count($invites) > 0)
            <p style="margin-top: 12px">A cada convite aceito, você vai ganhar 1% de comissão das vendas efetuadas pelos novos usuários que você convidou durante 1 ano.</p>
        @endif
    </div>

    <div class="page-content container">
        @if(count($invites) > 0)
            <div class="card shadow" data-plugin="matchHeight">
                    <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">

                        <table class="table table-striped">
                            <thead class="text-center">
                                <th class="text-left">Convite</th>
                                <td>Email convidado</td>
                                <td>Status</td>
                                <td>Data cadastro</td>
                                <td>Data expiração</td>
                            </thead>
                            <tbody>
                                @foreach($invites as $key => $invites)
                                    <tr>
                                        <td class="text-left"><button class="btn btn-floating btn-primary btn-sm" disabled>{!! $key + 1!!}</button></td>
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['email_invited'] !!}</td> 
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['status'] !!}</td>
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['register_date'] != '' ? $invites['register_date'] : 'Pendente' !!}</td>
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['expiration_date'] != '' ? $invites['expiration_date'] : 'Pendente' !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
            </div>
        @else
            @push('css')
                <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
            @endpush

            <div class="content-error d-flex text-center">
                <img src="{!! asset('modules/global/assets/img/emptyconvites.svg') !!}" width="250px">
                <h1 class="big gray">Você ainda não enviou convites!</h1>
                <p class="desc gray"> Envie convites, e <strong>ganhe 1% de tudo que seu convidado vender durante um ano!</strong>  </p>
            </div>
        @endif

        <div class="modal fade modal-3d-flip-vertical" id="modal_convite" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                        <h4 id="modal_estornar_titulo" class="modal-title" style="width: 100%; text-align:center">
                            @if(count($companies) > 0)    
                                Novo Convite
                            @else
                                Nenhuma empresa encontrada
                            @endif
                        </h4>
                    </div>
                    <div id="modal_estornar_body" class="modal-body">
                        @if(count($companies) > 0)    
                            <form method="POST" action="{{ route('invitations.send.invitation') }}">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="email">Email do convidado</label>
                                        <input name="email_invited" type="text" class="form-control" id="email" placeholder="Email">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="company">Empresa para receber</label>
                                        <select id="company" name="company" class="form-control">
                                            @foreach($companies as $company)
                                                <option value="{!! Hashids::encode($company['id']) !!}" invite-parameter="{!! Hashids::encode($company['id']) !!}">{!! $company['fantasy_name'] !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <label for="email">Link de convite</label>
                                    </div>
                                    <div class="input-group col-12">
                                        @foreach($companies as $company)
                                            <input type="text" class="form-control" id="invite-link" value="https://app.cloudfox.net/register/{!! Hashids::encode($company['id']) !!}" disabled>
                                            @break
                                        @endforeach
                                        <span class="input-group-btn">
                                            <button id="copy-link" class="btn btn-default" type="button">Copiar</button>
                                        </span>
                                    </div>
                                </div>
    
                                <div class="row" style="margin-top: 35px">
                                    <div class="form-group col-12">
                                        <input type="submit" class="form-control btn" value="Enviar Convite" style="color:white;width: 30%;background-image: linear-gradient(to right, #e6774c, #f92278);position:relative; float:right">
                                    </div>
                                </div>
                            </form>  
                        @else
                            <div class="row">
                                <h3 class="text-center">Para enviar convites primeiro cadastre uma empresa</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script src="{!! asset('modules/invites/js/invites.js') !!}"></script>
@endpush

@endsection

