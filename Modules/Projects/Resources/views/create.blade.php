@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
    @endpush

    <div class="page">
        <div class="page-header container">
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
                                 src="{{asset('modules/global/img/projeto.png')}}" style="cursor:pointer;">
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
            @include('companies::empty')
        </div>
    </div>

    @push('scripts')
        <script src="{!! asset('modules/projects/js/create.js?v=1') !!}"></script>
    @endpush


@endsection
