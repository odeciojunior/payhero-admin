@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Cadastrar nova empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.index')}}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div id='div1' class="page-content container" style='display:none;'>
            <form id='create_form' method="post" action="{{route('api.companies.store')}}">
                @csrf
                @method('POST')
                <div class="card shadow p-30" data-plugin="matchHeight">
                    <div class="form-group col-3">
                        <label for="country">País da empresa</label>
                        <select id="country" name='country' class="form-control select-pad">
                            <option value="brazil">Brasil</option>
                            <option value="usa" disabled>United States</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-6">
                        <label id='fantay_name_label'>Razão Social</label>
                        <input name="fantasy_name" type="text" class="input-pad fantasy_name_1" id="fantasy_name" placeholder="Razão Social" maxlength='40'>
                    </div>
                    <div id='div-company-document' class="form-group col-xl-6" style='display:none;'>
                        <label id='company_document_label' for='company_document'>CPF/CNPJ</label>
                        <input name="company_document" type="text" class="input-pad company_document_1" id="company_document" placeholder="">
                    </div>
                    <div id="store_form" style="width:100%">
                        {{--                        @include('companies::create_brazilian_company')--}}
                    </div>
                    <div class="form-group col-xl-4">
                        <button class="form-control btn btn-success btn-next-div1" type='submit'>
                            Proximo <i class='icon wb-chevron-right-mini' aria-hidden='true'></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div id='div2' class="page-content container" style='display:none;'>
            <form id='create_form' method="post" action="{{route('api.companies.store')}}">
                @csrf
                @method('POST')
                <div class="card shadow p-30" data-plugin="matchHeight">
                    <div id='text-main' class='row justify-content-center text-center'>
                        <h3 class="bold">
                            Você gostaria de utilizar a CloudFox para receber pagamentos para o seu negócio como...
                        </h3>
                    </div>
                    <div id='text-company' class='row justify-content-center text-center' style='display:none;'>
                        <h3 class="bold">
                            Precisamos saber um pouco mais da sua empresa...
                        </h3>
                    </div>
                    <div class='row justify-content-center text-center mt-60'>
                        <div class='col-lg-6'>
                            <button id='btn-physical-person' class='btn btn-info' data-type='physical person'>Pessoa fisíca</button>
                        </div>
                        <div class='col-lg-6'>
                            <button id='btn-juridical-person' class='btn btn-info' data-type='juridical person'>Pessoa jurídica</button>
                        </div>
                    </div>
                    <div id='div-juridical-person' class='mt-40' style='display:none;'>
                        <div class='row'>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="country">País da empresa</label>
                                    <select id="country" name='country' class="form-control select-pad">
                                        <option value="brazil">Brasil</option>
                                        <option value="usa" disabled>United States</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-holder d-flex flex-column">
                                    <label for="company_document">CPNJ</label>
                                    <input type="text" name="company_document" class="input-pad company_document_2" id="company_document" placeholder="Digite seu CNPJ">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-holder d-flex flex-column">
                                    <label for="fantasy_name">Razão social</label>
                                    <input type="text" name="company_document" class="input-pad fantasy_name_2" id="fantasy_name" placeholder="Digite a Razão social">
                                </div>
                            </div>
                            <div class="col-lg-4 mt-30">
                                <button class="form-control btn btn-success btn-next-div2" type='submit'>
                                    Proximo <i class='icon wb-chevron-right-mini' aria-hidden='true'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/companies/js/create.js?v=3') }}"></script>
    @endpush

@endsection

