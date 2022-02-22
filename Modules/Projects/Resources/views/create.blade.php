@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=' . versionsFile()) }}">
        <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css?v=' . versionsFile()) }}">
        <link rel="stylesheet" href="{{ asset('modules/projects/css/create.css?v=' . versionsFile()) }}">
    @endpush

    <div class="page" style="display: none; margin-bottom: 0 !important;">
        <div id="card-project">
            <div style="display: none" class="page-header container">
                <h1 class="page-title my-10" style="min-height: 28px">
                    <a href="/projects">
                        <span class="o-arrow-right-1 font-size-30 ml-2" aria-hidden="true"></span>
                        Cadastrar novo projeto
                    </a>
                </h1>
            </div>
            <div class="page-content container">
                <form id='form-create-project'>
                    <div class="panel px-40 p-20" data-plugin="matchHeight" style="border-radius: 16px">
                        <div class="row justify-content-between align-items-baseline">
                            <div class="form-group col-12 col-lg-5 col-xl-4">
                                <div class="d-flex flex-column" id="div_img" style="position: relative">
                                    <div class="d-flex flex-column" id="div_digital_product_upload">
                                        <label for="product_photo">Imagem do projeto</label>
                                        <input type="file" id="product_photo" name="photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                        <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px (1:1).</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-7 col-xl-8">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="name">Nome do projeto</label>
                                        <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o nome">
                                    </div>

                                    <div class='form-group col-12'>
                                        <label for='company'>Empresa</label>
                                        <select name='company' class='sirius-select' id='company' required>
                                            <option value=''>Selecione</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="description">Descrição</label>
                                        <textarea style="height: 140px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row pr-15 form-buttons pb-30">
                        <a type="button" class="btn btn-cancelar" href="/projects">Cancelar</a>

                        <button id="btn-save" type="submit" class="btn btn-primary btn-lg ml-15">
                            <img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div id="empty-companies-error" style="display:none;position: absolute; top: 25%;">
            <div class="text-center" style="display:flex;color: black;align-content: center;align-items: center;justify-content: center;flex-direction: column;text-align: center;padding: 20px;">
                <img src="{!! asset('modules/global/img/empty.svg') !!}" width="250px">
                <p class="desc gray" style='font-size:20px;'>Para criar um projeto você precisa ter pelo menos uma empresa aprovada para transacionar
                    e todos os documentos da empresa e do seu perfil precisam estar aprovados! </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/projects/js/create.js?v=' . versionsFile()) }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js?v=' . versionsFile()) }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js?v=' . versionsFile()) }}"></script>
    @endpush


@endsection
