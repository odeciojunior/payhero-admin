<div class="card card-body" style="margin-bottom: 25px; padding-bottom: 0;">
    <div class='row no-gutters mb-20'>
            
        
        <div class="top-holder text-right mb-0" style="width: 100%;">
                


            <div class="d-flex align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-name" name="discount-coupons" placeholder="Pesquisa por nome">
                        <span class="input-group-append" id="bt-search">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <img id="bt-search" src="/modules/global/img/icon-search_.svg">
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="d-flex justify-content-end">
                        {{-- <div id="config-cost-plan" class="btn-holder d-flex align-items-center pointer" style="padding-right: 10px; border-right: 1px solid #EDEDED; margin-top: -20px; margin-bottom: -20px; margin-right: 20px;">
                            <span class="link-button-dependent">Configurar custos </span>
                            <a class="rounded-add pointer bg-secondary">
                                <img src="http://dev.admin.com/modules/global/img/svg/settings.svg" height="22">
                            </a>
                        </div> --}}
                        <div id="add-coupon" class="btn-holder d-flex align-items-center pointer"
                        data-toggle="modal" data-target="#modal-create-coupon">
                            <span class="link-button-dependent blue">Adicionar </span>
                            <a class="ml-10 rounded-add pointer" style="display: inline-flex;">
                                <img src="/modules/global/img/icon-add.svg" style="width: 18px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>




            {{-- <div class="col-md-6 ">
                <div class="input-group" style="width: 472px">
                    <input style="border-radius: 8px 0px 0px 8px; height:48px !important; width: 421px !important;"
                        type="text" class="form-control" id="search-name" name="plan" placeholder="Pesquisar por nome">

                    <svg id="bt-search" class="pointer" width="49" height="48" viewBox="0 0 49 48" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0H41C45.4183 0 49 3.58172 49 8V40C49 44.4183 45.4183 48 41 48H0V0Z"
                            fill="#2E85EC" />
                        <path
                            d="M27.9633 27.0239C26.8085 27.9477 25.3438 28.5001 23.75 28.5001C20.0221 28.5001 17 25.478 17 21.75C17 18.0221 20.0221 15 23.75 15C27.4779 15 30.5 18.0221 30.5 21.75C30.5 23.3438 29.9477 24.8085 29.024 25.9633L34.7803 31.7197C35.0732 32.0126 35.0732 32.4874 34.7803 32.7803C34.4874 33.0732 34.0126 33.0732 33.7197 32.7803L27.9633 27.0239ZM29 21.75C29 18.8505 26.6495 16.5 23.75 16.5C20.8505 16.5 18.5 18.8505 18.5 21.75C18.5 24.6495 20.8505 27 23.75 27C26.6495 27 29 24.6495 29 21.75Z"
                            fill="white" />
                    </svg>

                </div>
            </div>
            <div class="col-md-3"></div> --}}
            
            {{-- <div id="add-coupon" class="p-0 col-md-3 add-coupon1 d-flex align-items-center justify-content-end pointer"
                data-toggle="modal" data-target="#modal-create-coupon">
                <span class="link-button-dependent red" style="color:#2E85EC;  font-weight: bold"> Adicionar</span>

                <svg style="margin-left:16px" width="46" height="46" viewBox="0 0 46 46" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect width="46" height="46" rx="8" fill="#2E85EC" />
                    <line x1="23" y1="15" x2="23" y2="33" stroke="white" stroke-width="2" stroke-linecap="round" />
                    <line x1="32" y1="24" x2="14" y2="24" stroke="white" stroke-width="2" stroke-linecap="round" />
                </svg>

            </div> --}}

            {{-- <div id="add-coupon" class="add-coupon1 btn-holder d-flex align-items-center pointer"
            data-toggle="modal" data-target="#modal-create-coupon">
                <span class="link-button-dependent blue">Adicionar </span>
                <a class="ml-10 rounded-add pointer" style="display: inline-flex;">
                    <img src="/modules/global/img/icon-add.svg" style="width: 18px;">
                </a>
            </div> --}}
        </div>
    </div>
    <div class="card shadow " style="margin: 0 -1.429rem;">
        <div style='min-height: 300px'>
            <div class='page-invoice-table table-responsive '>
                <table id='tabela-coupon' class='table text-left table-coupon table-striped unify' style='width:100%'>
                    <thead>
                        <tr style="background: #FBFBFB;">
                            <td class='table-title'>Categoria</td>
                            <td class='table-title'>Nome</td>
                            <td class='table-title'>Desconto de</td>
                            <td class='table-title'>Código</td>

                            <td class='table-title' style="text-align: center">Status</td>
                            <td class='table-title options-column-width text-right' style="width: 126px !important"></td>
                        </tr>
                    </thead>
                    <tbody id='data-table-coupon' class='min-row-height'>
                        {{-- js carregando dados --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-coupons" class="pagination-sm margin-chat-pagination text-right m-0">
        {{-- js carrega... --}}
    </ul>
</div>

<div id="coupon-modals">
    
    <input name="search_input_description_value" id="search_input_description_value" value="" type="hidden">

    <!-- Details -->
    <div id="modal-detail-coupon" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class="modal-header simple-border-bottom mb-10">
                    <h4 class="modal-title" id="modal-title">Detalhes do cupom</h4>
                    <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal"
                        aria-label="Close">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div class="modal-body" style='min-height: 100px'>
                    @include('discountcoupons::show')
                </div>
            </div>
        </div>
    </div>

    <!-- Create -->
    <div id="modal-create-coupon" class="modal  fade example-modal-lg modal-slide-bottom" role="dialog" tabindex="-1">
        <div id="modal-create-holder" class="modal-dialog modal-dialog-centered modal-simple">

            @include('discountcoupons::create')
            @include('discountcoupons::createDiscount')
            @include('discountcoupons::createCoupon')

        </div>
    </div>

    <!-- Edit -->
    <div id="modal-edit-coupon" class="modal fade example-modal-lg modal-slide-bottom" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            @include('discountcoupons::editCoupon')
            @include('discountcoupons::editDiscount')

        </div>
    </div>

    <!-- Delete -->
    <div id="modal-delete-coupon" class="modal fade example-modal-lg modal-slide-bottom" aria-hidden="true"
        role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button type="button"
                        class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button frete="" type="button" data-dismiss="modal"
                        class="col-4 btn border-0 btn-delete1 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        style="width: 20%;">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>



@push('css')

    <link rel="stylesheet" href="{!! asset('modules/discount-coupons/css/styles.css?v=01') !!}">
    <link rel="stylesheet"
        href="{{ asset('/modules/global/jquery-daterangepicker/daterangepicker.min.css?v=' . versionsFile()) }}">
@endpush

@push('scripts')

    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src="{{ asset('modules/global/jquery-daterangepicker/src/daterangepicker.js?v=' . versionsFile()) }}">
    </script>


@endpush
