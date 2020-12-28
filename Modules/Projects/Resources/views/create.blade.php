@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
    @endpush

    <div class="page" style="display: none">
        <div style="display: none" class="page-header container">
            <h1 class="page-title">Cadastrar novo projeto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/projects">
                    Meus projetos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div id="card-project" class="card shadow pt-30 p-30" data-plugin="matchHeight">
                <form id="form-create-project">
                    @csrf
                    <div class='row'>
                        <div class='col-md-4 col-sm-12'>
                            <input name='photo-main' type='file' class='form-control' id='project-photo'
                                   style="display:none">
                            <label for='preview-image-project'>Selecione a foto do projeto</label>
                            <br>
                            <img id="preview-image-project" alt='Selecione a foto do projeto' class='img-fluid mb-sm-2'
                                 src="{{asset('modules/global/img/projeto.svg')}}" style="cursor:pointer;">
                            <br> <input type='hidden' name='photo_x1'/> <input type='hidden' name='photo_y1'/>
                            <input type='hidden' name='photo_w'/> <input type='hidden' name='photo_h'/>
                        </div>
                        <div class='col-md-8 col-sm-12'>
                            <div class='form-group'>
                                <label for='name'>Nome</label>
                                <input name='name' type='text' class='form-controll' id='name'
                                       placeholder='Nome do projeto' maxlength='40' required>
                            </div>
                            <div class='form-group'>
                                <label for='company'>Empresa</label>
                                <select name='company' class='form-control select-pad' id='company' required>
                                    <option value=''>Selecione</option>
                                </select>
                            </div>
                            <div class='form-group'>
                                <label for='description'>Descrição</label>
                                <textarea name='description' class='form-control select-pad' id='description'
                                          placeholder='Descrição' rows='5' cols='50' maxlength='100'></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-right">
                            <button id="btn-save" type="button" class="btn btn-success">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id='modal-not-approved-document-companies' class='modal fade' aria-labelledby='modal-invite' role='dialog'
                 tabindex='-1' style='padding-right: 12px;'>
                <div class='modal-dialog modal-simple'>
                    <div class='modal-content text-center'>
                        <div class='modal-body text-center'>
                            <div class='swal2-icon swal2-error swal2-animate-error-icon' style='display:flex;'>
                            <span class='swal2-x-mark'>
                                <span class='swal2-x-mark-line-left'></span>
                                <span class='swal2-x-mark-line-right'></span>
                            </span>
                            </div>
                            <h3 align='center'>
                                Para criar um projeto você precisa ter pelo menos uma empresa aprovada para transacionar
                                e todos os documentos da empresa e do seu perfil precisam estar aprovados!
                            </h3>
                        </div>
                        <div class='modal-footer'>
                            <div style='width:100%; text-align: center; padding-top: 3%;'>
                                <span class='btn btn-primary' data-dismiss='modal' style='font-size: 25px;'>
                                Retornar
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center" id="empty-companies-error" style="display:none;color: black;display: flex;align-content: center;align-items: center;justify-content: center;flex-direction: column;text-align: center;padding: 20px;">
            <img src="{!! asset('modules/global/img/emptyempresas.svg') !!}" width="250px">
{{--            <h4 class="big gray">Para criar um projeto você precisa ter pelo menos uma empresa aprovada para transacionar--}}
{{--                e todos os documentos da empresa e do seu perfil precisam estar aprovados! </h4>--}}
            <p class="desc gray" style='font-size:20px;'>Para criar um projeto você precisa ter pelo menos uma empresa aprovada para transacionar
                e todos os documentos da empresa e do seu perfil precisam estar aprovados! </p>
{{--            <a href="/companies/create" class="btn btn-primary">Cadastrar empresa</a>--}}
        </div>
    </div>

    @push('scripts')
        <script src="{!! asset('modules/projects/js/create.js?v='.uniqid()) !!}"></script>
    @endpush


@endsection
