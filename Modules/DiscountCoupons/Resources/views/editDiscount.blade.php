<form id="form-update-discount" method="PUT">
    @csrf
    @method('PUT')

    <div style="display: none" id="edit-discount">
        
        <div class="modal-content s-border-radius" style="width:646px; height:706px; position: relative;">
            
            <div class="mdtpad simple-border-bottom ">
                <span class="  " id="modal-title"
                    style="color:#636363; font: normal normal bold 22px Muli; ">
                    Detalhes de desconto progressivo
                </span>
                <a id="modal-button-close-2" class=" modal-button-close-2 pointer close" role="button" data-dismiss="modal"
                    aria-label="Close">
                    
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 1L1 15M1 1L15 15L1 1Z" stroke="#636363" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        
                </a>
            </div>

            <div id="edit_step0" style="height: 553px; overflow: hidden;">

                <div style='min-height: 100px; position: relative;' class="pt-25 pb-30 pr-30 pl-30 simple-border-bottom">

                    <div class="modal-disc pb-20">
                        


                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M18.0005 15.8832C19.8179 15.8832 21.2912 17.3565 21.2912 19.1739C21.2912 20.9913 19.8179 22.4646 18.0005 22.4646C16.1831 22.4646 14.7098 20.9913 14.7098 19.1739C14.7098 17.3565 16.1831 15.8832 18.0005 15.8832ZM18.0005 17.1172C16.8646 17.1172 15.9438 18.038 15.9438 19.1739C15.9438 20.3098 16.8646 21.2306 18.0005 21.2306C19.1364 21.2306 20.0572 20.3098 20.0572 19.1739C20.0572 18.038 19.1364 17.1172 18.0005 17.1172ZM18.0005 13C21.7959 13 25.0723 15.5914 25.9814 19.223C26.0641 19.5536 25.8632 19.8887 25.5327 19.9714C25.2021 20.0542 24.8671 19.8533 24.7843 19.5227C24.0119 16.4371 21.2265 14.234 18.0005 14.234C14.7731 14.234 11.9868 16.439 11.2158 19.5264C11.1332 19.857 10.7983 20.0581 10.4677 19.9755C10.137 19.893 9.93597 19.558 10.0185 19.2274C10.926 15.5937 14.2034 13 18.0005 13Z" fill="#2E85EC"/>
                            </svg>
                            


                        <span class="sub-title"> Informações do desconto </span>


                        {{-- <svg id="edit-name" class="pointer" style="position: absolute; top:25px; right:30px" width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.5" y="0.5" width="35" height="35" rx="7.5" stroke="#2E85EC"/>
                            <path d="M25.2706 11.7294C26.2431 12.702 26.2431 14.2788 25.2706 15.2513L16.0731 24.4488C15.6741 24.8478 15.1742 25.1308 14.6269 25.2677L11.7742 25.9808C11.3182 26.0948 10.9052 25.6818 11.0192 25.2258L11.7323 22.3731C11.8692 21.8258 12.1522 21.3259 12.5512 20.9269L21.7487 11.7294C22.7212 10.7569 24.298 10.7569 25.2706 11.7294ZM20.8681 14.3707L13.4316 21.8074C13.1923 22.0468 13.0224 22.3467 12.9403 22.6751L12.4788 24.5212L14.3249 24.0597C14.6533 23.9776 14.9532 23.8077 15.1926 23.5684L22.6288 16.1314L20.8681 14.3707ZM22.6291 12.6099L21.7484 13.4904L23.5091 15.2511L24.3901 14.3709C24.8764 13.8846 24.8764 13.0962 24.3901 12.6099C23.9038 12.1236 23.1154 12.1236 22.6291 12.6099Z" fill="#2E85EC"/>
                            </svg> --}}
                        
                            <button type="button" id="edit-name" class="btn btn-edit" id="" data-code="">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"></path>
                                </svg>
                            </button>
                        
                            
                    </div>

                    <input name="id" type="hidden" id="discount-id" />

                    <div id="edit-name-box" style=" ">

                        <div class="row" id="display_name" style="color:#636363">
                            <div class="col-6">
                                <span class=""> Nome</span> <br>
                                <span id="d-name"></span>
                            </div>
                            <div class="col-6">
                                
                                <span class=""> Tipo</span> <br>
                                <span id="">Desconto Progressivo</span>
    
                            </div>
                        </div>
                        
                        <div class="" id="display_name_edit" style=" display: none; color: #636363">
                            <div style="margin-bottom: 4px">Nome</div>
                            <input class="input-pad" id="name-edit" name="name" maxlength="20" type="text" style="margin-bottom: 24px; height:48px" /> <br>
                            <div class="d-flex flex-row-reverse">
                            
                                <button id="save_name_edit" class="btn btn-primary save-name-btn" type="button" style="margin-left: 4px">Finalizar</button>
                                <button id="cancel_name_edit" class="clean-cancel  btn btn-default btn-lg mr-10" type="button">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="plans_holder" style='min-height: 100px; position: relative; padding: 30px 30px 0' class="simple-border-bottom scroller">

                    
                    <div class="modal-disc">


                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                        <path d="M23.2661 11C24.2237 11 25 11.7763 25 12.734V16.9434C25 17.6076 24.7362 18.2445 24.2667 18.7142L17.7155 25.2673C16.7372 26.2437 15.153 26.2444 14.1734 25.2684L10.7351 21.8351C9.75607 20.8578 9.75482 19.2719 10.7322 18.293L17.2823 11.7347C17.7521 11.2643 18.3896 11 19.0544 11H23.2661ZM23.2661 12.156H19.0544C18.6964 12.156 18.3531 12.2983 18.1002 12.5516L11.5405 19.1196C11.024 19.6475 11.0279 20.494 11.5518 21.0169L14.9897 24.4499C15.5175 24.9757 16.3717 24.9754 16.8985 24.4496L23.4492 17.8969C23.702 17.644 23.8441 17.301 23.8441 16.9434V12.734C23.8441 12.4148 23.5853 12.156 23.2661 12.156ZM21.1468 13.699C21.7852 13.699 22.3028 14.2166 22.3028 14.855C22.3028 15.4934 21.7852 16.011 21.1468 16.011C20.5084 16.011 19.9909 15.4934 19.9909 14.855C19.9909 14.2166 20.5084 13.699 21.1468 13.699Z" fill="#2E85EC"/>
                        </svg>
                        
                            


                        <span class="sub-title"> Planos com desconto <span style="color:#969696; font-size: 14px; font-weight: normal"> • <span id="planos-count">x planos</span></span> </span>


                        {{-- <svg id="edit-plans" class="pointer" style="position: absolute; top:24px; right:30px" width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.5" y="0.5" width="35" height="35" rx="7.5" stroke="#2E85EC"/>
                            <path d="M25.2706 11.7294C26.2431 12.702 26.2431 14.2788 25.2706 15.2513L16.0731 24.4488C15.6741 24.8478 15.1742 25.1308 14.6269 25.2677L11.7742 25.9808C11.3182 26.0948 10.9052 25.6818 11.0192 25.2258L11.7323 22.3731C11.8692 21.8258 12.1522 21.3259 12.5512 20.9269L21.7487 11.7294C22.7212 10.7569 24.298 10.7569 25.2706 11.7294ZM20.8681 14.3707L13.4316 21.8074C13.1923 22.0468 13.0224 22.3467 12.9403 22.6751L12.4788 24.5212L14.3249 24.0597C14.6533 23.9776 14.9532 23.8077 15.1926 23.5684L22.6288 16.1314L20.8681 14.3707ZM22.6291 12.6099L21.7484 13.4904L23.5091 15.2511L24.3901 14.3709C24.8764 13.8846 24.8764 13.0962 24.3901 12.6099C23.9038 12.1236 23.1154 12.1236 22.6291 12.6099Z" fill="#2E85EC"/>
                            </svg> --}}

                            <button type="button" id="edit-plans" class="btn btn-edit" id="" data-code="">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"></path>
                                </svg>
                            </button>
                            
                    </div>

                    <div id="show_plans"  class=" mostrar_menos scroller"
                            {{-- style="overflow-y: scroll; overflow-x: hidden; max-height: 164px"> --}}
                            style=" ">

                            

                            {{-- <div class="item_raw" >
                                <span style="background-image: url(http://dev.woo.com/wp-content/uploads/2021/07/sunglasses-2.jpg)" class="image"></span>
                                <span class="title">Nome do produto</span>
                                <span class="description">Descrição do produto</span>
                            </div> --}}


                        
                    </div>

                    <div style="padding-bottom: 18px; width: 160px">
                        <span style="color:#2E85EC; font-size: 14px; font-weight: 400; " id="mostrar_mais" class="pointer">
                        <span id="mostrar_mais_label">
                            
                            Ver todos os planos 
                        </span>

                        <svg id="mm-arrow-down" style="margin-left:4px" width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.21967 0.21967C9.51256 -0.0732233 9.98744 -0.0732233 10.2803 0.21967C10.5732 0.512563 10.5732 0.987436 10.2803 1.28033L5.78033 5.78033C5.48744 6.07322 5.01256 6.07322 4.71967 5.78033L0.21967 1.28033C-0.0732232 0.987436 -0.0732231 0.512563 0.21967 0.219669C0.512564 -0.0732237 0.987436 -0.0732237 1.28033 0.21967L5.25 4.18934L9.21967 0.21967Z" fill="#2E85EC"/>
                        </svg>

                        <svg id="mm-arrow-up" style="margin-left:4px; display: none" width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.28033 5.78033C0.987437 6.07322 0.512563 6.07322 0.21967 5.78033C-0.0732233 5.48744 -0.0732233 5.01256 0.21967 4.71967L4.71967 0.21967C5.01256 -0.0732235 5.48744 -0.0732235 5.78033 0.21967L10.2803 4.71967C10.5732 5.01256 10.5732 5.48744 10.2803 5.78033C9.98744 6.07322 9.51256 6.07322 9.21967 5.78033L5.25 1.81066L1.28033 5.78033Z" fill="#2E85EC"/>
                        </svg>
                            
                        </span>
                    </div>

                </div>

                <div style='min-height: 100px; position: relative' class="pt-20 pb-30 pr-30 pl-30 ">

                    <div class="modal-disc pb-20">


                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M18.0005 15.8832C19.8179 15.8832 21.2912 17.3565 21.2912 19.1739C21.2912 20.9913 19.8179 22.4646 18.0005 22.4646C16.1831 22.4646 14.7098 20.9913 14.7098 19.1739C14.7098 17.3565 16.1831 15.8832 18.0005 15.8832ZM18.0005 17.1172C16.8646 17.1172 15.9438 18.038 15.9438 19.1739C15.9438 20.3098 16.8646 21.2306 18.0005 21.2306C19.1364 21.2306 20.0572 20.3098 20.0572 19.1739C20.0572 18.038 19.1364 17.1172 18.0005 17.1172ZM18.0005 13C21.7959 13 25.0723 15.5914 25.9814 19.223C26.0641 19.5536 25.8632 19.8887 25.5327 19.9714C25.2021 20.0542 24.8671 19.8533 24.7843 19.5227C24.0119 16.4371 21.2265 14.234 18.0005 14.234C14.7731 14.234 11.9868 16.439 11.2158 19.5264C11.1332 19.857 10.7983 20.0581 10.4677 19.9755C10.137 19.893 9.93597 19.558 10.0185 19.2274C10.926 15.5937 14.2034 13 18.0005 13Z" fill="#2E85EC"/>
                            </svg>
                            

                        <span class="sub-title">Revisão de regras </span>


                        {{-- <svg id="edit-rules" class="pointer" style="position: absolute; top:24px; right:30px" width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.5" y="0.5" width="35" height="35" rx="7.5" stroke="#2E85EC"/>
                            <path d="M25.2706 11.7294C26.2431 12.702 26.2431 14.2788 25.2706 15.2513L16.0731 24.4488C15.6741 24.8478 15.1742 25.1308 14.6269 25.2677L11.7742 25.9808C11.3182 26.0948 10.9052 25.6818 11.0192 25.2258L11.7323 22.3731C11.8692 21.8258 12.1522 21.3259 12.5512 20.9269L21.7487 11.7294C22.7212 10.7569 24.298 10.7569 25.2706 11.7294ZM20.8681 14.3707L13.4316 21.8074C13.1923 22.0468 13.0224 22.3467 12.9403 22.6751L12.4788 24.5212L14.3249 24.0597C14.6533 23.9776 14.9532 23.8077 15.1926 23.5684L22.6288 16.1314L20.8681 14.3707ZM22.6291 12.6099L21.7484 13.4904L23.5091 15.2511L24.3901 14.3709C24.8764 13.8846 24.8764 13.0962 24.3901 12.6099C23.9038 12.1236 23.1154 12.1236 22.6291 12.6099Z" fill="#2E85EC"/>
                            </svg> --}}
                        
                            <button type="button" id="edit-rules" class="btn btn-edit" id="" data-code="">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.2706 0.729413C15.2431 1.70196 15.2431 3.27878 14.2706 4.25133L5.07307 13.4488C4.67412 13.8478 4.17424 14.1308 3.62688 14.2677L0.77416 14.9808C0.318185 15.0948 -0.094838 14.6818 0.0191557 14.2258L0.732336 11.3731C0.869176 10.8258 1.1522 10.3259 1.55116 9.92693L10.7487 0.729413C11.7212 -0.243138 13.298 -0.243138 14.2706 0.729413ZM9.8681 3.37072L2.43164 10.8074C2.19226 11.0468 2.02245 11.3467 1.94034 11.6751L1.47883 13.5212L3.32488 13.0597C3.65329 12.9776 3.95322 12.8077 4.19259 12.5684L11.6288 5.13141L9.8681 3.37072ZM11.6291 1.60989L10.7484 2.49037L12.5091 4.25106L13.3901 3.37085C13.8764 2.88458 13.8764 2.09617 13.3901 1.60989C12.9038 1.12362 12.1154 1.12362 11.6291 1.60989Z" fill="#2E85EC"></path>
                                </svg>
                            </button>
                    </div>

                    <div class="rules-label" >Por Valor em R$ ou Porcentagem</div>
                    <div class="rules"></div>
                </div>
                
                    

                
            </div>

            
            
            
            
            
            
            
            
            <div id="edit_step1" style="display: none">
                <div style='min-height: 100px; position: relative;' class="pt-25 pr-30 pl-30 ">
                
                    <p>
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M23.2661 11C24.2237 11 25 11.7763 25 12.734V16.9434C25 17.6076 24.7362 18.2445 24.2667 18.7142L17.7155 25.2673C16.7372 26.2437 15.153 26.2444 14.1734 25.2684L10.7351 21.8351C9.75607 20.8578 9.75482 19.2719 10.7322 18.293L17.2823 11.7347C17.7521 11.2643 18.3896 11 19.0544 11H23.2661ZM23.2661 12.156H19.0544C18.6964 12.156 18.3531 12.2983 18.1002 12.5516L11.5405 19.1196C11.024 19.6475 11.0279 20.494 11.5518 21.0169L14.9897 24.4499C15.5175 24.9757 16.3717 24.9754 16.8985 24.4496L23.4492 17.8969C23.702 17.644 23.8441 17.301 23.8441 16.9434V12.734C23.8441 12.4148 23.5853 12.156 23.2661 12.156ZM21.1468 13.699C21.7852 13.699 22.3028 14.2166 22.3028 14.855C22.3028 15.4934 21.7852 16.011 21.1468 16.011C20.5084 16.011 19.9909 15.4934 19.9909 14.855C19.9909 14.2166 20.5084 13.699 21.1468 13.699Z" fill="#2E85EC"/>
                            </svg>
                            
                        
                        <span class="sub-title"> Planos no desconto <span style="color:#969696; font-size: 14px; font-weight: normal"> • <span id="planos-count-edit">x planos</span></span>  </span>

                        <div class="custom-control custom-checkbox" style="position: absolute; right: 32px; top: 26px;">
                            <span id="all-plans4" class="pointer" style="color: #2E85EC; font-size: 16px; font-weight: 700">
                                
                                Selecionar todos
                                <svg class=" " style="margin-left: 4px;
                                    margin-top: -2px;" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"></circle>                            <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"></path>
                                </svg>
                            </span>
                        </div>
                    </p>

                    <div class="edit-plans-thumbs edit-disc-plans-thumbs-scroll" style="height: 76px">
                        
                    </div>

                    {{-- <div style="position: relative">

                        <input class="input-pad" style="height: 48px; padding-right: 38px" id="search_input2" autocomplete="off" autofocus placeholder="Pesquiser plano por nome" type="text">
                        <svg style="position: absolute;
                        right: 12px;
                        top: 16px;" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.9633 12.0239C9.80854 12.9477 8.34378 13.5001 6.75 13.5001C3.02208 13.5001 0 10.478 0 6.75003C0 3.02209 3.02208 0 6.75 0C10.4779 0 13.5 3.02209 13.5 6.75003C13.5 8.34377 12.9477 9.80852 12.024 10.9633L17.7803 16.7197C18.0732 17.0126 18.0732 17.4874 17.7803 17.7803C17.4874 18.0732 17.0126 18.0732 16.7197 17.7803L10.9633 12.0239ZM12 6.75003C12 3.85052 9.6495 1.50001 6.75 1.50001C3.85051 1.50001 1.5 3.85052 1.5 6.75003C1.5 9.64953 3.85051 12 6.75 12C9.6495 12 12 9.64953 12 6.75003Z" fill="#636363"/>
                            </svg>
                    </div> --}}

                    <div class="d-flex modal-new-layout box-description">
                        <input class="form-control form-control-lg search_input_create_coupon search_coupon" type="text" id="search_input2" placeholder="Pesquisa por nome" style="border-top-right-radius: 0;border-bottom-right-radius: 0; height: 48px !important; border-right: 0;">
                        <div class="input-group input-group-lg" style="width: 660px;">
                            <input onkeyup="set_description_value(this, $('.search_input_create_coupon'))" class="form-control" type="text" id="search_input_description" placeholder="Pesquisa por descrição" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <img src="/build/global/img/icon-search.svg" alt="Icon Search">
                                </span>
                            </div>
                        </div>
                    </div>
                        
                    <div id="search_result2" class="mt-30  " style="height: 312px; width:596px; overflow: hidden">

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

                
            </div>

            <div id="edit_step2" style="display: none">
                

                <div style='min-height: 100px' class="inputs-warning2 pl-30 pr-30 simple-border-bottom">


                    <div class="mt-25 mb-20">

                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M21.9925 12.8558C22.0509 12.4458 21.7659 12.0661 21.3559 12.0076C20.9458 11.9491 20.566 12.2341 20.5075 12.644L20.171 15.0028L16.6864 15.0025L16.9925 12.8587C17.051 12.4487 16.766 12.0689 16.356 12.0104C15.9459 11.9519 15.566 12.2368 15.5075 12.6468L15.1712 15.0023L13.7501 15.0021C13.3359 15.0021 13 15.3378 13 15.7519C13 16.166 13.3357 16.5017 13.7499 16.5018L14.9571 16.5019L14.5289 19.5009L12.75 19.501C12.3358 19.501 12 19.8367 12 20.2509C12 20.665 12.3358 21.0007 12.75 21.0006L14.3148 21.0006L14.0088 23.1441C13.9503 23.5541 14.2352 23.9338 14.6453 23.9924C15.0553 24.0509 15.4352 23.766 15.4937 23.356L15.83 21.0005L19.3153 21.0004L19.0094 23.1442C18.951 23.5542 19.236 23.9339 19.646 23.9924C20.0561 24.0509 20.4359 23.7659 20.4944 23.356L20.8305 21.0003L22.25 21.0002C22.6642 21.0002 23 20.6645 23 20.2504C23 19.8363 22.6642 19.5006 22.25 19.5006L21.0444 19.5007L21.4722 16.5026L23.2499 16.5028C23.6641 16.5028 24 16.1671 24 15.753C24 15.3389 23.6643 15.0032 23.2501 15.0031L21.6861 15.003L21.9925 12.8558ZM19.957 16.5024L19.5293 19.5007L16.0441 19.5009L16.4723 16.5021L19.957 16.5024Z" fill="#2E85EC"/>
                            </svg>
                            
                            
                        <span class="sub-title" style="padding-left: 10px; margin-bottom: 10px"> Regras para aplicação de
                            desconto </span>
                    </div>

                    

                    

                    <input type="hidden" name="progressive_rules" id="rules_edited">

                    <label class="mb-10">Adicionar novo desconto de</label>
                    <div class="row ">
                        <div class="col-3" style="padding-left: 18px;">

                            <input name="type" value="1"  class="discount_radio" type="radio" id="type_value-edit" checked style="outline: none" /> 
                            <label for="type_value-edit">
                                Valor em R$
                            </label>
                        </div>
                        <div class="col-3" >
                            <input name="type" value="0" class="discount_radio " type="radio" id="type_percent-edit" style="outline: none"/> 
                            <label for="type_percent-edit">Porcentagem</label>
                        </div>
                    </div>

                    
                    {{-- xxx --}}
                    <div class="mt-10 h-60">

                        <div class="float-left " style="padding: 14px 8px 0 0">

                            Na compra
                        </div>


                        <div class="float-left" style="padding-right: 4px">

                            <select id="buy-edit" class="sirius-select w-auto d-inline-block adjust-select">
                                <option value="above_of">acima de</option>
                                <option value="of">de</option>
                            </select>
                        </div>

                        <div class="float-left">

                            <input class="input-pad " id="qtde-edit" type="text" onkeyup="$(this).removeClass('warning-input')"
                                style="margin: 0 1px; width: 60px; height:49px" maxlength="2" data-mask="0#" />
                            itens, aplicar desconto de
                            <input class="input-pad " maxlength="9" id="value-edit" type="text" onkeyup="$(this).removeClass('warning-input')"
                                style="width: 86px; height:48px; margin-right: 8px" />
                            <input class="input-pad " type="text" onkeyup="$(this).removeClass('warning-input')"
                                style="width: 86px; display: none; height:48px; margin-right: 8px" id="percent-edit"
                                maxlength="2" data-mask="0#" autocomplete="off">
                        </div>

                        <div class="float-left">
                            <div id="add_rule-edit" class="add_rule pointer"
                                style=" margin-top: 1px; margin-left:0px; float:right; height:46px; width: 46px">
                            </div>
                        </div>



                    </div>

                    <div class="clear-both" style="height: 22px">

                        <div class="clear-both warning-text2" style="font-weight: normal;
                                font-size: 13px;
                                line-height: 16px;
                                display:none;
                                color: #DE0000;">
                            <svg style="margin-top: -4px; margin-right: 4px " width="12" height="12" viewBox="0 0 12 12"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6 0C9.31371 0 12 2.68629 12 6C12 9.31371 9.31371 12 6 12C2.68629 12 0 9.31371 0 6C0 2.68629 2.68629 0 6 0ZM6 8C5.58579 8 5.25 8.33579 5.25 8.75C5.25 9.16421 5.58579 9.5 6 9.5C6.41421 9.5 6.75 9.16421 6.75 8.75C6.75 8.33579 6.41421 8 6 8ZM6 2.5C5.75454 2.5 5.55039 2.67688 5.50806 2.91012L5.5 3V6.5L5.50806 6.58988C5.55039 6.82312 5.75454 7 6 7C6.24546 7 6.44961 6.82312 6.49194 6.58988L6.5 6.5V3L6.49194 2.91012C6.44961 2.67688 6.24546 2.5 6 2.5Z"
                                    fill="#DE0000" />
                            </svg>
    
                            Você precisa adicionar pelo menos uma regra para criar um desconto progressivo
                        </div>

                    </div>  
                    {{-- x --}}
                    



                    
                    <div class="mt-20 mb-10">

                        <span style="font-size: 14px; font-weight: 700">Regras adicionadas</span>
                    </div>

                    
                    

                </div>

                <div id="rules-edit" class="pb-20" style="
                    height: 242px;
                    padding: 0 15px;
                    margin: 15px;
                    overflow-x: hidden;
                    margin-right:4px;
                ">
                
                </div>
                <div id="empty-rules2" class="row" style="position: absolute; bottom:136px">
                    <div class="col-4">

                        <svg style="margin:45px 0 0 80px" width="150" height="153" viewBox="0 0 150 153" fill="none" xmlns="http://www.w3.org/2000/svg">

                            <path d="M75 150C116.421 150 150 116.421 150 75C150 33.5786 116.421 0 75 0C33.5786 0 0 33.5786 0 75C0 116.421 33.5786 150 75 150Z" fill="url(#paint0_linear_397:1078)"></path>
                            <g filter="url(#filter0_d_397:1078)">
                                <mask id="mask0_397:1078" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="150" height="150">
                                    <path d="M75 150C116.421 150 150 116.421 150 75C150 33.5786 116.421 0 75 0C33.5786 0 0 33.5786 0 75C0 116.421 33.5786 150 75 150Z" fill="url(#paint1_linear_397:1078)"></path>
                                </mask>
                                <g mask="url(#mask0_397:1078)">
                                    <path d="M118 43H32C29.2386 43 27 45.2386 27 48V153C27 155.761 29.2386 158 32 158H118C120.761 158 123 155.761 123 153V48C123 45.2386 120.761 43 118 43Z" fill="white"></path>
                                </g>
                            </g>
                            <path d="M66 53H40C38.3431 53 37 54.3431 37 56C37 57.6569 38.3431 59 40 59H66C67.6569 59 69 57.6569 69 56C69 54.3431 67.6569 53 66 53Z" fill="#E1EBFA"></path>
                            <path d="M66 95H40C38.3431 95 37 96.3431 37 98C37 99.6569 38.3431 101 40 101H66C67.6569 101 69 99.6569 69 98C69 96.3431 67.6569 95 66 95Z" fill="#E1EBFA"></path>
                            <path d="M108 68H42C39.7909 68 38 69.7909 38 72V82C38 84.2091 39.7909 86 42 86H108C110.209 86 112 84.2091 112 82V72C112 69.7909 110.209 68 108 68Z" stroke="#1485FD" stroke-width="2"></path>
                            <path d="M108 109H42C39.2386 109 37 111.239 37 114V122C37 124.761 39.2386 127 42 127H108C110.761 127 113 124.761 113 122V114C113 111.239 110.761 109 108 109Z" fill="#DFEAFB"></path>
                            <path d="M53 32C55.2091 32 57 30.2091 57 28C57 25.7909 55.2091 24 53 24C50.7909 24 49 25.7909 49 28C49 30.2091 50.7909 32 53 32Z" fill="white"></path>
                            <path d="M75 32C77.2091 32 79 30.2091 79 28C79 25.7909 77.2091 24 75 24C72.7909 24 71 25.7909 71 28C71 30.2091 72.7909 32 75 32Z" fill="#1485FD"></path>
                            <path d="M97 32C99.2091 32 101 30.2091 101 28C101 25.7909 99.2091 24 97 24C94.7909 24 93 25.7909 93 28C93 30.2091 94.7909 32 97 32Z" fill="white"></path>
                            <path d="M86 88C88.7614 88 91 85.7614 91 83C91 80.2386 88.7614 78 86 78C83.2386 78 81 80.2386 81 83C81 85.7614 83.2386 88 86 88Z" fill="#DFEAFB"></path>
                            <path d="M89.907 104.37C89.107 104.37 88.36 104.37 87.68 104.327C86.8424 104.27 86.0366 103.984 85.3514 103.499C84.6661 103.014 84.1279 102.349 83.796 101.578L79.577 93.24C79.2675 92.8797 79.113 92.4117 79.1471 91.938C79.1812 91.4643 79.4011 91.0233 79.759 90.711C80.0521 90.4754 80.4178 90.3485 80.794 90.352C81.0709 90.3601 81.3427 90.4281 81.5908 90.5513C81.8389 90.6746 82.0573 90.8502 82.231 91.066L84.147 93.681L84.176 93.715V83.78C84.176 83.2871 84.3718 82.8144 84.7203 82.4659C85.0688 82.1173 85.5416 81.9215 86.0345 81.9215C86.5274 81.9215 87.0001 82.1173 87.3486 82.4659C87.6972 82.8144 87.893 83.2871 87.893 83.78V90.28C87.8714 90.0408 87.8999 89.7998 87.9766 89.5722C88.0533 89.3446 88.1766 89.1355 88.3386 88.9582C88.5006 88.781 88.6978 88.6394 88.9175 88.5425C89.1373 88.4456 89.3748 88.3956 89.615 88.3956C89.8551 88.3956 90.0926 88.4456 90.3124 88.5425C90.5321 88.6394 90.7293 88.781 90.8913 88.9582C91.0533 89.1355 91.1766 89.3446 91.2533 89.5722C91.3301 89.7998 91.3585 90.0408 91.337 90.28V91.635C91.3154 91.3958 91.3439 91.1548 91.4206 90.9272C91.4973 90.6996 91.6206 90.4905 91.7826 90.3132C91.9446 90.136 92.1418 89.9944 92.3615 89.8975C92.5813 89.8006 92.8188 89.7506 93.059 89.7506C93.2991 89.7506 93.5366 89.8006 93.7564 89.8975C93.9761 89.9944 94.1733 90.136 94.3353 90.3132C94.4973 90.4905 94.6206 90.6996 94.6973 90.9272C94.7741 91.1548 94.8025 91.3958 94.781 91.635V92.679C94.7594 92.4398 94.7879 92.1988 94.8646 91.9712C94.9413 91.7436 95.0646 91.5345 95.2266 91.3572C95.3886 91.18 95.5858 91.0384 95.8055 90.9415C96.0253 90.8446 96.2628 90.7946 96.503 90.7946C96.7431 90.7946 96.9806 90.8446 97.2004 90.9415C97.4201 91.0384 97.6173 91.18 97.7793 91.3572C97.9413 91.5345 98.0646 91.7436 98.1413 91.9712C98.2181 92.1988 98.2465 92.4398 98.225 92.679V99.016C98.191 100.965 97.31 104.251 94.211 104.251C93.986 104.261 92.08 104.371 89.911 104.371L89.907 104.37Z" fill="#1485FD" stroke="white"></path>
                            <defs>
                                <filter id="filter0_d_397:1078" x="21" y="34" width="108" height="119" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"></feColorMatrix>
                                    <feOffset dy="-3"></feOffset>
                                    <feGaussianBlur stdDeviation="3"></feGaussianBlur>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.788235 0 0 0 0 0.803922 0 0 0 0 0.85098 0 0 0 0.349 0"></feColorMatrix>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_397:1078"></feBlend>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_397:1078" result="shape"></feBlend>
                                </filter>
                                <linearGradient id="paint0_linear_397:1078" x1="75" y1="0" x2="75" y2="150" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#E3ECFA"></stop>
                                    <stop offset="1" stop-color="#DAE7FF"></stop>
                                </linearGradient>
                                <linearGradient id="paint1_linear_397:1078" x1="75" y1="0" x2="75" y2="150" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#E3ECFA"></stop>
                                    <stop offset="1" stop-color="#DAE7FF"></stop>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="col-8" style="padding: 50px; ">

                        <span style="font-size: 24px">Nenhuma regra aqui.</span>
                        <br>
                        <span style="font-size: 16px">Para prosseguir com a criação de um desconto progressivo, você
                            precisa
                            adicionar pelo menos uma regra.</span>
                    </div>



                </div>

                <div class="modal-footer" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; position: absolute; bottom:0px; width:646px; border-top: 1px solid #EBEBEB; background-color: #fff; z-index:1000">
                    <div class="justify-center text-center mt-10" style="width: 100%">
                        <button id="edit-rules-back" class="btn back-btn" type="button">Voltar</button>
                        <button class="btn btn-primary btn2 btn-save-edit-rules"  type="button">Finalizar</button>
                    </div>
                </div>
                
            </div>


            <div class="modal-footer footer-padding" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; position: absolute; bottom:0px; width:646px; border-top: 1px solid #EBEBEB; background-color: #fff">
                <div class="d-flex mt-10" style="width: 610px">
                    

                    

                    <div id="edit-finish-btn" style="width:100%; justify-content: space-between" >
                        <div style="width: 206px; float: left;
                        margin-top: 12px;">
                            <div style="width: 206px" class="mr-auto  switch-holder d-flex align-items-center">
                                <label class="switch">
                                    <input id="edit_status" type="checkbox" value="1" name="status" class="check status" checked="">
                                    <span class="slider round"></span>
                                </label>
                                <label id="edit_status_label" for="edit_status" class="pointer" style="font: normal normal bold 16px Muli;color: #8B8B8B;margin-bottom: 0;">Desconto ativo</label>
                                <input type="hidden" name="set_status" id="set_status">
                            </div>
                        </div>
                        <button class="p-2 btn btn-primary edit-finish-btn btn2 float-right "  type="button">Fechar</button>
                    </div>


                    
                    <div id="plans-actions" style="display: none;  width:100%" class="justify-center text-center ">
                        <button class="btn cancel-btn" type="button">Voltar</button>
                        <button class="btn btn-primary next-btn btn-edit-plans-save" disabled type="button">Atualizar</button>
                        <input type="hidden" name="plans" id="plans_value">
                    </div>
                    

                </div>
            </div>

        </div>
    </div>


</form>
