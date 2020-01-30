@extends('affiliates::layouts.master')

@section('content')
    <div>
        <div class="page-content container col-md-6" style='display:none;'>
            <div class="card shadow p-30">
                <div class='row'>
                    <div class='col-md-12'>
                        {{--                        <h1 class='project-header'></h1>--}}
                        <div class='row mx-10'>
                            <div class='col-md-5'>
                                <h1 class='page-title project-header'></h1>
                                <p class="card-text sm mt-10 mx-5" id="created_by"></p>
                                <img class='project-image img-fluid rounded'>
                            </div>
                            <div class='col-md-7 mt-md-70 mt-sm-20 mt-20'>
                                <p class='text-about-project text-center'></p>
                            </div>
                        </div>
                        <div class="nav-tabs-horizontal mt-20" data-plugin="tabs">
                            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                                <li class="nav-item" role="presentation" id='nav_users'>
                                    <a class="nav-link active" data-toggle="tab" href="#tab_terms" aria-controls="tab_terms" role="tab">Termos de afiliação
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
                                    <div class="tab-pane fade show active" id="tab_terms" role="tabpanel">
                                        <p class='text-terms'></p>
                                    </div>
                                    <div class="tab-pane fade" id="tab_about" role="tabpanel">
                                        <p class='text-about-project'></p>
                                        <p class='percentage-affiliate'></p>
                                    </div>
                                    {{--                                    <div class='tab-pane fade' id='tab_taxs' role='tabpanel'>--}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                        <div class='col-md-12 my-10 text-right'>
                            <button id='btn-affiliation-request' class='btn btn-primary'>Solicitar Afiliação</button>
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
