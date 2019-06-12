@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header">
            <div class="row">
                <div class="col-7">
                    <h1 class="page-title">Meus produtos</h1>
                </div>
                <div class="col-4">
                    <div class="panel pt-15 p-15">
                        <label for="nome">Nome do produto</label>
                        <div class="input-group">
                            <input id="nome" class="form-control" placeholder="Nome do produto">
                            <span class="input-group-btn">
                          <button id="procurar" class="btn btn-success">Procurar</button>
                      </span>
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <a href="{{route('products.create')}}" class="btn btn-floating btn-danger" style="position: relative; float: right">
                        <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i></a>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            @if(count($products) == 0)
                <div class="alert alert-warning" role="alert">
                    Nenhum produto encontrado.
                </div>
            @else
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-xl-3 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$product['photo'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                                <div class="card-block">
                                    <h4 class="card-title text-center">{{$product->name}}</h4>
                                    <hr>
                                    <span data-toggle='modal' data-target='#modal_editar'>
                                            <a href="{{route('products.edit', $product->id)}}" class='btn btn-outline btn-primary editar_produto' data-placement='top' data-toggle='tooltip' title='Editar'>
                                                <i class='icon wb-pencil' aria-hidden='true'></i>
                                            </a>

                                    </span>
                                    <span data-toggle='modal' data-target='#modal_excluir' style="float:right">
                                        <a class='btn btn-outline btn-danger excluir_produto' data-placement='top' data-toggle='tooltip' title='Excluir' produto="{!! $product['id'] !!}">
                                            <i class='icon wb-trash' aria-hidden='true'></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{$products->links()}}
            @endif
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <form id="form_excluir_produto" method="POST" action="/products/">
                            @method('DELETE')
                            @csrf
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center">Excluir ?</h4>
                            </div>
                            <div id="modal_excluir_body" class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-success">Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {

            $("#procurar").on("click", function () {
                window.location.href = "/products?nome=" + $('#nome').val();
            });

            $('.excluir_produto').on('click', function () {

                var id_produto = $(this).attr('produto');

                $('#form_excluir_produto').attr('action', '/products/' + id_produto);

                var name = $(this).parent().parent().find(".card-title").html();

                $('#modal_excluir_titulo').html('Excluir o produto ' + name + '?');

            });

        });
    </script>


@endsection

