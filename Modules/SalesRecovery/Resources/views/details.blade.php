<div class="transition-details">
    <p class="sm-text text-muted">  {{$date . ' às ' .$hours}} <br>
    </p>
    <div class="status d-inline">
        @if($status  == 'Recuperado')
            <span class="badge badge-success mr-5"> {{$status}} </span>
        @else
            <span class="badge badge-danger mr-5"> {{$status}} </span>
        @endif
    </div>
</div>
<div class="clearfix"></div>
<div class="card shadow pr-20 pl-20 p-10">
    <div class="row">
        <div class="col-lg-6"><p class="table-title"> Produto </p></div>
        <div class="col-lg-2 text-right"><p class="text-muted"> Qtde </p></div>
        <div class="col-lg-4 text-right"><p class="text-muted"> Valor </p></div>
    </div>
    <div class='row align-items-baseline justify-content-between mb-15'>
        @foreach($plans as $plan)
            <div class="col-lg-2">
                <img src='{{$plan['photo'] ?? asset('modules/global/img/produto.png')}}' width='50px' style='border-radius: 6px;'>
            </div>
            <div class='col-lg-4'>
                <h4 class='table-title'>{{$plan['name']}}</h4>
            </div>
            <div class='col-lg-2 text-right'>
                <p class='sm-text text-muted'>{{$plan['amount']}}x</p>
            </div>
            <div class='col-lg-4 text-right'>
                <p class='sm-text text-muted'>
                    {{number_format($plan['value'], 2, ',', '.')}}
                </p>
            </div>
        @endforeach
    </div>
    <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
        <div class="col-lg-6">
            <h4 class="table-title"> Total </h4>
        </div>
        <div class="col-lg-6 text-right">
            <h4 class="table-title"> {{$total}} </h4>
        </div>
    </div>
</div>
<div class="nav-tabs-horizontal">
    <div class="nav nav-tabs nav-tabs-line text-center" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" style="width:50%;">Cliente</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" style="width:50%;">Detalhes</a>
    </div>
</div>
<div class="tab-content p-10" id="nav-tabContent">
    <!-- CLIENTE -->
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <h4> Dados Pessoais </h4>
        <span class="table-title gray"> Nome: {{$client->name}} </span>
        <br>
        <span class='table-title gray'>Telefone: {{$client->telephone}}</span>
        <a href="{{$whatsapp_link}}" target='_blank'>
            <img src="{!! asset('modules/global/img/whatsapplogo.png') !!}" width="25px">
        </a>
        <br>
        <span class="table-title gray"> E-mail: {{$client->email}} </span>
        <br>
        <span class="table-title gray"> CPF: {{$client->document}} </span>
        <h4> Entrega </h4>
        <span class="table-title gray"> Endereço:	{{$client->street}}</span>
        <br>
        <span class="table-title gray"> CEP: {{$client->zip_code}} </span>
        <br>
        <span class="table-title gray"> Cidade: {{$client->city . '/' .$client->state}}</span>
    </div>
    <!-- DETALHES  -->
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <h4> Dados Gerais </h4>
        <span class="table-title gray"> Motivo: {{$client->error}}  </span>
        <br>
        @if($link == 'Dominio removido')
            <span class="table-title gray"> Link: {{$link}} </span>
        @else
            <span class="table-title gray">Link: <a role='button' class='copy_link' style='cursor:pointer;' link='{{$link ?? ''}}'><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
        @endif
        <br>
        <span class="table-title gray"> IP: {{$checkout->ip}}  </span>
        <br>
        <span class="table-title gray "> Dispositivo: {{$checkout->is_mobile}} </span>
        <br>
        <h4> Conversão </h4>
        <span class="table-title gray"> SRC: {{$checkout->src}}  </span>
        <br>
        <span class="table-title gray"> UTM Source: {{$checkout->utm_source}}  </span>
        <br>
        <span class="table-title gray"> UTM Medium: {{$checkout->utm_medium}} </span>
        <br>
        <span class="table-title gray"> UTM Campaign: {{$checkout->utm_campaign}}</span>
        <br>
        <span class="table-title gray"> UTM Term: {{$checkout->utm_term}} </span>
        <br>
        <span class="table-title gray"> UTM Content: {{$checkout->utm_content}} </span>
    </div>
</div>

