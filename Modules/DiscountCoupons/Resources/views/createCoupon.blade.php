<form id="form-register-coupon">
        @csrf
        <div style="display: none; padding:25px" id="select-coupon">
            <div class="modal-content s-border-radius" style="width:646px; height:706px">
                <div class="mdtpad simple-border-bottom">
                    <span class=" " id="modal-title"
                        style="color:#636363; font: normal normal bold 22px Muli;">
                        Novo cupom de desconto
                </span>

                    <a id="modal-button-close-4" class="modal-button-close-3 pointer close" role="button" data-dismiss="modal"
                    aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 1L1 15M1 1L15 15L1 1Z" stroke="#636363" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                    </a>
                </div>

                <div class="step1">
                    <div style='min-height: 100px; position: relative;' class="pt-25 pl-25 pr-25 simple-border-bottom">
                    
                        <div class="mb-25">
                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                                <path d="M23.2661 11C24.2237 11 25 11.7763 25 12.734V16.9434C25 17.6076 24.7362 18.2445 24.2667 18.7142L17.7155 25.2673C16.7372 26.2437 15.153 26.2444 14.1734 25.2684L10.7351 21.8351C9.75607 20.8578 9.75482 19.2719 10.7322 18.293L17.2823 11.7347C17.7521 11.2643 18.3896 11 19.0544 11H23.2661ZM23.2661 12.156H19.0544C18.6964 12.156 18.3531 12.2983 18.1002 12.5516L11.5405 19.1196C11.024 19.6475 11.0279 20.494 11.5518 21.0169L14.9897 24.4499C15.5175 24.9757 16.3717 24.9754 16.8985 24.4496L23.4492 17.8969C23.702 17.644 23.8441 17.301 23.8441 16.9434V12.734C23.8441 12.4148 23.5853 12.156 23.2661 12.156ZM21.1468 13.699C21.7852 13.699 22.3028 14.2166 22.3028 14.855C22.3028 15.4934 21.7852 16.011 21.1468 16.011C20.5084 16.011 19.9909 15.4934 19.9909 14.855C19.9909 14.2166 20.5084 13.699 21.1468 13.699Z" fill="#2E85EC"/>
                                </svg>

                                
                            <span class="sub-title" style="color: #636363"> Selecione os planos que terão cupom </span>

                            

                            <div class="custom-control custom-checkbox" style="position: absolute; right: 32px; top: 28px;">
                                <span id="all-plans" class="pointer"
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
                        
                        {{-- <div style="position: relative;">

                            <input style="height: 48px; padding-right: 38px" id="search_input" class="search_coupon input-pad" autocomplete="off" autofocus placeholder="Pesquiser plano por nome" type="text">
                            <svg style="position: absolute;
                            right: 7px;
                            top: 15px;" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.9633 12.0239C9.80854 12.9477 8.34378 13.5001 6.75 13.5001C3.02208 13.5001 0 10.478 0 6.75003C0 3.02209 3.02208 0 6.75 0C10.4779 0 13.5 3.02209 13.5 6.75003C13.5 8.34377 12.9477 9.80852 12.024 10.9633L17.7803 16.7197C18.0732 17.0126 18.0732 17.4874 17.7803 17.7803C17.4874 18.0732 17.0126 18.0732 16.7197 17.7803L10.9633 12.0239ZM12 6.75003C12 3.85052 9.6495 1.50001 6.75 1.50001C3.85051 1.50001 1.5 3.85052 1.5 6.75003C1.5 9.64953 3.85051 12 6.75 12C9.6495 12 12 9.64953 12 6.75003Z" fill="#636363"/>
                                </svg>
                        </div> --}}

                        <div class="d-flex modal-new-layout box-description">
                            <input class="form-control form-control-lg search_input_create_coupon search_coupon" type="text" id="search_input" placeholder="Pesquisa por nome" style="border-top-right-radius: 0;border-bottom-right-radius: 0; height: 48px !important; border-right: 0;">
                            <div class="input-group input-group-lg" style="width: 650px;">
                                <input onkeyup="set_description_value(this, $('.search_input_create_coupon'))" class="form-control" type="text" id="search_input_description" placeholder="Pesquisa por descrição" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <img src="/modules/global/img/icon-search.svg" alt="Icon Search">
                                    </span>
                                </div>
                            </div>
                        </div>
                            
                        <div id="search_result" class="mt-20 " style=" height: 393px">
                            
                            

                            
    
                            
                        </div>
    
                    </div>
    
                    <div class="modal-footer">
                        <div style="width: 100%" class="justify-center text-center mt-10">
                            <button class="btn cancel-btn" type="button">Voltar</button>
                            <button class="btn btn-primary coupon-next btn2" disabled type="button">Continuar</button>
                        </div>
                    </div>
                </div>

                <div class="step2" style="display: none">
                    <div style='position: relative; min-height: 100px' class="p-25 simple-border-bottom">
                    
                        <div class="pb-20">
                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                                <path d="M18 10C22.4183 10 26 13.5817 26 18C26 22.4183 22.4183 26 18 26C13.5817 26 10 22.4183 10 18C10 13.5817 13.5817 10 18 10ZM18 11.1998C14.2444 11.1998 11.1998 14.2444 11.1998 18C11.1998 21.7556 14.2444 24.8002 18 24.8002C21.7556 24.8002 24.8002 21.7556 24.8002 18C24.8002 14.2444 21.7556 11.1998 18 11.1998ZM17.9971 16.7994C18.3008 16.7992 18.5519 17.0247 18.5919 17.3175L18.5974 17.3989L18.6003 21.7995C18.6005 22.1308 18.3321 22.3996 18.0008 22.3998C17.697 22.4 17.4459 22.1745 17.406 21.8817L17.4005 21.8003L17.3976 17.3997C17.3974 17.0684 17.6658 16.7996 17.9971 16.7994ZM18.0004 14.0016C18.4415 14.0016 18.7992 14.3593 18.7992 14.8004C18.7992 15.2416 18.4415 15.5992 18.0004 15.5992C17.5592 15.5992 17.2016 15.2416 17.2016 14.8004C17.2016 14.3593 17.5592 14.0016 18.0004 14.0016Z" fill="#2E85EC"/>
                                </svg>
                                
                                
                            
                            <span class="sub-title"> Informações do desconto </span>

                            <div style="display:none" class="  switch-holder ">
                                <label class="switch">
                                    <input id="create_status" type="checkbox" value="1" name="status" class="check " checked="" />
                                    <span class="slider round"></span>
                                </label>
                                
                                <input type="hidden" name="set_status" id="create_status" />
                            </div>
                        </div>

                        <div class="flex row" style="padding-left: 5px">
                            <div class="col-6" >
                                <label>Nome</label>
                                <input class="input-pad" style="height: 48px" name="name" id="c_name" maxlength="20" autocomplete="" autofocus placeholder="Digite um nome" type="text">
                            </div>
                            <div class="col-6" >
                                <label>Código do cupom</label>
                                <input class="input-pad" style="height: 48px" name="code" id="c_code" maxlength="20"  autocomplete=""  placeholder="Crie um código" type="text">
                            </div>
                        </div>
                        
                    </div>

                    <div style='position: relative; height: 364px' class="p-25 simple-border-bottom">
                        <p >    
                            <span class="sub-title"> Regras para aplicação de cupom </span>
                        </p>
                        <div class="pl-10">

                            <label>Selecione o tipo de desconto</label>
                        </div>
                        <div class="row mb-10 mt-10">
                            
                            <div class="col-3" style="padding-left: 25px">
    
                                <input name="type" value="1"  class="discount_radio " type="radio" id="c_type_value" checked /> 
                                <label for="c_type_value">
                                    Valor em R$
                                </label>
                            </div>
                            <div class="col-3">
                                <input name="type" value="0" class="discount_radio " type="radio" id="c_type_percent" /> 
                                <label for="c_type_percent">Porcentagem</label>
                            </div>
                        </div>
                            
                        <div class="flex row pl-10">
                            <div class="col-6" >
                                <label>Desconto de</label>
                                
                                <div id="money_opt" class="input-group input-group-lg mb-3">
                                    <div  class="input-group-prepend">
                                      <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">R$</span>
                                    </div>
                                    <input onkeyup="$(this).removeClass('warning-input')" maxlength="9" name="discount_value" id="discount_value" type="text" class=" input-pad form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" />
                                </div>
                                
                                <div id="percent_opt" style="display: none" class="input-group-lg input-group mb-3">
                                    
                                    <input onkeyup="$(this).removeClass('warning-input')" maxlength="2" data-mask="0#" name="percent_value" id="percent_value" style="" type="text" class=" input-dad form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                    <div  class="input-group-append">
                                        <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6" >
                                <label>Valor mínimo da compra</label>
                                
                                <div class="input-group input-group-lg mb-3">
                                    <div  class="input-group-prepend">
                                      <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">R$</span>
                                    </div>
                                    <input onkeyup="$(this).removeClass('warning-input')" maxlength="9" name="rule_value" id="minimum_value" style="" type="text" class="form-control input-pad " placeholder="" aria-label="" aria-describedby="basic-addon1">
                                </div>
                            </div>
                        </div>
                        <div class="pl-10 pt-20 flex row">
                            <div class="col-4" style="position: relative">

                                <label>Vence em</label>
                                
                                <input class="input-pad"  name="expires" type="text" id="date_range" style="padding-right: 40px; height: 48px" autocomplete="off">
                                <svg id="cal-icon" class="pointer" style="pointer-events: none;
                                position: absolute; top:43px; right: 28px;" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.75 0C20.5449 0 22 1.45507 22 3.25V18.75C22 20.5449 20.5449 22 18.75 22H3.25C1.45507 22 0 20.5449 0 18.75V3.25C0 1.45507 1.45507 0 3.25 0H18.75ZM20.5 6.503H1.5V18.75C1.5 19.7165 2.2835 20.5 3.25 20.5H18.75C19.7165 20.5 20.5 19.7165 20.5 18.75V6.503ZM18.75 1.5H3.25C2.2835 1.5 1.5 2.2835 1.5 3.25V5.003H20.5V3.25C20.5 2.2835 19.7165 1.5 18.75 1.5Z" fill="#636363"/>
                                    </svg>
                                    

                            </div>
                            <div class="col-6 pt-40">
                                

                                <label class="custom-check">
                                    <input name="nao_vence" type="checkbox" class="custom-control-input" id="nao_vence">
                                    <span class="icone"></span>
                                    Não vence
                                </label>
                                
                            </div>
                        </div>
                        
                    </div>

                    <input type="hidden" name="plans" id="c_plans">
                    <input type="hidden" name="value" id="c_value">

                    {{-- <div class="modal-footer" style="    position: absolute;
                    width: 646px;
                    bottom: 1px;">
                        <div class="justify-center text-center mt-10" style="width: 646px">
                            <button class="btn back-btn add-coupon-back" type="button">Voltar</button>
                            <button class="btn btn-primary btn2 add-coupon" disabled type="button">Finalizar</button>
                        </div>
                    </div> --}}
                    
                    <div class="modal-footer">
                        <div style="width: 100%" class="justify-center text-center mt-10">
                            <button class="btn back-btn add-coupon-back" type="button">Voltar</button>
                            <button class="btn btn-primary coupon-next btn2 add-coupon" disabled type="button">Finalizar</button>
                        </div>
                    </div>


                    
                </div>
        
            </div>
        </div>
</form>
