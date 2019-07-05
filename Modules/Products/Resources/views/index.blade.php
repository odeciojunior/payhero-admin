@extends("layouts.master") 

@section('content')

<!-- Page -->
<div class="page">
    <div class="page-header container">
        <div class="row">
            <div class="col-7">
                <h1 class="page-title">Meus produtos</h1>
            </div>
            <div class="col-4">
                {{--  <div class="panel pt-15 p-15">
                    <label for="nome">Nome do produto</label>
                    <div class="input-group">
                        <input id="nome" class="form-control" placeholder="Nome do produto">
                        <span class="input-group-btn">
                          <button id="procurar" class="btn btn-success">Procurar</button>
                      </span>
                    </div>
                </div>  --}}
            </div>
            <div class="col-1">
                @if($products->count() > 0)
                    <a href="/products/create" class="btn btn-floating btn-danger" style="position: relative; float: right">
                        <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="page-content container">
        @if($products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                    <div class="col-xl-3 col-md-6">
                        <div class="card shadow" style='cursor:pointer;'>
                            <img class="card-img-top product-image" src="{!! $product->photo !!}" onerror="this.onerror=null;this.src='{!! asset('modules/global/assets/img/semimagem.png') !!}';" data-link="/products/{{Hashids::encode($product->id)}}/edit" alt="Imagem não encontrada">
                            <div class="card-body">
                                <div class="row align-items-end justify-content-between">
                                    <div class="col-10">
                                        <h5 class="card-title">{{ substr($product->name, 0 ,18)}}</h5>
                                        <p class="card-text sm">Criado em dd/mm/aaaa</p>
                                    </div>
                                    <!-- <div class="col-2">
                                        <span data-toggle='modal' data-target='#modal_excluir' style="float:right">
                                            <a class="delete-product" data-placement='top' data-toggle='tooltip' title='Excluir' product-name='{{$product->name}}' product="{{Hashids::encode($product->id)}}">
                                                <i class='icon wb-trash' aria-hidden='true' style="color: #ff4c52;"></i>
                                            </a>
                                        </span>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{$products->links()}}

        @else
            @push('css')
                <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
            @endpush

            <div class="content-error d-flex text-center">
                <img src="{!! asset('modules/global/assets/img/emptyprodutos.svg') !!}" width="250px">
                <h1 class="big gray">Zero produtos por aqui!</h1>
                <p class="desc gray"> Vamos adicionar seu primeiro produto? </p>
                <a href="/products/create" class="btn btn-primary gradient">Novo Produto</a>
            </div>
        @endif


    </div>
</div>

@push('scripts')
    <script src='{{asset('/modules/products/js/index.js')}}'></script>
@endpush 

@endsection