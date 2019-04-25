@extends("layouts.master")
@section('title', '- Aplicativos')
@section('content')
@section('styles')
@endsection
 
<div class="page">
  <div class="page-content container-fluid">

    <h3 style="margin-bottom: 40px"> Aplicativos </h3>
    <div class="row" style="margin-bottom: 200px">

        <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
          <a href="#">
            <div class="card card-inverse card-shadow bg-green-600 white" id="widgetSaleBar">
              <div class="card-block p-0">
                  <div class="row no-space">
                    <div class="card-block">
                      <h4 class="card-title">Hotzapp</h4>
                      <div class="row">
                          <div class="col-10">
                              <p class="card-text">
                                Integração com Hotzapp.
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


@endsection

