@extends("layouts.master")

@section('content')

    @push('css')
        {{-- <link rel="stylesheet" href="{!! asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
        <link rel="stylesheet" href="{{ mix('modules/products/css/create.min.css') }}"> --}}
        <link rel="stylesheet" href="{{ mix('build/layouts/products/create.min.css') }}">
    @endpush

    <!-- Page -->
    <div class="page p-50" style="margin-bottom: 0 !important; display:none !important;">
        <div class="page-header container">
            <h1 class="page-title" style="color: #707070;">Novo produto digital</h1>
            <p class="desc mt-10 text-muted"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container pb-20">
            <form id='my-form-add-product'>
                <div class="panel px-40 p-20" data-plugin="matchHeight" style="border-radius: 16px">
                    <div class="row justify-content-between align-items-baseline">
                        <div class="form-group col-12 col-lg-5 col-xl-4">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="product_photo">Imagem do produto</label>
                                    <input type="file" id="product_photo" name="product_photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7 col-xl-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o nome">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                </div>

                                <div class="form-group col-12 col-md-7 input-file-container">
                                    <label>Arquivo do produto</label>
                                    <input class="input-file" name="digital_product_url" id="digital_product_url" type="file">
                                    <div class="form-group d-flex pt-10">
                                        <label tabindex="0" for="digital_product_url" class="input-file-trigger mb-0 px-15 py-5">Escolher arquivos</label>
                                        <span id="file_return" class="ml-20 pt-10"></span>
                                    </div>
                                    <small class="text-center text-muted mt-15">Este é o arquivo que será enviado ao cliente após a confirmação da venda.</small>
                                </div>

                                <div class="col-12 col-md-5 div-expiration-time">
                                    <label for="url_expiration_time">Expiração do link</label>
                                    <div class="form-group input-group">
                                        <input name="url_expiration_time" type="text" class="form-control" id="url_expiration_time" placeholder="Ex: 24" min="0" maxlength="3" data-mask="0#" value="24">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">HORA(S)</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="row pr-15 form-buttons">
                    <a type="button" class="btn btn-cancelar" href="/products">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="{{ mix('build/global/img/svg/check-all.svg') }}">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        {{-- <script src="{{ mix('modules/products/js/create-digital.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script> --}}
        <script src="{{ mix('build/layouts/products/create-digital.min.js') }}"></script>
    @endpush

@endsection
