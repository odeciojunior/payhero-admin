<div class="transition-details">
    <h3> Transação #{{strtoupper(Hashids::connection('sale_id')->encode($sale->id))}} </h3>
    <p class="sm-text text-muted">
        Pagamento via {{$sale->payment_method == 2 ? 'Boleto' : 'Cartão ' . $sale->flag  }} em {{ $sale->start_date}} às {{$sale->hours}}
    </p>
    <div class="status d-inline">
        <img style="width: 50px;" src="{{asset('/modules/global/assets/img/cartoes/'. $sale->flag. '.png')}}">
        @if($sale->status == 1)
            <span class='badge badge-success'>Aprovada</span></td>
        @elseif($sale->status == 2)
            <span class='badge badge-pendente'>Pendente</span>
        @elseif($sale->status == 3)
            <span class='badge badge-danger'>Recusada</span>
        @elseif($sale->status == 4)
            <span class='badge badge-secondary'>Estornada</span>
        @else
            <span class='badge badge-primary'>{{$sale->status}}</span>
        @endif
    </div>
</div>
<div class="clearfix"></div>
<div class="card shadow pr-20 pl-20 p-10">
    <div class="row">
        <div class="col-lg-7"><p class="table-title"> Produto </p></div>
        <div class="col-lg-2 text-right"><p class="text-muted"> Qtde </p></div>
        <div class="col-lg-3 text-right"><p class="text-muted"> Valor </p></div>
    </div>
    <div class="row align-items-baseline justify-content-between mb-15">
        @foreach($plans as $plan)
            <div class="col-lg-2">
                <img src="{{$plan['photo'] ?? asset('modules/global/assets/img/produto.png')  }}" width="50px;" style="border-radius:6px;">
            </div>
            <div class="col-lg-5">
                <h4 class="table-title"> {{$plan['name']}} </h4>
            </div>
            <div class="col-lg-2 text-right">
                <p class="sm-text text-muted"> {{$plan['amount']}}x </p>
            </div>
            <div class="col-lg-3 text-right">
                <p class="sm-text text-muted"> R${{$plan['value']}} </p>
            </div>
        @endforeach
    </div>
    <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
        <div class="col-lg-6 align-items-center">
            <span class="text-muted ft-12"> Subtotal </span>
        </div>
        <div class="col-lg-6 text-right">
            <span class="text-muted ft-12 subTotal"> R$ {{$subTotal}} </span>
        </div>
        <div class="col-lg-6">
            <span class="text-muted ft-12"> Frete </span>
        </div>
        <div class="col-lg-6 text-right">
            <span class="text-muted ft-12"> R$ {{$shipment_value}} </span>
        </div>
        @if(isset($sale->dolar_quotation))
            <div class="col-lg-6">
                <span class="text-muted ft-12"> IOF </span>
            </div>
            <div class="col-lg-6 text-right">
                <span class="text-muted ft-12"> R$ {{$sale->iof}} </span>
            </div>
        @endif
        <div class="col-lg-6">
            <span class="text-muted ft-12"> Desconto</span>
        </div>
        <div class="col-lg-6 text-right">
            <span class="text-muted ft-12"> R$ {{$discount}} </span>
        </div>
        <div class="col-lg-6">
            <h4 class="table-title"> Total </h4>
        </div>
        <div class="col-lg-6 text-right">
            <h4 class="table-title"> R$ {{$total}} </h4>
        </div>
    </div>
    <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
        @if(isset($sale->dolar_quotation))
            <div class='col-8'>
                <span class='text-muted ft-12'>Câmbio (1 $ = R$ {{$sale->dolar_quotation}}): </span>
            </div>
            <div class='col-4 text-right'>
                <span class='text-muted ft-12'>US$ {{$taxa}}</span>
            </div>
        @endif
        <div class='col-lg-8'>
            <span class='text-muted ft-12'>Taxas ({{$transaction->percentage_rate}}% + {{$transaction->transaction_rate}}): </span>
        </div>
        <div class='col-lg-4 text-right'>
            <span class='text-muted ft-12'>{{$taxaReal ?? ''}}</span>
        </div>
        <div class='col-lg-6'>
            <h4 class='table-title'>Comissão: </h4>
        </div>
        <div class='col-lg-6 text-right'>
            <h4 class='table-title'>{{$comission?? ''}}</h4>
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
        <span class="table-title gray"> Nome: {{$client->name}}</span>
        <br>
        <span class='table-title gray'>Telefone: {{$client->telephone}}</span>
        <a href="{{$whatsapp_link}}" target='_blank'>
            <img src="{!! asset('modules/global/assets/img/whatsapplogo.png') !!}" width="25px">
        </a>
        <br>
        <span class="table-title gray"> E-mail: {{$client->email}}</span>
        <br>
        <span class="table-title gray"> CPF: {{$client->document}}</span>
        <h4> Entrega </h4>
        <span class="table-title gray"> Endereço: {{$delivery->street}}, {{$delivery->number}}</span>
        <br>
        <span class="table-title gray"> CEP: {{$delivery->zip_code}}</span>
        <br>
        <span class="table-title gray"> Cidade: {{$delivery->city}}/{{$delivery->state}}</span>
    </div>
    <!-- DETALHES  -->
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <h4> Dados Gerais </h4>
        @if($sale->payment_method == 1)
            <span class="table-title gray"> Bandeira: {{$sale->flag ?? ''}}</span>
            <br>
            <span class="table-title gray"> Quantidade de parcelas: {{$sale->installments_amount ?? ''}}</span>
            <br>
        @endif
        @if($sale->payment_method == 2)
            <span class="table-title gray"> <a href="{{$sale->boleto_link ?? ''}}" class="gradient"> Link para o boleto </a></span>
            <br>
            <span class="table-title gray"> Linha Digitável:<br> {{$sale->boleto_digitable_line ?? ''}}</span>
            <br>
            <span class="table-title gray"> Vencimento: {{  with(new \Carbon\Carbon($sale->boleto_due_date))->format('d/m/Y ')?? ''}}</span>
            <br>
        @endif
        <span class="table-title gray"> IP: {{$checkout->ip ?? ''}}</span>
        <br>
        <span class="table-title gray "> Dispositivo: - </span>
        <br>
        <h4> Conversão </h4>
        <span class="table-title gray"> SRC: {{$checkout->src ?? ''}}  </span>
        <br>
        <span class="table-title gray"> UTM Source: {{$checkout->source ?? ''}}  </span>
        <br>
        <span class="table-title gray"> UTM Medium: {{$checkout->utm_medium ?? ''}} </span>
        <br>
        <span class="table-title gray"> UTM Campaign: {{$checkout->utm_campaign ?? ''}}</span>
        <br>
        <span class="table-title gray"> UTM Term: {{$checkout->utm_term ?? ''}} </span>
        <br>
        <span class="table-title gray"> UTM Content: {{$checkout->utm_content ?? ''}}</span>
    </div>
</div>
<script>
    $(document).ready(function () {

        $("#sales_tab").css("min-width", $(window).width() / 2);

        $("#client_tab").css("min-width", $("#sales_tab").width());
        $("#products_tab").css("min-width", $("#sales_tab").width());
    });
</script>

