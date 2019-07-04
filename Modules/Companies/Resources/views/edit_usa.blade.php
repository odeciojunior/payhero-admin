@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.scss')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
@endpush
@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Edit Company</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.index')}}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Back
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="card shadow" data-plugin="matchHeight">
                <div class="example-wrap">
                    <div class="nav-tabs-horizontal nav-tabs-line pt-15" data-plugin="tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation" id='nav_users'>
                                <a class="nav-link active" data-toggle="tab" href="#tab_user" aria-controls="tab_user" role="tab">Company
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" id='nav_bank_data'>
                                <a class="nav-link" data-toggle="tab" href="#tab_bank_data" aria-controls="tab_bank_data" role="tab">Bank Accounts
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" id="nav_documents">
                                <a class="nav-link" data-toggle="tab" href="#tab_documentos"
                                   aria-controls="tab_documentos" role="tab">Documents
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content pt-10 pr-30 pl-30">
                        <div class="tab-pane active" id="tab_user" role="tabpanel">
                            <form method="POST" action="{!! route('companies.update', ['id' => $company->id_code]) !!}" enctype="multipart/form-data" id='company_update_form'>
                                @csrf
                                @method('PUT')
                                <h3 class="mb-15 mt-10">Basic information</h3>
                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label for="fantasy_name">Fantasy Name</label>
                                        <input name="fantasy_name" value="{!! $company->fantasy_name !!}" type="text" class="input-pad" id="fantasy_name" placeholder='Fantasy Name'>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="company_document">Company Document</label>
                                        <input name="company_document" value="{!! $company->company_document !!}" type="text" class="input-pad" id="company_document" placeholder='Company Document'>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="business_website">Site</label>
                                        <input name="business_website" value="{!! $company->business_website !!}" type="text" class="input-pad" id="business_website" placeholder='Site'>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label for="support_email">E-mail</label>
                                        <input name="support_email" value="{!! $company->support_email !!}" type="text" class="input-pad" id="support_email" placeholder='E-mail'>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="support_telephone">Telephone</label>
                                        <input name="support_telephone" value="{!! $company->support_telephone !!}" type="text" class="input-pad" id="support_telephone" placeholder='Telephone'>
                                    </div>
                                </div>
                                <h3 class="mb-15">Aditional information</h3>
                                <div class="row">
                                    <div class="form-group col-xl-2">
                                        <label for="zip_code">Zipcode</label>
                                        <input name="zip_code" value="{!! $company->zip_code !!}" type="text" class="input-pad" id="zip_code" placeholder='Zipcode'>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xl-5">
                                        <label for="street">Street</label>
                                        <input name="street" value="{!! $company->street !!}" type="text" class="input-pad" id="street" placeholder='Street'>
                                    </div>
                                    <div class="form-group col-xl-2">
                                        <label for="number">Number</label>
                                        <input name="number" value="{!! $company->number !!}" type="text" class="input-pad" id="number" placeholder='Number'>
                                    </div>
                                    <div class="form-group col-xl-5">
                                        <label for="neighborhood">Neighborhood</label>
                                        <input name="neighborhood" value="{!! $company->neighborhood !!}" type="text" class="input-pad" id="neighborhood" placeholder='Neighborhood'>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="complement">Complement</label>
                                        <input name="complement" value="{!! $company->complement !!}" type="text" class="input-pad" id="complement" placeholder='Complement'>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="state">State</label>
                                        <input name="state" value="{!! $company->state !!}" type="text" class="input-pad" id="state" placeholder='State'>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label for="city">City</label>
                                        <input name="city" value="{!! $company->city !!}" type="text" class="input-pad" id="city" placeholder='City'>
                                    </div>
                                    {{--<div class="form-group col-xl-6">--}}
                                    {{--<label for="country">Country</label>--}}
                                    {{--<input name="country" value="{!! $company->country !!}" type="text" class="input-pad" id="country">--}}
                                    {{--</div>--}}
                                </div>
                                <div class="form-group text-right">
                                    <input id="update_profile" type="submit" class="btn btn-success" value="Update" style="width: auto;">
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab_bank_data" role="tabpanel">
                            <form method="POST" action="{!! route('companies.update', ['id' => $company->id_code]) !!}" enctype="multipart/form-data" id='company_bank_update_form'>
                                @csrf
                                @method('PUT')
                                <h3 class="mb-15 mt-10">Bank Account</h3>
                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label>Routing Number</label>
                                        <input id="routing_number" name="bank" type="text" value="{!! $company->bank !!}" class="input-pad" placeholder="Routing Number">
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label>Bank</label>
                                        <input id="bank" type="text" name="bank_name" class="input-pad" placeholder="Bank" disabled>
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label>Account number</label>
                                        <input name="account_digit" type="text" value="{!! $company->account_digit !!}" class="input-pad" placeholder="Account number">
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <input id="update_profile" type="submit" class="btn btn-success" value="Save Account" style="width: auto;">
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab_documentos" role="tabpanel">
                            <h3 class="mb-15 mt-10">Documents</h3>
                            <div class="row mt-15">
                                <div class="col-lg-6">
                                    <p class="mb-10"> Send a bank statement, proof of residence and the company's contract</p>
                                    <div id="dropzone drop-empresas">
                                        <form method="POST" action="{!! route('companies.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                            @csrf
                                            <div class="dz-message needsclick">
                                                Drag the files here or click to select.<br/>
                                            </div>
                                            <input id="company_id" name="company_id" value="{{$company->id_code}}" type="hidden" class="input-pad">
                                            <input id="document_type" name="document_type" value="" type="hidden" class="input-pad">
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table table-striped ">
                                        <tbody>
                                            <tr class="text-center">
                                                <td>
                                                    Bank statement
                                                </td>
                                                <td id='td_bank_status'>
                                                    {!! $company->bank_document_translate !!}
                                                </td>
                                            </tr>
                                            <tr class='text-center'>
                                                <td>
                                                    Proof of address
                                                </td>
                                                <td id='td_address_status'>
                                                    {!! $company->address_document_translate !!}
                                                </td>
                                            </tr>
                                            <tr class='text-center'>
                                                <td>
                                                    Company Contract
                                                </td>
                                                <td id='td_contract_status'>
                                                    {!! $company->contract_document_translate !!}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/companies/js/edit.js')}}"></script>
    @endpush
@endsection


