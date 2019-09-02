<div class="transition-details">
    <h3> Transação #{{strtoupper(Hashids::connection('sale_id')->encode($sale->id))}} </h3>
    <p class="sm-text text-muted">
        Pagamento via {{$sale->payment_method == 2 ? 'Boleto' : 'Cartão ' . $sale->flag  }} em {{ $sale->start_date}} às {{$sale->hours}}
    </p>
    <div class="status d-inline">
        <img style="width: 50px;" src="{{asset('/modules/global/img/cartoes/'. $sale->flag. '.png')}}">
        @if($sale->status == 1)
            <span class='badge badge-success'>Aprovada</span></td>
        @elseif($sale->status == 2)
            <span class='badge badge-pendente'>Pendente</span>
        @elseif($sale->status == 3)
            <span class='badge badge-danger'>Recusada</span>
        @elseif($sale->status == 4)
            <span class='badge badge-danger'>Estornada</span>
        @elseif($sale->status == 6)
            <span class='badge badge-primary'>Em análise</span>
        @else
            <span class='badge badge-primary'>{{$sale->status}}</span>
        @endif
    </div>
</div>
<div class="clearfix"></div>
<div class="card shadow pr-20 pl-20 p-10">
    <div class="row">
        <div class="col-lg-3"><p class="table-title"> Produto </p></div>
        <div class="col-lg-9 text-right"><p class="text-muted"> Qtde </p></div>
    </div>
    @foreach($products as $product)
        <div class="row align-items-baseline justify-content-between mb-15">
            <div class="col-lg-2">
                <img src="{{$product['photo'] ?? asset('modules/global/img/produto.png')  }}" width="50px;" style="border-radius:6px;">
            </div>
            <div class="col-lg-5">
                <h4 class="table-title"> {{$product['name']}} </h4>
            </div>
            <div class="col-lg-3 text-right">
                <p class="sm-text text-muted"> {{$product['amount']}}x </p>
            </div>
        </div>
    @endforeach
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
        @if($convertax_value != '0,00')
            <div class='col-lg-8'>
                <span class='text-muted ft-12'>App ConvertaX: </span>
            </div>
            <div class='col-lg-4 text-right'>
                <span class='text-muted ft-12'>{{$convertax_value ?? ''}}</span>
            </div>
        @endif
        <div class='col-lg-6'>
            <h4 class='table-title'>Comissão: </h4>
        </div>
        <div class='col-lg-6 text-right'>
            <h4 class='table-title'>{{$comission ?? ''}}</h4>
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
            <img src="{{ asset('modules/global/img/whatsapplogo.png') }}" width="25px">
        </a>
        <br>
        <span class="table-title gray"> E-mail: {{$client->email}}</span>
        <br>
        <span class="table-title gray"> CPF: {{$client->document}}</span>
        <h4> Entrega </h4>
        <span class="table-title gray table-code-tracking">
            <div class='row' style='line-height: 1.5;'>
                <span class="table-title gray ml-15">Código Rastreio:</span>
                @if (empty($sale->shopify_order) && $sale->status == 1)
                    <div class='col-xl-3 col-lg-3 col-md-3 col-3 icondemo-wrap vertical-align-middle'>
                    <a id='btn-edit-trackingcode' class='edit pointer' title='Editar Código de rastreio' data-code='{{strtoupper(Hashids::connection('sale_id')->encode($sale->id))}}'><i class='icon wb-edit' aria-hidden='true'></i></a>
                    <a id='btn-sent-tracking-user' class='pointer' @if(!empty($delivery->tracking_code)) style='' @else style='display: none;' @endif title='Enviar Email' data-code='{{strtoupper(Hashids::connection('sale_id')->encode($sale->id))}}'><i class='icon wb-inbox' aria-hidden='true'></i></a>
                </div>
                @endif
            </div>
            <div class='tracking-code'>
                <span class='tracking-code-value'>{{isset($delivery->tracking_code)? $delivery->tracking_code:'Não informado'}}</span>
            </div>
        </span>
        <input type='text' class='input-value-trackingcode my-10' style='display:none;' value='{{isset($delivery->tracking_code)? $delivery->tracking_code:''}}'>
        <button type='button' class='btn-save-tracking mb-10' style='display: none;' data-code='{{strtoupper(Hashids::connection('sale_id')->encode($sale->id))}}'>Salvar</button>
        <button type='button' class='btn-cancel-tracking mb-10' style='display: none;'>Cancelar</button>
        <br>
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
            <span class="table-title gray">Link para o boleto: <a role='button' class='copy_link' style='cursor:pointer;' link='{{$sale->boleto_link ?? ''}}'><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
            <br>
            <span class="table-title gray">Linha Digitável: <a role='button' class='copy_link' style='cursor:pointer;' digitable-line='{{$sale->boleto_digitable_line ?? ''}}'><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
            <br>
            <span class="table-title gray"> Vencimento: {{  with(new \Carbon\Carbon($sale->boleto_due_date))->format('d/m/Y ')?? ''}}</span>
            <br>
        @endif
        <span class="table-title gray"> IP: {{$checkout->ip ?? ''}}</span>
        <br>
        <span class="table-title gray "> Dispositivo: {{$checkout->operational_system}} </span>
        <br>
        <span class="table-title gray "> Navegador: {{$checkout->browser}} </span>
        <br>
        <h4> Conversão </h4>
        <span class="table-title gray"> SRC: {{$checkout->src}}  </span>
        <br>
        <span class="table-title gray"> UTM Source: {{$checkout->source}}  </span>
        <br>
        <span class="table-title gray"> UTM Medium: {{$checkout->utm_medium}} </span>
        <br>
        <span class="table-title gray"> UTM Campaign: {{$checkout->utm_campaign}}</span>
        <br>
        <span class="table-title gray"> UTM Term: {{$checkout->utm_term}} </span>
        <br>
        <span class="table-title gray"> UTM Content: {{$checkout->utm_content}}</span>
    </div>
</div>
<script>
    $(document).ready(function () {

        $("#sales_tab").css("min-width", $(window).width() / 2);

        $("#client_tab").css("min-width", $("#sales_tab").width());
        $("#products_tab").css("min-width", $("#sales_tab").width());

        $("#btn-edit-trackingcode").on('click', function () {
            $('.tracking-code').hide();
            $('.input-value-trackingcode').show();
            $('.btn-save-tracking').show();
            $('.btn-cancel-tracking').show();
        });

        $('.btn-cancel-tracking').on('click', function () {
            $('.tracking-code').show();
            $('.input-value-trackingcode').val('').hide();
            $('.btn-save-tracking').hide();
            $('.btn-cancel-tracking').hide();
        });

        $('.btn-save-tracking').on('click', function () {
            let trackingCode = $(".input-value-trackingcode").val();
            let referenceCode = $(this).attr('data-code');
            ajaxUpdateTracking(trackingCode, referenceCode);
        });

        function ajaxUpdateTracking(tracking, reference) {
            var delivery = '{{\Vinkla\Hashids\Facades\Hashids::encode($delivery->id)}}';
            var sale = '{{\Vinkla\Hashids\Facades\Hashids::encode($sale->id)}}';

            $.ajax({
                method: 'POST',
                url: '/sales/update/trackingcode',
                data: {
                    sale: sale,
                    delivery: delivery,
                    trackingCode: tracking,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (response) {
                    if (response.status == '422') {
                        for (error in response.responseJSON.errors) {
                            alertCustom('error', String(response.responseJSON.errors[error]));
                        }
                    } else {
                        alertCustom("error", response.message)
                    }
                    $(".btn-cancel-tracking").click();
                },
                success: function (response) {
                    $(".btn-cancel-tracking").click();
                    $(".tracking-code-value").html(tracking);
                    $('#btn-sent-tracking-user[data-code=' + reference + ']').show('slow');
                    alertCustom('success', response.message);
                }
            });
        }

        $('#btn-sent-tracking-user').on('click', function () {
            let sale = '{{\Vinkla\Hashids\Facades\Hashids::connection('sale_id')->encode($sale->id)}}';

            $.ajax({
                method: 'POST',
                url: '/sales/update/trackingcode/' + sale,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (response) {
                    if (response.status == '422') {
                        for (error in response.responseJSON.errors) {
                            alertCustom('error', String(response.responseJSON.errors[error]));
                        }
                    } else {
                        alertCustom("error", response.message)
                    }
                },
                success: function (response) {
                    alertCustom('success', response.message);
                }
            });
        });
    });
</script>

