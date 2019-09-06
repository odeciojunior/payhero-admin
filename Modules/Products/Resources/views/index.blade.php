@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-3">
                    <select id='type-products'> </select>
                </div>
                <div class="col-3" id='is-projects' style='display:none;'>
                    <select id='select-projects'> </select>
                </div>
                <div class="col-6">
                    <a href="/products/create" class="btn btn-floating btn-danger" style="position: relative; float: right">
                        <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div id='data-table-products' class='row'>
            </div>
            <div id='pagination-products'></div>
            <div class='products-is-empty' style='display:none;'>
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
                @endpush
                <div class="content-error text-center">
                    <img src="{!! asset('modules/global/img/emptyprodutos.svg') !!}" width="250px">
                    <h1 class="big gray">Zero produtos por aqui!</h1>
                    {{--<p class="desc gray"> Vamos adicionar seu primeiro produto? </p>
                    <a href="/products/create" class="btn btn-primary gradient">Novo Produto</a>--}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='{{asset('/modules/products/js/index.js')}}'></script>
    @endpush

@endsection
