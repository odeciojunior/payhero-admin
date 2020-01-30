@extends('affiliates::layouts.master')

@section('content')
    <div class='page'>
        <div class="page-header container">
            {{--            <h1 class="page-title">Perfil</h1>--}}
            <h1 class='page-title project-header'></h1>
            <p class="card-text sm mt-10 mx-5" id="created_by"></p>
        </div>
        <div class="page-content container">
            <div class="card shadow p-30">
                <div class='row'>
                    <div class='col-md-12'>
                        {{--                        <h1 class='project-header'></h1>--}}
                        <div class='d-flex'>
                            <div class=''>
                                <img class='project-image img-fluid'>
                            </div>
                            <div class='mx-20'>
                                <p>Comissão de até: 1%</p>
                                <button id='btn-affiliation-request' class='btn btn-primary'>Solicitar Filiação</button>
                            </div>
                        </div>
                        <div class="nav-tabs-horizontal mt-20" data-plugin="tabs">
                            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                                <li class="nav-item" role="presentation" id='nav_users'>
                                    <a class="nav-link active" data-toggle="tab" href="#tab_affiliation" aria-controls="tab_affiliation" role="tab">Afiliação
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="nav_documents">
                                    <a class="nav-link" data-toggle="tab" href="#tab_about" aria-controls="tab_about" role="tab">
                                        Sobre
                                    </a>
                                </li>
                                {{--                                <li class="nav-item" role="presentation" id="nav_taxs">--}}
                                {{--                                    <a class="nav-link" data-toggle="tab" href="#tab_taxs" aria-controls="tab_taxs" role="tab">--}}
                                {{--                                        Tarifas e Prazos--}}
                                {{--                                    </a>--}}
                                {{--                                </li>--}}
                            </ul>
                            <div class="p-30 pt-20">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab_affiliation" role="tabpanel">
                                        <p class='text-affiliation'></p>
                                    </div>
                                    <div class="tab-pane fade" id="tab_about" role="tabpanel">
                                        <p class='text-about-project'></p>
                                    </div>
                                    {{--                                    <div class='tab-pane fade' id='tab_taxs' role='tabpanel'>--}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('modules/affiliates/js/index.js') }}"></script>
    @endpush
@endsection
