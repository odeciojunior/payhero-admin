@extends("layouts.master")

@section('content')

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
            @if($companies->count() > 0)
                <div class="panel pt-30 p-30" data-plugin="matchHeight">
                    <form method="POST" action="/projects" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <h4> Dados gerais </h4>
                        <div class='row'>
                            <div class='col-md-6'>
                                <input name='project_photo' type='file' class='form-control' id='project-photo' style="display:none">
                                <div style="margin: 20px 0 0 30px;">
                                    <label for='preview-image-project'>Selecione a foto do projeto</label>
                                    <img id='preview-image-project' alt='Selecione a foto do projeto' src="{{asset('modules/projects/img/projeto.png')}}" style="max-height: 300px; max-width: 300px;">
                                </div>
                                <input type='hidden' name='photo_x1'/> <input type='hidden' name='photo_y1'/>
                                <input type='hidden' name='photo_w'/> <input type='hidden' name='photo_h'/>
                            </div>
                            <div class='col-md-6'>
                                <div class='form-group col-xl-12' style="margin-top: 30px">
                                    <label for='name'>Nome</label>
                                    <input name='name' type='text' class='form-controll' id='name' placeholder='Nome do projeto' required>
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback d-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class='form-group col-xl-12'>
                                    <label for='company'>Empresa</label>
                                    <select name='company' class='form-control' id='company' required>
                                        <option value=''>Selecione</option>
                                        @foreach($companies as $company)
                                            <option value='{{Hashids::encode($company->id)}}'>{{$company->fantasy_name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('company'))
                                        <div class="invalid-feedback d-block">
                                            <strong>{{ $errors->first('company') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class='form-group col-xl-12'>
                                    <label for='description'>Descrição</label>
                                    <textarea name='description' class='form-control' id='description' placeholder='Descrição' rows='4' cols='50'></textarea>
                                    @if ($errors->has('description'))
                                        <div class="invalid-feedback d-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <input type='hidden' value='private' name='visibility'>
                                <input type='hidden' value='1' name='status'>
                            </div>
                        </div>
                        <div class="row" style="margin: 30px 10px 0 0 ">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-success">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
                @endpush

                <div class="content-error d-flex text-center">        
                    <img src="{!! asset('modules/global/assets/img/emptyprojetos.svg') !!}" width="250px">
                    <h1 class="big gray">Você ainda não tem nenhuma empresa!</h1>
                    <p class="desc gray">Vamos cadastrar a primeira empresa? </p>
                    <a href="/companies/create" class="btn btn-primary gradient">Cadastrar empresa</a>
                </div>
            @endif
        </div>
    </div>

@push('scripts')
    <script src="{!! asset('modules/projects/js/create.js') !!}"></script>
@endpush


@endsection
