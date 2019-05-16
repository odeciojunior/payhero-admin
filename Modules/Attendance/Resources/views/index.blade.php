@extends("layouts.master")
@section('title', '- Dashboard')
@section('content')

@section('styles')
@endsection

<div class="page">
  <div class="page-content container-fluid">
    <a href="#" class="btn btn-floating btn-danger" style="position: relative; float: right"><i class="icon wb-settings" aria-hidden="true" style="margin-top:8px"></i></a>
    <h1 class="page-title">Atendimento</h1>
    <div class="row" style="margin-top: 80px">
        <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
        <a href="#">
            <!-- Widget Sale Bar -->
            <div class="card card-inverse card-shadow bg-green-600 white" id="widgetSaleBar">
                <div class="card-block p-0">
                <div class="pt-25 px-30">
                    <div class="row no-space">
                    <div class="col-12 text-center">
                        <h3 style="color:white">SAC</h3>
                    </div>
                    <hr>
                    <div class="col-12 text-center" style="margin-top:30px">
                        <p class="font-size-30 text-nowrap"></p>
                    </div>
                    </div>
                </div>
            </div>
            </div>
            <!-- End Widget Sale Bar -->
        </a>        
        </div>

        <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
        <a href="{!! route('atendimento.sms') !!}">
            <!-- Widget Sale Bar -->
            <div class="card card-inverse card-shadow bg-blue-600 white" id="widgetSaleBar">
                <div class="card-block p-0">
                    <div class="pt-25 px-30">
                        <div class="row no-space">
                            <div class="col-12 text-center">
                                <h3 style="color:white">SMS</h3>
                            </div>
                            <div class="col-12 text-center" style="margin-top:30px">
                                <p class="font-size-30 text-nowrap"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Widget Sale Bar -->
        </a>
        </div>

        <div class="col-xxl-12 col-lg-4 h-p50 h-only-lg-p100 h-only-xl-p100">
        <a href="#">
            <!-- Widget Sale Bar -->
            <div class="card card-inverse card-shadow bg-yellow-600 white" id="widgetSaleBar">
                <div class="card-block p-0">
                    <div class="pt-25 px-30">
                        <div class="row no-space">
                            <div class="col-12 text-center">
                                <h3 style="color:white">Email</h3>
                            </div>
                            <div class="col-12 text-center" style="margin-top:30px">
                                <p class="font-size-30 text-nowrap"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Widget Sale Bar -->
        </a>
        </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')


@endsection
