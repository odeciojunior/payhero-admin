@extends("layouts.master")
@section('content')
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Ferramentas</h1>
        </div>
        <div class="page-content container">
            <div class="row">
{{--                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">--}}
{{--                    <div class="card" onclick="window.location.href='/collaborators'" style='width:300px;'>--}}
{{--                        <a id="notazz-bt" href="/collaborators" class="add-btn">--}}
{{--                            <i id="notazz-icon" class="icon wb-plus" aria-hidden="true"></i></a>--}}
{{--                        <img class="card-img-top mt-50" src="{!! asset('modules/global/img/svg/collaboration.svg') !!}" alt="" height='150px'>--}}
{{--                        <div class="card-body">--}}
{{--                            <h5 class="card-title">Colaboradores</h5>--}}
{{--                            <p class="card-text sm">Cadastre colaboradores para ajudar a gerenciar sua conta</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                    <div class="card" onclick="window.location.href='/integrations'" style='width:300px;'>
                        <a id="tool_integrations-bt" href="/integrations" class="add-btn">
                            <i id="tool_integrations-icon" class="icon wb-plus" aria-hidden="true"></i></a>
                        <img class="card-img-top mt-50" src="{!! asset('modules/global/img/svg/api.svg') !!}" alt="" height='150px'>
                        <div class="card-body">
                            <h5 class="card-title">Integrações</h5>
                            <p class="card-text sm">Crie chaves de acesso para apps de terceiros</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>
@endsection
@push('scripts')
        <script src="{{ asset('modules/tools/js/index.js?v=1') }}"></script>
@endpush
