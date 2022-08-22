@push('css')
    <link rel="stylesheet"
          href="{!! mix('build/layouts/projects/create-store-modal.min.css') !!}">
@endpush

<div class="modal fade"
     id="new-store-modal"
     tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-dialog-centered"
         role="document"
         style="max-width: 450px">
        <div class="modal-content"
             style="border-radius: 8px;">
            <div class="d-flex flex-row-reverse simple-border-bottom py-10 px-20">
                <h4 class="new-store-modal-option-title text-center">Criar nova loja</h4>
                <button type="button"
                        class="new-store-modal-option-close-btn"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="d-flex flex-row justify-content-around"
                 style="padding: 32px 16px">
                <div class="d-flex flex-column align-items-center new-store-modal-option">
                    <a href="/apps/shopify"
                       class="d-flex justify-content-center align-items-center">
                        <img src="{{ mix('build/global/img/svg/shopify-logo.svg') }}"
                             data-value="product_digital"
                             alt="novo produto digital">
                    </a>
                    <span>Shopify</span>
                </div>
                <div class="d-flex flex-column align-items-center new-store-modal-option">
                    <a href="/apps/woocommerce"
                       class="d-flex justify-content-center align-items-center">
                        <img src="{{ mix('build/global/img/svg/woocommerce-logo.svg') }}"
                             data-value="product_digital"
                             alt="novo produto digital">
                    </a>
                    <span>Woocommerce</span>
                </div>
                <div class="d-flex flex-column align-items-center new-store-modal-option">
                    <a href="/projects/create"
                       class="d-flex justify-content-center align-items-center">
                        <img src="{{ mix('build/global/img/svg/landing-logo.svg') }}"
                             data-value="product_physical"
                             alt="novo produto fisico">
                    </a>
                    <span>Landing Page</span>
                </div>
            </div>
        </div>
    </div>
</div>
