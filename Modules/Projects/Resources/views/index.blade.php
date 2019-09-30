@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <a href="/projects/create" class="btn btn-floating btn-danger" id="btn-add-project"
               style="position: relative; float: right; display:none">
                <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
            </a>
            <h1 class="page-title">Meus projetos</h1>
            <div class="page-header-actions">
            </div>
        </div>
        <div class="page-content container">
            <div id="data-table-projects" class="row" style="margin-top: 30px">
            </div>
        </div>
        <div class="content-error text-center" style="display:none">
            <img src="{!! asset('modules/global/img/emptyprojetos.svg') !!}" width="250px">
            <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
            <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
            <a href="/projects/create" class="btn btn-primary gradient">Cadastrar primeiro projeto</a>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('modules/projects/js/index.js')}}"></script>
    @endpush

@endsection

