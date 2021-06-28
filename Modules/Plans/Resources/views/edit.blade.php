<style>
    .mb-3{margin-bottom: 1rem!important;}
    .btn{border-width: 1px !important;}
    .nav-tabs .nav-link:not(.active) {    
        border-bottom-color: #c3c3c3;
    }
    .nav-tabs .nav-link {        
        background-color: #ffffff;     
    }
    .btn-plus{margin-top: 28px;padding-top: 10px;padding-bottom: 10px;}
    .btn-active{background-color: #2E85EC;color:white;border: 1px solid #2E85EC;}
    .btn-active svg path{fill:#ffffff;}
    .btn-edit{background-color: #F4F4F4; border:1px solid #f4f4f4;}
    .btn-edit svg path{fill:#2E85EC;}
    .btn-edit-row{background-color: #2E85EC;color:white;border: 1px solid #2E85EC;}
    .btn-edit-row svg path{fill:#2E85EC;}
    .modal-footer{justify-content:center;}
    .btn-update-config-custom{background-color: #2E85EC;color:white;border: 1px solid #2E85EC;}
    .btn-update-config-custom:hover{background-color: #2E85EC;border: 1px solid #2E85EC;}
    .mb-4p{margin-bottom: 4%;}
    .mt-15{margin-top: 15px;}
    .pl-10{padding-left: 10px;}
    #form-update-plan .nav-link{font-size: 16px !important;}
    .btnDelete[readonly] span{color:#333333; -webkit-text-stroke: 0.6px #333333;}
    .btnDelete[readonly]{border-color: #6c757d;}
    .form-control:disabled, .form-control[readonly] {background-color: #F4F4F4;}
    .remove-custom-product{height: 43px;}
    .border-light-gray{border:1px solid #f4f4f4; background-color:#f4f4f4; height: 44px;}
    .btn-outline-secondary{height: 44px;border:1px solid #C4C4C4;}
    .btn-plus{color: #41DC8F;background-color: transparent;border-color: #41DC8F;}    
    .btn-plus:disabled{color: #333333;background-color: transparent;border-color: #333333;}    
    .btn-plus:disabled svg path{stroke:#333333;}
    .btn-plus:hover{background-color:transparent;}   
    .modal-content{border-radius: 12px;} 
    .card img{border: 1px solid #C4C4C4;
    border-radius: 8px;}
    .nav-tabs .nav-link:not(.active) {margin: 0px !important;}
    .nav-tabs .nav-link {margin: 0px !important;}
    .edit-input{background-color: #F4F4F4;}
    .edit-input:focus{background-color: #FFFFFF;}
</style>
<div class="container-fluid px-0" id="form-update-plan" style="display:none">
    <div class="nav-tabs-horizontal">
        <div class="nav nav-tabs nav-tabs-line text-center" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-geral-tab" data-toggle="tab" href="#nav-geral"
                role="tab" style="width:50%;"><strong>Dados gerais</strong>
            </a>
            <a class="nav-item nav-link" id="nav-custom-tab" data-toggle="tab" href="#nav-custom"  ng-click="init()"
                role="tab" style="width:50%;"><strong>Personalizações</strong>
            </a>
        </div>
    </div>
    <div class="tab-content p-15" id="nav-tabContent">                    
        <div class="tab-pane fade show active" id="nav-geral" role="tabpanel" aria-labelledby="nav-geral-tab">
            <div class="card container">
                <form id="form-update-plan-tab-1" method="PUT" action="/plans" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    {{--    <input type="hidden" value="{{Hashids::encode($plan->id)}}" name="id">--}}
                    <input type="hidden" value="" name="id" id='plan_id'>
                    <div class="panel" data-plugin="matchHeight">
                        <h4 class='mt-0'> Dados gerais </h4>
                        <div style="width:100%">
                            <div class="row mt-2">
                                <div class="form-group col-md-6 col-lg-6">
                                    <label for="name">Nome</label>
                                    <input value="" name="name" type="text" class="form-control" id="plan-name_edit" placeholder="Nome" maxlength='50' required>
                                </div>
                                <div class="form-group col-md-6 col-lg-6">
                                    <label for="price">Preço</label>
                                    <input value="" name="price" type="text" class="form-control" id="plan-price_edit" placeholder="Preço" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="description">Descrição</label>
                                    <input value="" name="description" type="text" class="form-control" id="plan-description_edit" maxlength='50' placeholder="Descrição">
                                </div>
                            </div>
                            <hr class='display-lg-none display-xlg-none'>
                            <div id="products" class='products_row_edit'>
                                    {{-- carregado no js--}}
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <button type="button" id="add_product_plan" class="btn btn-primary col-12 add_product_plan_edit">Adicionar produto</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <div class="tab-pane fade" id="nav-custom" role="tabpanel" aria-labelledby="nav-custom-tab" >
            <form id="form-update-plan-tab-2" method="POST" action="/plans/config-custom-product" enctype="multipart/form-data">
                @csrf                
                @method('POST')
                <div class="row">
                    <div id="custom_products" class='col-md-12 products_row_custom'>
                        {{-- carregado no js--}}
                    </div>
                    <div id="custom_products_checkbox" class="card container "></div>
                </div>
            </form>
        </div>
    </div>
</div>