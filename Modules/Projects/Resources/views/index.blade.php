@extends("layouts.master")
@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <a href="/projects/create" class="btn btn-floating btn-danger" id="btn-add-project"
               style="position: relative; float: right; display:none" title='Adicionar projeto'>
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
        @include('projects::empty')
    </div>

    @push('scripts')
        <script src="{{asset('modules/projects/js/index.js?v=4')}}"></script>
    @endpush

@endsection

