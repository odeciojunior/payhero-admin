<div>
    <form id="form-update-custom-config" enctype="multipart/form-data">
        <div class="box-breadcrumbs" style="margin-bottom: 38px;">
            <div class="d-flex" style="justify-content: space-between !important;">
                <div class="d-flex align-items-center">
                    <div class="icon mr-15"><img src="/build/global/img/icon-products-plans.svg" alt="Icon Products"></div>
                    <div class="title"></div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-bottom: 30px;">
            <div class="col-sm-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center product-custom">
                        <div class="background-photo"></div>
                        <div style="line-height: 1;">
                            <h1 class="name-product bold"></h1>
                            <small class="qtd-product"></small>
                        </div>
                    </div>
                    <div>
                        <div class="switch-holder d-inline">
                            <label class="switch m-0">
                                <input type="checkbox" class="active_custom" name="is_custom[]">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 24px;">
            <div class="col-sm-12">
                <div class="d-flex align-items-end">
                    <div style=" margin-right: 35px;">
                        <div>
                            <label for="type">Tipo</label>
                        </div>
                        <div class="d-flex">
                            <input type="hidden" id="custom-type">
                            <button style="width: 45px; height: 45px; margin-right: 8px;" type="button" class="btn btn-outline-secondary btn-type" typeCustom="Text" title="Solicitar personalização tipo Texto">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.9 1.8C2.7402 1.8 1.8 2.7402 1.8 3.9V7.5C1.8 7.99705 1.39705 8.4 0.9 8.4C0.402948 8.4 0 7.99705 0 7.5V3.9C0 1.74608 1.74608 0 3.9 0H7.5C7.99705 0 8.4 0.402948 8.4 0.9C8.4 1.39705 7.99705 1.8 7.5 1.8H3.9ZM3.9 22.2C2.7402 22.2 1.8 21.2598 1.8 20.1V16.5C1.8 16.003 1.39705 15.6 0.9 15.6C0.402948 15.6 0 16.003 0 16.5V20.1C0 22.2539 1.74608 24 3.9 24H7.5C7.99705 24 8.4 23.597 8.4 23.1C8.4 22.603 7.99705 22.2 7.5 22.2H3.9ZM22.2 3.9C22.2 2.7402 21.2598 1.8 20.1 1.8H16.5C16.003 1.8 15.6 1.39705 15.6 0.9C15.6 0.402948 16.003 0 16.5 0H20.1C22.2539 0 24 1.74608 24 3.9V7.5C24 7.99705 23.597 8.4 23.1 8.4C22.603 8.4 22.2 7.99705 22.2 7.5V3.9ZM20.1 22.2C21.2598 22.2 22.2 21.2598 22.2 20.1V16.5C22.2 16.003 22.603 15.6 23.1 15.6C23.597 15.6 24 16.003 24 16.5V20.1C24 22.2539 22.2539 24 20.1 24H16.5C16.003 24 15.6 23.597 15.6 23.1C15.6 22.603 16.003 22.2 16.5 22.2H20.1ZM6.9 4.8C6.40295 4.8 6 5.20295 6 5.7V7.2C6 7.69705 6.40295 8.1 6.9 8.1C7.39705 8.1 7.8 7.69705 7.8 7.2V6.6H11.1V17.4H9.3C8.80295 17.4 8.4 17.803 8.4 18.3C8.4 18.797 8.80295 19.2 9.3 19.2H14.7C15.197 19.2 15.6 18.797 15.6 18.3C15.6 17.803 15.197 17.4 14.7 17.4H12.9V6.6H16.2V7.2C16.2 7.69705 16.603 8.1 17.1 8.1C17.597 8.1 18 7.69705 18 7.2V5.7C18 5.20295 17.597 4.8 17.1 4.8H6.9Z" fill="#636363"/>
                                </svg>
                            </button>
                            <button style="width: 45px; height: 45px; margin-right: 8px;" type="button" class="btn btn-outline-secondary btn-type" typeCustom="Image"  title="Solicitar personalização tipo Imagem">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.6645 4C18.5055 4 19.9979 5.4924 19.9979 7.33333C19.9979 9.17427 18.5055 10.6667 16.6645 10.6667C14.8236 10.6667 13.3312 9.17427 13.3312 7.33333C13.3312 5.4924 14.8236 4 16.6645 4ZM14.9979 7.33333C14.9979 8.2538 15.7441 9 16.6645 9C17.585 9 18.3312 8.2538 18.3312 7.33333C18.3312 6.41287 17.585 5.66667 16.6645 5.66667C15.7441 5.66667 14.9979 6.41287 14.9979 7.33333ZM0 3.16667C0 1.41777 1.41777 0 3.16667 0H20.8333C22.5823 0 24 1.41777 24 3.16667V20.8333C24 21.6251 23.7094 22.3491 23.229 22.9043C23.1932 22.9687 23.1482 23.0294 23.094 23.0845C23.0365 23.1429 22.9726 23.1911 22.9044 23.2289C22.3492 23.7093 21.6252 24 20.8333 24H3.16667C2.37332 24 1.64812 23.7083 1.09249 23.2262C1.02613 23.1888 0.963833 23.1415 0.90772 23.0845C0.855053 23.0311 0.811093 22.9722 0.77582 22.9099C0.29256 22.3539 0 21.6278 0 20.8333V3.16667ZM22.3333 20.8333V3.16667C22.3333 2.33824 21.6617 1.66667 20.8333 1.66667H3.16667C2.33824 1.66667 1.66667 2.33824 1.66667 3.16667V20.8333C1.66667 20.9377 1.67732 21.0395 1.69759 21.1378L10.2465 12.7234C11.2194 11.7657 12.7807 11.7657 13.7537 12.7233L22.3027 21.1365C22.3228 21.0386 22.3333 20.9372 22.3333 20.8333ZM3.16667 22.3333H20.8333C20.9299 22.3333 21.0243 22.3242 21.1157 22.3068L12.5847 13.9112C12.2603 13.592 11.7399 13.592 11.4156 13.9112L2.8856 22.3071C2.97667 22.3243 3.0706 22.3333 3.16667 22.3333Z" fill="#636363"/>
                                </svg>
                            </button>
                            <button style="width: 45px; height: 45px;" type="button" class="btn btn-outline-secondary btn-type" typeCustom="File"  title="Solicitar personalização tipo Arquivo">
                                <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.77024 2.7431C12.1117 0.399509 15.9106 0.399509 18.2538 2.74266C20.5369 5.02572 20.5954 8.69093 18.4294 11.0449L18.2413 11.2422L9.44124 20.0404L9.40474 20.0707C7.94346 21.3875 5.68946 21.3427 4.28208 19.9353C2.96306 18.6163 2.84095 16.5536 3.91574 15.0969C3.93908 15.0516 3.96732 15.0078 4.00054 14.9667L4.0541 14.907L4.14101 14.8193L4.28208 14.6714L4.28501 14.6743L11.7207 7.21998C11.9866 6.95336 12.4032 6.9286 12.6971 7.14607L12.7814 7.21857C13.048 7.48449 13.0727 7.90112 12.8553 8.19502L12.7828 8.27923L5.1882 15.8923C4.47056 16.7679 4.52044 18.0622 5.33784 18.8796C6.1669 19.7087 7.48655 19.7481 8.36234 18.998L17.195 10.1676C18.9505 8.40992 18.9505 5.56068 17.1931 3.80332C15.4907 2.10087 12.7635 2.04767 10.9971 3.64371L10.8292 3.80332L10.8166 3.81763L1.28033 13.354C0.987435 13.6468 0.512565 13.6468 0.219665 13.354C-0.0465948 13.0877 -0.0708047 12.671 0.147055 12.3774L0.219665 12.2933L9.76854 2.74266L9.77024 2.7431Z" fill="#636363"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div style="width: 100%; margin-right: 16px;">
                        <div>
                            <label for="title">Nome da personalização</label>
                        </div>
                        <input style="height: 45px;" type="text" class="form-control input-pad" id="custom-title">
                    </div>

                    <div>
                        <button style="width: 45px; height: 45px; line-height: 19px;" type="button" class="btn btn-plus" id="add-list-custom-product">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 9.5H1M9.5 1V18V1Z" stroke="#41DC8F" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-bottom: 24px; line-height: 14px;">
            <div class="col-sm-12">
                <small>ATENÇÃO: Seja claro e objetivo. O campo de “nome” aparecerá como a descrição do arquivo ou texto que seu cliente preencherá na página de obrigado.</small>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h5 class="bold mb-0" style="color: #636363;">Personalizações adicionadas</h5>
            </div>
        </div>

        <div style="margin: 12px -30px 24px -30px; border-top: 1px solid #EBEBEB;"></div>

        <div class="row">
            <div class="col-sm-12">
                <div class="list-custom-products" id="list-custom-products">
                    {{-- JS carrega --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="custom_products_checkbox">
                    {{-- JS carrega --}}
                </div>
            </div>
        </div>

        <input type="hidden" name="product_id" id="product_id">
    </form>
</div>
