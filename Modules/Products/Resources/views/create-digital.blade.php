@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
        <link rel="stylesheet" href="{{ asset('/modules/products/css/create.css?v=01') }}">
    @endpush

    <!-- Page -->
    <div class="page" style="display: none">
        <div style="display: none" class="page-header container">
            <h1 class="page-title">Novo produto digital</h1>
            <p class="desc mt-10 text-muted"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container">
            <form id='my-form-add-product'>
                <div class="panel px-40 p-30" data-plugin="matchHeight" style="border-radius: 16px">
                    <div class="row justify-content-between align-items-baseline">
                        <div class="col-4">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="digital_product_url">Imagem do produto</label>
                                    <input type="file" id="digital_product_url" name="digital_product_url" data-height="300" data-max-width="300">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPEG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o nome">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                    <small class="text-muted"> Máximo 30 caracteres. </small>
                                </div>

                                <div class='form-group col-12 div-expiration-time'>
                                    <label for="url_expiration_time">Tempo de expiração da url (em horas)</label>
                                    <div class="d-flex input-group">
                                        <input type="text" min="0" class="form-control" name="url_expiration_time" id="url_expiration_time" placeholder="Tempo de expiração da url em horas" maxlength='5' data-mask="0#" >
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="product">Arquivo do produto</label>
                                    <input type="file" id="digital_product" name="digital_product">
                                    <small class="text-center text-muted mt-15">Este é o arquivo que será enviado ao cliente após a confirmação da venda.</small>
                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="form-group row float-right">
                    <a type="button" class="btn btn-light btn-lg" href="/products">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Tudo certo!</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/products/js/create.js?v=' . random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
