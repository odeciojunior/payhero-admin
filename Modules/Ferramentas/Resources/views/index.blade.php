@extends("layouts.master")
@section('title', '- Dashboard')
@section('content')
@section('styles')
@endsection
 
<div class="page">
  <div class="page-content container-fluid">
    <div class="row" data-plugin="matchHeight" data-by-row="true">
      <div class="col-xxl-12 col-lg-12">
        <div class="card card-shadow card-responsive" id="widgetLineareaColor">
          <div class="card-block p-0">
            <div class="pt-30 p-30" style="height:calc(100% - 250px);">
              <h3 style="margin-bottom: 40px"> Ferramentas </h3>
              <div class="row" style="margin-bottom: 200px">

                  <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <a href="{!! route('ferramentas.sms') !!}">
                      <div class="card card-inverse card-shadow bg-green-600 white" id="widgetSaleBar">
                        <div class="card-block p-0">
                            <div class="row no-space">
                              <div class="card-block">
                                <h4 class="card-title">SMS</h4>
                                <div class="row">
                                    <div class="col-10">
                                        <p class="card-text">
                                          Serviço de envio de sms.
                                        </p>
                                    </div>
                                    <div class="col-2">
                                        <i class="icon wb-envelope" aria-hidden="true"></i>
                                    </div>
                                </div>
                              </div>
                            </div>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
                    <a href="{!! route('ferramentas.shopify') !!}">
                      <div class="card card-inverse card-shadow bg-blue-600 white" id="widgetSaleBar">
                        <div class="card-block p-0">
                            <div class="row no-space">
                              <div class="card-block">
                                <h4 class="card-title">Shopify</h4>
                                <div class="row">
                                    <div class="col-10">
                                        <p class="card-text">
                                          Integração com shopify.
                                        </p>
                                    </div>
                                    <div class="col-2">
                                        <i class="icon wb-plugin" aria-hidden="true"></i>
                                    </div>
                                </div>
                              </div>
                            </div>
                        </div>
                      </div>
                    </a>

                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

