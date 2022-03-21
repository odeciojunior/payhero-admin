@extends("layouts.master")
@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/projects/index.min.css') }}">
        <style>
            .card{
                min-height:403px;
            }
            .card-title{
                min-height:56px;
            }
        </style>
    @endpush
    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">

                <div class="col-8">
                    <h1 class="page-title">Minhas Lojas</h1>
                    <div class="page-header-actions"></div>
                </div>
                
                <div class="col-4 text-right">

                    <div class="row align-items-center justify-content-end">

                        <div class="mr-30">
                            <label class="switch mr-3">
                                <input type="checkbox" class='check' value='{{auth()->user()->deleted_project_filter}}' name="deleted_project_filter" id="deleted_project_filter">
                                <span class="slider round"></span>
                            </label>
                            <label for='deleted_project_filter' class="mt-3"><b>Ver excluídas</b></label>
                        </div>
                        

                        <a href="/projects/create" class="btn btn-floating btn-primary" id="btn-add-project" style="position: relative; float: right; display:none" title='Adicionar loja'>
                            <span style="color: white; font-size: 35px" class='o-add-1'></span>
                        </a>

                    </div>
                    
                </div>
            </div>
        </div>

        @include('projects::empty')
        @include('projects::empty-company')
        <div class="page-content container" style="padding-top: 0">
            <div id="data-table-projects" class="row" style="margin-top: 0">
            </div>
        </div>

    </div>
    <!-- End Modal -->
    @push('scripts')
        <script src="{{ mix('build/layouts/projects/index.min.js') }}"></script>
    @endpush
@endsection
