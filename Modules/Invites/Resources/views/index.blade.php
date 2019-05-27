@extends("layouts.master")

@section('content')

<div class="page">

    <div class="page-header">
        <button id="enviar_convite" type="button" class="btn btn-floating btn-danger" style="position: relative; float: right" data-target='#modal_convite' data-toggle='modal'><i class="icon wb-plus" aria-hidden="true"></i></button>
        <h2 class="page-title">Convites</h2>
        <p style="margin-top: 12px">A cada convite aceito, você vai ganhar 1% de comissão das vendas efetuadas pelos novos usuários que você convidou durante 1 ano.</p>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">
            <div class="col-xl-12">
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">

                    <table class="table table-hover" style="margin-top:10px">
                        <thead class="text-center">
                            <th class="text-left">Convite</th>
                            <th>Email convidado</th>
                            <th>Status</th>
                            <th>Data cadastro</th>
                            <th>Data expiração</th>
                        </thead>
                        <tbody>
                            @if(count($invites) > 0)
                                @foreach($invites as $key => $invites)
                                    <tr>
                                        <td class="text-left"><button class="btn btn-floating btn-primary btn-sm" disabled>{!! $key + 1!!}</button></td>
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['email_invited'] !!}</td> 
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['status'] !!}</td>
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['register_date'] != '' ? $invites['register_date'] : 'Pendente' !!}</td>
                                        <td class="text-center" style="vertical-align: middle">{!! $invites['expiration_date'] != '' ? $invites['expiration_date'] : 'Pendente' !!}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Nenhum convite enviado
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="modal fade modal-3d-flip-vertical" id="modal_convite" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
              <div class="modal-dialog modal-simple">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                    <h4 id="modal_estornar_titulo" class="modal-title" style="width: 100%; text-align:center">Novo Convite</h4>
                  </div>
                  <div id="modal_estornar_body" class="modal-body">
                    <form method="POST" action="/convites/enviarconvite">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="email">Email do convidado</label>
                                <input name="email_invited" type="text" class="form-control" id="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <input type="submit" class="form-control btn" value="Enviar Convite" style="color:white;width: 30%;background-image: linear-gradient(to right, #e6774c, #f92278);position:relative; float:right">
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

<script>

    $(document).ready( function(){

    });

</script>


@endsection

