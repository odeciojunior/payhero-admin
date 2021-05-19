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
                <div class="panel px-40 p-20" data-plugin="matchHeight" style="border-radius: 16px">
                    <div class="row justify-content-between align-items-baseline">
                        <div class="col-12 col-md-4">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="product_photo">Imagem do produto</label>
                                    <input type="file" id="product_photo" name="product_photo" data-height="650" data-max-width="650">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
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

                                <div class="form-group col-12 col-xl-8 input-file-container">  
                                    <label for="digital_product_url">Arquivo do produto</label>
                                    <input class="input-file" name="digital_product_url" id="digital_product_url" type="file">
                                    <div>
                                        <label tabindex="0" for="digital_product_url" class="input-file-trigger px-10">Escolher arquivos</label>
                                        <span id="file_return" class="ml-20"></span>
                                    </div>
                                    <small class="text-center text-muted mt-15">Este é o arquivo que será enviado ao cliente após a confirmação da venda.</small>
                                </div>

                                <div class="col-12 col-xl-4 div-expiration-time">
                                    <label for="url_expiration_time">Tempo de expiração da url</label>
                                    <div class="input-group">
                                        <input name="url_expiration_time" type="text" class="form-control" id="url_expiration_time" min="0" maxlength="5" data-mask="0#">
                                        <div class="input-group-append">
                                            <span class="input-group-text">HORA</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="form-group row float-right pr-15">
                    <a type="button" class="btn btn-light btn-lg" href="/products">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Tudo certo!</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/products/js/create-digital.js?v=' . random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
