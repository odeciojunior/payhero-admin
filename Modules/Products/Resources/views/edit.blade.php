@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('modules/global/adminremark2/global/vendor/dropify/dropify.min.css') !!}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Editar produto</h1>
            <p class="desc mt-10"> Preencha os dados sobre seu produto atentamente. </p>
            <div class="page-header-actions">
                <a class="d-none d-lg-block btn btn-primary float-right" href="{{ route('products.index') }}">
                    Meus produtos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <form method="post" action="/products/{!! Hashids::encode($product->id) !!}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card shadow pt-15">
                    <nav>
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true">1. Informações Básicas</a>
                            <a class="nav-item nav-link" id="nav-logistic-tab" data-toggle="tab" href="#nav-logistic" role="tab" aria-controls="nav-logistic" aria-selected="false">2. Logística</a>
                        </div>
                    </nav>
                    <div class="p-15">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active p-30" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab">
                                <div class="row justify-content-between align-items-baseline">
                                    <div class="col-lg-10">
                                        <h3> 1. Informações Básicas </h3>
                                        <p> Preencha atentamente as informações sobre seu produto </p>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="d-flex flex-column">
                                            <input name="product_photo" type="file" class="form-control" id="photo" style="display:none">
                                            <label for="name">Selecione uma imagem</label>
                                            <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" src='{{ $product->photo ?? asset('modules/global/img/projeto.png')}}' style="max-height: 300px; max-width: 300px;">
                                            <input type="hidden" name="photo_x1"> <input type="hidden" name="photo_y1">
                                            <input type="hidden" name="photo_w"> <input type="hidden" name="photo_h">
                                            <p class="info mt-5" style="font-size:10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i> A imagem escolhida deve estar no formato JPG, JPEG, ou PNG.
                                                <br> Dimensões ideais: 300 x 300 pixels.
                                            </p>
                                        </div>
                                        <div class="d-flex flex-column" id="div_digital_product_upload" style="visibility: hidden">
                                            <label for="digital_product">Produto digital</label>
                                            <input type="file" id="digital_product" name="digital_product" data-plugin="dropify">
                                            <p class="info mt-5" style="font-size:10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i> Produto digital que será enviado para o cliente.
                                                <br>.
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label for="name">Nome</label>
                                                <input name="name" type="text" class="input-pad" id="name" value="{!! $product->name !!}" placeholder="O nome do seu produto" required="">
                                                @error('name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-lg-12">
                                                <label for="description">Descrição</label>
                                                <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Fale um pouco sobre seu produto" required="">{!! $product->description !!}</textarea>
                                                @error('description')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <p> Máximo 30 caracteres. </p>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="name">Tipo</label>
                                                <div class="d-flex justify-content-start">
                                                    <div class="radio-custom radio-primary pr-20">
                                                        <input type="radio" id="fisico" name="format" value="1" checked>
                                                        <label for="fisico">Físico</label>
                                                    </div>
                                                    <div class="radio-custom radio-primary d-flex">
                                                        <input type="radio" id="digital" name="format" value="0" disabled>
                                                        <label for="digital">Digital (em breve)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="category">Categoria</label>
                                                <select name="category" class="form-control select-pad">
                                                    <option value="">Selecione</option>
                                                    @foreach($categories as $category)
                                                        <option value="{!! $category['id'] !!}" {!! $category['id'] == $product->category ? 'selected' : '' !!}>{!! $category['name'] !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="cost">Custo
                                                    <span class="ml-5 sm-text text-muted" style="font-size: 0.8em; font-weight: normal;"> Opcional </span>
                                                </label>
                                                <input name="cost" type="text" class="input-pad money" id="cost" value="{!! $product->cost !!}" placeholder="Digite o custo" autocomplete="off">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="price">Preço
                                                    <span class="ml-5 sm-text text-muted" style="font-size: 0.8em; font-weight: normal;"> Opcional </span>
                                                </label>
                                                <input name="price" type="text" class="input-pad money" placeholder="Digite o preço" value="{!! $product->price !!}" autocomplete="off">
                                            </div>
                                            <div id="div_next_step" class="form-group col-lg-12 d-flex justify-content-between mt-10">
                                                <a class="btn btn-danger d-flex delete-product white" product-name='{{$product->name}}' product="{{Hashids::encode($product->id)}}" data-toggle="modal" data-target="#modal-delete">
                                                    <i class="icon wb-trash align-middle mr-5" aria-hidden="true"></i> Excluir produto
                                                </a>
                                                <button id="next_step" type="button" class="mr-5 btn btn-success">Prosseguir<i class="icon wb-chevron-right" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div id="div_save_digital_product" class="form-group col-lg-12 text-right" style="display:none">
                                                <button id="save_digital_product" type="submit" class="btn btn-success">Salvar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade p-30" id="nav-logistic" role="tabpanel" aria-labelledby="nav-logistic-tab">
                                <div class="row mt50">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h3> 2. Logística </h3>
                                                <p class="pt-10"> Preencha atentamente as informações sobre seu produto </p>
                                            </div>
                                            <div class="form-group col-lg-4">
                                                <label for="height">Altura (cm)</label>
                                                <input name="height" type="text" class="input-pad" id="height" placeholder="Ex: 150cm" value="{!! $product->height !!}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-4">
                                                <label for="width">Largura (cm)</label>
                                                <input name="width" type="text" class="input-pad" id="width" placeholder="Ex: 135cm" value="{!! $product->width !!}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-4">
                                                <label for="weight">Peso (g)</label>
                                                <input name="weight" type="text" class="input-pad" id="weight" placeholder="Ex: 950g" value="{!! $product->weight !!}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="shipping">Transportadora:</label>
                                                <select class="form-control select-pad" id="shipping">
                                                    <option value="proprio" {{--{!! 'proprio' == $product->shipping ? 'selected' : '' !!}--}}>Envio próprio</option>
                                                    <option value="null" {{--{!! 'kapsula' == $product->shipping ? 'selected' : '' !!}--}} disabled='disabled'>Kapsula (em breve)</option>
                                                    <option value="null" {{--{!! 'hubsmart' == $product->shipping ? 'selected' : '' !!}--}}disabled='disabled'>Hubsmart (em breve)</option>
                                                    <option value="null" {{--{!! 'cosmarca' == $product->shipping ? 'selected' : '' !!}--}}disabled='disabled'>Cosmarca (em breve)</option>
                                                    <option value="null" {{--{!! 'nutreno' == $product->shipping ? 'selected' : '' !!}--}}disabled='disabled'>Nutreno (em breve)</option>
                                                    <option value="null" {{--{!! 'nutracaps' == $product->shipping ? 'selected' : '' !!}--}}disabled='disabled'>Nutracaps (em breve)</option>
                                                    <option value="null" {{--{!! 'biosupra' == $product->shipping ? 'selected' : '' !!}--}}disabled='disabled'>Biosupra (em breve)</option>
                                                </select>
                                            </div>
                                            <div id="div_carrier_id" class="form-group col-lg-6" style="display: none">
                                                <label for="id_shipping">ID na Transportadora:</label>
                                                <input type="text" class="input-pad" id="carrier_id" placeholder="ID do seu produto na transportadora" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-12 text-right btnSave">
                                                <button type="submit" class="btn btn-success">Atualizar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Modal padrão para excluir -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog  modal-dialog-centered  modal-simple">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="close-modal-delete">
                                <i class="material-icons md-16">close</i>
                            </a>
                        </div>
                        <div id="modal_excluir_body" class="modal-body text-center p-20">
                            <div class="d-flex justify-content-center">
                                <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                            </div>
                            <h3 class="black"> Você tem certeza? </h3>
                            <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-center">
                            <button id='bt_cancelar' type="button" class="btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                            <button id="bt_excluir" type="button" class="btn btn-danger" style="width: 20%;">Excluir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{!! asset('modules/products/js/products.js') !!}"></script>
        <script src="{!! asset('modules/global/adminremark2/global/vendor/dropify/dropify.min.js') !!}"></script>
        <script src="{!! asset('modules/global/adminremark2/global/js/Plugin/dropify.js') !!}"></script>
    @endpush

@endsection
