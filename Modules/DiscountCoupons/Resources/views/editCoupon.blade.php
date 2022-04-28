<form id="form-update-coupon" method="PUT">
    @csrf
    @method('PUT')

    <div style="display: none" id="edit-coupon">


        <div class="modal-content s-border-radius" style="width:646px; height:706px">

            <div class="mdtpad simple-border-bottom">
                <span class="" id="modal-title"
                    style="color:#636363; font: normal normal bold 22px Muli;">
                    Detalhes de cupom de desconto
            </span>
                <a id="modal-button-close-3" class="modal-button-close-2 pointer close" role="button"
                    data-dismiss="modal" aria-label="Close">

                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 1L1 15M1 1L15 15L1 1Z" stroke="#636363" stroke-width="2" stroke-miterlimit="10"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </a>
            </div>

            <div id="c-edit_step0" >
                <div id="coupon_edit_step0" style="height: 553px; overflow:hidden">

                    <div style=' position: relative; padding: 25px 30px 20px' class=" simple-border-bottom">
    
                        <div class="modal-disc">
    
                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                                <path d="M18 10C22.4183 10 26 13.5817 26 18C26 22.4183 22.4183 26 18 26C13.5817 26 10 22.4183 10 18C10 13.5817 13.5817 10 18 10ZM18 11.1998C14.2444 11.1998 11.1998 14.2444 11.1998 18C11.1998 21.7556 14.2444 24.8002 18 24.8002C21.7556 24.8002 24.8002 21.7556 24.8002 18C24.8002 14.2444 21.7556 11.1998 18 11.1998ZM17.9971 16.7994C18.3008 16.7992 18.5519 17.0247 18.5919 17.3175L18.5974 17.3989L18.6003 21.7995C18.6005 22.1308 18.3321 22.3996 18.0008 22.3998C17.697 22.4 17.4459 22.1745 17.406 21.8817L17.4005 21.8003L17.3976 17.3997C17.3974 17.0684 17.6658 16.7996 17.9971 16.7994ZM18.0004 14.0016C18.4415 14.0016 18.7992 14.3593 18.7992 14.8004C18.7992 15.2416 18.4415 15.5992 18.0004 15.5992C17.5592 15.5992 17.2016 15.2416 17.2016 14.8004C17.2016 14.3593 17.5592 14.0016 18.0004 14.0016Z" fill="#2E85EC"/>
                                </svg>
                                
    
    
    
                            <span class="sub-title"> Informações do cupom </span>
    
    
                            {{-- <svg id="c-edit-name" class="pointer" style="position: absolute; top:25px; right:30px"
                                width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="35" height="35" rx="7.5" stroke="#2E85EC" />
                                <path
                                    d="M25.2706 11.7294C26.2431 12.702 26.2431 14.2788 25.2706 15.2513L16.0731 24.4488C15.6741 24.8478 15.1742 25.1308 14.6269 25.2677L11.7742 25.9808C11.3182 26.0948 10.9052 25.6818 11.0192 25.2258L11.7323 22.3731C11.8692 21.8258 12.1522 21.3259 12.5512 20.9269L21.7487 11.7294C22.7212 10.7569 24.298 10.7569 25.2706 11.7294ZM20.8681 14.3707L13.4316 21.8074C13.1923 22.0468 13.0224 22.3467 12.9403 22.6751L12.4788 24.5212L14.3249 24.0597C14.6533 23.9776 14.9532 23.8077 15.1926 23.5684L22.6288 16.1314L20.8681 14.3707ZM22.6291 12.6099L21.7484 13.4904L23.5091 15.2511L24.3901 14.3709C24.8764 13.8846 24.8764 13.0962 24.3901 12.6099C23.9038 12.1236 23.1154 12.1236 22.6291 12.6099Z"
                                    fill="#2E85EC" />
                            </svg> --}}
    
    
                            <button type="button" id="c-edit-name" class="btn btn-edit" id="" data-code="">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"></path>
                                </svg>
                            </button>
    
                        </div>
    
                        <input name="id" type="hidden" id="coupon-id2" />
                        <input name="plans" type="hidden" id="edited-plans" />
                        
                        <div id="edit-name-box-c" style=" height: 68px; ">

                            <div class="row pt-20" id="c-display_name" style="color: #636363; padding-bottom:2px">
                                <div class="col-6">
                                    <span class=""> Nome</span> <br>
                                    <span style="font-size: 16px" id="c-d-name"></span>
                                </div>
                                <div class="col-6">
        
                                    <span class=""> Código do cupom</span> <br>
                                    <span style="font-size: 16px" id="d-code"></span>
        
                                </div>
                            </div>
        
                            <div class="row pt-20" id="c-display_name_edit" style="  display: none; color:#636363">
                                <div class="col-6">
        
                                    Nome <br>
                                    <input class="input-pad" maxlength="20" id="c-name-edit" name="name" type="text"
                                        style="margin-bottom: 20px; height:48px" /> <br>
                                </div>
        
                                <div class="col-6">
        
                                    Código do cupom <br>
                                    <input class="input-pad" maxlength="20" id="c-code-edit" name="code" type="text"
                                        style="margin-bottom: 20px; height:48px" /> <br>
                                </div>
        
                                <div class="col-12 text-right">
        
                                    <button id="c-cancel_name_edit" class="mr-10 btn btn-default btn-lg clean-cancel"
                                        type="button">Cancelar</button>
                                    <button id="c-save_name_edit" class="btn btn-primary save-name-btn" type="button"
                                        style="margin-left: 4px">Atualizar</button>
                                </div>
                            </div>
                        </div>
                    </div>
    
    
                    <div style='min-height: 100px; position: relative; padding: 20px 30px 0px'
                        class=" simple-border-bottom">
    
                        <div class="modal-disc">
                            
    
    
                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                                <path d="M23.2661 11C24.2237 11 25 11.7763 25 12.734V16.9434C25 17.6076 24.7362 18.2445 24.2667 18.7142L17.7155 25.2673C16.7372 26.2437 15.153 26.2444 14.1734 25.2684L10.7351 21.8351C9.75607 20.8578 9.75482 19.2719 10.7322 18.293L17.2823 11.7347C17.7521 11.2643 18.3896 11 19.0544 11H23.2661ZM23.2661 12.156H19.0544C18.6964 12.156 18.3531 12.2983 18.1002 12.5516L11.5405 19.1196C11.024 19.6475 11.0279 20.494 11.5518 21.0169L14.9897 24.4499C15.5175 24.9757 16.3717 24.9754 16.8985 24.4496L23.4492 17.8969C23.702 17.644 23.8441 17.301 23.8441 16.9434V12.734C23.8441 12.4148 23.5853 12.156 23.2661 12.156ZM21.1468 13.699C21.7852 13.699 22.3028 14.2166 22.3028 14.855C22.3028 15.4934 21.7852 16.011 21.1468 16.011C20.5084 16.011 19.9909 15.4934 19.9909 14.855C19.9909 14.2166 20.5084 13.699 21.1468 13.699Z" fill="#2E85EC"/>
                                </svg>
                                
    
    
    
                            <span class="sub-title"> Planos no cupom <span
                                    style="color:#969696; font-size: 14px; font-weight: normal"> • <span
                                        id="planos-count2">x planos</span></span> </span>
    
    
                            {{-- <svg id="c-edit-plans" class="pointer" style="position: absolute; top:20px; right:30px"
                                width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="35" height="35" rx="7.5" stroke="#2E85EC" />
                                <path
                                    d="M25.2706 11.7294C26.2431 12.702 26.2431 14.2788 25.2706 15.2513L16.0731 24.4488C15.6741 24.8478 15.1742 25.1308 14.6269 25.2677L11.7742 25.9808C11.3182 26.0948 10.9052 25.6818 11.0192 25.2258L11.7323 22.3731C11.8692 21.8258 12.1522 21.3259 12.5512 20.9269L21.7487 11.7294C22.7212 10.7569 24.298 10.7569 25.2706 11.7294ZM20.8681 14.3707L13.4316 21.8074C13.1923 22.0468 13.0224 22.3467 12.9403 22.6751L12.4788 24.5212L14.3249 24.0597C14.6533 23.9776 14.9532 23.8077 15.1926 23.5684L22.6288 16.1314L20.8681 14.3707ZM22.6291 12.6099L21.7484 13.4904L23.5091 15.2511L24.3901 14.3709C24.8764 13.8846 24.8764 13.0962 24.3901 12.6099C23.9038 12.1236 23.1154 12.1236 22.6291 12.6099Z"
                                    fill="#2E85EC" />
                            </svg> --}}
    
    
                            <button type="button" id="c-edit-plans" class="btn btn-edit" id="" data-code="">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"></path>
                                </svg>
                            </button>
    
                        </div>
    
                        <div id="c-show_plans" class=" mostrar_menos scroller" style="margin-top: 10px" {{-- style="overflow-y: scroll; overflow-x: hidden; max-height: 164px" --}}>
    
    
    
                            {{-- <div class="item_raw" >
                                        <span style="background-image: url(http://dev.woo.com/wp-content/uploads/2021/07/sunglasses-2.jpg)" class="image"></span>
                                        <span class="title">Nome do produto</span>
                                        <span class="description">Descrição do produto</span>
                                    </div> --}}
    
    
    
                        </div>
                        <div style="padding-top: 10px; width: 160px; padding-bottom:18px">
                            <span style="color:#2E85EC; font-size: 14px; font-weight: 400; " id="mostrar_mais2"
                                class="pointer">
                                <span id="mostrar_mais_label2">
    
                                    Ver todos os planos
                                </span>
    
                                <svg id="mm-arrow-down2" style="margin-left:4px" width="11" height="6" viewBox="0 0 11 6"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.21967 0.21967C9.51256 -0.0732233 9.98744 -0.0732233 10.2803 0.21967C10.5732 0.512563 10.5732 0.987436 10.2803 1.28033L5.78033 5.78033C5.48744 6.07322 5.01256 6.07322 4.71967 5.78033L0.21967 1.28033C-0.0732232 0.987436 -0.0732231 0.512563 0.21967 0.219669C0.512564 -0.0732237 0.987436 -0.0732237 1.28033 0.21967L5.25 4.18934L9.21967 0.21967Z"
                                        fill="#2E85EC" />
                                </svg>
    
                                <svg id="mm-arrow-up2" style="margin-left:4px; display: none" width="11" height="6"
                                    viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.28033 5.78033C0.987437 6.07322 0.512563 6.07322 0.21967 5.78033C-0.0732233 5.48744 -0.0732233 5.01256 0.21967 4.71967L4.71967 0.21967C5.01256 -0.0732235 5.48744 -0.0732235 5.78033 0.21967L10.2803 4.71967C10.5732 5.01256 10.5732 5.48744 10.2803 5.78033C9.98744 6.07322 9.51256 6.07322 9.21967 5.78033L5.25 1.81066L1.28033 5.78033Z"
                                        fill="#2E85EC" />
                                </svg>
    
                            </span>
                        </div>
    
    
                    </div>
    
                    <div style='min-height: 100px; position: relative; padding: 20px 30px 30px;' class="">
    
                        
                        <div class="modal-disc pb-20">
    
                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                                <path d="M18.0005 15.8832C19.8179 15.8832 21.2912 17.3565 21.2912 19.1739C21.2912 20.9913 19.8179 22.4646 18.0005 22.4646C16.1831 22.4646 14.7098 20.9913 14.7098 19.1739C14.7098 17.3565 16.1831 15.8832 18.0005 15.8832ZM18.0005 17.1172C16.8646 17.1172 15.9438 18.038 15.9438 19.1739C15.9438 20.3098 16.8646 21.2306 18.0005 21.2306C19.1364 21.2306 20.0572 20.3098 20.0572 19.1739C20.0572 18.038 19.1364 17.1172 18.0005 17.1172ZM18.0005 13C21.7959 13 25.0723 15.5914 25.9814 19.223C26.0641 19.5536 25.8632 19.8887 25.5327 19.9714C25.2021 20.0542 24.8671 19.8533 24.7843 19.5227C24.0119 16.4371 21.2265 14.234 18.0005 14.234C14.7731 14.234 11.9868 16.439 11.2158 19.5264C11.1332 19.857 10.7983 20.0581 10.4677 19.9755C10.137 19.893 9.93597 19.558 10.0185 19.2274C10.926 15.5937 14.2034 13 18.0005 13Z" fill="#2E85EC"/>
                                </svg>
                                
    
                            <span class="sub-title">Revisão de regras </span>
    
    
                            {{-- <svg id="c-edit-rules" class="pointer" style="position: absolute; top:20px; right:30px"
                                width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="35" height="35" rx="7.5" stroke="#2E85EC" />
                                <path
                                    d="M25.2706 11.7294C26.2431 12.702 26.2431 14.2788 25.2706 15.2513L16.0731 24.4488C15.6741 24.8478 15.1742 25.1308 14.6269 25.2677L11.7742 25.9808C11.3182 26.0948 10.9052 25.6818 11.0192 25.2258L11.7323 22.3731C11.8692 21.8258 12.1522 21.3259 12.5512 20.9269L21.7487 11.7294C22.7212 10.7569 24.298 10.7569 25.2706 11.7294ZM20.8681 14.3707L13.4316 21.8074C13.1923 22.0468 13.0224 22.3467 12.9403 22.6751L12.4788 24.5212L14.3249 24.0597C14.6533 23.9776 14.9532 23.8077 15.1926 23.5684L22.6288 16.1314L20.8681 14.3707ZM22.6291 12.6099L21.7484 13.4904L23.5091 15.2511L24.3901 14.3709C24.8764 13.8846 24.8764 13.0962 24.3901 12.6099C23.9038 12.1236 23.1154 12.1236 22.6291 12.6099Z"
                                    fill="#2E85EC" />
                            </svg> --}}
    
                            <button type="button" id="c-edit-rules" class="btn btn-edit" id="" data-code="">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"></path>
                                </svg>
                            </button>
    
                        </div>
    
                        <div id="c-rules"></div>
    
                    </div>
                </div>

                <div class="modal-footer " style="position: absolute;
                    bottom: 0;
                    width: 646px;
                    border-top: 1px solid #EBEBEB;
                    background-color: #fff;
                    border-radius: 0px 0px 12px 12px;
                    padding:0">
                    <div class="d-flex justify-content-between" style="padding:15px 30px 20px; width: 100%;">
                        <div style="width: 236px" class="mr-auto switch-holder d-flex align-items-center">
                            <label class="switch">
                                <input id="c-edit_status" type="checkbox" value="1" name="status" class="check status"
                                    checked="">
                                <span class="slider round"></span>
                            </label>
                            <label id="c-edit_status_label" for="c-edit_status" class="pointer"
                                style="font: normal normal bold 16px Muli; margin-bottom: 0;">Desconto ativo</label>
                            <input type="hidden" name="set_status" id="c-set_status">
                        </div>
                        <div style="width: 256px">

                        </div>
                        <button class="p-2 btn btn-primary edit-finish-btn btn2 float-right "
                            type="button">Fechar</button>
                    </div>
                </div>


            </div>

            <div id="c-edit_step1" style="display: none">
                <div style='min-height: 100px; position: relative; padding:25px 25px 30px 30px' class=" ">

                    <div class="mb-20">

                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8" />
                            <path
                                d="M23.2661 11C24.2237 11 25 11.7763 25 12.734V16.9434C25 17.6076 24.7362 18.2445 24.2667 18.7142L17.7155 25.2673C16.7372 26.2437 15.153 26.2444 14.1734 25.2684L10.7351 21.8351C9.75607 20.8578 9.75482 19.2719 10.7322 18.293L17.2823 11.7347C17.7521 11.2643 18.3896 11 19.0544 11H23.2661ZM23.2661 12.156H19.0544C18.6964 12.156 18.3531 12.2983 18.1002 12.5516L11.5405 19.1196C11.024 19.6475 11.0279 20.494 11.5518 21.0169L14.9897 24.4499C15.5175 24.9757 16.3717 24.9754 16.8985 24.4496L23.4492 17.8969C23.702 17.644 23.8441 17.301 23.8441 16.9434V12.734C23.8441 12.4148 23.5853 12.156 23.2661 12.156ZM21.1468 13.699C21.7852 13.699 22.3028 14.2166 22.3028 14.855C22.3028 15.4934 21.7852 16.011 21.1468 16.011C20.5084 16.011 19.9909 15.4934 19.9909 14.855C19.9909 14.2166 20.5084 13.699 21.1468 13.699Z"
                                fill="#2E85EC" />
                        </svg>


                        {{-- <span class="sub-title"> Selecione os planos que terão cupom </span> --}}
                        <span class="sub-title"> Planos no cupom <span style="color:#969696; font-size: 14px; font-weight: normal"> • <span id="planos-count-edit">x planos</span></span>  </span>



                        <div class="custom-control custom-checkbox" style="position: absolute; right: 32px; top: 34px;">
                            <span id="all-plans2" class="pointer"
                                style="color: #2E85EC; font-size: 16px; font-weight: 700">

                                Selecionar todos
                                <svg class=" " style="margin-left: 4px;
                                        margin-top: -2px;" width="19" height="20" viewBox="0 0 19 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"></circle>
                                    <path
                                        d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z"
                                        fill="white"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <div class="edit-plans-thumbs edit-plans-thumbs-scroll" style="height: 76px; ">
                        
                    </div>

                    {{-- <div style="position: relative">

                        <input class="input-pad" style="height: 48px; padding-right: 38px" id="search_input2"
                            autocomplete="off" autofocus placeholder="Pesquiser plano por nome" type="text">
                        <svg style="position: absolute;
                            right: 12px;
                            top: 16px;" width="18" height="18" viewBox="0 0 18 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.9633 12.0239C9.80854 12.9477 8.34378 13.5001 6.75 13.5001C3.02208 13.5001 0 10.478 0 6.75003C0 3.02209 3.02208 0 6.75 0C10.4779 0 13.5 3.02209 13.5 6.75003C13.5 8.34377 12.9477 9.80852 12.024 10.9633L17.7803 16.7197C18.0732 17.0126 18.0732 17.4874 17.7803 17.7803C17.4874 18.0732 17.0126 18.0732 16.7197 17.7803L10.9633 12.0239ZM12 6.75003C12 3.85052 9.6495 1.50001 6.75 1.50001C3.85051 1.50001 1.5 3.85052 1.5 6.75003C1.5 9.64953 3.85051 12 6.75 12C9.6495 12 12 9.64953 12 6.75003Z"
                                fill="#636363" />
                        </svg>
                    </div> --}}

                    <div class="d-flex modal-new-layout box-description">
                        <input class="form-control form-control-lg search_input_create_coupon search_coupon search_input_create_coupon_id" type="text" id="search_input2" placeholder="Pesquisa por nome" style="border-top-right-radius: 0;border-bottom-right-radius: 0; height: 48px !important; border-right: 0;">
                        <div class="input-group input-group-lg" style="width: 678px;">
                            <input onkeyup="set_description_value(this, $('.search_input_create_coupon_id'))" class="form-control" type="text" id="search_input_description" placeholder="Pesquisa por descrição" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <img src="/build/global/img/icon-search.svg" alt="Icon Search">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div id="search_result2" class="mt-20  "
                        style=" height: 312px; width:598px; overflow: hidden">

                        {{-- <div class="item item_selected" >
                                <span style="background-image: url(http://dev.woo.com/wp-content/uploads/2021/07/sunglasses-2.jpg)" class="image"></span>
                                <span class="title">Nome do produto mui </span>
                                <span class="description">Descrição do produto muito longo muito longo muito longo </span>
                                
                                <svg class="selected_check" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>
                                    <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>
                                    </svg>
                                    
                                <svg class="empty_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>
                                    </svg>
                                    
                            </div> --}}

                        {{-- <div class="item" >
                                <span style="background-image: url(http://dev.woo.com/wp-content/uploads/2021/07/sunglasses-2.jpg)" class="image"></span>
                                <span class="title">Nome do produto</span>
                                <span class="description">Descrição do produto</span>
                                <svg class="selected_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>                            <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>                            </svg>    
                                <svg class="empty_check " width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>                            </svg>
                            </div> --}}


                    </div>

                </div>

                <div class="modal-footer" style="position: absolute;
                    bottom: 0px;
                    text-align: center;
                    width:646px;
                    
                    border-top:1px solid #EBEBEB">
                    <div style="width: 646px" class="justify-center text-center mt-10">
                        <button class="btn btn2 c-plans-back" type="button">Voltar</button>
                        <button class="btn btn-primary btn2 c-edit-plans-save" disabled type="button">Continuar</button>

                    </div>
                </div>
            </div>

            <div id="c-edit_step2" style="display: none">


                <div style='padding: 25px 30px 44px' class=" simple-border-bottom">

                    <div>

                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M18 10C22.4183 10 26 13.5817 26 18C26 22.4183 22.4183 26 18 26C13.5817 26 10 22.4183 10 18C10 13.5817 13.5817 10 18 10ZM18 11.1998C14.2444 11.1998 11.1998 14.2444 11.1998 18C11.1998 21.7556 14.2444 24.8002 18 24.8002C21.7556 24.8002 24.8002 21.7556 24.8002 18C24.8002 14.2444 21.7556 11.1998 18 11.1998ZM17.9971 16.7994C18.3008 16.7992 18.5519 17.0247 18.5919 17.3175L18.5974 17.3989L18.6003 21.7995C18.6005 22.1308 18.3321 22.3996 18.0008 22.3998C17.697 22.4 17.4459 22.1745 17.406 21.8817L17.4005 21.8003L17.3976 17.3997C17.3974 17.0684 17.6658 16.7996 17.9971 16.7994ZM18.0004 14.0016C18.4415 14.0016 18.7992 14.3593 18.7992 14.8004C18.7992 15.2416 18.4415 15.5992 18.0004 15.5992C17.5592 15.5992 17.2016 15.2416 17.2016 14.8004C17.2016 14.3593 17.5592 14.0016 18.0004 14.0016Z" fill="#2E85EC"/>
                            </svg>
                            


                        <span class="sub-title"> Informações do desconto </span>




                    </div>



                    <div class="row mt-15" id="c-display_name">
                        <div class="col-6">
                            <span class=""> Nome</span> <br>
                            <span style="font-size: 16px" id="c-d-name2"></span>
                        </div>
                        <div class="col-6">

                            <span class=""> Código do cupom</span> <br>
                            <span style="font-size: 16px" id="d-code2"></span>

                        </div>
                    </div>


                </div>

                <div style='position: relative; min-height: 100px; padding:30px ' class="">
                    <div class="mb-20">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M18.0005 15.8832C19.8179 15.8832 21.2912 17.3565 21.2912 19.1739C21.2912 20.9913 19.8179 22.4646 18.0005 22.4646C16.1831 22.4646 14.7098 20.9913 14.7098 19.1739C14.7098 17.3565 16.1831 15.8832 18.0005 15.8832ZM18.0005 17.1172C16.8646 17.1172 15.9438 18.038 15.9438 19.1739C15.9438 20.3098 16.8646 21.2306 18.0005 21.2306C19.1364 21.2306 20.0572 20.3098 20.0572 19.1739C20.0572 18.038 19.1364 17.1172 18.0005 17.1172ZM18.0005 13C21.7959 13 25.0723 15.5914 25.9814 19.223C26.0641 19.5536 25.8632 19.8887 25.5327 19.9714C25.2021 20.0542 24.8671 19.8533 24.7843 19.5227C24.0119 16.4371 21.2265 14.234 18.0005 14.234C14.7731 14.234 11.9868 16.439 11.2158 19.5264C11.1332 19.857 10.7983 20.0581 10.4677 19.9755C10.137 19.893 9.93597 19.558 10.0185 19.2274C10.926 15.5937 14.2034 13 18.0005 13Z" fill="#2E85EC"/>
                            </svg>
                            

                        <span class="sub-title"> Regras para aplicação de cupom </span>
                    </div>
                    <div class="">

                        <label>Selecione o tipo de desconto</label>
                    </div>
                    <div style="padding: 10px 0" class="row mb-10">

                        <div class="col-3">

                            <input name="type" value="1" class="discount_radio" type="radio" id="2c_type_value"
                                checked />
                            <label for="2c_type_value">
                                Valor em R$
                            </label>
                        </div>
                        <div class="col-3">
                            <input name="type" value="0" class="discount_radio " type="radio" id="2c_type_percent" />
                            <label for="2c_type_percent">Porcentagem</label>
                        </div>
                    </div>

                    <div class="flex row ">
                        <div class="col-6">
                            <label>Desconto de</label>

                            <div id="2money_opt" class="input-group input-group-lg mb-3">
                                <div 
                                    class="input-group-prepend">
                                    <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">R$</span>
                                </div>
                                <input onkeyup="$(this).removeClass('warning-input')" maxlength="9" name="discount_value" id="2discount_value" style="" type="text"
                                    class="input-pad form-control" placeholder="" aria-label=""
                                    aria-describedby="basic-addon1" />
                            </div>

                            <div id="2percent_opt" style="display: none" class="input-group input-group-lg mb-3">

                                <input onkeyup="$(this).removeClass('warning-input')" maxlength="2" data-mask="0#" name="percent_value" id="2percent_value" style=""
                                    type="text" class="input-pad form-control" placeholder="" aria-label=""
                                    aria-describedby="basic-addon1">
                                <div 
                                    class="input-group-append">
                                    <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <label>Valor mínimo da compra</label>

                            <div class="input-group-lg input-group mb-3">
                                <div 
                                    class="input-group-prepend">
                                    <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">R$</span>
                                </div>
                                <input onkeyup="$(this).removeClass('warning-input')" maxlength="9" name="rule_value" id="2minimum_value" style="" type="text"
                                    class="input-pad form-control" placeholder="" aria-label=""
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>


                    <div class=" pt-20 flex row">
                        <div class="col-4" style="position: relative">

                            <label>Vence em</label>

                            <input name="expires" class="input-pad" type="text" id="date_range2"
                                style="padding-right: 40px; height: 48px" autocomplete="off">
                            <svg id="cal-icon2" class="pointer" style="pointer-events: none;
                            position: absolute; top:43px; right: 28px;"
                                width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M18.75 0C20.5449 0 22 1.45507 22 3.25V18.75C22 20.5449 20.5449 22 18.75 22H3.25C1.45507 22 0 20.5449 0 18.75V3.25C0 1.45507 1.45507 0 3.25 0H18.75ZM20.5 6.503H1.5V18.75C1.5 19.7165 2.2835 20.5 3.25 20.5H18.75C19.7165 20.5 20.5 19.7165 20.5 18.75V6.503ZM18.75 1.5H3.25C2.2835 1.5 1.5 2.2835 1.5 3.25V5.003H20.5V3.25C20.5 2.2835 19.7165 1.5 18.75 1.5Z"
                                    fill="#636363" />
                            </svg>


                        </div>
                        <div class="col-6 pt-40">

                            <label class="custom-check">
                                <input name="nao_vence" type="checkbox" class="custom-control-input" id="nao_vence2">
                                <span class="icone"></span>
                                Não vence
                            </label>


                            

                              
                        </div>
                    </div>

                </div>

                <input type="hidden" name="value" id="2c_value">

                <div class="modal-footer" style="position: absolute;
                    bottom: 0;
                    width: 646px;
                    border-top: 1px solid #EBEBEB;
                    background-color: #fff;
                    border-radius: 0px 0px 12px 12px;">
                    <div style="width: 646px" class="justify-center text-center mt-10">
                        <button class="btn back-btn rule-coupon-back" type="button">Voltar</button>
                        <button class="btn btn-primary btn2 update-rule-coupon" type="button">Finalizar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
