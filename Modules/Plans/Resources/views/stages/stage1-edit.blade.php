<div class="informations-edit">
    <div class="box-breadcrumbs">
        <div class="d-flex" style="justify-content: space-between !important;">
            <div class="d-flex align-items-center">
                <div class="icon mr-15"><img src="/modules/global/img/icon-info-plans-c.svg" alt="Icon Informations"></div>
                <div class="title">Informações do plano</div>
            </div>
            <button class="btn btn-edit" id="btn-edit-informations-plan">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="informations-data" style="margin-top: 22px;">
        <div class="row mb-20">
            <div class="col-sm-6">
                <label for="name">Nome</label>
                <input type="text" class="form-control" id="name" readonly>
            </div>
            <div class="col-sm-6">
                <label for="price">Preço de venda</label>
                <input type="text" class="form-control" id="price" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label for="description">Descrição</label>
                <input type="text" class="form-control" id="description" readonly>
            </div>
        </div>
    </div>
</div>

<div class="line"></div>

<div class="products-edit">
    <div>
        <div class="d-flex" style="justify-content: space-between !important;">
            <div class="d-flex align-items-center">
                <div class="icon mr-15"><img src="/modules/global/img/icon-products-plans.svg" alt="Icon Informations"></div>
                <div class="title">Produtos no plano <span></span></div>
            </div>
            <button class="btn btn-edit" id="btn-edit-products-plan">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="box-products products-data" style="margin-top: 23px;">
        {{-- js carrega --}}
    </div>
</div>

<div class="line"></div>

<div class="review-edit">
    <div>
        <div class="d-flex" style="justify-content: space-between !important;">
            <div class="d-flex align-items-center">
                <div class="icon mr-15"><img src="/modules/global/img/icon-review-plans-c.svg" alt="Icon Informations"></div>
                <div class="title">Revisão geral</div>
            </div>
        </div>
    </div>

    <div class="review-data" style="margin-top: 22px;">
        <div class="d-flex justify-content-between" style="margin-bottom: 24px;">
            <div class="price-plan">
                <div style="font-size: 80%;font-weight: 400;">Preço de venda</div>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="costs-plan">
                <div style="font-size: 80%;font-weight: 400;">Seu custo</div>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="tax-plan">
                <div style="font-size: 80%;font-weight: 400;">Taxas est.</div>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="comission-plan">
                <div style="font-size: 80%;font-weight: 400;">Comissão aprox.</div>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="profit-plan">
                <div style="font-size: 80%;font-weight: 400;">Lucro aprox.</div>
                <p class="font-weight-bold m-0" style="line-height: 100%; color: #41DC8F;"></p>
            </div>
        </div>
        <div class="text-center description-tax">
            <p class="m-0" style="line-height: 14px; font-size: 11px;">Simulação considerando compras à vista com taxa de <span></span> % + 1,00 (30D).</p>
            <p class="font-weight-bold m-0" style="line-height: 14px; font-size: 11px;">Valor estimado sujeito à mudanças de acordo com as condições de pagamento.</p>
        </div>
    </div>
</div>
